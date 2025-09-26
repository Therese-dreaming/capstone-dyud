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
        $user = Auth::user();
        
        // Only regular users can create maintenance requests
        if ($user->role !== 'user') {
            abort(403, 'Only regular users can create maintenance requests.');
        }
        
        // Get only locations owned by this user
        $locations = $user->ownedLocations()->orderBy('building')->orderBy('room')->get();
        
        // If user has no owned locations, show message
        if ($locations->isEmpty()) {
            return view('maintenance-requests.create', [
                'locations' => $locations,
                'noLocations' => true
            ]);
        }
        
        return view('maintenance-requests.create', compact('locations'));
    }

    // User: submit request
    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_scope' => 'required|in:location,assets',
            'school_year' => 'required|string|max:20',
            'department' => 'required|string|max:100',
            'date_reported' => 'required|date',
            'program' => 'nullable|string|max:100',
            'location_id' => 'required_if:request_scope,location|nullable|exists:locations,id',
            'instructor_name' => 'required|string|max:100',
            'notes' => 'nullable|string|max:1000',
            'asset_codes' => 'required_if:request_scope,assets|array',
            'asset_codes.*' => 'required_if:request_scope,assets|distinct|exists:assets,asset_code',
            'idempotency_key' => 'nullable|string|max:100',
        ]);

        $user = Auth::user();
        
        // Only regular users can create maintenance requests
        if ($user->role !== 'user') {
            abort(403, 'Only regular users can create maintenance requests.');
        }
        
        $locationId = $validated['request_scope'] === 'location' ? $validated['location_id'] : null;
        $location = $locationId ? Location::findOrFail($locationId) : null;
        
        // Validate location ownership
        if ($locationId && !$user->ownsLocation($locationId)) {
            return back()->withErrors(['location_id' => 'You can only submit maintenance requests for locations you own.'])->withInput();
        }
        
        // For asset-specific requests, validate that user owns the locations of all requested assets
        if ($validated['request_scope'] === 'assets') {
            $assetCodes = $validated['asset_codes'];
            $assets = \App\Models\Asset::whereIn('asset_code', $assetCodes)->get();
            
            foreach ($assets as $asset) {
                if (!$user->ownsLocation($asset->location_id)) {
                    return back()->withErrors(['asset_codes' => 'You can only request maintenance for assets in locations you own.'])->withInput();
                }
            }
        }

        // Idempotency (avoid duplicate submissions)
        $idempotencyKey = $validated['idempotency_key'] ?? ($validated['request_scope'] . '|' . ($locationId ?? 'none') . '|' . Auth::id() . '|' . now()->format('YmdHis'));

        // If a request with same key exists and is still pending, return it
        if ($existing = MaintenanceRequest::where('idempotency_key', $idempotencyKey)->where('status', 'pending')->first()) {
            return redirect()->route('maintenance-requests.user-index')->with('success', 'Maintenance request submitted successfully. Awaiting admin approval.');
        }

        $maintenanceRequest = MaintenanceRequest::create([
            'requester_id' => Auth::id(),
            'location_id' => $locationId, // may be null for specific-assets requests
            'school_year' => $validated['school_year'],
            'department' => $validated['department'],
            'date_reported' => $validated['date_reported'],
            'program' => $validated['program'] ?? null,
            'instructor_name' => $validated['instructor_name'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
            'idempotency_key' => $idempotencyKey,
        ]);

        // Persist requested asset codes for admin/GSU approval processing
        if (($validated['request_scope'] ?? 'location') === 'assets') {
            $maintenanceRequest->update([
                'requested_asset_codes' => json_encode($validated['asset_codes']),
            ]);
        }

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

            // Detect scope: location vs specific assets (location_id can be null)
            $requestedAssetCodes = $maintenanceRequest->requested_asset_codes ? json_decode($maintenanceRequest->requested_asset_codes, true) : [];
            $isSpecificAssets = empty($maintenanceRequest->location_id) && !empty($requestedAssetCodes);

            // Create maintenance checklist automatically
            $checklist = \App\Models\MaintenanceChecklist::create([
                'school_year' => $maintenanceRequest->school_year,
                'department' => $maintenanceRequest->department,
                'date_reported' => $maintenanceRequest->date_reported,
                'program' => $maintenanceRequest->program,
                'location_id' => $isSpecificAssets ? null : $maintenanceRequest->location_id,
                'room_number' => $isSpecificAssets ? 'Asset-specific request' : (optional($maintenanceRequest->location)->room ?? 'N/A'),
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

            // Initialize assets collection
            $assets = collect();

            if ($isSpecificAssets) {
                // Populate with specifically requested assets (validate again for safety)
                $assets = \App\Models\Asset::whereIn('asset_code', $requestedAssetCodes)
                    ->whereNotIn('status', ['Disposed', 'Lost'])
                    ->with('location')
                    ->get();

                foreach ($assets as $asset) {
                    $loc = $asset->location;
                    \App\Models\MaintenanceChecklistItem::create([
                        'maintenance_checklist_id' => $checklist->id,
                        'asset_code' => $asset->asset_code,
                        'particulars' => $asset->name,
                        'quantity' => 1,
                        'start_status' => 'OK',
                        'end_status' => null,
                        'notes' => null,
                        'location_id' => optional($loc)->id,
                        'location_name' => $loc ? ($loc->building . ' - Floor ' . $loc->floor . ' - Room ' . $loc->room) : 'N/A'
                    ]);
                }
            } else {
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
                        'location_name' => optional($maintenanceRequest->location)->building
                            ? ($maintenanceRequest->location->building . ' - Floor ' . $maintenanceRequest->location->floor . ' - Room ' . $maintenanceRequest->location->room)
                            : 'N/A'
                    ]);
                }
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
            \Log::error('Failed to approve maintenance request and create checklist: ' . $e->getMessage(), [
                'maintenance_request_id' => $maintenanceRequest->id,
                'exception' => $e->getTraceAsString()
            ]);
            
            // Provide more specific error message in development
            if (config('app.debug')) {
                return back()->with('error', 'Failed to approve request: ' . $e->getMessage());
            }
            
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

    // API: Get pending maintenance requests count for admin sidebar
    public function pendingCount()
    {
        $this->authorizeAdmin();
        
        $count = MaintenanceRequest::where('status', 'pending')->count();
        
        return response()->json(['count' => $count]);
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


