<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetChange;
use App\Models\Category;
use App\Models\Location;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SemesterAssetController extends Controller
{
    /**
     * Display semester asset tracking dashboard
     */
    public function index(Request $request)
    {
        // Get current semester or auto-detect
        $currentSemester = Semester::current() ?? Semester::forDate(now());
        
        // Get selected semester from request or use current
        $selectedSemesterId = $request->get('semester_id', $currentSemester?->id);
        $selectedSemester = $selectedSemesterId ? Semester::find($selectedSemesterId) : $currentSemester;
        
        // Get asset statistics for the selected semester
        $assetStats = $selectedSemester ? $this->getAssetStatistics($selectedSemester) : $this->getEmptyStats();
        
        // Get available semesters for dropdown
        $availableSemesters = Semester::active()
            ->orderBy('academic_year', 'desc')
            ->orderBy('start_date', 'desc')
            ->get();
        
        // Get available academic years
        $availableYears = Semester::getAcademicYears();
        
        // Get categories for filtering
        $categories = Category::orderBy('name')->get();
        
        // Get locations for filtering
        $locations = Location::orderBy('building')->get();
        
        return view('semester-assets.index', compact(
            'assetStats',
            'selectedSemester',
            'availableSemesters',
            'availableYears',
            'categories',
            'locations',
            'currentSemester'
        ));
    }
    
    /**
     * Get detailed asset data for a specific category and action
     */
    public function getAssetDetails(Request $request)
    {
        $semesterId = $request->get('semester_id');
        $action = $request->get('action'); // registered, transferred, disposed, lost
        $categoryId = $request->get('category_id');
        $locationId = $request->get('location_id');
        
        $semester = Semester::find($semesterId);
        if (!$semester) {
            return response()->json(['error' => 'Semester not found'], 404);
        }
        
        $query = Asset::with(['category', 'location', 'createdBy', 'registeredSemester']);
        
        // Filter by action type
        switch ($action) {
            case 'registered':
                $query->where('registered_semester_id', $semester->id);
                break;
                
            case 'transferred':
                // Assets that had location changes during the semester
                $query->whereHas('changes', function($q) use ($semester) {
                    $q->where('semester_id', $semester->id)
                      ->whereIn('change_type', [AssetChange::TYPE_LOCATION_CHANGE, AssetChange::TYPE_TRANSFER])
                      ->where('field', 'location_id');
                });
                break;
                
            case 'disposed':
                $query->where('disposed_semester_id', $semester->id);
                break;
                
            case 'lost':
                $query->where('lost_semester_id', $semester->id);
                break;
        }
        
        // Apply filters
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        
        if ($locationId) {
            $query->where('location_id', $locationId);
        }
        
        $assets = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return response()->json([
            'assets' => $assets,
            'action' => $action,
            'semester' => $semester
        ]);
    }
    
    /**
     * Export semester asset report
     */
    public function exportReport(Request $request)
    {
        $semesterId = $request->get('semester_id');
        $semester = Semester::find($semesterId);
        
        if (!$semester) {
            return response()->json(['error' => 'Semester not found'], 404);
        }
        
        $assetStats = $this->getAssetStatistics($semester);
        
        // Generate CSV report
        $filename = "semester_asset_report_{$semester->academic_year}_{$semester->name}.csv";
        $filename = str_replace(' ', '_', $filename);
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($assetStats, $semester) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, ['Semester Asset Report']);
            fputcsv($file, ["Academic Year: {$semester->academic_year}", "Semester: {$semester->name}"]);
            fputcsv($file, ["Period: {$semester->start_date->format('M d, Y')} - {$semester->end_date->format('M d, Y')}"]);
            fputcsv($file, ['']);
            fputcsv($file, ['Category', 'Registered', 'Transferred', 'Disposed', 'Lost', 'Total']);
            
            // Data rows
            foreach ($assetStats['by_category'] as $category) {
                fputcsv($file, [
                    $category['name'],
                    $category['registered'],
                    $category['transferred'],
                    $category['disposed'],
                    $category['lost'],
                    $category['total']
                ]);
            }
            
            // Summary
            fputcsv($file, ['']);
            fputcsv($file, ['SUMMARY']);
            fputcsv($file, ['Total Registered', $assetStats['summary']['total_registered']]);
            fputcsv($file, ['Total Transferred', $assetStats['summary']['total_transferred']]);
            fputcsv($file, ['Total Disposed', $assetStats['summary']['total_disposed']]);
            fputcsv($file, ['Total Lost', $assetStats['summary']['total_lost']]);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Get comprehensive asset statistics for a semester
     */
    private function getAssetStatistics(Semester $semester)
    {
        // Get all categories
        $categories = Category::orderBy('name')->get();
        
        $categoryStats = [];
        $summary = [
            'total_registered' => 0,
            'total_transferred' => 0,
            'total_disposed' => 0,
            'total_lost' => 0
        ];
        
        foreach ($categories as $category) {
            // Assets registered during semester
            $registered = Asset::where('category_id', $category->id)
                ->where('registered_semester_id', $semester->id)
                ->count();
            
            // Assets transferred during semester (location changes)
            $transferred = AssetChange::whereHas('asset', function($query) use ($category) {
                    $query->where('category_id', $category->id);
                })
                ->where('semester_id', $semester->id)
                ->whereIn('change_type', [AssetChange::TYPE_LOCATION_CHANGE, AssetChange::TYPE_TRANSFER])
                ->where('field', 'location_id')
                ->distinct('asset_id')
                ->count('asset_id');
            
            // Assets disposed during semester
            $disposed = Asset::where('category_id', $category->id)
                ->where('disposed_semester_id', $semester->id)
                ->count();
            
            // Assets marked as lost during semester
            $lost = Asset::where('category_id', $category->id)
                ->where('lost_semester_id', $semester->id)
                ->count();
            
            $total = $registered + $transferred + $disposed + $lost;
            
            $categoryStats[] = [
                'id' => $category->id,
                'name' => $category->name,
                'registered' => $registered,
                'transferred' => $transferred,
                'disposed' => $disposed,
                'lost' => $lost,
                'total' => $total
            ];
            
            // Update summary
            $summary['total_registered'] += $registered;
            $summary['total_transferred'] += $transferred;
            $summary['total_disposed'] += $disposed;
            $summary['total_lost'] += $lost;
        }
        
        // Get monthly breakdown for charts
        $monthlyBreakdown = $this->getMonthlyBreakdown($semester);
        
        // Get top locations by activity
        $topLocations = $this->getTopLocationsByActivity($semester);
        
        return [
            'by_category' => $categoryStats,
            'summary' => $summary,
            'monthly_breakdown' => $monthlyBreakdown,
            'top_locations' => $topLocations
        ];
    }
    
    /**
     * Get empty statistics structure
     */
    private function getEmptyStats()
    {
        return [
            'by_category' => [],
            'summary' => [
                'total_registered' => 0,
                'total_transferred' => 0,
                'total_disposed' => 0,
                'total_lost' => 0
            ],
            'monthly_breakdown' => [],
            'top_locations' => []
        ];
    }
    
    /**
     * Get monthly breakdown of asset activities for a semester
     */
    private function getMonthlyBreakdown(Semester $semester)
    {
        $months = [];
        $current = $semester->start_date->copy();
        
        while ($current->lte($semester->end_date)) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();
            
            // Ensure we don't go beyond semester end date
            if ($monthEnd->gt($semester->end_date)) {
                $monthEnd = $semester->end_date->copy();
            }
            
            $registered = Asset::where('registered_semester_id', $semester->id)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();
            
            $transferred = AssetChange::where('semester_id', $semester->id)
                ->whereIn('change_type', [AssetChange::TYPE_LOCATION_CHANGE, AssetChange::TYPE_TRANSFER])
                ->where('field', 'location_id')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->distinct('asset_id')
                ->count('asset_id');
            
            $disposed = Asset::where('disposed_semester_id', $semester->id)
                ->whereBetween('updated_at', [$monthStart, $monthEnd])
                ->count();
                
            $lost = Asset::where('lost_semester_id', $semester->id)
                ->whereBetween('updated_at', [$monthStart, $monthEnd])
                ->count();
            
            $months[] = [
                'month' => $current->format('M Y'),
                'registered' => $registered,
                'transferred' => $transferred,
                'disposed' => $disposed,
                'lost' => $lost
            ];
            
            $current->addMonth();
        }
        
        return $months;
    }
    
    /**
     * Get top locations by asset activity for a semester
     */
    private function getTopLocationsByActivity(Semester $semester)
    {
        return Location::withCount([
            'assets as registered_count' => function($query) use ($semester) {
                $query->where('registered_semester_id', $semester->id);
            },
            'assets as disposed_count' => function($query) use ($semester) {
                $query->where('disposed_semester_id', $semester->id);
            },
            'assets as lost_count' => function($query) use ($semester) {
                $query->where('lost_semester_id', $semester->id);
            }
        ])
        ->having('registered_count', '>', 0)
        ->orHaving('disposed_count', '>', 0)
        ->orHaving('lost_count', '>', 0)
        ->orderByDesc('registered_count')
        ->take(10)
        ->get();
    }
}
