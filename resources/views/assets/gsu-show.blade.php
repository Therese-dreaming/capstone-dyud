@extends('layouts.gsu')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('gsu.locations.index') }}" 
                   class="inline-flex items-center justify-center w-10 h-10 bg-gray-100 text-gray-600 rounded-full hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-cube text-red-800"></i>
                        {{ $asset->name }}
                    </h1>
                    <p class="text-gray-600 mt-1">Asset Details & Information</p>
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
        <!-- LEFT COLUMN -->
        <div class="space-y-6">
            <!-- Basic Information Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        Basic Information
                    </h2>
                </div>
                <div class="p-6 space-y-5">
                    <div class="flex items-start justify-between py-3 border-b border-gray-100 last:border-b-0">
                        <div class="flex-1">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Asset Name</dt>
                            <dd class="text-base font-semibold text-gray-900">{{ $asset->name }}</dd>
                        </div>
                        <div class="ml-4">
                            <i class="fas fa-tag text-gray-400"></i>
                        </div>
                    </div>
                    
                    <div class="flex items-start justify-between py-3 border-b border-gray-100 last:border-b-0">
                        <div class="flex-1">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Asset Code</dt>
                            <dd class="font-mono text-base font-bold text-gray-900 bg-gray-50 px-2 py-1 rounded inline-block">
                                {{ $asset->asset_code }}
                            </dd>
                        </div>
                        <div class="ml-4">
                            <i class="fas fa-barcode text-gray-400"></i>
                        </div>
                    </div>
                    
                    <div class="flex items-start justify-between py-3 border-b border-gray-100 last:border-b-0">
                        <div class="flex-1">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Category</dt>
                            <dd>
                                <span class="inline-flex items-center gap-2 text-blue-600 font-medium">
                                    <i class="fas fa-folder text-sm"></i>
                                    {{ $asset->category->name ?? 'N/A' }}
                                </span>
                            </dd>
                        </div>
                        <div class="ml-4">
                            <i class="fas fa-folder text-gray-400 text-sm"></i>
                        </div>
                    </div>
                    
                    @if($asset->description)
                    <div class="flex items-start justify-between py-3 border-b border-gray-100 last:border-b-0">
                        <div class="flex-1">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Description</dt>
                            <dd class="text-base text-gray-700 leading-relaxed">{{ $asset->description }}</dd>
                        </div>
                        <div class="ml-4">
                            <i class="fas fa-file-alt text-gray-400"></i>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Location Information Card -->
            @if($asset->location)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-green-600"></i>
                        Location Details
                    </h2>
                </div>
                <div class="p-6 space-y-5">
                    <div class="flex items-start justify-between py-3 border-b border-gray-100 last:border-b-0">
                        <div class="flex-1">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Building</dt>
                            <dd class="text-base font-semibold text-gray-900">{{ $asset->location->building }}</dd>
                        </div>
                        <div class="ml-4">
                            <i class="fas fa-building text-gray-400"></i>
                        </div>
                    </div>
                    
                    <div class="flex items-start justify-between py-3 border-b border-gray-100 last:border-b-0">
                        <div class="flex-1">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Floor</dt>
                            <dd class="text-base text-gray-900">{{ $asset->location->floor }}</dd>
                        </div>
                        <div class="ml-4">
                            <i class="fas fa-layer-group text-gray-400"></i>
                        </div>
                    </div>
                    
                    <div class="flex items-start justify-between py-3 border-b border-gray-100 last:border-b-0">
                        <div class="flex-1">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Room</dt>
                            <dd class="text-base text-gray-900">{{ $asset->location->room }}</dd>
                        </div>
                        <div class="ml-4">
                            <i class="fas fa-door-open text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                        Location Status
                    </h2>
                </div>
                <div class="p-6">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-map-marker-alt text-yellow-600 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Location Not Assigned</h3>
                        <p class="text-gray-600 mb-4">This asset has not been deployed to a location yet.</p>
                        @if(!$asset->location_id)
                            <a href="{{ route('gsu.assets.assign-location', $asset) }}" 
                               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                Assign Location
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Status & Condition Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-purple-600"></i>
                        Status & Condition
                    </h2>
                </div>
                <div class="p-6 space-y-5">
                    <div class="flex items-start justify-between py-3 border-b border-gray-100 last:border-b-0">
                        <div class="flex-1">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Status</dt>
                            <dd>
                                @if($asset->status == 'Available')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Available
                                    </span>
                                @elseif($asset->status == 'In Use')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-user mr-1"></i> In Use
                                    </span>
                                @elseif($asset->status == 'Disposed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-ban mr-1"></i> Disposed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-question-circle mr-1"></i> {{ $asset->status }}
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div class="ml-4">
                            <i class="fas fa-info-circle text-gray-400"></i>
                        </div>
                    </div>
                    
                    <div class="flex items-start justify-between py-3 border-b border-gray-100 last:border-b-0">
                        <div class="flex-1">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Condition</dt>
                            <dd>
                                @if($asset->condition == 'Good')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-thumbs-up mr-1"></i> Good
                                    </span>
                                @elseif($asset->condition == 'Fair')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-minus-circle mr-1"></i> Fair
                                    </span>
                                @elseif($asset->condition == 'Poor')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-thumbs-down mr-1"></i> Poor
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div class="ml-4">
                            <i class="fas fa-tools text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="space-y-6">
            <!-- QR Code Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-qrcode text-gray-600"></i>
                        Asset QR Code
                    </h2>
                </div>
                <div class="p-6">
                    <div class="flex flex-col items-center">
                        <div class="bg-white p-4 rounded-lg border-2 border-gray-200 shadow-sm">
                            <img src="{{ route('gsu.qrcode.asset', $asset->asset_code) }}" 
                                 alt="QR Code for {{ $asset->asset_code }}" 
                                 class="w-48 h-48">
                        </div>
                        <div class="mt-4 text-center">
                            <div class="text-sm text-gray-600 mb-2">Scan to view asset details</div>
                            <div class="font-mono text-lg font-bold text-gray-900 bg-gray-100 px-3 py-1 rounded">
                                {{ $asset->asset_code }}
                            </div>
                        </div>
                        <div class="mt-4 w-full space-y-2">
                            <a href="{{ route('gsu.qrcode.asset.download', $asset->asset_code) }}" 
                               class="w-full inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                                <i class="fas fa-download"></i>
                                Download PNG
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Information Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-dollar-sign text-yellow-600"></i>
                        Financial Information
                    </h2>
                </div>
                <div class="p-6 space-y-5">
                    <div class="flex items-start justify-between py-3 border-b border-gray-100 last:border-b-0">
                        <div class="flex-1">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Purchase Cost</dt>
                            <dd class="text-base font-semibold text-gray-900">${{ number_format($asset->purchase_cost, 2) }}</dd>
                        </div>
                        <div class="ml-4">
                            <i class="fas fa-dollar-sign text-gray-400"></i>
                        </div>
                    </div>
                    
                    <div class="flex items-start justify-between py-3 border-b border-gray-100 last:border-b-0">
                        <div class="flex-1">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Purchase Date</dt>
                            <dd class="text-base text-gray-900">{{ $asset->purchase_date->format('M d, Y') }}</dd>
                        </div>
                        <div class="ml-4">
                            <i class="fas fa-calendar text-gray-400"></i>
                        </div>
                    </div>
                    
                    <div class="flex items-start justify-between py-3 border-b border-gray-100 last:border-b-0">
                        <div class="flex-1">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Age</dt>
                            <dd class="text-base text-gray-900">{{ $asset->purchase_date->diffForHumans() }}</dd>
                        </div>
                        <div class="ml-4">
                            <i class="fas fa-clock text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Warranty Information Card -->
            @if($asset->warranty)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-shield-alt text-indigo-600"></i>
                        Warranty Information
                    </h2>
                </div>
                <div class="p-6 space-y-5">
                    <div class="flex items-start justify-between py-3 border-b border-gray-100 last:border-b-0">
                        <div class="flex-1">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Manufacturer</dt>
                            <dd class="text-base font-semibold text-gray-900">{{ $asset->warranty->manufacturer }}</dd>
                        </div>
                        <div class="ml-4">
                            <i class="fas fa-industry text-gray-400"></i>
                        </div>
                    </div>
                    
                    <div class="flex items-start justify-between py-3 border-b border-gray-100 last:border-b-0">
                        <div class="flex-1">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Model</dt>
                            <dd class="text-base text-gray-900">{{ $asset->warranty->model }}</dd>
                        </div>
                        <div class="ml-4">
                            <i class="fas fa-tag text-gray-400"></i>
                        </div>
                    </div>
                    
                    <div class="flex items-start justify-between py-3 border-b border-gray-100 last:border-b-0">
                        <div class="flex-1">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Warranty Expiry</dt>
                            <dd class="text-base text-gray-900">
                                {{ $asset->warranty->warranty_expiry->format('M d, Y') }}
                                @if($asset->warranty->warranty_expiry->isPast())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 ml-2">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> Expired
                                    </span>
                                @elseif($asset->warranty->warranty_expiry->diffInDays(now()) <= 30)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 ml-2">
                                        <i class="fas fa-clock mr-1"></i> Expiring Soon
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 ml-2">
                                        <i class="fas fa-check-circle mr-1"></i> Active
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div class="ml-4">
                            <i class="fas fa-calendar-check text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Action Buttons Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-red-50 to-red-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-cogs text-red-600"></i>
                        GSU Actions
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-3">
                        @if(!$asset->location_id)
                            <a href="{{ route('gsu.assets.assign-location', $asset) }}" 
                               class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                Assign Location
                            </a>
                        @else
                            <div class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-600 rounded-lg">
                                <i class="fas fa-check-circle mr-2"></i>
                                Asset Deployed
                            </div>
                        @endif
                        
                        <a href="{{ route('gsu.assets.index') }}" 
                           class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-boxes mr-2"></i>
                            Back to Assets
                        </a>
                        
                        <a href="{{ route('gsu.qr.scanner') }}" 
                           class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            <i class="fas fa-qrcode mr-2"></i>
                            QR Scanner
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection 