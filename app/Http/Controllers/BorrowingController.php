<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Asset;
use App\Models\AssetChange;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BorrowingController extends Controller
{
    /**
     * Display a listing of all borrowing requests (admin view).
     */
    public function index(Request $request)
    {
        $query = Borrowing::with(['user', 'asset.category', 'asset.location', 'approvedBy', 'location']);
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('category_id')) {
            $query->whereHas('asset.category', function($q) use ($request) {
                $q->where('id', $request->category_id);
            });
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('borrower_name', 'like', "%{$search}%")
                  ->orWhere('borrower_id_number', 'like', "%{$search}%")
                  ->orWhereHas('asset', function($assetQuery) use ($search) {
                      $assetQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('asset_code', 'like', "%{$search}%");
                  });
            });
        }
        
        // Check for overdue items and update their status
        $this->updateOverdueBorrowings();
        
        $borrowings = $query->latest()->paginate(15);
        $categories = Category::all();
        
        return view('borrowings.index', compact('borrowings', 'categories'));
    }

    /**
     * Display a listing of ongoing borrowings (approved and overdue).
     */
    public function ongoing(Request $request)
    {
        $query = Borrowing::with(['user', 'asset.category', 'asset.location', 'approvedBy', 'location'])
            ->whereIn('status', ['approved', 'overdue']);
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('category_id')) {
            $query->whereHas('asset.category', function($q) use ($request) {
                $q->where('id', $request->category_id);
            });
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('borrower_name', 'like', "%{$search}%")
                  ->orWhere('borrower_id_number', 'like', "%{$search}%")
                  ->orWhereHas('asset', function($assetQuery) use ($search) {
                      $assetQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('asset_code', 'like', "%{$search}%");
                  });
            });
        }
        
        // Check for overdue items and update their status
        $this->updateOverdueBorrowings();
        
        $borrowings = $query->latest()->paginate(15);
        $categories = Category::all();
        
        return view('borrowings.ongoing', compact('borrowings', 'categories'));
    }

    /**
     * Display the specified borrowing request.
     */
    public function show(Borrowing $borrowing)
    {
        $borrowing->load(['user', 'asset.category', 'asset.location', 'approvedBy', 'location']);
        return view('borrowings.show', compact('borrowing'));
    }

    /**
     * Approve a borrowing request.
     */
    public function approve(Borrowing $borrowing)
    {
        try {
            return DB::transaction(function () use ($borrowing) {
                // Check if asset is still available
                if ($borrowing->asset->status !== 'Available') {
                    return redirect()->back()
                        ->with('error', 'Asset is no longer available for borrowing.');
                }
                
                // Update borrowing status
                $borrowing->update([
                    'status' => Borrowing::STATUS_APPROVED,
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);
                
                // Store original location if not already stored
                if (!$borrowing->asset->original_location_id) {
                    $borrowing->asset->update(['original_location_id' => $borrowing->asset->location_id]);
                }
                
                // Update asset location to borrowing location and status to 'In Use'
                // If borrowing doesn't have a location_id, keep the asset at its current location
                $newLocationId = $borrowing->location_id ?? $borrowing->asset->location_id;
                $oldLocation = $borrowing->asset->location;
                $oldStatus = $borrowing->asset->status;
                
                $borrowing->asset->update([
                    'location_id' => $newLocationId,
                    'status' => 'In Use'
                ]);
                
                // Record the changes
                $locationDescription = $borrowing->location 
                    ? "{$borrowing->location->building} - Floor {$borrowing->location->floor} - Room {$borrowing->location->room}"
                    : $borrowing->custom_location;
                
                Asset::recordChange(
                    $borrowing->asset->id,
                    AssetChange::TYPE_BORROWING_APPROVED,
                    'status',
                    ucfirst($oldStatus),
                    'In Use',
                    "Asset approved for borrowing to {$locationDescription}"
                );
                
                if ($oldLocation->id !== $newLocationId) {
                    $newLocationDescription = $borrowing->location 
                        ? "{$borrowing->location->building} - Floor {$borrowing->location->floor} - Room {$borrowing->location->room}"
                        : $borrowing->custom_location;
                        
                    Asset::recordChange(
                        $borrowing->asset->id,
                        AssetChange::TYPE_LOCATION_CHANGE,
                        'location_id',
                        "{$oldLocation->building} - Floor {$oldLocation->floor} - Room {$oldLocation->room}",
                        $newLocationDescription,
                        "Location changed due to borrowing approval"
                    );
                }
                
                return redirect()->back()
                    ->with('success', 'Borrowing request approved successfully. Asset location updated and status changed to "In Use".');
            });
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to approve borrowing request: ' . $e->getMessage());
        }
    }

    /**
     * Reject a borrowing request.
     */
    public function reject(Request $request, Borrowing $borrowing)
    {
        $request->validate([
            'notes' => 'required|string|max:500',
        ]);
        
        try {
            $borrowing->update([
                'status' => Borrowing::STATUS_REJECTED,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'notes' => $request->notes,
            ]);
            
            return redirect()->back()
                ->with('success', 'Borrowing request rejected successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to reject borrowing request: ' . $e->getMessage());
        }
    }

    /**
     * Mark a borrowing as returned.
     */
    public function return(Borrowing $borrowing)
    {
        try {
            return DB::transaction(function () use ($borrowing) {
                // Update borrowing status
                $borrowing->update([
                    'status' => Borrowing::STATUS_RETURNED,
                    'return_date' => now(),
                ]);
                
                // Restore asset to original location and update status to 'Available'
                $originalLocationId = $borrowing->asset->original_location_id ?? $borrowing->asset->location_id;
                $oldLocation = $borrowing->asset->location;
                $oldStatus = $borrowing->asset->status;
                
                $borrowing->asset->update([
                    'location_id' => $originalLocationId,
                    'status' => 'Available'
                ]);
                
                // Record the changes
                Asset::recordChange(
                    $borrowing->asset->id,
                    AssetChange::TYPE_BORROWING_RETURNED,
                    'status',
                    ucfirst($oldStatus),
                    'Available',
                    "Asset returned from borrowing"
                );
                
                if ($oldLocation->id !== $originalLocationId) {
                    $originalLocation = $borrowing->asset->originalLocation;
                    Asset::recordChange(
                        $borrowing->asset->id,
                        AssetChange::TYPE_LOCATION_CHANGE,
                        'location_id',
                        "{$oldLocation->building} - Floor {$oldLocation->floor} - Room {$oldLocation->room}",
                        "{$originalLocation->building} - Floor {$originalLocation->floor} - Room {$originalLocation->room}",
                        "Location restored to original location after return"
                    );
                }
                
                return redirect()->back()
                    ->with('success', 'Asset returned successfully. Asset restored to original location and status updated to "Available".');
            });
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to mark asset as returned: ' . $e->getMessage());
        }
    }

    /**
     * Cancel/Delete a borrowing request.
     */
    public function destroy(Borrowing $borrowing)
    {
        try {
            return DB::transaction(function () use ($borrowing) {
                // If the borrowing was approved and asset is in use, restore it to original location
                if ($borrowing->status === Borrowing::STATUS_APPROVED) {
                    $originalLocationId = $borrowing->asset->original_location_id ?? $borrowing->asset->location_id;
                    $borrowing->asset->update([
                        'location_id' => $originalLocationId,
                        'status' => 'Available'
                    ]);
                }
                
                $borrowing->delete();
                
                return redirect()->back()
                    ->with('success', 'Borrowing request deleted successfully.');
            });
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete borrowing request: ' . $e->getMessage());
        }
    }

    /**
     * Get borrowing statistics for dashboard.
     */
    public function statistics()
    {
        $stats = [
            'total_requests' => Borrowing::count(),
            'pending_requests' => Borrowing::where('status', Borrowing::STATUS_PENDING)->count(),
            'approved_requests' => Borrowing::where('status', Borrowing::STATUS_APPROVED)->count(),
            'overdue_requests' => Borrowing::where('status', Borrowing::STATUS_OVERDUE)->count(),
            'returned_requests' => Borrowing::where('status', Borrowing::STATUS_RETURNED)->count(),
        ];
        
        return response()->json($stats);
    }

    /**
     * Update overdue borrowings status.
     */
    private function updateOverdueBorrowings()
    {
        $overdueBorrowings = Borrowing::where('status', Borrowing::STATUS_APPROVED)
            ->where('due_date', '<', now())
            ->get();
            
        foreach ($overdueBorrowings as $borrowing) {
            $borrowing->update(['status' => Borrowing::STATUS_OVERDUE]);
        }
    }
}
