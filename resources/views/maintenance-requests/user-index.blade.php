@extends('layouts.user')

@section('title', 'My Maintenance Requests')

@section('content')
<style>
    .animate-fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .card-hover {
        transition: all 0.3s ease;
    }
    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 600;
    }
    .status-pending { background-color: #fef3c7; color: #92400e; }
    .status-approved { background-color: #dbeafe; color: #1e40af; }
    .status-rejected { background-color: #fee2e2; color: #dc2626; }
    .status-acknowledged { background-color: #d1fae5; color: #065f46; }
    .status-in_progress { background-color: #fef3c7; color: #d97706; }
    .status-completed { background-color: #d1fae5; color: #047857; }
</style>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-600 to-blue-700 rounded-full mb-4">
                <i class="fas fa-history text-white text-2xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">My Maintenance Requests</h1>
            <p class="text-lg text-gray-600">Track the status of your submitted maintenance requests</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $requests->where('status', 'pending')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-check text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Approved</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $requests->where('status', 'approved')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                        <i class="fas fa-cogs text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">In Progress</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $requests->where('status', 'in_progress')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Completed</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $requests->where('status', 'completed')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Table Header -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">Request History</h2>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-filter text-gray-400"></i>
                        <span class="text-sm text-gray-600">Total: {{ $requests->total() }}</span>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($requests as $req)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <!-- Request Details -->
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <div class="text-sm font-medium text-gray-900">{{ $req->school_year }}</div>
                                    <div class="text-sm text-gray-600">{{ $req->department }}</div>
                                    @if($req->program)
                                        <div class="text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded-full inline-block">{{ $req->program }}</div>
                                    @endif
                                </div>
                            </td>

                            <!-- Asset Code -->
                            <td class="px-6 py-4">
                                @if($req->isSpecificAssetsRequest())
                                    @php
                                        $assetCodes = $req->getRequestedAssetCodes();
                                    @endphp
                                    @if(count($assetCodes) > 0)
                                        <div class="space-y-1">
                                            @foreach($assetCodes as $index => $code)
                                                @if($index < 2)
                                                    <div class="text-sm font-mono text-gray-900 bg-gray-100 px-2 py-1 rounded">{{ $code }}</div>
                                                @elseif($index === 2)
                                                    <div class="text-xs text-gray-500">+{{ count($assetCodes) - 2 }} more</div>
                                                    @break
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">No assets specified</span>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-500">Location-based</span>
                                @endif
                            </td>

                            <!-- Location -->
                            <td class="px-6 py-4">
                                @if($req->isSpecificAssetsRequest())
                                    @php
                                        $assetLocations = $req->getAssetLocations();
                                    @endphp
                                    @if($assetLocations->count() > 0)
                                        <div class="space-y-1">
                                            @foreach($assetLocations->take(2) as $index => $location)
                                                <div>
                                                    <div class="text-sm text-gray-900">{{ $location->building ?? 'N/A' }}</div>
                                                    <div class="text-sm text-gray-600">Floor {{ $location->floor ?? 'N/A' }} - Room {{ $location->room ?? 'N/A' }}</div>
                                                </div>
                                            @endforeach
                                            @if($assetLocations->count() > 2)
                                                <div class="text-xs text-gray-500">+{{ $assetLocations->count() - 2 }} more locations</div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">No location data</span>
                                    @endif
                                @else
                                    <div class="text-sm text-gray-900">{{ optional($req->location)->building ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-600">Floor {{ optional($req->location)->floor ?? 'N/A' }} - Room {{ optional($req->location)->room ?? 'N/A' }}</div>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{
                                    $req->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                    ($req->status === 'approved' ? 'bg-blue-100 text-blue-800' : 
                                    ($req->status === 'rejected' ? 'bg-red-100 text-red-800' : 
                                    ($req->status === 'acknowledged' ? 'bg-green-100 text-green-800' : 
                                    ($req->status === 'in_progress' ? 'bg-orange-100 text-orange-800' : 
                                    ($req->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')))))
                                }}">
                                    <i class="fas {{
                                        $req->status === 'pending' ? 'fa-clock' : 
                                        ($req->status === 'approved' ? 'fa-check' : 
                                        ($req->status === 'rejected' ? 'fa-times' : 
                                        ($req->status === 'acknowledged' ? 'fa-handshake' : 
                                        ($req->status === 'in_progress' ? 'fa-cogs' : 
                                        ($req->status === 'completed' ? 'fa-check-circle' : 'fa-question')))))
                                    }} mr-1"></i>
                                    {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                                </span>
                                
                                @if($req->status === 'rejected' && $req->rejection_reason)
                                    <div class="mt-2 text-xs text-red-600 bg-red-50 p-2 rounded">
                                        <strong>Reason:</strong> {{ $req->rejection_reason }}
                                    </div>
                                @endif
                                
                                @if($req->status === 'approved' && $req->admin_notes)
                                    <div class="mt-2 text-xs text-blue-600 bg-blue-50 p-2 rounded">
                                        <strong>Admin Notes:</strong> {{ $req->admin_notes }}
                                    </div>
                                @endif
                            </td>

                            <!-- Submitted Date -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $req->created_at->format('M d, Y') }}
                                <div class="text-xs text-gray-500">{{ $req->created_at->format('H:i') }}</div>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <!-- View Details Link -->
                                    <a href="{{ route('maintenance-requests.user-show', $req) }}" 
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </a>
                                    
                                    @if($req->status === 'approved' && $req->maintenance_checklist_id)
                                        <a href="{{ route('maintenance-checklists.user-show', $req->maintenance_checklist_id) }}" 
                                           class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                            <i class="fas fa-clipboard-check mr-1"></i> Checklist
                                        </a>
                                    @elseif($req->status === 'in_progress' && $req->maintenance_checklist_id)
                                        <a href="{{ route('maintenance-checklists.user-show', $req->maintenance_checklist_id) }}" 
                                           class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                                            <i class="fas fa-cogs mr-1"></i> In Progress
                                        </a>
                                    @elseif($req->status === 'completed' && $req->maintenance_checklist_id)
                                        <a href="{{ route('maintenance-checklists.user-show', $req->maintenance_checklist_id) }}" 
                                           class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                            <i class="fas fa-check-circle mr-1"></i> Completed
                                        </a>
                                    @elseif($req->status === 'pending')
                                        <span class="text-gray-500 text-sm">Awaiting review</span>
                                    @endif
                                    
                                    <!-- Request Repair Dropdown for Asset-based Requests -->
                                    @if($req->isSpecificAssetsRequest())
                                        @php
                                            $assetCodes = $req->getRequestedAssetCodes();
                                            $availableAssets = \App\Models\Asset::whereIn('asset_code', $assetCodes)
                                                ->where('status', '!=', 'Disposed')
                                                ->pluck('asset_code')
                                                ->toArray();
                                        @endphp
                                        @if(count($availableAssets) > 0)
                                            <div class="relative" x-data="{ open: false }">
                                                <button @click="open = !open" 
                                                        class="inline-flex items-center px-3 py-2 border border-orange-300 text-sm leading-4 font-medium rounded-md text-orange-700 bg-orange-50 hover:bg-orange-100 transition-colors">
                                                    <i class="fas fa-wrench mr-1"></i> Request Repair
                                                    <i class="fas fa-chevron-down ml-1 text-xs"></i>
                                                </button>
                                                
                                                <div x-show="open" @click.away="open = false" 
                                                     x-transition:enter="transition ease-out duration-100"
                                                     x-transition:enter-start="transform opacity-0 scale-95"
                                                     x-transition:enter-end="transform opacity-100 scale-100"
                                                     x-transition:leave="transition ease-in duration-75"
                                                     x-transition:leave-start="transform opacity-100 scale-100"
                                                     x-transition:leave-end="transform opacity-0 scale-95"
                                                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-10"
                                                     style="display: none;">
                                                    <div class="py-1">
                                                        @foreach($availableAssets as $assetCode)
                                                            <a href="{{ route('repair-requests.create', ['asset_code' => $assetCode]) }}" 
                                                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-900">
                                                                <i class="fas fa-wrench mr-2 text-orange-600"></i>
                                                                {{ $assetCode }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif(count($assetCodes) > 0)
                                            <span class="inline-flex items-center px-3 py-2 text-sm text-gray-500 bg-gray-100 rounded-md">
                                                <i class="fas fa-ban mr-1"></i> Assets Disposed
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No maintenance requests found</h3>
                                    <p class="text-gray-500 mb-4">You haven't submitted any maintenance requests yet.</p>
                                    <a href="{{ route('maintenance-requests.create') }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                        <i class="fas fa-plus mr-2"></i> Submit Request
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
