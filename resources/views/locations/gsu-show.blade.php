@extends('layouts.gsu')

@section('content')
<div class="container mx-auto px-4 sm:px-6 py-8">
    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
        <div class="flex items-start gap-2">
            <i class="fas fa-check-circle mt-0.5"></i>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
        <div class="flex items-start gap-2">
            <i class="fas fa-times-circle mt-0.5"></i>
            <span class="font-semibold">{{ session('error') }}</span>
        </div>
    </div>
    @endif
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 sm:gap-0 mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('gsu.locations.index') }}" class="text-gray-600 hover:text-red-800 transition-colors">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-2xl sm:text-3xl font-bold flex items-center gap-2">
                <i class="fas fa-building text-red-800"></i>
                {{ $location->building ?? 'Location Not Found' }}
            </h1>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="text-sm text-gray-600">
                <i class="fas fa-boxes mr-2"></i>{{ $assets ? $assets->count() : 0 }} assets
            </div>
            <a href="{{ route('gsu.assets.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2 text-sm sm:text-base">
                <i class="fas fa-boxes"></i> View All Assets
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-building text-red-800"></i>
                <div>
                    <div class="text-sm text-gray-600">Building</div>
                    <div class="font-semibold">{{ $location->building ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <i class="fas fa-layer-group text-red-800"></i>
                <div>
                    <div class="text-sm text-gray-600">Floor</div>
                    <div class="font-semibold">{{ $location->floor ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <i class="fas fa-door-open text-red-800"></i>
                <div>
                    <div class="text-sm text-gray-600">Room</div>
                    <div class="font-semibold">{{ $location->room ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>

    @if($assets && $assets->count())
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                <h2 class="text-xl font-semibold text-gray-900">Assets in this Location</h2>
                <div class="text-sm text-gray-500">Showing {{ $assets->count() }} assets</div>
            </div>

            <!-- Mobile Card List -->
            <div class="sm:hidden px-4 py-4 space-y-3">
                @foreach($assets as $asset)
                    <div class="border border-gray-200 rounded-xl shadow-sm hover:shadow-md active:shadow transition-shadow duration-150 cursor-pointer" onclick="window.location='{{ route('gsu.assets.show', $asset) }}'" aria-label="View {{ $asset->name }}">
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 rounded-t-xl flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-red-800 rounded-lg flex items-center justify-center text-white">
                                    <i class="fas fa-box text-xs"></i>
                                </div>
                                <div>
                                    <div class="font-mono text-xs font-bold text-gray-900">{{ $asset->asset_code }}</div>
                                    <div class="text-[11px] text-gray-500">Asset Code</div>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-[11px] font-semibold rounded-full
                                {{ $asset->status === 'Available' ? 'bg-green-100 text-green-800' :
                                   ($asset->status === 'In Use' ? 'bg-blue-100 text-blue-800' :
                                   ($asset->status === 'Lost' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                {{ $asset->status }}
                            </span>
                        </div>
                        <div class="p-4 space-y-3">
                            <div>
                                <div class="text-[11px] text-gray-500 uppercase">Name</div>
                                <div class="text-sm font-semibold text-gray-900 leading-snug break-words">{{ $asset->name }}</div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="flex items-center justify-between text-sm">
                                    <div class="text-gray-600">Category</div>
                                    <div class="text-gray-900 font-medium text-right ml-3 truncate">{{ $asset->category->name ?? 'N/A' }}</div>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <div class="text-gray-600">Condition</div>
                                    <span class="px-2 inline-flex text-[11px] leading-5 font-semibold rounded-full ml-3
                                        {{ $asset->condition === 'Good' ? 'bg-green-100 text-green-800' :
                                           ($asset->condition === 'Fair' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $asset->condition }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 rounded-b-xl flex items-center gap-2">
                            <a href="{{ route('gsu.assets.show', $asset) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700" onclick="event.stopPropagation()">
                                <i class="fas fa-eye text-xs"></i> View
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Desktop Table -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset Code</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Condition</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($assets as $asset)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $asset->asset_code }}</td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-gray-900">{{ $asset->name }}</td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $asset->category->name ?? 'N/A' }}</td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $asset->condition === 'Good' ? 'bg-green-100 text-green-800' : 
                                           ($asset->condition === 'Fair' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $asset->condition }}
                                    </span>
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $asset->status === 'Available' ? 'bg-green-100 text-green-800' : 
                                           ($asset->status === 'In Use' ? 'bg-blue-100 text-blue-800' : 
                                           ($asset->status === 'Lost' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                        {{ $asset->status }}
                                    </span>
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('gsu.assets.show', $asset) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-lg p-6 sm:p-8 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-boxes text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-600 mb-2">No Assets in this Location</h3>
            <p class="text-gray-500 mb-4">This location doesn't have any assets yet.</p>
            <a href="{{ route('gsu.assets.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 inline-flex items-center gap-2">
                <i class="fas fa-boxes"></i> View Available Assets
            </a>
        </div>
    @endif
</div>
@endsection
