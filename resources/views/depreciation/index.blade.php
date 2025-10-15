@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-red-50">
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="bg-red-800 text-white p-3 rounded-xl shadow-lg">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    Asset Depreciation Report
                </h1>
                <p class="text-gray-600 mt-2">Track asset values and depreciation over time</p>
            </div>
            <a href="{{ route('depreciation.export', request()->query()) }}" 
               class="bg-green-600 text-white px-6 py-2.5 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                <i class="fas fa-file-excel"></i>
                <span>Export to Excel</span>
            </a>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Total Assets</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $summary['total_assets'] }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i class="fas fa-boxes text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Purchase Cost</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">₱{{ number_format($summary['total_purchase_cost'], 2) }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Book Value</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">₱{{ number_format($summary['total_current_book_value'], 2) }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Depreciation</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">₱{{ number_format($summary['total_accumulated_depreciation'], 2) }}</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-lg">
                        <i class="fas fa-arrow-down text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Metrics -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6 border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-3 bg-blue-50 rounded-lg">
                    <p class="text-xs text-gray-600 uppercase mb-1">Annual Depreciation</p>
                    <p class="text-xl font-bold text-gray-900">₱{{ number_format($summary['total_annual_depreciation'], 2) }}</p>
                </div>
                <div class="text-center p-3 bg-purple-50 rounded-lg">
                    <p class="text-xs text-gray-600 uppercase mb-1">Avg Depreciation Rate</p>
                    <p class="text-xl font-bold text-gray-900">{{ $summary['average_depreciation_rate'] }}%</p>
                </div>
                <div class="text-center p-3 bg-green-50 rounded-lg">
                    <p class="text-xs text-gray-600 uppercase mb-1">Fully Depreciated</p>
                    <p class="text-xl font-bold text-gray-900">{{ $summary['fully_depreciated_count'] }} / {{ $summary['total_assets'] }}</p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6 border border-gray-200">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-filter text-red-800"></i>
                Filter Assets
            </h2>
            
            <form method="GET" action="{{ route('depreciation.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category_id" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-300 focus:border-red-800 focus:ring-2 focus:ring-red-100 transition">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                        <select name="location_id" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-300 focus:border-red-800 focus:ring-2 focus:ring-red-100 transition">
                            <option value="">All Locations</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                    {{ $location->building }} - Floor {{ $location->floor }}, Room {{ $location->room }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Depreciation Method</label>
                        <select name="depreciation_method" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-300 focus:border-red-800 focus:ring-2 focus:ring-red-100 transition">
                            <option value="">All Methods</option>
                            <option value="straight_line" {{ request('depreciation_method') == 'straight_line' ? 'selected' : '' }}>Straight-Line</option>
                            <option value="declining_balance" {{ request('depreciation_method') == 'declining_balance' ? 'selected' : '' }}>Declining Balance</option>
                            <option value="sum_of_years_digits" {{ request('depreciation_method') == 'sum_of_years_digits' ? 'selected' : '' }}>Sum of Years Digits</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="fully_depreciated" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-300 focus:border-red-800 focus:ring-2 focus:ring-red-100 transition">
                            <option value="">All Assets</option>
                            <option value="0" {{ request('fully_depreciated') === '0' ? 'selected' : '' }}>Not Fully Depreciated</option>
                            <option value="1" {{ request('fully_depreciated') === '1' ? 'selected' : '' }}>Fully Depreciated</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="bg-red-800 text-white px-4 py-2 rounded-lg hover:bg-red-900 transition flex items-center gap-2">
                        <i class="fas fa-filter"></i>
                        Apply Filters
                    </button>
                    <a href="{{ route('depreciation.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition flex items-center gap-2">
                        <i class="fas fa-times"></i>
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Assets Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
            <div class="bg-red-800 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-table"></i>
                    Asset Details
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase Info</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase Cost</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Book Value</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Depreciation</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($assetsWithDepreciation as $asset)
                        @php
                            $dep = $asset->depreciation_data;
                            $depreciationPercentage = $dep['purchase_cost'] > 0 
                                ? ($dep['accumulated_depreciation'] / $dep['purchase_cost']) * 100 
                                : 0;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $asset->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $asset->asset_code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $asset->category->name ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">
                                    @if($asset->location)
                                        {{ $asset->location->building }} - F{{ $asset->location->floor }}, R{{ $asset->location->room }}
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $asset->purchase_date->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $dep['useful_life_years'] }} years life</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                    {{ $asset->getDepreciationMethodLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $dep['age_years'] }} {{ Str::plural('year', $dep['age_years']) }} - 
                                    {{ $dep['age_months'] }} {{ Str::plural('month', $dep['age_months']) }} - 
                                    {{ $dep['age_days'] }} {{ Str::plural('day', $dep['age_days']) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-medium text-gray-900">₱{{ number_format($dep['purchase_cost'], 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-medium text-gray-900">₱{{ number_format($dep['current_book_value'], 2) }}</div>
                                <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ 100 - $depreciationPercentage }}%"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm text-red-600 font-medium">₱{{ number_format($dep['accumulated_depreciation'], 2) }}</div>
                                <div class="text-xs text-gray-500">{{ number_format($depreciationPercentage, 1) }}%</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($dep['is_fully_depreciated'])
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                        <i class="fas fa-check-circle mr-1"></i>Fully Depreciated
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-clock mr-1"></i>Depreciating
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <a href="{{ route('depreciation.show', $asset) }}" class="text-blue-600 hover:text-blue-900 mr-3" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('update', $asset)
                                    <a href="{{ route('depreciation.edit', $asset) }}" class="text-green-600 hover:text-green-900" title="Edit Settings">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4 text-gray-400"></i>
                                <p>No assets found matching the selected filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>
@endsection
