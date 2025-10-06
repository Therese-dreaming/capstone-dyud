@extends('layouts.gsu')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-yellow-50 py-4 md:py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 md:mb-8">
            <!-- Back Button -->
            <div class="mb-4">
                <a href="{{ route('gsu.repair-requests.index') }}" 
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
                            'approved' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'in_progress' => 'bg-orange-100 text-orange-800 border-orange-200',
                            'acknowledged' => 'bg-orange-100 text-orange-800 border-orange-200',
                            'completed' => 'bg-green-100 text-green-800 border-green-200',
                            'rejected' => 'bg-red-100 text-red-800 border-red-200',
                        ];
                        $statusClass = $statusColors[$repairRequest->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                        
                        // Display friendly status name
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
                            @endif
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
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <div class="text-lg font-semibold text-gray-900">{{ $repairRequest->asset->name }}</div>
                                    <div class="text-sm text-gray-600">Code: <span class="font-mono">{{ $repairRequest->asset->asset_code }}</span></div>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $repairRequest->asset->category->name ?? 'N/A' }}
                                </span>
                            </div>
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

                <!-- Completion Notes (if completed) -->
                @if($repairRequest->status === 'completed' && $repairRequest->completion_notes)
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
                    <div class="bg-gradient-to-r from-red-600 to-red-700 px-4 md:px-6 py-3 md:py-4">
                        <h2 class="text-lg md:text-xl font-bold text-white flex items-center">
                            <i class="fas fa-tasks mr-2"></i>
                            Actions
                        </h2>
                    </div>
                    <div class="p-4 md:p-6 space-y-3">
                        @if($repairRequest->status === 'pending')
                            <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-4 text-center">
                                <i class="fas fa-clock text-yellow-600 text-2xl mb-2"></i>
                                <p class="text-sm text-yellow-800 font-medium">Pending Admin Approval</p>
                                <p class="text-xs text-yellow-700 mt-1">Waiting for admin to approve this repair request</p>
                            </div>
                        @endif

                        @if($repairRequest->status === 'in_progress')
                            <button onclick="document.getElementById('complete-modal').classList.remove('hidden')" 
                                    class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold py-3 px-4 rounded-lg hover:from-green-600 hover:to-green-700 transition-all shadow-md flex items-center justify-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span>Mark as Complete</span>
                            </button>
                            <div class="bg-orange-50 border-2 border-orange-200 rounded-lg p-4 text-center">
                                <i class="fas fa-cog fa-spin text-orange-600 text-2xl mb-2"></i>
                                <p class="text-sm text-orange-800 font-medium">In Progress</p>
                                <p class="text-xs text-orange-700 mt-1">Complete when repair is finished</p>
                            </div>
                        @endif

                        @if($repairRequest->status === 'completed')
                            <div class="bg-green-50 border-2 border-green-200 rounded-lg p-4 text-center">
                                <i class="fas fa-check-circle text-green-600 text-3xl mb-2"></i>
                                <p class="text-sm text-green-800 font-medium">Repair Completed</p>
                                <p class="text-xs text-green-700 mt-1">This repair has been finished</p>
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

<!-- Complete Modal -->
<div id="complete-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-black opacity-50" onclick="document.getElementById('complete-modal').classList.add('hidden')"></div>
        
        <div class="relative bg-white rounded-xl md:rounded-2xl shadow-2xl max-w-lg w-full p-4 md:p-8 max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-4 md:mb-6">
                <h3 class="text-xl md:text-2xl font-bold text-gray-900">Complete Repair Request</h3>
                <button type="button" 
                        onclick="document.getElementById('complete-modal').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600 p-2">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form action="{{ route('gsu.repair-requests.complete', $repairRequest) }}" method="POST">
                @csrf
                
                <div class="mb-6">
                    <label for="completion_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Completion Notes <span class="text-red-500">*</span>
                    </label>
                    <textarea id="completion_notes" 
                              name="completion_notes" 
                              rows="5" 
                              required
                              placeholder="Describe what was done to fix the issue..."
                              class="w-full px-3 md:px-4 py-2 md:py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm md:text-base"></textarea>
                    <p class="mt-2 text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Provide details about the repair work completed
                    </p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="button" 
                            onclick="document.getElementById('complete-modal').classList.add('hidden')"
                            class="w-full sm:flex-1 px-4 md:px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold text-sm md:text-base">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit" 
                            class="w-full sm:flex-1 px-4 md:px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all font-semibold shadow-lg text-sm md:text-base">
                        <i class="fas fa-check-circle mr-2"></i>Complete Repair
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
