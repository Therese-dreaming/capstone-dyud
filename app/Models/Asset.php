<?php

namespace App\Models;

use App\Traits\TracksAssetChanges;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use TracksAssetChanges;
    
    // Status constants
    const STATUS_AVAILABLE = 'Available';
    const STATUS_LOST = 'Lost';
    const STATUS_DISPOSED = 'Disposed';
    const STATUS_FOR_REPAIR = 'For Repair';
    const STATUS_FOR_MAINTENANCE = 'For Maintenance';
    const STATUS_UNVERIFIED = 'Unverified';
    
    // Approval status constants
    const APPROVAL_PENDING = 'pending';
    const APPROVAL_APPROVED = 'approved';
    const APPROVAL_REJECTED = 'rejected';
    
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
        'status',
        'approval_status',
        'rejection_reason',
        'approved_at',
        'approved_by',
        'created_by',
        'registered_semester_id',
        'disposed_semester_id',
        'lost_semester_id',
        'depreciation_method',
        'useful_life_years',
        'salvage_value',
        'declining_balance_rate',
        'depreciation_start_date'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_cost' => 'decimal:2',
        'approved_at' => 'datetime',
        'useful_life_years' => 'decimal:2',
        'salvage_value' => 'decimal:2',
        'declining_balance_rate' => 'decimal:2',
        'depreciation_start_date' => 'date',
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

    public function legitimateMaintenanceHistory(): HasMany
    {
        return $this->hasMany(AssetMaintenanceHistory::class, 'asset_code', 'asset_code')
            ->whereNotNull('maintenance_checklist_id')
            ->whereNotNull('scanned_at')
            ->whereNotNull('scanned_by')
            ->whereHas('maintenanceChecklist', function($query) {
                $query->whereNotNull('id');
            })
            ->whereIn('end_status', ['OK', 'FOR REPAIR', 'FOR MAINTENANCE', 'FOR REPLACEMENT', 'UNVERIFIED'])
            ->where(function($query) {
                $query->whereNull('notes')
                      ->orWhere(function($subQuery) {
                          $subQuery->where('notes', 'NOT LIKE', '%transfer%')
                                   ->where('notes', 'NOT LIKE', '%Transfer%')
                                   ->where('notes', 'NOT LIKE', '%TRANSFER%')
                                   ->where('notes', 'NOT LIKE', '%moved%')
                                   ->where('notes', 'NOT LIKE', '%location%');
                      });
            });
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

    // Semester relationships
    public function registeredSemester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'registered_semester_id');
    }

    public function disposedSemester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'disposed_semester_id');
    }

    public function lostSemester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'lost_semester_id');
    }

    public function lostAssets(): HasMany
    {
        return $this->hasMany(LostAsset::class);
    }

    public function repairResolutions(): HasMany
    {
        return $this->hasMany(AssetRepairResolution::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function currentLostAsset()
    {
        return $this->lostAssets()
            ->whereIn('status', [LostAsset::STATUS_INVESTIGATING])
            ->latest()
            ->first();
    }

    // Status helper methods
    public function isAvailable()
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    public function isLost()
    {
        return $this->status === self::STATUS_LOST;
    }

    public function isDisposed()
    {
        return $this->status === self::STATUS_DISPOSED;
    }

    public function isForRepair()
    {
        return $this->status === self::STATUS_FOR_REPAIR;
    }

    public function isForMaintenance()
    {
        return $this->status === self::STATUS_FOR_MAINTENANCE;
    }

    public function isUnverified()
    {
        return $this->status === self::STATUS_UNVERIFIED;
    }

    // Approval status helper methods
    public function isPending()
    {
        return $this->approval_status === self::APPROVAL_PENDING;
    }

    public function isApproved()
    {
        return $this->approval_status === self::APPROVAL_APPROVED;
    }

    public function isRejected()
    {
        return $this->approval_status === self::APPROVAL_REJECTED;
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            self::STATUS_AVAILABLE => 'bg-green-100 text-green-800',
            self::STATUS_LOST => 'bg-red-100 text-red-800',
            self::STATUS_DISPOSED => 'bg-gray-100 text-gray-800',
            self::STATUS_FOR_REPAIR => 'bg-yellow-100 text-yellow-800',
            self::STATUS_FOR_MAINTENANCE => 'bg-blue-100 text-blue-800',
            self::STATUS_UNVERIFIED => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getStatusLabel()
    {
        return match($this->status) {
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_LOST => 'Lost',
            self::STATUS_DISPOSED => 'Disposed',
            self::STATUS_FOR_REPAIR => 'For Repair',
            self::STATUS_FOR_MAINTENANCE => 'For Maintenance',
            self::STATUS_UNVERIFIED => 'Unverified',
            default => $this->status
        };
    }

    public function getApprovalStatusBadgeClass()
    {
        return match($this->approval_status) {
            self::APPROVAL_PENDING => 'bg-yellow-100 text-yellow-800',
            self::APPROVAL_APPROVED => 'bg-green-100 text-green-800',
            self::APPROVAL_REJECTED => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getApprovalStatusLabel()
    {
        return match($this->approval_status) {
            self::APPROVAL_PENDING => 'Pending Approval',
            self::APPROVAL_APPROVED => 'Approved',
            self::APPROVAL_REJECTED => 'Rejected',
            default => $this->approval_status
        };
    }

    // Repair resolution helper methods
    public function needsRepairResolution()
    {
        return in_array($this->status, [
            self::STATUS_FOR_REPAIR,
            self::STATUS_FOR_MAINTENANCE,
            'For Replacement' // Legacy status
        ]);
    }

    public function hasUnresolvedRepair()
    {
        return $this->needsRepairResolution() && 
               !$this->repairResolutions()
                    ->where('resolution_date', '>=', now()->subDays(30)) // Within last 30 days
                    ->exists();
    }

    public function getRepairResolutionDays()
    {
        if (!$this->needsRepairResolution()) {
            return 0;
        }

        $lastMaintenance = $this->maintenanceHistory()
            ->whereIn('end_status', ['FOR REPAIR', 'FOR MAINTENANCE', 'FOR REPLACEMENT'])
            ->latest('scanned_at')
            ->first();

        if (!$lastMaintenance) {
            return 0;
        }

        return $lastMaintenance->scanned_at->diffInDays(now());
    }

    // Depreciation helper methods
    
    /**
     * Get depreciation calculation for this asset
     */
    public function getDepreciation(?Carbon $asOfDate = null): array
    {
        $service = app(\App\Services\DepreciationService::class);
        return $service->calculateDepreciation($this, $asOfDate);
    }
    
    /**
     * Get current book value
     */
    public function getCurrentBookValue(): float
    {
        $depreciation = $this->getDepreciation();
        return $depreciation['current_book_value'];
    }
    
    /**
     * Get accumulated depreciation
     */
    public function getAccumulatedDepreciation(): float
    {
        $depreciation = $this->getDepreciation();
        return $depreciation['accumulated_depreciation'];
    }
    
    /**
     * Check if asset is fully depreciated
     */
    public function isFullyDepreciated(): bool
    {
        $depreciation = $this->getDepreciation();
        return $depreciation['is_fully_depreciated'];
    }
    
    /**
     * Get depreciation schedule
     */
    public function getDepreciationSchedule(): array
    {
        $service = app(\App\Services\DepreciationService::class);
        return $service->calculateDepreciationSchedule($this);
    }
    
    /**
     * Get depreciation method label
     */
    public function getDepreciationMethodLabel(): string
    {
        return match($this->depreciation_method) {
            'straight_line' => 'Straight-Line',
            'declining_balance' => 'Declining Balance',
            'sum_of_years_digits' => 'Sum of Years Digits',
            default => ucwords(str_replace('_', ' ', $this->depreciation_method))
        };
    }
}
