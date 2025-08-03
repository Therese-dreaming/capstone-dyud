<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Location;
use App\Models\Asset;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserBorrowingController extends Controller
{
    /**
     * Display a listing of the user's borrowings.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Borrowing::where('borrower_id_number', $user->id_number);
        
        // Apply tab filtering
        $tab = $request->get('tab', 'all');
        switch ($tab) {
            case 'current':
                $query->whereIn('status', ['active', 'overdue']);
                break;
            case 'overdue':
                $query->where('status', 'overdue');
                break;
            case 'returned':
                $query->where('status', 'returned');
                break;
            default:
                // Show all borrowings
                break;
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
                $q->where('category', 'like', "%{$search}%")
                  ->orWhere('room', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%");
            });
        }
        
        // Apply date range filter
        if ($request->has('date_range') && $request->date_range) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('borrow_date', today());
                    break;
                case 'week':
                    $query->whereBetween('borrow_date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('borrow_date', now()->month);
                    break;
            }
        }
        
        // Apply sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('borrow_date', 'asc');
                break;
            case 'due_date':
                $query->orderBy('due_date', 'asc');
                break;
            default:
                $query->latest();
                break;
        }
        
        // Check for overdue items and update their status
        $overdueItems = Borrowing::where('borrower_id_number', $user->id_number)
            ->where('status', 'active')
            ->where('due_date', '<', now()->format('Y-m-d'))
            ->get();
            
        foreach ($overdueItems as $item) {
            $item->status = 'overdue';
            $item->save();
        }
        
        $borrowings = $query->paginate(10);
        
        // Breadcrumbs for navigation
        $breadcrumbs = [
            ['title' => 'My Borrowings', 'url' => route('user.borrowing.index')]
        ];
        
        return view('user.borrowing.index', compact('borrowings', 'breadcrumbs'));
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
     * Show the form for creating a new borrowing request.
     */
    public function create()
    {
        $locations = Location::orderBy('building')
                        ->orderBy('floor')
                        ->orderBy('room')
                        ->get();
        
        // Breadcrumbs for navigation
        $breadcrumbs = [
            ['title' => 'My Borrowings', 'url' => route('user.borrowing.index')],
            ['title' => 'New Request', 'url' => route('user.borrowing.create')]
        ];
                        
        return view('user.borrowing.create', compact('locations', 'breadcrumbs'));
    }

    /**
     * Store a newly created borrowing request.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'id_number' => 'required|string|max:255',
                'location_id' => 'required|exists:locations,id',
                'request_date' => 'required|date',
                'due_date' => 'required|date|after:request_date',
                'category' => 'required|string|max:255',
                'item_name' => 'required|string|max:255',
                'quantity' => 'required|integer|min:1',
                'purpose' => 'nullable|string',
            ]);
            
            // Verify that the user is creating a request for themselves
            if ($validated['id_number'] !== $user->id_number) {
                return redirect()->back()
                    ->with('error', 'You can only create borrowing requests for yourself.')
                    ->withInput();
            }
            
            // Get location details
            $location = Location::findOrFail($validated['location_id']);
            $roomInfo = $location->building . ' - Floor ' . $location->floor . ' - Room ' . $location->room;
            
            // Get category ID
            $category = Category::where('name', $validated['category'])->first();
            if (!$category) {
                return redirect()->back()
                    ->with('error', 'Invalid category selected.')
                    ->withInput();
            }
            
            // Check available assets for the requested item and category
            $availableAssets = Asset::where('category_id', $category->id)
                ->where('name', 'like', '%' . $validated['item_name'] . '%')
                ->where('status', 'Available')
                ->get();
            
            if ($availableAssets->count() < $validated['quantity']) {
                return redirect()->back()
                    ->with('error', 'Insufficient available assets. Only ' . $availableAssets->count() . ' items available.')
                    ->withInput();
            }
            
            // Automatically assign assets
            $assignedAssets = $availableAssets->take($validated['quantity']);
            $assetNames = $assignedAssets->pluck('name')->toArray();
            
            $borrowing = new Borrowing();
            $borrowing->borrower_name = $validated['name'];
            $borrowing->borrower_id_number = $validated['id_number'];
            $borrowing->room = $roomInfo;
            $borrowing->category = $validated['category'];
            $borrowing->items = $assetNames;
            $borrowing->purpose = $validated['purpose'] ?? null;
            $borrowing->borrow_date = $validated['request_date'];
            $borrowing->borrow_time = now()->format('H:i');
            $borrowing->due_date = $validated['due_date'];
            $borrowing->status = 'active';
            
            $borrowing->save();
            
            // Update asset status to 'In Use' for assigned items
            foreach ($assignedAssets as $asset) {
                $asset->status = 'In Use';
                $asset->save();
            }
            
            return redirect()->route('user.borrowing.index')
                ->with('success', 'Borrowing request submitted successfully! ' . count($assetNames) . ' items have been assigned to you.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to submit borrowing request. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified borrowing.
     */
    public function show(Borrowing $borrowing)
    {
        $user = auth()->user();
        
        // Ensure user can only view their own borrowings
        if ($borrowing->borrower_id_number !== $user->id_number) {
            abort(403, 'Unauthorized access.');
        }
        
        // Breadcrumbs for navigation
        $breadcrumbs = [
            ['title' => 'My Borrowings', 'url' => route('user.borrowing.index')],
            ['title' => 'Borrowing Details', 'url' => route('user.borrowing.show', $borrowing)]
        ];
        
        return view('user.borrowing.show', compact('borrowing', 'breadcrumbs'));
    }

    /**
     * Show current borrowings (active and overdue).
     */
    public function current(Request $request)
    {
        $user = auth()->user();
        $query = Borrowing::where('borrower_id_number', $user->id_number)
                         ->whereIn('status', ['active', 'overdue']);
        
        // Apply search and filters
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('category', 'like', "%{$search}%")
                  ->orWhere('room', 'like', "%{$search}%");
            });
        }
        
        $borrowings = $query->latest()->paginate(10);
        
        // Breadcrumbs for navigation
        $breadcrumbs = [
            ['title' => 'My Borrowings', 'url' => route('user.borrowing.index')],
            ['title' => 'Current Items', 'url' => route('user.borrowing.current')]
        ];
        
        return view('user.borrowing.index', compact('borrowings', 'breadcrumbs'))->with('activeTab', 'current');
    }

    /**
     * Show overdue borrowings.
     */
    public function overdue(Request $request)
    {
        $user = auth()->user();
        $query = Borrowing::where('borrower_id_number', $user->id_number)
                         ->where('status', 'overdue');
        
        // Apply search and filters
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('category', 'like', "%{$search}%")
                  ->orWhere('room', 'like', "%{$search}%");
            });
        }
        
        $borrowings = $query->latest()->paginate(10);
        
        // Breadcrumbs for navigation
        $breadcrumbs = [
            ['title' => 'My Borrowings', 'url' => route('user.borrowing.index')],
            ['title' => 'Overdue Items', 'url' => route('user.borrowing.overdue')]
        ];
        
        return view('user.borrowing.index', compact('borrowings', 'breadcrumbs'))->with('activeTab', 'overdue');
    }

    /**
     * Cancel a borrowing request.
     */
    public function cancel(Borrowing $borrowing)
    {
        $user = auth()->user();
        
        // Ensure user can only cancel their own borrowings
        if ($borrowing->borrower_id_number !== $user->id_number) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }
        
        // Only allow cancellation of active or overdue borrowings
        if (!in_array($borrowing->status, ['active', 'overdue'])) {
            return response()->json(['success' => false, 'message' => 'Cannot cancel this borrowing request.'], 400);
        }
        
        try {
            $borrowing->delete();
            return response()->json(['success' => true, 'message' => 'Borrowing request cancelled successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to cancel borrowing request.'], 500);
        }
    }
} 