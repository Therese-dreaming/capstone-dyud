<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'asset_id',
        'location_id',
        'custom_location',
        'borrower_name',
        'borrower_id_number',
        'purpose',
        'request_date',
        'due_date',
        'return_date',
        'status',
        'approved_by',
        'approved_at',
        'notes'
    ];

    protected $casts = [
        'request_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'approved_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_RETURNED = 'returned';
    const STATUS_OVERDUE = 'overdue';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function isOverdue()
    {
        if ($this->status === self::STATUS_RETURNED || $this->status === self::STATUS_REJECTED) {
            return false;
        }
        
        return now()->greaterThan($this->due_date);
    }

    public function getOverdueText()
    {
        if (!$this->isOverdue()) {
            return null;
        }

        $diffInMinutes = $this->due_date->diffInMinutes(now());
        
        // Additional safety check to ensure we have a positive number
        if ($diffInMinutes <= 0) {
            return null;
        }
        
        if ($diffInMinutes < 60) {
            return "Overdue by {$diffInMinutes} minute" . ($diffInMinutes > 1 ? 's' : '');
        } elseif ($diffInMinutes < 1440) { // Less than 24 hours
            $hours = floor($diffInMinutes / 60);
            return "Overdue by {$hours} hour" . ($hours > 1 ? 's' : '');
        } else {
            $days = $this->due_date->diffInDays(now());
            return "Overdue by {$days} day" . ($days > 1 ? 's' : '');
        }
    }

    public function getDueInText()
    {
        if ($this->isOverdue()) {
            return null;
        }

        $diffInMinutes = now()->diffInMinutes($this->due_date, false);
        
        if ($diffInMinutes < 60) {
            return "Due in {$diffInMinutes} minute" . ($diffInMinutes > 1 ? 's' : '');
        } elseif ($diffInMinutes < 1440) { // Less than 24 hours
            $hours = floor($diffInMinutes / 60);
            return "Due in {$hours} hour" . ($hours > 1 ? 's' : '');
        } else {
            $days = now()->diffInDays($this->due_date, false);
            return "Due in {$days} day" . ($days > 1 ? 's' : '');
        }
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isReturned()
    {
        return $this->status === self::STATUS_RETURNED;
    }

    public function getStatusBadgeClass()
    {
        switch ($this->status) {
            case self::STATUS_PENDING:
                return 'bg-yellow-100 text-yellow-800';
            case self::STATUS_APPROVED:
                return 'bg-green-100 text-green-800';
            case self::STATUS_REJECTED:
                return 'bg-red-100 text-red-800';
            case self::STATUS_RETURNED:
                return 'bg-blue-100 text-blue-800';
            case self::STATUS_OVERDUE:
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }
}
