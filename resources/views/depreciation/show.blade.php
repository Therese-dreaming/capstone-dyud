@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Asset Depreciation Details</h1>
            <p class="text-gray-600">{{ $asset->name }} ({{ $asset->asset_code }})</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('depreciation.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to Report
            </a>
            @can('update', $asset)
                <a href="{{ route('depreciation.edit', $asset) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-edit mr-2"></i>Edit Settings
                </a>
            @endcan
        </div>
    </div>

    <!-- Asset Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Asset Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">Asset Code</p>
                    <p class="font-medium">{{ $asset->asset_code }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Name</p>
                    <p class="font-medium">{{ $asset->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Category</p>
                    <p class="font-medium">{{ $asset->category->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Location</p>
                    <p class="font-medium">{{ $asset->location->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <span class="inline-block px-2 py-1 text-xs rounded-full {{ $asset->getStatusBadgeClass() }}">
                        {{ $asset->getStatusLabel() }}
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Purchase Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">Purchase Date</p>
                    <p class="font-medium">{{ $asset->purchase_date->format('F d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Purchase Cost</p>
                    <p class="font-medium text-lg">₱{{ number_format($depreciation['purchase_cost'], 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Salvage Value</p>
                    <p class="font-medium">₱{{ number_format($depreciation['salvage_value'], 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Depreciable Amount</p>
                    <p class="font-medium">₱{{ number_format($depreciation['purchase_cost'] - $depreciation['salvage_value'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Depreciation Settings</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">Method</p>
                    <p class="font-medium">{{ $asset->getDepreciationMethodLabel() }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Useful Life</p>
                    <p class="font-medium">{{ $depreciation['useful_life_years'] }} years</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Depreciation Rate</p>
                    <p class="font-medium">{{ $depreciation['depreciation_rate'] }}%</p>
                </div>
                @if($asset->depreciation_method === 'declining_balance')
                    <div>
                        <p class="text-sm text-gray-600">Declining Balance Rate</p>
                        <p class="font-medium">{{ $asset->declining_balance_rate }}x</p>
                    </div>
                @endif
                <div>
                    <p class="text-sm text-gray-600">Start Date</p>
                    <p class="font-medium">
                        {{ $asset->depreciation_start_date ? $asset->depreciation_start_date->format('F d, Y') : $asset->purchase_date->format('F d, Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Depreciation Status -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-md p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm opacity-90">Current Age</p>
                <i class="fas fa-calendar-alt text-2xl opacity-75"></i>
            </div>
            <p class="text-3xl font-bold">{{ $depreciation['age_years'] }}</p>
            <p class="text-sm opacity-90 mt-1">years ({{ $depreciation['age_months'] }} months)</p>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-md p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm opacity-90">Current Book Value</p>
                <i class="fas fa-dollar-sign text-2xl opacity-75"></i>
            </div>
            <p class="text-3xl font-bold">₱{{ number_format($depreciation['current_book_value'], 2) }}</p>
            <p class="text-sm opacity-90 mt-1">
                {{ number_format(($depreciation['current_book_value'] / $depreciation['purchase_cost']) * 100, 1) }}% of original
            </p>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-md p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm opacity-90">Accumulated Depreciation</p>
                <i class="fas fa-arrow-down text-2xl opacity-75"></i>
            </div>
            <p class="text-3xl font-bold">₱{{ number_format($depreciation['accumulated_depreciation'], 2) }}</p>
            <p class="text-sm opacity-90 mt-1">
                {{ number_format(($depreciation['accumulated_depreciation'] / ($depreciation['purchase_cost'] - $depreciation['salvage_value'])) * 100, 1) }}% depreciated
            </p>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-md p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm opacity-90">Annual Depreciation</p>
                <i class="fas fa-chart-line text-2xl opacity-75"></i>
            </div>
            <p class="text-3xl font-bold">₱{{ number_format($depreciation['annual_depreciation'], 2) }}</p>
            <p class="text-sm opacity-90 mt-1">₱{{ number_format($depreciation['monthly_depreciation'], 2) }}/month</p>
        </div>
    </div>

    <!-- Depreciation Progress -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Depreciation Progress</h3>
        <div class="space-y-4">
            <div>
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-600">Depreciation Progress</span>
                    <span class="font-medium">
                        {{ number_format(($depreciation['accumulated_depreciation'] / ($depreciation['purchase_cost'] - $depreciation['salvage_value'])) * 100, 1) }}%
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="bg-gradient-to-r from-green-500 to-red-500 h-4 rounded-full transition-all duration-500" 
                         style="width: {{ min(100, ($depreciation['accumulated_depreciation'] / ($depreciation['purchase_cost'] - $depreciation['salvage_value'])) * 100) }}%">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 text-center pt-4 border-t">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Useful Life</p>
                    <p class="text-lg font-bold text-gray-900">{{ $depreciation['useful_life_years'] }} years</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Age</p>
                    <p class="text-lg font-bold text-blue-600">{{ $depreciation['age_years'] }} years</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Remaining Life</p>
                    <p class="text-lg font-bold text-green-600">{{ $depreciation['remaining_useful_life_years'] }} years</p>
                </div>
            </div>

            @if($depreciation['is_fully_depreciated'])
                <div class="bg-gray-100 border-l-4 border-gray-500 p-4 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-gray-600 text-xl mr-3"></i>
                        <div>
                            <p class="font-semibold text-gray-900">Fully Depreciated</p>
                            <p class="text-sm text-gray-600">This asset has reached its salvage value and is fully depreciated.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Depreciation Schedule -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Depreciation Schedule</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Annual Depreciation</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Accumulated Depreciation</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Book Value</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $currentYear = $depreciation['age_years'];
                    @endphp
                    @foreach($schedule as $year)
                        <tr class="{{ floor($currentYear) >= $year['year'] ? 'bg-blue-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium">Year {{ $year['year'] }}</span>
                                @if(floor($currentYear) == $year['year'])
                                    <span class="ml-2 px-2 py-1 text-xs rounded-full bg-blue-600 text-white">Current</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($year['date'])->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                ₱{{ number_format($year['annual_depreciation'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-red-600">
                                ₱{{ number_format($year['accumulated_depreciation'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-green-600">
                                ₱{{ number_format($year['book_value'], 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
