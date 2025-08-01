@extends('layouts.superadmin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-red-50" x-data="{ 
    showToast: {{ session('success') || session('error') ? 'true' : 'false' }},
    activeTab: 'overview',
    showWelcome: true
}">
    <!-- Welcome Banner -->
    <div x-show="showWelcome" x-transition class="bg-gradient-to-r from-red-600 to-red-800 text-white p-4 md:p-6 mb-6 rounded-xl shadow-lg relative overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-white/20 p-3 rounded-full">
                    <i class="fas fa-tachometer-alt text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-xl md:text-2xl font-bold">Welcome back, {{ Auth::user()->name ?? 'Admin' }}! 👋</h1>
                    <p class="text-red-100 text-sm md:text-base">Here's what's happening with your assets today</p>
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
                                    <div class="bg-gradient-to-r from-red-600 to-red-800 text-white p-3 rounded-xl shadow-lg">
                    <i class="fas fa-tachometer-alt text-xl"></i>
                </div>
                    Dashboard
                </h1>
                <p class="text-gray-600 mt-2 text-sm md:text-base">Monitor and manage your asset inventory efficiently</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                <div class="bg-white rounded-lg shadow-sm px-4 py-2 border border-gray-200">
                    <div class="text-xs text-gray-500">Last updated</div>
                    <div class="text-sm font-medium text-gray-900">{{ now()->format('M d, Y h:i A') }}</div>
                </div>
                <button class="bg-white rounded-lg shadow-sm p-2 border border-gray-200 hover:bg-gray-50 transition-colors" title="Refresh">
                    <i class="fas fa-sync-alt text-gray-600"></i>
                </button>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8 overflow-hidden">
            <div class="flex border-b border-gray-200">
                <button @click="activeTab = 'overview'" 
                        :class="activeTab === 'overview' ? 'bg-red-50 text-red-700 border-b-2 border-red-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
                        class="flex-1 px-6 py-4 text-sm font-medium transition-all duration-200 flex items-center justify-center gap-2">
                                            <i class="fas fa-chart-pie"></i>
                        Overview
                </button>
                <button @click="activeTab = 'analytics'" 
                        :class="activeTab === 'analytics' ? 'bg-red-50 text-red-700 border-b-2 border-red-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
                        class="flex-1 px-6 py-4 text-sm font-medium transition-all duration-200 flex items-center justify-center gap-2">
                                            <i class="fas fa-chart-line"></i>
                        Analytics
                </button>
                <button @click="activeTab = 'quick-actions'" 
                        :class="activeTab === 'quick-actions' ? 'bg-red-50 text-red-700 border-b-2 border-red-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
                        class="flex-1 px-6 py-4 text-sm font-medium transition-all duration-200 flex items-center justify-center gap-2">
                                            <i class="fas fa-bolt"></i>
                        Quick Actions
                </button>
            </div>
        </div>

        <!-- Overview Tab -->
        <div x-show="activeTab === 'overview'" x-transition>
            <!-- Key Metrics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Assets -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-3 rounded-xl">
                            <i class="fas fa-boxes text-white text-xl"></i>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-gray-900">{{ number_format($totalAssets) }}</div>
                            <div class="text-sm text-gray-500">Total Assets</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">Across all categories</span>
                        <a href="{{ route('assets.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium group-hover:underline">
                            View all <i class="fa fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>

                <!-- Available Assets -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-gradient-to-r from-green-500 to-green-600 p-3 rounded-xl">
                            <i class="fas fa-check-circle text-white text-xl"></i>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-gray-900">{{ number_format($availableAssets) }}</div>
                            <div class="text-sm text-gray-500">Available</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">Ready for use</span>
                        <a href="{{ route('assets.index') }}?status=Available" class="text-green-600 hover:text-green-700 text-sm font-medium group-hover:underline">
                            View available <i class="fa fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>

                <!-- Pending Maintenance -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 p-3 rounded-xl">
                            <i class="fas fa-wrench text-white text-xl"></i>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-gray-900">{{ number_format($pendingMaintenances) }}</div>
                            <div class="text-sm text-gray-500">Maintenance</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">Scheduled for service</span>
                        <a href="{{ route('maintenances.history') }}" class="text-yellow-600 hover:text-yellow-700 text-sm font-medium group-hover:underline">
                            View history <i class="fa fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>

                <!-- Total Users -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-3 rounded-xl">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-gray-900">{{ number_format($totalUsers) }}</div>
                            <div class="text-sm text-gray-500">Users</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">System users</span>
                        <a href="{{ route('users.index') }}" class="text-purple-600 hover:text-purple-700 text-sm font-medium group-hover:underline">
                            Manage users <i class="fa fa-arrow-right ml-1"></i>
                        </a>
                    </div>  
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Recent Assets -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-clock text-red-600"></i>
                                    Recently Added Assets
                                </h2>
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-medium">
                                    {{ $recentAssets->count() }} items
                                </span>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fas fa-box mr-1"></i>Asset
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
                                            <i class="fas fa-folder mr-1"></i>Category
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">
                                            <i class="fas fa-map-marker-alt mr-1"></i>Location
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fas fa-info-circle mr-1"></i>Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                            <i class="fas fa-calendar mr-1"></i>Added
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($recentAssets as $asset)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-lg bg-gradient-to-r from-red-500 to-red-600 flex items-center justify-center">
                                                        <i class="fa fa-box text-white text-sm" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <a href="{{ route('assets.show', $asset) }}" class="hover:text-red-600 transition-colors">
                                                            {{ $asset->name }}
                                                        </a>
                                                    </div>
                                                    <div class="text-sm text-gray-500 font-mono">{{ $asset->asset_code }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $asset->category->name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                            <div class="text-sm text-gray-900">{{ $asset->location->building }}</div>
                                            <div class="text-sm text-gray-500">Room {{ $asset->location->room }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($asset->status == 'Available')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fa fa-check-circle mr-1" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i> Available
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <i class="fa fa-times-circle mr-1" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i> {{ $asset->status }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                                            {{ $asset->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <div class="bg-gray-100 p-4 rounded-full mb-4">
                                                    <i class="fa fa-inbox text-gray-400 text-2xl" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                                                </div>
                                                <h3 class="text-lg font-medium text-gray-900 mb-2">No assets found</h3>
                                                <p class="text-gray-500 text-sm mb-4">Get started by adding your first asset</p>
                                                <a href="{{ route('assets.create') }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors text-sm font-medium">
                                                    Add First Asset
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                            <a href="{{ route('assets.index') }}" class="text-red-600 hover:text-red-700 text-sm font-medium flex items-center justify-end">
                                View all assets <i class="fa fa-arrow-right ml-1" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Top Locations - Moved under Recently Added Assets -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mt-6">
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-map-marker-alt text-red-600"></i>
                                Top Locations
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                @forelse($locations as $location)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-blue-100 p-2 rounded-lg">
                                            <i class="fas fa-building text-blue-600"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('locations.show', $location) }}" class="text-sm font-medium text-gray-900 hover:text-red-600 transition-colors">
                                                {{ $location->building }}
                                            </a>
                                            <div class="text-xs text-gray-500">Room {{ $location->room }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-bold text-gray-900">{{ $location->assets_count }}</div>
                                        <div class="text-xs text-gray-500">assets</div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-8">
                                    <div class="bg-gray-100 p-4 rounded-full inline-block mb-4">
                                        <i class="fas fa-map-marker-alt text-gray-400 text-2xl"></i>
                                    </div>
                                    <p class="text-gray-500 text-sm">No locations found</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                            <a href="{{ route('locations.index') }}" class="text-red-600 hover:text-red-700 text-sm font-medium flex items-center justify-end">
                                Manage locations <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Categories -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-folder text-red-600"></i>
                                Top Categories
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                @forelse($categories as $category)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-red-100 p-2 rounded-lg">
                                            <i class="fas fa-folder-open text-red-600"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('categories.show', $category) }}" class="text-sm font-medium text-gray-900 hover:text-red-600 transition-colors">
                                                {{ $category->name }}
                                            </a>
                                            <div class="text-xs text-gray-500">{{ $category->assets_count }} assets</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-bold text-gray-900">{{ $category->assets_count }}</div>
                                        <div class="text-xs text-gray-500">items</div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-8">
                                    <div class="bg-gray-100 p-4 rounded-full inline-block mb-4">
                                        <i class="fas fa-folder-open text-gray-400 text-2xl"></i>
                                    </div>
                                    <p class="text-gray-500 text-sm">No categories found</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                            <a href="{{ route('categories.index') }}" class="text-red-600 hover:text-red-700 text-sm font-medium flex items-center justify-end">
                                Manage categories <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Tab -->
        <div x-show="activeTab === 'analytics'" x-transition>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="flex flex-col md:flex-row justify-between items-center mb-8">
                    <div class="flex items-center gap-4 mb-4 md:mb-0">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 rounded-full">
                            <i class="fas fa-chart-line text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">Analytics Dashboard</h3>
                            <p class="text-gray-600">Asset performance and utilization insights</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
                            <option selected>Last 30 days</option>
                            <option>Last 90 days</option>
                            <option>Last year</option>
                            <option>All time</option>
                        </select>
                        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                            <i class="fas fa-sync-alt mr-1"></i> Refresh
                        </button>
                    </div>
                </div>

                <!-- Key Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-sm font-medium text-blue-800">Total Assets</h4>
                            <span class="bg-blue-200 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">All time</span>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($totalAssets) }}</div>
                        <div class="text-sm text-blue-800">
                            <i class="fas fa-arrow-up mr-1"></i> 12% increase from last month
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-sm font-medium text-green-800">Available Assets</h4>
                            <span class="bg-green-200 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Current</span>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($availableAssets) }}</div>
                        <div class="text-sm text-green-800">
                            <i class="fas fa-check-circle mr-1"></i> {{ round(($availableAssets / max($totalAssets, 1)) * 100) }}% availability rate
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-6 border border-yellow-200">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-sm font-medium text-yellow-800">Maintenance</h4>
                            <span class="bg-yellow-200 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Pending</span>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($pendingMaintenances) }}</div>
                        <div class="text-sm text-yellow-800">
                            <i class="fas fa-tools mr-1"></i> Scheduled for service
                        </div>
                    </div>
                </div>

                <!-- Asset Distribution Chart -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Asset Distribution by Category</h4>
                        <div class="h-64 relative">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Asset Status Distribution</h4>
                        <div class="h-64 relative">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
                

                <!-- Recent Activity -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <h4 class="text-lg font-semibold text-gray-900">Recent Activity</h4>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg">
                                <div class="bg-blue-100 p-2 rounded-full">
                                    <i class="fas fa-plus-circle text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">New asset added</p>
                                    <p class="text-xs text-gray-500">Laptop Dell XPS 15 was added to Electronics category</p>
                                    <p class="text-xs text-gray-400 mt-1">2 hours ago</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg">
                                <div class="bg-yellow-100 p-2 rounded-full">
                                    <i class="fas fa-tools text-yellow-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Maintenance scheduled</p>
                                    <p class="text-xs text-gray-500">Projector in Conference Room scheduled for maintenance</p>
                                    <p class="text-xs text-gray-400 mt-1">Yesterday</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg">
                                <div class="bg-green-100 p-2 rounded-full">
                                    <i class="fas fa-check-circle text-green-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Asset status updated</p>
                                    <p class="text-xs text-gray-500">Printer HP LaserJet marked as Available</p>
                                    <p class="text-xs text-gray-400 mt-1">3 days ago</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                        <a href="#" class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center justify-end">
                            View all activity <i class="fas fa-arrow-right ml-1"></i>
                        </a>
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
                        <a href="{{ route('assets.create') }}" class="group">
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 text-center hover:from-blue-100 hover:to-blue-200 transition-all duration-300 border border-blue-200 hover:border-blue-300">
                                <div class="bg-blue-500 p-3 rounded-full inline-block mb-4 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-plus-circle text-white text-xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Add Asset</h3>
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

                        <a href="{{ route('users.create') }}" class="group">
                            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-6 text-center hover:from-yellow-100 hover:to-yellow-200 transition-all duration-300 border border-yellow-200 hover:border-yellow-300">
                                <div class="bg-yellow-500 p-3 rounded-full inline-block mb-4 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-user-plus text-white text-xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Add User</h3>
                                <p class="text-sm text-gray-600">Create a new system user</p>
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

                        <a href="{{ route('assets.report') }}" class="group">
                            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl p-6 text-center hover:from-indigo-100 hover:to-indigo-200 transition-all duration-300 border border-indigo-200 hover:border-indigo-300">
                                <div class="bg-indigo-500 p-3 rounded-full inline-block mb-4 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-file-alt text-white text-xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Generate Report</h3>
                                <p class="text-sm text-gray-600">Create asset reports</p>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to cards
    const cards = document.querySelectorAll('.bg-white.rounded-xl');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Add loading states to links
    const links = document.querySelectorAll('a[href]');
    links.forEach(link => {
        link.addEventListener('click', function() {
            if (this.href && !this.href.includes('#')) {
                this.style.opacity = '0.7';
            }
        });
    });

    // Chart.js initialization
    function renderCharts() {
        // Destroy previous charts if they exist
        if (window.categoryChartInstance) {
            window.categoryChartInstance.destroy();
        }
        if (window.statusChartInstance) {
            window.statusChartInstance.destroy();
        }
        // Category Chart
        const categoryChartElem = document.getElementById('categoryChart');
        if (categoryChartElem) {
            const categoryCtx = categoryChartElem.getContext('2d');
            window.categoryChartInstance = new Chart(categoryCtx, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($categories->pluck('name')) !!},
                    datasets: [{
                        data: {!! json_encode($categories->pluck('assets_count')) !!},
                        backgroundColor: [
                            '#4F46E5', '#2563EB', '#3B82F6', '#60A5FA', '#93C5FD',
                            '#EF4444', '#F87171', '#FCA5A5', '#10B981', '#34D399',
                            '#F59E0B', '#FBBF24', '#8B5CF6', '#A78BFA', '#EC4899'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 15,
                                padding: 15,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Asset Distribution by Category',
                            font: {
                                size: 16
                            }
                        }
                    }
                }
            });
        }
        // Status Chart
        const statusChartElem = document.getElementById('statusChart');
        if (statusChartElem) {
            const statusCtx = statusChartElem.getContext('2d');
            window.statusChartInstance = new Chart(statusCtx, {
                type: 'bar',
                data: {
                    labels: ['Available', 'In Use', 'Maintenance', 'Retired'],
                    datasets: [{
                        label: 'Number of Assets',
                        data: [
                            {{ $availableAssets }},
                            {{ $inUseAssets }},
                            {{ $pendingMaintenances }},
                            0
                        ],
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.7)',  // Green for Available
                            'rgba(59, 130, 246, 0.7)',  // Blue for In Use
                            'rgba(245, 158, 11, 0.7)',  // Yellow for Maintenance
                            'rgba(239, 68, 68, 0.7)'    // Red for Retired
                        ],
                        borderColor: [
                            'rgb(16, 185, 129)',
                            'rgb(59, 130, 246)',
                            'rgb(245, 158, 11)',
                            'rgb(239, 68, 68)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Asset Status Distribution',
                            font: {
                                size: 16
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
    }

    // Render charts on initial load if Analytics tab is visible
    if (document.querySelector('[x-show="activeTab === \'analytics\'"]')) {
        renderCharts();
    }

    // Re-render charts when switching to Analytics tab
    document.querySelectorAll('button').forEach(button => {
        button.addEventListener('click', function() {
            if (this.textContent.trim().includes('Analytics')) {
                setTimeout(() => {
                    renderCharts();
                }, 200);
            }
        });
    });
});
</script>
@endsection