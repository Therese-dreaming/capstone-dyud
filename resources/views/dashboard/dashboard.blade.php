@extends('layouts.admin')

@section('title', 'Admin Dashboard - Asset Management Analytics')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-red-50" x-data="adminDashboardData()">
    <!-- Welcome Banner -->
    <div x-show="showWelcome" x-transition class="bg-gradient-to-r from-red-600 to-red-800 text-white p-3 md:p-6 mb-4 md:mb-6 rounded-xl shadow-lg relative overflow-hidden mx-2 md:mx-4 mt-2 md:mt-4">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-3 md:gap-0">
            <div class="flex items-center space-x-2 md:space-x-4 flex-1 min-w-0">
                <div class="bg-white/20 p-2 md:p-3 rounded-full flex-shrink-0">
                    <i class="fas fa-chart-line text-lg md:text-2xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h1 class="text-base md:text-xl lg:text-2xl font-bold truncate">Welcome back, {{ Auth::user()->name ?? 'Admin' }}! 📊</h1>
                    <p class="text-red-100 text-xs md:text-sm lg:text-base">Comprehensive asset management analytics and insights</p>
                    <p class="text-xs text-red-200 mt-1 hidden md:block">Last login: {{ Auth::user()->last_login ? Auth::user()->last_login->diffForHumans() : 'Never' }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 md:space-x-4 self-end md:self-auto">
                <div class="text-right hidden md:block">
                    <div class="text-sm text-red-200">System Status</div>
                    <div class="text-lg font-bold text-green-300 flex items-center gap-2">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        Online
                    </div>
                </div>
                <button @click="showWelcome = false" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-lg md:text-xl"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <!-- Dashboard Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 md:mb-8">
            <div class="mb-3 sm:mb-0">
                <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 flex items-center gap-2 md:gap-3">
                    <div class="bg-gradient-to-r from-red-600 to-red-800 text-white p-2 md:p-3 rounded-xl shadow-lg">
                        <i class="fas fa-chart-line text-base md:text-xl"></i>
                    </div>
                    <span class="truncate">Analytics Dashboard</span>
                </h1>
                <p class="text-gray-600 mt-1 md:mt-2 text-xs md:text-sm lg:text-base">Comprehensive asset management insights and performance metrics</p>
            </div>
            <div class="flex items-center space-x-2 md:space-x-3 w-full sm:w-auto">
                @if($unreadNotifications > 0)
                <div class="bg-red-50 border border-red-200 text-red-800 px-3 md:px-4 py-1.5 md:py-2 rounded-lg flex items-center gap-2">
                    <i class="fas fa-bell animate-pulse text-sm md:text-base"></i>
                    <span class="font-medium text-xs md:text-sm whitespace-nowrap">{{ $unreadNotifications }} <span class="hidden sm:inline">unread notification{{ $unreadNotifications > 1 ? 's' : '' }}</span><span class="sm:hidden">unread</span></span>
                </div>
                @endif
                <div class="bg-white rounded-lg shadow-sm px-3 md:px-4 py-1.5 md:py-2 border border-gray-200 hidden sm:block">
                    <div class="text-xs text-gray-500">Last updated</div>
                    <div class="text-xs md:text-sm font-medium text-gray-900 whitespace-nowrap">{{ now()->format('M d, h:i A') }}</div>
                </div>
                <button @click="refreshDashboard()" class="bg-white rounded-lg shadow-sm p-2 border border-gray-200 hover:bg-gray-50 transition-colors" title="Refresh">
                    <i class="fas fa-sync-alt text-gray-600 text-sm md:text-base"></i>
                </button>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 mb-6 md:mb-8 overflow-hidden">
            <nav class="flex overflow-x-auto">
                <button @click="activeTab = 'overview'" 
                        :class="activeTab === 'overview' ? 'bg-red-50 text-red-600 border-b-2 border-red-500' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                        class="flex-1 min-w-[120px] py-3 md:py-4 px-4 md:px-6 font-medium text-xs md:text-sm transition-all duration-200 flex items-center justify-center gap-1 md:gap-2 whitespace-nowrap">
                    <i class="fas fa-chart-pie text-xs md:text-sm"></i><span class="hidden sm:inline">Overview</span><span class="sm:hidden">Overview</span>
                </button>
                <button @click="activeTab = 'analytics'" 
                        :class="activeTab === 'analytics' ? 'bg-blue-50 text-blue-600 border-b-2 border-blue-500' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                        class="flex-1 min-w-[120px] py-3 md:py-4 px-4 md:px-6 font-medium text-xs md:text-sm transition-all duration-200 flex items-center justify-center gap-1 md:gap-2 whitespace-nowrap">
                    <i class="fas fa-chart-line text-xs md:text-sm"></i><span class="hidden sm:inline">Analytics</span><span class="sm:hidden">Analytics</span>
                </button>
                <button @click="activeTab = 'approvals'" 
                        :class="activeTab === 'approvals' ? 'bg-orange-50 text-orange-600 border-b-2 border-orange-500' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                        class="flex-1 min-w-[120px] py-3 md:py-4 px-4 md:px-6 font-medium text-xs md:text-sm transition-all duration-200 flex items-center justify-center gap-1 md:gap-2 whitespace-nowrap">
                    <i class="fas fa-hourglass-half text-xs md:text-sm"></i><span class="hidden sm:inline">Approvals</span><span class="sm:hidden">Approve</span>
                    @if($pendingApprovals > 0)
                    <span class="bg-orange-500 text-white text-xs rounded-full px-1.5 md:px-2 py-0.5 md:py-1 ml-1">{{ $pendingApprovals }}</span>
                    @endif
                </button>
                <button @click="activeTab = 'maintenance'" 
                        :class="activeTab === 'maintenance' ? 'bg-purple-50 text-purple-600 border-b-2 border-purple-500' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                        class="flex-1 min-w-[120px] py-3 md:py-4 px-4 md:px-6 font-medium text-xs md:text-sm transition-all duration-200 flex items-center justify-center gap-1 md:gap-2 whitespace-nowrap">
                    <i class="fas fa-tools text-xs md:text-sm"></i><span class="hidden sm:inline">Maintenance</span><span class="sm:hidden">Maint.</span>
                    @if($pendingMaintenanceRequests > 0)
                    <span class="bg-purple-500 text-white text-xs rounded-full px-1.5 md:px-2 py-0.5 md:py-1 ml-1">{{ $pendingMaintenanceRequests }}</span>
                    @endif
                </button>
                <button @click="activeTab = 'repairs'" 
                        :class="activeTab === 'repairs' ? 'bg-yellow-50 text-yellow-600 border-b-2 border-yellow-500' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                        class="flex-1 min-w-[120px] py-3 md:py-4 px-4 md:px-6 font-medium text-xs md:text-sm transition-all duration-200 flex items-center justify-center gap-1 md:gap-2 whitespace-nowrap">
                    <i class="fas fa-wrench text-xs md:text-sm"></i><span class="hidden sm:inline">Repairs</span><span class="sm:hidden">Repair</span>
                    @php $pendingRepairs = \App\Models\RepairRequest::where('status', 'pending')->count(); @endphp
                    @if($pendingRepairs > 0)
                    <span class="bg-yellow-500 text-white text-xs rounded-full px-1.5 md:px-2 py-0.5 md:py-1 ml-1">{{ $pendingRepairs }}</span>
                    @endif
                </button>
                <button @click="activeTab = 'quick-actions'" 
                        :class="activeTab === 'quick-actions' ? 'bg-green-50 text-green-600 border-b-2 border-green-500' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                        class="flex-1 min-w-[120px] py-3 md:py-4 px-4 md:px-6 font-medium text-xs md:text-sm transition-all duration-200 flex items-center justify-center gap-1 md:gap-2 whitespace-nowrap">
                    <i class="fas fa-bolt text-xs md:text-sm"></i><span class="hidden sm:inline">Quick Actions</span><span class="sm:hidden">Actions</span>
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="space-y-8">
            <!-- Overview Tab -->
            <div x-show="activeTab === 'overview'" x-transition>
                <!-- Enhanced Key Metrics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Pending Approvals - Priority Card -->
                    <div class="group bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 cursor-pointer"
                         @click="activeTab = 'approvals'">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 group-hover:text-orange-600 transition-colors">Pending Approvals</p>
                                    <p class="text-3xl font-bold text-gray-900 group-hover:text-orange-700 transition-colors">{{ number_format($pendingApprovals ?? 0) }}</p>
                                    <p class="text-sm text-orange-600 mt-1 flex items-center">
                                        <i class="fas fa-clock mr-1"></i>
                                        Requires attention
                                    </p>
                                </div>
                                <div class="bg-orange-100 p-3 rounded-full group-hover:bg-orange-200 transition-colors">
                                    <i class="fas fa-hourglass-half text-orange-600 text-xl group-hover:scale-110 transition-transform"></i>
                                </div>
                            </div>
                        </div>
                        <div class="h-1 bg-gradient-to-r from-orange-500 to-orange-600 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left"></div>
                    </div>

                    <!-- Total Assets -->
                    <div class="group bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 cursor-pointer"
                         @click="activeTab = 'analytics'">
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

                    <!-- Repair Requests -->
                    <div class="group bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 cursor-pointer"
                         @click="activeTab = 'repairs'">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 group-hover:text-yellow-600 transition-colors">Repair Requests</p>
                                    <p class="text-3xl font-bold text-gray-900 group-hover:text-yellow-700 transition-colors">{{ \App\Models\RepairRequest::count() }}</p>
                                    <p class="text-sm text-yellow-600 mt-1 flex items-center">
                                        <i class="fas fa-wrench mr-1"></i>
                                        {{ \App\Models\RepairRequest::where('status', 'pending')->count() }} pending
                                    </p>
                                </div>
                                <div class="bg-yellow-100 p-3 rounded-full group-hover:bg-yellow-200 transition-colors">
                                    <i class="fas fa-tools text-yellow-600 text-xl group-hover:scale-110 transition-transform"></i>
                                </div>
                            </div>
                        </div>
                        <div class="h-1 bg-gradient-to-r from-yellow-500 to-yellow-600 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left"></div>
                    </div>
                </div>

                <!-- Quick Analytics Overview -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Asset Status Chart -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-chart-pie text-red-600"></i>
                                Asset Status Overview
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="relative">
                                <canvas id="overviewAssetStatusChart" width="400" height="300"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Trend Chart -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-chart-line text-blue-600"></i>
                                Asset Creation Trend
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="relative">
                                <canvas id="overviewTrendChart" width="400" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Health Indicators -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-heartbeat text-green-600"></i>
                            System Health & Performance
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-gray-900 mb-2">{{ round(($availableAssets / max($totalAssets, 1)) * 100) }}%</div>
                                <div class="text-sm text-gray-600 mb-2">Asset Availability Rate</div>
                                <div class="bg-green-100 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ round(($availableAssets / max($totalAssets, 1)) * 100) }}%"></div>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-gray-900 mb-2">{{ $totalCategories ?? 0 }}</div>
                                <div class="text-sm text-gray-600 mb-2">Asset Categories</div>
                                <div class="bg-blue-100 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: 85%"></div>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-gray-900 mb-2">{{ $totalLocations ?? 0 }}</div>
                                <div class="text-sm text-gray-600 mb-2">Active Locations</div>
                                <div class="bg-purple-100 rounded-full h-2">
                                    <div class="bg-purple-500 h-2 rounded-full" style="width: 92%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics Tab -->
            <div x-show="activeTab === 'analytics'" x-transition>
                <!-- Comprehensive Analytics Dashboard -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Asset Status Distribution -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-chart-pie text-blue-600"></i>
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

                    <!-- User Role Distribution -->
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

                <!-- Monthly Trend and Category Distribution -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Monthly Asset Creation Trend -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-chart-line text-purple-600"></i>
                                Asset Creation Trend (12 Months)
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="relative">
                                <canvas id="monthlyTrendChart" width="400" height="300"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Assets by Category -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-folder text-orange-600"></i>
                                Assets by Category
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="relative">
                                <canvas id="categoryChart" width="400" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Key Performance Indicators -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-tachometer-alt text-indigo-600"></i>
                            Key Performance Indicators
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-gray-900 mb-2">{{ round(($availableAssets / max($totalAssets, 1)) * 100) }}%</div>
                                <div class="text-sm text-gray-600 mb-2">Availability Rate</div>
                                <div class="bg-green-100 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ round(($availableAssets / max($totalAssets, 1)) * 100) }}%"></div>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-gray-900 mb-2">{{ round(($completedMaintenanceRequests / max($totalMaintenanceRequests, 1)) * 100) }}%</div>
                                <div class="text-sm text-gray-600 mb-2">Maintenance Completion</div>
                                <div class="bg-blue-100 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ round(($completedMaintenanceRequests / max($totalMaintenanceRequests, 1)) * 100) }}%"></div>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-gray-900 mb-2">{{ round(($approvedAssets / max(($pendingApprovals + $approvedAssets + $rejectedAssets), 1)) * 100) }}%</div>
                                <div class="text-sm text-gray-600 mb-2">Approval Rate</div>
                                <div class="bg-purple-100 rounded-full h-2">
                                    <div class="bg-purple-500 h-2 rounded-full" style="width: {{ round(($approvedAssets / max(($pendingApprovals + $approvedAssets + $rejectedAssets), 1)) * 100) }}%"></div>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-gray-900 mb-2">${{ number_format($totalAssetValue ?? 0) }}</div>
                                <div class="text-sm text-gray-600 mb-2">Total Asset Value</div>
                                <div class="bg-yellow-100 rounded-full h-2">
                                    <div class="bg-yellow-500 h-2 rounded-full" style="width: 78%"></div>
                                </div>
                            </div>
                        </div>
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
                                    <div class="text-2xl font-bold text-yellow-600">{{ $pendingApprovals ?? 0 }}</div>
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
                            @if($pendingApprovals > 0)
                            <div class="space-y-4">
                                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="font-semibold text-orange-800">Assets Awaiting Approval</h4>
                                            <p class="text-sm text-orange-600">{{ $pendingApprovals }} assets need your review</p>
                                        </div>
                                        <a href="{{ route('assets.pending') }}" 
                                           class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                            Review Now
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

            <!-- Repairs Tab -->
            <div x-show="activeTab === 'repairs'" x-transition>
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-wrench text-yellow-600"></i>
                            Recent Repair Requests
                        </h3>
                    </div>
                    <div class="p-6">
                        @php $recentRepairRequests = \App\Models\RepairRequest::with(['requester', 'asset'])->orderBy('created_at', 'desc')->take(5)->get(); @endphp
                        @if($recentRepairRequests->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentRepairRequests as $request)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-yellow-100 p-2 rounded-full">
                                            <i class="fas fa-tools text-yellow-600"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">Repair Request #{{ $request->id }}</h4>
                                            <p class="text-sm text-gray-600">
                                                Asset: {{ $request->asset->asset_code ?? 'Unknown' }} - 
                                                {{ $request->asset->name ?? 'Unknown Asset' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($request->urgency_level === 'critical') bg-red-100 text-red-800
                                            @elseif($request->urgency_level === 'high') bg-orange-100 text-orange-800
                                            @elseif($request->urgency_level === 'medium') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($request->urgency_level) }}
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($request->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($request->status === 'in_progress') bg-blue-100 text-blue-800
                                            @elseif($request->status === 'completed') bg-green-100 text-green-800
                                            @elseif($request->status === 'rejected') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $request->status === 'in_progress' ? 'In Progress' : ucfirst($request->status) }}
                                        </span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-700 mb-3">{{ Str::limit($request->issue_description, 100) }}</p>
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
                            <i class="fas fa-wrench text-4xl text-gray-300 mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No Repair Requests</h4>
                            <p class="text-gray-600">No repair requests have been submitted.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

            <!-- Quick Actions Tab -->
            <div x-show="activeTab === 'quick-actions'" x-transition>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-bolt text-red-600"></i>
                        Quick Actions
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Common tasks and shortcuts</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @if(auth()->user()->role === 'purchasing')
                            <a href="{{ route('purchasing.assets.create') }}" class="group">
                                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 text-center hover:from-blue-100 hover:to-blue-200 transition-all duration-300 border border-blue-200 hover:border-blue-300">
                                    <div class="bg-blue-500 p-3 rounded-full inline-block mb-4 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-plus-circle text-white text-xl"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Add Asset</h3>
                        @else
                            <div class="group cursor-not-allowed">
                                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 text-center border border-gray-200 opacity-60">
                                    <div class="bg-gray-400 p-3 rounded-full inline-block mb-4">
                                        <i class="fas fa-lock text-white text-xl"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-500 mb-2">Add Asset</h3>
                                    <p class="text-xs text-gray-400">Purchasing role only</p>
                                </div>
                            </div>
                        @endif
                                <p class="text-sm text-gray-600">Register a new asset in the system</p>
                            </div>
                        </a>

                        <a href="{{ route('categories.create') }}" class="group">
                            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 text-center hover:from-green-100 hover:to-green-200 transition-all duration-300 border border-green-200 hover:border-green-300">
                                <div class="bg-green-500 p-3 rounded-full inline-block mb-4 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-folder-plus text-white text-xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Add Category</h3>
                                <p class="text-sm text-gray-600">Create a new asset category</p>
                            </div>
                        </a>

                        <a href="{{ route('locations.create') }}" class="group">
                            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 text-center hover:from-purple-100 hover:to-purple-200 transition-all duration-300 border border-purple-200 hover:border-purple-300">
                                <div class="bg-purple-500 p-3 rounded-full inline-block mb-4 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-map-marked-alt text-white text-xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Add Location</h3>
                                <p class="text-sm text-gray-600">Define a new asset location</p>
                            </div>
                        </a>


                        <a href="{{ route('assets.index') }}" class="group">
                            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-6 text-center hover:from-red-100 hover:to-red-200 transition-all duration-300 border border-red-200 hover:border-red-300">
                                <div class="bg-red-500 p-3 rounded-full inline-block mb-4 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-tools text-white text-xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Manage Assets</h3>
                                <p class="text-sm text-gray-600">View and manage all assets</p>
                            </div>
                        </a>

                        <a href="{{ route('semester-assets.index') }}" class="group">
                            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl p-6 text-center hover:from-indigo-100 hover:to-indigo-200 transition-all duration-300 border border-indigo-200 hover:border-indigo-300">
                                <div class="bg-indigo-500 p-3 rounded-full inline-block mb-4 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-calendar-alt text-white text-xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Semester Tracking</h3>
                                <p class="text-sm text-gray-600">Track assets by semester</p>
                            </div>
                        </a>

                        <a href="{{ route('disposals.history') }}" class="group">
                            <div class="bg-gradient-to-br from-teal-50 to-teal-100 rounded-xl p-6 text-center hover:from-teal-100 hover:to-teal-200 transition-all duration-300 border border-teal-200 hover:border-teal-300">
                                <div class="bg-teal-500 p-3 rounded-full inline-block mb-4 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-file-alt text-white text-xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Generate Report</h3>
                                <p class="text-sm text-gray-600">View disposal history and reports</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

    <!-- Enhanced Toast Messages -->
    @if(session('success'))
    <div class="fixed top-6 right-6 z-50 bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-4 animate-fade-in min-w-[300px] border border-green-400"
         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
        <div class="bg-white/20 p-2 rounded-full">
            <i class="fas fa-check-circle text-xl"></i>
        </div>
        <div class="flex-1">
            <div class="font-semibold text-sm">Success!</div>
            <div class="text-xs opacity-90">{{ session('success') }}</div>
        </div>
        <button @click="show = false" class="text-white/80 hover:text-white transition-colors">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="fixed top-6 right-6 z-50 bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-4 animate-fade-in min-w-[300px] border border-red-400"
         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
        <div class="bg-white/20 p-2 rounded-full">
            <i class="fas fa-exclamation-triangle text-xl"></i>
        </div>
        <div class="flex-1">
            <div class="font-semibold text-sm">Error!</div>
            <div class="text-xs opacity-90">{{ session('error') }}</div>
        </div>
        <button @click="show = false" class="text-white/80 hover:text-white transition-colors">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif
