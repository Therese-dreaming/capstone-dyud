@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-8" x-data="{ showFilters: false }">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <i class="fas fa-chart-bar text-red-800"></i>
                Asset Report
            </h1>
            <p class="text-gray-600 mt-1">Comprehensive asset analysis and reporting</p>
        </div>
        <div class="flex gap-4">
            <button @click="showFilters = !showFilters" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                <i class="fas fa-filter"></i> 
                <span x-text="showFilters ? 'Hide Filters' : 'Show Filters'"></span>
            </button>
            <button onclick="window.print()" 
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Assets</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalAssets) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-boxes text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Value</p>
                    <p class="text-2xl font-bold text-gray-900">₱{{ number_format($totalValue, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Available</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($availableAssets) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Disposed</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($disposedAssets) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Statistics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Asset Status Distribution -->
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-chart-pie text-blue-600"></i>
                Status Distribution
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Available</span>
                    <span class="font-semibold text-green-600">{{ $availableAssets }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">In Use</span>
                    <span class="font-semibold text-blue-600">{{ $inUseAssets }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Lost</span>
                    <span class="font-semibold text-yellow-600">{{ $lostAssets }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Disposed</span>
                    <span class="font-semibold text-red-600">{{ $disposedAssets }}</span>
                </div>
            </div>
        </div>

        <!-- Asset Condition Distribution -->
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-tools text-orange-600"></i>
                Condition Distribution
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Good</span>
                    <span class="font-semibold text-green-600">{{ $goodCondition }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Fair</span>
                    <span class="font-semibold text-yellow-600">{{ $fairCondition }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Poor</span>
                    <span class="font-semibold text-red-600">{{ $poorCondition }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Panel -->
    <div x-show="showFilters" x-transition class="bg-white rounded-lg shadow-md p-6 mb-6 border border-gray-200">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-filter text-blue-600"></i>
            Filters
        </h3>
        
        <form method="GET" action="{{ route('assets.report') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Asset name, code, description..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Location Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <select name="location_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Locations</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->building }} - Floor {{ $location->floor }} - Room {{ $location->room }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="Available" {{ request('status') == 'Available' ? 'selected' : '' }}>Available</option>
                        <option value="In Use" {{ request('status') == 'In Use' ? 'selected' : '' }}>In Use</option>
                        <option value="Lost" {{ request('status') == 'Lost' ? 'selected' : '' }}>Lost</option>
                        <option value="Disposed" {{ request('status') == 'Disposed' ? 'selected' : '' }}>Disposed</option>
                    </select>
                </div>

                <!-- Condition Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Condition</label>
                    <select name="condition" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Conditions</option>
                        <option value="Good" {{ request('condition') == 'Good' ? 'selected' : '' }}>Good</option>
                        <option value="Fair" {{ request('condition') == 'Fair' ? 'selected' : '' }}>Fair</option>
                        <option value="Poor" {{ request('condition') == 'Poor' ? 'selected' : '' }}>Poor</option>
                    </select>
                </div>

                <!-- Purchase Date From -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Date From</label>
                    <input type="date" name="purchase_date_from" value="{{ request('purchase_date_from') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Purchase Date To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Date To</label>
                    <input type="date" name="purchase_date_to" value="{{ request('purchase_date_to') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Cost Min -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Min Cost (₱)</label>
                    <input type="number" step="0.01" name="cost_min" value="{{ request('cost_min') }}"
                           placeholder="0.00"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Cost Max -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Cost (₱)</label>
                    <input type="number" step="0.01" name="cost_max" value="{{ request('cost_max') }}"
                           placeholder="999999.99"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200 flex items-center gap-2">
                    <i class="fas fa-search"></i> Apply Filters
                </button>
                <a href="{{ route('assets.report') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-6 rounded-lg transition duration-200 flex items-center gap-2">
                    <i class="fas fa-times"></i> Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Asset Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <div class="bg-gray-50 p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800">Asset Details</h3>
                <div class="text-sm text-gray-600 font-medium">
                    Showing: <span class="text-red-800 font-bold">{{ $assets->count() }}</span> of <span class="text-red-800 font-bold">{{ $assets->total() }}</span> assets
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 font-sans">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-hash mr-1"></i>#
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-barcode mr-1"></i>Code
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-tag mr-1"></i>Asset Name
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-folder mr-1"></i>Category
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-map-marker-alt mr-1"></i>Location
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-tools mr-1"></i>Condition
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-info-circle mr-1"></i>Status
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-dollar-sign mr-1"></i>Cost
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <i class="fas fa-calendar mr-1"></i>Purchase Date
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($assets as $index => $asset)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100 text-sm text-gray-600">
                                {{ $assets->firstItem() + $index }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                <span class="font-mono text-sm font-bold text-gray-900 bg-gray-100 px-2 py-1 rounded">
                                    {{ $asset->asset_code }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                <div class="font-medium text-sm text-gray-900">{{ $asset->name }}</div>
                                @if($asset->description)
                                    <div class="text-xs text-gray-500 mt-1">{{ Str::limit($asset->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                <span class="text-sm text-gray-700 bg-blue-50 px-2 py-1 rounded-full">
                                    {{ $asset->category->name }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                <div class="text-xs text-gray-600">
                                    <div class="font-medium">{{ $asset->location->building }}</div>
                                    <div class="text-gray-500">Floor {{ $asset->location->floor }} • Room {{ $asset->location->room }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                                    {{ $asset->condition === 'Good' ? 'bg-green-100 text-green-800' : 
                                       ($asset->condition === 'Fair' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $asset->condition }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                                    {{ $asset->status === 'Available' ? 'bg-green-100 text-green-800' : 
                                       ($asset->status === 'In Use' ? 'bg-blue-100 text-blue-800' : 
                                       ($asset->status === 'Lost' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                    {{ $asset->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                <div class="text-sm font-medium text-gray-900">₱{{ number_format($asset->purchase_cost, 2) }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $asset->purchase_date->format('M d, Y') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-4"></i>
                                    <div class="text-lg font-medium text-gray-600">No assets found</div>
                                    <div class="text-sm text-gray-500 mt-1">Try adjusting your filters</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $assets->appends(request()->query())->links() }}
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white !important; }
    .shadow-md { box-shadow: none !important; }
}
</style>
@endsection
