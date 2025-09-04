<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Asset;
use Carbon\Carbon;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::orderBy('building')->orderBy('floor')->orderBy('room')->get();
        return view('locations.index', compact('locations'));
    }

    public function create()
    {
        return view('locations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'building' => 'required|string|max:255',
            'floor' => 'required|string|max:255',
            'room' => 'required|string|max:255',
        ]);
        
        try {
            Location::create($validated);
            return redirect()->route('locations.index')->with('success', 'Location added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to add location.');
        }
    }

    public function show(Location $location)
    {
        // Get assets in this location
        $assets = $location->assets;
        
        return view('locations.show', compact('location', 'assets'));
    }

    public function dateRangeView(Request $request, Location $location)
    {
        // Get date range from request or use default (last 30 days)
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        // Convert to Carbon objects
        $startDateCarbon = Carbon::parse($startDate);
        $endDateCarbon = Carbon::parse($endDate);
        
        // Get assets that were present in this location during the date range
        $assets = Asset::where(function($query) use ($location, $startDateCarbon, $endDateCarbon) {
            // Assets purchased during this date range AND currently in this location
            $query->where(function($purchaseQuery) use ($location, $startDateCarbon, $endDateCarbon) {
                $purchaseQuery->whereBetween('purchase_date', [$startDateCarbon->format('Y-m-d'), $endDateCarbon->format('Y-m-d')])
                             ->where('location_id', $location->id);
            })
            // OR assets that had location changes involving this location during the date range
            ->orWhereHas('changes', function($changeQuery) use ($location, $startDateCarbon, $endDateCarbon) {
                $changeQuery->whereBetween('created_at', [$startDateCarbon->toDateTimeString(), $endDateCarbon->toDateTimeString()])
                           ->where('change_type', 'location_change')
                           ->where(function($locQuery) use ($location) {
                               $locQuery->where('previous_value', 'like', '%' . $location->building . '%')
                                       ->orWhere('new_value', 'like', '%' . $location->building . '%');
                           });
            })
            // OR assets that had status changes (lost, disposed) while in this location during the date range
            ->orWhereHas('changes', function($changeQuery) use ($location, $startDateCarbon, $endDateCarbon) {
                $changeQuery->whereBetween('created_at', [$startDateCarbon->toDateTimeString(), $endDateCarbon->toDateTimeString()])
                           ->whereIn('change_type', ['status_change', 'disposed'])
                           ->whereHas('asset', function($assetQuery) use ($location) {
                               $assetQuery->where('location_id', $location->id);
                           });
            });
        })
        ->with(['category', 'location', 'changes' => function($query) use ($startDateCarbon, $endDateCarbon) {
            $query->whereBetween('created_at', [$startDateCarbon->toDateTimeString(), $endDateCarbon->toDateTimeString()]);
        }])
        ->get();
        
        // Debug information
        \Log::info('Date Range View Debug', [
            'location_id' => $location->id,
            'location_name' => $location->building,
            'start_date' => $startDateCarbon->format('Y-m-d'),
            'end_date' => $endDateCarbon->format('Y-m-d'),
            'total_assets' => $assets->count(),
            'assets_purchased_in_period' => $assets->filter(function($asset) use ($startDateCarbon, $endDateCarbon) {
                return $asset->purchase_date >= $startDateCarbon && $asset->purchase_date <= $endDateCarbon;
            })->count(),
            'assets_with_location_changes' => $assets->filter(function($asset) use ($startDateCarbon, $endDateCarbon) {
                return $asset->changes->where('change_type', 'location_change')->count() > 0;
            })->count(),
            'assets_with_status_changes' => $assets->filter(function($asset) use ($startDateCarbon, $endDateCarbon) {
                return $asset->changes->whereIn('change_type', ['status_change', 'disposed'])->count() > 0;
            })->count(),
        ]);
        
        return view('locations.date-range-view', compact(
            'location',
            'assets',
            'startDate',
            'endDate',
            'startDateCarbon',
            'endDateCarbon'
        ));
    }

    public function edit(Location $location)
    {
        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'building' => 'required|string|max:255',
            'floor' => 'required|string|max:255',
            'room' => 'required|string|max:255',
        ]);
        
        try {
            $location->update($validated);
            return redirect()->route('locations.index')->with('success', 'Location updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to update location.');
        }
    }

    public function destroy(Location $location)
    {
        try {
            // Check if location has assets
            if ($location->assets()->count() > 0) {
                return redirect()->route('locations.index')
                    ->with('error', 'Cannot delete location. It has assets assigned to it.');
            }
            
            $location->delete();
            return redirect()->route('locations.index')
                ->with('success', 'Location deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('locations.index')
                ->with('error', 'Failed to delete location.');
        }
    }
}
