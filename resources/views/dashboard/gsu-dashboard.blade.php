@extends('layouts.gsu')

@section('title', 'GSU Dashboard - Asset Management System')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-red-50" x-data="gsuDashboardData()">
    <!-- GSU Welcome Banner -->
    <div x-show="showWelcome" x-transition class="bg-gradient-to-r from-red-600 to-red-800 text-white p-4 md:p-6 mb-6 rounded-xl shadow-lg relative overflow-hidden mx-4 mt-4">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-white/20 p-3 rounded-full">
                    <i class="fas fa-crown text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-xl md:text-2xl font-bold">Welcome, {{ Auth::user()->name }}! 👑</h1>
                    <p class="text-red-100 text-sm md:text-base">GSU Asset Management Dashboard</p>
                    <p class="text-xs text-red-200 mt-1">Last login: {{ Auth::user()->last_login ? Auth::user()->last_login->diffForHumans() : 'Never' }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <div class="text-sm text-red-200">System Status</div>
                    <div class="text-lg font-bold text-green-300 flex items-center gap-2">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        Online
                    </div>
                </div>
                <button @click="showWelcome = false" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <!-- Dashboard Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="bg-gradient-to-r from-red-600 to-red-800 text-white p-3 rounded-xl shadow-lg">
                        <i class="fas fa-crown text-xl"></i>
                    </div>
                    GSU Dashboard
                </h1>
                <p class="text-gray-600 mt-2 text-sm md:text-base">Manage assets, deployments, and system operations</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                @if($unreadNotifications > 0)
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-2 rounded-lg flex items-center gap-2">
                    <i class="fas fa-bell animate-pulse"></i>
                    <span class="font-medium">{{ $unreadNotifications }} unread notification{{ $unreadNotifications > 1 ? 's' : '' }}</span>
                </div>
                @endif
                <div class="bg-white rounded-lg shadow-sm px-4 py-2 border border-gray-200">
                    <div class="text-xs text-gray-500">Last updated</div>
                    <div class="text-sm font-medium text-gray-900">{{ now()->format('M d, Y h:i A') }}</div>
                </div>
            </div>
        </div>

        <!-- Enhanced Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Assets -->
            <div class="group bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 cursor-pointer"
                 @click="activeTab = 'assets'">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 group-hover:text-blue-600 transition-colors">Total Assets</p>
                            <p class="text-3xl font-bold text-gray-900 group-hover:text-blue-700 transition-colors">{{ number_format($totalAssets ?? 0) }}</p>
                            <p class="text-sm text-green-600 mt-1 flex items-center">
                                <i class="fas fa-boxes mr-1"></i>
                                {{ $availableAssets ?? 0 }} available
                            </p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full group-hover:bg-blue-200 transition-colors">
                            <i class="fas fa-boxes text-blue-600 text-xl group-hover:scale-110 transition-transform"></i>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-blue-500 to-blue-600 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left"></div>
            </div>

            <!-- Pending Approvals -->
            <div class="group bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 cursor-pointer"
                 @click="activeTab = 'approvals'">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 group-hover:text-orange-600 transition-colors">Pending Approvals</p>
                            <p class="text-3xl font-bold text-gray-900 group-hover:text-orange-700 transition-colors">{{ $pendingApprovalAssets ?? 0 }}</p>
                            <p class="text-sm text-orange-600 mt-1 flex items-center">
                                <i class="fas fa-clock mr-1"></i>
                                Awaiting deployment
                            </p>
                        </div>
                        <div class="bg-orange-100 p-3 rounded-full group-hover:bg-orange-200 transition-colors">
                            <i class="fas fa-hourglass-half text-orange-600 text-xl group-hover:scale-110 transition-transform"></i>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-orange-500 to-orange-600 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left"></div>
            </div>

            <!-- Maintenance Requests -->
            <div class="group bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 cursor-pointer"
                 @click="activeTab = 'maintenance'">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 group-hover:text-purple-600 transition-colors">Maintenance Requests</p>
                            <p class="text-3xl font-bold text-gray-900 group-hover:text-purple-700 transition-colors">{{ $totalMaintenanceRequests ?? 0 }}</p>
                            <p class="text-sm text-purple-600 mt-1 flex items-center">
                                <i class="fas fa-tools mr-1"></i>
                                {{ $pendingMaintenanceRequests ?? 0 }} pending
                            </p>
                        </div>
                        <div class="bg-purple-100 p-3 rounded-full group-hover:bg-purple-200 transition-colors">
                            <i class="fas fa-wrench text-purple-600 text-xl group-hover:scale-110 transition-transform"></i>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-purple-500 to-purple-600 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left"></div>
            </div>

            <!-- System Users -->
            <div class="group bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 cursor-pointer"
                 @click="activeTab = 'users'">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 group-hover:text-green-600 transition-colors">System Users</p>
                            <p class="text-3xl font-bold text-gray-900 group-hover:text-green-700 transition-colors">{{ number_format($totalUsers ?? 0) }}</p>
                            <p class="text-sm text-green-600 mt-1 flex items-center">
                                <i class="fas fa-users mr-1"></i>
                                {{ $regularUsers ?? 0 }} regular users
                            </p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full group-hover:bg-green-200 transition-colors">
                            <i class="fas fa-users text-green-600 text-xl group-hover:scale-110 transition-transform"></i>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-green-500 to-green-600 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left"></div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-bolt text-yellow-500"></i>
                Quick Actions
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="{{ route('gsu.assets.index') }}" 
                   class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-lg hover:from-blue-100 hover:to-blue-200 transition-all duration-200 group">
                    <div class="bg-blue-500 p-3 rounded-full mr-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-boxes text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">Deploy Assets</h4>
                        <p class="text-sm text-gray-600">Manage asset deployment</p>
                    </div>
                </a>
                
                <a href="{{ route('gsu.qr.scanner') }}" 
                   class="flex items-center p-4 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-lg hover:from-green-100 hover:to-green-200 transition-all duration-200 group">
                    <div class="bg-green-500 p-3 rounded-full mr-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-qrcode text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">QR Scanner</h4>
                        <p class="text-sm text-gray-600">Scan asset QR codes</p>
                    </div>
                </a>
                
                <a href="{{ route('notifications.index') }}" 
                   class="flex items-center p-4 bg-gradient-to-r from-purple-50 to-purple-100 border border-purple-200 rounded-lg hover:from-purple-100 hover:to-purple-200 transition-all duration-200 group">
                    <div class="bg-purple-500 p-3 rounded-full mr-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-bell text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">Notifications</h4>
                        <p class="text-sm text-gray-600">View system notifications</p>
                    </div>
                </a>
                
                <button @click="refreshDashboard()" 
                        class="flex items-center p-4 bg-gradient-to-r from-red-50 to-red-100 border border-red-200 rounded-lg hover:from-red-100 hover:to-red-200 transition-all duration-200 group">
                    <div class="bg-red-500 p-3 rounded-full mr-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-sync-alt text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">Refresh Dashboard</h4>
                        <p class="text-sm text-gray-600">Update all data and charts</p>
                    </div>
                </button>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="mb-8">
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <nav class="flex">
                    <button @click="activeTab = 'overview'" 
                            :class="activeTab === 'overview' ? 'bg-red-50 text-red-600 border-b-2 border-red-500' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                            class="flex-1 py-4 px-6 font-medium text-sm transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-chart-pie"></i>Overview
                    </button>
                    <button @click="activeTab = 'assets'" 
                            :class="activeTab === 'assets' ? 'bg-blue-50 text-blue-600 border-b-2 border-blue-500' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                            class="flex-1 py-4 px-6 font-medium text-sm transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-boxes"></i>Assets
                    </button>
                    <button @click="activeTab = 'approvals'" 
                            :class="activeTab === 'approvals' ? 'bg-orange-50 text-orange-600 border-b-2 border-orange-500' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                            class="flex-1 py-4 px-6 font-medium text-sm transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-hourglass-half"></i>Approvals
                        @if($pendingApprovalAssets > 0)
                        <span class="bg-orange-500 text-white text-xs rounded-full px-2 py-1 ml-1">{{ $pendingApprovalAssets }}</span>
                        @endif
                    </button>
                    <button @click="activeTab = 'maintenance'" 
                            :class="activeTab === 'maintenance' ? 'bg-purple-50 text-purple-600 border-b-2 border-purple-500' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                            class="flex-1 py-4 px-6 font-medium text-sm transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-tools"></i>Maintenance
                        @if($pendingMaintenanceRequests > 0)
                        <span class="bg-purple-500 text-white text-xs rounded-full px-2 py-1 ml-1">{{ $pendingMaintenanceRequests }}</span>
                        @endif
                    </button>
                    <button @click="activeTab = 'users'" 
                            :class="activeTab === 'users' ? 'bg-green-50 text-green-600 border-b-2 border-green-500' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                            class="flex-1 py-4 px-6 font-medium text-sm transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-users"></i>Users
                    </button>
                </nav>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="space-y-8">
            <!-- Overview Tab -->
            <div x-show="activeTab === 'overview'" x-transition>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Asset Status Chart -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-chart-pie text-red-600"></i>
                                Asset Status Distribution
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="relative">
                                <canvas id="assetStatusChart" width="400" height="300"></canvas>
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-4 text-center">
                                <div class="bg-green-50 p-3 rounded-lg">
                                    <div class="text-2xl font-bold text-green-600">{{ $availableAssets ?? 0 }}</div>
                                    <div class="text-sm text-green-700">Available</div>
                                </div>
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600">{{ $inUseAssets ?? 0 }}</div>
                                    <div class="text-sm text-blue-700">In Use</div>
                                </div>
                                <div class="bg-orange-50 p-3 rounded-lg">
                                    <div class="text-2xl font-bold text-orange-600">{{ $maintenanceAssets ?? 0 }}</div>
                                    <div class="text-sm text-orange-700">Maintenance</div>
                                </div>
                                <div class="bg-red-50 p-3 rounded-lg">
                                    <div class="text-2xl font-bold text-red-600">{{ $disposedAssets ?? 0 }}</div>
                                    <div class="text-sm text-red-700">Disposed</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Role Distribution Chart -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-users text-green-600"></i>
                                User Role Distribution
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="relative">
                                <canvas id="userRoleChart" width="400" height="300"></canvas>
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-4 text-center">
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600">{{ $adminUsers ?? 0 }}</div>
                                    <div class="text-sm text-blue-700">Admin</div>
                                </div>
                                <div class="bg-green-50 p-3 rounded-lg">
                                    <div class="text-2xl font-bold text-green-600">{{ $regularUsers ?? 0 }}</div>
                                    <div class="text-sm text-green-700">Users</div>
                                </div>
                                <div class="bg-red-50 p-3 rounded-lg">
                                    <div class="text-2xl font-bold text-red-600">{{ $gsuUsers ?? 0 }}</div>
                                    <div class="text-sm text-red-700">GSU</div>
                                </div>
                                <div class="bg-purple-50 p-3 rounded-lg">
                                    <div class="text-2xl font-bold text-purple-600">{{ $purchasingUsers ?? 0 }}</div>
                                    <div class="text-sm text-purple-700">Purchasing</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Statistics -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-chart-bar text-blue-600"></i>
                            System Statistics
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-gray-900 mb-2">{{ $totalCategories ?? 0 }}</div>
                                <div class="text-sm text-gray-600">Asset Categories</div>
                                <div class="mt-2 bg-blue-100 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: 85%"></div>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-gray-900 mb-2">{{ $totalLocations ?? 0 }}</div>
                                <div class="text-sm text-gray-600">Locations</div>
                                <div class="mt-2 bg-green-100 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: 92%"></div>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-gray-900 mb-2">{{ $totalMaintenanceRequests ?? 0 }}</div>
                                <div class="text-sm text-gray-600">Maintenance Requests</div>
                                <div class="mt-2 bg-purple-100 rounded-full h-2">
                                    <div class="bg-purple-500 h-2 rounded-full" style="width: 67%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assets Tab -->
            <div x-show="activeTab === 'assets'" x-transition>
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-boxes text-blue-600"></i>
                            Recent Assets
                        </h3>
                        <a href="{{ route('gsu.assets.index') }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="p-6">
                        @if($recentAssets->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentAssets as $asset)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div class="bg-blue-100 p-3 rounded-full">
                                        <i class="fas fa-cube text-blue-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $asset->asset_name ?? $asset->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $asset->asset_code }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $asset->location->building ?? 'No Location' }} - 
                                            {{ $asset->category->name ?? 'No Category' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($asset->status === 'Available') bg-green-100 text-green-800
                                        @elseif($asset->status === 'In Use') bg-blue-100 text-blue-800
                                        @elseif($asset->status === 'Under Maintenance') bg-orange-100 text-orange-800
                                        @elseif($asset->status === 'Disposed') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $asset->status }}
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Added {{ $asset->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8">
                            <i class="fas fa-boxes text-4xl text-gray-300 mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No Assets Found</h4>
                            <p class="text-gray-600">No assets have been created yet.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Approvals Tab -->
            <div x-show="activeTab === 'approvals'" x-transition>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Approval Status Chart -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-chart-bar text-orange-600"></i>
                                Asset Approval Status
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="relative">
                                <canvas id="assetApprovalChart" width="400" height="300"></canvas>
                            </div>
                            <div class="mt-4 grid grid-cols-3 gap-4 text-center">
                                <div class="bg-yellow-50 p-3 rounded-lg">
                                    <div class="text-2xl font-bold text-yellow-600">{{ $pendingApprovalAssets ?? 0 }}</div>
                                    <div class="text-sm text-yellow-700">Pending</div>
                                </div>
                                <div class="bg-green-50 p-3 rounded-lg">
                                    <div class="text-2xl font-bold text-green-600">{{ $approvedAssets ?? 0 }}</div>
                                    <div class="text-sm text-green-700">Approved</div>
                                </div>
                                <div class="bg-red-50 p-3 rounded-lg">
                                    <div class="text-2xl font-bold text-red-600">{{ $rejectedAssets ?? 0 }}</div>
                                    <div class="text-sm text-red-700">Rejected</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Actions -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-clock text-orange-600"></i>
                                Pending Actions
                            </h3>
                        </div>
                        <div class="p-6">
                            @if($pendingApprovalAssets > 0)
                            <div class="space-y-4">
                                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="font-semibold text-orange-800">Assets Awaiting Deployment</h4>
                                            <p class="text-sm text-orange-600">{{ $pendingApprovalAssets }} assets ready for deployment</p>
                                        </div>
                                        <a href="{{ route('gsu.assets.index') }}" 
                                           class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                            Deploy Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="text-center py-8">
                                <i class="fas fa-check-circle text-4xl text-green-300 mb-4"></i>
                                <h4 class="text-lg font-medium text-gray-900 mb-2">All Caught Up!</h4>
                                <p class="text-gray-600">No pending approvals at this time.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Maintenance Tab -->
            <div x-show="activeTab === 'maintenance'" x-transition>
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-tools text-purple-600"></i>
                            Recent Maintenance Requests
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($recentMaintenanceRequests->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentMaintenanceRequests as $request)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-purple-100 p-2 rounded-full">
                                            <i class="fas fa-wrench text-purple-600"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">Request #{{ $request->id }}</h4>
                                            <p class="text-sm text-gray-600">
                                                {{ $request->location->building ?? 'Unknown Location' }} - 
                                                Floor {{ $request->location->floor ?? 'N/A' }} - 
                                                Room {{ $request->location->room ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($request->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($request->status === 'approved') bg-blue-100 text-blue-800
                                        @elseif($request->status === 'completed') bg-green-100 text-green-800
                                        @elseif($request->status === 'rejected') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-700 mb-3">{{ Str::limit($request->description, 100) }}</p>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>
                                        <i class="fas fa-user mr-1"></i>
                                        {{ $request->requester->name ?? 'Unknown User' }}
                                    </span>
                                    <span>
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ $request->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8">
                            <i class="fas fa-tools text-4xl text-gray-300 mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No Maintenance Requests</h4>
                            <p class="text-gray-600">No maintenance requests have been submitted.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Users Tab -->
            <div x-show="activeTab === 'users'" x-transition>
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-users text-green-600"></i>
                            System Users Overview
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                                <div class="bg-blue-100 p-3 rounded-full inline-block mb-3">
                                    <i class="fas fa-user-shield text-blue-600 text-xl"></i>
                                </div>
                                <div class="text-2xl font-bold text-blue-900">{{ $adminUsers ?? 0 }}</div>
                                <div class="text-sm text-blue-700">Admin Users</div>
                            </div>
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                                <div class="bg-green-100 p-3 rounded-full inline-block mb-3">
                                    <i class="fas fa-users text-green-600 text-xl"></i>
                                </div>
                                <div class="text-2xl font-bold text-green-900">{{ $regularUsers ?? 0 }}</div>
                                <div class="text-sm text-green-700">Regular Users</div>
                            </div>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                                <div class="bg-red-100 p-3 rounded-full inline-block mb-3">
                                    <i class="fas fa-crown text-red-600 text-xl"></i>
                                </div>
                                <div class="text-2xl font-bold text-red-900">{{ $gsuUsers ?? 0 }}</div>
                                <div class="text-sm text-red-700">GSU Users</div>
                            </div>
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 text-center">
                                <div class="bg-purple-100 p-3 rounded-full inline-block mb-3">
                                    <i class="fas fa-shopping-cart text-purple-600 text-xl"></i>
                                </div>
                                <div class="text-2xl font-bold text-purple-900">{{ $purchasingUsers ?? 0 }}</div>
                                <div class="text-sm text-purple-700">Purchasing Users</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Alpine.js GSU Dashboard Data
function gsuDashboardData() {
    return {
        showWelcome: true,
        activeTab: 'overview',
        assetStatusChart: null,
        userRoleChart: null,
        assetApprovalChart: null,
        
        init() {
            this.$nextTick(() => {
                this.initCharts();
            });
        },
        
        initCharts() {
            // Asset Status Chart
            const assetStatusCtx = document.getElementById('assetStatusChart');
            if (assetStatusCtx) {
                this.assetStatusChart = new Chart(assetStatusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Available', 'In Use', 'Under Maintenance', 'Disposed', 'Lost'],
                        datasets: [{
                            data: [
                                {{ $availableAssets ?? 0 }},
                                {{ $inUseAssets ?? 0 }},
                                {{ $maintenanceAssets ?? 0 }},
                                {{ $disposedAssets ?? 0 }},
                                {{ $lostAssets ?? 0 }}
                            ],
                            backgroundColor: [
                                '#10B981', // Green
                                '#3B82F6', // Blue
                                '#F59E0B', // Orange
                                '#EF4444', // Red
                                '#6B7280'  // Gray
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? Math.round((context.parsed * 100) / total) : 0;
                                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // User Role Chart
            const userRoleCtx = document.getElementById('userRoleChart');
            if (userRoleCtx) {
                this.userRoleChart = new Chart(userRoleCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Admin', 'GSU', 'Users', 'Purchasing'],
                        datasets: [{
                            label: 'Users',
                            data: [
                                {{ $adminUsers ?? 0 }},
                                {{ $gsuUsers ?? 0 }},
                                {{ $regularUsers ?? 0 }},
                                {{ $purchasingUsers ?? 0 }}
                            ],
                            backgroundColor: [
                                '#3B82F6', // Blue
                                '#EF4444', // Red
                                '#10B981', // Green
                                '#8B5CF6'  // Purple
                            ],
                            borderColor: [
                                '#2563EB',
                                '#DC2626',
                                '#059669',
                                '#7C3AED'
                            ],
                            borderWidth: 1,
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                },
                                grid: {
                                    color: '#F3F4F6'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // Asset Approval Chart
            const assetApprovalCtx = document.getElementById('assetApprovalChart');
            if (assetApprovalCtx) {
                this.assetApprovalChart = new Chart(assetApprovalCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Pending', 'Approved', 'Rejected'],
                        datasets: [{
                            label: 'Assets',
                            data: [
                                {{ $pendingApprovalAssets ?? 0 }},
                                {{ $approvedAssets ?? 0 }},
                                {{ $rejectedAssets ?? 0 }}
                            ],
                            backgroundColor: [
                                '#EAB308', // Yellow
                                '#10B981', // Green
                                '#EF4444'  // Red
                            ],
                            borderColor: [
                                '#CA8A04',
                                '#059669',
                                '#DC2626'
                            ],
                            borderWidth: 1,
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                },
                                grid: {
                                    color: '#F3F4F6'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        },
        
        refreshDashboard() {
            window.location.reload();
        }
    }
}

// Auto-refresh functionality
document.addEventListener('DOMContentLoaded', function() {
    setInterval(function() {
        const lastUpdated = document.querySelector('[data-last-updated]');
        if (lastUpdated) {
            lastUpdated.textContent = new Date().toLocaleString();
        }
    }, 60000); // Update every minute
});
</script>
@endsection