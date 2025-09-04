@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <i class="fas fa-clipboard-check text-red-800"></i>
                Maintenance Checklist Details
            </h1>
            <p class="text-gray-600 mt-1">{{ $checklist->room_number }} - {{ $checklist->school_year }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('maintenance-checklists.batch-update-view', $checklist) }}" 
               class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                <i class="fas fa-tasks"></i> Batch Update
            </a>
            <a href="{{ route('maintenance-checklists.edit', $checklist) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('maintenance-checklists.export', $checklist) }}" 
               class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                <i class="fas fa-download"></i> Export CSV
            </a>
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
                <label class="block text-sm font-medium text-gray-600">Room Number</label>
                <p class="text-lg font-semibold text-gray-900">{{ $checklist->room_number }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Instructor</label>
                <p class="text-lg font-semibold text-gray-900">{{ $checklist->instructor }}</p>
            </div>
            @if($checklist->instructor_signature)
            <div>
                <label class="block text-sm font-medium text-gray-600">Instructor Signature</label>
                <p class="text-lg font-semibold text-gray-900">{{ $checklist->instructor_signature }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Status Summary -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Status Summary</h2>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start of SY Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End of SY Status</th>
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $item->start_status === 'OK' ? 'bg-green-100 text-green-800' : 
                                       ($item->start_status === 'FOR REPAIR' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $item->start_status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->status_class }}">
                                    {{ $item->end_status }}
                                </span>
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
                <p class="text-lg font-semibold text-gray-900">{{ $checklist->checked_by_signature }}</p>
            </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-600">Date Checked</label>
                <p class="text-lg font-semibold text-gray-900">{{ $checklist->date_checked->format('M d, Y') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">GSU Staff</label>
                <p class="text-lg font-semibold text-gray-900">{{ $checklist->gsu_staff }}</p>
            </div>
            @if($checklist->gsu_staff_signature)
            <div>
                <label class="block text-sm font-medium text-gray-600">GSU Staff Signature</label>
                <p class="text-lg font-semibold text-gray-900">{{ $checklist->gsu_staff_signature }}</p>
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