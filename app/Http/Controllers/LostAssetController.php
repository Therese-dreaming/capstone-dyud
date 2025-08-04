<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetChange;
use App\Models\Borrowing;
use App\Models\LostAsset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LostAssetController extends Controller
{
    /**
     * Display a listing of lost assets.
     */
    public function index(Request $request)
    {
        $query = LostAsset::with(['asset.category', 'asset.location', 'reportedBy', 'lastBorrower']);
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('asset', function($assetQuery) use ($search) {
                    $assetQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('asset_code', 'like', "%{$search}%");
                })
                ->orWhereHas('reportedBy', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                });
            });
        }
        
        $lostAssets = $query->latest()->paginate(15);
        
        return view('lost-assets.index', compact('lostAssets'));
    }

    /**
     * Show the form for reporting an asset as lost.
     */
    public function create(Asset $asset)
    {
        // Get the last borrower if the asset was borrowed
        $lastBorrowing = $asset->borrowings()
            ->whereIn('status', [Borrowing::STATUS_APPROVED, Borrowing::STATUS_OVERDUE, Borrowing::STATUS_RETURNED])
            ->latest()
            ->first();

        return view('lost-assets.create', compact('asset', 'lastBorrowing'));
    }

    /**
     * Store a newly created lost asset report.
     */
    public function store(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'last_seen_date' => 'required|date|before_or_equal:today',
            'description' => 'required|string|max:1000',
            'last_known_location' => 'nullable|string|max:500',
            'investigation_notes' => 'nullable|string|max:1000',
        ]);

        try {
            return DB::transaction(function () use ($validated, $asset, $request) {
                // Get the last borrower if the asset was borrowed
                $lastBorrowing = $asset->borrowings()
                    ->whereIn('status', [Borrowing::STATUS_APPROVED, Borrowing::STATUS_OVERDUE, Borrowing::STATUS_RETURNED])
                    ->latest()
                    ->first();

                $lostAsset = LostAsset::create([
                    'asset_id' => $asset->id,
                    'reported_by' => auth()->id(),
                    'last_borrower_id' => $lastBorrowing ? $lastBorrowing->user_id : null,
                    'last_seen_date' => $validated['last_seen_date'],
                    'reported_date' => now()->toDateString(),
                    'description' => $validated['description'],
                    'last_known_location' => $validated['last_known_location'],
                    'investigation_notes' => $validated['investigation_notes'],
                    'status' => LostAsset::STATUS_INVESTIGATING,
                ]);

                // Update asset status to 'Lost'
                $asset->update(['status' => 'Lost']);

                // Record the change
                Asset::recordChange(
                    $asset->id,
                    AssetChange::TYPE_STATUS_CHANGE,
                    'status',
                    ucfirst($asset->getOriginal('status')),
                    'Lost',
                    "Asset reported as lost. Last seen: {$validated['last_seen_date']}. Description: {$validated['description']}"
                );

                return redirect()->route('lost-assets.index')
                    ->with('success', 'Asset reported as lost successfully. Investigation has been initiated.');
            });
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to report asset as lost: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified lost asset.
     */
    public function show(LostAsset $lostAsset)
    {
        $lostAsset->load(['asset.category', 'asset.location', 'reportedBy', 'lastBorrower']);
        return view('lost-assets.show', compact('lostAsset'));
    }

    /**
     * Update the status of a lost asset (found, permanently lost, etc.).
     */
    public function updateStatus(Request $request, LostAsset $lostAsset)
    {
        $validated = $request->validate([
            'status' => 'required|in:found,permanently_lost',
            'found_date' => 'required_if:status,found|date|before_or_equal:today',
            'found_location' => 'required_if:status,found|string|max:500',
            'found_notes' => 'nullable|string|max:1000',
        ]);

        try {
            return DB::transaction(function () use ($validated, $lostAsset) {
                $oldStatus = $lostAsset->status;
                
                $lostAsset->update([
                    'status' => $validated['status'],
                    'found_date' => $validated['status'] === 'found' ? $validated['found_date'] : null,
                    'found_location' => $validated['status'] === 'found' ? $validated['found_location'] : null,
                    'found_notes' => $validated['status'] === 'found' ? $validated['found_notes'] : null,
                ]);

                // Update asset status based on lost asset status
                if ($validated['status'] === 'found') {
                    $lostAsset->asset->update(['status' => 'Available']);
                    
                    Asset::recordChange(
                        $lostAsset->asset->id,
                        AssetChange::TYPE_STATUS_CHANGE,
                        'status',
                        'Lost',
                        'Available',
                        "Asset found. Found date: {$validated['found_date']}. Location: {$validated['found_location']}"
                    );
                } elseif ($validated['status'] === 'permanently_lost') {
                    $lostAsset->asset->update(['status' => 'Lost']);
                    
                    Asset::recordChange(
                        $lostAsset->asset->id,
                        AssetChange::TYPE_STATUS_CHANGE,
                        'status',
                        'Lost',
                        'Lost',
                        "Asset permanently lost. Investigation closed."
                    );
                }

                $statusMessage = $validated['status'] === 'found' ? 'Asset marked as found successfully.' : 'Asset marked as permanently lost.';
                
                return redirect()->back()
                    ->with('success', $statusMessage);
            });
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update lost asset status: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified lost asset record.
     */
    public function destroy(LostAsset $lostAsset)
    {
        try {
            return DB::transaction(function () use ($lostAsset) {
                // If the asset is still marked as lost, restore it to available
                if ($lostAsset->asset->status === 'Lost') {
                    $lostAsset->asset->update(['status' => 'Available']);
                    
                    Asset::recordChange(
                        $lostAsset->asset->id,
                        AssetChange::TYPE_STATUS_CHANGE,
                        'status',
                        'Lost',
                        'Available',
                        "Lost asset record deleted. Asset restored to available status."
                    );
                }
                
                $lostAsset->delete();
                
                return redirect()->route('lost-assets.index')
                    ->with('success', 'Lost asset record deleted successfully.');
            });
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete lost asset record: ' . $e->getMessage());
        }
    }
}
