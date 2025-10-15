<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Services\DepreciationService;
use App\Exports\DepreciationExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DepreciationController extends Controller
{
    protected $depreciationService;

    public function __construct(DepreciationService $depreciationService)
    {
        $this->depreciationService = $depreciationService;
    }

    /**
     * Display depreciation report
     */
    public function index(Request $request)
    {
        $query = Asset::with(['category', 'location', 'warranty'])
            ->where('approval_status', Asset::APPROVAL_APPROVED)
            ->whereNotIn('status', [Asset::STATUS_DISPOSED, Asset::STATUS_LOST]);

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by location
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        // Filter by depreciation method
        if ($request->filled('depreciation_method')) {
            $query->where('depreciation_method', $request->depreciation_method);
        }

        // Filter by fully depreciated status
        if ($request->filled('fully_depreciated')) {
            // This requires calculation, so we'll filter after fetching
        }

        $assets = $query->orderBy('purchase_date', 'desc')->get();

        // Calculate depreciation for each asset
        $assetsWithDepreciation = $assets->map(function ($asset) {
            $depreciation = $this->depreciationService->calculateDepreciation($asset);
            $asset->depreciation_data = $depreciation;
            return $asset;
        });

        // Filter by fully depreciated if requested
        if ($request->filled('fully_depreciated')) {
            $assetsWithDepreciation = $assetsWithDepreciation->filter(function ($asset) use ($request) {
                return $asset->depreciation_data['is_fully_depreciated'] == ($request->fully_depreciated == '1');
            });
        }

        // Get summary
        $summary = $this->depreciationService->getDepreciationSummary($assets);

        // Get filter options
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('building')->orderBy('floor')->orderBy('room')->get();

        return view('depreciation.index', compact(
            'assetsWithDepreciation',
            'summary',
            'categories',
            'locations'
        ));
    }

    /**
     * Show depreciation details for a specific asset
     */
    public function show(Asset $asset)
    {
        $asset->load(['category', 'location', 'warranty', 'changes']);

        $depreciation = $this->depreciationService->calculateDepreciation($asset);
        $schedule = $this->depreciationService->calculateDepreciationSchedule($asset);

        return view('depreciation.show', compact('asset', 'depreciation', 'schedule'));
    }

    /**
     * Show form to edit depreciation settings for an asset
     */
    public function edit(Asset $asset)
    {
        return view('depreciation.edit', compact('asset'));
    }

    /**
     * Update depreciation settings for an asset
     */
    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'depreciation_method' => 'required|in:straight_line,declining_balance,sum_of_years_digits',
            'useful_life_years' => 'required|numeric|min:0.1|max:100',
            'salvage_value' => 'required|numeric|min:0',
            'declining_balance_rate' => 'nullable|numeric|min:1|max:5',
            'depreciation_start_date' => 'nullable|date',
        ]);

        // Ensure salvage value is not greater than purchase cost
        if ($validated['salvage_value'] > $asset->purchase_cost) {
            return back()->withErrors([
                'salvage_value' => 'Salvage value cannot exceed purchase cost.'
            ])->withInput();
        }

        // Set default declining balance rate if not provided
        if (!isset($validated['declining_balance_rate'])) {
            $validated['declining_balance_rate'] = 2; // Double declining by default
        }

        $asset->update($validated);

        return redirect()
            ->route('depreciation.show', $asset)
            ->with('success', 'Depreciation settings updated successfully.');
    }

    /**
     * Export depreciation report to Excel
     */
    public function export(Request $request)
    {
        $query = Asset::with(['category', 'location'])
            ->where('approval_status', Asset::APPROVAL_APPROVED)
            ->whereNotIn('status', [Asset::STATUS_DISPOSED, Asset::STATUS_LOST]);

        // Apply same filters as index
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('depreciation_method')) {
            $query->where('depreciation_method', $request->depreciation_method);
        }

        $assets = $query->orderBy('purchase_date', 'desc')->get();

        // Calculate depreciation for each asset
        $assetsWithDepreciation = $assets->map(function($asset) {
            $asset->depreciation_data = $this->depreciationService->calculateDepreciation($asset);
            return $asset;
        });

        // Get summary
        $summary = $this->depreciationService->getDepreciationSummary($assets);

        return Excel::download(new DepreciationExport($assetsWithDepreciation, $summary), 'depreciation-report.xlsx');
    }

    /**
     * Get depreciation data by category (for charts)
     */
    public function byCategory()
    {
        $categories = Category::with(['assets' => function($query) {
            $query->where('approval_status', Asset::APPROVAL_APPROVED)
                  ->whereNotIn('status', [Asset::STATUS_DISPOSED, Asset::STATUS_LOST]);
        }])->get();

        $data = $categories->map(function($category) {
            $summary = $this->depreciationService->getDepreciationSummary($category->assets);
            return [
                'category' => $category->name,
                'total_purchase_cost' => $summary['total_purchase_cost'],
                'total_book_value' => $summary['total_current_book_value'],
                'total_depreciation' => $summary['total_accumulated_depreciation'],
            ];
        })->filter(function($item) {
            return $item['total_purchase_cost'] > 0;
        })->values();

        return response()->json($data);
    }

    /**
     * Get depreciation trend data (for charts)
     */
    public function trend()
    {
        $assets = Asset::where('approval_status', Asset::APPROVAL_APPROVED)
            ->whereNotIn('status', [Asset::STATUS_DISPOSED, Asset::STATUS_LOST])
            ->get();

        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i)->endOfMonth();
            $monthLabel = $date->format('M Y');
            
            $summary = $this->depreciationService->getDepreciationSummary($assets);
            
            $months[] = [
                'month' => $monthLabel,
                'book_value' => $summary['total_current_book_value'],
                'accumulated_depreciation' => $summary['total_accumulated_depreciation'],
            ];
        }

        return response()->json($months);
    }
}
