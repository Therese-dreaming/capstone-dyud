@extends('layouts.user')

@section('title', 'Dashboard - Asset Management System')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-blue-50" x-data="dashboardData()">
    <!-- Welcome Banner -->
    <div x-show="showWelcome" x-transition class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-4 md:p-6 mb-6 rounded-xl shadow-lg relative overflow-hidden mx-4 mt-4">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-white/20 p-3 rounded-full">
                    <i class="fas fa-user-circle text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-xl md:text-2xl font-bold">Welcome back, {{ auth()->user()->name }}! 👋</h1>
                    <p class="text-blue-100 text-sm md:text-base">Here's an overview of your assigned locations and assets</p>
                </div>
            </div>
            <button @click="showWelcome = false" class="text-white/80 hover:text-white transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <!-- Dashboard Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-3 rounded-xl shadow-lg">
                        <i class="fas fa-tachometer-alt text-xl"></i>
                    </div>
                    My Dashboard
                </h1>
                <p class="text-gray-600 mt-2 text-sm md:text-base">Monitor your assigned locations, assets, and maintenance requests</p>
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

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Assigned Locations -->
            <div class="group bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 cursor-pointer"
                 @click="activeTab = 'overview'">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 group-hover:text-blue-600 transition-colors">Assigned Locations</p>
                            <p class="text-3xl font-bold text-gray-900 group-hover:text-blue-700 transition-colors">{{ $totalLocations ?? 0 }}</p>
                            <p class="text-sm text-green-600 mt-1 flex items-center">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                Active assignments
                            </p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full group-hover:bg-blue-200 transition-colors">
                            <i class="fas fa-building text-blue-600 text-xl group-hover:scale-110 transition-transform"></i>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-blue-500 to-blue-600 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left"></div>
            </div>

            <!-- Total Assets -->
            <div class="group bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 cursor-pointer"
                 @click="activeTab = 'assets'">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 group-hover:text-green-600 transition-colors">Total Assets</p>
                            <p class="text-3xl font-bold text-gray-900 group-hover:text-green-700 transition-colors">{{ $totalAssets ?? 0 }}</p>
                            <p class="text-sm text-blue-600 mt-1 flex items-center">
                                <i class="fas fa-boxes mr-1"></i>
                                Under your management
                            </p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full group-hover:bg-green-200 transition-colors">
                            <i class="fas fa-cube text-green-600 text-xl group-hover:scale-110 transition-transform"></i>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-green-500 to-green-600 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left"></div>
            </div>

            <!-- Maintenance Requests -->
            <div class="group bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 cursor-pointer"
                 @click="activeTab = 'maintenance'">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 group-hover:text-orange-600 transition-colors">Maintenance Requests</p>
                            <p class="text-3xl font-bold text-gray-900 group-hover:text-orange-700 transition-colors">{{ $totalRequests ?? 0 }}</p>
                            <p class="text-sm text-orange-600 mt-1 flex items-center">
                                <i class="fas fa-tools mr-1"></i>
                                {{ $pendingRequests ?? 0 }} pending
                            </p>
                        </div>
                        <div class="bg-orange-100 p-3 rounded-full group-hover:bg-orange-200 transition-colors">
                            <i class="fas fa-wrench text-orange-600 text-xl group-hover:scale-110 transition-transform"></i>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-orange-500 to-orange-600 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left"></div>
            </div>

            <!-- Notifications -->
            <div class="group bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 cursor-pointer"
                 @click="activeTab = 'notifications'">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 group-hover:text-red-600 transition-colors">Notifications</p>
                            <p class="text-3xl font-bold text-gray-900 group-hover:text-red-700 transition-colors">{{ $unreadNotifications ?? 0 }}</p>
                            <p class="text-sm text-red-600 mt-1 flex items-center">
                                <i class="fas fa-bell mr-1"></i>
                                Unread messages
                            </p>
                        </div>
                        <div class="bg-red-100 p-3 rounded-full group-hover:bg-red-200 transition-colors">
                            <i class="fas fa-envelope text-red-600 text-xl group-hover:scale-110 transition-transform"></i>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-red-500 to-red-600 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left"></div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-bolt text-yellow-500"></i>
                Quick Actions
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('maintenance-requests.create') }}" 
                   class="flex items-center p-4 bg-gradient-to-r from-orange-50 to-orange-100 border border-orange-200 rounded-lg hover:from-orange-100 hover:to-orange-200 transition-all duration-200 group">
                    <div class="bg-orange-500 p-3 rounded-full mr-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-plus text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">New Maintenance Request</h4>
                        <p class="text-sm text-gray-600">Submit a new maintenance request</p>
                    </div>
                </a>
                
                <a href="{{ route('notifications.index') }}" 
                   class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-lg hover:from-blue-100 hover:to-blue-200 transition-all duration-200 group">
                    <div class="bg-blue-500 p-3 rounded-full mr-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-bell text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">View All Notifications</h4>
                        <p class="text-sm text-gray-600">Check your notification history</p>
                    </div>
                </a>
                
                <button @click="refreshDashboard()" 
                        class="flex items-center p-4 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-lg hover:from-green-100 hover:to-green-200 transition-all duration-200 group">
                    <div class="bg-green-500 p-3 rounded-full mr-4 group-hover:scale-110 transition-transform">
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
                            :class="activeTab === 'overview' ? 'bg-blue-50 text-blue-600 border-b-2 border-blue-500' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                            class="flex-1 py-4 px-6 font-medium text-sm transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-chart-pie"></i>Overview
                    </button>
                    <button @click="activeTab = 'assets'" 
                            :class="activeTab === 'assets' ? 'bg-green-50 text-green-600 border-b-2 border-green-500' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                            class="flex-1 py-4 px-6 font-medium text-sm transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-boxes"></i>Assets
                    </button>
                    <button @click="activeTab = 'maintenance'" 
                            :class="activeTab === 'maintenance' ? 'bg-orange-50 text-orange-600 border-b-2 border-orange-500' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                            class="flex-1 py-4 px-6 font-medium text-sm transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-tools"></i>Maintenance
                    </button>
                    <button @click="activeTab = 'notifications'" 
                            :class="activeTab === 'notifications' ? 'bg-red-50 text-red-600 border-b-2 border-red-500' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                            class="flex-1 py-4 px-6 font-medium text-sm transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-bell"></i>Notifications
                        @if($unreadNotifications > 0)
                        <span class="bg-red-500 text-white text-xs rounded-full px-2 py-1 ml-1">{{ $unreadNotifications }}</span>
                        @endif
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
                                <i class="fas fa-chart-pie text-blue-600"></i>
                                Asset Status Distribution
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="relative">
                                <canvas id="assetStatusChart" width="400" height="300"></canvas>
                            </div>
                            <div class="mt-4 grid grid-cols-3 gap-4 text-center">
                                <div class="bg-green-50 p-3 rounded-lg">
                                    <div class="text-2xl font-bold text-green-600">{{ $availableAssets }}</div>
                                    <div class="text-sm text-green-700">Available</div>
                                </div>
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600">{{ $inUseAssets }}</div>
                                    <div class="text-sm text-blue-700">In Use</div>
                                </div>
                                <div class="bg-orange-50 p-3 rounded-lg">
                                    <div class="text-2xl font-bold text-orange-600">{{ $maintenanceAssets }}</div>
                                    <div class="text-sm text-orange-700">Maintenance</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Maintenance Requests Chart -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-chart-bar text-orange-600"></i>
                                Maintenance Request Status
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="relative">
                                <canvas id="requestStatusChart" width="400" height="300"></canvas>
                            </div>
                            <div class="mt-4 grid grid-cols-3 gap-4 text-center">
                                <div class="bg-yellow-50 p-3 rounded-lg">
                                    <div class="text-2xl font-bold text-yellow-600">{{ $pendingRequests }}</div>
                                    <div class="text-sm text-yellow-700">Pending</div>
                                </div>
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600">{{ $approvedRequests }}</div>
                                    <div class="text-sm text-blue-700">Approved</div>
                                </div>
                                <div class="bg-green-50 p-3 rounded-lg">
                                    <div class="text-2xl font-bold text-green-600">{{ $completedRequests }}</div>
                                    <div class="text-sm text-green-700">Completed</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assigned Locations -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-blue-600"></i>
                            Your Assigned Locations
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($assignedLocations->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($assignedLocations as $location)
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="bg-blue-600 text-white p-2 rounded-full">
                                        <i class="fas fa-building text-sm"></i>
                                    </div>
                                    <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full">
                                        {{ $location->assets->count() }} assets
                                    </span>
                                </div>
                                <h4 class="font-semibold text-gray-900">{{ $location->building }}</h4>
                                <p class="text-sm text-gray-600">Floor {{ $location->floor }} - Room {{ $location->room }}</p>
                                <div class="mt-3 flex items-center justify-between">
                                    <span class="text-xs text-blue-600 font-medium">
                                        <i class="fas fa-calendar mr-1"></i>
                                        Assigned {{ $location->pivot->assigned_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8">
                            <i class="fas fa-map-marker-alt text-4xl text-gray-300 mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No Locations Assigned</h4>
                            <p class="text-gray-600">You haven't been assigned to manage any locations yet.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Assets Tab -->
            <div x-show="activeTab === 'assets'" x-transition>
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-boxes text-green-600"></i>
                            Recent Asset Activity
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($recentAssets->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentAssets as $asset)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div class="bg-green-100 p-3 rounded-full">
                                        <i class="fas fa-cube text-green-600"></i>
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
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $asset->status }}
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Updated {{ $asset->updated_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8">
                            <i class="fas fa-boxes text-4xl text-gray-300 mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No Assets Found</h4>
                            <p class="text-gray-600">No assets are currently assigned to your locations.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Maintenance Tab -->
            <div x-show="activeTab === 'maintenance'" x-transition>
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-tools text-orange-600"></i>
                            Your Maintenance Requests
                        </h3>
                        <a href="{{ route('maintenance-requests.create') }}" 
                           class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            <i class="fas fa-plus mr-2"></i>New Request
                        </a>
                    </div>
                    <div class="p-6">
                        @if($recentRequests->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentRequests as $request)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-orange-100 p-2 rounded-full">
                                            <i class="fas fa-wrench text-orange-600"></i>
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
                                        <i class="fas fa-calendar mr-1"></i>
                                        Submitted {{ $request->created_at->diffForHumans() }}
                                    </span>
                                    <span>
                                        <i class="fas fa-building mr-1"></i>
                                        {{ $request->department }} - {{ $request->school_year }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8">
                            <i class="fas fa-tools text-4xl text-gray-300 mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No Maintenance Requests</h4>
                            <p class="text-gray-600 mb-4">You haven't submitted any maintenance requests yet.</p>
                            <a href="{{ route('maintenance-requests.create') }}" 
                               class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-plus mr-2"></i>Submit Your First Request
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Notifications Tab -->
            <div x-show="activeTab === 'notifications'" x-transition>
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-bell text-red-600"></i>
                            Recent Notifications
                        </h3>
                        <a href="{{ route('notifications.index') }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="p-6">
                        @if($notifications->count() > 0)
                        <div class="space-y-4">
                            @foreach($notifications as $notification)
                            <div class="flex items-start space-x-4 p-4 {{ $notification->is_read ? 'bg-gray-50' : 'bg-blue-50' }} rounded-lg">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 {{ $notification->is_read ? 'bg-gray-100' : 'bg-blue-100' }} rounded-full flex items-center justify-center">
                                        <i class="{{ $notification->icon }} {{ $notification->color }} text-sm"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-semibold {{ $notification->is_read ? 'text-gray-900' : 'text-blue-900' }}">
                                            {{ $notification->title }}
                                        </h4>
                                        @if(!$notification->is_read)
                                        <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                                        @endif
                                    </div>
                                    <p class="text-sm {{ $notification->is_read ? 'text-gray-600' : 'text-blue-800' }} mt-1">
                                        {{ $notification->message }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-2">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8">
                            <i class="fas fa-bell text-4xl text-gray-300 mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No Notifications</h4>
                            <p class="text-gray-600">You're all caught up! No new notifications.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Alpine.js Dashboard Data
function dashboardData() {
    return {
        showWelcome: true,
        activeTab: 'overview',
        assetStatusChart: null,
        requestStatusChart: null,
        
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
                        labels: ['Available', 'In Use', 'Under Maintenance'],
                        datasets: [{
                            data: [
                                {{ $availableAssets ?? 0 }},
                                {{ $inUseAssets ?? 0 }},
                                {{ $maintenanceAssets ?? 0 }}
                            ],
                            backgroundColor: [
                                '#10B981', // Green
                                '#3B82F6', // Blue
                                '#F59E0B'  // Orange
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

            // Maintenance Request Status Chart
            const requestStatusCtx = document.getElementById('requestStatusChart');
            if (requestStatusCtx) {
                this.requestStatusChart = new Chart(requestStatusCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Pending', 'Approved', 'Completed'],
                        datasets: [{
                            label: 'Requests',
                            data: [
                                {{ $pendingRequests ?? 0 }},
                                {{ $approvedRequests ?? 0 }},
                                {{ $completedRequests ?? 0 }}
                            ],
                            backgroundColor: [
                                '#EAB308', // Yellow
                                '#3B82F6', // Blue
                                '#10B981'  // Green
                            ],
                            borderColor: [
                                '#CA8A04',
                                '#2563EB',
                                '#059669'
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
        },
        
        markNotificationAsRead(notificationId) {
            fetch(`/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    window.location.reload();
                }
            }).catch(error => {
                console.error('Error marking notification as read:', error);
            });
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