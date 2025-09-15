@extends('layouts.purchasing')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Purchasing Dashboard</h1>
        <p class="mt-2 text-gray-600">Manage your asset submissions and track approval status</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Assets -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-purple-600 uppercase tracking-wide">Total Assets</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalAssets }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-box text-2xl text-purple-600"></i>
                </div>
            </div>
            <div class="mt-4 h-1 bg-gray-200 rounded-full">
                <div class="h-1 bg-purple-600 rounded-full" style="width: 100%"></div>
            </div>
        </div>

        <!-- Pending Approval -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-yellow-600 uppercase tracking-wide">Pending Approval</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $pendingAssets }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-clock text-2xl text-yellow-600"></i>
                </div>
            </div>
            <div class="mt-4 h-1 bg-gray-200 rounded-full">
                <div class="h-1 bg-yellow-500 rounded-full" style="width: {{ $totalAssets > 0 ? ($pendingAssets / $totalAssets) * 100 : 0 }}%"></div>
            </div>
        </div>

        <!-- Approved Assets -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-600 uppercase tracking-wide">Approved Assets</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $approvedAssets }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
            </div>
            <div class="mt-4 h-1 bg-gray-200 rounded-full">
                <div class="h-1 bg-green-500 rounded-full" style="width: {{ $totalAssets > 0 ? ($approvedAssets / $totalAssets) * 100 : 0 }}%"></div>
            </div>
        </div>

        <!-- Rejected Assets -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-red-600 uppercase tracking-wide">Rejected Assets</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $rejectedAssets }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-times-circle text-2xl text-red-600"></i>
                </div>
            </div>
            <div class="mt-4 h-1 bg-gray-200 rounded-full">
                <div class="h-1 bg-red-500 rounded-full" style="width: {{ $totalAssets > 0 ? ($rejectedAssets / $totalAssets) * 100 : 0 }}%"></div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Quick Actions</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('purchasing.assets.create') }}" 
                   class="flex items-center justify-center px-6 py-4 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Add New Asset
                </a>
                <a href="{{ route('purchasing.assets.index') }}" 
                   class="flex items-center justify-center px-6 py-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    <i class="fas fa-list mr-2"></i>
                    View All Assets
                </a>
                <a href="{{ route('notifications.index') }}" 
                   class="flex items-center justify-center px-6 py-4 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
                    <i class="fas fa-bell mr-2"></i>
                    View Notifications
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Recent Assets -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Assets</h2>
                    <a href="{{ route('purchasing.assets.index') }}" 
                       class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                        View All →
                    </a>
                </div>
                <div class="p-6">
                    @if($recentAssets->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-4 font-medium text-gray-700">Asset Code</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-700">Name</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-700">Category</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-700">Status</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-700">Created</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($recentAssets as $asset)
                                        <tr class="hover:bg-gray-50">
                                            <td class="py-3 px-4">
                                                <span class="font-semibold text-gray-900">{{ $asset->asset_code }}</span>
                                            </td>
                                            <td class="py-3 px-4 text-gray-700">{{ $asset->name }}</td>
                                            <td class="py-3 px-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $asset->category->name ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $asset->getApprovalStatusBadgeClass() }}">
                                                    {{ $asset->getApprovalStatusLabel() }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-gray-600">{{ $asset->created_at->format('M d, Y') }}</td>
                                            <td class="py-3 px-4">
                                                <a href="{{ route('purchasing.assets.show', $asset) }}" 
                                                   class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-700 rounded-md hover:bg-purple-200 transition-colors text-sm">
                                                    <i class="fas fa-eye mr-1"></i>
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-box-open text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-600 mb-4">No assets created yet.</p>
                            <a href="{{ route('purchasing.assets.create') }}" 
                               class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Create Your First Asset
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Assets by Category -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Assets by Category</h2>
                </div>
                <div class="p-6">
                    @if($assetsByCategory->count() > 0)
                        <div class="space-y-4">
                            @foreach($assetsByCategory as $category)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                                        <span class="text-sm font-medium text-gray-700">{{ $category->name }}</span>
                                    </div>
                                    <span class="text-lg font-bold text-gray-900">{{ $category->total }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-chart-pie text-3xl text-gray-400 mb-3"></i>
                            <p class="text-gray-600">No category data available.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Workflow Information -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Asset Workflow Process</h2>
            <p class="text-sm text-gray-600 mt-1">Understanding the 3-step asset management workflow</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Step 1: Create Asset -->
                <div class="text-center">
                    <div class="relative">
                        <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-plus text-2xl text-white"></i>
                        </div>
                        <div class="absolute top-8 left-full w-8 h-0.5 bg-gray-300 hidden md:block transform -translate-y-1/2"></div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">1. Create Asset</h3>
                    <p class="text-sm text-gray-600">Submit asset details without location assignment for admin review</p>
                </div>

                <!-- Step 2: Admin Approval -->
                <div class="text-center">
                    <div class="relative">
                        <div class="w-16 h-16 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-check text-2xl text-white"></i>
                        </div>
                        <div class="absolute top-8 left-full w-8 h-0.5 bg-gray-300 hidden md:block transform -translate-y-1/2"></div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">2. Admin Approval</h3>
                    <p class="text-sm text-gray-600">Admin reviews and approves or rejects your asset submission</p>
                </div>

                <!-- Step 3: GSU Deployment -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-map-marker-alt text-2xl text-white"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">3. GSU Deployment</h3>
                    <p class="text-sm text-gray-600">GSU assigns location and deploys the approved asset</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
