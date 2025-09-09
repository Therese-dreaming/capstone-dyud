<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceChecklist;
use App\Models\MaintenanceChecklistItem;
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
        
        $checklists = MaintenanceChecklist::with('items')
            ->orderBy('date_reported', 'desc')
            ->paginate(10);
        
        // Check if user is GSU and return appropriate view
        if ($user->role === 'gsu') {
            return view('maintenance-checklists.gsu-index', compact('checklists'));
        }
        
        return view('maintenance-checklists.index', compact('checklists'));
    }

    public function create()
    {
        // Only admin users can create maintenance checklists
        $user = auth()->user();
        if ($user->role !== 'admin') {
            abort(403, 'Only administrators can create maintenance checklists.');
        }
        
        $locations = \App\Models\Location::orderBy('building')->orderBy('floor')->orderBy('room')->get();
        
        return view('maintenance-checklists.create', compact('locations'));
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
            
            // Auto-populate with existing assets in the location
            $assets = \App\Models\Asset::where('location_id', $location->id)
                ->where('status', '!=', 'Disposed')
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
                    'notes' => null
                ]);
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
        $checklist = $maintenanceChecklist->load('items');
        
        // Check if user is GSU and return appropriate view
        if (auth()->user()->role === 'gsu') {
            return view('maintenance-checklists.gsu-show', compact('checklist'));
        }
        
        return view('maintenance-checklists.show', compact('checklist'));
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
            $maintenanceChecklist->update([
                'status' => 'in_progress'
            ]);

            return redirect()->route('maintenance-checklists.scanner', $maintenanceChecklist)
                ->with('success', 'Maintenance process started!');
        } catch (\Exception $e) {
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

            // Mark unscanned assets as lost
            foreach ($unscannedAssets as $item) {
                // Update the checklist item
                $item->update([
                    'end_status' => 'MISSING',
                    'is_missing' => true,
                    'missing_reason' => 'Not found during maintenance - automatically marked as lost',
                    'date_checked' => now(),
                    'checked_by' => auth()->user()->name
                ]);

                // Create asset maintenance history record
                \App\Models\AssetMaintenanceHistory::create([
                    'asset_code' => $item->asset_code,
                    'maintenance_checklist_id' => $maintenanceChecklist->id,
                    'start_status' => $item->start_status,
                    'end_status' => 'MISSING',
                    'scanned_by' => auth()->user()->name,
                    'scanned_at' => now(),
                    'notes' => 'Asset not found during maintenance checklist - automatically marked as lost'
                ]);

                // Resolve the related Asset by asset_code and create lost asset record if available
                $asset = $item->asset; // belongsTo via asset_code
                if ($asset) {
                    \App\Models\LostAsset::create([
                        'asset_id' => $asset->id,
                        'reported_by' => auth()->id(),
                        'reported_date' => now(),
                        'last_known_location' => $asset->location->name ?? 'Unknown',
                        'investigation_notes' => 'Asset not found during maintenance checklist - automatically marked as lost',
                        'status' => 'lost',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                } else {
                    // If no asset found for the asset_code, skip LostAsset creation but continue processing
                    \Log::warning('Skipping LostAsset creation: No Asset found for asset_code', [
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

            DB::commit();

            $message = $markedAsLostCount > 0 
                ? "Maintenance checklist completed successfully! {$markedAsLostCount} unscanned assets have been marked as lost."
                : "Maintenance checklist completed successfully! All assets were processed.";

            return response()->json([
                'success' => true,
                'message' => $message,
                'marked_as_lost_count' => $markedAsLostCount
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
            
            // Get all active assets in this location
            $assets = \App\Models\Asset::where('location_id', $location->id)
                ->where('status', '!=', 'Disposed')
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
} 