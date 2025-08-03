@extends('layouts.gsu')

@section('content')
<div class="container mx-auto py-8" x-data="{ showToast: {{ session('success') || session('error') ? 'true' : 'false' }}, showModal: false, deleteLocationId: null, deleteLocationName: '' }">
    <!-- GSU Location Management Header -->
    <div class="bg-gradient-to-r from-red-800 to-red-900 text-white p-6 rounded-xl shadow-lg mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-white/20 p-3 rounded-full">
                    <i class="fas fa-map-marker-alt text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold">Location Management</h1>
                    <p class="text-red-100 text-sm md:text-base">GSU Asset Location Control Panel</p>
                </div>
            </div>
            <a href="{{ route('locations.create') }}" class="bg-white text-red-800 font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center gap-2 shadow-lg hover:bg-gray-100">
                <i class="fas fa-plus-circle"></i> Add Location
            </a>
        </div>
    </div>

    <!-- Location Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-blue-100 p-3 rounded-xl">
                    <i class="fas fa-building text-blue-600 text-xl"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">{{ $locations->count() }}</div>
                    <div class="text-sm text-gray-500">Total Locations</div>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500">Asset locations</span>
                <span class="text-blue-600 text-sm font-medium">Active</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-green-100 p-3 rounded-xl">
                    <i class="fas fa-boxes text-green-600 text-xl"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">{{ $locations->sum('assets_count') }}</div>
                    <div class="text-sm text-gray-500">Total Assets</div>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500">Across locations</span>
                <span class="text-green-600 text-sm font-medium">Distributed</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-purple-100 p-3 rounded-xl">
                    <i class="fas fa-chart-pie text-purple-600 text-xl"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">{{ $locations->where('assets_count', '>', 0)->count() }}</div>
                    <div class="text-sm text-gray-500">Active Locations</div>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500">With assets</span>
                <span class="text-purple-600 text-sm font-medium">Populated</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-yellow-100 p-3 rounded-xl">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">{{ $locations->where('assets_count', 0)->count() }}</div>
                    <div class="text-sm text-gray-500">Empty Locations</div>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500">No assets</span>
                <span class="text-yellow-600 text-sm font-medium">Attention</span>
            </div>
        </div>
    </div>

    <!-- Locations Grid -->
    @if($locations->count())
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-red-600"></i>
                    Asset Locations
                </h2>
                <div class="text-sm text-gray-600">
                    Showing {{ $locations->count() }} locations
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($locations as $location)
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 hover:shadow-lg transition-all duration-300 border border-gray-200 hover:border-red-300 group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-red-100 p-3 rounded-lg group-hover:bg-red-200 transition-colors">
                                <i class="fas fa-building text-red-600 text-xl"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-gray-900">{{ $location->assets_count }}</div>
                                <div class="text-xs text-gray-500">assets</div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $location->building }}</h3>
                            <div class="text-sm text-gray-600 mb-3">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-layer-group text-gray-400"></i>
                                    <span>Floor {{ $location->floor }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-door-open text-gray-400"></i>
                                    <span>Room {{ $location->room }}</span>
                                </div>
                            </div>
                            
                            @if($location->assets_count > 0)
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-3">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-check-circle text-green-600"></i>
                                        <span class="text-sm font-medium text-green-800">{{ $location->assets_count }} assets</span>
                                    </div>
                                </div>
                            @else
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-3">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                                        <span class="text-sm font-medium text-yellow-800">No assets</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="text-xs text-gray-500">
                                Created {{ $location->created_at->diffForHumans() }}
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('locations.show', $location) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors duration-150"
                                   title="View Location">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('locations.edit', $location) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 bg-yellow-100 text-yellow-600 rounded-full hover:bg-yellow-200 transition-colors duration-150"
                                   title="Edit Location">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                @if($location->assets_count == 0)
                                <button @click="showModal = true; deleteLocationId = {{ $location->id }}; deleteLocationName = '{{ addslashes($location->building . ' - Floor ' . $location->floor . ' - Room ' . $location->room) }}'"
                                        class="inline-flex items-center justify-center w-8 h-8 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-150"
                                        title="Delete Location">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <div class="text-gray-400 mb-6">
            <i class="fas fa-map-marker-alt text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">No locations found</h3>
            <p class="text-gray-500">Get started by creating your first asset location</p>
        </div>
        <a href="{{ route('locations.create') }}" class="inline-block bg-red-800 text-white px-6 py-3 rounded-lg hover:bg-red-900 transition-colors font-medium">
            <i class="fas fa-plus-circle mr-2"></i>Create First Location
        </a>
    </div>
    @endif

    <!-- Location Analytics -->
    @if($locations->where('assets_count', '>', 0)->count() > 0)
    <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <i class="fas fa-chart-bar text-red-600"></i>
                Location Analytics
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($locations->where('assets_count', '>', 0)->take(5) as $location)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="bg-red-100 p-2 rounded-lg">
                            <i class="fas fa-building text-red-600"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">{{ $location->building }}</div>
                            <div class="text-sm text-gray-500">Floor {{ $location->floor }} • Room {{ $location->room }}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-gray-900">{{ $location->assets_count }}</div>
                        <div class="text-xs text-gray-500">assets</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

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

<!-- Toast Messages -->
@if(session('success'))
    <div class="fixed top-6 right-6 z-50 bg-green-900 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-4 animate-fade-in min-w-[300px] border border-green-700"
         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
        <i class="fas fa-check-circle text-2xl text-green-300"></i>
        <span class="font-semibold">{{ session('success') }}</span>
        <button @click="show = false" class="ml-auto text-green-200 hover:text-white"><i class="fas fa-times"></i></button>
    </div>
@endif
@if(session('error'))
    <div class="fixed top-6 right-6 z-50 bg-red-900 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-4 animate-fade-in min-w-[300px] border border-red-700"
         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
        <i class="fas fa-times-circle text-2xl text-red-300"></i>
        <span class="font-semibold">{{ session('error') }}</span>
        <button @click="show = false" class="ml-auto text-red-200 hover:text-white"><i class="fas fa-times"></i></button>
    </div>
@endif

<style>
@keyframes fade-in { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: none; } }
.animate-fade-in { animation: fade-in 0.5s; }
</style>
@endsection 