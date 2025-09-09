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
        'last_borrower_id',
        'last_seen_date',
        'reported_date',
        'description',
        'last_known_location',
        'investigation_notes',
        'status',
        'found_date',
        'found_location',
        'found_notes',
    ];

    protected $casts = [
        'last_seen_date' => 'date',
        'reported_date' => 'date',
        'found_date' => 'date',
    ];

    // Status constants aligned with migration
    const STATUS_INVESTIGATING = 'investigating';
    const STATUS_FOUND = 'found';
    const STATUS_PERMANENTLY_LOST = 'permanently_lost';
    const STATUS_LOST = 'lost';

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
            case self::STATUS_INVESTIGATING:
                return 'bg-yellow-100 text-yellow-800';
            case self::STATUS_FOUND:
                return 'bg-green-100 text-green-800';
            case self::STATUS_PERMANENTLY_LOST:
                return 'bg-red-100 text-red-800';
            case self::STATUS_LOST:
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    public function getStatusLabel()
    {
        switch ($this->status) {
            case self::STATUS_INVESTIGATING:
                return 'Under Investigation';
            case self::STATUS_FOUND:
                return 'Found';
            case self::STATUS_PERMANENTLY_LOST:
                return 'Permanently Lost';
            case self::STATUS_LOST:
                return 'Lost';
            default:
                return 'Unknown';
        }
    }

    public function isInvestigating()
    {
        return $this->status === self::STATUS_INVESTIGATING;
    }

    public function isFound()
    {
        return $this->status === self::STATUS_FOUND;
    }

    public function isPermanentlyLost()
    {
        return $this->status === self::STATUS_PERMANENTLY_LOST;
    }

    public function isLost()
    {
        return $this->status === self::STATUS_LOST;
    }
}
