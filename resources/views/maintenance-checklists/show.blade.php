@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <i class="fas fa-clipboard-check text-red-800"></i>
                Maintenance Checklist Details
            </h1>
            <div class="flex items-center gap-4 mt-2">
                <p class="text-gray-600">{{ $checklist->room_number }} - {{ $checklist->school_year }}</p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    <i class="fas fa-tag mr-1"></i>
                    {{ $checklist->maintenance_id }}
                </span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <!-- Admin actions: Edit (if created), Export (if completed), and Back -->
            @if($checklist->status === 'created')
                <a href="{{ route('maintenance-checklists.edit', $checklist) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                    <i class="fas fa-edit"></i> Edit
                </a>
            @endif
            
            @if($checklist->status === 'completed')
                <a href="{{ route('maintenance-checklists.export', $checklist) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                    <i class="fas fa-download"></i> Export CSV
                </a>
            @endif
            
            <a href="{{ route('maintenance-checklists.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Header Information -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Header Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-600">School Year</label>
                <p class="text-lg font-semibold text-gray-900">{{ $checklist->school_year }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Department</label>
                <p class="text-lg font-semibold text-gray-900">{{ $checklist->department }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Date Reported</label>
                <p class="text-lg font-semibold text-gray-900">{{ $checklist->date_reported->format('M d, Y') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Program</label>
                <p class="text-lg font-semibold text-gray-900">{{ $checklist->program ?? 'N/A' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Location</label>
                <p class="text-lg font-semibold text-gray-900">
                    @if($checklist->location)
                        {{ $checklist->location->building }} - Floor {{ $checklist->location->floor }} - Room {{ $checklist->location->room }}
                    @else
                        {{ $checklist->room_number }}
                    @endif
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Instructor</label>
                <p class="text-lg font-semibold text-gray-900">{{ $checklist->instructor }}</p>
            </div>
            @if($checklist->instructor_signature)
            <div>
                <label class="block text-sm font-medium text-gray-600">Instructor Signature</label>
                @if(str_starts_with($checklist->instructor_signature, 'data:image'))
                    <img src="{{ $checklist->instructor_signature }}" alt="Instructor Signature" class="mt-2 border border-gray-300 rounded max-w-xs">
                @elseif(str_contains($checklist->instructor_signature, 'signatures/'))
                    <img src="{{ asset('storage/' . $checklist->instructor_signature) }}" alt="Instructor Signature" class="mt-2 border border-gray-300 rounded max-w-xs">
                @else
                    <p class="text-lg font-semibold text-gray-900">{{ $checklist->instructor_signature }}</p>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Workflow Status -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Workflow Status</h2>
        @php
            $statusColors = [
                'created' => 'bg-gray-100 text-gray-800',
                'acknowledged' => 'bg-blue-100 text-blue-800',
                'in_progress' => 'bg-yellow-100 text-yellow-800',
                'completed' => 'bg-green-100 text-green-800'
            ];
            $statusLabels = [
                'created' => 'Created',
                'acknowledged' => 'Acknowledged',
                'in_progress' => 'In Progress',
                'completed' => 'Completed'
            ];
        @endphp
        <div class="flex items-center gap-4 mb-4">
            <span class="px-4 py-2 text-lg font-semibold rounded-full {{ $statusColors[$checklist->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ $statusLabels[$checklist->status] ?? ucfirst($checklist->status) }}
            </span>
            @if($checklist->acknowledged_at)
                <span class="text-sm text-gray-600">
                    Acknowledged: {{ $checklist->acknowledged_at->format('M d, Y H:i') }}
                    @if($checklist->acknowledgedBy)
                        by {{ $checklist->acknowledgedBy->name }}
                    @endif
                </span>
            @endif
            @if($checklist->completed_at)
                <span class="text-sm text-gray-600">
                    Completed: {{ $checklist->completed_at->format('M d, Y H:i') }}
                    @if($checklist->completedBy)
                        by {{ $checklist->completedBy->name }}
                    @endif
                </span>
            @endif
        </div>
        
        @if($checklist->status !== 'completed')
            @php
                $progress = $checklist->scanning_progress;
            @endphp
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
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
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="bg-blue-600 h-4 rounded-full transition-all duration-300" 
                         style="width: {{ $progress['percentage'] }}%"></div>
                </div>
                <p class="text-sm text-gray-600 mt-2">{{ $progress['percentage'] }}% Complete</p>
            </div>
        @endif
    </div>

    <!-- Status Summary (for completed checklists) -->
    @if($checklist->status === 'completed')
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Final Status Summary</h2>
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
    @endif

    <!-- Maintenance Items -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Maintenance Items</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Particulars/Items</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">QTY</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start of SY Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End of SY Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scan Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($checklist->items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                @if($item->asset_code)
                                    <a href="{{ route('assets.show', $item->asset_code) }}" 
                                       class="text-blue-600 hover:text-blue-900 font-mono">
                                        {{ $item->asset_code }}
                                    </a>
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($item->location_name)
                                    <div class="flex flex-col">
                                        <span class="font-medium text-blue-600">{{ $item->location_name }}</span>
                                        @if($item->location)
                                            <span class="text-xs text-gray-500">Current: {{ $item->location->building }} - Floor {{ $item->location->floor }} - Room {{ $item->location->room }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
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
                                            // Treat Available, For Repair, For Maintenance as Found/Resolved
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
                                    @if($item->scanned_at)
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $item->scanned_at->format('M d, H:i') }}
                                        </div>
                                    @endif
                                @elseif($item->is_missing)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-times"></i> Missing
                                    </span>
                                    @if($item->scanned_at)
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $item->scanned_at->format('M d, H:i') }}
                                        </div>
                                    @endif
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock"></i> Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $item->notes ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer Information -->
    <div class="bg-white rounded-lg shadow-lg p-6 mt-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Footer Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-600">Checked By</label>
                <p class="text-lg font-semibold text-gray-900">{{ $checklist->checked_by }}</p>
            </div>
            @if($checklist->checked_by_signature)
            <div>
                <label class="block text-sm font-medium text-gray-600">Checked By Signature</label>
                @if(str_starts_with($checklist->checked_by_signature, 'data:image'))
                    <img src="{{ $checklist->checked_by_signature }}" alt="Checked By Signature" class="mt-2 border border-gray-300 rounded max-w-xs">
                @elseif(str_contains($checklist->checked_by_signature, 'signatures/'))
                    <img src="{{ asset('storage/' . $checklist->checked_by_signature) }}" alt="Checked By Signature" class="mt-2 border border-gray-300 rounded max-w-xs">
                @else
                    <p class="text-lg font-semibold text-gray-900">{{ $checklist->checked_by_signature }}</p>
                @endif
            </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-600">Date Checked</label>
                <p class="text-lg font-semibold text-gray-900">{{ $checklist->date_checked ? $checklist->date_checked->format('M d, Y') : 'Not set' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">GSU Staff</label>
                <p class="text-lg font-semibold text-gray-900">{{ $checklist->gsu_staff }}</p>
            </div>
            @if($checklist->gsu_staff_signature)
            <div>
                <label class="block text-sm font-medium text-gray-600">GSU Staff Signature</label>
                @if(str_starts_with($checklist->gsu_staff_signature, 'data:image'))
                    <img src="{{ $checklist->gsu_staff_signature }}" alt="GSU Staff Signature" class="mt-2 border border-gray-300 rounded max-w-xs">
                @elseif(str_contains($checklist->gsu_staff_signature, 'signatures/'))
                    <img src="{{ asset('storage/' . $checklist->gsu_staff_signature) }}" alt="GSU Staff Signature" class="mt-2 border border-gray-300 rounded max-w-xs">
                @else
                    <p class="text-lg font-semibold text-gray-900">{{ $checklist->gsu_staff_signature }}</p>
                @endif
            </div>
            @endif
        </div>
        @if($checklist->notes)
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-600">Additional Notes</label>
                <p class="text-gray-900 mt-1">{{ $checklist->notes }}</p>
            </div>
        @endif
    </div>
</div>
@endsection 