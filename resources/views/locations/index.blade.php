@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-8" x-data="{ showToast: {{ session('success') || session('error') ? 'true' : 'false' }}, showModal: false, deleteLocationId: null, deleteLocationName: '' }">
    @if(session('success'))
        <div x-show="showToast" x-transition class="fixed top-6 right-6 z-50 bg-green-900 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-4 animate-fade-in min-w-[300px] border border-green-700"
            x-init="setTimeout(() => showToast = false, 3000)">
            <i class="fas fa-check-circle text-2xl text-green-300"></i>
            <span class="font-semibold">{{ session('success') }}</span>
            <button @click="showToast = false" class="ml-auto text-green-200 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div x-show="showToast" x-transition class="fixed top-6 right-6 z-50 bg-red-900 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-4 animate-fade-in min-w-[300px] border border-red-700"
            x-init="setTimeout(() => showToast = false, 3000)">
            <i class="fas fa-times-circle text-2xl text-red-300"></i>
            <span class="font-semibold">{{ session('error') }}</span>
            <button @click="showToast = false" class="ml-auto text-red-200 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
    @endif
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold flex items-center gap-2"><i class="fas fa-map-marker-alt text-red-800"></i> Locations</h1>
        <a href="{{ route('locations.create') }}" class="bg-red-800 hover:bg-red-900 text-white font-bold py-2 px-4 rounded transition duration-200 flex items-center gap-2">
            <i class="fas fa-plus-circle"></i> Add Location
        </a>
    </div>
    @if($locations->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($locations as $location)
            <div class="bg-white rounded-xl shadow p-6 relative hover:shadow-lg transition-shadow">
                <!-- Action buttons -->
                <div class="absolute top-3 right-3 flex gap-2">
                    <a href="{{ route('locations.edit', $location) }}" 
                       class="inline-flex items-center justify-center w-8 h-8 bg-yellow-100 text-yellow-600 rounded-full hover:bg-yellow-200 transition-colors duration-150"
                       title="Edit Location">
                        <i class="fas fa-edit text-xs"></i>
                    </a>
                    <button @click="showModal = true; deleteLocationId = {{ $location->id }}; deleteLocationName = '{{ addslashes($location->building . ' - Floor ' . $location->floor . ' - Room ' . $location->room) }}'"
                            class="inline-flex items-center justify-center w-8 h-8 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-150"
                            title="Delete Location">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
                
                <!-- Clickable content -->
                <a href="{{ route('locations.show', $location->id) }}" class="block cursor-pointer">
                    <div class="flex flex-col items-center">
                        <div class="mb-2">
                            <i class="fas fa-building text-3xl text-red-800"></i>
                        </div>
                        <div class="text-center">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2 justify-center hover:text-red-800">
                                {{ $location->building }}
                            </h2>
                            <div class="text-sm text-gray-600 mt-1">
                                <div><i class="fas fa-layer-group text-xs"></i> Floor: {{ $location->floor }}</div>
                                <div><i class="fas fa-door-open text-xs"></i> Room: {{ $location->room }}</div>
                            </div>
                            <div class="text-gray-400 text-xs mt-2">
                                @if($location->created_at)
                                    Created {{ $location->created_at->diffForHumans() }}
                                @else
                                    0
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
    @else
    <div class="text-center text-gray-500 py-12">
        <i class="fas fa-map-marker-alt text-4xl mb-4"></i>
        <div>No locations found.</div>
    </div>
    @endif

    <!-- Delete Modal -->
    <div x-show="showModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" style="display: none;">
        <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-md relative">
            <button @click="showModal = false" class="absolute top-3 right-3 text-gray-400 hover:text-red-800 text-xl"><i class="fas fa-times"></i></button>
            <div class="flex flex-col items-center">
                <div class="bg-red-100 text-red-800 rounded-full p-4 mb-4">
                    <i class="fas fa-exclamation-triangle text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2 text-gray-800">Delete Location</h3>
                <p class="text-gray-600 mb-6 text-center">Are you sure you want to delete <span class="font-semibold text-red-800" x-text="deleteLocationName"></span>? This action cannot be undone.</p>
                <form :action="'/locations/' + deleteLocationId" method="POST" class="w-full flex flex-col items-center gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-800 hover:bg-red-900 text-white font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                    <button type="button" @click="showModal = false" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
<style>
@keyframes fade-in { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: none; } }
.animate-fade-in { animation: fade-in 0.5s; }
</style>
@endsection
