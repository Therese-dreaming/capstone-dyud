@extends('layouts.gsu')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-red-50" x-data="bulkDeployment()">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-red-800 to-red-900 text-white p-6 mb-6 rounded-xl shadow-lg relative overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative z-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
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
                        <div class="text-sm text-red-200">Pending Deploy</div>
                        <div class="text-2xl font-bold text-white">{{ $pendingCount }}</div>
                    </div>
                    <template x-if="selectAllPages">
                        <button @click="openBulkDeployModal()"
                                class="bg-white text-red-800 px-6 py-3 rounded-lg font-semibold hover:bg-red-50 transition-colors shadow-lg flex items-center gap-2">
                            <i class="fas fa-layer-group"></i>
                            <span>Bulk Deploy All ({{ $pendingCount }})</span>
                        </button>
                    </template>
                    <template x-if="!selectAllPages && selectedAssets.length > 0">
                        <button @click="openBulkDeployModal()"
                                class="bg-white text-red-800 px-6 py-3 rounded-lg font-semibold hover:bg-red-50 transition-colors shadow-lg flex items-center gap-2">
                            <i class="fas fa-layer-group"></i>
                            <span>Bulk Deploy (<span x-text="selectedAssets.length"></span>)</span>
                        </button>
                    </template>
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
                        <div class="text-2xl font-bold text-gray-900">{{ $pendingCount }}</div>
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
                        <div class="text-2xl font-bold text-gray-900">{{ $deployedCount }}</div>
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
                        <div class="text-2xl font-bold text-gray-900">{{ $totalCount }}</div>
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
                        <div class="text-lg font-bold text-gray-900">₱{{ number_format($totalValue, 0) }}</div>
                        <div class="text-sm text-gray-500">Total Value</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs (server-side) -->
        <div class="mb-6">
            <div class="border-b border-gray-200 mb-6">
                <nav class="flex space-x-8">
                    <a href="{{ route('gsu.assets.index', ['tab' => 'pending']) }}"
                       class="py-4 px-1 border-b-2 font-semibold text-sm flex items-center gap-2 transition-colors {{ $tab === 'pending' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <div class="p-2 rounded-lg transition-colors {{ $tab === 'pending' ? 'bg-yellow-100' : 'bg-gray-100' }}">
                            <i class="fas fa-clock {{ $tab === 'pending' ? 'text-yellow-600' : 'text-gray-400' }}"></i>
                        </div>
                        Pending Deployment
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $tab === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-600' }}">
                            {{ $pendingCount }}
                        </span>
                    </a>
                    
                    <a href="{{ route('gsu.assets.index', ['tab' => 'deployed']) }}"
                       class="py-4 px-1 border-b-2 font-semibold text-sm flex items-center gap-2 transition-colors {{ $tab === 'deployed' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <div class="p-2 rounded-lg transition-colors {{ $tab === 'deployed' ? 'bg-green-100' : 'bg-gray-100' }}">
                            <i class="fas fa-check-circle {{ $tab === 'deployed' ? 'text-green-600' : 'text-gray-400' }}"></i>
                        </div>
                        Deployed Assets
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $tab === 'deployed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                            {{ $deployedCount }}
                        </span>
                    </a>
                </nav>
            </div>

            @if($tab === 'pending' && $pendingCount > 0)
            <!-- Selection Controls -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-4">
                        <!-- Select All on Page -->
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" 
                                   class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                                   @change="toggleSelectAllPage($event.target.checked)"
                                   :checked="allOnPageSelected"
                                   :indeterminate="someOnPageSelected && !allOnPageSelected">
                            <span class="text-sm font-medium text-gray-700">Select all on this page</span>
                        </label>

                        <!-- Divider -->
                        <div class="hidden sm:block w-px h-5 bg-gray-300"></div>

                        <!-- Select All Across Pages -->
                        @if($assets->lastPage() > 1)
                        <button type="button"
                                @click="toggleSelectAllPages()"
                                :class="selectAllPages ? 'bg-green-100 text-green-800 border-green-300' : 'bg-gray-50 text-gray-700 border-gray-300 hover:bg-gray-100'"
                                class="text-sm font-medium px-3 py-1.5 rounded-lg border transition-colors">
                            <template x-if="!selectAllPages">
                                <span><i class="fas fa-check-double mr-1"></i>Select all {{ $pendingCount }} across all pages</span>
                            </template>
                            <template x-if="selectAllPages">
                                <span><i class="fas fa-check-double mr-1"></i>All {{ $pendingCount }} selected — click to clear</span>
                            </template>
                        </button>
                        @endif
                    </div>

                    <!-- Selection Info -->
                    <div class="text-sm text-gray-500" x-show="selectedAssets.length > 0 || selectAllPages">
                        <template x-if="selectAllPages">
                            <span class="font-semibold text-green-700"><i class="fas fa-check-circle mr-1"></i>All {{ $pendingCount }} pending assets selected</span>
                        </template>
                        <template x-if="!selectAllPages && selectedAssets.length > 0">
                            <span><span x-text="selectedAssets.length" class="font-semibold"></span> asset(s) selected</span>
                        </template>
                    </div>
                </div>
            </div>
            @endif

            <!-- Asset Cards -->
            @if($assets->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($assets as $asset)
                    <div @if($tab === 'pending') 
                             @click="toggleAssetSelection({{ $asset->id }})" 
                             :class="{
                                 'ring-4 ring-green-500 border-green-500 shadow-xl': selectedAssets.includes({{ $asset->id }}) || selectAllPages,
                                 'border-gray-200': !selectedAssets.includes({{ $asset->id }}) && !selectAllPages
                             }"
                             class="cursor-pointer"
                         @else
                             class="opacity-75 border-gray-200"
                         @endif
                         class="bg-white rounded-xl shadow-sm border overflow-hidden hover:shadow-lg transition-all duration-300 relative group">
                        
                        @if($tab === 'pending')
                        <!-- Selection indicator -->
                        <div class="absolute top-3 right-3 z-10"
                             x-show="selectedAssets.includes({{ $asset->id }}) || selectAllPages">
                            <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white text-xs"></i>
                            </div>
                        </div>
                        @endif

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
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Purchase Cost</span>
                                    <span class="text-lg font-semibold text-gray-900">₱{{ number_format($asset->purchase_cost, 2) }}</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Created By</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $asset->createdBy->name ?? 'Unknown' }}</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Asset Status</span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ 
                                        $asset->status === 'Available' ? 'bg-green-100 text-green-800' : 
                                        ($asset->status === 'In Use' ? 'bg-blue-100 text-blue-800' : 
                                        ($asset->status === 'For Repair' ? 'bg-yellow-100 text-yellow-800' : 
                                        ($asset->status === 'For Maintenance' ? 'bg-orange-100 text-orange-800' :
                                        ($asset->status === 'Lost' ? 'bg-red-100 text-red-800' : 
                                        ($asset->status === 'Disposed' ? 'bg-gray-100 text-gray-800' : 'bg-gray-100 text-gray-800')))))
                                    }}">
                                        <i class="fas {{ 
                                            $asset->status === 'Available' ? 'fa-check-circle' : 
                                            ($asset->status === 'In Use' ? 'fa-user' : 
                                            ($asset->status === 'For Repair' ? 'fa-wrench' : 
                                            ($asset->status === 'For Maintenance' ? 'fa-tools' :
                                            ($asset->status === 'Lost' ? 'fa-exclamation-triangle' : 
                                            ($asset->status === 'Disposed' ? 'fa-trash' : 'fa-question-circle')))))
                                        }} mr-1"></i>
                                        {{ $asset->status }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Deployment</span>
                                    @if($asset->location_id)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-map-marker-alt mr-1"></i>Deployed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>Pending
                                        </span>
                                    @endif
                                </div>

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

            <!-- Pagination -->
            @if($assets->hasPages())
            <div class="mt-8">
                {{ $assets->links() }}
            </div>
            @endif
            @else
                @if($tab === 'pending')
                <div class="bg-yellow-50 rounded-xl border border-yellow-200 p-12 text-center">
                    <div class="w-24 h-24 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-clock text-yellow-400 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Pending Assets</h3>
                    <p class="text-gray-600">All assets have been deployed!</p>
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
            @endif
        </div>

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
                        <p class="text-sm text-blue-600 mt-1">Click cards to select them, then use Bulk Deploy. Use "Select all across all pages" to deploy everything at once.</p>
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
                        <template x-if="selectAllPages">
                            <div class="text-sm text-blue-800">
                                <i class="fas fa-check-double mr-1"></i>
                                All {{ $pendingCount }} pending asset(s) across all pages will be deployed
                            </div>
                        </template>
                        <template x-if="!selectAllPages">
                            <div class="text-sm text-blue-800" x-text="selectedAssets.length + ' asset(s) will be deployed'"></div>
                        </template>
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
        selectAllPages: false,
        showBulkModal: false,
        locations: [],
        filteredLocations: [],
        locationSearch: '',
        selectedLocation: null,
        pageAssetIds: @json($tab === 'pending' ? $assets->pluck('id')->values() : []),
        
        get allOnPageSelected() {
            if (this.pageAssetIds.length === 0) return false;
            return this.pageAssetIds.every(id => this.selectedAssets.includes(id));
        },
        
        get someOnPageSelected() {
            return this.pageAssetIds.some(id => this.selectedAssets.includes(id));
        },
        
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
            if (this.selectAllPages) return; // Don't toggle individual when all pages selected
            const index = this.selectedAssets.indexOf(assetId);
            if (index > -1) {
                this.selectedAssets.splice(index, 1);
            } else {
                this.selectedAssets.push(assetId);
            }
        },
        
        toggleSelectAllPage(checked) {
            this.selectAllPages = false;
            if (checked) {
                // Add all page asset IDs that aren't already selected
                this.pageAssetIds.forEach(id => {
                    if (!this.selectedAssets.includes(id)) {
                        this.selectedAssets.push(id);
                    }
                });
            } else {
                // Remove all page asset IDs
                this.selectedAssets = this.selectedAssets.filter(id => !this.pageAssetIds.includes(id));
            }
        },
        
        toggleSelectAllPages() {
            if (this.selectAllPages) {
                // Deselect all
                this.selectAllPages = false;
                this.selectedAssets = [];
            } else {
                // Select all across all pages
                this.selectAllPages = true;
                // Also select all on current page visually
                this.pageAssetIds.forEach(id => {
                    if (!this.selectedAssets.includes(id)) {
                        this.selectedAssets.push(id);
                    }
                });
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
            if (!this.selectedLocation) {
                alert('Please select a location');
                return;
            }
            
            if (!this.selectAllPages && this.selectedAssets.length === 0) {
                alert('Please select at least one asset');
                return;
            }
            
            try {
                const body = {
                    location_id: this.selectedLocation.id
                };
                
                if (this.selectAllPages) {
                    body.deploy_all = true;
                } else {
                    body.asset_ids = this.selectedAssets;
                }
                
                const response = await fetch('/gsu/assets/bulk-deploy', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(body)
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
