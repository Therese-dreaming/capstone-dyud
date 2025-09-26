@extends('layouts.admin')

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
</style>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-4">
                <a href="{{ route('maintenance-requests.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Requests
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Maintenance Request Details</h1>
                    <p class="text-lg text-gray-600">Request #{{ $maintenanceRequest->id }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <span class="status-badge status-{{ $maintenanceRequest->status }}">
                    <i class="fas {{
                        $maintenanceRequest->status === 'pending' ? 'fa-clock' : 
                        ($maintenanceRequest->status === 'approved' ? 'fa-check' : 
                        ($maintenanceRequest->status === 'rejected' ? 'fa-times' : 
                        ($maintenanceRequest->status === 'acknowledged' ? 'fa-handshake' : 'fa-question')))
                    }} mr-2"></i>
                    {{ ucfirst($maintenanceRequest->status) }}
                </span>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Request Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information Card -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-info-circle text-blue-700 text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Request Information</h2>
                                <p class="text-blue-100">Basic details of the maintenance request</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">School Year</label>
                                <div class="text-lg font-semibold text-gray-900">{{ $maintenanceRequest->school_year }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                                <div class="text-lg font-semibold text-gray-900">{{ $maintenanceRequest->department }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date Reported</label>
                                <div class="text-lg font-semibold text-gray-900">{{ $maintenanceRequest->date_reported->format('M d, Y') }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Program</label>
                                <div class="text-lg font-semibold text-gray-900">{{ $maintenanceRequest->program ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location & Instructor Card -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover">
                    <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-map-marker-alt text-green-700 text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Location & Instructor</h2>
                                <p class="text-green-100">Where maintenance is needed and who requested it</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                                @if($maintenanceRequest->isSpecificAssetsRequest())
                                    @php
                                        $assetLocations = $maintenanceRequest->getAssetLocations();
                                    @endphp
                                    @if($assetLocations->count() > 0)
                                        @foreach($assetLocations->take(3) as $index => $location)
                                            <div class="mb-2">
                                                <div class="text-lg font-semibold text-gray-900">{{ $location->building ?? 'N/A' }}</div>
                                                <div class="text-sm text-gray-600">Floor {{ $location->floor ?? 'N/A' }} - Room {{ $location->room ?? 'N/A' }}</div>
                                            </div>
                                        @endforeach
                                        @if($assetLocations->count() > 3)
                                            <div class="text-sm text-gray-500">+{{ $assetLocations->count() - 3 }} more locations</div>
                                        @endif
                                    @else
                                        <div class="text-lg font-semibold text-gray-900">Asset-based Request</div>
                                        <div class="text-sm text-gray-600">Location determined by specific assets</div>
                                    @endif
                                @else
                                    @if($maintenanceRequest->location)
                                        <div class="text-lg font-semibold text-gray-900">{{ $maintenanceRequest->location->building }}</div>
                                        <div class="text-sm text-gray-600">Floor {{ $maintenanceRequest->location->floor }} - Room {{ $maintenanceRequest->location->room }}</div>
                                    @else
                                        <div class="text-lg font-semibold text-gray-900">N/A</div>
                                        <div class="text-sm text-gray-600">No location specified</div>
                                    @endif
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Instructor</label>
                                <div class="text-lg font-semibold text-gray-900">{{ $maintenanceRequest->instructor_name }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Requested Assets Card (for asset-based requests) -->
                @if($maintenanceRequest->isSpecificAssetsRequest())
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover">
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-qrcode text-indigo-700 text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Requested Assets</h2>
                                <p class="text-indigo-100">Specific assets included in this maintenance request</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        @php
                            $requestedAssets = $maintenanceRequest->getRequestedAssets();
                            $assetCodes = $maintenanceRequest->getRequestedAssetCodes();
                        @endphp
                        
                        @if($requestedAssets->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($requestedAssets as $asset)
                                    <div class="border rounded-lg p-4 bg-gray-50">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="font-mono text-sm font-semibold text-gray-900">{{ $asset->asset_code }}</div>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $asset->getStatusBadgeClass() }}">
                                                {{ $asset->getStatusLabel() }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-900 font-medium">{{ $asset->name }}</div>
                                        @if($asset->location)
                                            <div class="text-xs text-gray-600 mt-1">
                                                {{ $asset->location->building }} - Floor {{ $asset->location->floor }} - Room {{ $asset->location->room }}
                                            </div>
                                        @else
                                            <div class="text-xs text-gray-500 mt-1">No location assigned</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-gray-500">
                                <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                                <p class="text-sm mb-2">Some requested assets could not be found</p>
                                @if(count($assetCodes) > 0)
                                    <div class="text-xs text-gray-400">
                                        Requested codes: {{ implode(', ', $assetCodes) }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Notes Card -->
                @if($maintenanceRequest->notes)
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover">
                    <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-sticky-note text-purple-700 text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Additional Notes</h2>
                                <p class="text-purple-100">Special instructions or requirements</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="text-gray-900 whitespace-pre-wrap">{{ $maintenanceRequest->notes }}</div>
                    </div>
                </div>
                @endif

                <!-- Admin Notes Card -->
                @if($maintenanceRequest->admin_notes)
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover">
                    <div class="bg-gradient-to-r from-orange-600 to-orange-700 px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-user-shield text-orange-700 text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Admin Notes</h2>
                                <p class="text-orange-100">Additional notes from administrator</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="text-gray-900 whitespace-pre-wrap">{{ $maintenanceRequest->admin_notes }}</div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column: Status & Actions -->
            <div class="space-y-6">
                <!-- Status Timeline Card -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover">
                    <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-history text-gray-700 text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Status Timeline</h2>
                                <p class="text-gray-100">Request progress history</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- Requested -->
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-plus text-blue-600 text-sm"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">Request Submitted</p>
                                    <p class="text-xs text-gray-500">{{ $maintenanceRequest->created_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>

                            @if($maintenanceRequest->approved_at)
                            <!-- Approved -->
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-green-600 text-sm"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">Approved by Admin</p>
                                    <p class="text-xs text-gray-500">{{ $maintenanceRequest->approved_at->format('M d, Y H:i') }}</p>
                                    @if($maintenanceRequest->approvedBy)
                                        <p class="text-xs text-gray-400">by {{ $maintenanceRequest->approvedBy->name }}</p>
                                    @endif
                                </div>
                            </div>
                            @endif

                            @if($maintenanceRequest->acknowledged_at)
                            <!-- Acknowledged -->
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-handshake text-purple-600 text-sm"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">Acknowledged by GSU</p>
                                    <p class="text-xs text-gray-500">{{ $maintenanceRequest->acknowledged_at->format('M d, Y H:i') }}</p>
                                    @if($maintenanceRequest->acknowledgedBy)
                                        <p class="text-xs text-gray-400">by {{ $maintenanceRequest->acknowledgedBy->name }}</p>
                                    @endif
                                </div>
                            </div>
                            @endif

                            @if($maintenanceRequest->rejected_at)
                            <!-- Rejected -->
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-times text-red-600 text-sm"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">Rejected</p>
                                    <p class="text-xs text-gray-500">{{ $maintenanceRequest->rejected_at->format('M d, Y H:i') }}</p>
                                    @if($maintenanceRequest->rejectedBy)
                                        <p class="text-xs text-gray-400">by {{ $maintenanceRequest->rejectedBy->name }}</p>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Requester Information Card -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover">
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-user text-indigo-700 text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Requester</h2>
                                <p class="text-indigo-100">User who submitted the request</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-12 w-12">
                                <div class="h-12 w-12 rounded-full bg-gradient-to-r from-indigo-500 to-indigo-600 flex items-center justify-center">
                                    <span class="text-lg font-medium text-white">{{ substr($maintenanceRequest->requester->name, 0, 2) }}</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-lg font-semibold text-gray-900">{{ $maintenanceRequest->requester->name }}</div>
                                <div class="text-sm text-gray-600">{{ $maintenanceRequest->requester->email ?? 'No email' }}</div>
                                <div class="text-xs text-gray-500">ID: {{ $maintenanceRequest->requester->id_number ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover">
                    <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-cogs text-red-700 text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Actions</h2>
                                <p class="text-red-100">Available actions for this request</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($maintenanceRequest->status === 'pending')
                            @php
                                $locationText = $maintenanceRequest->location ? ($maintenanceRequest->location->building . ' - Floor ' . $maintenanceRequest->location->floor . ' - Room ' . $maintenanceRequest->location->room) : 'Asset-based request';
                            @endphp
                            <div class="space-y-3">
                                <button onclick="openRejectModal({{ $maintenanceRequest->id }}, '{{ $maintenanceRequest->requester->name }}', '{{ $locationText }}')" 
                                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                    <i class="fas fa-times mr-2"></i> Reject Request
                                </button>
                                <button onclick="openApproveModal({{ $maintenanceRequest->id }}, '{{ $maintenanceRequest->requester->name }}', '{{ $locationText }}', '{{ $maintenanceRequest->school_year }}', '{{ $maintenanceRequest->department }}', '{{ $maintenanceRequest->instructor_name }}')" 
                                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                    <i class="fas fa-check mr-2"></i> Approve Request
                                </button>
                            </div>
                        @else
                            <div class="text-center text-gray-500">
                                <i class="fas fa-check-circle text-2xl mb-2"></i>
                                <p class="text-sm">Request has been processed</p>
                                <p class="text-xs text-gray-400 mt-1">Further actions are handled by GSU team</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Maintenance Checklist Card -->
                @if($maintenanceRequest->maintenance_checklist_id)
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover">
                    <div class="bg-gradient-to-r from-teal-600 to-teal-700 px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-clipboard-check text-white text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Maintenance Checklist</h2>
                                <p class="text-teal-100">Associated checklist details</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Checklist ID</label>
                                <div class="text-lg font-semibold text-gray-900">#{{ $maintenanceRequest->maintenance_checklist_id }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ ucfirst($maintenanceRequest->checklist->status ?? 'Unknown') }}
                                </span>
                            </div>
                            <div class="text-center text-gray-500 py-2">
                                <i class="fas fa-info-circle text-lg mb-1"></i>
                                <p class="text-sm">Checklist is managed by GSU team</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <i class="fas fa-times text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 text-center mb-2">Reject Maintenance Request</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 text-center mb-4">
                    Are you sure you want to reject this maintenance request?
                </p>
                <div class="bg-gray-50 rounded-lg p-3 mb-4">
                    <p class="text-sm font-medium text-gray-900" id="rejectRequesterName"></p>
                    <p class="text-xs text-gray-600" id="rejectLocation"></p>
                </div>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason</label>
                        <textarea name="rejection_reason" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                                  placeholder="Please provide a reason for rejection..." required></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeRejectModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                            Reject Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 rounded-full mb-4">
                <i class="fas fa-check text-green-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 text-center mb-2">Approve Maintenance Request</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 text-center mb-4">
                    This will create a maintenance checklist and notify GSU.
                </p>
                <div class="bg-green-50 rounded-lg p-3 mb-4">
                    <p class="text-sm font-medium text-gray-900" id="approveRequesterName"></p>
                    <p class="text-xs text-gray-600" id="approveLocation"></p>
                    <p class="text-xs text-gray-600" id="approveDetails"></p>
                </div>
                <form id="approveForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes (Optional)</label>
                        <textarea name="admin_notes" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                                  placeholder="Add any additional notes for GSU..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeApproveModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                            Approve Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openRejectModal(requestId, requesterName, location) {
    document.getElementById('rejectRequesterName').textContent = requesterName;
    document.getElementById('rejectLocation').textContent = location;
    document.getElementById('rejectForm').action = `/admin/maintenance-requests/${requestId}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

function openApproveModal(requestId, requesterName, location, schoolYear, department, instructor) {
    document.getElementById('approveRequesterName').textContent = requesterName;
    document.getElementById('approveLocation').textContent = location;
    document.getElementById('approveDetails').textContent = `${schoolYear} - ${department} - ${instructor}`;
    document.getElementById('approveForm').action = `/admin/maintenance-requests/${requestId}/approve`;
    document.getElementById('approveModal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const rejectModal = document.getElementById('rejectModal');
    const approveModal = document.getElementById('approveModal');
    
    if (event.target === rejectModal) {
        closeRejectModal();
    }
    if (event.target === approveModal) {
        closeApproveModal();
    }
}
</script>
@endsection
