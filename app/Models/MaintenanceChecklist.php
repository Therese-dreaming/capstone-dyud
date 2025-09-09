<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceChecklist extends Model
{
    protected $fillable = [
        'maintenance_id',
        'school_year',
        'department',
        'date_reported',
        'program',
        'room_number',
        'location_id',
        'instructor',
        'instructor_signature',
        'checked_by',
        'checked_by_signature',
        'date_checked',
        'gsu_staff',
        'gsu_staff_signature',
        'notes',
        'status',
        'acknowledged_at',
        'acknowledged_by',
        'completed_at',
        'completed_by',
        'has_missing_assets',
        'missing_assets_acknowledged'
    ];

    protected $casts = [
        'date_reported' => 'date',
        'date_checked' => 'date',
        'acknowledged_at' => 'datetime',
        'completed_at' => 'datetime',
        'missing_assets_acknowledged' => 'array',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(MaintenanceChecklistItem::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function acknowledgedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function maintenanceRequest(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRequest::class, 'id', 'maintenance_checklist_id');
    }

    public function maintenanceHistory(): HasMany
    {
        return $this->hasMany(AssetMaintenanceHistory::class);
    }

    public function assets()
    {
        return $this->items()->whereNotNull('asset_code');
    }

    public function getStatusSummaryAttribute()
    {
        $totalItems = $this->items->count();
        $okItems = $this->items->where('end_status', 'OK')->count();
        $repairItems = $this->items->where('end_status', 'FOR REPAIR')->count();
        $replacementItems = $this->items->where('end_status', 'FOR REPLACEMENT')->count();

        return [
            'total' => $totalItems,
            'ok' => $okItems,
            'repair' => $repairItems,
            'replacement' => $replacementItems
        ];
    }

    public function getScanningProgressAttribute()
    {
        $totalItems = $this->items->count();
        $scannedItems = $this->items->where('is_scanned', true)->count();
        $missingItems = $this->items->where('is_missing', true)->count();

        return [
            'total' => $totalItems,
            'scanned' => $scannedItems,
            'missing' => $missingItems,
            'remaining' => $totalItems - $scannedItems - $missingItems,
            'percentage' => $totalItems > 0 ? round(($scannedItems + $missingItems) / $totalItems * 100, 2) : 0
        ];
    }

    public function getMissingAssetsAttribute()
    {
        return $this->items->where('is_missing', true);
    }

    public function getUnscannedAssetsAttribute()
    {
        return $this->items->where('is_scanned', false)->where('is_missing', false);
    }

    public function canBeAcknowledged()
    {
        return $this->status === 'created';
    }

    public function canBeStarted()
    {
        return $this->status === 'acknowledged';
    }

    public function canBeCompleted()
    {
        return $this->status === 'in_progress' && 
               ($this->scanning_progress['scanned'] + $this->scanning_progress['missing']) === $this->scanning_progress['total'];
    }

    /**
     * Generate a unique maintenance ID
     */
    public static function generateMaintenanceId()
    {
        do {
            // Format: MNT-YYYY-XXXX (e.g., MNT-2024-0001)
            $year = date('Y');
            $randomNumber = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $maintenanceId = "MNT-{$year}-{$randomNumber}";
        } while (self::where('maintenance_id', $maintenanceId)->exists());

        return $maintenanceId;
    }

    /**
     * Boot method to automatically generate maintenance_id
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->maintenance_id)) {
                $model->maintenance_id = self::generateMaintenanceId();
            }
        });
    }
} 