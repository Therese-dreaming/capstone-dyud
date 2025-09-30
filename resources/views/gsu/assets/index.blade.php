@extends('layouts.gsu')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-red-50" x-data="bulkDeployment()">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-red-800 to-red-900 text-white p-6 mb-6 rounded-xl shadow-lg relative overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-white/20 p-3 rounded-full">
                        <i class="fas fa-boxes text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold">Asset Deployment Center</h1>
                        <p class="text-red-100 text-sm md:text-base">Deploy approved assets to their designated locations</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <div class="text-sm text-red-200">Assets Ready</div>
                        <div class="text-2xl font-bold text-white">{{ $assets->count() }}</div>
                    </div>
                    <button x-show="selectedAssets.length > 0" 
                            @click="openBulkDeployModal()"
                            class="bg-white text-red-800 px-6 py-3 rounded-lg font-semibold hover:bg-red-50 transition-colors shadow-lg flex items-center gap-2">
                        <i class="fas fa-layer-group"></i>
                        <span>Bulk Deploy (<span x-text="selectedAssets.length"></span>)</span>
                    </button>
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
                    <div class="bg-blue-100 p-3 rounded-xl">
                        <i class="fas fa-clock text-blue-600 text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">{{ $assets->where('location_id', null)->count() }}</div>
                        <div class="text-sm text-gray-500">Pending Deployment</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div class="bg-green-100 p-3 rounded-xl">
                        <i class="fas fa-map-marker-alt text-green-600 text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">{{ $assets->where('location_id', '!=', null)->count() }}</div>
                        <div class="text-sm text-gray-500">Deployed</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div class="bg-purple-100 p-3 rounded-xl">
                        <i class="fas fa-boxes text-purple-600 text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">{{ $assets->count() }}</div>
                        <div class="text-sm text-gray-500">Total Assets</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div class="bg-yellow-100 p-3 rounded-xl">
                        <i class="fas fa-dollar-sign text-yellow-600 text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-gray-900">₱{{ number_format($assets->sum('purchase_cost'), 0) }}</div>
                        <div class="text-sm text-gray-500">Total Value</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        @php
            $pendingAssets = $assets->where('location_id', null);
            $deployedAssets = $assets->where('location_id', '!=', null);
        @endphp

        <div class="mb-6" x-data="{ activeTab: 'pending' }">
            <!-- Tab Navigation -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="flex space-x-8">
                    <button @click="activeTab = 'pending'" 
                            :class="activeTab === 'pending' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="py-4 px-1 border-b-2 font-semibold text-sm flex items-center gap-2 transition-colors">
                        <div :class="activeTab === 'pending' ? 'bg-yellow-100' : 'bg-gray-100'" class="p-2 rounded-lg transition-colors">
                            <i class="fas fa-clock" :class="activeTab === 'pending' ? 'text-yellow-600' : 'text-gray-400'"></i>
                        </div>
                        Pending Deployment
                        <span class="px-2 py-1 rounded-full text-xs font-medium" :class="activeTab === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-600'">
                            {{ $pendingAssets->count() }}
                        </span>
                    </button>
                    
                    <button @click="activeTab = 'deployed'" 
                            :class="activeTab === 'deployed' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="py-4 px-1 border-b-2 font-semibold text-sm flex items-center gap-2 transition-colors">
                        <div :class="activeTab === 'deployed' ? 'bg-green-100' : 'bg-gray-100'" class="p-2 rounded-lg transition-colors">
                            <i class="fas fa-check-circle" :class="activeTab === 'deployed' ? 'text-green-600' : 'text-gray-400'"></i>
                        </div>
                        Deployed Assets
                        <span class="px-2 py-1 rounded-full text-xs font-medium" :class="activeTab === 'deployed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'">
                            {{ $deployedAssets->count() }}
                        </span>
                    </button>
                </nav>
            </div>

            <!-- Pending Deployment Tab Content -->
            <div x-show="activeTab === 'pending'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                @if($pendingAssets->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($pendingAssets as $asset)
                        <div @click="toggleAssetSelection({{ $asset->id }})" 
                             :class="{
                                 'ring-4 ring-green-500 border-green-500 shadow-xl': selectedAssets.includes({{ $asset->id }}),
                                 'border-gray-200': !selectedAssets.includes({{ $asset->id }})
                             }"
                             class="bg-white rounded-xl shadow-sm border overflow-hidden hover:shadow-lg transition-all duration-300 cursor-pointer relative group">
                        <!-- Asset Header -->
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $asset->name }}</h3>
                                    <p class="text-sm text-gray-600">Code: <span class="font-mono font-medium">{{ $asset->asset_code }}</span></p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $asset->category->name ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Asset Details -->
                        <div class="p-6">
                            <div class="space-y-4">
                                <!-- Purchase Info -->
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Purchase Cost</span>
                                    <span class="text-lg font-semibold text-gray-900">₱{{ number_format($asset->purchase_cost, 2) }}</span>
                                </div>

                                <!-- Created By -->
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Created By</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $asset->createdBy->name ?? 'Unknown' }}</span>
                                </div>

                                <!-- Location Status -->
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Status</span>
                                    @if($asset->location_id)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                            Deployed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            Pending
                                        </span>
                                    @endif
                                </div>

                                <!-- Location Details -->
                                @if($asset->location_id)
                                    <div class="bg-green-50 rounded-lg p-3">
                                        <div class="text-sm text-green-800">
                                            <i class="fas fa-building mr-2"></i>
                                            <strong>{{ $asset->location->building }}</strong>
                                        </div>
                                        <div class="text-xs text-green-600 mt-1">
                                            Floor {{ $asset->location->floor }} • Room {{ $asset->location->room }}
                                        </div>
                                    </div>
                                @else
                                    <div class="bg-yellow-50 rounded-lg p-3">
                                        <div class="text-sm text-yellow-800">
                                            <i class="fas fa-clock mr-2"></i>
                                            <strong>Awaiting Deployment</strong>
                                        </div>
                                        <div class="text-xs text-yellow-600 mt-1">
                                            Location will be assigned during deployment
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-3 mt-6 pt-4 border-t border-gray-200">
                                <a href="{{ route('gsu.assets.show', $asset) }}" 
                                   @click.stop
                                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg transition-colors duration-200 text-sm font-medium">
                                    <i class="fas fa-eye mr-2"></i>View Details
                                </a>
                                
                                @if(!$asset->location_id)
                                    <a href="{{ route('gsu.assets.assign-location', $asset) }}" 
                                       @click.stop
                                       class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-2 px-4 rounded-lg transition-colors duration-200 text-sm font-medium">
                                        <i class="fas fa-map-marker-alt mr-2"></i>Deploy
                                    </a>
                                @else
                                    <div class="flex-1 bg-gray-100 text-gray-500 text-center py-2 px-4 rounded-lg text-sm font-medium">
                                        <i class="fas fa-check-circle mr-2"></i>Deployed
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                    <div class="bg-yellow-50 rounded-xl border border-yellow-200 p-12 text-center">
                        <div class="w-24 h-24 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-clock text-yellow-400 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">No Pending Assets</h3>
                        <p class="text-gray-600">All assets have been deployed!</p>
                    </div>
                @endif
            </div>

            <!-- Deployed Assets Tab Content -->
            <div x-show="activeTab === 'deployed'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                @if($deployedAssets->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($deployedAssets as $asset)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-300 relative opacity-75">
                        <!-- Asset Header -->
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $asset->name }}</h3>
                                    <p class="text-sm text-gray-600">Code: <span class="font-mono font-medium">{{ $asset->asset_code }}</span></p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $asset->category->name ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Asset Details -->
                        <div class="p-6">
                            <div class="space-y-4">
                                <!-- Purchase Info -->
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Purchase Cost</span>
                                    <span class="text-lg font-semibold text-gray-900">₱{{ number_format($asset->purchase_cost, 2) }}</span>
                                </div>

                                <!-- Created By -->
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Created By</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $asset->createdBy->name ?? 'Unknown' }}</span>
                                </div>

                                <!-- Location Status -->
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Status</span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        Deployed
                                    </span>
                                </div>

                                <!-- Location Details -->
                                <div class="bg-green-50 rounded-lg p-3">
                                    <div class="text-sm text-green-800">
                                        <i class="fas fa-building mr-2"></i>
                                        <strong>{{ $asset->location->building }}</strong>
                                    </div>
                                    <div class="text-xs text-green-600 mt-1">
                                        Floor {{ $asset->location->floor }} • Room {{ $asset->location->room }}
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-3 mt-6 pt-4 border-t border-gray-200">
                                <a href="{{ route('gsu.assets.show', $asset) }}" 
                                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg transition-colors duration-200 text-sm font-medium">
                                    <i class="fas fa-eye mr-2"></i>View Details
                                </a>
                                <div class="flex-1 bg-gray-100 text-gray-500 text-center py-2 px-4 rounded-lg text-sm font-medium">
                                    <i class="fas fa-check-circle mr-2"></i>Deployed
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                    <div class="bg-green-50 rounded-xl border border-green-200 p-12 text-center">
                        <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-box-open text-green-400 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">No Deployed Assets</h3>
                        <p class="text-gray-600">Deploy assets from the Pending tab to see them here.</p>
                    </div>
                @endif
            </div>
        </div>

        @if($assets->count() === 0)
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-box-open text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Assets Available</h3>
                <p class="text-gray-600 mb-4">There are currently no approved assets ready for deployment.</p>
                <p class="text-sm text-gray-500">Assets will appear here once they are approved by the Admin team.</p>
            </div>
        @endif

        <!-- Info Panel -->
        <div class="bg-blue-50 rounded-xl border border-blue-200 p-6">
            <div class="flex items-start gap-4">
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h4 class="text-lg font-semibold text-blue-900 mb-2">GSU Deployment Workflow</h4>
                    <div class="space-y-2 text-sm text-blue-800">
                        <p><strong>Your Role:</strong> Deploy approved assets by assigning them to specific locations within the university.</p>
                        <p><strong>Process:</strong> Review asset details → Assign location → Confirm deployment → Asset becomes available in the system.</p>
                        <p><strong>Important:</strong> Ensure the physical asset is placed at the assigned location before confirming deployment.</p>
                    </div>
                    <div class="mt-4 p-3 bg-blue-100 rounded-lg">
                        <div class="flex items-center gap-2 text-blue-700">
                            <i class="fas fa-lightbulb"></i>
                            <span class="font-medium">Pro Tip:</span>
                        </div>
                        <p class="text-sm text-blue-600 mt-1">Only approved assets without assigned locations are eligible for deployment.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Deploy Modal -->
    <div x-show="showBulkModal" 
         x-transition
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" @click="closeBulkDeployModal()"></div>
            
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-8">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Bulk Deploy Assets</h3>
                        <p class="text-sm text-gray-600 mt-1">Deploy <span x-text="selectedAssets.length"></span> selected asset(s) to a location</p>
                    </div>
                    <button @click="closeBulkDeployModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>

                <!-- Form -->
                <form @submit.prevent="submitBulkDeploy()">
                    @csrf
                    
                    <!-- Location Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-map-marker-alt mr-2 text-green-600"></i>Select Deployment Location
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   x-model="locationSearch"
                                   @input="filterLocations()"
                                   placeholder="Search for a location..."
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <i class="fas fa-search absolute right-4 top-4 text-gray-400"></i>
                        </div>
                        
                        <!-- Location List -->
                        <div class="mt-3 max-h-64 overflow-y-auto border border-gray-200 rounded-xl">
                            <template x-for="location in filteredLocations" :key="location.id">
                                <div @click="selectLocation(location)" 
                                     :class="selectedLocation && selectedLocation.id === location.id ? 'bg-green-50 border-green-500' : 'bg-white hover:bg-gray-50'"
                                     class="p-4 border-b border-gray-200 cursor-pointer transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="font-semibold text-gray-900" x-text="location.building"></div>
                                            <div class="text-sm text-gray-600">Floor <span x-text="location.floor"></span> • Room <span x-text="location.room"></span></div>
                                        </div>
                                        <div x-show="selectedLocation && selectedLocation.id === location.id" class="text-green-600">
                                            <i class="fas fa-check-circle text-xl"></i>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div x-show="filteredLocations.length === 0" class="p-8 text-center text-gray-500">
                                <i class="fas fa-search text-3xl mb-2"></i>
                                <p>No locations found</p>
                            </div>
                        </div>
                    </div>

                    <!-- Selected Assets Summary -->
                    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <h4 class="font-semibold text-blue-900 mb-2">Selected Assets:</h4>
                        <div class="text-sm text-blue-800" x-text="selectedAssets.length + ' asset(s) will be deployed'"></div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3">
                        <button type="button" 
                                @click="closeBulkDeployModal()"
                                class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors font-semibold">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="submit" 
                                :disabled="!selectedLocation"
                                :class="selectedLocation ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-300 cursor-not-allowed'"
                                class="flex-1 px-6 py-3 text-white rounded-xl transition-colors font-semibold">
                            <i class="fas fa-check-circle mr-2"></i>Deploy Assets
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function bulkDeployment() {
    return {
        selectedAssets: [],
        showBulkModal: false,
        locations: [],
        filteredLocations: [],
        locationSearch: '',
        selectedLocation: null,
        
        init() {
            this.loadLocations();
        },
        
        async loadLocations() {
            try {
                const response = await fetch('/api/locations');
                this.locations = await response.json();
                this.filteredLocations = this.locations;
            } catch (error) {
                console.error('Error loading locations:', error);
            }
        },
        
        toggleAssetSelection(assetId) {
            const index = this.selectedAssets.indexOf(assetId);
            if (index > -1) {
                this.selectedAssets.splice(index, 1);
            } else {
                this.selectedAssets.push(assetId);
            }
        },
        
        filterLocations() {
            const search = this.locationSearch.toLowerCase();
            this.filteredLocations = this.locations.filter(location => {
                return location.building.toLowerCase().includes(search) ||
                       location.floor.toString().includes(search) ||
                       location.room.toLowerCase().includes(search);
            });
        },
        
        selectLocation(location) {
            this.selectedLocation = location;
        },
        
        openBulkDeployModal() {
            this.showBulkModal = true;
            this.locationSearch = '';
            this.selectedLocation = null;
            this.filteredLocations = this.locations;
        },
        
        closeBulkDeployModal() {
            this.showBulkModal = false;
        },
        
        async submitBulkDeploy() {
            if (!this.selectedLocation || this.selectedAssets.length === 0) {
                alert('Please select a location and at least one asset');
                return;
            }
            
            try {
                const response = await fetch('/gsu/assets/bulk-deploy', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        asset_ids: this.selectedAssets,
                        location_id: this.selectedLocation.id
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to deploy assets');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while deploying assets');
            }
        }
    }
}
</script>
@endsection
