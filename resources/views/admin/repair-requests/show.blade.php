@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-yellow-50 py-4 md:py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 md:mb-8">
            <!-- Back Button -->
            <div class="mb-4">
                <a href="{{ route('admin.repair-requests.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors shadow-sm">
                    <i class="fas fa-arrow-left mr-2"></i> 
                    <span class="hidden sm:inline">Back to Requests</span>
                    <span class="sm:hidden">Back</span>
                </a>
            </div>
            
            <!-- Title and Status -->
            <div class="bg-white rounded-xl shadow-lg p-4 md:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-start sm:items-center space-x-3 sm:space-x-4">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-wrench text-white text-lg sm:text-2xl"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 break-words">
                                Repair Request #{{ $repairRequest->id }}
                            </h1>
                            <p class="text-sm sm:text-base text-gray-600 mt-1">
                                Submitted by {{ $repairRequest->requester->name ?? 'Unknown' }}
                            </p>
                            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                                {{ $repairRequest->created_at->format('M d, Y g:i A') }}
                            </p>
                        </div>
                    </div>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                            'in_progress' => 'bg-orange-100 text-orange-800 border-orange-200',
                            'completed' => 'bg-green-100 text-green-800 border-green-200',
                            'rejected' => 'bg-red-100 text-red-800 border-red-200',
                        ];
                        $statusClass = $statusColors[$repairRequest->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                        $statusDisplay = $repairRequest->status === 'in_progress' ? 'In Progress' : ucfirst($repairRequest->status);
                    @endphp
                    <span class="inline-flex items-center px-3 sm:px-4 py-2 rounded-full text-xs sm:text-sm font-semibold border-2 {{ $statusClass }} self-start sm:self-auto">
                        <i class="fas fa-circle text-xs mr-2"></i>
                        {{ $statusDisplay }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-xl">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-xl">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-4 md:space-y-6">
                <!-- Request Details -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-6 py-4">
                        <h2 class="text-xl font-bold text-white">Request Details</h2>
                    </div>
                    <div class="p-6">
                        @php
                            // Repair requests have dedicated fields, not extracted from notes
                            $priority = ucfirst($repairRequest->urgency_level ?? 'medium');
                            $issue = $repairRequest->issue_description ?? 'No description provided';
                            
                            $priorityColors = [
                                'Low' => 'bg-gray-100 text-gray-800',
                                'Medium' => 'bg-yellow-100 text-yellow-800',
                                'High' => 'bg-orange-100 text-orange-800',
                                'Critical' => 'bg-red-100 text-red-800',
                            ];
                            $priorityClass = $priorityColors[$priority] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Priority Level</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $priorityClass }}">
                                    <i class="fas fa-exclamation-circle mr-2"></i>{{ $priority }}
                                </span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Issue Description</label>
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <p class="text-gray-900 whitespace-pre-wrap">{{ trim($issue) }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                                    <div class="text-gray-900">{{ $repairRequest->department }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Program</label>
                                    <div class="text-gray-900">{{ $repairRequest->program ?? 'N/A' }}</div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date Reported</label>
                                <div class="text-gray-900">{{ $repairRequest->date_reported->format('F j, Y') }}</div>
                            </div>

                            @if($repairRequest->asset && $repairRequest->asset->location)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Asset Location</label>
                                <div class="text-gray-900">
                                    {{ $repairRequest->asset->location->building }} - Floor {{ $repairRequest->asset->location->floor }} - Room {{ $repairRequest->asset->location->room }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Asset Information -->
                @if($repairRequest->asset)
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white">Asset Information</h2>
                    </div>
                    <div class="p-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-mono text-sm font-medium text-blue-800">{{ $repairRequest->asset->asset_code }}</span>
                                <span class="text-xs text-blue-600 bg-blue-100 px-2 py-1 rounded">{{ $repairRequest->asset->category->name ?? 'N/A' }}</span>
                            </div>
                            <div class="text-gray-900 font-medium mb-1">{{ $repairRequest->asset->name }}</div>
                            @if($repairRequest->asset->description)
                            <div class="text-sm text-gray-600 mb-2">{{ $repairRequest->asset->description }}</div>
                            @endif
                            @if($repairRequest->asset->location)
                            <div class="text-sm text-gray-600">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                {{ $repairRequest->asset->location->building }} - Floor {{ $repairRequest->asset->location->floor }} - Room {{ $repairRequest->asset->location->room }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white">Completion Notes</h2>
                    </div>
                    <div class="p-6">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $repairRequest->completion_notes }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-4 md:space-y-6">
                <!-- Actions Card -->
                <div class="bg-white rounded-xl md:rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 md:px-6 py-3 md:py-4">
                        <h2 class="text-lg md:text-xl font-bold text-white flex items-center">
                            <i class="fas fa-tasks mr-2"></i>
                            Admin Actions
                        </h2>
                    </div>
                    <div class="p-4 md:p-6 space-y-3">
                        @if($repairRequest->status === 'pending')
                            <form action="{{ route('admin.repair-requests.approve', $repairRequest) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold py-3 px-4 rounded-lg hover:from-green-600 hover:to-green-700 transition-all shadow-md flex items-center justify-center">
                                    <i class="fas fa-check mr-2"></i>
                                    <span>Approve & Send to GSU</span>
                                </button>
                            </form>
                            
                            <!-- Reject Button with Modal -->
                            <button onclick="openRejectModal()" 
                                    type="button"
                                    class="w-full bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold py-3 px-4 rounded-lg hover:from-red-600 hover:to-red-700 transition-all shadow-md flex items-center justify-center cursor-pointer">
                                <i class="fas fa-times mr-2"></i>
                                <span>Reject Request</span>
                            </button>
                            <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-4 text-center">
                                <i class="fas fa-clock text-yellow-600 text-2xl mb-2"></i>
                                <p class="text-sm text-yellow-800 font-medium">Pending Approval</p>
                                <p class="text-xs text-yellow-700 mt-1">Approve to mark as In Progress for GSU</p>
                            </div>
                        @endif

                        @if($repairRequest->status === 'in_progress')
                            <div class="bg-orange-50 border-2 border-orange-200 rounded-lg p-4 text-center">
                                <i class="fas fa-cog fa-spin text-orange-600 text-3xl mb-2"></i>
                                <p class="text-sm text-orange-800 font-medium">In Progress</p>
                                <p class="text-xs text-orange-700 mt-1">GSU is working on this repair</p>
                            </div>
                        @endif

                        @if($repairRequest->status === 'completed')
                            <div class="bg-green-50 border-2 border-green-200 rounded-lg p-4 text-center">
                                <i class="fas fa-check-circle text-green-600 text-3xl mb-2"></i>
                                <p class="text-sm text-green-800 font-medium">Repair Completed</p>
                                <p class="text-xs text-green-700 mt-1">This repair has been finished by GSU</p>
                            </div>
                        @endif
                        @if($repairRequest->status === 'rejected')
                            <div class="bg-red-50 border-2 border-red-200 rounded-lg p-4 text-center">
                                <i class="fas fa-times-circle text-red-600 text-3xl mb-2"></i>
                                <p class="text-sm text-red-800 font-medium">Request Rejected</p>
                                <p class="text-xs text-red-700 mt-1">This request was not approved</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Requester Info -->
                <div class="bg-white rounded-xl md:rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-4 md:px-6 py-3 md:py-4">
                        <h2 class="text-lg md:text-xl font-bold text-white flex items-center">
                            <i class="fas fa-user mr-2"></i>
                            Requester Info
                        </h2>
                    </div>
                    <div class="p-4 md:p-6 space-y-3">
                        <div>
                            <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">Name</label>
                            <div class="text-sm md:text-base text-gray-900 font-medium">{{ $repairRequest->requester->name ?? 'Unknown' }}</div>
                        </div>
                        <div>
                            <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">Email</label>
                            <div class="text-xs md:text-sm text-gray-900 break-all">{{ $repairRequest->requester->email ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">Submitted</label>
                            <div class="text-xs md:text-sm text-gray-900">{{ $repairRequest->created_at->format('M d, Y g:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Reject Repair Request</h3>
                <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('admin.repair-requests.reject', $repairRequest) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Reason for Rejection <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="rejection_reason" 
                        name="rejection_reason" 
                        rows="4" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                        placeholder="Please provide a reason for rejecting this repair request..."
                        required></textarea>
                </div>
                
                <div class="flex space-x-3">
                    <button type="button" onclick="closeRejectModal()" class="flex-1 bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 bg-red-500 text-white font-semibold py-2 px-4 rounded-lg hover:bg-red-600 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Reject Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openRejectModal() {
    console.log('Opening reject modal');
    document.getElementById('rejectModal').style.display = 'flex';
}

function closeRejectModal() {
    console.log('Closing reject modal');
    document.getElementById('rejectModal').style.display = 'none';
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('rejectModal');
    if (event.target === modal) {
        closeRejectModal();
    }
});
</script>

@endsection
