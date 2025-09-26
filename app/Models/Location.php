<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Location extends Model
{
    protected $fillable = ['building', 'floor', 'room'];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    /**
     * Get the users who own this location
     */
    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_locations')
                    ->withPivot('assigned_at', 'assigned_by', 'notes')
                    ->withTimestamps();
    }

    /**
     * Get the user location assignments for this location
     */
    public function userAssignments(): HasMany
    {
        return $this->hasMany(UserLocation::class);
    }

    /**
     * Check if a specific user owns this location
     */
    public function isOwnedBy($userId): bool
    {
        return $this->owners()->where('user_id', $userId)->exists();
    }
}
