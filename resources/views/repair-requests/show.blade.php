@extends('layouts.user')

@section('title', 'Repair Request Details')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-yellow-50 via-orange-50 to-red-50 py-6 md:py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('repair-requests.index') }}" 
               class="inline-flex items-center px-4 py-2 border-2 border-gray-300 rounded-xl text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 transition-all shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i> Back to My Requests
            </a>
        </div>

        <!-- Header Card -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden mb-8 border border-gray-100">
            <div class="bg-gradient-to-r from-yellow-500 via-orange-500 to-red-500 px-6 md:px-8 py-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 backdrop-blur-sm p-4 rounded-xl">
                            <i class="fas fa-wrench text-white text-3xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-white">Repair Request #{{ $repairRequest->id }}</h1>
                            <p class="text-yellow-100 mt-1">Submitted on {{ $repairRequest->created_at->format('F j, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                    <div>
                        @php
                            $statusConfig = [
                                'completed' => ['bg' => 'bg-green-500', 'text' => 'text-white', 'icon' => 'fa-check-circle', 'label' => 'Completed'],
                                'in_progress' => ['bg' => 'bg-blue-500', 'text' => 'text-white', 'icon' => 'fa-cog fa-spin', 'label' => 'In Progress'],
                                'rejected' => ['bg' => 'bg-red-500', 'text' => 'text-white', 'icon' => 'fa-times-circle', 'label' => 'Rejected'],
                                'pending' => ['bg' => 'bg-yellow-400', 'text' => 'text-gray-900', 'icon' => 'fa-clock', 'label' => 'Pending']
                            ];
                            $config = $statusConfig[$repairRequest->status] ?? $statusConfig['pending'];
                        @endphp
                        <span class="inline-flex items-center px-5 py-2.5 rounded-xl text-sm font-bold {{ $config['bg'] }} {{ $config['text'] }} shadow-lg">
                            <i class="fas {{ $config['icon'] }} mr-2"></i>
                            {{ $config['label'] }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Request Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Asset Information -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 hover:shadow-2xl transition-shadow">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-box-open mr-3"></i>
                            Asset Information
                        </h2>
                    </div>
                    <div class="p-6 md:p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-xl border border-blue-100">
                                <label class="block text-xs font-semibold text-blue-600 uppercase tracking-wide mb-2">Asset Code</label>
                                <div class="text-xl font-bold text-gray-900 font-mono">{{ $repairRequest->asset->asset_code }}</div>
                            </div>
                            <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-4 rounded-xl border border-purple-100">
                                <label class="block text-xs font-semibold text-purple-600 uppercase tracking-wide mb-2">Asset Name</label>
                                <div class="text-xl font-bold text-gray-900">{{ $repairRequest->asset->name }}</div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Category</label>
                                <div class="text-lg font-medium text-gray-900">{{ $repairRequest->asset->category->name ?? 'N/A' }}</div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Current Status</label>
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold
                                    {{ $repairRequest->asset->status === 'For Repair' ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' : 
                                       ($repairRequest->asset->status === 'Available' ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-gray-100 text-gray-800 border border-gray-300') }}">
                                    <i class="fas {{ $repairRequest->asset->status === 'For Repair' ? 'fa-tools' : 'fa-check-circle' }} mr-2"></i>
                                    {{ $repairRequest->asset->status }}
                                </span>
                            </div>
                            @if($repairRequest->asset->location)
                            <div class="md:col-span-2 bg-gradient-to-r from-gray-50 to-gray-100 p-4 rounded-xl border border-gray-200">
                                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">
                                    <i class="fas fa-map-marker-alt mr-1"></i> Location
                                </label>
                                <div class="text-lg font-medium text-gray-900">
                                    <i class="fas fa-building text-gray-500 mr-2"></i>{{ $repairRequest->asset->location->building }} - 
                                    <i class="fas fa-layer-group text-gray-500 mr-1 ml-2"></i>Floor {{ $repairRequest->asset->location->floor }} - 
                                    <i class="fas fa-door-open text-gray-500 mr-1 ml-2"></i>Room {{ $repairRequest->asset->location->room }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Issue Details -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 hover:shadow-2xl transition-shadow">
                    <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-exclamation-circle mr-3"></i>
                            Issue Details
                        </h2>
                    </div>
                    <div class="p-6 md:p-8">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">
                                    <i class="fas fa-file-alt mr-1"></i> Issue Description
                                </label>
                                <div class="bg-gradient-to-br from-red-50 to-pink-50 border-2 border-red-200 rounded-xl p-5">
                                    <p class="text-gray-900 text-base leading-relaxed">{{ $repairRequest->issue_description }}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Urgency Level</label>
                                    @php
                                        $urgencyConfig = [
                                            'critical' => ['bg' => 'bg-red-500', 'text' => 'text-white', 'icon' => 'fa-fire', 'border' => 'border-red-600'],
                                            'high' => ['bg' => 'bg-orange-500', 'text' => 'text-white', 'icon' => 'fa-exclamation-triangle', 'border' => 'border-orange-600'],
                                            'medium' => ['bg' => 'bg-yellow-400', 'text' => 'text-gray-900', 'icon' => 'fa-exclamation', 'border' => 'border-yellow-500'],
                                            'low' => ['bg' => 'bg-green-500', 'text' => 'text-white', 'icon' => 'fa-info-circle', 'border' => 'border-green-600']
                                        ];
                                        $urgency = $urgencyConfig[$repairRequest->urgency_level] ?? $urgencyConfig['medium'];
                                    @endphp
                                    <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold {{ $urgency['bg'] }} {{ $urgency['text'] }} border-2 {{ $urgency['border'] }} shadow-md">
                                        <i class="fas {{ $urgency['icon'] }} mr-2"></i>
                                        {{ ucfirst($repairRequest->urgency_level) }}
                                    </span>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Date Reported</label>
                                    <div class="text-lg font-semibold text-gray-900">
                                        <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                                        {{ $repairRequest->date_reported->format('F j, Y') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Completion Notes (if completed) -->
                @if($repairRequest->status === 'completed' && $repairRequest->completion_notes)
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            Completion Notes
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="p-4 bg-green-50 rounded-lg">
                            <p class="text-gray-900">{{ $repairRequest->completion_notes }}</p>
                        </div>
                        <div class="mt-4 text-sm text-gray-600">
                            Completed by {{ $repairRequest->completedBy->name ?? 'GSU Staff' }} on {{ $repairRequest->completed_at->format('F j, Y \a\t g:i A') }}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Rejection Reason (if rejected) -->
                @if($repairRequest->status === 'rejected' && $repairRequest->rejection_reason)
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-times-circle mr-2"></i>
                            Rejection Reason
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="p-4 bg-red-50 rounded-lg">
                            <p class="text-gray-900">{{ $repairRequest->rejection_reason }}</p>
                        </div>
                        <div class="mt-4 text-sm text-gray-600">
                            Rejected by {{ $repairRequest->rejectedBy->name ?? 'Admin' }} on {{ $repairRequest->rejected_at->format('F j, Y \a\t g:i A') }}
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column - Status Timeline -->
            <div class="space-y-6">
                <!-- Request Information -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-500 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            Request Info
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Department</label>
                            <div class="text-gray-900">{{ $repairRequest->department }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Program</label>
                            <div class="text-gray-900">{{ $repairRequest->program ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Instructor</label>
                            <div class="text-gray-900">{{ $repairRequest->instructor_name }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">School Year</label>
                            <div class="text-gray-900">{{ $repairRequest->school_year }}</div>
                        </div>
                    </div>
                </div>

                <!-- Status Timeline -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-history mr-3"></i>
                            Status Timeline
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="flow-root">
                            <ul class="space-y-4">
                                <!-- Created -->
                                <li>
                                    <div class="relative pb-4">
                                        @if($repairRequest->approved_at || $repairRequest->rejected_at || $repairRequest->completed_at)
                                        <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-300" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex items-start space-x-3">
                                            <div class="relative">
                                                <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center ring-4 ring-white shadow-md">
                                                    <i class="fas fa-paper-plane text-white"></i>
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-sm font-semibold text-gray-900">Request Submitted</div>
                                                <div class="text-xs text-gray-500 mt-0.5">
                                                    <i class="fas fa-calendar-alt mr-1"></i>
                                                    {{ $repairRequest->created_at->format('M j, Y') }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    {{ $repairRequest->created_at->format('g:i A') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <!-- Approved -->
                                @if($repairRequest->approved_at)
                                <li>
                                    <div class="relative pb-4">
                                        @if($repairRequest->completed_at)
                                        <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-300" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex items-start space-x-3">
                                            <div class="relative">
                                                <div class="h-10 w-10 rounded-full bg-green-500 flex items-center justify-center ring-4 ring-white shadow-md">
                                                    <i class="fas fa-check text-white"></i>
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-sm font-semibold text-gray-900">Approved by Admin</div>
                                                <div class="text-xs text-gray-500 mt-0.5">
                                                    <i class="fas fa-calendar-alt mr-1"></i>
                                                    {{ $repairRequest->approved_at->format('M j, Y') }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    {{ $repairRequest->approved_at->format('g:i A') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif

                                <!-- In Progress (if approved) -->
                                @if($repairRequest->status === 'in_progress')
                                <li>
                                    <div class="relative pb-4">
                                        <div class="relative flex items-start space-x-3">
                                            <div class="relative">
                                                <div class="h-10 w-10 rounded-full bg-orange-500 flex items-center justify-center ring-4 ring-white shadow-md">
                                                    <i class="fas fa-cog fa-spin text-white"></i>
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-sm font-semibold text-gray-900">Repair In Progress</div>
                                                <div class="text-xs text-orange-600 font-medium mt-0.5">
                                                    <i class="fas fa-tools mr-1"></i>
                                                    GSU is currently working on this repair
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif

                                <!-- Completed -->
                                @if($repairRequest->completed_at)
                                <li>
                                    <div class="relative">
                                        <div class="relative flex items-start space-x-3">
                                            <div class="relative">
                                                <div class="h-10 w-10 rounded-full bg-green-600 flex items-center justify-center ring-4 ring-white shadow-md">
                                                    <i class="fas fa-check-circle text-white"></i>
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-sm font-semibold text-gray-900">Repair Completed</div>
                                                <div class="text-xs text-gray-500 mt-0.5">
                                                    <i class="fas fa-calendar-alt mr-1"></i>
                                                    {{ $repairRequest->completed_at->format('M j, Y') }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    {{ $repairRequest->completed_at->format('g:i A') }}
                                                </div>
                                                @if($repairRequest->completedBy)
                                                <div class="text-xs text-green-600 font-medium mt-1">
                                                    <i class="fas fa-user-check mr-1"></i>
                                                    By {{ $repairRequest->completedBy->name }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif

                                <!-- Rejected -->
                                @if($repairRequest->rejected_at)
                                <li>
                                    <div class="relative">
                                        <div class="relative flex items-start space-x-3">
                                            <div class="relative">
                                                <div class="h-10 w-10 rounded-full bg-red-500 flex items-center justify-center ring-4 ring-white shadow-md">
                                                    <i class="fas fa-times-circle text-white"></i>
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-sm font-semibold text-gray-900">Request Rejected</div>
                                                <div class="text-xs text-gray-500 mt-0.5">
                                                    <i class="fas fa-calendar-alt mr-1"></i>
                                                    {{ $repairRequest->rejected_at->format('M j, Y') }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    {{ $repairRequest->rejected_at->format('g:i A') }}
                                                </div>
                                                @if($repairRequest->rejectedBy)
                                                <div class="text-xs text-red-600 font-medium mt-1">
                                                    <i class="fas fa-user-times mr-1"></i>
                                                    By {{ $repairRequest->rejectedBy->name }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
