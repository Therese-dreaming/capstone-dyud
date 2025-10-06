@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route(request()->routeIs('gsu.*') ? 'gsu.categories.index' : 'categories.index') }}" class="text-gray-600 hover:text-red-800 transition-colors">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-3xl font-bold flex items-center gap-2">
                <i class="fas fa-folder text-red-800"></i>
                {{ $category->name }}
            </h1>
        </div>
        <div class="text-sm text-gray-600">
            <i class="fas fa-boxes mr-2"></i>{{ $assets->total() }} assets
        </div>
    </div>

    @if($assets->count())
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Condition</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($assets as $asset)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $asset->asset_code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900">{{ $asset->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    @if($asset->location)
                                        {{ $asset->location->building }} - Floor {{ $asset->location->floor }} - Room {{ $asset->location->room }}
                                    @else
                                        <span class="text-gray-400 italic">Not assigned</span>
                                    @endif
                                </td>
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

        <div class="mt-6">
            {{ $assets->links() }}
        </div>
    @else
        <div class="text-center text-gray-500 py-12 bg-white rounded-lg shadow">
            <i class="fas fa-inbox text-4xl mb-4"></i>
            <div class="text-lg font-medium">No assets found in this category</div>
            <div class="text-sm mt-2">Assets will appear here once they are created and approved.</div>
            @if(Auth::user()->role === 'purchasing')
                <a href="{{ route('purchasing.assets.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>Register New Asset
                </a>
            @else
                <div class="mt-4 text-sm text-gray-400">
                    <i class="fas fa-info-circle mr-1"></i>
                    Contact purchasing department to add new assets
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
