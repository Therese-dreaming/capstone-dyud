<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\Maintenance;
use App\Models\User;
use App\Models\Borrowing;
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
        } else {
            return $this->adminDashboard($user);
        }
    }

    private function userDashboard($user)
    {
        // Get user-specific borrowing statistics
        $currentBorrowings = Borrowing::where('borrower_id_number', $user->id_number)
            ->whereIn('status', ['active', 'overdue'])
            ->count();
        
        $returnedItems = Borrowing::where('borrower_id_number', $user->id_number)
            ->where('status', 'returned')
            ->count();
        
        $overdueItems = Borrowing::where('borrower_id_number', $user->id_number)
            ->where('status', 'overdue')
            ->count();
        
        // Get recent borrowings for the user
        $recentBorrowings = Borrowing::where('borrower_id_number', $user->id_number)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Breadcrumbs for navigation
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')]
        ];

        return view('dashboard.user-dashboard', compact(
            'currentBorrowings',
            'returnedItems',
            'overdueItems',
            'recentBorrowings',
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