<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'academic_year',
        'start_date',
        'end_date',
        'is_current',
        'is_active',
        'description'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'is_active' => 'boolean'
    ];

    /**
     * Get the current active semester
     */
    public static function current()
    {
        return static::where('is_current', true)->first();
    }

    /**
     * Get the semester for a given date
     */
    public static function forDate($date = null)
    {
        $date = $date ? Carbon::parse($date) : now();
        
        return static::where('start_date', '<=', $date->format('Y-m-d'))
                    ->where('end_date', '>=', $date->format('Y-m-d'))
                    ->where('is_active', true)
                    ->first();
    }

    /**
     * Get all semesters for a specific academic year
     */
    public static function forAcademicYear($academicYear)
    {
        return static::where('academic_year', $academicYear)
                    ->where('is_active', true)
                    ->orderBy('start_date')
                    ->get();
    }

    /**
     * Get available academic years
     */
    public static function getAcademicYears()
    {
        return static::where('is_active', true)
                    ->distinct()
                    ->orderBy('academic_year', 'desc')
                    ->pluck('academic_year');
    }

    /**
     * Set this semester as the current one
     */
    public function setCurrent()
    {
        return DB::transaction(function () {
            // First, unset all other current semesters
            static::where('is_current', true)->update(['is_current' => false]);
            
            // Then set this one as current
            $this->update(['is_current' => true]);
            
            return $this;
        });
    }

    /**
     * Check if this semester is currently active (within date range)
     */
    public function isWithinDateRange($date = null)
    {
        $date = $date ? Carbon::parse($date) : now();
        
        return $date->between($this->start_date, $this->end_date);
    }

    /**
     * Get the full semester display name
     */
    public function getFullNameAttribute()
    {
        return "{$this->academic_year} - {$this->name}";
    }

    /**
     * Get assets registered in this semester
     */
    public function registeredAssets(): HasMany
    {
        return $this->hasMany(Asset::class, 'registered_semester_id');
    }

    /**
     * Get asset changes that occurred in this semester
     */
    public function assetChanges(): HasMany
    {
        return $this->hasMany(AssetChange::class, 'semester_id');
    }

    /**
     * Scope for active semesters
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for current semester
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Get semester statistics
     */
    public function getStatistics()
    {
        $stats = [
            'registered_assets' => $this->registeredAssets()->count(),
            'transferred_assets' => $this->assetChanges()
                ->whereIn('change_type', [AssetChange::TYPE_LOCATION_CHANGE, AssetChange::TYPE_TRANSFER])
                ->where('field', 'location_id')
                ->distinct('asset_id')
                ->count('asset_id'),
            'disposed_assets' => Asset::where('status', 'Disposed')
                ->where('disposed_semester_id', $this->id)
                ->count(),
            'lost_assets' => Asset::where('status', 'Lost')
                ->where('lost_semester_id', $this->id)
                ->count()
        ];

        $stats['total_activity'] = array_sum($stats);
        
        return $stats;
    }

    /**
     * Auto-detect and set current semester based on today's date
     */
    public static function autoSetCurrent()
    {
        $currentSemester = static::forDate(now());
        
        if ($currentSemester) {
            $currentSemester->setCurrent();
            return $currentSemester;
        }
        
        return null;
    }

    /**
     * Create default semesters for an academic year
     */
    public static function createDefaultSemesters($academicYear, $startYear)
    {
        $semesters = [
            [
                'name' => '1st Semester',
                'start_date' => Carbon::create($startYear, 8, 1),
                'end_date' => Carbon::create($startYear, 12, 31),
            ],
            [
                'name' => '2nd Semester',
                'start_date' => Carbon::create($startYear + 1, 1, 1),
                'end_date' => Carbon::create($startYear + 1, 5, 31),
            ],
            [
                'name' => 'Summer',
                'start_date' => Carbon::create($startYear + 1, 6, 1),
                'end_date' => Carbon::create($startYear + 1, 7, 31),
            ]
        ];

        $createdSemesters = [];
        
        foreach ($semesters as $semesterData) {
            $createdSemesters[] = static::create([
                'name' => $semesterData['name'],
                'academic_year' => $academicYear,
                'start_date' => $semesterData['start_date'],
                'end_date' => $semesterData['end_date'],
                'is_current' => false,
                'is_active' => true,
                'description' => "Default {$semesterData['name']} for {$academicYear}"
            ]);
        }

        return $createdSemesters;
    }
}
