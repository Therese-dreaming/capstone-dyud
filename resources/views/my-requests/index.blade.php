@extends('layouts.user')

@section('title', 'My Requests')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-600 to-blue-700 rounded-full mb-4">
                <i class="fas fa-history text-white text-2xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">My Requests</h1>
            <p class="text-lg text-gray-600">Track the status of your maintenance and repair requests</p>
        </div>

        <!-- Request Type Tabs -->
        <div class="mb-8">
            <div class="flex justify-center">
                <div class="bg-white rounded-xl shadow-lg p-2 inline-flex">
                    <button id="maintenance-tab" 
                            onclick="showTab('maintenance')" 
                            class="tab-button active px-6 py-3 rounded-lg font-semibold transition-all duration-200">
                        <i class="fas fa-tools mr-2"></i>
                        Maintenance Requests
                        <span class="ml-2 bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">{{ $maintenanceRequests->count() }}</span>
                    </button>
                    <button id="repair-tab" 
                            onclick="showTab('repair')" 
                            class="tab-button px-6 py-3 rounded-lg font-semibold transition-all duration-200">
                        <i class="fas fa-wrench mr-2"></i>
                        Repair Requests
                        <span class="ml-2 bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs">{{ $repairRequests->count() }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Maintenance Requests Tab -->
        <div id="maintenance-content" class="tab-content">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Maintenance Requests</h2>
                <a href="{{ route('maintenance-requests.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i> New Maintenance Request
                </a>
            </div>

            @if($maintenanceRequests->count() > 0)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location/Assets</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($maintenanceRequests as $request)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">#{{ $request->id }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-tools mr-1"></i> Maintenance
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($request->location)
                                        <div class="text-sm text-gray-900">{{ $request->location->building }}</div>
                                        <div class="text-sm text-gray-500">Floor {{ $request->location->floor }}, Room {{ $request->location->room }}</div>
                                    @else
                                        <div class="text-sm text-gray-900">Specific Assets</div>
                                        <div class="text-sm text-gray-500">{{ count($request->getRequestedAssetCodes()) }} assets</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $request->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                           ($request->status === 'in_progress' ? 'bg-orange-100 text-orange-800' : 
                                           ($request->status === 'approved' ? 'bg-blue-100 text-blue-800' :
                                           ($request->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'))) }}">
                                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $request->created_at->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('maintenance-requests.user-show', $request) }}" 
                                           class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </a>
                                        
                                        <!-- Request Repair for Asset-based Maintenance -->
                                        @if($request->isSpecificAssetsRequest())
                                            @php
                                                $assetCodes = $request->getRequestedAssetCodes();
                                                $availableAssets = \App\Models\Asset::whereIn('asset_code', $assetCodes)
                                                    ->where('status', '!=', 'Disposed')
                                                    ->pluck('asset_code')
                                                    ->toArray();
                                            @endphp
                                            @if(count($availableAssets) > 0)
                                                <div class="relative" x-data="{ open: false }">
                                                    <button @click="open = !open" 
                                                            class="inline-flex items-center px-2 py-1 text-xs font-medium text-orange-700 bg-orange-100 hover:bg-orange-200 rounded transition-colors">
                                                        <i class="fas fa-wrench mr-1"></i> Repair
                                                        <i class="fas fa-chevron-down ml-1 text-xs"></i>
                                                    </button>
                                                    
                                                    <div x-show="open" @click.away="open = false" 
                                                         x-transition:enter="transition ease-out duration-100"
                                                         x-transition:enter-start="transform opacity-0 scale-95"
                                                         x-transition:enter-end="transform opacity-100 scale-100"
                                                         class="absolute right-0 mt-1 w-40 bg-white rounded-md shadow-lg border border-gray-200 z-10"
                                                         style="display: none;">
                                                        <div class="py-1">
                                                            @foreach($availableAssets as $assetCode)
                                                                <a href="{{ route('repair-requests.create', ['asset_code' => $assetCode]) }}" 
                                                                   class="block px-3 py-1 text-xs text-gray-700 hover:bg-orange-50">
                                                                    <i class="fas fa-wrench mr-1 text-orange-600"></i>
                                                                    {{ $assetCode }}
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @elseif(count($assetCodes) > 0)
                                                <span class="inline-flex items-center px-2 py-1 text-xs text-gray-500 bg-gray-100 rounded">
                                                    <i class="fas fa-ban mr-1"></i> Disposed
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                <i class="fas fa-tools text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Maintenance Requests</h3>
                <p class="text-gray-600 mb-4">You haven't submitted any maintenance requests yet.</p>
                <a href="{{ route('maintenance-requests.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i> Submit First Request
                </a>
            </div>
            @endif
        </div>

        <!-- Repair Requests Tab -->
        <div id="repair-content" class="tab-content hidden">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Repair Requests</h2>
                <a href="{{ route('repair-requests.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i> New Repair Request
                </a>
            </div>

            @if($repairRequests->count() > 0)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issue</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Urgency</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($repairRequests as $request)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">#{{ $request->id }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $request->asset->asset_code }}</div>
                                    <div class="text-sm text-gray-500">{{ $request->asset->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ Str::limit($request->issue_description, 50) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $request->urgency_level === 'critical' ? 'bg-red-100 text-red-800' : 
                                           ($request->urgency_level === 'high' ? 'bg-orange-100 text-orange-800' : 
                                           ($request->urgency_level === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')) }}">
                                        {{ ucfirst($request->urgency_level) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $request->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                           ($request->status === 'in_progress' ? 'bg-orange-100 text-orange-800' : 
                                           ($request->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $request->date_reported->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('repair-requests.show', $request) }}" 
                                       class="text-orange-600 hover:text-orange-900">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                <i class="fas fa-wrench text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Repair Requests</h3>
                <p class="text-gray-600 mb-4">You haven't submitted any repair requests yet.</p>
                <a href="{{ route('repair-requests.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i> Submit First Request
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .tab-button {
        color: #6b7280;
        background-color: transparent;
    }
    
    .tab-button.active {
        color: #1f2937;
        background-color: #f3f4f6;
    }
    
    .tab-button:hover {
        color: #374151;
        background-color: #f9fafb;
    }
    
    .tab-content {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    function showTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Remove active class from all tab buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active');
        });
        
        // Show selected tab content
        document.getElementById(tabName + '-content').classList.remove('hidden');
        
        // Add active class to selected tab button
        document.getElementById(tabName + '-tab').classList.add('active');
    }
</script>
@endsection
