@extends('layouts.admin')

@section('content')

<!-- DEBUG INFORMATION -->
@if(config('app.debug'))
<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
    <h3 class="text-sm font-medium text-yellow-800 mb-2">🔍 Debug Information</h3>
    <div class="text-xs text-yellow-700 space-y-1">
        <p><strong>Start Date:</strong> {{ $startDate }}</p>
        <p><strong>End Date:</strong> {{ $endDate }}</p>
        <p><strong>Total Assets:</strong> {{ $assets->count() }}</p>
        <p><strong>Assets Purchased in Period:</strong> {{ $assets->filter(function($asset) use ($startDateCarbon, $endDateCarbon) { return $asset->purchase_date >= $startDateCarbon && $asset->purchase_date <= $endDateCarbon; })->count() }}</p>
        <p><strong>Assets with Location Changes:</strong> {{ $assets->filter(function($asset) use ($startDateCarbon, $endDateCarbon) { return $asset->changes->where('change_type', 'location_change')->count() > 0; })->count() }}</p>
        <p><strong>Assets with Status Changes:</strong> {{ $assets->filter(function($asset) use ($startDateCarbon, $endDateCarbon) { return $asset->changes->whereIn('change_type', ['status_change', 'disposed'])->count() > 0; })->count() }}</p>
        @if($assets->count() > 0)
            <p><strong>Sample Assets:</strong></p>
            <ul class="ml-4">
                @foreach($assets->take(5) as $asset)
                    @php
                        $wasPurchased = $asset->purchase_date >= $startDateCarbon && $asset->purchase_date <= $endDateCarbon;
                        $hasLocationChanges = $asset->changes->where('change_type', 'location_change')->count() > 0;
                        $hasStatusChanges = $asset->changes->whereIn('change_type', ['status_change', 'disposed'])->count() > 0;
                        
                        if ($wasPurchased) {
                            $reason = 'Purchased in period';
                        } elseif ($hasLocationChanges) {
                            $reason = 'Had location changes';
                        } elseif ($hasStatusChanges) {
                            $reason = 'Had status changes';
                        } else {
                            $reason = 'Unknown';
                        }
                    @endphp
                    <li>• {{ $asset->asset_code }} - {{ $asset->name }} - Purchase: {{ $asset->purchase_date->format('Y-m-d') }} - Reason: {{ $reason }}</li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endif

<div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('locations.index') }}" class="text-gray-600 hover:text-red-800 transition-colors">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-3xl font-bold flex items-center gap-2">
                <i class="fas fa-building text-red-800"></i>
                {{ $location->building }} - Date Range View
            </h1>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-sm text-gray-600">
                <i class="fas fa-boxes mr-2"></i>{{ $assets->count() }} assets
            </div>
        </div>
    </div>

    <!-- Location Information -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-building text-red-800"></i>
                <div>
                    <div class="text-sm text-gray-600">Building</div>
                    <div class="font-semibold">{{ $location->building }}</div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <i class="fas fa-layer-group text-red-800"></i>
                <div>
                    <div class="text-sm text-gray-600">Floor</div>
                    <div class="font-semibold">{{ $location->floor }}</div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <i class="fas fa-door-open text-red-800"></i>
                <div>
                    <div class="text-sm text-gray-600">Room</div>
                    <div class="font-semibold">{{ $location->room }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Range Selection -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                    <i class="fas fa-search"></i> View
                </button>
            </div>
        </form>
    </div>

    <!-- Date Range Information -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Assets in Date Range</h2>
            <div class="text-sm text-gray-600">
                {{ $startDateCarbon->format('M d, Y') }} - {{ $endDateCarbon->format('M d, Y') }}
            </div>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900">{{ $assets->count() }}</div>
                <div class="text-sm text-gray-600">Total Assets</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ $assets->where('status', 'Available')->count() }}</div>
                <div class="text-sm text-gray-600">Available</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $assets->where('status', 'In Use')->count() }}</div>
                <div class="text-sm text-gray-600">In Use</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">₱{{ number_format($assets->sum('purchase_cost'), 2) }}</div>
                <div class="text-sm text-gray-600">Total Value</div>
            </div>
        </div>
        
        <div class="mt-4 text-sm text-gray-600">
            <p><strong>Showing assets that:</strong></p>
            <ul class="list-disc list-inside mt-1 space-y-1">
                <li>Were purchased during this date range (if currently in this location)</li>
                <li>Had location changes involving this location during the date range</li>
                <li>Had status changes (lost, disposed) while in this location during the date range</li>
            </ul>
        </div>
    </div>

    <!-- Assets Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">
                Assets in {{ $location->building }} ({{ $startDateCarbon->format('M d, Y') }} - {{ $endDateCarbon->format('M d, Y') }})
            </h2>
            <p class="text-sm text-gray-600 mt-1">Showing assets present in this location during the selected date range</p>
        </div>
        
        @if($assets->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Condition</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Changes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($assets as $asset)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $asset->asset_code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900">{{ $asset->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $asset->category->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $asset->condition === 'Good' ? 'bg-green-100 text-green-800' : 
                                       ($asset->condition === 'Fair' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $asset->condition }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $asset->status === 'Available' ? 'bg-green-100 text-green-800' : 
                                       ($asset->status === 'In Use' ? 'bg-blue-100 text-blue-800' : 
                                       ($asset->status === 'Lost' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                    {{ $asset->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ₱{{ number_format($asset->purchase_cost, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @php
                                    // Check if asset was purchased during this date range
                                    $wasPurchased = $asset->purchase_date >= $startDateCarbon && $asset->purchase_date <= $endDateCarbon;
                                    
                                    // Get asset changes during this date range
                                    $assetChanges = $asset->changes->sortByDesc('created_at');
                                    $latestChange = $assetChanges->count() > 0 ? $assetChanges->first() : null;
                                    
                                    if ($wasPurchased) {
                                        $changeReason = 'Purchased';
                                        $changeClass = 'bg-green-100 text-green-800';
                                    } elseif ($assetChanges->count() > 0) {
                                        $changeReason = $latestChange->getChangeTypeLabel();
                                        $changeClass = match($latestChange->change_type) {
                                            'disposed' => 'bg-red-100 text-red-800',
                                            'status_change' => 'bg-yellow-100 text-yellow-800',
                                            'location_change' => 'bg-blue-100 text-blue-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                    } else {
                                        $changeReason = 'Current Location';
                                        $changeClass = 'bg-gray-100 text-gray-800';
                                    }
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $changeClass }}">
                                    {{ $changeReason }}
                                </span>
                                @if($assetChanges->count() > 0)
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $assetChanges->first()->created_at->format('M d, Y H:i') }}
                                    </div>
                                    @if($latestChange && $latestChange->change_type === 'location_change')
                                        <div class="text-xs text-gray-600 mt-1">
                                            <div class="text-red-600">{{ $latestChange->previous_value ?? 'N/A' }}</div>
                                            <div class="text-gray-400">→</div>
                                            <div class="text-green-600">{{ $latestChange->new_value ?? 'N/A' }}</div>
                                        </div>
                                    @endif
                                @elseif($wasPurchased)
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $asset->purchase_date->format('M d, Y') }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-6 text-center">
            <div class="bg-gray-100 p-4 rounded-full inline-block mb-4">
                <i class="fas fa-box-open text-gray-400 text-2xl"></i>
            </div>
            <p class="text-gray-500 text-sm">No assets found for this date range</p>
        </div>
        @endif
    </div>
</div>

@endsection 