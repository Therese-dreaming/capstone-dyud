@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <i class="fas fa-clipboard-check text-red-800"></i>
                Maintenance Checklists
            </h1>
            <p class="text-gray-600 mt-1">Room-based maintenance records following the CSV format</p>
        </div>
        <a href="{{ route('maintenance-checklists.create') }}" class="bg-gradient-to-r from-red-800 to-red-900 hover:from-red-900 hover:to-red-950 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center gap-2 shadow-lg">
            <i class="fas fa-plus"></i> Create Checklist
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">All Maintenance Checklists</h2>
        </div>
        
        @if($checklists->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">School Year</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instructor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Reported</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assets Count</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Summary</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($checklists as $checklist)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $checklist->school_year }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $checklist->department }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $checklist->room_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $checklist->instructor }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $checklist->date_reported->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $checklist->items->whereNotNull('asset_code')->count() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $summary = $checklist->status_summary;
                                @endphp
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $summary['ok'] }} OK
                                    </span>
                                    @if($summary['repair'] > 0)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            {{ $summary['repair'] }} Repair
                                        </span>
                                    @endif
                                    @if($summary['replacement'] > 0)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            {{ $summary['replacement'] }} Replace
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('maintenance-checklists.show', $checklist) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('maintenance-checklists.batch-update-view', $checklist) }}" 
                                       class="text-purple-600 hover:text-purple-900">
                                        <i class="fas fa-tasks"></i>
                                    </a>
                                    <a href="{{ route('maintenance-checklists.edit', $checklist) }}" 
                                       class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('maintenance-checklists.export', $checklist) }}" 
                                       class="text-purple-600 hover:text-purple-900">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <form action="{{ route('maintenance-checklists.destroy', $checklist) }}" 
                                          method="POST" class="inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this checklist?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $checklists->links() }}
        </div>
        @else
        <div class="p-6 text-center">
            <div class="bg-gray-100 p-4 rounded-full inline-block mb-4">
                <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
            </div>
            <p class="text-gray-500 text-sm">No maintenance checklists found</p>
            <a href="{{ route('maintenance-checklists.create') }}" class="mt-4 inline-block bg-red-800 text-white px-4 py-2 rounded-lg hover:bg-red-900 transition-colors">
                Create Your First Checklist
            </a>
        </div>
        @endif
    </div>
</div>
@endsection 