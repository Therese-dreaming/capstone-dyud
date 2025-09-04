@extends('layouts.gsu')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold flex items-center gap-2">
            <i class="fas fa-map-marker-alt text-red-800"></i> Locations
        </h1>
        <div class="text-sm text-gray-600">
            <i class="fas fa-info-circle mr-2"></i>View Only - Contact Admin to add/edit locations
        </div>
    </div>
    
    @if($locations->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($locations as $location)
            <div class="bg-white rounded-xl shadow p-6 relative hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-building text-red-800 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-800">{{ $location->building }}</h3>
                            <p class="text-sm text-gray-600">Floor {{ $location->floor }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-2 mb-4">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="fas fa-layer-group w-4"></i>
                        <span>Floor: {{ $location->floor }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="fas fa-door-open w-4"></i>
                        <span>Room: {{ $location->room }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="fas fa-boxes w-4"></i>
                        <span>{{ $location->assets->count() }} assets</span>
                    </div>
                </div>
                
                <div class="flex gap-2">
                    <a href="{{ route('gsu.locations.show', $location) }}" 
                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-3 rounded-lg transition duration-200 text-sm font-medium">
                        <i class="fas fa-eye mr-1"></i> View
                    </a>
                </div>
            </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-12">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-map-marker-alt text-gray-400 text-3xl"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">No Locations Found</h3>
        <p class="text-gray-500">No locations have been created yet.</p>
    </div>
    @endif
</div>
@endsection