@extends('layouts.gsu')

@section('content')
<div class="container mx-auto py-8">
    <div class="mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <i class="fas fa-clipboard-check text-red-800"></i>
                Maintenance Checklists
                @if(isset($status))
                    <span class="text-lg font-normal text-gray-600">
                        - {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </span>
                @endif
            </h1>
            <p class="text-gray-600 mt-1">
                @if(isset($status))
                    Showing {{ str_replace('_', ' ', $status) }} maintenance checklists
                @else
                    View and manage maintenance checklists assigned to you
                @endif
            </p>
        </div>
    </div>

    <!-- Status Filter Navigation -->
    <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('maintenance-checklists.index') }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ !request()->has('status') ? 'bg-red-800 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <i class="fas fa-th-list mr-1"></i> All Checklists
            </a>
            <a href="{{ route('maintenance-checklists.index', ['status' => 'created']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->get('status') == 'created' ? 'bg-red-800 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <i class="fas fa-clock mr-1"></i> Pending Acknowledgment
            </a>
            <a href="{{ route('maintenance-checklists.index', ['status' => 'acknowledged']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->get('status') == 'acknowledged' ? 'bg-red-800 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <i class="fas fa-play-circle mr-1"></i> Ready to Start
            </a>
            <a href="{{ route('maintenance-checklists.index', ['status' => 'in_progress']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->get('status') == 'in_progress' ? 'bg-red-800 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <i class="fas fa-spinner mr-1"></i> In Progress
            </a>
            <a href="{{ route('maintenance-checklists.index', ['status' => 'completed']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->get('status') == 'completed' ? 'bg-red-800 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <i class="fas fa-check-circle mr-1"></i> Completed
            </a>
        </div>
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
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900">
                    @if(isset($status))
                        {{ ucfirst(str_replace('_', ' ', $status)) }} Maintenance Checklists
                    @else
                        All Maintenance Checklists
                    @endif
                </h2>
                <div class="text-sm text-gray-500">
                    Total: {{ $checklists->total() }} checklists
                </div>
            </div>
        </div>
        
        @if($checklists->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Maintenance ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">School Year</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instructor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Reported</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($checklists as $checklist)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $checklist->maintenance_id }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $checklist->school_year }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $checklist->department }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($checklist->location)
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ $checklist->location->building }}</span>
                                        <span class="text-xs text-gray-500">Floor {{ $checklist->location->floor }} - Room {{ $checklist->location->room }}</span>
                                    </div>
                                @else
                                    {{ $checklist->room_number }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $checklist->instructor }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $checklist->date_reported->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
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
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$checklist->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$checklist->status] ?? ucfirst($checklist->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($checklist->status === 'completed')
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
                                @else
                                    @php
                                        $progress = $checklist->scanning_progress;
                                    @endphp
                                    <div class="text-sm text-gray-900">
                                        {{ $progress['scanned'] + $progress['missing'] }}/{{ $progress['total'] }} processed
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $progress['percentage'] }}%"></div>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('maintenance-checklists.show', $checklist) }}" 
                                       class="text-blue-600 hover:text-blue-900" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($checklist->status === 'created')
                                        <form action="{{ route('maintenance-checklists.acknowledge', $checklist) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900" title="Acknowledge">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @elseif($checklist->status === 'acknowledged')
                                        <form action="{{ route('maintenance-checklists.start', $checklist) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-yellow-600 hover:text-yellow-900" title="Start Maintenance">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </form>
                                    @elseif($checklist->status === 'in_progress')
                                        <a href="{{ route('maintenance-checklists.scanner', $checklist) }}" 
                                           class="text-purple-600 hover:text-purple-900" title="Continue Scanning">
                                            <i class="fas fa-qrcode"></i>
                                        </a>
                                    @endif
                                    
                                    @if($checklist->status === 'completed')
                                        <a href="{{ route('maintenance-checklists.export', $checklist) }}" 
                                           class="text-purple-600 hover:text-purple-900" title="Export">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @endif
                                    
                                    <!-- GSU users can only view and work with checklists, not edit or delete them -->
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
            <p class="text-gray-500 text-sm">
                @if(isset($status))
                    No {{ str_replace('_', ' ', $status) }} maintenance checklists found
                @else
                    No maintenance checklists found
                @endif
            </p>
            @if(!isset($status))
                <a href="{{ route('maintenance-checklists.index') }}" class="mt-4 inline-block bg-red-800 text-white px-4 py-2 rounded-lg hover:bg-red-900 transition-colors">
                    View All Checklists
                </a>
            @else
                <a href="{{ route('maintenance-checklists.index') }}" class="mt-4 inline-block bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    View All Checklists
                </a>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection 