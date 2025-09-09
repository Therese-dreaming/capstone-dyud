<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\MaintenanceChecklist;
use App\Models\MaintenanceChecklistItem;
use App\Models\AssetMaintenanceHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssetScannerController extends Controller
{
    public function scan(Request $request)
    {
        $request->validate([
            'asset_code' => 'required|string',
            'maintenance_checklist_id' => 'required|exists:maintenance_checklists,id',
            'end_status' => 'required|in:OK,FOR REPAIR,FOR REPLACEMENT',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $checklist = MaintenanceChecklist::findOrFail($request->maintenance_checklist_id);
            
            // Check if checklist is in the right status for scanning
            if (!in_array($checklist->status, ['acknowledged', 'in_progress'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'This checklist cannot be scanned at this time.'
                ], 400);
            }

            // Find the asset
            $asset = Asset::where('asset_code', $request->asset_code)->first();
            if (!$asset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asset not found.'
                ], 404);
            }

            // Find the checklist item
            $checklistItem = $checklist->items()
                ->where('asset_code', $request->asset_code)
                ->first();

            if (!$checklistItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'This asset is not part of this maintenance checklist.'
                ], 400);
            }

            // Check if already scanned
            if ($checklistItem->is_scanned) {
                return response()->json([
                    'success' => false,
                    'message' => 'This asset has already been scanned.'
                ], 400);
            }

            // Update the checklist item
            $checklistItem->update([
                'end_status' => $request->end_status,
                'notes' => $request->notes,
                'is_scanned' => true,
                'scanned_at' => now(),
                'scanned_by' => auth()->user()->name
            ]);

            // Create maintenance history record with location information
            AssetMaintenanceHistory::create([
                'asset_code' => $request->asset_code,
                'maintenance_checklist_id' => $checklist->id,
                'start_status' => $checklistItem->start_status,
                'end_status' => $request->end_status,
                'scanned_by' => auth()->user()->name,
                'scanned_at' => now(),
                'notes' => $request->notes,
                'location_id' => $checklistItem->location_id,
                'location_name' => $checklistItem->location_name
            ]);

            // Update asset condition and status based on end status
            $newCondition = $this->mapStatusToCondition($request->end_status);
            $newStatus = $this->mapStatusToAssetStatus($request->end_status);
            
            $asset->update([
                'condition' => $newCondition,
                'status' => $newStatus
            ]);

            // Update checklist status to in_progress if it's still acknowledged
            if ($checklist->status === 'acknowledged') {
                $checklist->update(['status' => 'in_progress']);
            }

            DB::commit();

            Log::info("Asset {$request->asset_code} scanned for checklist {$checklist->id} by " . auth()->user()->name);

            return response()->json([
                'success' => true,
                'message' => 'Asset scanned successfully.',
                'data' => [
                    'asset' => $asset,
                    'checklist_item' => $checklistItem,
                    'progress' => $checklist->fresh()->scanning_progress
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Asset scanning failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to scan asset: ' . $e->getMessage()
            ], 500);
        }
    }

    public function markMissing(Request $request)
    {
        $request->validate([
            'asset_code' => 'required|string',
            'maintenance_checklist_id' => 'required|exists:maintenance_checklists,id',
            'reported_by' => 'required|exists:users,id',
            'reported_date' => 'required|date',
            'investigation_notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $checklist = MaintenanceChecklist::findOrFail($request->maintenance_checklist_id);
            
            if (!in_array($checklist->status, ['acknowledged', 'in_progress'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'This checklist cannot be modified at this time.'
                ], 400);
            }

            $checklistItem = $checklist->items()
                ->where('asset_code', $request->asset_code)
                ->first();

            if (!$checklistItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'This asset is not part of this maintenance checklist.'
                ], 400);
            }

            if ($checklistItem->is_scanned || $checklistItem->is_missing) {
                return response()->json([
                    'success' => false,
                    'message' => 'This asset has already been processed.'
                ], 400);
            }

            // Find the asset
            $asset = Asset::where('asset_code', $request->asset_code)->first();
            if (!$asset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asset not found.'
                ], 404);
            }

            // Mark as missing in maintenance checklist
            $checklistItem->update([
                'is_missing' => true,
                'missing_reason' => 'Lost during maintenance',
                'scanned_at' => now(),
                'scanned_by' => auth()->user()->name
            ]);

            // Create record in asset_maintenance_history
            AssetMaintenanceHistory::create([
                'asset_code' => $request->asset_code,
                'maintenance_checklist_id' => $checklist->id,
                'start_status' => $checklistItem->start_status,
                'end_status' => 'LOST',
                'scanned_by' => auth()->user()->name,
                'scanned_at' => now(),
                'notes' => 'Asset marked as lost during maintenance checklist',
                'location_id' => $checklistItem->location_id,
                'location_name' => $checklistItem->location_name
            ]);

            // Get asset location for last_known_location (fallback to original location)
            $asset->load(['location', 'originalLocation']);
            $lastKnownLocation = 'Unknown';
            if ($asset->location) {
                $lastKnownLocation = $asset->location->building . ' - Floor ' . $asset->location->floor . ' - Room ' . $asset->location->room;
            } elseif ($asset->originalLocation) {
                $lastKnownLocation = $asset->originalLocation->building . ' - Floor ' . $asset->originalLocation->floor . ' - Room ' . $asset->originalLocation->room;
            }

            // Create record in lost_assets table
            \App\Models\LostAsset::create([
                'asset_id' => $asset->id,
                'reported_by' => $request->reported_by,
                'reported_date' => now()->format('Y-m-d'), // Automatic as now()
                'last_known_location' => $lastKnownLocation, // Automatic from asset location
                'investigation_notes' => $request->investigation_notes,
                'status' => \App\Models\LostAsset::STATUS_LOST,
                'found_date' => null,
                'found_notes' => null
            ]);

            // Update asset status to Lost
            $asset->update(['status' => 'Lost']);

            // Update checklist status to in_progress if it's still acknowledged
            if ($checklist->status === 'acknowledged') {
                $checklist->update(['status' => 'in_progress']);
            }

            DB::commit();

            Log::info("Asset {$request->asset_code} marked as lost for checklist {$checklist->id} by " . auth()->user()->name);

            return response()->json([
                'success' => true,
                'message' => 'Asset marked as lost successfully.',
                'data' => [
                    'progress' => $checklist->fresh()->scanning_progress
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Mark missing asset failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark asset as lost: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProgress(Request $request, MaintenanceChecklist $checklist)
    {
        $checklist->load('items');
        
        return response()->json([
            'success' => true,
            'data' => [
                'progress' => $checklist->scanning_progress,
                'unscanned_assets' => $checklist->unscanned_assets,
                'missing_assets' => $checklist->missing_assets,
                'can_complete' => $checklist->canBeCompleted()
            ]
        ]);
    }

    private function mapStatusToCondition($status)
    {
        return match($status) {
            'OK' => 'Good',
            'FOR REPAIR' => 'Fair',
            'FOR REPLACEMENT' => 'Poor',
            default => 'Unknown'
        };
    }

    private function mapStatusToAssetStatus($status)
    {
        return match($status) {
            'OK' => 'Available',
            'FOR REPAIR' => 'For Repair',
            'FOR REPLACEMENT' => 'For Replacement',
            default => 'Available'
        };
    }
}