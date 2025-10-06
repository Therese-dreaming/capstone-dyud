<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\Maintenance;
use App\Models\User;
use App\Models\MaintenanceRequest;
use App\Models\RepairRequest;
use App\Models\Notification;
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
        } elseif ($user->role === 'purchasing') {
            return $this->purchasingDashboard($user);
        } else {
            return $this->adminDashboard($user);
        }
    }

    private function userDashboard($user)
    {
        // Get user's assigned locations
        $assignedLocations = $user->ownedLocations()->with('assets')->get();
        
        // Ensure assigned_at is properly cast to Carbon for each location
        $assignedLocations->each(function ($location) {
            if (is_string($location->pivot->assigned_at)) {
                $location->pivot->assigned_at = \Carbon\Carbon::parse($location->pivot->assigned_at);
            }
        });
        $totalLocations = $assignedLocations->count();
        
        // Get assets in user's locations
        $ownedAssets = $user->ownedAssets()->get();
        $totalAssets = $ownedAssets->count();
        $availableAssets = $ownedAssets->where('status', 'Available')->count();
        $inUseAssets = $ownedAssets->where('status', 'In Use')->count();
        $maintenanceAssets = $ownedAssets->where('status', 'Under Maintenance')->count();
        
        // Get user's maintenance requests
        $maintenanceRequests = MaintenanceRequest::where('requester_id', $user->id)
            ->with(['location'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $totalRequests = $maintenanceRequests->count();
        $pendingRequests = $maintenanceRequests->where('status', 'pending')->count();
        $approvedRequests = $maintenanceRequests->where('status', 'approved')->count();
        $completedRequests = $maintenanceRequests->where('status', 'completed')->count();
        
        // Get recent maintenance requests (last 5)
        $recentRequests = $maintenanceRequests->take(5);
        
        // Get user's repair requests
        $repairRequests = \App\Models\RepairRequest::where('requester_id', $user->id)
            ->with(['asset'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $totalRepairRequests = $repairRequests->count();
        $pendingRepairRequests = $repairRequests->where('status', 'pending')->count();
        
        // Get user's notifications
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        $unreadNotifications = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
        
        // Get asset status breakdown for charts
        $assetStatusData = [
            'Available' => $availableAssets,
            'In Use' => $inUseAssets,
            'Under Maintenance' => $maintenanceAssets,
        ];
        
        // Get maintenance request status breakdown
        $requestStatusData = [
            'Pending' => $pendingRequests,
            'Approved' => $approvedRequests,
            'Completed' => $completedRequests,
        ];
        
        // Get assets by category in user's locations
        $assetsByCategory = $ownedAssets->groupBy('category.name')->map(function ($assets) {
            return $assets->count();
        });
        
        // Get recent asset activity (last 10 assets created/updated in user's locations)
        $recentAssets = $ownedAssets->sortByDesc('updated_at')->take(10);

        return view('dashboard.user-dashboard', compact(
            'totalLocations',
            'totalAssets',
            'availableAssets',
            'inUseAssets',
            'maintenanceAssets',
            'totalRequests',
            'pendingRequests',
            'approvedRequests',
            'completedRequests',
            'recentRequests',
            'repairRequests',
            'totalRepairRequests',
            'pendingRepairRequests',
            'notifications',
            'unreadNotifications',
            'assetStatusData',
            'requestStatusData',
            'assetsByCategory',
            'recentAssets',
            'assignedLocations'
        ));
    }

    private function gsuDashboard($user)
    {
        // Get comprehensive asset statistics
        $totalAssets = Asset::count();
        
        // Pending deployment = assets without location assigned (not deployed yet)
        $pendingDeploymentAssets = Asset::whereNull('location_id')
            ->where('status', '!=', 'Disposed')
            ->where('status', '!=', 'Lost')
            ->count();
        
        // Deployed assets = all assets that have been assigned to a location
        $deployedAssets = Asset::whereNotNull('location_id')
            ->where('status', '!=', 'Disposed')
            ->where('status', '!=', 'Lost')
            ->count();
        
        $maintenanceAssets = Asset::where('status', 'Under Maintenance')->count();
        $disposedAssets = Asset::where('status', 'Disposed')->count();
        $lostAssets = Asset::where('status', 'Lost')->count();
        
        // Get maintenance statistics
        $totalMaintenanceRequests = MaintenanceRequest::count();
        $pendingMaintenanceRequests = MaintenanceRequest::where('status', 'pending')->count();
        $approvedMaintenanceRequests = MaintenanceRequest::where('status', 'approved')->count();
        $completedMaintenanceRequests = MaintenanceRequest::where('status', 'completed')->count();
        
        // Get user statistics
        $totalUsers = User::count();
        $adminUsers = User::where('role', 'admin')->count();
        $gsuUsers = User::where('role', 'gsu')->count();
        $regularUsers = User::where('role', 'user')->count();
        $purchasingUsers = User::where('role', 'purchasing')->count();
        
        // System statistics
        $totalCategories = Category::count();
        $totalLocations = Location::count();
        
        // Get recent assets (last 10)
        $recentAssets = Asset::with(['category', 'location', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Get categories with asset counts
        $categories = Category::withCount('assets')
            ->orderBy('assets_count', 'desc')
            ->get();

        // Get locations with asset counts
        $locations = Location::withCount('assets')
            ->orderBy('assets_count', 'desc')
            ->take(10)
            ->get();
            
        // Get recent maintenance requests
        $recentMaintenanceRequests = MaintenanceRequest::with(['requester', 'location'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Get notifications for GSU
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        $unreadNotifications = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        // Prepare chart data
        $assetStatusData = [
            'Pending Deployment' => $pendingDeploymentAssets,
            'Deployed Assets' => $deployedAssets,
            'Maintenance' => $maintenanceAssets,
            'Lost' => $lostAssets,
            'Disposed' => $disposedAssets,
        ];
        
        $maintenanceStatusData = [
            'Pending' => $pendingMaintenanceRequests,
            'Approved' => $approvedMaintenanceRequests,
            'Completed' => $completedMaintenanceRequests,
        ];
        
        $userRoleData = [
            'Admin' => $adminUsers,
            'GSU' => $gsuUsers,
            'User' => $regularUsers,
            'Purchasing' => $purchasingUsers,
        ];
        
        // Get assets by category for chart
        $assetsByCategory = $categories->pluck('assets_count', 'name')->toArray();

        return view('dashboard.gsu-dashboard', compact(
            'totalAssets',
            'deployedAssets',
            'maintenanceAssets',
            'disposedAssets',
            'lostAssets',
            'pendingDeploymentAssets',
            'totalMaintenanceRequests',
            'pendingMaintenanceRequests',
            'approvedMaintenanceRequests',
            'completedMaintenanceRequests',
            'totalUsers',
            'adminUsers',
            'gsuUsers',
            'regularUsers',
            'purchasingUsers',
            'totalCategories',
            'totalLocations',
            'recentAssets',
            'categories',
            'locations',
            'recentMaintenanceRequests',
            'notifications',
            'unreadNotifications',
            'assetStatusData',
            'maintenanceStatusData',
            'userRoleData',
            'assetsByCategory'
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
            'purchasing' => User::where('role', 'purchasing')->count(),
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

    private function purchasingDashboard($user)
    {
        // Get asset counts created by this purchasing user
        $totalAssets = Asset::where('created_by', $user->id)->count();
        $pendingAssets = Asset::where('created_by', $user->id)
            ->where('approval_status', Asset::APPROVAL_PENDING)
            ->count();
        $approvedAssets = Asset::where('created_by', $user->id)
            ->where('approval_status', Asset::APPROVAL_APPROVED)
            ->count();
        $rejectedAssets = Asset::where('created_by', $user->id)
            ->where('approval_status', Asset::APPROVAL_REJECTED)
            ->count();

        // Get recent assets created by this user
        $recentAssets = Asset::where('created_by', $user->id)
            ->with(['category', 'location'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get asset breakdown by category for this user
        $assetsByCategory = Asset::where('created_by', $user->id)
            ->join('categories', 'assets.category_id', '=', 'categories.id')
            ->selectRaw('categories.name, count(*) as total')
            ->groupBy('categories.name')
            ->get();

        return view('dashboard.purchasing-dashboard', compact(
            'totalAssets',
            'pendingAssets',
            'approvedAssets',
            'rejectedAssets',
            'recentAssets',
            'assetsByCategory'
        ));
    }

    private function adminDashboard($user)
    {
        // Get comprehensive asset statistics
        $totalAssets = Asset::count();
        $availableAssets = Asset::where('status', 'Available')->count();
        $inUseAssets = Asset::where('status', 'In Use')->count();
        $maintenanceAssets = Asset::where('status', 'Under Maintenance')->count();
        $disposedAssets = Asset::where('status', 'Disposed')->count();
        $lostAssets = Asset::where('status', 'Lost')->count();
        
        // Get approval workflow statistics
        $pendingApprovals = Asset::where('approval_status', Asset::APPROVAL_PENDING ?? 'pending')->count();
        $approvedAssets = Asset::where('approval_status', Asset::APPROVAL_APPROVED ?? 'approved')->count();
        $rejectedAssets = Asset::where('approval_status', Asset::APPROVAL_REJECTED ?? 'rejected')->count();
        
        // Get maintenance statistics
        $totalMaintenanceRequests = MaintenanceRequest::count();
        $pendingMaintenanceRequests = MaintenanceRequest::where('status', 'pending')->count();
        $approvedMaintenanceRequests = MaintenanceRequest::where('status', 'approved')->count();
        $completedMaintenanceRequests = MaintenanceRequest::where('status', 'completed')->count();
        $pendingMaintenances = Maintenance::whereIn('status', ['Scheduled', 'In Progress'])->count();
        
        // Get repair request statistics
        $totalRepairRequests = RepairRequest::count();
        $pendingRepairRequests = RepairRequest::where('status', 'pending')->count();
        $inProgressRepairRequests = RepairRequest::where('status', 'in_progress')->count();
        $completedRepairRequests = RepairRequest::where('status', 'completed')->count();
        $criticalRepairRequests = RepairRequest::where('urgency_level', 'critical')->count();
        
        // Get user statistics
        $totalUsers = User::count();
        $adminUsers = User::where('role', 'admin')->count();
        $gsuUsers = User::where('role', 'gsu')->count();
        $regularUsers = User::where('role', 'user')->count();
        $purchasingUsers = User::where('role', 'purchasing')->count();
        
        // System statistics
        $totalCategories = Category::count();
        $totalLocations = Location::count();

        // Get recent assets (last 10)
        $recentAssets = Asset::with(['category', 'location', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Get categories with asset counts
        $categories = Category::withCount('assets')
            ->orderBy('assets_count', 'desc')
            ->get();

        // Get locations with asset counts
        $locations = Location::withCount('assets')
            ->orderBy('assets_count', 'desc')
            ->take(10)
            ->get();
            
        // Get recent maintenance requests
        $recentMaintenanceRequests = MaintenanceRequest::with(['requester', 'location'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Get notifications for admin
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        $unreadNotifications = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        // Prepare comprehensive chart data
        $assetStatusData = [
            'Available' => $availableAssets,
            'In Use' => $inUseAssets,
            'Under Maintenance' => $maintenanceAssets,
            'Disposed' => $disposedAssets,
            'Lost' => $lostAssets,
        ];
        
        $assetApprovalData = [
            'Pending' => $pendingApprovals,
            'Approved' => $approvedAssets,
            'Rejected' => $rejectedAssets,
        ];
        
        $maintenanceStatusData = [
            'Pending' => $pendingMaintenanceRequests,
            'Approved' => $approvedMaintenanceRequests,
            'Completed' => $completedMaintenanceRequests,
        ];
        
        $userRoleData = [
            'Admin' => $adminUsers,
            'GSU' => $gsuUsers,
            'User' => $regularUsers,
            'Purchasing' => $purchasingUsers,
        ];
        
        // Get assets by category for chart
        $assetsByCategory = $categories->pluck('assets_count', 'name')->toArray();
        
        // Get monthly asset creation trend (last 12 months)
        $monthlyAssetTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Asset::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $monthlyAssetTrend[$date->format('M Y')] = $count;
        }
        
        // Get asset value statistics (using purchase_cost column)
        $totalAssetValue = Asset::sum('purchase_cost') ?? 0;
        $averageAssetValue = $totalAssets > 0 ? $totalAssetValue / $totalAssets : 0;

        return view('dashboard.dashboard', compact(
            'totalAssets',
            'availableAssets',
            'inUseAssets',
            'maintenanceAssets',
            'disposedAssets',
            'lostAssets',
            'pendingApprovals',
            'approvedAssets',
            'rejectedAssets',
            'totalMaintenanceRequests',
            'pendingMaintenanceRequests',
            'approvedMaintenanceRequests',
            'completedMaintenanceRequests',
            'pendingMaintenances',
            'totalUsers',
            'adminUsers',
            'gsuUsers',
            'regularUsers',
            'purchasingUsers',
            'totalCategories',
            'totalLocations',
            'recentAssets',
            'categories',
            'locations',
            'recentMaintenanceRequests',
            'notifications',
            'unreadNotifications',
            'assetStatusData',
            'assetApprovalData',
            'maintenanceStatusData',
            'userRoleData',
            'assetsByCategory',
            'monthlyAssetTrend',
            'totalAssetValue',
            'averageAssetValue'
        ));
    }
}