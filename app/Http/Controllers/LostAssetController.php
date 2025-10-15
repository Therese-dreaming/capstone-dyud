<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetChange;
use App\Models\LostAsset;
use App\Models\User;
use App\Exports\LostAssetsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LostAssetController extends Controller
{
    /**
     * Display a listing of lost assets.
     */
    public function index(Request $request)
    {
        $query = LostAsset::with(['asset.category', 'asset.location', 'reportedBy']);
        
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
        
        // Overall counts from database (not page-limited)
        $counts = [
            'lost' => LostAsset::where('status', LostAsset::STATUS_LOST)->count(),
            'resolved' => LostAsset::where('status', LostAsset::STATUS_RESOLVED)->count(),
        ];
        
        // Check if user is GSU and return appropriate view
        if (auth()->user()->role === 'gsu') {
            return view('lost-assets.gsu-index', compact('lostAssets', 'counts'));
        }
        
        return view('lost-assets.index', compact('lostAssets', 'counts'));
    }

    /**
     * Show the form for reporting an asset as lost.
     */
    public function create(Asset $asset)
    {
        // Borrowings removed; no last borrower information available
        $lastBorrowing = null;

        // Check if user is GSU and return appropriate view
        if (auth()->user()->role === 'gsu') {
            return view('lost-assets.gsu-create', compact('asset', 'lastBorrowing'));
        }
        
        return view('lost-assets.create', compact('asset', 'lastBorrowing'));
    }

    /**
     * Store a newly created lost asset report.
     */
    public function store(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'investigation_notes' => 'nullable|string|max:1000',
        ]);

        try {
            return DB::transaction(function () use ($validated, $asset, $request) {
                // Borrowings removed; no last borrower information available
                $lastBorrowing = null;

                // Determine last known location automatically from current location, fallback to original
                $asset->load(['location', 'originalLocation']);
                $lastKnownLocation = 'Unknown';
                if ($asset->location) {
                    $lastKnownLocation = $asset->location->building . ' - Floor ' . $asset->location->floor . ' - Room ' . $asset->location->room;
                } elseif ($asset->originalLocation) {
                    $lastKnownLocation = $asset->originalLocation->building . ' - Floor ' . $asset->originalLocation->floor . ' - Room ' . $asset->originalLocation->room;
                }

                $lostAsset = LostAsset::create([
                    'asset_id' => $asset->id,
                    'reported_by' => auth()->id(),
                    'reported_date' => now()->toDateString(),
                    'last_known_location' => $lastKnownLocation,
                    'investigation_notes' => $validated['investigation_notes'] ?? null,
                    'status' => LostAsset::STATUS_LOST,
                ]);

                // Update asset status to 'Lost'
                $asset->update(['status' => 'Lost']);

                // Record the change
                \App\Traits\TracksAssetChanges::recordChange(
                    $asset->id,
                    AssetChange::TYPE_STATUS_CHANGE,
                    'status',
                    ucfirst($asset->getOriginal('status')),
                    'Lost',
                    "Asset reported as lost. Location: {$lastKnownLocation}"
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
        $lostAsset->load(['asset.category', 'asset.location', 'reportedBy']);
        
        // Check if user is GSU and return appropriate view
        if (auth()->user()->role === 'gsu') {
            return view('lost-assets.gsu-show', compact('lostAsset'));
        }
        
        return view('lost-assets.show', compact('lostAsset'));
    }

    /**
     * Update the status of a lost asset (found, permanently lost, etc.).
     */
    public function updateStatus(Request $request, LostAsset $lostAsset)
    {
        $validated = $request->validate([
            'status' => 'required|in:resolved',
            'found_date' => 'required|date|before_or_equal:today',
            'found_notes' => 'nullable|string|max:1000',
        ]);

        try {
            return DB::transaction(function () use ($validated, $lostAsset) {
                $oldStatus = $lostAsset->status;
                
                $lostAsset->update([
                    'status' => $validated['status'],
                    'found_date' => $validated['found_date'],
                    'found_notes' => $validated['found_notes'] ?? null,
                ]);

                // Update asset status based on lost asset status
                if ($validated['status'] === 'resolved') {
                    $lostAsset->asset->update(['status' => 'Available']);
                    
                    \App\Traits\TracksAssetChanges::recordChange(
                        $lostAsset->asset->id,
                        AssetChange::TYPE_STATUS_CHANGE,
                        'status',
                        'Lost',
                        'Available',
                        "Asset found and resolved. Found date: {$validated['found_date']}"
                    );
                }

                $statusMessage = 'Asset marked as resolved successfully.';
                
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
                    
                    \App\Traits\TracksAssetChanges::recordChange(
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

    public function export(Request $request)
    {
        $query = LostAsset::with(['asset.category', 'asset.location', 'reportedBy']);

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

        $lostAssets = $query->orderBy('reported_date', 'desc')->get();

        return Excel::download(new LostAssetsExport($lostAssets), 'lost-assets.xlsx');
    }
}
