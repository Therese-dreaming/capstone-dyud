<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
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
            ->with(['category', 'location', 'approvedBy', 'createdBy'])
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
        
        return view('purchasing.assets.create', compact('categories'));
    }

    /**
     * Store a newly created asset in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'asset_code' => 'required|string|max:255|unique:assets',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'condition' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purchase_cost' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
        ]);

        $asset = Asset::create([
            'asset_code' => $request->asset_code,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'location_id' => null, // No location for purchasing
            'original_location_id' => null,
            'condition' => $request->condition,
            'description' => $request->description,
            'purchase_cost' => $request->purchase_cost,
            'purchase_date' => $request->purchase_date,
            'status' => Asset::STATUS_UNVERIFIED, // Default status for new assets
            'approval_status' => Asset::APPROVAL_PENDING, // Pending approval
            'created_by' => Auth::id(),
        ]);

        // Notify all admin users about the new asset pending approval
        $this->notificationService->notifyAdminsOfPendingAsset($asset);

        return redirect()->route('purchasing.assets.index')
            ->with('success', 'Asset created successfully and submitted for approval.');
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

        $asset->load(['category', 'location', 'approvedBy', 'createdBy']);
        
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
            'asset_code' => 'required|string|max:255|unique:assets,asset_code,' . $asset->id,
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'condition' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purchase_cost' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
        ]);

        $asset->update([
            'asset_code' => $request->asset_code,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'condition' => $request->condition,
            'description' => $request->description,
            'purchase_cost' => $request->purchase_cost,
            'purchase_date' => $request->purchase_date,
        ]);

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
}
