<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'id_number',
        'email',
        'password',
        'role',
        'last_login',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login' => 'datetime',
        ];
    }

    /**
     * Get the locations owned by this user
     */
    public function ownedLocations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'user_locations')
                    ->withPivot('assigned_at', 'assigned_by', 'notes')
                    ->withTimestamps();
    }

    /**
     * Get the user location assignments
     */
    public function locationAssignments(): HasMany
    {
        return $this->hasMany(UserLocation::class);
    }

    /**
     * Check if user owns a specific location
     */
    public function ownsLocation($locationId): bool
    {
        return $this->ownedLocations()->where('location_id', $locationId)->exists();
    }

    /**
     * Get all assets in locations owned by this user
     */
    public function ownedAssets()
    {
        $locationIds = $this->ownedLocations()->pluck('locations.id');
        return Asset::whereIn('location_id', $locationIds)->with(['location', 'category']);
    }

    /**
     * Check if user can submit maintenance request for a location
     */
    public function canSubmitMaintenanceRequestFor($locationId): bool
    {
        // Only regular users with location ownership can submit requests
        return $this->role === 'user' && $this->ownsLocation($locationId);
    }

    /**
     * Get locations this user can submit maintenance requests for
     */
    public function getMaintenanceRequestLocations()
    {
        if ($this->role !== 'user') {
            return collect();
        }
        
        return $this->ownedLocations;
    }
}
