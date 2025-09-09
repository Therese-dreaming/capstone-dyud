<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetMaintenanceHistory extends Model
{
    protected $table = 'asset_maintenance_history';

    protected $fillable = [
        'asset_code',
        'maintenance_checklist_id',
        'start_status',
        'end_status',
        'scanned_by',
        'scanned_at',
        'notes'
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_code', 'asset_code');
    }

    public function maintenanceChecklist(): BelongsTo
    {
        return $this->belongsTo(MaintenanceChecklist::class);
    }

    public function getStatusClassAttribute()
    {
        return match($this->end_status) {
            'OK' => 'bg-green-100 text-green-800',
            'FOR REPAIR' => 'bg-yellow-100 text-yellow-800',
            'FOR REPLACEMENT' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}
