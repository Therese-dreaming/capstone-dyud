<?php

namespace App\Models;

use App\Traits\TracksAssetChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use TracksAssetChanges;
    
    protected $fillable = [
        'asset_code',
        'name',
        'category_id',
        'location_id',
        'original_location_id',
        'condition',
        'description',
        'purchase_cost',
        'purchase_date',
        'status'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_cost' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function originalLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'original_location_id');
    }

    public function warranty(): HasOne
    {
        return $this->hasOne(Warranty::class);
    }

    public function maintenanceHistory(): HasMany
    {
        return $this->hasMany(AssetMaintenanceHistory::class, 'asset_code', 'asset_code');
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }

    public function disposes(): HasMany
    {
        return $this->hasMany(Dispose::class);
    }

    public function changes(): HasMany
    {
        return $this->hasMany(AssetChange::class);
    }

    public function lostAssets(): HasMany
    {
        return $this->hasMany(LostAsset::class);
    }

    public function currentLostAsset()
    {
        return $this->lostAssets()
            ->whereIn('status', [LostAsset::STATUS_INVESTIGATING])
            ->latest()
            ->first();
    }
}
