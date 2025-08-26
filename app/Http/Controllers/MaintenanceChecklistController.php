<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceChecklist;
use App\Models\MaintenanceChecklistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaintenanceChecklistController extends Controller
{
    public function index()
    {
        $checklists = MaintenanceChecklist::with('items')
            ->orderBy('date_reported', 'desc')
            ->paginate(10);
        
        return view('maintenance-checklists.index', compact('checklists'));
    }

    public function create()
    {
        $locations = \App\Models\Location::orderBy('building')->orderBy('floor')->orderBy('room')->get();
        return view('maintenance-checklists.create', compact('locations'));
    }

    public function store(Request $request)
    {
        \Log::info('Entered MaintenanceChecklistController@store');
        $validated = $request->validate([
            'school_year' => 'required|string|max:20',
            'department' => 'required|string|max:100',
            'date_reported' => 'required|date',
            'program' => 'nullable|string|max:100',
            'location_id' => 'required|exists:locations,id',
            'instructor' => 'required|string|max:100',
            'instructor_signature' => 'nullable|string|max:100',
            'checked_by' => 'required|string|max:100',
            'checked_by_signature' => 'nullable|string|max:100',
            'date_checked' => 'required|date',
            'gsu_staff' => 'required|string|max:100',
            'gsu_staff_signature' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.asset_code' => 'nullable|string|max:50',
            'items.*.particulars' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:0',
            'items.*.start_status' => 'required|in:OK,FOR REPAIR,FOR REPLACEMENT',
            'items.*.end_status' => 'nullable|in:OK,FOR REPAIR,FOR REPLACEMENT',
            'items.*.notes' => 'nullable|string'
        ]);
        \Log::info('Validated data:', $validated);
        try {
            DB::beginTransaction();
            $location = \App\Models\Location::findOrFail($validated['location_id']);
            $checklist = MaintenanceChecklist::create([
                'school_year' => $validated['school_year'],
                'department' => $validated['department'],
                'date_reported' => $validated['date_reported'],
                'program' => $validated['program'],
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
            \Log::info('Created checklist ID: ' . $checklist->id);
            foreach ($validated['items'] as $item) {
                $itemModel = MaintenanceChecklistItem::create([
                    'maintenance_checklist_id' => $checklist->id,
                    'asset_code' => $item['asset_code'] ?? null,
                    'particulars' => $item['particulars'],
                    'quantity' => $item['quantity'],
                    'start_status' => $item['start_status'],
                    'end_status' => $item['end_status'],
                    'notes' => $item['notes'] ?? null
                ]);
                \Log::info('Created checklist item ID: ' . $itemModel->id);
            }
            DB::commit();
            \Log::info('Maintenance checklist created successfully!');
            return redirect()->route('maintenance-checklists.index')
                ->with('success', 'Maintenance checklist created successfully!');
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
        return view('maintenance-checklists.show', compact('checklist'));
    }

    public function edit(MaintenanceChecklist $maintenanceChecklist)
    {
        $checklist = $maintenanceChecklist->load('items');
        return view('maintenance-checklists.edit', compact('checklist'));
    }

    public function update(Request $request, MaintenanceChecklist $maintenanceChecklist)
    {
        $validated = $request->validate([
            'school_year' => 'required|string|max:20',
            'department' => 'required|string|max:100',
            'date_reported' => 'required|date',
            'program' => 'nullable|string|max:100',
            'location_id' => 'required|exists:locations,id',
            'instructor' => 'required|string|max:100',
            'instructor_signature' => 'nullable|string|max:100',
            'checked_by' => 'required|string|max:100',
            'checked_by_signature' => 'nullable|string|max:100',
            'date_checked' => 'required|date',
            'gsu_staff' => 'required|string|max:100',
            'gsu_staff_signature' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.asset_code' => 'nullable|string|max:50',
            'items.*.particulars' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:0',
            'items.*.start_status' => 'required|in:OK,FOR REPAIR,FOR REPLACEMENT',
            'items.*.end_status' => 'required|in:OK,FOR REPAIR,FOR REPLACEMENT',
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
        
        $filename = "maintenance_checklist_{$checklist->room_number}_{$checklist->school_year}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($checklist) {
            $file = fopen('php://output', 'w');
            
            // Header information
            fputcsv($file, ['SY:', $checklist->school_year, '', '']);
            fputcsv($file, ['Department:', $checklist->department, '', '']);
            fputcsv($file, ['', '', '', '']);
            fputcsv($file, ['Date Reported:', $checklist->date_reported->format('d-M-y'), '', '']);
            fputcsv($file, ['Program:', $checklist->program ?? 'N/A', '', '']);
            fputcsv($file, ['Room Number:', $checklist->room_number, '', '']);
            fputcsv($file, ['Instructor', $checklist->instructor, '', '']);
            fputcsv($file, ['Signature:', '', '', '']);
            fputcsv($file, ['', '', '', '']);
            
            // Items header
            fputcsv($file, ['Particulars/Items', 'QTY', 'Start of SY Status', 'End of SY Status']);
            
            // Items
            foreach ($checklist->items as $item) {
                fputcsv($file, [$item->particulars, $item->quantity, $item->start_status, $item->end_status]);
            }
            
            fputcsv($file, ['', '', '', '']);
            fputcsv($file, ['Checked by:', $checklist->checked_by, '', '']);
            fputcsv($file, ['Signature:', '', '', '']);
            fputcsv($file, ['Date Checked:', $checklist->date_checked->format('d-M-y'), '', '']);
            fputcsv($file, ['', '', '', '']);
            fputcsv($file, ['Printed Name over Signature of GSU Staff:', $checklist->gsu_staff, '', '']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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