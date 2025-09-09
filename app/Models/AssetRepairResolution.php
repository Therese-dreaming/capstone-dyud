<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetRepairResolution extends Model
{
    protected $fillable = [
        'asset_id',
        'resolved_by',
        'previous_status',
        'resolution_status',
        'resolution_notes',
        'actions_taken',
        'repair_cost',
        'resolution_date',
        'vendor_name',
        'invoice_number'
    ];

    protected $casts = [
        'resolution_date' => 'date',
        'repair_cost' => 'decimal:2',
    ];

    // Resolution status constants
    const RESOLUTION_REPAIRED = 'Repaired';
    const RESOLUTION_DISPOSED = 'Disposed';
    const RESOLUTION_REPLACED = 'Replaced';
    const RESOLUTION_RETURNED_TO_SERVICE = 'Returned to Service';

    // Previous status constants
    const PREVIOUS_FOR_REPAIR = 'For Repair';
    const PREVIOUS_FOR_MAINTENANCE = 'For Maintenance';
    const PREVIOUS_FOR_REPLACEMENT = 'For Replacement';

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function getResolutionBadgeClass()
    {
        return match($this->resolution_status) {
            self::RESOLUTION_REPAIRED => 'bg-green-100 text-green-800',
            self::RESOLUTION_DISPOSED => 'bg-gray-100 text-gray-800',
            self::RESOLUTION_REPLACED => 'bg-blue-100 text-blue-800',
            self::RESOLUTION_RETURNED_TO_SERVICE => 'bg-emerald-100 text-emerald-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getPreviousStatusBadgeClass()
    {
        return match($this->previous_status) {
            self::PREVIOUS_FOR_REPAIR => 'bg-yellow-100 text-yellow-800',
            self::PREVIOUS_FOR_MAINTENANCE => 'bg-blue-100 text-blue-800',
            self::PREVIOUS_FOR_REPLACEMENT => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}