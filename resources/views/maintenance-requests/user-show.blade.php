@extends('layouts.user')

@section('title', 'Maintenance Request Details')

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
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-4">
                <a href="{{ route('maintenance-requests.user-index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Back to My Requests
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
                        ($maintenanceRequest->status === 'acknowledged' ? 'fa-handshake' : 
                        ($maintenanceRequest->status === 'in_progress' ? 'fa-cogs' : 
                        ($maintenanceRequest->status === 'completed' ? 'fa-check-circle' : 'fa-question')))))
                    }} mr-2"></i>
                    {{ ucfirst(str_replace('_', ' ', $maintenanceRequest->status)) }}
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
                                <p class="text-blue-100">Basic details of your maintenance request</p>
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
                                <div class="text-lg font-semibold text-gray-900">{{ $maintenanceRequest->location->building }}</div>
                                <div class="text-sm text-gray-600">Floor {{ $maintenanceRequest->location->floor }} - Room {{ $maintenanceRequest->location->room }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Instructor</label>
                                <div class="text-lg font-semibold text-gray-900">{{ $maintenanceRequest->instructor_name }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes Card -->
                @if($maintenanceRequest->notes)
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover">
                    <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-sticky-note text-purple-700 text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Your Notes</h2>
                                <p class="text-purple-100">Special instructions or requirements you provided</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="text-gray-900 whitespace-pre-wrap">{{ $maintenanceRequest->notes }}</div>
                    </div>
                </div>
                @endif

                <!-- Admin Response Card -->
                @if($maintenanceRequest->admin_notes || $maintenanceRequest->rejection_reason)
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover">
                    <div class="bg-gradient-to-r from-orange-600 to-orange-700 px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-user-shield text-orange-700 text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Admin Response</h2>
                                <p class="text-orange-100">Feedback from the administrator</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($maintenanceRequest->admin_notes)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes</label>
                                <div class="text-gray-900 whitespace-pre-wrap bg-green-50 p-3 rounded-lg">{{ $maintenanceRequest->admin_notes }}</div>
                            </div>
                        @endif
                        
                        @if($maintenanceRequest->rejection_reason)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason</label>
                                <div class="text-gray-900 whitespace-pre-wrap bg-red-50 p-3 rounded-lg">{{ $maintenanceRequest->rejection_reason }}</div>
                            </div>
                        @endif
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

                            @if($maintenanceRequest->status === 'in_progress' || $maintenanceRequest->status === 'completed')
                            <!-- In Progress -->
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-cogs text-orange-600 text-sm"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">Maintenance Started</p>
                                    <p class="text-xs text-gray-500">GSU has begun the maintenance process</p>
                                </div>
                            </div>
                            @endif

                            @if($maintenanceRequest->status === 'completed')
                            <!-- Completed -->
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check-circle text-green-600 text-sm"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">Maintenance Completed</p>
                                    <p class="text-xs text-gray-500">All maintenance tasks have been finished</p>
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
                            <div class="text-center text-gray-500">
                                <i class="fas fa-clock text-2xl mb-2"></i>
                                <p class="text-sm">Your request is pending admin review</p>
                            </div>
                        @elseif($maintenanceRequest->status === 'approved' && $maintenanceRequest->maintenance_checklist_id)
                            <a href="{{ route('maintenance-checklists.user-show', $maintenanceRequest->maintenance_checklist_id) }}" 
                               class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                <i class="fas fa-eye mr-2"></i> View Maintenance Checklist
                            </a>
                        @elseif($maintenanceRequest->status === 'in_progress' && $maintenanceRequest->maintenance_checklist_id)
                            <a href="{{ route('maintenance-checklists.user-show', $maintenanceRequest->maintenance_checklist_id) }}" 
                               class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                                <i class="fas fa-cogs mr-2"></i> View Maintenance in Progress
                            </a>
                        @elseif($maintenanceRequest->status === 'completed' && $maintenanceRequest->maintenance_checklist_id)
                            <a href="{{ route('maintenance-checklists.user-show', $maintenanceRequest->maintenance_checklist_id) }}" 
                               class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                <i class="fas fa-check-circle mr-2"></i> View Completed Maintenance
                            </a>
                        @elseif($maintenanceRequest->status === 'rejected')
                            <div class="text-center text-gray-500">
                                <i class="fas fa-times text-2xl mb-2"></i>
                                <p class="text-sm">Your request was rejected</p>
                                <a href="{{ route('maintenance-requests.create') }}" 
                                   class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                    <i class="fas fa-plus mr-2"></i> Submit New Request
                                </a>
                            </div>
                        @else
                            <div class="text-center text-gray-500">
                                <i class="fas fa-info-circle text-2xl mb-2"></i>
                                <p class="text-sm">No actions available</p>
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
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Checklist ID</label>
                                    <div class="text-lg font-semibold text-gray-900">#{{ $maintenanceRequest->maintenance_checklist_id }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{
                                        $maintenanceRequest->checklist->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                        ($maintenanceRequest->checklist->status === 'acknowledged' ? 'bg-blue-100 text-blue-800' : 
                                        ($maintenanceRequest->checklist->status === 'in_progress' ? 'bg-orange-100 text-orange-800' : 
                                        ($maintenanceRequest->checklist->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')))
                                    }}">
                                        {{ ucfirst(str_replace('_', ' ', $maintenanceRequest->checklist->status ?? 'Unknown')) }}
                                    </span>
                                </div>
                            </div>
                            
                            @if($maintenanceRequest->checklist)
                                <div class="border-t pt-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-3">Checklist Information</h4>
                                    <div class="grid grid-cols-1 gap-3 text-sm">
                                        @if($maintenanceRequest->checklist->checked_by)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Checked By:</span>
                                                <span class="font-medium">{{ $maintenanceRequest->checklist->checked_by }}</span>
                                            </div>
                                        @endif
                                        
                                        @if($maintenanceRequest->checklist->gsu_staff)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">GSU Staff:</span>
                                                <span class="font-medium">{{ $maintenanceRequest->checklist->gsu_staff }}</span>
                                            </div>
                                        @endif
                                        
                                        @if($maintenanceRequest->checklist->date_checked)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Date Checked:</span>
                                                <span class="font-medium">{{ $maintenanceRequest->checklist->date_checked->format('M d, Y') }}</span>
                                            </div>
                                        @endif
                                        
                                        @if($maintenanceRequest->checklist->completed_at)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Completed:</span>
                                                <span class="font-medium">{{ $maintenanceRequest->checklist->completed_at->format('M d, Y H:i') }}</span>
                                            </div>
                                        @endif
                                        
                                        @if($maintenanceRequest->checklist->has_missing_assets)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Missing Assets:</span>
                                                <span class="font-medium text-orange-600">Yes</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($maintenanceRequest->checklist->status === 'completed' && $maintenanceRequest->checklist->statusSummary)
                                    <div class="border-t pt-4">
                                        <h4 class="text-sm font-medium text-gray-700 mb-3">Asset Summary</h4>
                                        <div class="grid grid-cols-2 gap-3 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Total Assets:</span>
                                                <span class="font-medium">{{ $maintenanceRequest->checklist->statusSummary['total'] }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">OK:</span>
                                                <span class="font-medium text-green-600">{{ $maintenanceRequest->checklist->statusSummary['ok'] }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">For Repair:</span>
                                                <span class="font-medium text-orange-600">{{ $maintenanceRequest->checklist->statusSummary['repair'] }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">For Replacement:</span>
                                                <span class="font-medium text-red-600">{{ $maintenanceRequest->checklist->statusSummary['replacement'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                            
                            <a href="{{ route('maintenance-checklists.user-show', $maintenanceRequest->maintenance_checklist_id) }}" 
                               class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-colors">
                                <i class="fas fa-external-link-alt mr-2"></i> View Full Checklist
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
