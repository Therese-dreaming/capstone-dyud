@extends('layouts.gsu')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-red-50">
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
                <div class="text-right">
                    <div class="text-sm text-red-200">Asset Code</div>
                    <div class="font-mono text-lg font-bold text-white bg-white/20 px-3 py-1 rounded">
                        {{ $asset->asset_code }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <div class="max-w-4xl mx-auto">
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
                    <form action="{{ route('gsu.assets.update-location', $asset) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
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

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('gsu.assets.index') }}" 
                               class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Back to Assets
                            </a>
                            <button type="submit" 
                                    class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium shadow-sm">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                Deploy Asset to Location
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Add visual feedback when location is selected
document.getElementById('location_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    if (selectedOption.value) {
        this.classList.add('border-green-500', 'bg-green-50');
        this.classList.remove('border-gray-300');
    } else {
        this.classList.remove('border-green-500', 'bg-green-50');
        this.classList.add('border-gray-300');
    }
});
</script>
@endsection
