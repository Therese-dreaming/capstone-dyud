<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BorrowingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Borrowing::query();
        
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
     * Display the specified resource.
     */
    public function show(Borrowing $borrowing)
    {
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
            
            \Log::info('Borrowing created successfully', ['borrowing_id' => $borrowing->id]);
            
            return redirect()->route('borrowing.index')->with('success', 'Item borrowed successfully!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Borrowing validation failed', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            \Log::error('Borrowing creation failed', [
                'error' => $e->getMessage(),
                'input' => $request->all()
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to create borrowing. Please try again.')
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
