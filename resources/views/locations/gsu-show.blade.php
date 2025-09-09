@extends('layouts.gsu')

@section('content')
<div class="container mx-auto py-8">
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
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('gsu.locations.index') }}" class="text-gray-600 hover:text-red-800 transition-colors">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-3xl font-bold flex items-center gap-2">
                <i class="fas fa-building text-red-800"></i>
                {{ $location->building }}
            </h1>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-sm text-gray-600">
                <i class="fas fa-boxes mr-2"></i>{{ $assets->count() }} assets
            </div>
            <a href="{{ route('gsu.assets.create', ['location_id' => $location->id]) }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                <i class="fas fa-plus"></i> Create Asset
            </a>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('gsu.assets.show', $asset) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('gsu.assets.edit', $asset) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-boxes text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-600 mb-2">No Assets in this Location</h3>
            <p class="text-gray-500 mb-4">This location doesn't have any assets yet.</p>
            <a href="{{ route('gsu.assets.create', ['location_id' => $location->id]) }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 inline-flex items-center gap-2">
                <i class="fas fa-plus"></i> Create First Asset
            </a>
        </div>
    @endif
</div>
@endsection
