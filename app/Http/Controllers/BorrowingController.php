<?php

namespace App\Http\Controllers;2    

use App\Models\Borrowing;
use App\Models\Asset;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BorrowingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Borrowing::query();
        
        // If user is not admin or GSU, only show their own borrowings
        if ($user->role === 'user') {
            $query->where('borrower_id_number', $user->id_number);
        }
        
        // Apply filters if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('borrower_name', 'like', "%{$search}%")
                  ->orWhere('borrower_id_number', 'like', "%{$search}%")
                  ->orWhere('room', 'like', "%{$search}%");
            });
        }
        
        // Check for overdue items and update their status
        $overdueItems = Borrowing::where('status', 'active')
            ->where('due_date', '<', now()->format('Y-m-d'))
            ->get();
            
        foreach ($overdueItems as $item) {
            $item->status = 'overdue';
            $item->save();
        }
        
        $borrowings = $query->latest()->paginate(10);
        
        return view('borrowing.borrowing', compact('borrowings'));
    }

    /**
     * Get available assets by category for dynamic item selection.
     */
    public function getAvailableAssets(Request $request)
    {
        $categoryName = $request->input('category');
        
        if (!$categoryName) {
            return response()->json(['assets' => []]);
        }
        
        // Get category ID by name
        $category = Category::where('name', $categoryName)->first();
        
        if (!$category) {
            return response()->json(['assets' => []]);
        }
        
        // Get available assets in this category
        $assets = Asset::where('category_id', $category->id)
            ->where('status', 'Available')
            ->select('id', 'name', 'asset_code', 'condition')
            ->get();
        
        return response()->json(['assets' => $assets]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Borrowing $borrowing)
    {
        $user = auth()->user();
        
        // If user is not admin or GSU, only allow viewing their own borrowings
        if ($user->role === 'user' && $borrowing->borrower_id_number !== $user->id_number) {
            abort(403, 'Unauthorized access.');
        }
        
        return view('borrowing.show', compact('borrowing'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug logging
        \Log::info('Borrowing form submitted', [
            'all_data' => $request->all(),
            'has_category' => $request->has('category'),
            'category_value' => $request->input('category'),
            'has_items' => $request->has('items'),
            'items_value' => $request->input('items'),
        ]);
        
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'id_number' => 'required|string|max:255',
                'location_id' => 'required|exists:locations,id',
                'date' => 'required|date',
                'time' => 'required',
                'due_date' => 'required|date|after_or_equal:date',
                'category' => 'required|string|max:255',
                'items' => 'required|array',
                'purpose' => 'nullable|string',
            ]);
            
            // Get location details
            $location = \App\Models\Location::findOrFail($validated['location_id']);
            $roomInfo = $location->building . ' - Floor ' . $location->floor . ' - Room ' . $location->room;
            
            $borrowing = new Borrowing();
            $borrowing->borrower_name = $validated['name'];
            $borrowing->borrower_id_number = $validated['id_number'];
            $borrowing->room = $roomInfo;
            $borrowing->category = $validated['category'];
            $borrowing->items = $validated['items'];
            $borrowing->purpose = $validated['purpose'] ?? null;
            $borrowing->borrow_date = $validated['date'];
            $borrowing->borrow_time = $validated['time'];
            $borrowing->due_date = $validated['due_date'];
            $borrowing->status = 'active';
            
            $borrowing->save();
            
            // Update asset status to 'In Use' for borrowed items
            if (is_array($validated['items'])) {
                foreach ($validated['items'] as $assetId) {
                    $asset = Asset::find($assetId);
                    if ($asset && $asset->status === 'Available') {
                        $asset->status = 'In Use';
                        $asset->save();
                    }
                }
            }
            
            return redirect()->route('borrowing.index')
                ->with('success', 'Borrowing request created successfully!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create borrowing request. Please try again.')
                ->withInput();
        }
    }

    /**
     * Mark a borrowing as returned.
     */
    public function markAsReturned(Borrowing $borrowing)
    {
        $borrowing->status = 'returned';
        $borrowing->return_date = now();
        $borrowing->save();
        
        return redirect()->back()->with('success', 'Item marked as returned successfully!');
    }

    /**
     * Cancel a borrowing.
     */
    public function cancel(Borrowing $borrowing)
    {
        $borrowing->delete();
        
        return redirect()->back()->with('success', 'Borrowing cancelled successfully!');
    }

    // Add this method to your BorrowingController
    public function create()
    {
        $locations = \App\Models\Location::orderBy('building')
                        ->orderBy('floor')
                        ->orderBy('room')
                        ->get();
                        
        return view('borrowing.create', compact('locations'));
    }
}
