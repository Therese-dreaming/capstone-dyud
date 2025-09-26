<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send notification to all admin users
     */
    public function notifyAdmins(string $type, string $title, string $message, array $data = [], ?int $createdBy = null): void
    {
        try {
            $adminUsers = User::where('role', 'admin')->orWhere('role', 'superadmin')->get();
            
            foreach ($adminUsers as $admin) {
                Notification::create([
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                    'data' => $data,
                    'user_id' => $admin->id,
                    'created_by' => $createdBy,
                ]);
            }
            
            Log::info("Notification sent to {$adminUsers->count()} admin users", [
                'type' => $type,
                'title' => $title,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send admin notification', [
                'type' => $type,
                'title' => $title,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send notification to all GSU users
     */
    public function notifyGSU(string $type, string $title, string $message, array $data = [], ?int $createdBy = null): void
    {
        try {
            $gsuUsers = User::where('role', 'gsu')->get();
            
            foreach ($gsuUsers as $gsu) {
                Notification::create([
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                    'data' => $data,
                    'user_id' => $gsu->id,
                    'created_by' => $createdBy,
                ]);
            }
            
            Log::info("Notification sent to {$gsuUsers->count()} GSU users", [
                'type' => $type,
                'title' => $title,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send GSU notification', [
                'type' => $type,
                'title' => $title,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send notification to specific user
     */
    public function notifyUser(int $userId, string $type, string $title, string $message, array $data = [], ?int $createdBy = null): void
    {
        try {
            Notification::create([
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
                'user_id' => $userId,
                'created_by' => $createdBy,
            ]);
            
            Log::info("Notification sent to user {$userId}", [
                'type' => $type,
                'title' => $title,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send user notification', [
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send maintenance request notification
     */
    public function notifyMaintenanceRequest($maintenanceRequest): void
    {
        $locationStr = $this->formatLocation(optional($maintenanceRequest)->location);
        $this->notifyAdmins(
            Notification::TYPE_MAINTENANCE_REQUEST,
            'New Maintenance Request',
            "A new maintenance request has been submitted by {$maintenanceRequest->requester->name} for {$locationStr}",
            [
                'maintenance_request_id' => $maintenanceRequest->id,
                'requester_name' => $maintenanceRequest->requester->name,
                'location' => $locationStr,
                'school_year' => $maintenanceRequest->school_year,
                'department' => $maintenanceRequest->department,
            ],
            $maintenanceRequest->requester_id
        );
    }

    /**
     * Send asset created notification
     */
    public function notifyAssetCreated($asset): void
    {
        $this->notifyAdmins(
            Notification::TYPE_ASSET_CREATED,
            'New Asset Created',
            "A new asset '{$asset->asset_name}' (Code: {$asset->asset_code}) has been created by GSU",
            [
                'asset_id' => $asset->id,
                'asset_code' => $asset->asset_code,
                'asset_name' => $asset->asset_name,
                'location' => $asset->location ? $asset->location->building . ' - Floor ' . $asset->location->floor . ' - Room ' . $asset->location->room : 'Unknown',
            ],
            auth()->id()
        );
    }

    /**
     * Send asset edited notification
     */
    public function notifyAssetEdited($asset): void
    {
        $this->notifyAdmins(
            Notification::TYPE_ASSET_EDITED,
            'Asset Updated',
            "Asset '{$asset->asset_name}' (Code: {$asset->asset_code}) has been updated by GSU",
            [
                'asset_id' => $asset->id,
                'asset_code' => $asset->asset_code,
                'asset_name' => $asset->asset_name,
                'location' => $asset->location ? $asset->location->building . ' - Floor ' . $asset->location->floor . ' - Room ' . $asset->location->room : 'Unknown',
            ],
            auth()->id()
        );
    }

    /**
     * Send checklist acknowledged notification
     */
    public function notifyChecklistAcknowledged($checklist): void
    {
        $locationStr = $this->formatLocation(optional($checklist)->location);
        $this->notifyAdmins(
            Notification::TYPE_CHECKLIST_ACKNOWLEDGED,
            'Maintenance Checklist Acknowledged',
            "Maintenance checklist #{$checklist->id} for {$locationStr} has been acknowledged by GSU",
            [
                'checklist_id' => $checklist->id,
                'location' => $locationStr,
                'school_year' => $checklist->school_year,
                'department' => $checklist->department,
            ],
            auth()->id()
        );
    }

    /**
     * Send checklist started notification
     */
    public function notifyChecklistStarted($checklist): void
    {
        $locationStr = $this->formatLocation(optional($checklist)->location);
        $this->notifyAdmins(
            Notification::TYPE_CHECKLIST_STARTED,
            'Maintenance Checklist Started',
            "Maintenance checklist #{$checklist->id} for {$locationStr} has been started by GSU",
            [
                'checklist_id' => $checklist->id,
                'location' => $locationStr,
                'school_year' => $checklist->school_year,
                'department' => $checklist->department,
            ],
            auth()->id()
        );
    }

    /**
     * Send checklist completed notification
     */
    public function notifyChecklistCompleted($checklist): void
    {
        $locationStr = $this->formatLocation(optional($checklist)->location);
        $this->notifyAdmins(
            Notification::TYPE_CHECKLIST_COMPLETED,
            'Maintenance Checklist Completed',
            "Maintenance checklist #{$checklist->id} for {$locationStr} has been completed by GSU",
            [
                'checklist_id' => $checklist->id,
                'location' => $locationStr,
                'school_year' => $checklist->school_year,
                'department' => $checklist->department,
                'completed_at' => $checklist->completed_at,
            ],
            auth()->id()
        );
    }

    /**
     * Send maintenance request approved notification to GSU
     */
    public function notifyMaintenanceRequestApproved($maintenanceRequest): void
    {
        $locationStr = $this->formatLocation(optional($maintenanceRequest)->location);
        $this->notifyGSU(
            Notification::TYPE_MAINTENANCE_REQUEST,
            'New Maintenance Checklist Assigned',
            "A maintenance checklist has been approved and assigned to you for {$locationStr}",
            [
                'maintenance_request_id' => $maintenanceRequest->id,
                'maintenance_checklist_id' => $maintenanceRequest->maintenance_checklist_id,
                'requester_name' => $maintenanceRequest->requester->name,
                'location' => $locationStr,
                'school_year' => $maintenanceRequest->school_year,
                'department' => $maintenanceRequest->department,
            ],
            auth()->id()
        );
    }

    /**
     * Send self-notification for acknowledging checklist
     */
    public function notifyGSUChecklistAcknowledged($checklist): void
    {
        $locationStr = $this->formatLocation(optional($checklist)->location);
        $this->notifyUser(
            auth()->id(),
            Notification::TYPE_CHECKLIST_ACKNOWLEDGED,
            'Checklist Acknowledged',
            "You have acknowledged maintenance checklist #{$checklist->id} for {$locationStr}",
            [
                'checklist_id' => $checklist->id,
                'location' => $locationStr,
                'school_year' => $checklist->school_year,
                'department' => $checklist->department,
            ],
            auth()->id()
        );
    }

    /**
     * Send self-notification for starting checklist
     */
    public function notifyGSUChecklistStarted($checklist): void
    {
        $locationStr = $this->formatLocation(optional($checklist)->location);
        $this->notifyUser(
            auth()->id(),
            Notification::TYPE_CHECKLIST_STARTED,
            'Maintenance Started',
            "You have started maintenance checklist #{$checklist->id} for {$locationStr}",
            [
                'checklist_id' => $checklist->id,
                'location' => $locationStr,
                'school_year' => $checklist->school_year,
                'department' => $checklist->department,
            ],
            auth()->id()
        );
    }

    /**
     * Send self-notification for completing checklist
     */
    public function notifyGSUChecklistCompleted($checklist): void
    {
        $locationStr = $this->formatLocation(optional($checklist)->location);
        $this->notifyUser(
            auth()->id(),
            Notification::TYPE_CHECKLIST_COMPLETED,
            'Maintenance Completed',
            "You have completed maintenance checklist #{$checklist->id} for {$locationStr}",
            [
                'checklist_id' => $checklist->id,
                'location' => $locationStr,
                'school_year' => $checklist->school_year,
                'department' => $checklist->department,
                'completed_at' => $checklist->completed_at,
            ],
            auth()->id()
        );
    }

    /**
     * Send notification to user when they create maintenance request
     */
    public function notifyUserMaintenanceRequestCreated($maintenanceRequest): void
    {
        $locationStr = $this->formatLocation(optional($maintenanceRequest)->location);
        $this->notifyUser(
            $maintenanceRequest->requester_id,
            Notification::TYPE_MAINTENANCE_REQUEST,
            'Maintenance Request Submitted',
            "Your maintenance request for {$locationStr} has been submitted successfully and is awaiting admin approval.",
            [
                'maintenance_request_id' => $maintenanceRequest->id,
                'location' => $locationStr,
                'school_year' => $maintenanceRequest->school_year,
                'department' => $maintenanceRequest->department,
                'status' => 'pending',
            ],
            $maintenanceRequest->requester_id
        );
    }

    /**
     * Send notification to user when admin approves their request
     */
    public function notifyUserMaintenanceRequestApproved($maintenanceRequest): void
    {
        $locationStr = $this->formatLocation(optional($maintenanceRequest)->location);
        $this->notifyUser(
            $maintenanceRequest->requester_id,
            Notification::TYPE_MAINTENANCE_REQUEST,
            'Maintenance Request Approved',
            "Your maintenance request for {$locationStr} has been approved by admin. GSU will now handle your request.",
            [
                'maintenance_request_id' => $maintenanceRequest->id,
                'maintenance_checklist_id' => $maintenanceRequest->maintenance_checklist_id,
                'location' => $locationStr,
                'school_year' => $maintenanceRequest->school_year,
                'department' => $maintenanceRequest->department,
                'status' => 'approved',
                'approved_at' => $maintenanceRequest->approved_at,
            ],
            auth()->id()
        );
    }

    /**
     * Send notification to user when admin rejects their request
     */
    public function notifyUserMaintenanceRequestRejected($maintenanceRequest): void
    {
        $this->notifyUser(
            $maintenanceRequest->requester_id,
            Notification::TYPE_MAINTENANCE_REQUEST,
            'Maintenance Request Rejected',
            "Your maintenance request for {$maintenanceRequest->location->building} - Floor {$maintenanceRequest->location->floor} - Room {$maintenanceRequest->location->room} has been rejected by admin. Please check the admin notes for more details.",
            [
                'maintenance_request_id' => $maintenanceRequest->id,
                'location' => $maintenanceRequest->location->building . ' - Floor ' . $maintenanceRequest->location->floor . ' - Room ' . $maintenanceRequest->location->room,
                'school_year' => $maintenanceRequest->school_year,
                'department' => $maintenanceRequest->department,
                'status' => 'rejected',
                'rejected_at' => $maintenanceRequest->rejected_at,
                'admin_notes' => $maintenanceRequest->admin_notes,
            ],
            auth()->id()
        );
    }

    /**
     * Send notification to user when GSU starts their maintenance
     */
    public function notifyUserMaintenanceStarted($maintenanceRequest): void
    {
        $locationStr = $this->formatLocation(optional($maintenanceRequest)->location);
        $this->notifyUser(
            $maintenanceRequest->requester_id,
            Notification::TYPE_CHECKLIST_STARTED,
            'Maintenance In Progress',
            "GSU has started working on your maintenance request for {$locationStr}. The maintenance is now in progress.",
            [
                'maintenance_request_id' => $maintenanceRequest->id,
                'maintenance_checklist_id' => $maintenanceRequest->maintenance_checklist_id,
                'location' => $locationStr,
                'school_year' => $maintenanceRequest->school_year,
                'department' => $maintenanceRequest->department,
                'status' => 'in_progress',
            ],
            auth()->id()
        );
    }

    /**
     * Send notification to user when GSU completes their maintenance
     */
    public function notifyUserMaintenanceCompleted($maintenanceRequest): void
    {
        $locationStr = $this->formatLocation(optional($maintenanceRequest)->location);
        $this->notifyUser(
            $maintenanceRequest->requester_id,
            Notification::TYPE_CHECKLIST_COMPLETED,
            'Maintenance Completed',
            "Your maintenance request for {$locationStr} has been completed by GSU. You can now view the maintenance checklist details.",
            [
                'maintenance_request_id' => $maintenanceRequest->id,
                'maintenance_checklist_id' => $maintenanceRequest->maintenance_checklist_id,
                'location' => $locationStr,
                'school_year' => $maintenanceRequest->school_year,
                'department' => $maintenanceRequest->department,
                'status' => 'completed',
                'completed_at' => $maintenanceRequest->updated_at,
            ],
            auth()->id()
        );
    }

    /**
     * Helper to format a location string safely
     */
    private function formatLocation($location): string
    {
        if (!$location) {
            return 'N/A';
        }
        $building = $location->building ?? 'Unknown';
        $floor = $location->floor ?? 'N/A';
        $room = $location->room ?? 'N/A';
        return $building . ' - Floor ' . $floor . ' - Room ' . $room;
    }

    /**
     * Get unread notifications count for user
     */
    public function getUnreadCount(int $userId): int
    {
        return Notification::forUser($userId)->unread()->count();
    }

    /**
     * Get recent notifications for user
     */
    public function getRecentNotifications(int $userId, int $limit = 10)
    {
        return Notification::forUser($userId)
            ->with(['createdBy'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId, int $userId): bool
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            return true;
        }

        return false;
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead(int $userId): int
    {
        return Notification::forUser($userId)->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Notify admins of new asset pending approval from purchasing
     */
    public function notifyAdminsOfPendingAsset($asset): void
    {
        $this->notifyAdmins(
            Notification::TYPE_ASSET_CREATED,
            'New Asset Pending Approval',
            "A new asset '{$asset->name}' (Code: {$asset->asset_code}) has been submitted by Purchasing and is pending your approval.",
            [
                'asset_id' => $asset->id,
                'asset_code' => $asset->asset_code,
                'asset_name' => $asset->name,
                'category' => $asset->category->name ?? 'Unknown',
                'purchase_cost' => $asset->purchase_cost,
                'created_by_name' => $asset->createdBy->name ?? 'Unknown',
            ],
            $asset->created_by
        );
    }

    /**
     * Notify purchasing user when asset is approved
     */
    public function notifyAssetApproved($asset): void
    {
        $this->notifyUser(
            $asset->created_by,
            Notification::TYPE_ASSET_EDITED,
            'Asset Approved',
            "Your asset '{$asset->name}' (Code: {$asset->asset_code}) has been approved by admin. It can now be deployed by GSU.",
            [
                'asset_id' => $asset->id,
                'asset_code' => $asset->asset_code,
                'asset_name' => $asset->name,
                'approved_by_name' => $asset->approvedBy->name ?? 'Admin',
                'approved_at' => $asset->approved_at,
            ],
            $asset->approved_by
        );
    }

    /**
     * Notify purchasing user when asset is rejected
     */
    public function notifyAssetRejected($asset): void
    {
        $this->notifyUser(
            $asset->created_by,
            Notification::TYPE_ASSET_EDITED,
            'Asset Rejected',
            "Your asset '{$asset->name}' (Code: {$asset->asset_code}) has been rejected by admin. Reason: {$asset->rejection_reason}",
            [
                'asset_id' => $asset->id,
                'asset_code' => $asset->asset_code,
                'asset_name' => $asset->name,
                'rejection_reason' => $asset->rejection_reason,
                'rejected_by_name' => $asset->approvedBy->name ?? 'Admin',
                'rejected_at' => $asset->approved_at,
            ],
            $asset->approved_by
        );
    }

    /**
     * Notify purchasing user when asset is deployed by GSU
     */
    public function notifyAssetDeployed($asset): void
    {
        $this->notifyUser(
            $asset->created_by,
            Notification::TYPE_ASSET_EDITED,
            'Asset Deployed',
            "Your asset '{$asset->name}' (Code: {$asset->asset_code}) has been deployed by GSU to {$asset->location->building} - Floor {$asset->location->floor} - Room {$asset->location->room}.",
            [
                'asset_id' => $asset->id,
                'asset_code' => $asset->asset_code,
                'asset_name' => $asset->name,
                'location' => $asset->location->building . ' - Floor ' . $asset->location->floor . ' - Room ' . $asset->location->room,
                'deployed_at' => now(),
            ],
            auth()->id()
        );
    }

    /**
     * Notify GSU users when asset is approved by admin
     */
    public function notifyGSUAssetApproved($asset): void
    {
        $this->notifyGSU(
            Notification::TYPE_ASSET_CREATED,
            'New Asset Ready for Deployment',
            "Asset '{$asset->name}' (Code: {$asset->asset_code}) has been approved by admin and is ready for deployment.",
            [
                'asset_id' => $asset->id,
                'asset_code' => $asset->asset_code,
                'asset_name' => $asset->name,
                'category' => $asset->category->name ?? 'Unknown',
                'purchase_cost' => $asset->purchase_cost,
                'approved_by_name' => $asset->approvedBy->name ?? 'Admin',
                'approved_at' => $asset->approved_at,
            ],
            $asset->approved_by
        );
    }

    /**
     * Send asset transfer notification
     */
    public function notifyAssetTransferred($asset, $oldLocation, $newLocation): void
    {
        $transferredBy = auth()->user() ? auth()->user()->name : 'System';
        
        // Notify admins about the transfer
        $this->notifyAdmins(
            Notification::TYPE_ASSET_TRANSFERRED,
            'Asset Transferred',
            "Asset '{$asset->name}' (Code: {$asset->asset_code}) has been transferred from {$oldLocation->building} - Floor {$oldLocation->floor} - Room {$oldLocation->room} to {$newLocation->building} - Floor {$newLocation->floor} - Room {$newLocation->room} by {$transferredBy}.",
            [
                'asset_id' => $asset->id,
                'asset_code' => $asset->asset_code,
                'asset_name' => $asset->name,
                'old_location' => $oldLocation->building . ' - Floor ' . $oldLocation->floor . ' - Room ' . $oldLocation->room,
                'new_location' => $newLocation->building . ' - Floor ' . $newLocation->floor . ' - Room ' . $newLocation->room,
                'transferred_by' => $transferredBy,
                'transferred_at' => now(),
            ],
            auth()->id()
        );

        // Notify GSU users about the transfer
        $this->notifyGSU(
            Notification::TYPE_ASSET_TRANSFERRED,
            'Asset Location Updated',
            "Asset '{$asset->name}' (Code: {$asset->asset_code}) has been moved to {$newLocation->building} - Floor {$newLocation->floor} - Room {$newLocation->room}.",
            [
                'asset_id' => $asset->id,
                'asset_code' => $asset->asset_code,
                'asset_name' => $asset->name,
                'old_location' => $oldLocation->building . ' - Floor ' . $oldLocation->floor . ' - Room ' . $oldLocation->room,
                'new_location' => $newLocation->building . ' - Floor ' . $newLocation->floor . ' - Room ' . $newLocation->room,
                'transferred_by' => $transferredBy,
                'transferred_at' => now(),
            ],
            auth()->id()
        );

        // If the asset was created by purchasing, notify them about the transfer
        if ($asset->created_by) {
            $this->notifyUser(
                $asset->created_by,
                Notification::TYPE_ASSET_TRANSFERRED,
                'Your Asset Has Been Transferred',
                "Your asset '{$asset->name}' (Code: {$asset->asset_code}) has been transferred to a new location: {$newLocation->building} - Floor {$newLocation->floor} - Room {$newLocation->room}.",
                [
                    'asset_id' => $asset->id,
                    'asset_code' => $asset->asset_code,
                    'asset_name' => $asset->name,
                    'old_location' => $oldLocation->building . ' - Floor ' . $oldLocation->floor . ' - Room ' . $oldLocation->room,
                    'new_location' => $newLocation->building . ' - Floor ' . $newLocation->floor . ' - Room ' . $newLocation->room,
                    'transferred_by' => $transferredBy,
                    'transferred_at' => now(),
                ],
                auth()->id()
            );
        }
    }

    /**
     * Send notification to user when they are assigned to manage a location
     */
    public function notifyUserLocationAssigned($userLocation): void
    {
        $locationStr = $this->formatLocation($userLocation->location);
        $assignedByName = $userLocation->assignedBy->name ?? 'Admin';
        
        $this->notifyUser(
            $userLocation->user_id,
            Notification::TYPE_LOCATION_ASSIGNED,
            'Location Assignment',
            "You have been assigned to manage {$locationStr} by {$assignedByName}. You can now access and manage assets in this location.",
            [
                'user_location_id' => $userLocation->id,
                'location_id' => $userLocation->location_id,
                'location' => $locationStr,
                'assigned_by_name' => $assignedByName,
                'assigned_at' => $userLocation->assigned_at ?? now(),
                'notes' => $userLocation->notes,
            ],
            $userLocation->assigned_by
        );
    }
}
