<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\Warranty;
use App\Models\Dispose;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::with(['category', 'location', 'originalLocation', 'warranty'])->paginate(10);
        return view('assets.index', compact('assets'));
    }

    public function gsuIndex()
    {
        $assets = Asset::with(['category', 'location', 'originalLocation', 'warranty'])->paginate(10);
        return view('assets.gsu-index', compact('assets'));
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
                
                // Check if we should redirect back to a location page
                $redirectLocation = $request->get('redirect_to_location');
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
            'warranty'
        ]);
        
        // Get the active tab from request (default to maintenance)
        $activeTab = $request->get('tab', 'maintenance');
        
        // Paginate history records to prevent overloading
        $maintenances = $asset->maintenances()->orderBy('maintenance_date', 'desc')->paginate(10);
        $disposes = $asset->disposes()->orderBy('disposal_date', 'desc')->paginate(10);
        $changes = $asset->changes()->with('user')->orderBy('created_at', 'desc')->paginate(10);
        
        // Check if user is GSU and return appropriate view
        if (auth()->user()->role === 'gsu') {
            return view('assets.gsu-show', compact('asset'));
        }
        
        return view('assets.show', compact('asset', 'maintenances', 'disposes', 'changes', 'activeTab'));
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
                // Create disposal record
                Dispose::create([
                    'asset_id' => $asset->id,
                    'disposal_reason' => $validated['disposal_reason'],
                    'disposal_date' => now()->toDateString(),
                    'disposed_by' => 'System Admin', // You might want to use auth()->user()->name if you have authentication
                ]);

                // Update asset status to disposed
                $asset->update([
                    'status' => 'Disposed'
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
}
