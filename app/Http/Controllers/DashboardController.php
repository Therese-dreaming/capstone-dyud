<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\Maintenance;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get counts for dashboard stats
        $totalAssets = Asset::count();
        $availableAssets = Asset::where('status', 'Available')->count();
        $disposedAssets = Asset::where('status', 'Disposed')->count();
        $pendingMaintenances = Maintenance::where('status', 'Pending')->count();
        $totalUsers = User::count();

        // Get recent assets
        $recentAssets = Asset::with(['category', 'location'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get categories with asset counts
        $categories = Category::withCount('assets')
            ->orderBy('assets_count', 'desc')
            ->take(5)
            ->get();

        // Get locations with asset counts
        $locations = Location::withCount('assets')
            ->orderBy('assets_count', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.dashboard', compact(
            'totalAssets',
            'availableAssets',
            'disposedAssets',
            'pendingMaintenances',
            'totalUsers',
            'recentAssets',
            'categories',
            'locations'
        ));
    }
}