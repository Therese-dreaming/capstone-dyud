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
        $user = auth()->user();
        
        // Return different views based on user role
        if ($user->role === 'user') {
            return $this->userDashboard($user);
        } elseif ($user->role === 'gsu') {
            return $this->gsuDashboard($user);
        } elseif ($user->role === 'superadmin') {
            return $this->superAdminDashboard($user);
        } else {
            return $this->adminDashboard($user);
        }
    }

    private function userDashboard($user)
    {
        // Breadcrumbs for navigation
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')]
        ];

        return view('dashboard.user-dashboard', compact(
            'breadcrumbs'
        ));
    }

    private function gsuDashboard($user)
    {
        // Get counts for dashboard stats
        $totalAssets = Asset::count();
        $availableAssets = Asset::where('status', 'Available')->count();
        $disposedAssets = Asset::where('status', 'Disposed')->count();
        $pendingMaintenances = Maintenance::whereIn('status', ['Scheduled', 'In Progress'])->count();
        $totalUsers = User::count();
        $totalCategories = Category::count();
        $totalLocations = Location::count();

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

        return view('dashboard.gsu-dashboard', compact(
            'totalAssets',
            'availableAssets',
            'disposedAssets',
            'pendingMaintenances',
            'totalUsers',
            'totalCategories',
            'totalLocations',
            'recentAssets',
            'categories',
            'locations'
        ));
    }

    private function superAdminDashboard($user)
    {
        // Get user counts for dashboard stats
        $totalUsers = User::count();

        // Get user counts by role
        $userCounts = [
            'superadmin' => User::where('role', 'superadmin')->count(),
            'gsu' => User::where('role', 'gsu')->count(),
            'admin' => User::where('role', 'admin')->count(),
            'user' => User::where('role', 'user')->count(),
        ];

        // Get recent users
        $recentUsers = User::orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.superadmin-dashboard', compact(
            'totalUsers',
            'userCounts',
            'recentUsers'
        ));
    }

    private function adminDashboard($user)
    {
        // Get counts for dashboard stats
        $totalAssets = Asset::count();
        $availableAssets = Asset::where('status', 'Available')->count();
        $inUseAssets = Asset::where('status', 'In Use')->count();
        $disposedAssets = Asset::where('status', 'Disposed')->count();
        $pendingMaintenances = Maintenance::whereIn('status', ['Scheduled', 'In Progress'])->count();
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
            'inUseAssets',
            'disposedAssets',
            'pendingMaintenances',
            'totalUsers',
            'recentAssets',
            'categories',
            'locations'
        ));
    }
}