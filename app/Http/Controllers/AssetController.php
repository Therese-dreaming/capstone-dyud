<?php
namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\Dispose;
use App\Models\Semester;
use App\Models\AssetMaintenanceHistory;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::with(['category', 'location', 'originalLocation', 'warranty']);

        // Search (asset code, name, category name, location building/room)
        if ($search = trim((string) $request->get('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('asset_code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('location', function ($lq) use ($search) {
                      $lq->where('building', 'like', "%{$search}%")
                         ->orWhere('room', 'like', "%{$search}%");
                  });
            });
        }

        // Category filter
        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Status filter (accepts raw status string)
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // Condition filter
        if ($condition = $request->get('condition')) {
            $query->where('condition', $condition);
        }

        // Deployment filter: deployed / not_deployed
        if ($deployment = $request->get('deployment')) {
            if ($deployment === 'deployed') {
                $query->whereNotNull('location_id');
            } elseif ($deployment === 'not_deployed') {
                $query->whereNull('location_id');
            }
        }

        // Specific location filter
        if ($locationId = $request->get('location_id')) {
            $query->where('location_id', $locationId);
        }

        $assets = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->query());

        $categories = Category::orderBy('name')->get(['id', 'name']);
        $locations = Location::orderBy('building')->orderBy('floor')->orderBy('room')->get(['id', 'building', 'floor', 'room']);

        return view('assets.index', [
            'assets' => $assets,
            'categories' => $categories,
            'locations' => $locations,
            'filters' => [
                'q' => $search ?? '',
                'category_id' => $request->get('category_id'),
                'status' => $request->get('status'),
                'condition' => $request->get('condition'),
                'deployment' => $request->get('deployment'),
                'location_id' => $request->get('location_id'),
            ],
        ]);
    }

    public function gsuIndex()
    {
        // Show only approved assets for GSU (for location assignment and management)
        // Order by created_at descending so newest assets appear first
        $assets = Asset::where('approval_status', Asset::APPROVAL_APPROVED)
            ->with(['category', 'location', 'originalLocation', 'warranty', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('gsu.assets.index', compact('assets'));
    }

    public function create(Request $request)
    {
        $categories = Category::all();
        $locations = Location::all();
        
        // Get the pre-selected location ID from the request
        $selectedLocationId = $request->get('location_id');
        
        // Check if user is GSU and return appropriate view
        if (auth()->user()->role === 'gsu') {
            return view('assets.gsu-create', compact('categories', 'locations', 'selectedLocationId'));
        }
        
        return view('assets.create', compact('categories', 'locations', 'selectedLocationId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1|max:100',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'condition' => 'required|in:Good,Fair,Poor',
            'description' => 'nullable|string',
            'purchase_cost' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'status' => 'required|in:Available,Disposed',
            // Warranty fields
            'manufacturer' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'warranty_expiry' => 'required|date'
        ]);

        try {
            return DB::transaction(function () use ($validated, $request) {
                $quantity = $validated['quantity'];
                $createdAssets = [];
                
                // Get the latest asset for this category to determine starting sequence
                $category = Category::find($request->category_id);
                
                // Get the highest sequence number for this category by examining asset codes
                $latestAsset = Asset::where('category_id', $category->id)
                    ->where('asset_code', 'like', $category->code . '%')
                    ->orderByRaw('CAST(SUBSTRING(asset_code, -4) AS UNSIGNED) DESC')
                    ->first();
                    
                $startingSequence = $latestAsset ? (int)substr($latestAsset->asset_code, -4) + 1 : 1;
                
                for ($i = 0; $i < $quantity; $i++) {
                    // Generate unique asset code for each asset
                    $sequence = $startingSequence + $i;
                    $assetCode = $category->code . str_pad($sequence, 4, '0', STR_PAD_LEFT);
                    
                    // Create asset
                    $asset = Asset::create([
                        'asset_code' => $assetCode,
                        'name' => $validated['name'],
                        'category_id' => $validated['category_id'],
                        'location_id' => $validated['location_id'],
                        'original_location_id' => $validated['location_id'], // Set original location same as current location
                        'condition' => $validated['condition'],
                        'description' => $validated['description'],
                        'purchase_cost' => $validated['purchase_cost'],
                        'purchase_date' => $validated['purchase_date'],
                        'status' => $validated['status']
                    ]);
                    
                    // Create warranty for each asset
                    $warranty = Warranty::create([
                        'asset_id' => $asset->id,
                        'manufacturer' => $validated['manufacturer'],
                        'model' => $validated['model'],
                        'warranty_expiry' => $validated['warranty_expiry']
                    ]);
                    
                    $createdAssets[] = $asset;
                }

                $message = $quantity === 1 
                    ? 'Asset and warranty created successfully.' 
                    : "{$quantity} assets and warranties created successfully.";

                // Send notification to admins for each created asset
                $notificationService = new NotificationService();
                foreach ($createdAssets as $asset) {
                    $notificationService->notifyAssetCreated($asset);
                }

                // Determine target location for redirection
                $redirectLocation = $request->get('redirect_to_location');
                $targetLocationId = $redirectLocation ?: $validated['location_id'];

                // Redirect per role: GSU should always go back to the location's show page
                if (auth()->user()->role === 'gsu') {
                    return redirect()->route('gsu.locations.show', $targetLocationId)
                        ->with('success', $message);
                }

                if ($redirectLocation) {
                    return redirect()->route('locations.show', $redirectLocation)
                        ->with('success', $message);
                }
                
                return redirect()->route('locations.index')
                    ->with('success', $message);
            });
        } catch (\Exception $e) {
            \Log::error('Asset and warranty creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create asset and warranty: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Asset $asset, Request $request)
    {
        $asset->load([
            'category', 
            'location', 
            'originalLocation', 
            'warranty',
            'createdBy'
        ]);
        
        // Check if this is an admin viewing a pending asset (from admin/assets/{asset} route)
        if (auth()->user()->role === 'admin' && $request->route()->getName() === 'admin.assets.show') {
            return view('admin.assets.show', compact('asset'));
        }
        
        // Get the active tab from request (default to maintenance)
        $activeTab = $request->get('tab', 'maintenance');
        
        // Use only legitimate maintenance checklist scanning history (excludes transfers)
        $maintenances = $asset->legitimateMaintenanceHistory()
            ->orderBy('scanned_at', 'desc')
            ->paginate(10);
            
        $disposes = $asset->disposes()->orderBy('disposal_date', 'desc')->paginate(10);
        
        // Asset changes (transfers, status changes, etc.)
        $changes = $asset->changes()->with('user')->orderBy('created_at', 'desc')->paginate(10);
        
        // Get repair count and history - all repair requests for this asset
        $repairs = \App\Models\MaintenanceRequest::where('notes', 'like', '%REPAIR REQUEST%')
            ->where('requested_asset_codes', 'like', '%"' . $asset->asset_code . '"%')
            ->with(['requester', 'approver'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $repairCount = \App\Models\MaintenanceRequest::where('notes', 'like', '%REPAIR REQUEST%')
            ->where('requested_asset_codes', 'like', '%"' . $asset->asset_code . '"%')
            ->where('status', 'completed')
            ->count();
        
        // Check if user is GSU and return appropriate view
        if (auth()->user()->role === 'gsu') {
            return view('assets.gsu-show', compact('asset'));
        }
        
        return view('assets.show', compact('asset', 'maintenances', 'disposes', 'changes', 'repairs', 'activeTab', 'repairCount'));
    }

    public function gsuShowByCode(string $assetCode, Request $request)
    {
        $asset = Asset::where('asset_code', $assetCode)
            ->with(['category', 'location', 'originalLocation', 'warranty'])
            ->firstOrFail();
        // Force GSU view regardless of role, since route is in GSU group
        return view('assets.gsu-show', compact('asset'));
    }

    

    public function edit(Asset $asset)
    {
        $asset->load(['warranty', 'originalLocation']);
        $categories = Category::all();
        $locations = Location::all();
        return view('assets.edit', compact('asset', 'categories', 'locations'));
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'condition' => 'required|in:Good,Fair,Poor',
            'description' => 'nullable|string',
            'purchase_cost' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'status' => 'required|in:Available,Disposed',
            // Warranty fields
            'manufacturer' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'warranty_expiry' => 'required|date'
        ]);

        // Update asset
        $asset->update([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'condition' => $validated['condition'],
            'description' => $validated['description'],
            'purchase_cost' => $validated['purchase_cost'],
            'purchase_date' => $validated['purchase_date'],
            'status' => $validated['status']
        ]);

        // Handle location update - if asset is not currently borrowed, update both location and original_location
        // If asset is borrowed, only update the original_location (temporary location remains until return)
        if ($asset->status !== 'In Use') {
            // Asset is not borrowed, update both current and original location
            $asset->update([
                'location_id' => $validated['location_id'],
                'original_location_id' => $validated['location_id']
            ]);
        } else {
            // Asset is currently borrowed, only update the original location
            $asset->update([
                'original_location_id' => $validated['location_id']
            ]);
        }

        // Update or create warranty
        $asset->warranty()->updateOrCreate(
            ['asset_id' => $asset->id],
            [
                'manufacturer' => $validated['manufacturer'],
                'model' => $validated['model'],
                'warranty_expiry' => $validated['warranty_expiry']
            ]
        );

        // Send notification to admins
        $notificationService = new NotificationService();
        $notificationService->notifyAssetEdited($asset);

        // Redirect per role after update
        if (auth()->user()->role === 'gsu') {
            // Prefer returning to the asset's location show page in GSU context
            $targetLocationId = $asset->original_location_id ?: $asset->location_id;
            return redirect()->route('gsu.locations.show', $targetLocationId)
                ->with('success', 'Asset updated successfully.');
        }

        return redirect()->route('locations.index')
            ->with('success', 'Asset updated successfully.');
    }

    public function dispose(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'disposal_reason' => 'required|string|max:1000',
        ]);

        // Check if asset is not already disposed
        if ($asset->status === 'Disposed') {
            return redirect()->route(auth()->user()->role === 'gsu' ? 'gsu.assets.index' : 'assets.index')
                ->with('error', 'Asset is already disposed.');
        }

        try {
            return DB::transaction(function () use ($asset, $validated) {
                // Auto-detect current semester for disposal
                $currentSemester = Semester::current() ?? Semester::forDate(now());
                
                // Create disposal record
                Dispose::create([
                    'asset_id' => $asset->id,
                    'disposal_reason' => $validated['disposal_reason'],
                    'disposal_date' => now()->toDateString(),
                    'disposed_by' => 'System Admin', // You might want to use auth()->user()->name if you have authentication
                ]);

                // Update asset status to disposed and assign disposal semester
                $asset->update([
                    'status' => 'Disposed',
                    'disposed_semester_id' => $currentSemester?->id, // Auto-assign current semester
                ]);

                return redirect()->route(auth()->user()->role === 'gsu' ? 'gsu.assets.index' : 'assets.index')
                    ->with('success', "Asset {$asset->asset_code} has been disposed successfully.");
            });
        } catch (\Exception $e) {
            Log::error('Asset disposal failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to dispose asset: ' . $e->getMessage());
        }
    }

    public function destroy(Asset $asset)
    {
        try {
            $assetCode = $asset->asset_code;
            $userRole = auth()->user()->role;
            
            // Log the deletion attempt
            Log::info('Asset deletion started', [
                'asset_id' => $asset->id,
                'asset_code' => $assetCode,
                'user_role' => $userRole,
                'user_id' => auth()->id()
            ]);
            
            // Delete the asset
            $asset->delete();
            
            // Log successful deletion
            Log::info('Asset deleted successfully', [
                'asset_id' => $asset->id,
                'asset_code' => $assetCode
            ]);
            
            // Redirect based on user role
            return redirect()->route('locations.index')
                ->with('success', "Asset {$assetCode} has been deleted successfully.");
        } catch (\Exception $e) {
            // Log the error
            Log::error('Asset deletion failed', [
                'asset_id' => $asset->id,
                'asset_code' => $asset->asset_code ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return with error message
            return redirect()->back()
                ->with('error', 'Failed to delete asset: ' . $e->getMessage());
        }
    }

    public function report(Request $request)
    {
        $query = Asset::with(['category', 'location', 'warranty']);

        // Apply filters
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('purchase_date_from')) {
            $query->whereDate('purchase_date', '>=', $request->purchase_date_from);
        }

        if ($request->filled('purchase_date_to')) {
            $query->whereDate('purchase_date', '<=', $request->purchase_date_to);
        }

        if ($request->filled('cost_min')) {
            $query->where('purchase_cost', '>=', $request->cost_min);
        }

        if ($request->filled('cost_max')) {
            $query->where('purchase_cost', '<=', $request->cost_max);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('asset_code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('category', function($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Get filtered assets
        $assets = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get summary statistics
        $totalAssets = Asset::count();
        $totalValue = Asset::sum('purchase_cost');
        $availableAssets = Asset::where('status', 'Available')->count();
        $disposedAssets = Asset::where('status', 'Disposed')->count();
        $inUseAssets = Asset::where('status', 'In Use')->count();
        $lostAssets = Asset::where('status', 'Lost')->count();
        
        // Assets by condition
        $goodCondition = Asset::where('condition', 'Good')->count();
        $fairCondition = Asset::where('condition', 'Fair')->count();
        $poorCondition = Asset::where('condition', 'Poor')->count();
        
        // Assets by category
        $assetsByCategory = Asset::select('categories.name', DB::raw('count(*) as total'))
            ->join('categories', 'assets.category_id', '=', 'categories.id')
            ->groupBy('categories.name')
            ->get();
            
        // Get filter options
        $categories = Category::all();
        $locations = Location::all();
        
        return view('assets.report', compact(
            'assets', 'categories', 'locations', 'totalAssets', 'totalValue',
            'availableAssets', 'disposedAssets', 'inUseAssets', 'lostAssets',
            'goodCondition', 'fairCondition', 'poorCondition', 'assetsByCategory'
        ));
    }

    /**
     * Display pending assets for admin approval
     */
    public function pendingAssets()
    {
        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized. Only admin users can access pending assets.');
        }

        \Log::info('pendingAssets method called', [
            'user' => auth()->user()->email,
            'role' => auth()->user()->role,
            'timestamp' => now()
        ]);

        $assets = Asset::where('approval_status', Asset::APPROVAL_PENDING)
            ->with(['category', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        \Log::info('Pending assets query result', [
            'count' => $assets->count(),
            'total' => $assets->total()
        ]);

        return view('admin.assets.pending', compact('assets'));
    }

    /**
     * Debug method for pending assets
     */
    public function debugPendingAssets()
    {
        \Log::info('debugPendingAssets method called', [
            'user' => auth()->user()->email,
            'role' => auth()->user()->role,
            'timestamp' => now()
        ]);

        $pendingCount = Asset::where('approval_status', Asset::APPROVAL_PENDING)->count();
        $allAssets = Asset::count();
        
        return response()->json([
            'debug_info' => [
                'user' => auth()->user(),
                'pending_assets_count' => $pendingCount,
                'total_assets_count' => $allAssets,
                'approval_statuses' => Asset::select('approval_status', \DB::raw('count(*) as count'))
                    ->groupBy('approval_status')
                    ->get(),
                'view_exists' => view()->exists('admin.assets.pending'),
                'timestamp' => now()
            ]
        ]);
    }

    /**
     * Approve an asset
     */
    public function approve(Request $request, Asset $asset)
    {
        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized. Only admin users can approve assets.');
        }

        if (!$asset->isPending()) {
            return redirect()->back()->with('error', 'Asset is not pending approval.');
        }

        $asset->update([
            'approval_status' => Asset::APPROVAL_APPROVED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Notify the purchasing user and GSU users
        $notificationService = app(NotificationService::class);
        $notificationService->notifyAssetApproved($asset);
        $notificationService->notifyGSUAssetApproved($asset);

        return redirect()->back()->with('success', 'Asset approved successfully.');
    }

    /**
     * Reject an asset
     */
    public function reject(Request $request, Asset $asset)
    {
        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized. Only admin users can reject assets.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        if (!$asset->isPending()) {
            return redirect()->back()->with('error', 'Asset is not pending approval.');
        }

        $asset->update([
            'approval_status' => Asset::APPROVAL_REJECTED,
            'rejection_reason' => $request->rejection_reason,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Notify the purchasing user
        $notificationService = app(NotificationService::class);
        $notificationService->notifyAssetRejected($asset);

        return redirect()->back()->with('success', 'Asset rejected successfully.');
    }

    /**
     * Show form for GSU to assign location to approved asset
     */
    public function assignLocationForm(Asset $asset)
    {
        // Only allow location assignment for approved assets without location
        if (!$asset->isApproved() || $asset->location_id) {
            return redirect()->back()->with('error', 'Asset is not eligible for location assignment.');
        }

        $locations = Location::orderBy('building')->orderBy('floor')->orderBy('room')->get();
        
        return view('gsu.assets.assign-location', compact('asset', 'locations'));
    }

    /**
     * Assign location to approved asset (GSU only)
     */
    public function assignLocation(Request $request, Asset $asset)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,id',
        ]);

        // Only allow location assignment for approved assets without location
        if (!$asset->isApproved() || $asset->location_id) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Asset is not eligible for location assignment.'], 400);
            }
            return redirect()->back()->with('error', 'Asset is not eligible for location assignment.');
        }

        $asset->update([
            'location_id' => $request->location_id,
            'original_location_id' => $request->location_id,
            'status' => Asset::STATUS_AVAILABLE, // Set to available once deployed
        ]);

        // Notify the purchasing user that their asset has been deployed
        $notificationService = app(NotificationService::class);
        $notificationService->notifyAssetDeployed($asset);

        if ($request->expectsJson()) {
            return response()->json(['success' => 'Location assigned successfully. Asset is now deployed and available.']);
        }

        return redirect()->route('gsu.assets.index')
            ->with('success', 'Location assigned successfully. Asset is now deployed and available.');
    }

    /**
     * Bulk deploy multiple assets to a single location (GSU only)
     */
    public function bulkDeploy(Request $request)
    {
        $request->validate([
            'asset_ids' => 'required|array|min:1',
            'asset_ids.*' => 'required|exists:assets,id',
            'location_id' => 'required|exists:locations,id',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $assetIds = $request->asset_ids;
                $locationId = $request->location_id;
                $deployedCount = 0;
                $skippedCount = 0;

                foreach ($assetIds as $assetId) {
                    $asset = Asset::find($assetId);
                    
                    // Only deploy approved assets without location
                    if ($asset && $asset->isApproved() && !$asset->location_id) {
                        $asset->update([
                            'location_id' => $locationId,
                            'original_location_id' => $locationId,
                            'status' => Asset::STATUS_AVAILABLE,
                        ]);

                        // Notify the purchasing user that their asset has been deployed
                        $notificationService = app(NotificationService::class);
                        $notificationService->notifyAssetDeployed($asset);
                        
                        $deployedCount++;
                    } else {
                        $skippedCount++;
                    }
                }

                $message = $deployedCount > 0 
                    ? "{$deployedCount} asset(s) deployed successfully." 
                    : "No assets were deployed.";
                
                if ($skippedCount > 0) {
                    $message .= " {$skippedCount} asset(s) were skipped (already deployed or not approved).";
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'deployed' => $deployedCount,
                    'skipped' => $skippedCount
                ]);
            });
        } catch (\Exception $e) {
            \Log::error('Bulk deployment failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to deploy assets: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pending assets count for admin dashboard
     */
    public function pendingCount()
    {
        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            return response()->json(['count' => 0]);
        }

        \Log::info('pendingCount method called', [
            'user' => auth()->user()->email,
            'role' => auth()->user()->role,
            'timestamp' => now()
        ]);

        $count = Asset::where('approval_status', Asset::APPROVAL_PENDING)->count();
        
        \Log::info('Pending count result', ['count' => $count]);
        
        return response()->json(['count' => $count]);
    }

    /**
     * Get deployment count for GSU dashboard (approved assets without location)
     */
    public function deploymentCount()
    {
        $count = Asset::where('approval_status', Asset::APPROVAL_APPROVED)
            ->whereNull('location_id')
            ->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Show transfer form for an asset
     */
    public function transferForm(Asset $asset)
    {
        // Only allow transfer for available assets with location
        if (!$asset->isAvailable() || !$asset->location_id) {
            return redirect()->back()->with('error', 'Asset is not available for transfer.');
        }

        $locations = Location::orderBy('building')->orderBy('floor')->orderBy('room')->get();
        
        return view('assets.transfer', compact('asset', 'locations'));
    }

    /**
     * Transfer asset to a new location
     */
    public function transfer(Request $request, Asset $asset)
    {
        $request->validate([
            'new_location_id' => 'required|exists:locations,id',
            'transfer_reason' => 'nullable|string|max:500',
        ]);

        // Only allow transfer for available assets with location
        if (!$asset->isAvailable() || !$asset->location_id) {
            return redirect()->back()->with('error', 'Asset is not available for transfer.');
        }

        // Check if the new location is different from current location
        if ($asset->location_id == $request->new_location_id) {
            return redirect()->back()->with('error', 'Asset is already at this location.');
        }

        $oldLocation = $asset->location;
        $newLocation = Location::find($request->new_location_id);

        try {
            return DB::transaction(function () use ($asset, $request, $oldLocation, $newLocation) {
                // Update asset location
                $asset->update([
                    'location_id' => $request->new_location_id,
                ]);

                // Add transfer notes to the automatically created change record if provided
                if ($request->transfer_reason) {
                    $latestChange = \App\Models\AssetChange::where('asset_id', $asset->id)
                        ->where('field', 'location_id')
                        ->where('change_type', \App\Models\AssetChange::TYPE_TRANSFER)
                        ->latest()
                        ->first();
                    
                    if ($latestChange) {
                        $latestChange->update(['notes' => $request->transfer_reason]);
                    }
                }

                // Send notification about the transfer
                $notificationService = new NotificationService();
                $notificationService->notifyAssetTransferred($asset, $oldLocation, $newLocation);

                return redirect()->route('assets.show', $asset)
                    ->with('success', "Asset {$asset->asset_code} has been transferred successfully from {$oldLocation->building} to {$newLocation->building}.");
            });
        } catch (\Exception $e) {
            Log::error('Asset transfer failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to transfer asset: ' . $e->getMessage())
                ->withInput();
        }
    }
}
