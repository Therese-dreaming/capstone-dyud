<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Location;
use App\Models\UserLocation;
use App\Services\NotificationService;

class UserLocationController extends Controller
{
    /**
     * Display user-location assignments
     */
    public function index()
    {
        $this->authorizeAdmin();
        
        $assignments = UserLocation::with(['user', 'location', 'assignedBy'])
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(15);
        
        $users = User::where('role', 'user')->orderBy('name')->get();
        $locations = Location::orderBy('building')->orderBy('room')->get();
        
        return view('admin.user-locations.index', compact('assignments', 'users', 'locations'));
    }

    /**
     * Store a new user-location assignment
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'location_id' => 'required|exists:locations,id',
            'notes' => 'nullable|string|max:500'
        ]);

        // Check if user is a regular user
        $user = User::findOrFail($validated['user_id']);
        if ($user->role !== 'user') {
            return back()->withErrors(['user_id' => 'Only regular users can be assigned to locations.']);
        }

        // Check if assignment already exists
        if (UserLocation::where('user_id', $validated['user_id'])
                        ->where('location_id', $validated['location_id'])
                        ->exists()) {
            return back()->withErrors(['location_id' => 'This user is already assigned to this location.']);
        }

        $userLocation = UserLocation::create([
            'user_id' => $validated['user_id'],
            'location_id' => $validated['location_id'],
            'assigned_by' => Auth::id(),
            'assigned_at' => now(),
            'notes' => $validated['notes']
        ]);

        // Load relationships for notification
        $userLocation->load(['user', 'location', 'assignedBy']);

        // Send notification to the assigned user
        $notificationService = app(NotificationService::class);
        $notificationService->notifyUserLocationAssigned($userLocation);

        return back()->with('success', 'User successfully assigned to location.');
    }

    /**
     * Remove a user-location assignment
     */
    public function destroy(UserLocation $userLocation)
    {
        $this->authorizeAdmin();
        
        $userLocation->delete();
        
        return back()->with('success', 'User-location assignment removed successfully.');
    }

    /**
     * Get locations for a specific user (AJAX)
     */
    public function getUserLocations(User $user)
    {
        $this->authorizeAdmin();
        
        $locations = $user->ownedLocations()->with('assets')->get();
        
        return response()->json([
            'locations' => $locations->map(function ($location) {
                return [
                    'id' => $location->id,
                    'building' => $location->building,
                    'floor' => $location->floor,
                    'room' => $location->room,
                    'assets_count' => $location->assets->count(),
                    'full_name' => $location->building . ' - Floor ' . $location->floor . ' - Room ' . $location->room
                ];
            })
        ]);
    }

    private function authorizeAdmin(): void
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'superadmin'])) {
            abort(403);
        }
    }
}
