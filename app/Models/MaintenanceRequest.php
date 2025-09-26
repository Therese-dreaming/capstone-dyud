<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Asset;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_id',
        'location_id',
        'semester_id',
        'requested_asset_codes',
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
    public function semester(): BelongsTo { return $this->belongsTo(Semester::class); }
    public function checklist(): BelongsTo { return $this->belongsTo(MaintenanceChecklist::class, 'maintenance_checklist_id'); }
    
    // Alias methods for view compatibility
    public function approvedBy(): BelongsTo { return $this->approver(); }
    public function rejectedBy(): BelongsTo { return $this->rejector(); }
    public function acknowledgedBy(): BelongsTo { return $this->acknowledger(); }
    
    // Helper method to get requested asset codes
    public function getRequestedAssetCodes(): array
    {
        return $this->requested_asset_codes ? json_decode($this->requested_asset_codes, true) : [];
    }
    
    // Helper method to check if this is a specific assets request
    public function isSpecificAssetsRequest(): bool
    {
        return empty($this->location_id) && !empty($this->getRequestedAssetCodes());
    }
    
    // Helper method to get the assets for this request
    public function getRequestedAssets()
    {
        if (!$this->isSpecificAssetsRequest()) {
            return collect();
        }
        
        $assetCodes = $this->getRequestedAssetCodes();
        return Asset::whereIn('asset_code', $assetCodes)->with('location')->get();
    }
    
    // Helper method to get unique locations from requested assets
    public function getAssetLocations()
    {
        return $this->getRequestedAssets()
            ->filter(function($asset) {
                return $asset->location !== null;
            })
            ->pluck('location')
            ->unique('id');
    }
}


