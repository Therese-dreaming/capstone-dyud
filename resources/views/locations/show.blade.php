@extends('layouts.superadmin')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('locations.index') }}" class="text-gray-600 hover:text-red-800 transition-colors">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-3xl font-bold flex items-center gap-2">
                <i class="fas fa-building text-red-800"></i>
                {{ $location->building }}
            </h1>
        </div>
        <div class="text-sm text-gray-600">
            <i class="fas fa-boxes mr-2"></i>{{ $assets->count() }} assets
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-building text-red-800"></i>
                <div>
                    <div class="text-sm text-gray-600">Building</div>
                    <div class="font-semibold">{{ $location->building }}</div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <i class="fas fa-layer-group text-red-800"></i>
                <div>
                    <div class="text-sm text-gray-600">Floor</div>
                    <div class="font-semibold">{{ $location->floor }}</div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <i class="fas fa-door-open text-red-800"></i>
                <div>
                    <div class="text-sm text-gray-600">Room</div>
                    <div class="font-semibold">{{ $location->room }}</div>
                </div>
            </div>
        </div>
    </div>

    @if($assets->count())
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Assets in this Location</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Condition</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($assets as $asset)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $asset->asset_code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900">{{ $asset->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $asset->category->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $asset->condition === 'Good' ? 'bg-green-100 text-green-800' : 
                                           ($asset->condition === 'Fair' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $asset->condition }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $asset->status === 'Available' ? 'bg-green-100 text-green-800' : 
                                           ($asset->status === 'In Use' ? 'bg-blue-100 text-blue-800' : 
                                           ($asset->status === 'Lost' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                        {{ $asset->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- New section for borrowings -->
    @if(isset($borrowings) && $borrowings->count())
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Current Borrowings from this Location</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrower</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrow Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($borrowings as $borrowing)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                            <i class="fa fa-box text-white"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $borrowing->category }}</div>
                                        <div class="text-sm text-gray-500 font-mono">
                                            @foreach($borrowing->items as $item)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-1">
                                                    {{ ucfirst($item) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $borrowing->borrower_name }}</div>
                                <div class="text-sm text-gray-500">{{ $borrowing->borrower_id_number }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $borrowing->borrow_date->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $borrowing->borrow_time }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $borrowing->due_date->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">
                                    @if($borrowing->isOverdue())
                                        <span class="text-red-600 font-medium">Overdue</span>
                                    @else
                                        {{ $borrowing->due_date->diffForHumans() }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($borrowing->status === 'active') bg-green-100 text-green-800 
                                    @elseif($borrowing->status === 'overdue') bg-red-100 text-red-800 
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($borrowing->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <a href="{{ route('borrowing.show', $borrowing) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                <div class="text-sm text-gray-500">
                    Showing {{ $borrowings->count() }} active borrowings from this location
                </div>
            </div>
        </div>
    @elseif(isset($borrowings))
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mt-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Current Borrowings from this Location</h2>
            </div>
            <div class="p-6 text-center">
                <div class="bg-gray-100 p-4 rounded-full inline-block mb-4">
                    <i class="fas fa-box-open text-gray-400 text-2xl"></i>
                </div>
                <p class="text-gray-500 text-sm">No active borrowings from this location</p>
            </div>
        </div>
    @endif
</div>
@endsection
