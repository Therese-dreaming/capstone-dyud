<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Asset;
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
        $query = Borrowing::with(['user', 'asset.category', 'asset.location', 'approvedBy']);
        
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
     * Display the specified borrowing request.
     */
    public function show(Borrowing $borrowing)
    {
        $borrowing->load(['user', 'asset.category', 'asset.location', 'approvedBy']);
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
                
                // Update asset status to 'In Use'
                $borrowing->asset->update(['status' => 'In Use']);
                
                return redirect()->back()
                    ->with('success', 'Borrowing request approved successfully. Asset status updated to "In Use".');
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
                
                // Update asset status back to 'Available'
                $borrowing->asset->update(['status' => 'Available']);
                
                return redirect()->back()
                    ->with('success', 'Asset returned successfully. Asset status updated to "Available".');
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
            // If the borrowing was approved and asset is in use, update asset status
            if ($borrowing->status === Borrowing::STATUS_APPROVED) {
                $borrowing->asset->update(['status' => 'Available']);
            }
            
            $borrowing->delete();
            
            return redirect()->back()
                ->with('success', 'Borrowing request deleted successfully.');
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
