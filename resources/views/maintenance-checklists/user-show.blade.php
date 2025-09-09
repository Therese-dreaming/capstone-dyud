@extends('layouts.user')

@section('title', 'Maintenance Checklist Details')

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
                    <h1 class="text-3xl font-bold text-gray-900">Maintenance Checklist Details</h1>
                    <p class="text-lg text-gray-600">{{ $checklist->room_number }} - {{ $checklist->school_year }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{
                    $checklist->status === 'created' ? 'bg-gray-100 text-gray-800' : 
                    ($checklist->status === 'acknowledged' ? 'bg-blue-100 text-blue-800' : 
                    ($checklist->status === 'in_progress' ? 'bg-orange-100 text-orange-800' : 
                    ($checklist->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')))
                }}">
                    <i class="fas {{
                        $checklist->status === 'created' ? 'fa-plus' : 
                        ($checklist->status === 'acknowledged' ? 'fa-handshake' : 
                        ($checklist->status === 'in_progress' ? 'fa-cogs' : 
                        ($checklist->status === 'completed' ? 'fa-check-circle' : 'fa-question')))
                    }} mr-2"></i>
                    {{ ucfirst(str_replace('_', ' ', $checklist->status)) }}
                </span>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Checklist Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information Card -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-info-circle text-white text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Checklist Information</h2>
                                <p class="text-blue-100">Basic details of the maintenance checklist</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">School Year</label>
                                <div class="text-lg font-semibold text-gray-900">{{ $checklist->school_year }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                                <div class="text-lg font-semibold text-gray-900">{{ $checklist->department }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date Reported</label>
                                <div class="text-lg font-semibold text-gray-900">{{ $checklist->date_reported->format('M d, Y') }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Program</label>
                                <div class="text-lg font-semibold text-gray-900">{{ $checklist->program ?? 'N/A' }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                                <div class="text-lg font-semibold text-gray-900">
                                    @if($checklist->location)
                                        {{ $checklist->location->building }} - Floor {{ $checklist->location->floor }} - Room {{ $checklist->location->room }}
                                    @else
                                        {{ $checklist->room_number }}
                                    @endif
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Instructor</label>
                                <div class="text-lg font-semibold text-gray-900">{{ $checklist->instructor }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Card (for non-completed checklists) -->
                @if($checklist->status !== 'completed')
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover">
                    <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-chart-line text-white text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Progress Status</h2>
                                <p class="text-green-100">Current maintenance progress</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        @php
                            $progress = $checklist->scanning_progress;
                        @endphp
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ $progress['total'] }}</div>
                                <div class="text-sm text-blue-800">Total Assets</div>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ $progress['scanned'] }}</div>
                                <div class="text-sm text-green-800">Scanned</div>
                            </div>
                            <div class="text-center p-4 bg-red-50 rounded-lg">
                                <div class="text-2xl font-bold text-red-600">{{ $progress['missing'] }}</div>
                                <div class="text-sm text-red-800">Missing</div>
                            </div>
                            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600">{{ $progress['remaining'] }}</div>
                                <div class="text-sm text-yellow-800">Remaining</div>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div class="bg-blue-600 h-4 rounded-full transition-all duration-300" 
                                 style="width: {{ $progress['percentage'] }}%"></div>
                        </div>
                        <p class="text-sm text-gray-600 mt-2 text-center">{{ $progress['percentage'] }}% Complete</p>
                    </div>
                </div>
                @endif

                <!-- Final Status Summary (for completed checklists) -->
                @if($checklist->status === 'completed')
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover">
                    <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-check-circle text-white text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Final Status Summary</h2>
                                <p class="text-green-100">Asset condition summary after maintenance</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        @php
                            $summary = $checklist->status_summary;
                        @endphp
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ $summary['ok'] }}</div>
                                <div class="text-sm text-green-800">OK</div>
                            </div>
                            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600">{{ $summary['repair'] }}</div>
                                <div class="text-sm text-yellow-800">For Repair</div>
                            </div>
                            <div class="text-center p-4 bg-red-50 rounded-lg">
                                <div class="text-2xl font-bold text-red-600">{{ $summary['replacement'] }}</div>
                                <div class="text-sm text-red-800">For Replacement</div>
                            </div>
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ $summary['total'] }}</div>
                                <div class="text-sm text-blue-800">Total Items</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Maintenance Items Table -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover">
                    <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-list text-white text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Maintenance Items</h2>
                                <p class="text-purple-100">Assets and items being maintained</p>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Particulars/Items</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">QTY</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scan Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($checklist->items as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            @if($item->asset_code)
                                                <span class="font-mono text-blue-600">{{ $item->asset_code }}</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $item->particulars }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $item->start_status === 'OK' ? 'bg-green-100 text-green-800' : 
                                                   ($item->start_status === 'FOR REPAIR' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                {{ $item->start_status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->status_class }}">
                                                {{ $item->end_status ?? 'Not Set' }}
                                            </span>
                                            @php
                                                $resolvedLabel = null;
                                                $resolvedClass = null;
                                                if (($item->end_status === 'UNVERIFIED') && optional($item->asset)->status) {
                                                    if (in_array($item->asset->status, [
                                                        \App\Models\Asset::STATUS_AVAILABLE,
                                                        \App\Models\Asset::STATUS_FOR_REPAIR,
                                                        \App\Models\Asset::STATUS_FOR_MAINTENANCE,
                                                    ], true)) {
                                                        $resolvedLabel = 'Resolved: Found';
                                                        $resolvedClass = 'bg-green-100 text-green-800';
                                                    } elseif ($item->asset->status === \App\Models\Asset::STATUS_LOST) {
                                                        $resolvedLabel = 'Resolved: Lost';
                                                        $resolvedClass = 'bg-red-100 text-red-800';
                                                    }
                                                }
                                            @endphp
                                            @if($resolvedLabel)
                                                <div class="mt-1">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $resolvedClass }}">
                                                        {{ $resolvedLabel }}
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($item->is_scanned)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    <i class="fas fa-check"></i> Scanned
                                                </span>
                                            @elseif($item->is_missing)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    <i class="fas fa-times"></i> Missing
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-clock"></i> Pending
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column: Status & Footer Information -->
            <div class="space-y-6">
                <!-- Status Timeline Card -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover">
                    <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-history text-white text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Status Timeline</h2>
                                <p class="text-gray-100">Checklist progress history</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- Created -->
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-plus text-blue-600 text-sm"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">Checklist Created</p>
                                    <p class="text-xs text-gray-500">{{ $checklist->created_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>

                            @if($checklist->acknowledged_at)
                            <!-- Acknowledged -->
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-handshake text-purple-600 text-sm"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">Acknowledged by GSU</p>
                                    <p class="text-xs text-gray-500">{{ $checklist->acknowledged_at->format('M d, Y H:i') }}</p>
                                    @if($checklist->acknowledgedBy)
                                        <p class="text-xs text-gray-400">by {{ $checklist->acknowledgedBy->name }}</p>
                                    @endif
                                </div>
                            </div>
                            @endif

                            @if($checklist->status === 'in_progress' || $checklist->status === 'completed')
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

                            @if($checklist->status === 'completed')
                            <!-- Completed -->
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check-circle text-green-600 text-sm"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">Maintenance Completed</p>
                                    <p class="text-xs text-gray-500">{{ $checklist->completed_at->format('M d, Y H:i') }}</p>
                                    @if($checklist->completedBy)
                                        <p class="text-xs text-gray-400">by {{ $checklist->completedBy->name }}</p>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Footer Information Card -->
                @if($checklist->checked_by || $checklist->gsu_staff)
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover">
                    <div class="bg-gradient-to-r from-teal-600 to-teal-700 px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-clipboard-check text-white text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Completion Details</h2>
                                <p class="text-teal-100">Maintenance completion information</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @if($checklist->checked_by)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Checked By</label>
                                    <div class="text-lg font-semibold text-gray-900">{{ $checklist->checked_by }}</div>
                                </div>
                            @endif
                            
                            @if($checklist->date_checked)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Date Checked</label>
                                    <div class="text-lg font-semibold text-gray-900">{{ $checklist->date_checked->format('M d, Y') }}</div>
                                </div>
                            @endif
                            
                            @if($checklist->gsu_staff)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">GSU Staff</label>
                                    <div class="text-lg font-semibold text-gray-900">{{ $checklist->gsu_staff }}</div>
                                </div>
                            @endif
                            
                            @if($checklist->completed_at)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Completed</label>
                                    <div class="text-lg font-semibold text-gray-900">{{ $checklist->completed_at->format('M d, Y H:i') }}</div>
                                </div>
                            @endif
                            
                            @if($checklist->has_missing_assets)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Missing Assets</label>
                                    <div class="text-lg font-semibold text-orange-600">Yes</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Notes Card -->
                @if($checklist->notes)
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover">
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-sticky-note text-white text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Additional Notes</h2>
                                <p class="text-indigo-100">Special notes and comments</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="text-gray-900 whitespace-pre-wrap">{{ $checklist->notes }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
