@extends('layouts.gsu')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-yellow-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-red-600 to-red-600 text-white p-6 mb-6 rounded-xl shadow-lg relative overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-white/20 p-3 rounded-full">
                        <i class="fas fa-wrench text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold">Repair Requests</h1>
                        <p class="text-yellow-100 text-sm md:text-base">Review and manage repair requests from users</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-yellow-200">Total Requests</div>
                    <div class="text-2xl font-bold text-white">{{ $repairRequests->total() }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-xl shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="bg-green-100 p-2 rounded-full">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold">Success!</h4>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-xl shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="bg-red-100 p-2 rounded-full">
                        <i class="fas fa-exclamation-circle text-red-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold">Error!</h4>
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div class="bg-yellow-100 p-3 rounded-xl">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">{{ $repairRequests->where('status', 'pending')->count() }}</div>
                        <div class="text-sm text-gray-500">Pending</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div class="bg-blue-100 p-3 rounded-xl">
                        <i class="fas fa-check text-blue-600 text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">{{ $repairRequests->where('status', 'approved')->count() }}</div>
                        <div class="text-sm text-gray-500">Approved</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div class="bg-orange-100 p-3 rounded-xl">
                        <i class="fas fa-tools text-orange-600 text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">{{ $repairRequests->where('status', 'in_progress')->count() }}</div>
                        <div class="text-sm text-gray-500">In Progress</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div class="bg-green-100 p-3 rounded-xl">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">{{ $repairRequests->where('status', 'completed')->count() }}</div>
                        <div class="text-sm text-gray-500">Completed</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Repair Requests List -->
        @if($repairRequests->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requester</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($repairRequests as $request)
                                @php
                                    // Extract priority from notes
                                    preg_match('/Priority: (\w+)/', $request->notes, $matches);
                                    $priority = $matches[1] ?? 'MEDIUM';
                                    
                                    $priorityColors = [
                                        'LOW' => 'bg-gray-100 text-gray-800',
                                        'MEDIUM' => 'bg-yellow-100 text-yellow-800',
                                        'HIGH' => 'bg-orange-100 text-orange-800',
                                        'URGENT' => 'bg-red-100 text-red-800',
                                    ];
                                    $priorityClass = $priorityColors[$priority] ?? 'bg-gray-100 text-gray-800';
                                    
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'approved' => 'bg-blue-100 text-blue-800',
                                        'in_progress' => 'bg-orange-100 text-orange-800',
                                        'acknowledged' => 'bg-orange-100 text-orange-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                    ];
                                    $statusClass = $statusColors[$request->status] ?? 'bg-gray-100 text-gray-800';
                                    $statusDisplay = $request->status === 'in_progress' ? 'In Progress' : ucfirst($request->status);
                                    
                                    $assetCodes = $request->getRequestedAssetCodes();
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">#{{ $request->id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $request->requester->name ?? 'Unknown' }}</div>
                                        <div class="text-sm text-gray-500">{{ $request->department }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(count($assetCodes) > 0)
                                            <div class="text-sm font-mono font-medium text-gray-900">{{ $assetCodes[0] }}</div>
                                        @else
                                            <span class="text-sm text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $priorityClass }}">
                                            {{ $priority }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                            {{ $statusDisplay }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $request->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('gsu.repair-requests.show', $request) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $repairRequests->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-24 h-24 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-wrench text-yellow-400 text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Repair Requests</h3>
                <p class="text-gray-600">There are currently no repair requests to review.</p>
            </div>
        @endif
    </div>
</div>
@endsection
