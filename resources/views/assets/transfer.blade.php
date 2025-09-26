@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('assets.show', $asset) }}" 
                   class="inline-flex items-center justify-center w-10 h-10 bg-gray-100 text-gray-600 rounded-full hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-exchange-alt text-blue-600"></i>
                        Transfer Asset
                    </h1>
                    <p class="text-gray-600 mt-1">Move {{ $asset->name }} to a new location</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500">Asset Code</div>
                <div class="font-mono text-lg font-bold text-gray-900 bg-gray-100 px-3 py-1 rounded">
                    {{ $asset->asset_code }}
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Current Asset Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-600"></i>
                    Current Asset Information
                </h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500 mb-1">Asset Name</dt>
                    <dd class="text-base font-semibold text-gray-900">{{ $asset->name }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500 mb-1">Category</dt>
                    <dd class="text-base text-gray-900">{{ $asset->category->name }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500 mb-1">Current Location</dt>
                    <dd class="bg-green-50 rounded-lg p-3 border border-green-200">
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div>
                                <div class="text-xs font-medium text-green-600 mb-1">BUILDING</div>
                                <div class="font-bold text-gray-900">{{ $asset->location->building }}</div>
                            </div>
                            <div class="border-l border-r border-green-200">
                                <div class="text-xs font-medium text-green-600 mb-1">FLOOR</div>
                                <div class="font-bold text-gray-900">{{ $asset->location->floor }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-medium text-green-600 mb-1">ROOM</div>
                                <div class="font-bold text-gray-900">{{ $asset->location->room }}</div>
                            </div>
                        </div>
                    </dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500 mb-1">Status</dt>
                    <dd>
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $asset->getStatusBadgeClass() }}">
                            {{ $asset->status }}
                        </span>
                    </dd>
                </div>
            </div>
        </div>

        <!-- Transfer Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-orange-50 to-orange-100 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-orange-600"></i>
                    Transfer Details
                </h2>
            </div>
            <div class="p-6">
                <form action="{{ route('assets.transfer', $asset) }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="new_location_id" class="block text-sm font-medium text-gray-700 mb-2">
                            New Location <span class="text-red-500">*</span>
                        </label>
                        <select name="new_location_id" id="new_location_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a location...</option>
                            @foreach($locations as $location)
                                @if($location->id !== $asset->location_id)
                                    <option value="{{ $location->id }}" {{ old('new_location_id') == $location->id ? 'selected' : '' }}>
                                        {{ $location->building }} - Floor {{ $location->floor }} - Room {{ $location->room }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('new_location_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="transfer_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Transfer Reason
                        </label>
                        <textarea name="transfer_reason" id="transfer_reason" rows="4"
                                  placeholder="Optional: Explain why this asset is being transferred..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('transfer_reason') }}</textarea>
                        @error('transfer_reason')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">
                            This will be recorded in the asset's change history.
                        </p>
                    </div>
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-3"></i>
                            <div>
                                <h4 class="text-sm font-medium text-yellow-800 mb-1">Transfer Confirmation</h4>
                                <p class="text-sm text-yellow-700">
                                    This action will move the asset from its current location to the selected new location. 
                                    The change will be recorded in the asset's history and relevant users will be notified.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex gap-4 pt-4">
                        <button type="submit" 
                                class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                            <i class="fas fa-exchange-alt"></i>
                            Transfer Asset
                        </button>
                        
                        <a href="{{ route('assets.show', $asset) }}" 
                           class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                            <i class="fas fa-times"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
