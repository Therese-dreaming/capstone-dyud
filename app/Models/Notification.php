<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'data',
        'user_id',
        'created_by',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Notification types
    const TYPE_MAINTENANCE_REQUEST = 'maintenance_request';
    const TYPE_ASSET_CREATED = 'asset_created';
    const TYPE_ASSET_EDITED = 'asset_edited';
    const TYPE_ASSET_TRANSFERRED = 'asset_transferred';
    const TYPE_CHECKLIST_ACKNOWLEDGED = 'checklist_acknowledged';
    const TYPE_CHECKLIST_STARTED = 'checklist_started';
    const TYPE_CHECKLIST_COMPLETED = 'checklist_completed';
    const TYPE_LOCATION_ASSIGNED = 'location_assigned';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(): void
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get notification icon based on type
     */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            self::TYPE_MAINTENANCE_REQUEST => 'fas fa-tools',
            self::TYPE_ASSET_CREATED => 'fas fa-plus-circle',
            self::TYPE_ASSET_EDITED => 'fas fa-edit',
            self::TYPE_ASSET_TRANSFERRED => 'fas fa-exchange-alt',
            self::TYPE_CHECKLIST_ACKNOWLEDGED => 'fas fa-handshake',
            self::TYPE_CHECKLIST_STARTED => 'fas fa-cogs',
            self::TYPE_CHECKLIST_COMPLETED => 'fas fa-check-circle',
            self::TYPE_LOCATION_ASSIGNED => 'fas fa-map-marker-alt',
            default => 'fas fa-bell',
        };
    }

    /**
     * Get notification color based on type
     */
    public function getColorAttribute(): string
    {
        return match($this->type) {
            self::TYPE_MAINTENANCE_REQUEST => 'text-blue-600',
            self::TYPE_ASSET_CREATED => 'text-green-600',
            self::TYPE_ASSET_EDITED => 'text-yellow-600',
            self::TYPE_ASSET_TRANSFERRED => 'text-indigo-600',
            self::TYPE_CHECKLIST_ACKNOWLEDGED => 'text-purple-600',
            self::TYPE_CHECKLIST_STARTED => 'text-orange-600',
            self::TYPE_CHECKLIST_COMPLETED => 'text-green-600',
            self::TYPE_LOCATION_ASSIGNED => 'text-blue-600',
            default => 'text-gray-600',
        };
    }
}