@extends('layouts.user')

@section('title', 'My Assets')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-600 to-blue-700 rounded-full mb-4">
                <i class="fas fa-boxes text-white text-2xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">My Assets</h1>
            <p class="text-lg text-gray-600">Assets in locations you manage</p>
        </div>

        <!-- Owned Locations Summary -->
        @if($ownedLocations->count() > 0)
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-map-marker-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">Your Managed Locations</h2>
                        <p class="text-green-100">{{ $ownedLocations->count() }} location(s) under your management</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($ownedLocations as $location)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center">
                            <i class="fas fa-building text-green-600 mr-3"></i>
                            <div>
                                <div class="font-semibold text-gray-900">{{ $location->building }}</div>
                                <div class="text-sm text-gray-600">Floor {{ $location->floor }} - Room {{ $location->room }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $location->assets->count() }} assets</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Assets Table -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-boxes text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">Asset Inventory</h2>
                            <p class="text-blue-100">Assets in your managed locations</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-white" id="totalCount">{{ $assets->total() }}</div>
                        <div class="text-blue-100 text-sm">Total Assets</div>
                    </div>
                </div>
                
                @if($ownedLocations->count() > 1)
                <!-- Tab Navigation -->
                <div class="flex space-x-1 bg-white bg-opacity-20 p-1 rounded-lg">
                    <button onclick="filterAssets('all')" 
                            class="tab-button flex-1 px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200 bg-white text-blue-900 shadow-sm" 
                            id="tab-all">
                        <i class="fas fa-list mr-2"></i>All Assets
                        <span class="ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-600 rounded-full" id="count-all">{{ $assets->count() }}</span>
                    </button>
                    @foreach($ownedLocations as $location)
                    <button onclick="filterAssets('location-{{ $location->id }}')" 
                            class="tab-button flex-1 px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200 text-white hover:text-blue-100" 
                            id="tab-location-{{ $location->id }}">
                        <i class="fas fa-building mr-2"></i>{{ $location->building }} R{{ $location->room }}
                        <span class="ml-2 px-2 py-1 text-xs bg-white bg-opacity-20 text-white rounded-full" id="count-location-{{ $location->id }}">{{ $location->assets->count() }}</span>
                    </button>
                    @endforeach
                </div>
                @endif
            </div>

            @if($assets->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($assets as $asset)
                        <tr class="asset-row hover:bg-gray-50 transition-colors duration-200" 
                            data-location-id="{{ $asset->location_id }}"
                            data-location-name="{{ $asset->location->building ?? '' }} R{{ $asset->location->room ?? '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $asset->asset_code }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $asset->name }}</div>
                                @if($asset->description)
                                <div class="text-sm text-gray-500">{{ Str::limit($asset->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $asset->category->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $asset->location->building ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">Floor {{ $asset->location->floor ?? 'N/A' }} - Room {{ $asset->location->room ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{
                                    $asset->status === 'Available' ? 'bg-green-100 text-green-800' : 
                                    ($asset->status === 'In Use' ? 'bg-blue-100 text-blue-800' : 
                                    ($asset->status === 'Under Maintenance' ? 'bg-yellow-100 text-yellow-800' : 
                                    ($asset->status === 'Lost' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')))
                                }}">
                                    {{ $asset->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('user-assets.show', $asset) }}" 
                                   class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                    <i class="fas fa-eye mr-1"></i> View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                {{ $assets->links() }}
            </div>
            @else
            <div class="p-12 text-center">
                <div class="flex flex-col items-center">
                    <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No assets found</h3>
                    @if($ownedLocations->isEmpty())
                    <p class="text-gray-500 mb-4">You don't have any managed locations assigned to you.</p>
                    <p class="text-sm text-gray-400">Contact your administrator to get locations assigned to you.</p>
                    @else
                    <p class="text-gray-500">There are no assets in your managed locations yet.</p>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Quick Actions -->
        @if($ownedLocations->count() > 0)
        <div class="mt-8 text-center">
            <a href="{{ route('maintenance-requests.create') }}" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                <i class="fas fa-tools mr-2"></i> Submit Maintenance Request
            </a>
        </div>
        @endif
    </div>
</div>

<style>
    .tab-button {
        position: relative;
        overflow: hidden;
    }
    .tab-button:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    .tab-button:hover:before {
        left: 100%;
    }
    .asset-row {
        transition: all 0.3s ease;
    }
    .asset-row[style*="display: none"] {
        opacity: 0;
        transform: translateY(-10px);
    }
</style>

<script>
// Tab filtering functionality
let currentFilter = 'all';

function filterAssets(filter) {
    currentFilter = filter;
    const rows = document.querySelectorAll('.asset-row');
    const tabs = document.querySelectorAll('.tab-button');
    
    // Update tab styles
    tabs.forEach(tab => {
        tab.classList.remove('bg-white', 'text-blue-900', 'shadow-sm');
        tab.classList.add('text-white', 'hover:text-blue-100');
        
        // Update count badge styles
        const badge = tab.querySelector('span:last-child');
        if (badge) {
            badge.classList.remove('bg-blue-100', 'text-blue-600');
            badge.classList.add('bg-white', 'bg-opacity-20', 'text-white');
        }
    });
    
    const activeTab = document.getElementById(`tab-${filter}`);
    if (activeTab) {
        activeTab.classList.remove('text-white', 'hover:text-blue-100');
        activeTab.classList.add('bg-white', 'text-blue-900', 'shadow-sm');
        
        // Update active count badge styles
        const activeBadge = activeTab.querySelector('span:last-child');
        if (activeBadge) {
            activeBadge.classList.remove('bg-white', 'bg-opacity-20', 'text-white');
            activeBadge.classList.add('bg-blue-100', 'text-blue-600');
        }
    }
    
    // Filter rows
    let visibleCount = 0;
    rows.forEach(row => {
        const locationId = row.getAttribute('data-location-id');
        
        if (filter === 'all' || filter === `location-${locationId}`) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update total count display
    document.getElementById('totalCount').textContent = visibleCount;
    
    // Update empty state message
    updateEmptyState(visibleCount, filter);
}

function updateEmptyState(visibleCount, filter) {
    const tbody = document.querySelector('tbody');
    const existingEmptyRow = document.getElementById('empty-state-row');
    
    if (visibleCount === 0) {
        // Remove existing empty row if it exists
        if (existingEmptyRow) {
            existingEmptyRow.remove();
        }
        
        // Create new empty state row
        const emptyRowHtml = `
            <tr id="empty-state-row">
                <td colspan="6" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center">
                        <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No assets found</h3>
                        <p class="text-gray-500">${getFilterMessage(filter)}</p>
                    </div>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', emptyRowHtml);
    } else if (existingEmptyRow) {
        // Remove empty state if assets are visible
        existingEmptyRow.remove();
    }
}

function getFilterMessage(filter) {
    if (filter === 'all') {
        return 'No assets found in your managed locations.';
    } else if (filter.startsWith('location-')) {
        const locationId = filter.replace('location-', '');
        const row = document.querySelector(`[data-location-id="${locationId}"]`);
        const locationName = row ? row.getAttribute('data-location-name') : 'this location';
        return `No assets found in ${locationName}.`;
    }
    return 'No assets match the current filter.';
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set initial counts and state
    updateCounts();
});

function updateCounts() {
    const rows = document.querySelectorAll('.asset-row');
    const locationCounts = {};
    let totalCount = 0;
    
    rows.forEach(row => {
        const locationId = row.getAttribute('data-location-id');
        totalCount++;
        
        if (locationId) {
            locationCounts[locationId] = (locationCounts[locationId] || 0) + 1;
        }
    });
    
    // Update all count
    const allCountElement = document.getElementById('count-all');
    if (allCountElement) {
        allCountElement.textContent = totalCount;
    }
    
    // Update location-specific counts
    Object.keys(locationCounts).forEach(locationId => {
        const countElement = document.getElementById(`count-location-${locationId}`);
        if (countElement) {
            countElement.textContent = locationCounts[locationId];
        }
    });
}
</script>
@endsection
