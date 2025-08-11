<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserBorrowingController extends Controller
{
    /**
     * Display a listing of the user's borrowing requests.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Borrowing::with(['asset.category', 'asset.location', 'approvedBy', 'location'])
            ->where('user_id', $user->id);
        
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
                });
            });
        }
        
        $borrowings = $query->latest()->paginate(10);
        
        return view('user.borrowings.index', compact('borrowings'));
    }

    /**
     * Show the form for creating a new borrowing request.
     */
    public function create(Request $request)
    {
        $categories = Category::all();
        $locations = Location::all();
        
        // Build query for available assets with pagination
        // Exclude assets that have pending or approved borrowing requests
        $query = Asset::where('status', 'Available')
            ->whereDoesntHave('borrowings', function($borrowingQuery) {
                $borrowingQuery->whereIn('status', [Borrowing::STATUS_PENDING, Borrowing::STATUS_APPROVED]);
            })
            ->with(['category', 'location']);
        
        // Apply category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_code', 'like', "%{$search}%")
                  ->orWhereHas('category', function($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $availableAssets = $query->paginate(12); // Show 12 assets per page
        
        return view('user.borrowings.create', compact('categories', 'locations', 'availableAssets'));
    }

    /**
     * Store a newly created borrowing request.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'location_id' => 'required_without:custom_building|exists:locations,id',
            'custom_building' => 'required_if:location_id,custom|nullable|string|max:255',
            'custom_floor' => 'required_if:location_id,custom|nullable|string|max:255',
            'custom_room' => 'required_if:location_id,custom|nullable|string|max:255',
            'purpose' => 'required|string|max:500',
            'request_date' => 'required|date|after_or_equal:today',
            'due_date' => 'required|date|after:request_date',
        ]);
        
        try {
            return DB::transaction(function () use ($validated, $user) {
                // Check if asset is still available
                $asset = Asset::findOrFail($validated['asset_id']);
                if ($asset->status !== 'Available') {
                    return redirect()->back()
                        ->with('error', 'This asset is no longer available for borrowing.')
                        ->withInput();
                }
                
                // Check if there are any existing pending or approved requests for this asset
                $existingRequest = Borrowing::where('asset_id', $validated['asset_id'])
                    ->whereIn('status', [Borrowing::STATUS_PENDING, Borrowing::STATUS_APPROVED])
                    ->first();
                
                if ($existingRequest) {
                    return redirect()->back()
                        ->with('error', 'This asset is already requested by another user.')
                        ->withInput();
                }
                
                // Check if user already has a pending request for this asset
                $userExistingRequest = Borrowing::where('user_id', $user->id)
                    ->where('asset_id', $validated['asset_id'])
                    ->whereIn('status', [Borrowing::STATUS_PENDING, Borrowing::STATUS_APPROVED])
                    ->first();
                
                if ($userExistingRequest) {
                    return redirect()->back()
                        ->with('error', 'You already have a request for this asset.')
                        ->withInput();
                }
                
                // Handle custom location
                $locationId = null;
                $customLocation = null;
                
                if ($validated['location_id'] === 'custom') {
                    // Create a custom location string
                    $customLocation = $validated['custom_building'] . ' - Floor ' . $validated['custom_floor'] . ' - Room ' . $validated['custom_room'];
                } else {
                    $locationId = $validated['location_id'];
                }
                
                // Create borrowing request
                $borrowing = Borrowing::create([
                    'user_id' => $user->id,
                    'asset_id' => $validated['asset_id'],
                    'location_id' => $locationId,
                    'custom_location' => $customLocation,
                    'borrower_name' => $user->name,
                    'borrower_id_number' => $user->id_number,
                    'purpose' => $validated['purpose'],
                    'request_date' => $validated['request_date'],
                    'due_date' => $validated['due_date'],
                    'status' => Borrowing::STATUS_PENDING,
                ]);
                
                return redirect()->route('user.borrowings.index')
                    ->with('success', 'Borrowing request submitted successfully! Please wait for admin approval.');
            });
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to submit borrowing request. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified borrowing request.
     */
    public function show(Borrowing $borrowing)
    {
        $user = auth()->user();
        
        // Ensure user can only view their own borrowings
        if ($borrowing->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }
        
        $borrowing->load(['asset.category', 'asset.location', 'approvedBy', 'location']);
        
        return view('user.borrowings.show', compact('borrowing'));
    }

    /**
     * Cancel a borrowing request.
     */
    public function cancel(Borrowing $borrowing)
    {
        $user = auth()->user();
        
        // Ensure user can only cancel their own borrowings
        if ($borrowing->user_id !== $user->id) {
            return redirect()->back()
                ->with('error', 'Unauthorized access.');
        }
        
        // Only allow cancellation of pending requests
        if ($borrowing->status !== Borrowing::STATUS_PENDING) {
            return redirect()->back()
                ->with('error', 'Only pending requests can be cancelled.');
        }
        
        try {
            $borrowing->delete();
            
            return redirect()->back()
                ->with('success', 'Borrowing request cancelled successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to cancel borrowing request: ' . $e->getMessage());
        }
    }

    /**
     * Get available assets by category.
     */
    public function getAvailableAssets(Request $request)
    {
        $categoryId = $request->input('category_id');
        
        $query = Asset::where('status', 'Available')
            ->whereDoesntHave('borrowings', function($borrowingQuery) {
                $borrowingQuery->whereIn('status', [Borrowing::STATUS_PENDING, Borrowing::STATUS_APPROVED]);
            })
            ->with(['category', 'location']);
        
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        
        $assets = $query->get();
        
        return response()->json(['assets' => $assets]);
    }

    /**
     * Store multiple borrowing requests (bulk borrowing).
     */
    public function storeBulk(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.asset_id' => 'required|exists:assets,id',
            'location_id' => 'required_without:custom_building|exists:locations,id',
            'custom_building' => 'required_if:location_id,custom|nullable|string|max:255',
            'custom_floor' => 'required_if:location_id,custom|nullable|string|max:255',
            'custom_room' => 'required_if:location_id,custom|nullable|string|max:255',
            'purpose' => 'required|string|max:500',
            'request_date' => 'required|date|after_or_equal:today',
            'due_date' => 'required|date|after:request_date',
        ]);
        
        try {
            return DB::transaction(function () use ($validated, $user) {
                $createdBorrowings = [];
                $errors = [];
                
                foreach ($validated['items'] as $item) {
                    // Check if asset is still available
                    $asset = Asset::findOrFail($item['asset_id']);
                    if ($asset->status !== 'Available') {
                        $errors[] = "Asset {$asset->name} ({$asset->asset_code}) is no longer available.";
                        continue;
                    }
                    
                    // Check if there are any existing pending or approved requests for this asset
                    $existingRequest = Borrowing::where('asset_id', $item['asset_id'])
                        ->whereIn('status', [Borrowing::STATUS_PENDING, Borrowing::STATUS_APPROVED])
                        ->first();
                    
                    if ($existingRequest) {
                        $errors[] = "Asset {$asset->name} ({$asset->asset_code}) is already requested by another user.";
                        continue;
                    }
                    
                    // Check if user already has a pending request for this asset
                    $userExistingRequest = Borrowing::where('user_id', $user->id)
                        ->where('asset_id', $item['asset_id'])
                        ->whereIn('status', [Borrowing::STATUS_PENDING, Borrowing::STATUS_APPROVED])
                        ->first();
                    
                    if ($userExistingRequest) {
                        $errors[] = "You already have a request for asset {$asset->name} ({$asset->asset_code}).";
                        continue;
                    }
                    
                    // Handle custom location
                    $locationId = null;
                    $customLocation = null;
                    
                    if ($validated['location_id'] === 'custom') {
                        // Create a custom location string
                        $customLocation = $validated['custom_building'] . ' - Floor ' . $validated['custom_floor'] . ' - Room ' . $validated['custom_room'];
                    } else {
                        $locationId = $validated['location_id'];
                    }
                    
                    // Create borrowing request
                    $borrowing = Borrowing::create([
                        'user_id' => $user->id,
                        'asset_id' => $item['asset_id'],
                        'location_id' => $locationId,
                        'custom_location' => $customLocation,
                        'borrower_name' => $user->name,
                        'borrower_id_number' => $user->id_number,
                        'purpose' => $validated['purpose'],
                        'request_date' => $validated['request_date'],
                        'due_date' => $validated['due_date'],
                        'status' => Borrowing::STATUS_PENDING,
                    ]);
                    
                    $createdBorrowings[] = $borrowing;
                }
                
                if (empty($createdBorrowings)) {
                    return redirect()->back()
                        ->with('error', 'No borrowing requests were created. Please check the selected assets and try again.')
                        ->withInput();
                }
                
                $successMessage = count($createdBorrowings) . ' borrowing request(s) submitted successfully!';
                if (!empty($errors)) {
                    $successMessage .= ' (' . count($errors) . ' items could not be processed)';
                }
                
                return redirect()->route('user.borrowings.index')
                    ->with('success', $successMessage);
            });
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to submit borrowing requests. Please try again.')
                ->withInput();
        }
    }
} 