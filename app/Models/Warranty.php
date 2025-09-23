<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warranty extends Model
{
    protected $fillable = [
        'asset_id',
        'manufacturer',
        'model',
        'warranty_expiry',
    ];

    protected $casts = [
        'warranty_expiry' => 'date',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Check if warranty is expired
     */
    public function isExpired(): bool
    {
        return $this->warranty_expiry < now()->toDateString();
    }

    /**
     * Check if warranty is expiring soon (within 30 days)
     */
    public function isExpiringSoon(): bool
    {
        return $this->warranty_expiry <= now()->addDays(30)->toDateString() && !$this->isExpired();
    }

    /**
     * Get warranty status
     */
    public function getStatus(): string
    {
        if ($this->isExpired()) {
            return 'expired';
        } elseif ($this->isExpiringSoon()) {
            return 'expiring_soon';
        }
        return 'active';
    }

    /**
     * Get warranty status badge class
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->getStatus()) {
            'expired' => 'bg-red-100 text-red-800',
            'expiring_soon' => 'bg-yellow-100 text-yellow-800',
            'active' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get warranty status label
     */
    public function getStatusLabel(): string
    {
        return match($this->getStatus()) {
            'expired' => 'Expired',
            'expiring_soon' => 'Expiring Soon',
            'active' => 'Active',
            default => 'Unknown'
        };
    }

    /**
     * Get days until expiry
     */
    public function getDaysUntilExpiry(): int
    {
        return now()->diffInDays($this->warranty_expiry, false);
    }
}
