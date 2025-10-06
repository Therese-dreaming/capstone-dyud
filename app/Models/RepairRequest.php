<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_id',
        'asset_id',
        'semester_id',
        'school_year',
        'department',
        'date_reported',
        'program',
        'instructor_name',
        'issue_description',
        'urgency_level',
        'status',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'acknowledged_by',
        'acknowledged_at',
        'completed_by',
        'completed_at',
        'completion_notes',
    ];

    protected $casts = [
        'date_reported' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';

    // Urgency level constants
    const URGENCY_LOW = 'low';
    const URGENCY_MEDIUM = 'medium';
    const URGENCY_HIGH = 'high';
    const URGENCY_CRITICAL = 'critical';

    // Relationships
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function acknowledgedBy()
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeByUrgency($query, $urgency)
    {
        return $query->where('urgency_level', $urgency);
    }

    // Helper methods
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_APPROVED => 'blue',
            self::STATUS_REJECTED => 'red',
            self::STATUS_IN_PROGRESS => 'orange',
            self::STATUS_COMPLETED => 'green',
            default => 'gray'
        };
    }

    public function getUrgencyColorAttribute()
    {
        return match($this->urgency_level) {
            self::URGENCY_LOW => 'green',
            self::URGENCY_MEDIUM => 'yellow',
            self::URGENCY_HIGH => 'orange',
            self::URGENCY_CRITICAL => 'red',
            default => 'gray'
        };
    }

    public function getFormattedStatusAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending Approval',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            default => ucfirst($this->status)
        };
    }

    public function getFormattedUrgencyAttribute()
    {
        return match($this->urgency_level) {
            self::URGENCY_LOW => 'Low Priority',
            self::URGENCY_MEDIUM => 'Medium Priority',
            self::URGENCY_HIGH => 'High Priority',
            self::URGENCY_CRITICAL => 'Critical',
            default => ucfirst($this->urgency_level)
        };
    }

    public function canBeApproved()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeRejected()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeAcknowledged()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function canBeCompleted()
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }
}
