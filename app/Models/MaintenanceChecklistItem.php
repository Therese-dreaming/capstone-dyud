<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceChecklistItem extends Model
{
    protected $fillable = [
        'maintenance_checklist_id',
        'asset_code',
        'particulars',
        'quantity',
        'start_status',
        'end_status',
        'notes'
    ];

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(MaintenanceChecklist::class, 'maintenance_checklist_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_code', 'asset_code');
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