<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceChecklist;
use App\Models\MaintenanceChecklistItem;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MaintenanceChecklistController extends Controller
{
    /**
     * Save base64 signature data as an image file
     */
    private function saveSignature($base64Data, $prefix = 'signature')
    {
        if (!$base64Data || !str_starts_with($base64Data, 'data:image')) {
            return null;
        }

        try {
            // Extract the image data from base64
            $imageData = explode(',', $base64Data);
            if (count($imageData) !== 2) {
                return null;
            }

            $imageData = base64_decode($imageData[1]);
            if ($imageData === false) {
                return null;
            }

            // Generate unique filename
            $filename = $prefix . '_' . Str::random(20) . '.png';
            $path = 'signatures/' . $filename;

            // Save to storage
            Storage::disk('public')->put($path, $imageData);

            return $path;
        } catch (\Exception $e) {
            Log::error('Failed to save signature: ' . $e->getMessage());
            return null;
        }
    }
    public function index()
    {
        // Check if user has permission to access maintenance checklists
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'gsu'])) {
            abort(403, 'Unauthorized access.');
        }
        
        $checklists = MaintenanceChecklist::with(['items', 'location'])
            ->orderBy('date_reported', 'desc')
            ->paginate(10);
        
        // Check if user is GSU and return appropriate view
        if ($user->role === 'gsu') {
            return view('maintenance-checklists.gsu-index', compact('checklists'));
        }
        
        return view('maintenance-checklists.index', compact('checklists'));
    }

    public function create(Request $request)
    {
        // Only admin users can create maintenance checklists
        $user = auth()->user();
        if ($user->role !== 'admin') {
            abort(403, 'Only administrators can create maintenance checklists.');
        }
        
        $locations = \App\Models\Location::orderBy('building')->orderBy('floor')->orderBy('room')->get();

        // Prefill support from Maintenance Request acknowledge
        $prefillLocationId = $request->query('prefill_location_id');
        $prefillInstructor = $request->query('prefill_instructor');
        $maintenanceRequestId = $request->query('maintenance_request_id');
        
        // Get locations that already have pending maintenance checklists
        $pendingLocationIds = MaintenanceChecklist::whereIn('status', ['created', 'acknowledged', 'in_progress'])
            ->pluck('location_id')
            ->toArray();
        
        // Get locations with assets that have 'For Repair' or 'For Maintenance' status (excluding recently resolved)
        $repairMaintenanceLocationIds = \App\Models\Asset::whereIn('status', ['For Repair', 'For Maintenance'])
            ->whereDoesntHave('repairResolutions', function($query) {
                $query->where('resolution_date', '>=', now()->subDays(30));
            })
            ->pluck('location_id')
            ->unique()
            ->toArray();
        
        return view('maintenance-checklists.create', compact('locations', 'pendingLocationIds', 'repairMaintenanceLocationIds', 'prefillLocationId', 'prefillInstructor', 'maintenanceRequestId'));
    }

    public function store(Request $request)
    {
        // Only admin users can create maintenance checklists
        $user = auth()->user();
        if ($user->role !== 'admin') {
            abort(403, 'Only administrators can create maintenance checklists.');
        }
        
        \Log::info('Entered MaintenanceChecklistController@store');
        $validated = $request->validate([
            'school_year' => 'required|string|max:20',
            'department' => 'required|string|max:100',
            'date_reported' => 'required|date',
            'program' => 'nullable|string|max:100',
            'location_id' => 'required|exists:locations,id',
            'instructor' => 'required|string|max:100',
            'instructor_signature' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);
        
        // Check for existing pending/created maintenance checklist for this location
        $existingChecklist = MaintenanceChecklist::where('location_id', $validated['location_id'])
            ->whereIn('status', ['created', 'acknowledged', 'in_progress'])
            ->first();
            
        if ($existingChecklist) {
            $location = \App\Models\Location::find($validated['location_id']);
            return redirect()->back()
                ->with('error', "A maintenance checklist for {$location->building} - Floor {$location->floor} - Room {$location->room} is already pending or in progress. Please complete or cancel the existing checklist before creating a new one.")
                ->withInput();
        }

        // Check for assets with 'For Repair' or 'For Maintenance' status in this location (excluding recently resolved)
        $repairMaintenanceAssets = \App\Models\Asset::where('location_id', $validated['location_id'])
            ->whereIn('status', ['For Repair', 'For Maintenance'])
            ->whereDoesntHave('repairResolutions', function($query) {
                $query->where('resolution_date', '>=', now()->subDays(30));
            })
            ->get();
            
        if ($repairMaintenanceAssets->count() > 0) {
            $location = \App\Models\Location::find($validated['location_id']);
            $assetCodes = $repairMaintenanceAssets->pluck('asset_code')->join(', ');
            return redirect()->back()
                ->with('error', "Cannot create maintenance checklist for {$location->building} - Floor {$location->floor} - Room {$location->room}. The following assets are currently marked as 'For Repair' or 'For Maintenance': {$assetCodes}. Please resolve these assets first before creating a maintenance checklist.")
                ->withInput();
        }
        
        \Log::info('Validated data:', $validated);
        try {
            DB::beginTransaction();
            $location = \App\Models\Location::findOrFail($validated['location_id']);
            
            // Save instructor signature as file if provided
            $instructorSignaturePath = null;
            if (!empty($validated['instructor_signature'])) {
                $instructorSignaturePath = $this->saveSignature($validated['instructor_signature'], 'instructor');
            }
            
            // Create checklist with status 'created'
            $checklist = MaintenanceChecklist::create([
                'school_year' => $validated['school_year'],
                'department' => $validated['department'],
                'date_reported' => $validated['date_reported'],
                'program' => $validated['program'],
                'location_id' => $validated['location_id'],
                'room_number' => $location->room,
                'instructor' => $validated['instructor'],
                'instructor_signature' => $instructorSignaturePath,
                'checked_by' => 'To be filled by GSU', // Will be updated during maintenance
                'checked_by_signature' => null,
                'date_checked' => null, // Will be set when maintenance is completed
                'gsu_staff' => 'To be filled by GSU', // Will be updated during maintenance
                'gsu_staff_signature' => null,
                'notes' => $validated['notes'],
                'status' => 'created'
            ]);
            
            \Log::info('Created checklist ID: ' . $checklist->id);
            
            // Auto-populate with existing assets in the location (exclude disposed and missing assets)
            $assets = \App\Models\Asset::where('location_id', $location->id)
                ->whereNotIn('status', ['Disposed', 'Lost'])
                ->with('category')
                ->get();
            
            foreach ($assets as $asset) {
                MaintenanceChecklistItem::create([
                    'maintenance_checklist_id' => $checklist->id,
                    'asset_code' => $asset->asset_code,
                    'particulars' => $asset->name,
                    'quantity' => 1,
                    'start_status' => 'OK', // All assets start as OK
                    'end_status' => null, // Will be set during scanning
                    'notes' => null,
                    'location_id' => $location->id,
                    'location_name' => "{$location->building} - Floor {$location->floor} - Room {$location->room}"
                ]);
            }
            
            // Link to Maintenance Request if provided
            if ($request->filled('maintenance_request_id')) {
                try {
                    $mr = \App\Models\MaintenanceRequest::find($request->input('maintenance_request_id'));
                    if ($mr) {
                        $mr->update([
                            'maintenance_checklist_id' => $checklist->id,
                            'status' => 'in_progress',
                        ]);
                    }
                } catch (\Throwable $ignored) {}
            }

            DB::commit();
            \Log::info('Maintenance checklist created successfully with ' . $assets->count() . ' assets!');
            return redirect()->route('maintenance-checklists.index')
                ->with('success', 'Maintenance checklist created successfully with ' . $assets->count() . ' assets!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Maintenance checklist creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create maintenance checklist: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(MaintenanceChecklist $maintenanceChecklist)
    {
        $checklist = $maintenanceChecklist->load(['items.location', 'location']);
        
        // Check if user is GSU and return appropriate view
        if (auth()->user()->role === 'gsu') {
            return view('maintenance-checklists.gsu-show', compact('checklist'));
        }
        
        return view('maintenance-checklists.show', compact('checklist'));
    }

    public function userShow(MaintenanceChecklist $maintenanceChecklist)
    {
        // Check if this checklist is associated with a maintenance request from the current user
        $maintenanceRequest = $maintenanceChecklist->maintenanceRequest;
        if (!$maintenanceRequest || $maintenanceRequest->requester_id !== auth()->id()) {
            abort(403, 'You can only view maintenance checklists for your own requests.');
        }
        
        $checklist = $maintenanceChecklist->load(['items.asset', 'location', 'acknowledgedBy', 'completedBy']);
        return view('maintenance-checklists.user-show', compact('checklist'));
    }

    public function edit(MaintenanceChecklist $maintenanceChecklist)
    {
        // Only admin users can edit maintenance checklists
        $user = auth()->user();
        if ($user->role !== 'admin') {
            abort(403, 'Only administrators can edit maintenance checklists.');
        }
        
        $checklist = $maintenanceChecklist->load('items');
        $locations = \App\Models\Location::orderBy('building')->orderBy('floor')->orderBy('room')->get();
        
        return view('maintenance-checklists.edit', compact('checklist', 'locations'));
    }

    public function update(Request $request, MaintenanceChecklist $maintenanceChecklist)
    {
        // Only admin users can update maintenance checklists
        $user = auth()->user();
        if ($user->role !== 'admin') {
            abort(403, 'Only administrators can update maintenance checklists.');
        }
        
        $validated = $request->validate([
            'school_year' => 'required|string|max:20',
            'department' => 'required|string|max:100',
            'date_reported' => 'required|date',
            'program' => 'nullable|string|max:100',
            'location_id' => 'required|exists:locations,id',
            'instructor' => 'required|string|max:100',
            'instructor_signature' => 'nullable|string|max:10000',
            'checked_by' => 'required|string|max:100',
            'checked_by_signature' => 'nullable|string|max:10000',
            'date_checked' => 'required|date',
            'gsu_staff' => 'required|string|max:100',
            'gsu_staff_signature' => 'nullable|string|max:10000',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.asset_code' => 'nullable|string|max:50',
            'items.*.particulars' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:0',
            'items.*.start_status' => 'required|in:OK,FOR REPAIR,FOR REPLACEMENT',
            'items.*.end_status' => 'nullable|in:OK,FOR REPAIR,FOR REPLACEMENT',
            'items.*.notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Get the location to get the room number
            $location = \App\Models\Location::findOrFail($validated['location_id']);

            $maintenanceChecklist->update([
                'school_year' => $validated['school_year'],
                'department' => $validated['department'],
                'date_reported' => $validated['date_reported'],
                'program' => $validated['program'],
                'location_id' => $validated['location_id'],
                'room_number' => $location->room,
                'instructor' => $validated['instructor'],
                'instructor_signature' => $validated['instructor_signature'],
                'checked_by' => $validated['checked_by'],
                'checked_by_signature' => $validated['checked_by_signature'],
                'date_checked' => $validated['date_checked'],
                'gsu_staff' => $validated['gsu_staff'],
                'gsu_staff_signature' => $validated['gsu_staff_signature'],
                'notes' => $validated['notes']
            ]);

            // Delete existing items and recreate
            $maintenanceChecklist->items()->delete();

            foreach ($validated['items'] as $item) {
                MaintenanceChecklistItem::create([
                    'maintenance_checklist_id' => $maintenanceChecklist->id,
                    'asset_code' => $item['asset_code'] ?? null,
                    'particulars' => $item['particulars'],
                    'quantity' => $item['quantity'],
                    'start_status' => $item['start_status'],
                    'end_status' => $item['end_status'],
                    'notes' => $item['notes'] ?? null
                ]);
            }

            DB::commit();

            return redirect()->route('maintenance-checklists.index')
                ->with('success', 'Maintenance checklist updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Maintenance checklist update failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to update maintenance checklist: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(MaintenanceChecklist $maintenanceChecklist)
    {
        try {
            $maintenanceChecklist->delete();
            return redirect()->route('maintenance-checklists.index')
                ->with('success', 'Maintenance checklist deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Maintenance checklist deletion failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete maintenance checklist.');
        }
    }

    public function exportCsv(MaintenanceChecklist $maintenanceChecklist)
    {
        $checklist = $maintenanceChecklist->load('items');
        $xml = \App\Http\Controllers\Exports\MaintenanceChecklistExcel::buildExcelXml($checklist);
        $filename = "maintenance_checklist_{$checklist->room_number}_{$checklist->school_year}.xls";
        return response($xml, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    public function batchUpdate(Request $request, MaintenanceChecklist $maintenanceChecklist)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.end_status' => 'nullable|in:OK,FOR REPAIR,FOR REPLACEMENT',
            'items.*.notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            foreach ($validated['items'] as $index => $itemData) {
                $checklistItem = $maintenanceChecklist->items[$index] ?? null;
                if ($checklistItem) {
                    $checklistItem->update([
                        'end_status' => $itemData['end_status'] ?? null,
                        'notes' => $itemData['notes'] ?? null
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('maintenance-checklists.show', $maintenanceChecklist)
                ->with('success', 'Batch update completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Maintenance checklist batch update failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to update maintenance checklist: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function batchUpdateView(MaintenanceChecklist $maintenanceChecklist)
    {
        $checklist = $maintenanceChecklist->load('items');
        
        // Check if user is GSU and return appropriate view
        if (auth()->user()->role === 'gsu') {
            return view('maintenance-checklists.gsu-batch-update', compact('checklist'));
        }
        
        return view('maintenance-checklists.batch-update', compact('checklist'));
    }

    public function acknowledge(Request $request, MaintenanceChecklist $maintenanceChecklist)
    {
        if (!$maintenanceChecklist->canBeAcknowledged()) {
            return redirect()->back()->with('error', 'This checklist cannot be acknowledged at this time.');
        }

        try {
            $maintenanceChecklist->update([
                'status' => 'acknowledged',
                'acknowledged_at' => now(),
                'acknowledged_by' => auth()->id()
            ]);

            // Send notification to admins
            $notificationService = new NotificationService();
            $notificationService->notifyChecklistAcknowledged($maintenanceChecklist);
            
            // Send self-notification to GSU
            $notificationService->notifyGSUChecklistAcknowledged($maintenanceChecklist);

            return redirect()->route('maintenance-checklists.show', $maintenanceChecklist)
                ->with('success', 'Maintenance checklist acknowledged successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to acknowledge checklist: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to acknowledge checklist.');
        }
    }

    public function startMaintenance(Request $request, MaintenanceChecklist $maintenanceChecklist)
    {
        if (!$maintenanceChecklist->canBeStarted()) {
            return redirect()->back()->with('error', 'This checklist cannot be started at this time.');
        }

        try {
            DB::beginTransaction();
            
            $maintenanceChecklist->update([
                'status' => 'in_progress'
            ]);

            // Update related maintenance request status to 'in_progress'
            $maintenanceRequest = $maintenanceChecklist->maintenanceRequest;
            if ($maintenanceRequest) {
                $maintenanceRequest->update([
                    'status' => 'in_progress'
                ]);
            }

            // Send notification to admins
            $notificationService = new NotificationService();
            $notificationService->notifyChecklistStarted($maintenanceChecklist);
            
            // Send self-notification to GSU
            $notificationService->notifyGSUChecklistStarted($maintenanceChecklist);
            
            // Send notification to user if this is from a maintenance request
            if ($maintenanceRequest) {
                $notificationService->notifyUserMaintenanceStarted($maintenanceRequest);
            }

            DB::commit();

            return redirect()->route('maintenance-checklists.scanner', $maintenanceChecklist)
                ->with('success', 'Maintenance process started!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to start maintenance: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to start maintenance process.');
        }
    }

    public function scanner(MaintenanceChecklist $maintenanceChecklist)
    {
        $checklist = $maintenanceChecklist->load('items');
        
        // Check if user is GSU and return appropriate view
        if (auth()->user()->role === 'gsu') {
            return view('maintenance-checklists.gsu-scanner', compact('checklist'));
        }
        
        return view('maintenance-checklists.scanner', compact('checklist'));
    }

    public function submitMaintenance(Request $request, MaintenanceChecklist $maintenanceChecklist)
    {
        $request->validate([
            'missing_assets_acknowledged' => 'nullable|array',
            'missing_assets_acknowledged.*' => 'string',
            'checked_by' => 'required|string|max:100',
            'checked_by_signature' => 'nullable|string',
            'gsu_staff' => 'required|string|max:100',
            'gsu_staff_signature' => 'nullable|string'
        ]);

        if (!$maintenanceChecklist->canBeCompleted()) {
            return redirect()->back()->with('error', 'This checklist cannot be completed at this time.');
        }

        try {
            DB::beginTransaction();

            // Check for missing assets
            $missingAssets = $maintenanceChecklist->missing_assets;
            $hasMissingAssets = $missingAssets->count() > 0;

            if ($hasMissingAssets) {
                // Store acknowledged missing assets
                $acknowledgedMissing = $request->input('missing_assets_acknowledged', []);
                $maintenanceChecklist->update([
                    'has_missing_assets' => true,
                    'missing_assets_acknowledged' => $acknowledgedMissing
                ]);
            }

            // Save GSU signatures as files if provided
            $checkedBySignaturePath = null;
            $gsuStaffSignaturePath = null;
            
            if (!empty($request->input('checked_by_signature'))) {
                $checkedBySignaturePath = $this->saveSignature($request->input('checked_by_signature'), 'checked_by');
            }
            
            if (!empty($request->input('gsu_staff_signature'))) {
                $gsuStaffSignaturePath = $this->saveSignature($request->input('gsu_staff_signature'), 'gsu_staff');
            }

            // Complete the checklist with GSU staff information
            $maintenanceChecklist->update([
                'checked_by' => $request->input('checked_by'),
                'checked_by_signature' => $checkedBySignaturePath,
                'date_checked' => now(),
                'gsu_staff' => $request->input('gsu_staff'),
                'gsu_staff_signature' => $gsuStaffSignaturePath,
                'status' => 'completed',
                'completed_at' => now(),
                'completed_by' => auth()->id()
            ]);

            DB::commit();

            return redirect()->route('maintenance-checklists.show', $maintenanceChecklist)
                ->with('success', 'Maintenance checklist completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to complete checklist: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to complete checklist.');
        }
    }

    public function completeWithMissing(Request $request, MaintenanceChecklist $maintenanceChecklist)
    {
        $request->validate([
            'checked_by' => 'required|string|max:100',
            'gsu_staff' => 'required|string|max:100',
            'checked_by_signature' => 'required|string',
            'gsu_staff_signature' => 'required|string'
        ]);

        if ($maintenanceChecklist->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'This checklist cannot be completed at this time.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Get unscanned assets
            $unscannedAssets = $maintenanceChecklist->unscanned_assets;
            $markedAsLostCount = 0;

            // Mark unscanned assets as Unverified (pending admin confirmation)
            foreach ($unscannedAssets as $item) {
                // Update the checklist item
                $item->update([
                    'end_status' => 'UNVERIFIED',
                    'is_missing' => false, // Not missing, just unverified
                    'missing_reason' => 'Not scanned during maintenance - requires admin verification',
                    'date_checked' => now(),
                    'checked_by' => auth()->user()->name
                ]);

                // Create asset maintenance history record
                \App\Models\AssetMaintenanceHistory::create([
                    'asset_code' => $item->asset_code,
                    'maintenance_checklist_id' => $maintenanceChecklist->id,
                    'start_status' => $item->start_status,
                    'end_status' => 'UNVERIFIED',
                    'scanned_by' => auth()->user()->name,
                    'scanned_at' => now(),
                    'notes' => 'Asset not scanned during maintenance checklist - requires admin verification',
                    'location_id' => $item->location_id,
                    'location_name' => $item->location_name
                ]);

                // Resolve the related Asset by asset_code and update status to Unverified
                $asset = $item->asset; // belongsTo via asset_code
                if ($asset) {
                    // Update the asset status to 'Unverified'
                    $asset->update([
                        'status' => \App\Models\Asset::STATUS_UNVERIFIED
                    ]);
                } else {
                    // If no asset found for the asset_code, log warning but continue processing
                    \Log::warning('Skipping Asset status update: No Asset found for asset_code', [
                        'asset_code' => $item->asset_code,
                        'maintenance_checklist_id' => $maintenanceChecklist->id,
                    ]);
                }

                $markedAsLostCount++;
            }

            // Save signatures to storage
            $checkedBySignaturePath = $this->saveSignature($request->input('checked_by_signature'), 'checked_by');
            $gsuStaffSignaturePath = $this->saveSignature($request->input('gsu_staff_signature'), 'gsu_staff');

            // Complete the checklist
            $maintenanceChecklist->update([
                'checked_by' => $request->input('checked_by'),
                'checked_by_signature' => $checkedBySignaturePath,
                'date_checked' => now(),
                'gsu_staff' => $request->input('gsu_staff'),
                'gsu_staff_signature' => $gsuStaffSignaturePath,
                'status' => 'completed',
                'completed_at' => now(),
                'completed_by' => auth()->id(),
                'has_missing_assets' => $markedAsLostCount > 0
            ]);

            // Update related maintenance request status to 'completed'
            $maintenanceRequest = $maintenanceChecklist->maintenanceRequest;
            if ($maintenanceRequest) {
                $maintenanceRequest->update([
                    'status' => 'completed'
                ]);
            }

            // Send notification to admins
            $notificationService = new NotificationService();
            $notificationService->notifyChecklistCompleted($maintenanceChecklist);
            
            // Send self-notification to GSU
            $notificationService->notifyGSUChecklistCompleted($maintenanceChecklist);
            
            // Send notification to user if this is from a maintenance request
            if ($maintenanceRequest) {
                $notificationService->notifyUserMaintenanceCompleted($maintenanceRequest);
            }

            DB::commit();

            $message = $markedAsLostCount > 0 
                ? "Maintenance checklist completed successfully! {$markedAsLostCount} unscanned assets have been marked as 'Unverified' and require admin confirmation."
                : "Maintenance checklist completed successfully! All assets were processed.";

            return response()->json([
                'success' => true,
                'message' => $message,
                'marked_as_unverified_count' => $markedAsLostCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to complete checklist with missing assets: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete checklist.'
            ], 500);
        }
    }

    public function getCommonItems(Request $request)
    {
        \Log::info('getCommonItems called with location_id: ' . $request->get('location_id'));
        
        $locationId = $request->get('location_id');
        
        if (!$locationId) {
            \Log::warning('No location ID provided');
            return response()->json([]);
        }
        
        try {
            // Find the location by ID
            $location = \App\Models\Location::find($locationId);
            
            if (!$location) {
                \Log::warning('Location not found for ID: ' . $locationId);
                return response()->json([]);
            }
            
            \Log::info('Found location: ' . $location->id . ' for room: ' . $location->room);
            
            // Get all active assets in this location (exclude disposed and missing assets)
            $assets = \App\Models\Asset::where('location_id', $location->id)
                ->whereNotIn('status', ['Disposed', 'Lost'])
                ->with('category')
                ->get();
            
            \Log::info('Found ' . $assets->count() . ' assets in location ' . $location->id);
            
            $result = $assets->map(function($asset) {
                return [
                    'asset_code' => $asset->asset_code,
                    'name' => $asset->name,
                    'category' => $asset->category ? $asset->category->name : 'Unknown',
                    'quantity' => 1, // Each asset is counted as 1
                    'condition' => $asset->condition
                ];
            });
            
            \Log::info('Returning ' . $result->count() . ' assets');
            return response()->json($result);
            
        } catch (\Exception $e) {
            \Log::error('Error in getCommonItems: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Show unverified assets that need admin confirmation
     */
    public function unverifiedAssets()
    {
        $user = auth()->user();
        if ($user->role !== 'admin') {
            abort(403, 'Only administrators can view unverified assets.');
        }

        $unverifiedAssets = \App\Models\Asset::where('status', \App\Models\Asset::STATUS_UNVERIFIED)
            ->with(['category', 'location'])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('maintenance-checklists.unverified-assets', compact('unverifiedAssets'));
    }

    /**
     * Confirm unverified asset as lost
     */
    public function confirmAsLost(Request $request, \App\Models\Asset $asset)
    {
        $user = auth()->user();
        if ($user->role !== 'admin') {
            abort(403, 'Only administrators can confirm assets as lost.');
        }

        if ($asset->status !== \App\Models\Asset::STATUS_UNVERIFIED) {
            return redirect()->back()->with('error', 'This asset is not in unverified status.');
        }

        $request->validate([
            'investigation_notes' => 'required|string|max:1000',
            'actions_taken' => 'nullable|string|max:1000',
            'resolution_date' => 'required|date|before_or_equal:today'
        ]);

        try {
            DB::beginTransaction();

            // Note: No repair resolution record is created for lost confirmation

            // Update asset status to Lost
            $asset->update(['status' => \App\Models\Asset::STATUS_LOST]);

            // Determine last known location from asset's current location
            $lastKnownLocation = 'Unknown';
            if ($asset->location) {
                $lastKnownLocation = $asset->location->building . ' - Floor ' . $asset->location->floor . ', Room ' . $asset->location->room;
            }

            // Create lost asset record
            \App\Models\LostAsset::create([
                'asset_id' => $asset->id,
                'reported_by' => auth()->id(),
                'reported_date' => $request->resolution_date,
                'last_known_location' => $lastKnownLocation,
                'investigation_notes' => $request->investigation_notes,
                'status' => \App\Models\LostAsset::STATUS_LOST,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Update the original checklist item that ended as UNVERIFIED
            $lastUnverifiedHistory = \App\Models\AssetMaintenanceHistory::where('asset_code', $asset->asset_code)
                ->where('end_status', 'UNVERIFIED')
                ->latest('id')
                ->first();

            if ($lastUnverifiedHistory && $lastUnverifiedHistory->maintenance_checklist_id) {
                $checklist = \App\Models\MaintenanceChecklist::find($lastUnverifiedHistory->maintenance_checklist_id);
                if ($checklist) {
                    $item = $checklist->items()->where('asset_code', $asset->asset_code)->first();
                    if ($item) {
                        $item->update([
                            'end_status' => 'LOST',
                            'is_scanned' => false,
                            'is_missing' => true,
                            'scanned_at' => $request->resolution_date ? \Carbon\Carbon::parse($request->resolution_date) : now(),
                            'scanned_by' => auth()->user()->name,
                            'missing_reason' => 'Confirmed LOST via admin after checklist completion',
                            'notes' => trim(($item->notes ? $item->notes.' ' : '') . '(Resolved via admin: LOST)')
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->back()->with('success', "Asset {$asset->asset_code} has been confirmed as lost.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to confirm asset as lost: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to confirm asset as lost. Please try again.');
        }
    }

    /**
     * Mark unverified asset as found (back to Available)
     */
    public function markAsFound(Request $request, \App\Models\Asset $asset)
    {
        $user = auth()->user();
        if ($user->role !== 'admin') {
            abort(403, 'Only administrators can mark assets as found.');
        }

        if ($asset->status !== \App\Models\Asset::STATUS_UNVERIFIED) {
            return redirect()->back()->with('error', 'This asset is not in unverified status.');
        }

        $request->validate([
            'end_status' => 'required|in:OK,FOR REPAIR,FOR MAINTENANCE',
            'resolution_notes' => 'required|string|max:1000',
            'actions_taken' => 'nullable|string|max:1000',
            'resolution_date' => 'required|date|before_or_equal:today'
        ]);

        try {
            DB::beginTransaction();

            // Map end-of-SY status to asset status
            $newStatus = match ($request->end_status) {
                'OK' => \App\Models\Asset::STATUS_AVAILABLE,
                'FOR REPAIR' => \App\Models\Asset::STATUS_FOR_REPAIR,
                'FOR MAINTENANCE' => \App\Models\Asset::STATUS_FOR_MAINTENANCE,
            };

            // Always create a repair resolution record for audit trail
            $resolutionStatus = match ($request->end_status) {
                'OK' => \App\Models\AssetRepairResolution::RESOLUTION_RETURNED_TO_SERVICE,
                'FOR REPAIR' => 'For Repair',
                'FOR MAINTENANCE' => 'For Maintenance',
            };

            \App\Models\AssetRepairResolution::create([
                'asset_id' => $asset->id,
                'resolved_by' => auth()->id(),
                'previous_status' => $asset->status,
                'resolution_status' => $resolutionStatus,
                'resolution_notes' => $request->resolution_notes,
                'actions_taken' => $request->actions_taken,
                'resolution_date' => $request->resolution_date
            ]);

            // Update asset status accordingly
            // Note: The asset's location remains unchanged (it was preserved when marked as Unverified)
            $asset->update(['status' => $newStatus]);

            // Update the original checklist item that ended as UNVERIFIED
            $lastUnverifiedHistory = \App\Models\AssetMaintenanceHistory::where('asset_code', $asset->asset_code)
                ->where('end_status', 'UNVERIFIED')
                ->latest('id')
                ->first();

            if ($lastUnverifiedHistory && $lastUnverifiedHistory->maintenance_checklist_id) {
                $checklist = \App\Models\MaintenanceChecklist::find($lastUnverifiedHistory->maintenance_checklist_id);
                if ($checklist) {
                    $item = $checklist->items()->where('asset_code', $asset->asset_code)->first();
                    if ($item) {
                        $item->update([
                            'end_status' => $request->end_status,
                            'is_scanned' => true,
                            'is_missing' => false,
                            'scanned_at' => $request->resolution_date ? \Carbon\Carbon::parse($request->resolution_date) : now(),
                            'scanned_by' => auth()->user()->name,
                            'notes' => trim(($item->notes ? $item->notes.' ' : '') . '(Resolved via admin: FOUND)')
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->back()->with('success', "Asset {$asset->asset_code} has been marked as found and is now available at its original location.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to mark asset as found: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to mark asset as found. Please try again.');
        }
    }

    /**
     * Resolve repair/maintenance status for an asset
     */
    public function resolveRepair(Request $request, \App\Models\Asset $asset)
    {
        $user = auth()->user();
        if ($user->role !== 'admin') {
            abort(403, 'Only administrators can resolve repair status.');
        }

        if (!$asset->needsRepairResolution()) {
            return redirect()->back()->with('error', 'This asset does not need repair resolution.');
        }

        $request->validate([
            'resolution_status' => 'required|in:Repaired,Disposed,Replaced,Returned to Service',
            'resolution_notes' => 'nullable|string|max:1000',
            'actions_taken' => 'nullable|string|max:1000',
            'repair_cost' => 'nullable|numeric|min:0',
            'resolution_date' => 'required|date|before_or_equal:today',
            'vendor_name' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            // Create repair resolution record
            \App\Models\AssetRepairResolution::create([
                'asset_id' => $asset->id,
                'resolved_by' => auth()->id(),
                'previous_status' => $asset->status,
                'resolution_status' => $request->resolution_status,
                'resolution_notes' => $request->resolution_notes,
                'actions_taken' => $request->actions_taken,
                'repair_cost' => $request->repair_cost,
                'resolution_date' => $request->resolution_date,
                'vendor_name' => $request->vendor_name,
                'invoice_number' => $request->invoice_number
            ]);

            // Update asset status based on resolution
            $newStatus = match($request->resolution_status) {
                'Repaired', 'Returned to Service' => \App\Models\Asset::STATUS_AVAILABLE,
                'Disposed' => \App\Models\Asset::STATUS_DISPOSED,
                'Replaced' => \App\Models\Asset::STATUS_AVAILABLE, // New asset replaces old one
                default => \App\Models\Asset::STATUS_AVAILABLE
            };

            $asset->update(['status' => $newStatus]);

            // If disposed, create disposal record
            if ($request->resolution_status === 'Disposed') {
                \App\Models\Dispose::create([
                    'asset_id' => $asset->id,
                    'disposal_date' => $request->resolution_date,
                    'disposal_method' => 'Repair Resolution',
                    'disposal_reason' => $request->resolution_notes ?? 'Asset disposed due to repair resolution',
                    'disposed_by' => auth()->id()
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', "Asset {$asset->asset_code} repair status has been resolved successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to resolve repair status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to resolve repair status. Please try again.');
        }
    }
} 