<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_id',
        'location_id',
        'school_year',
        'department',
        'date_reported',
        'program',
        'instructor_name',
        'notes',
        'status',
        'approved_by',
        'approved_at',
        'admin_notes',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'acknowledged_by',
        'acknowledged_at',
        'maintenance_checklist_id',
    ];

    protected $casts = [
        'date_reported' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'acknowledged_at' => 'datetime',
    ];

    public function requester(): BelongsTo { return $this->belongsTo(User::class, 'requester_id'); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function rejector(): BelongsTo { return $this->belongsTo(User::class, 'rejected_by'); }
    public function acknowledger(): BelongsTo { return $this->belongsTo(User::class, 'acknowledged_by'); }
    public function location(): BelongsTo { return $this->belongsTo(Location::class); }
    public function checklist(): BelongsTo { return $this->belongsTo(MaintenanceChecklist::class, 'maintenance_checklist_id'); }
    
    // Alias methods for view compatibility
    public function approvedBy(): BelongsTo { return $this->approver(); }
    public function rejectedBy(): BelongsTo { return $this->rejector(); }
    public function acknowledgedBy(): BelongsTo { return $this->acknowledger(); }
}


