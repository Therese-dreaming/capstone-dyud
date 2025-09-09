<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LostAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'reported_by',
        'reported_date',
        'last_known_location',
        'investigation_notes',
        'status',
        'found_date',
        'found_notes'
    ];

    protected $casts = [
        'reported_date' => 'date',
        'found_date' => 'date',
    ];

    // Status constants
    const STATUS_LOST = 'lost';
    const STATUS_RESOLVED = 'resolved';

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }


    public function getStatusBadgeClass()
    {
        switch ($this->status) {
            case self::STATUS_LOST:
                return 'bg-red-100 text-red-800';
            case self::STATUS_RESOLVED:
                return 'bg-green-100 text-green-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    public function getStatusLabel()
    {
        switch ($this->status) {
            case self::STATUS_LOST:
                return 'Lost';
            case self::STATUS_RESOLVED:
                return 'Resolved';
            default:
                return 'Unknown';
        }
    }

    public function isLost()
    {
        return $this->status === self::STATUS_LOST;
    }

    public function isResolved()
    {
        return $this->status === self::STATUS_RESOLVED;
    }
}
