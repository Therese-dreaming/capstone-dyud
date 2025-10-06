<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RepairRequest;
use App\Models\Asset;
use App\Models\Semester;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RepairRequestController extends Controller
{
    private function authorizeAdmin()
    {
        if (!in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            abort(403, 'Only admins can access this resource.');
        }
    }

    private function authorizeGSU()
    {
        if (auth()->user()->role !== 'gsu') {
            abort(403, 'Only GSU staff can access this resource.');
        }
    }

    // User: repair request form
    public function create(Request $request)
    {
        $user = Auth::user();
        
        // Get asset if asset_code is provided
        $asset = null;
        if ($request->has('asset_code')) {
            $asset = Asset::where('asset_code', $request->asset_code)->first();
            
            // Verify user owns the location of this asset
            if ($asset && !$user->ownsLocation($asset->location_id)) {
                abort(403, 'You can only request repairs for assets in locations you manage.');
            }
        }
        
        // Get current semester
        $currentSemester = Semester::where('is_current', true)->first();
        
        // Get user's owned locations for the dropdown
        $ownedLocations = $user->ownedLocations;
        
        return view('repair-requests.create', compact('asset', 'currentSemester', 'ownedLocations'));
    }

    // User: submit repair request
    public function store(Request $request)
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
            'urgency_level' => 'required|in:low,medium,high,critical',
        ]);

        $user = Auth::user();
        $asset = Asset::where('asset_code', $validated['asset_code'])->first();

        // Verify user owns the location of this asset
        if (!$user->ownsLocation($asset->location_id)) {
            return back()->withErrors(['asset_code' => 'You can only request repairs for assets in locations you manage.'])->withInput();
        }

        // Prevent repair requests for disposed assets
        if ($asset->status === 'Disposed') {
            return back()->withErrors(['asset_code' => 'Cannot request repairs for disposed assets. This asset has been retired from service.'])->withInput();
        }

        // Create repair request
        $repairRequest = RepairRequest::create([
            'requester_id' => Auth::id(),
            'asset_id' => $asset->id,
            'semester_id' => $validated['semester_id'],
            'school_year' => $validated['school_year'],
            'department' => $validated['department'],
            'date_reported' => $validated['date_reported'],
            'program' => $validated['program'],
            'instructor_name' => $validated['instructor_name'],
            'issue_description' => $validated['issue_description'],
            'urgency_level' => $validated['urgency_level'],
            'status' => 'pending',
        ]);

        // Update asset status to "For Repair"
        $previousStatus = $asset->status;
        $asset->update(['status' => 'For Repair']);
        
        // Log the asset status change for audit trail
        \Log::info('Asset status updated to For Repair on repair request creation', [
            'repair_request_id' => $repairRequest->id,
            'asset_code' => $asset->asset_code,
            'asset_id' => $asset->id,
            'previous_status' => $previousStatus,
            'new_status' => 'For Repair',
            'requested_by' => Auth::id(),
            'created_at' => now(),
        ]);

        // Send notification to admins
        $notificationService = new NotificationService();
        $notificationService->notifyRepairRequest($repairRequest);

        return redirect()->route('repair-requests.index')->with('success', 'Repair request submitted successfully. The asset has been marked for repair.');
    }

    // User: list their repair requests
    public function index()
    {
        $requests = RepairRequest::where('requester_id', Auth::id())
            ->with(['asset.location', 'asset.category', 'approvedBy', 'rejectedBy', 'completedBy'])
            ->orderByDesc('created_at')
            ->paginate(20);
        
        return view('repair-requests.index', compact('requests'));
    }

    // User: show their repair request details
    public function show(RepairRequest $repairRequest)
    {
        // Ensure user can only view their own requests
        if ($repairRequest->requester_id !== Auth::id()) {
            abort(403, 'You can only view your own repair requests.');
        }
        
        $repairRequest->load(['asset.location', 'asset.category', 'approvedBy', 'rejectedBy', 'completedBy']);
        return view('repair-requests.show', compact('repairRequest'));
    }

    // Admin: list all repair requests
    public function adminIndex()
    {
        $this->authorizeAdmin();
        
        $repairRequests = RepairRequest::with(['requester', 'asset.location', 'asset.category'])
            ->orderByDesc('created_at')
            ->paginate(20);
        
        return view('admin.repair-requests.index', compact('repairRequests'));
    }

    // Admin: show repair request details
    public function adminShow(RepairRequest $repairRequest)
    {
        $this->authorizeAdmin();
        
        $repairRequest->load(['requester', 'asset.location', 'asset.category', 'approvedBy', 'rejectedBy', 'completedBy']);
        return view('admin.repair-requests.show', compact('repairRequest'));
    }

    // Admin: approve repair request (sets to in_progress)
    public function approve(RepairRequest $repairRequest)
    {
        $this->authorizeAdmin();
        
        if ($repairRequest->status !== 'pending') {
            return back()->with('error', 'Request is not pending.');
        }

        $repairRequest->update([
            'status' => 'in_progress',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Send notification to GSU
        $notificationService = new NotificationService();
        $notificationService->notifyRepairRequestApproved($repairRequest);
        
        // Send notification to user
        $notificationService->notifyUserRepairRequestApproved($repairRequest);

        return back()->with('success', 'Repair request approved and set to in progress for GSU.');
    }

    // Admin: reject repair request
    public function reject(Request $request, RepairRequest $repairRequest)
    {
        $this->authorizeAdmin();
        
        $request->validate(['rejection_reason' => 'required|string|max:1000']);
        
        if ($repairRequest->status !== 'pending') {
            return back()->with('error', 'Request is not pending.');
        }

        $repairRequest->update([
            'status' => 'rejected',
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Keep asset status as "For Repair" since the issue still exists
        // The asset should only return to "Available" when GSU completes a repair
        $asset = $repairRequest->asset;
        $previousStatus = $asset->status;
        
        // Log the rejection but keep asset status as "For Repair"
        \Log::info('Repair request rejected - asset remains "For Repair" until issue is fixed', [
            'repair_request_id' => $repairRequest->id,
            'asset_code' => $asset->asset_code,
            'asset_id' => $asset->id,
            'current_status' => $asset->status,
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Send notification to user
        $notificationService = new NotificationService();
        $notificationService->notifyUserRepairRequestRejected($repairRequest);

        return back()->with('success', 'Repair request rejected. Asset remains "For Repair" until issue is resolved.');
    }

    // GSU: list repair requests assigned to them
    public function gsuIndex()
    {
        $this->authorizeGSU();
        
        $repairRequests = RepairRequest::where('status', 'in_progress')
            ->with(['requester', 'asset.location', 'asset.category'])
            ->orderBy('urgency_level', 'desc')
            ->orderBy('created_at', 'asc')
            ->paginate(20);
        
        return view('gsu.repair-requests.index', compact('repairRequests'));
    }

    // GSU: show repair request details
    public function gsuShow(RepairRequest $repairRequest)
    {
        $this->authorizeGSU();
        
        if (!in_array($repairRequest->status, ['in_progress', 'completed'])) {
            abort(404, 'Repair request not found or not accessible.');
        }
        
        $repairRequest->load(['requester', 'asset.location', 'asset.category', 'approvedBy', 'completedBy']);
        return view('gsu.repair-requests.show', compact('repairRequest'));
    }

    // Note: GSU acknowledge method removed - repairs go directly from admin approval to in_progress

    // GSU: complete repair request
    public function gsuComplete(Request $request, RepairRequest $repairRequest)
    {
        $this->authorizeGSU();
        
        $validated = $request->validate([
            'completion_notes' => 'required|string|max:1000',
        ]);
        
        if ($repairRequest->status !== 'in_progress') {
            return back()->with('error', 'Only in-progress repair requests can be completed.');
        }

        $repairRequest->update([
            'status' => 'completed',
            'completed_by' => Auth::id(),
            'completed_at' => now(),
            'completion_notes' => $validated['completion_notes'],
        ]);

        // Update asset status back to Available
        $asset = $repairRequest->asset;
        $previousStatus = $asset->status;
        
        if ($asset->status === 'For Repair') {
            $asset->update(['status' => 'Available']);
            
            // Log the asset status change for audit trail
            \Log::info('Asset status updated after repair completion', [
                'repair_request_id' => $repairRequest->id,
                'asset_code' => $asset->asset_code,
                'asset_id' => $asset->id,
                'previous_status' => $previousStatus,
                'new_status' => 'Available',
                'completed_by' => Auth::id(),
                'completed_at' => now(),
            ]);
        }

        // Send notification to user
        $notificationService = new NotificationService();
        $notificationService->notifyUserRepairCompleted($repairRequest);

        return redirect()->route('gsu.repair-requests.index')
            ->with('success', 'Repair request completed successfully. Asset status updated to Available.');
    }
}
