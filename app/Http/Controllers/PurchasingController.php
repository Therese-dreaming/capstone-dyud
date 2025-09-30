<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Semester;
use App\Models\Warranty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class PurchasingController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of assets created by purchasing.
     */
    public function index()
    {
        $assets = Asset::where('created_by', Auth::id())
            ->with(['category', 'location', 'approvedBy', 'createdBy', 'warranty'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('purchasing.assets.index', compact('assets'));
    }

    /**
     * Show the form for creating a new asset.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        
        // Get current semester for display
        $currentSemester = Semester::current() ?? Semester::forDate(now());
        
        return view('purchasing.assets.create', compact('categories', 'currentSemester'));
    }

    /**
     * Store a newly created asset in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1|max:100',
            'category_id' => 'required|exists:categories,id',
            'condition' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purchase_cost' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            // Warranty validation
            'manufacturer' => 'required|string|max:255',
            'model' => 'required|string|max:255',
        ]);

        $quantity = $request->quantity;
        $createdAssets = [];
        
        // Get the category for asset code generation
        $category = Category::find($request->category_id);
        
        // Get the highest sequence number for this category
        $latestAsset = Asset::where('category_id', $category->id)
            ->where('asset_code', 'like', $category->code . '%')
            ->orderByRaw('CAST(SUBSTRING(asset_code, -4) AS UNSIGNED) DESC')
            ->first();
            
        $startingSequence = $latestAsset ? (int)substr($latestAsset->asset_code, -4) + 1 : 1;
        
        // Auto-detect current semester
        $currentSemester = Semester::current() ?? Semester::forDate(now());
        
        // Create multiple assets based on quantity
        for ($i = 0; $i < $quantity; $i++) {
            // Generate unique asset code for each asset
            $sequence = $startingSequence + $i;
            $assetCode = $category->code . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            
            // Create the asset
            $asset = Asset::create([
                'asset_code' => $assetCode,
                'name' => $request->name,
                'category_id' => $request->category_id,
                'condition' => $request->condition,
                'description' => $request->description,
                'purchase_cost' => $request->purchase_cost,
                'purchase_date' => $request->purchase_date,
                'status' => 'Available', // Default status
                'approval_status' => 'pending', // Pending approval from admin
                'created_by' => Auth::id(),
                'registered_semester_id' => $currentSemester?->id, // Auto-assign current semester
            ]);

            // Create warranty record for each asset
            Warranty::create([
                'asset_id' => $asset->id,
                'manufacturer' => $request->manufacturer,
                'model' => $request->model,
                'warranty_expiry' => $request->warranty_expiry,
            ]);
            
            // Notify all admin users about the new asset pending approval
            $this->notificationService->notifyAdminsOfPendingAsset($asset);
            
            $createdAssets[] = $asset;
        }

        $message = $quantity === 1 
            ? 'Asset and warranty information created successfully and submitted for approval.' 
            : "{$quantity} assets and warranties created successfully and submitted for approval.";

        return redirect()->route('purchasing.assets.index')
            ->with('success', $message);
    }

    /**
     * Display the specified asset.
     */
    public function show(Asset $asset)
    {
        // Ensure purchasing user can only view their own assets
        if ($asset->created_by !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $asset->load(['category', 'location', 'approvedBy', 'createdBy', 'warranty']);
        
        return view('purchasing.assets.show', compact('asset'));
    }

    /**
     * Show the form for editing the specified asset.
     */
    public function edit(Asset $asset)
    {
        // Ensure purchasing user can only edit their own pending assets
        if ($asset->created_by !== Auth::id() || !$asset->isPending()) {
            abort(403, 'Unauthorized access or asset cannot be edited.');
        }

        $categories = Category::orderBy('name')->get();
        $asset->load(['warranty']);
        
        return view('purchasing.assets.edit', compact('asset', 'categories'));
    }

    /**
     * Update the specified asset in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        // Ensure purchasing user can only update their own pending assets
        if ($asset->created_by !== Auth::id() || !$asset->isPending()) {
            abort(403, 'Unauthorized access or asset cannot be updated.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'condition' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purchase_cost' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            // Warranty validation
            'manufacturer' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'warranty_expiry' => 'required|date|after:today',
        ]);

        $asset->update([
            // asset_code is not updatable - it remains the same
            'name' => $request->name,
            'category_id' => $request->category_id,
            'condition' => $request->condition,
            'description' => $request->description,
            'purchase_cost' => $request->purchase_cost,
            'purchase_date' => $request->purchase_date,
        ]);

        // Update warranty record
        $asset->warranty()->updateOrCreate(
            ['asset_id' => $asset->id],
            [
                'manufacturer' => $request->manufacturer,
                'model' => $request->model,
                'warranty_expiry' => $request->warranty_expiry,
            ]
        );

        return redirect()->route('purchasing.assets.index')
            ->with('success', 'Asset updated successfully.');
    }

    /**
     * Remove the specified asset from storage.
     */
    public function destroy(Asset $asset)
    {
        // Ensure purchasing user can only delete their own pending assets
        if ($asset->created_by !== Auth::id() || !$asset->isPending()) {
            abort(403, 'Unauthorized access or asset cannot be deleted.');
        }

        $asset->delete();

        return redirect()->route('purchasing.assets.index')
            ->with('success', 'Asset deleted successfully.');
    }

    /**
     * Generate a unique asset code
     */
    private function generateAssetCode()
    {
        do {
            // Generate asset code format: AST-YYYY-XXXXXX (e.g., AST-2024-001234)
            $year = date('Y');
            $randomNumber = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $assetCode = "AST-{$year}-{$randomNumber}";
        } while (Asset::where('asset_code', $assetCode)->exists());

        return $assetCode;
    }
}