</div>

<style>
@keyframes fade-in { 
    from { 
        opacity: 0; 
        transform: translateY(-20px) scale(0.95); 
    } 
    to { 
        opacity: 1; 
        transform: translateY(0) scale(1); 
    } 
}
.animate-fade-in { 
    animation: fade-in 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Smooth transitions */
* {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}
</style>
<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Alpine.js Admin Dashboard Data
function adminDashboardData() {
    return {
        showWelcome: true,
        activeTab: 'overview',
        assetStatusChart: null,
        userRoleChart: null,
        assetApprovalChart: null,
        monthlyTrendChart: null,
        categoryChart: null,
        overviewAssetStatusChart: null,
        overviewTrendChart: null,
        
        init() {
            this.$nextTick(() => {
                this.initCharts();
            });
        },
        
        initCharts() {
            // Overview Asset Status Chart
            const overviewAssetStatusCtx = document.getElementById('overviewAssetStatusChart');
            if (overviewAssetStatusCtx) {
                this.overviewAssetStatusChart = new Chart(overviewAssetStatusCtx, {
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
                                    padding: 15,
                                    usePointStyle: true,
                                    font: { size: 11 }
                                }
                            }
                        }
                    }
                });
            }

            // Overview Trend Chart
            const overviewTrendCtx = document.getElementById('overviewTrendChart');
            if (overviewTrendCtx) {
                this.overviewTrendChart = new Chart(overviewTrendCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode(array_keys($monthlyAssetTrend ?? [])) !!},
                        datasets: [{
                            label: 'Assets Created',
                            data: {!! json_encode(array_values($monthlyAssetTrend ?? [])) !!},
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });
            }

            // Asset Status Chart (Analytics Tab)
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
                                '#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#6B7280'
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
                                    font: { size: 12 }
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
                            backgroundColor: ['#3B82F6', '#EF4444', '#10B981', '#8B5CF6'],
                            borderColor: ['#2563EB', '#DC2626', '#059669', '#7C3AED'],
                            borderWidth: 1,
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 },
                                grid: { color: '#F3F4F6' }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            // Monthly Trend Chart
            const monthlyTrendCtx = document.getElementById('monthlyTrendChart');
            if (monthlyTrendCtx) {
                this.monthlyTrendChart = new Chart(monthlyTrendCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode(array_keys($monthlyAssetTrend ?? [])) !!},
                        datasets: [{
                            label: 'Assets Created',
                            data: {!! json_encode(array_values($monthlyAssetTrend ?? [])) !!},
                            borderColor: '#8B5CF6',
                            backgroundColor: 'rgba(139, 92, 246, 0.1)',
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#8B5CF6',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 },
                                grid: { color: '#F3F4F6' }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            // Category Chart
            const categoryCtx = document.getElementById('categoryChart');
            if (categoryCtx) {
                this.categoryChart = new Chart(categoryCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode(array_keys($assetsByCategory ?? [])) !!},
                        datasets: [{
                            label: 'Assets',
                            data: {!! json_encode(array_values($assetsByCategory ?? [])) !!},
                            backgroundColor: [
                                '#F59E0B', '#EF4444', '#10B981', '#3B82F6', '#8B5CF6',
                                '#EC4899', '#14B8A6', '#F97316', '#84CC16', '#6366F1'
                            ],
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 },
                                grid: { color: '#F3F4F6' }
                            },
                            x: { grid: { display: false } }
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
                                {{ $pendingApprovals ?? 0 }},
                                {{ $approvedAssets ?? 0 }},
                                {{ $rejectedAssets ?? 0 }}
                            ],
                            backgroundColor: ['#EAB308', '#10B981', '#EF4444'],
                            borderColor: ['#CA8A04', '#059669', '#DC2626'],
                            borderWidth: 1,
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 },
                                grid: { color: '#F3F4F6' }
                            },
                            x: { grid: { display: false } }
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