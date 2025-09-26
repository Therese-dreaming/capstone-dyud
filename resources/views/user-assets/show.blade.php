@extends('layouts.user')

@section('title', 'Asset Details - ' . $asset->asset_code)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-4">
                <a href="{{ route('user-assets.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Back to My Assets
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Asset Details</h1>
                    <p class="text-lg text-gray-600">{{ $asset->asset_code }}</p>
                </div>
            </div>
        </div>

        <!-- Asset Information Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-box text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">{{ $asset->name }}</h2>
                        <p class="text-blue-100">Asset Code: {{ $asset->asset_code }}</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Asset Name</label>
                            <div class="text-lg font-semibold text-gray-900">{{ $asset->name }}</div>
                        </div>
                        
                        @if($asset->description)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <div class="text-gray-900">{{ $asset->description }}</div>
                        </div>
                        @endif
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Category</label>
                            <div class="text-gray-900">{{ $asset->category->name ?? 'N/A' }}</div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{
                                $asset->status === 'Available' ? 'bg-green-100 text-green-800' : 
                                ($asset->status === 'In Use' ? 'bg-blue-100 text-blue-800' : 
                                ($asset->status === 'Under Maintenance' ? 'bg-yellow-100 text-yellow-800' : 
                                ($asset->status === 'Lost' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')))
                            }}">
                                <i class="fas {{
                                    $asset->status === 'Available' ? 'fa-check-circle' : 
                                    ($asset->status === 'In Use' ? 'fa-user' : 
                                    ($asset->status === 'Under Maintenance' ? 'fa-wrench' : 
                                    ($asset->status === 'Lost' ? 'fa-exclamation-triangle' : 'fa-question-circle')))
                                }} mr-1"></i>
                                {{ $asset->status }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Location & Financial Information -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Location</label>
                            <div class="text-gray-900">
                                {{ $asset->location->building ?? 'N/A' }}
                                @if($asset->location)
                                <div class="text-sm text-gray-600">Floor {{ $asset->location->floor }} - Room {{ $asset->location->room }}</div>
                                @endif
                            </div>
                        </div>
                        
                        @if($asset->purchase_cost)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Purchase Cost</label>
                            <div class="text-gray-900">₱{{ number_format($asset->purchase_cost, 2) }}</div>
                        </div>
                        @endif
                        
                        @if($asset->purchase_date)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Purchase Date</label>
                            <div class="text-gray-900">{{ $asset->purchase_date->format('F j, Y') }}</div>
                        </div>
                        @endif
                        
                        @if($asset->supplier)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Supplier</label>
                            <div class="text-gray-900">{{ $asset->supplier }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-tools text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">Available Actions</h2>
                        <p class="text-red-100">What you can do with this asset</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="text-center">
                    <div class="mb-4">
                        <i class="fas fa-tools text-4xl text-red-600 mb-3"></i>
                        <h3 class="text-lg font-semibold text-gray-900">Submit Maintenance Request</h3>
                        <p class="text-gray-600 mb-4">Report issues or request maintenance for this asset</p>
                    </div>
                    <a href="{{ route('maintenance-requests.create') }}" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        <i class="fas fa-tools mr-2"></i> Submit Maintenance Request
                    </a>
                </div>
                
                <div class="mt-8 bg-gray-50 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                        <div>
                            <h4 class="font-medium text-gray-900">Asset Management Permissions</h4>
                            <p class="text-sm text-gray-600 mt-1">
                                As a location manager, you can view asset details and submit maintenance requests. 
                                Other asset operations (editing, transferring, disposing) are handled by administrators and GSU staff.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
