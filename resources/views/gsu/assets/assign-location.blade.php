@extends('layouts.gsu')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-red-50" x-data="deploymentPage()">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-red-800 to-red-900 text-white p-6 mb-6 rounded-xl shadow-lg relative overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-white/20 p-3 rounded-full">
                        <i class="fas fa-map-marker-alt text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold">Deploy Asset to Location</h1>
                        <p class="text-red-100 text-sm md:text-base">Assign a permanent location for asset deployment</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <div class="text-sm text-red-200">Asset Code</div>
                        <div class="font-mono text-lg font-bold text-white bg-white/20 px-3 py-1 rounded">
                            {{ $asset->asset_code }}
                        </div>
                    </div>
                    <button type="button" 
                            @click="showBulkSelector = !showBulkSelector"
                            class="bg-white text-red-800 px-4 py-2 rounded-lg font-semibold hover:bg-red-50 transition-colors shadow-lg flex items-center gap-2">
                        <i class="fas fa-layer-group"></i>
                        <span x-text="showBulkSelector ? 'Single Deploy' : 'Bulk Deploy'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <div class="max-w-4xl mx-auto">
            <!-- Bulk Deployment Selector -->
            <div x-show="showBulkSelector" 
                 x-transition
                 class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-layer-group text-purple-600"></i>
                        Bulk Asset Selection
                    </h2>
                    <p class="text-sm text-purple-700 mt-1">Select multiple assets to deploy to the same location</p>
                </div>
                <div class="p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            <span x-text="selectedAssets.length"></span> asset(s) selected
                        </div>
                        <button type="button" 
                                @click="selectAllPendingAssets()"
                                class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            <i class="fas fa-check-double mr-1"></i>Select All Pending
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-96 overflow-y-auto">
                        @foreach($allPendingAssets ?? [] as $pendingAsset)
                            <div @click="toggleAssetSelection({{ $pendingAsset->id }})"
                                 :class="selectedAssets.includes({{ $pendingAsset->id }}) ? 'ring-2 ring-purple-500 bg-purple-50' : 'bg-gray-50'"
                                 class="p-4 rounded-lg border border-gray-200 cursor-pointer hover:shadow-md transition-all">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900">{{ $pendingAsset->asset_code }}</div>
                                        <div class="text-sm text-gray-600">{{ $pendingAsset->name }}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $pendingAsset->category->name ?? 'N/A' }}
                                        </div>
                                    </div>
                                    <div x-show="selectedAssets.includes({{ $pendingAsset->id }})" 
                                         class="text-purple-600">
                                        <i class="fas fa-check-circle text-xl"></i>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if(empty($allPendingAssets) || count($allPendingAssets) === 0)
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-inbox text-3xl mb-2"></i>
                            <p>No other pending assets available</p>
                        </div>
                    @endif
                </div>
            </div>
            <!-- Asset Information Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        Asset Information
                    </h2>
                    <p class="text-sm text-blue-700 mt-1">Review asset details before deployment</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-500">Asset Code</div>
                                    <div class="font-mono text-lg font-bold text-gray-900">{{ $asset->asset_code }}</div>
                                </div>
                                <div class="bg-blue-100 p-2 rounded-full">
                                    <i class="fas fa-barcode text-blue-600"></i>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-500">Asset Name</div>
                                    <div class="text-lg font-semibold text-gray-900">{{ $asset->name }}</div>
                                </div>
                                <div class="bg-green-100 p-2 rounded-full">
                                    <i class="fas fa-tag text-green-600"></i>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-500">Category</div>
                                    <div class="text-base font-medium text-gray-900">{{ $asset->category->name ?? 'N/A' }}</div>
                                </div>
                                <div class="bg-purple-100 p-2 rounded-full">
                                    <i class="fas fa-folder text-purple-600"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-500">Purchase Cost</div>
                                    <div class="text-lg font-bold text-gray-900">₱{{ number_format($asset->purchase_cost, 2) }}</div>
                                </div>
                                <div class="bg-yellow-100 p-2 rounded-full">
                                    <i class="fas fa-dollar-sign text-yellow-600"></i>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-500">Condition</div>
                                    <div class="text-base font-medium text-gray-900">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                            {{ $asset->condition === 'Good' ? 'bg-green-100 text-green-800' : 
                                               ($asset->condition === 'Fair' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $asset->condition }}
                                        </span>
                                    </div>
                                </div>
                                <div class="bg-indigo-100 p-2 rounded-full">
                                    <i class="fas fa-tools text-indigo-600"></i>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-500">Approval Status</div>
                                    <div class="text-base font-medium text-gray-900">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Approved
                                        </span>
                                    </div>
                                </div>
                                <div class="bg-emerald-100 p-2 rounded-full">
                                    <i class="fas fa-shield-check text-emerald-600"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Assignment Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-green-600"></i>
                        Location Assignment
                    </h2>
                    <p class="text-sm text-green-700 mt-1">Select the permanent location for this asset</p>
                </div>
                <div class="p-6">
                    <form @submit.prevent="submitDeployment()" class="space-y-6">
                        
                        <!-- Location Selection -->
                        <div>
                            <label for="location_id" class="block text-sm font-medium text-gray-700 mb-3">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                Select Deployment Location <span class="text-red-500">*</span>
                            </label>
                            <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('location_id') border-red-500 @enderror" 
                                    id="location_id" name="location_id" required>
                                <option value="">Choose a location for deployment...</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                        {{ $location->building }} - Floor {{ $location->floor }} - Room {{ $location->room }}
                                        @if($location->description)
                                            ({{ $location->description }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('location_id')
                                <div class="mt-2 text-sm text-red-600 flex items-center gap-2">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                                <div class="flex items-start gap-2 text-blue-700">
                                    <i class="fas fa-lightbulb mt-0.5"></i>
                                    <div class="text-sm">
                                        <strong>Important:</strong> This will be the asset's permanent location. Choose carefully as this affects inventory tracking and asset management.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Warning Notice -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <div class="bg-yellow-100 p-2 rounded-full">
                                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                                </div>
                                <div>
                                    <h4 class="text-yellow-800 font-semibold mb-1">Deployment Confirmation Required</h4>
                                    <p class="text-yellow-700 text-sm">
                                        Once you assign a location, the asset will be marked as "Available" and deployed in the system. 
                                        Please ensure the physical asset is actually placed at the selected location before confirming.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Selected Assets Summary (for bulk) -->
                        <div x-show="showBulkSelector && selectedAssets.length > 0" 
                             class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <h4 class="font-semibold text-purple-900 mb-2">
                                <i class="fas fa-layer-group mr-2"></i>Bulk Deployment Summary
                            </h4>
                            <div class="text-sm text-purple-800">
                                <span x-text="selectedAssets.length"></span> asset(s) will be deployed to the selected location
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('gsu.assets.index') }}" 
                               class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Back to Assets
                            </a>
                            <button type="submit" 
                                    :disabled="showBulkSelector && selectedAssets.length === 0"
                                    :class="(showBulkSelector && selectedAssets.length === 0) ? 'opacity-50 cursor-not-allowed' : ''"
                                    class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium shadow-sm">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                <span x-text="showBulkSelector ? 'Deploy ' + selectedAssets.length + ' Asset(s)' : 'Deploy Asset to Location'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deploymentPage() {
    return {
        showBulkSelector: false,
        selectedAssets: [{{ $asset->id }}], // Start with current asset selected
        
        toggleAssetSelection(assetId) {
            const index = this.selectedAssets.indexOf(assetId);
            if (index > -1) {
                // Don't allow deselecting if it's the only asset and we're in single mode
                if (this.selectedAssets.length > 1 || this.showBulkSelector) {
                    this.selectedAssets.splice(index, 1);
                }
            } else {
                this.selectedAssets.push(assetId);
            }
        },
        
        selectAllPendingAssets() {
            const allAssetIds = @json(($allPendingAssets ?? collect())->pluck('id')->toArray());
            if (this.selectedAssets.length === allAssetIds.length) {
                // Deselect all except current asset
                this.selectedAssets = [{{ $asset->id }}];
            } else {
                // Select all
                this.selectedAssets = [...allAssetIds];
            }
        },
        
        async submitDeployment() {
            const locationId = document.getElementById('location_id').value;
            
            if (!locationId) {
                alert('Please select a location');
                return;
            }
            
            if (this.showBulkSelector && this.selectedAssets.length === 0) {
                alert('Please select at least one asset');
                return;
            }
            
            try {
                let response;
                
                if (this.showBulkSelector && this.selectedAssets.length > 1) {
                    // Bulk deployment
                    response = await fetch('/gsu/assets/bulk-deploy', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            asset_ids: this.selectedAssets,
                            location_id: parseInt(locationId)
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok) {
                        window.location.href = '{{ route('gsu.assets.index') }}';
                    } else {
                        alert(data.message || 'Failed to deploy assets');
                    }
                } else {
                    // Single deployment - use traditional form submission
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('gsu.assets.update-location', $asset) }}';
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    form.appendChild(csrfInput);
                    
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PUT';
                    form.appendChild(methodInput);
                    
                    const locationInput = document.createElement('input');
                    locationInput.type = 'hidden';
                    locationInput.name = 'location_id';
                    locationInput.value = locationId;
                    form.appendChild(locationInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while deploying assets');
            }
        }
    }
}

// Add visual feedback when location is selected
document.addEventListener('DOMContentLoaded', function() {
    const locationSelect = document.getElementById('location_id');
    if (locationSelect) {
        locationSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                this.classList.add('border-green-500', 'bg-green-50');
                this.classList.remove('border-gray-300');
            } else {
                this.classList.remove('border-green-500', 'bg-green-50');
                this.classList.add('border-gray-300');
            }
        });
    }
});
</script>
@endsection
