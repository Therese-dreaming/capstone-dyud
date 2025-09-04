<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceChecklist extends Model
{
    protected $fillable = [
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
        'notes'
    ];

    protected $casts = [
        'date_reported' => 'date',
        'date_checked' => 'date',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(MaintenanceChecklistItem::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
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
} 