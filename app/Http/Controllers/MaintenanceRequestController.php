<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\MaintenanceChecklist;
use App\Models\Location;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaintenanceRequestController extends Controller
{
    // User: request form
    public function create()
    {
        $locations = Location::orderBy('building')->orderBy('room')->get();
        return view('maintenance-requests.create', compact('locations'));
    }

    // User: submit request
    public function store(Request $request)
    {
        $request->validate([
            'school_year' => 'required|string|max:20',
            'department' => 'required|string|max:100',
            'date_reported' => 'required|date',
            'program' => 'nullable|string|max:100',
            'location_id' => 'required|exists:locations,id',
            'instructor_name' => 'required|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        $location = Location::findOrFail($request->location_id);
        $maintenanceRequest = MaintenanceRequest::create([
            'requester_id' => Auth::id(),
            'location_id' => $location->id,
            'school_year' => $request->school_year,
            'department' => $request->department,
            'date_reported' => $request->date_reported,
            'program' => $request->program,
            'instructor_name' => $request->instructor_name,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        // Send notification to admins
        $notificationService = new NotificationService();
        $notificationService->notifyMaintenanceRequest($maintenanceRequest);
        
        // Send notification to user
        $notificationService->notifyUserMaintenanceRequestCreated($maintenanceRequest);

        return redirect()->route('maintenance-requests.user-index')->with('success', 'Maintenance request submitted successfully. Awaiting admin approval.');
    }

    // User: list their requests
    public function userIndex()
    {
        $requests = MaintenanceRequest::where('requester_id', Auth::id())
            ->with(['location', 'approvedBy', 'rejectedBy', 'acknowledgedBy', 'checklist'])
            ->orderByDesc('created_at')
            ->paginate(20);
        return view('maintenance-requests.user-index', compact('requests'));
    }

    // User: show their request details
    public function userShow(MaintenanceRequest $maintenanceRequest)
    {
        // Ensure user can only view their own requests
        if ($maintenanceRequest->requester_id !== Auth::id()) {
            abort(403, 'You can only view your own maintenance requests.');
        }
        
        $maintenanceRequest->load(['location', 'approvedBy', 'rejectedBy', 'acknowledgedBy', 'checklist']);
        return view('maintenance-requests.user-show', compact('maintenanceRequest'));
    }

    // Admin: list requests
    public function index()
    {
        $this->authorizeAdmin();
        $requests = MaintenanceRequest::with(['requester','location'])->orderByDesc('created_at')->paginate(20);
        return view('maintenance-requests.admin-index', compact('requests'));
    }

    // Admin: show request details
    public function show(MaintenanceRequest $maintenanceRequest)
    {
        $this->authorizeAdmin();
        $maintenanceRequest->load(['requester', 'location', 'approvedBy', 'rejectedBy', 'acknowledgedBy', 'checklist']);
        return view('maintenance-requests.show', compact('maintenanceRequest'));
    }

    // Admin: approve
    public function approve(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $this->authorizeAdmin();
        if ($maintenanceRequest->status !== 'pending') {
            return back()->with('error', 'Request is not pending.');
        }

        try {
            DB::beginTransaction();

            // Update request status
            $maintenanceRequest->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'admin_notes' => $request->input('admin_notes'),
            ]);

            // Create maintenance checklist automatically
            $checklist = \App\Models\MaintenanceChecklist::create([
                'school_year' => $maintenanceRequest->school_year,
                'department' => $maintenanceRequest->department,
                'date_reported' => $maintenanceRequest->date_reported,
                'program' => $maintenanceRequest->program,
                'location_id' => $maintenanceRequest->location_id,
                'room_number' => $maintenanceRequest->location->room,
                'instructor' => $maintenanceRequest->instructor_name,
                'instructor_signature' => null, // Will be filled by GSU
                'checked_by' => 'To be filled by GSU',
                'checked_by_signature' => null,
                'date_checked' => null,
                'gsu_staff' => 'To be filled by GSU',
                'gsu_staff_signature' => null,
                'notes' => $maintenanceRequest->notes,
                'status' => 'created'
            ]);

            // Link the request to the created checklist
            $maintenanceRequest->update([
                'maintenance_checklist_id' => $checklist->id
            ]);

            // Auto-populate with existing assets in the location
            $assets = \App\Models\Asset::where('location_id', $maintenanceRequest->location_id)
                ->whereNotIn('status', ['Disposed', 'Lost'])
                ->with('category')
                ->get();
            
            foreach ($assets as $asset) {
                \App\Models\MaintenanceChecklistItem::create([
                    'maintenance_checklist_id' => $checklist->id,
                    'asset_code' => $asset->asset_code,
                    'particulars' => $asset->name,
                    'quantity' => 1,
                    'start_status' => 'OK',
                    'end_status' => null,
                    'notes' => null,
                    'location_id' => $maintenanceRequest->location_id,
                    'location_name' => "{$maintenanceRequest->location->building} - Floor {$maintenanceRequest->location->floor} - Room {$maintenanceRequest->location->room}"
                ]);
            }

            DB::commit();

            // Send notification to GSU
            $notificationService = new NotificationService();
            $notificationService->notifyMaintenanceRequestApproved($maintenanceRequest);
            
            // Send notification to user
            $notificationService->notifyUserMaintenanceRequestApproved($maintenanceRequest);

            return back()->with('success', "Request approved and maintenance checklist created with {$assets->count()} assets. GSU can now proceed with the maintenance.");

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to approve maintenance request and create checklist: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve request. Please try again.');
        }
    }

    // Admin: reject
    public function reject(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $this->authorizeAdmin();
        $request->validate(['rejection_reason' => 'required|string|max:1000']);
        if ($maintenanceRequest->status !== 'pending') {
            return back()->with('error', 'Request is not pending.');
        }
        $maintenanceRequest->update([
            'status' => 'rejected',
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);
        
        // Send notification to user
        $notificationService = new NotificationService();
        $notificationService->notifyUserMaintenanceRequestRejected($maintenanceRequest);
        
        return back()->with('success', 'Request rejected.');
    }

    // GSU: acknowledge and start checklist
    public function acknowledge(MaintenanceRequest $maintenanceRequest)
    {
        $this->authorizeGSU();
        if ($maintenanceRequest->status !== 'approved') {
            return back()->with('error', 'Request is not approved.');
        }
        
        if (!$maintenanceRequest->maintenance_checklist_id) {
            return back()->with('error', 'No maintenance checklist found for this request.');
        }

        $maintenanceRequest->update([
            'status' => 'acknowledged',
            'acknowledged_by' => Auth::id(),
            'acknowledged_at' => now(),
        ]);

        // Redirect to the created checklist
        return redirect()->route('maintenance-checklists.show', $maintenanceRequest->maintenance_checklist_id)
            ->with('success', 'Request acknowledged. You can now proceed with the maintenance checklist.');
    }

    private function authorizeAdmin(): void
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            abort(403);
        }
    }

    private function authorizeGSU(): void
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['gsu','admin'])) {
            abort(403);
        }
    }
}


