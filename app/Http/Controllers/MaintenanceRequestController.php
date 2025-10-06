<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\MaintenanceChecklist;
use App\Models\Location;
use App\Models\Semester;
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
        
        // Get available semesters
        $semesters = Semester::active()->orderBy('academic_year', 'desc')->orderBy('start_date', 'desc')->get();
        
        // If user has no owned locations, show message
        if ($locations->isEmpty()) {
            return view('maintenance-requests.create', [
                'locations' => $locations,
                'semesters' => $semesters,
                'noLocations' => true
            ]);
        }
        
        return view('maintenance-requests.create', compact('locations', 'semesters'));
    }

    // User: submit request
    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_scope' => 'required|in:location,assets',
            'semester_id' => 'required|exists:semesters,id',
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
                
                // Prevent maintenance requests for disposed assets
                if ($asset->status === 'Disposed') {
                    return back()->withErrors(['asset_codes' => "Cannot request maintenance for asset {$asset->asset_code} - it is disposed and has been retired from service."])->withInput();
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
            'semester_id' => $validated['semester_id'],
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

            // Get semester information for automatic date population
            $semester = Semester::find($maintenanceRequest->semester_id);
            
            // Create maintenance checklist automatically
            $checklist = \App\Models\MaintenanceChecklist::create([
                'school_year' => $maintenanceRequest->school_year,
                'start_of_sy_date' => $semester ? $semester->start_date : null,
                'end_of_sy_date' => $semester ? $semester->end_date : null,
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
        if (!$user || $user->role !== 'gsu') {
            abort(403);
        }
    }

    // User: repair request form
    public function createRepair(Request $request)
    {
        $user = Auth::user();
        
        // Only regular users can create repair requests
        if ($user->role !== 'user') {
            abort(403, 'Only regular users can create repair requests.');
        }
        
        // Get asset code from query parameter
        $assetCode = $request->query('asset_code');
        $asset = null;
        
        if ($assetCode) {
            $asset = \App\Models\Asset::where('asset_code', $assetCode)->first();
        }
        
        // Get only locations owned by this user
        $locations = $user->ownedLocations()->orderBy('building')->orderBy('room')->get();
        
        // Get available semesters
        $semesters = Semester::active()->orderBy('academic_year', 'desc')->orderBy('start_date', 'desc')->get();
        
        return view('repair-requests.create', compact('locations', 'semesters', 'asset', 'assetCode'));
    }

    // User: submit repair request
    public function storeRepair(Request $request)
    {
        $validated = $request->validate([
            'asset_code' => 'required|exists:assets,asset_code',
            'semester_id' => 'required|exists:semesters,id',
            'school_year' => 'required|string|max:20',
            'department' => 'required|string|max:100',
            'date_reported' => 'required|date',
            'program' => 'nullable|string|max:100',
            'instructor_name' => 'required|string|max:100',
            'issue_description' => 'required|string|max:1000',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $user = Auth::user();
        
        // Only regular users can create repair requests
        if ($user->role !== 'user') {
            abort(403, 'Only regular users can create repair requests.');
        }
        
        // Get the asset
        $asset = \App\Models\Asset::where('asset_code', $validated['asset_code'])->firstOrFail();
        
        // Validate that user owns the location of the asset
        if ($asset->location_id && !$user->ownsLocation($asset->location_id)) {
            return back()->withErrors(['asset_code' => 'You can only submit repair requests for assets in locations you own.'])->withInput();
        }
        
        try {
            DB::beginTransaction();
            
            // Create maintenance request with repair type
            $maintenanceRequest = MaintenanceRequest::create([
                'request_scope' => 'assets',
                'semester_id' => $validated['semester_id'],
                'school_year' => $validated['school_year'],
                'department' => $validated['department'],
                'date_reported' => $validated['date_reported'],
                'program' => $validated['program'],
                'location_id' => $asset->location_id,
                'instructor_name' => $validated['instructor_name'],
                'notes' => "REPAIR REQUEST\nPriority: " . strtoupper($validated['priority']) . "\n\nIssue: " . $validated['issue_description'],
                'requester_id' => $user->id,
                'requested_asset_codes' => json_encode([$validated['asset_code']]),
                'status' => 'pending',
            ]);
            
            // Notify admins and user
            $notificationService = app(NotificationService::class);
            $notificationService->notifyMaintenanceRequest($maintenanceRequest);
            $notificationService->notifyUserMaintenanceRequestCreated($maintenanceRequest);
            
            DB::commit();
            
            return redirect()->route('user-assets.show', $asset)
                ->with('success', 'Repair request submitted successfully! Admins will review your request.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Repair request creation failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to submit repair request. Please try again.'])->withInput();
        }
    }

    // GSU: View all repair requests
    public function gsuIndex()
    {
        $this->authorizeGSU();
        
        // Get all maintenance requests that contain "REPAIR REQUEST" in notes
        $repairRequests = MaintenanceRequest::where('notes', 'like', '%REPAIR REQUEST%')
            ->with(['requester', 'location', 'semester'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('gsu.repair-requests.index', compact('repairRequests'));
    }

    // GSU: View repair request details
    public function gsuShow(MaintenanceRequest $maintenanceRequest)
    {
        $this->authorizeGSU();
        
        // Verify this is a repair request
        if (strpos($maintenanceRequest->notes, 'REPAIR REQUEST') === false) {
            abort(404, 'Not a repair request');
        }
        
        $maintenanceRequest->load(['requester', 'location', 'semester']);
        
        // Get the requested assets
        $requestedAssets = $maintenanceRequest->getRequestedAssets();
        
        return view('gsu.repair-requests.show', compact('maintenanceRequest', 'requestedAssets'));
    }

    // GSU: Complete repair request
    public function gsuComplete(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $this->authorizeGSU();
        
        $validated = $request->validate([
            'completion_notes' => 'required|string|max:1000',
        ]);
        
        if ($maintenanceRequest->status !== 'in_progress') {
            return back()->with('error', 'Only in-progress repair requests can be completed.');
        }
        
        $maintenanceRequest->update([
            'status' => 'completed',
            'notes' => $maintenanceRequest->notes . "\n\nCOMPLETION NOTES:\n" . $validated['completion_notes'],
        ]);
        
        // Update asset status back to Available for completed repairs
        if ($maintenanceRequest->maintenance_checklist_id) {
            $checklist = $maintenanceRequest->checklist;
            if ($checklist) {
                // Get all assets from the maintenance checklist that were marked for repair
                $assetCodes = $checklist->items()
                    ->where('end_status', 'FOR REPAIR')
                    ->pluck('asset_code')
                    ->toArray();
                
                if (!empty($assetCodes)) {
                    // Update asset status back to Available
                    \App\Models\Asset::whereIn('asset_code', $assetCodes)
                        ->where('status', 'For Repair')
                        ->update(['status' => 'Available']);
                        
                    \Log::info('Updated asset status to Available after repair completion', [
                        'maintenance_request_id' => $maintenanceRequest->id,
                        'asset_codes' => $assetCodes
                    ]);
                }
            }
        }
        
        // Notify user
        $notificationService = app(NotificationService::class);
        $notificationService->notifyUserMaintenanceCompleted($maintenanceRequest);
        
        return redirect()->route('gsu.repair-requests.index')
            ->with('success', 'Repair request marked as completed.');
    }

    // Admin: View all repair requests
    public function adminRepairIndex()
    {
        $this->authorizeAdmin();
        
        // Get all maintenance requests that contain "REPAIR REQUEST" in notes
        $repairRequests = MaintenanceRequest::where('notes', 'like', '%REPAIR REQUEST%')
            ->with(['requester', 'location', 'semester'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.repair-requests.index', compact('repairRequests'));
    }

    // Admin: View repair request details
    public function adminRepairShow(MaintenanceRequest $maintenanceRequest)
    {
        $this->authorizeAdmin();
        
        // Verify this is a repair request
        if (strpos($maintenanceRequest->notes, 'REPAIR REQUEST') === false) {
            abort(404, 'Not a repair request');
        }
        
        $maintenanceRequest->load(['requester', 'location', 'semester']);
        
        // Get the requested assets
        $requestedAssets = $maintenanceRequest->getRequestedAssets();
        
        return view('admin.repair-requests.show', compact('maintenanceRequest', 'requestedAssets'));
    }

    // Admin: Approve repair request (sets to in_progress instead of approved)
    public function adminRepairApprove(MaintenanceRequest $maintenanceRequest)
    {
        $this->authorizeAdmin();
        
        if ($maintenanceRequest->status !== 'pending') {
            return back()->with('error', 'Only pending repair requests can be approved.');
        }
        
        $maintenanceRequest->update([
            'status' => 'in_progress',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        
        // Notify user and GSU
        $notificationService = app(NotificationService::class);
        $notificationService->notifyUserMaintenanceRequestApproved($maintenanceRequest);
        $notificationService->notifyMaintenanceRequestApproved($maintenanceRequest);
        
        return back()->with('success', 'Repair request approved and marked as In Progress. GSU can now work on it.');
    }
}
