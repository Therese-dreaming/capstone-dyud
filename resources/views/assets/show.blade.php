@extends('layouts.admin')

@section('title', $asset->name . ' - Asset Details')

@section('content')
<div class="max-w-7xl mx-auto py-4 md:py-6 px-2 sm:px-4 lg:px-8">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 mb-4 md:mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3 md:gap-4 flex-1 min-w-0">
                <a href="{{ route(request()->routeIs('gsu.*') ? 'gsu.locations.index' : 'locations.index') }}" 
                   class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 bg-gray-100 text-gray-600 rounded-full hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="flex-1 min-w-0">
                    <h1 class="text-lg md:text-xl lg:text-2xl font-bold text-gray-900 flex items-center gap-2 md:gap-3 truncate">
                        <i class="fas fa-cube text-red-800"></i>
                        {{ $asset->name }}
                    </h1>
                    <p class="text-gray-600 mt-1 text-xs md:text-sm">Asset Details & Information</p>
                </div>
            </div>
            <div class="text-left sm:text-right flex-shrink-0">
                <div class="text-xs md:text-sm text-gray-500">Asset Code</div>
                <div class="font-mono text-sm md:text-base lg:text-lg font-bold text-gray-900 bg-gray-100 px-2 md:px-3 py-1 rounded">
                    {{ $asset->asset_code }}
                </div>
            </div>
        </div>
        
        <!-- Quick Actions Row -->
        <div class="mt-3 md:mt-4 pt-3 md:pt-4 border-t border-gray-200">
            <div class="flex items-center justify-between mb-2 md:mb-3">
                <div class="text-xs md:text-sm text-gray-600">
                    <i class="fas fa-tools mr-1"></i>Quick Actions
                </div>
            </div>
            <div class="grid grid-cols-2 sm:flex sm:flex-wrap gap-2 md:gap-3">
                <!-- Back to Assets Button -->
                <a href="{{ route(request()->routeIs('gsu.*') ? 'gsu.locations.index' : 'locations.index') }}" 
                   class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white font-semibold py-2 px-3 md:px-4 rounded-lg transition duration-200 flex items-center gap-2 shadow-md hover:shadow-lg text-xs md:text-sm">
                    <i class="fas fa-list text-xs md:text-sm"></i>
                    <span class="hidden sm:inline">Back to Assets</span><span class="sm:hidden">Back</span>
                </a>
                
                <!-- Transfer Asset Button -->
                @if($asset->isAvailable() && $asset->location_id && (auth()->user()->role === 'admin' || auth()->user()->role === 'gsu'))
                    <a href="{{ route(request()->routeIs('gsu.*') ? 'gsu.assets.transfer-form' : 'assets.transfer-form', $asset) }}" 
                       class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-2 px-3 md:px-4 rounded-lg transition duration-200 flex items-center gap-2 shadow-md hover:shadow-lg text-xs md:text-sm">
                        <i class="fas fa-exchange-alt text-xs md:text-sm"></i>
                        <span class="hidden sm:inline">Transfer Asset</span><span class="sm:hidden">Transfer</span>
                    </a>
                @else
                    <div class="bg-gray-100 text-gray-500 font-semibold py-2 px-3 md:px-4 rounded-lg flex items-center gap-2 border border-gray-200 text-xs md:text-sm" 
                         title="{{ !$asset->isAvailable() ? 'Asset is not available for transfer' : (!$asset->location_id ? 'Asset must be deployed to a location first' : 'Transfer restricted to Admin and GSU roles') }}">
                        <i class="fas fa-exchange-alt text-xs md:text-sm"></i>
                        <span class="hidden sm:inline">Transfer Asset</span><span class="sm:hidden">Transfer</span>
                    </div>
                @endif
                
                <!-- Dispose Asset Button -->
                @if($asset->isAvailable())
                    <button onclick="openDisposeModal({{ $asset->id }}, '{{ $asset->asset_code }}')" 
                            class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold py-2 px-3 md:px-4 rounded-lg transition duration-200 flex items-center gap-2 shadow-md hover:shadow-lg text-xs md:text-sm">
                        <i class="fas fa-trash text-xs md:text-sm"></i>
                        <span class="hidden sm:inline">Dispose Asset</span><span class="sm:hidden">Dispose</span>
                    </button>
                @endif
                
                <!-- Print QR Code Button -->
                <button onclick="printQRCode()" 
                        class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold py-2 px-3 md:px-4 rounded-lg transition duration-200 flex items-center gap-2 shadow-md hover:shadow-lg text-xs md:text-sm">
                    <i class="fas fa-print text-xs md:text-sm"></i>
                    <span class="hidden sm:inline">Print QR Code</span><span class="sm:hidden">Print</span>
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- LEFT COLUMN -->
        <div class="space-y-6">
            <!-- Basic Information Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-4 md:px-6 py-3 md:py-4 border-b border-gray-200">
                    <h2 class="text-base md:text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        Basic Information
                    </h2>
                </div>
                <div class="p-4 md:p-6 space-y-5">
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
                                <a href="{{ route('categories.show', $asset->category) }}" 
                                   class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium hover:underline">
                                    <i class="fas fa-folder text-sm"></i>
                                    {{ $asset->category->name }}
                                </a>
                            </dd>
                        </div>
                        <div class="ml-4">
                            <i class="fas fa-external-link-alt text-gray-400 text-sm"></i>
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
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-green-600"></i>
                        Location Details
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-4">
                        @if($asset->location)
                        <div class="bg-green-50 rounded-lg p-4">
                            <a href="{{ route('locations.show', $asset->location->id) }}" 
                               class="block hover:bg-green-100 transition-colors rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div class="grid grid-cols-3 gap-4 flex-1">
                                        <div class="text-center">
                                            <div class="text-xs font-medium text-green-600 mb-1">BUILDING</div>
                                            <div class="font-bold text-gray-900">{{ $asset->location->building }}</div>
                                        </div>
                                        <div class="text-center border-l border-r border-green-200">
                                            <div class="text-xs font-medium text-green-600 mb-1">FLOOR</div>
                                            <div class="font-bold text-gray-900">{{ $asset->location->floor }}</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-xs font-medium text-green-600 mb-1">ROOM</div>
                                            <div class="font-bold text-gray-900">{{ $asset->location->room }}</div>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <i class="fas fa-external-link-alt text-green-600 text-sm"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @else
                        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                            <div class="flex items-center justify-center text-yellow-800">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <span class="font-medium">No location assigned</span>
                            </div>
                            <p class="text-sm text-yellow-700 text-center mt-2">
                                This asset has not been deployed to a location yet.
                            </p>
                        </div>
                        @endif
                        @if($asset->originalLocation && $asset->location && $asset->originalLocation->id !== $asset->location->id)
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="text-xs font-medium text-blue-600 mb-1">ORIGINAL LOCATION</div>
                                    <div class="font-semibold text-gray-900">
                                        @if($asset->originalLocation)
                                            {{ $asset->originalLocation->building }} - Floor {{ $asset->originalLocation->floor }} - Room {{ $asset->originalLocation->room }}
                                        @else
                                            No original location recorded
                                        @endif
                                    </div>
                                    <div class="text-xs text-blue-600 mt-1">
                                        <i class="fas fa-info-circle mr-1"></i>Currently borrowed - will return here
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <i class="fas fa-home text-blue-600"></i>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- 🛡️ Warranty Information Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-orange-50 to-orange-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-shield-alt text-orange-600"></i>
                        Warranty Protection
                    </h2>
                </div>
                
                @if($asset->warranty)
                    <div class="p-6 space-y-5">
                        <div class="flex items-start justify-between py-3 border-b border-gray-100">
                            <div class="flex-1">
                                <dt class="text-sm font-medium text-gray-500 mb-1">Manufacturer</dt>
                                <dd class="text-base font-semibold text-gray-900">{{ $asset->warranty->manufacturer }}</dd>
                            </div>
                            <div class="ml-4">
                                <i class="fas fa-industry text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div class="flex items-start justify-between py-3 border-b border-gray-100">
                            <div class="flex-1">
                                <dt class="text-sm font-medium text-gray-500 mb-1">Model</dt>
                                <dd class="text-base font-semibold text-gray-900">{{ $asset->warranty->model }}</dd>
                            </div>
                            <div class="ml-4">
                                <i class="fas fa-cog text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div class="flex items-start justify-between py-3">
                            <div class="flex-1">
                                <dt class="text-sm font-medium text-gray-500 mb-1">Warranty Expiry</dt>
                                <dd class="text-base font-medium text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-calendar-times text-gray-400"></i>
                                    {{ $asset->warranty->warranty_expiry->format('F d, Y') }}
                                </dd>
                                @php
                                    $isExpired = $asset->warranty->isExpired();
                                    $isExpiringSoon = $asset->warranty->isExpiringSoon();
                                @endphp
                                <div class="mt-2">
                                    @if($isExpired)
                                        <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-800 border border-red-200">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Expired
                                        </span>
                                    @elseif($isExpiringSoon)
                                        <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                                            <i class="fas fa-clock mr-1"></i>Expiring Soon
                                        </span>
                                    @else
                                        <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                            <i class="fas fa-check mr-1"></i>Active
                                        </span>
                                    @endif
                                    <span class="text-xs text-gray-500 ml-2">{{ $asset->warranty->warranty_expiry->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    @empty($asset->warranty)
                        <div class="p-8 text-center">
                            <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-shield-alt text-2xl text-orange-400"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900 mb-2">No Warranty Information</h4>
                            <p class="text-gray-600 mb-4 max-w-sm mx-auto">
                                Warranty details are not available for this asset. This may affect support and maintenance coverage.
                            </p>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 max-w-md mx-auto">
                                <div class="flex items-start space-x-2">
                                    <i class="fas fa-info-circle text-blue-600 mt-0.5 text-sm"></i>
                                    <div class="text-left">
                                        <p class="text-xs font-medium text-blue-900 mb-1">Warranty Benefits:</p>
                                        <ul class="text-xs text-blue-700 space-y-0.5">
                                            <li>• Manufacturer support coverage</li>
                                            <li>• Repair and replacement protection</li>
                                            <li>• Maintenance scheduling alerts</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endempty
                @endif
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="space-y-6">
            <!-- Status Information Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-chart-pie text-purple-600"></i>
                        Status & Condition
                    </h2>
                </div>
                <div class="p-6 space-y-5">
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 mb-2">Current Condition</dt>
                            <dd>
                                <span class="px-3 py-2 inline-flex text-sm font-semibold rounded-full border 
                                    {{ $asset->condition === 'Good' ? 'bg-green-100 text-green-800 border-green-200' : 
                                       ($asset->condition === 'Fair' ? 'bg-yellow-100 text-yellow-800 border-yellow-200' : 'bg-red-100 text-red-800 border-red-200') }}">
                                    <i class="fas fa-tools mr-2"></i>{{ $asset->condition }}
                                </span>
                            </dd>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 mb-2">Asset Status</dt>
                            <dd>
                                <span class="px-3 py-2 inline-flex text-sm font-semibold rounded-full border 
                                    {{ $asset->status === 'Available' ? 'bg-green-100 text-green-800 border-green-200' : 
                                       ($asset->status === 'In Use' ? 'bg-blue-100 text-blue-800 border-blue-200' : 
                                       ($asset->status === 'Lost' ? 'bg-yellow-100 text-yellow-800 border-yellow-200' : 'bg-red-100 text-red-800 border-red-200')) }}">
                                    <i class="fas fa-info-circle mr-2"></i>{{ $asset->status }}
                                </span>
                            </dd>
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
                    <div class="flex items-start justify-between py-3 border-b border-gray-100">
                        <div class="flex-1">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Purchase Cost</dt>
                            <dd class="text-2xl font-bold text-green-600 flex items-center gap-2">
                                <i class="fas fa-peso-sign text-lg"></i>
                                {{ number_format($asset->purchase_cost, 2) }}
                            </dd>
                        </div>
                    </div>
                    
                    <div class="flex items-start justify-between py-3 border-b border-gray-100">
                        <div class="flex-1">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Purchase Date</dt>
                            <dd class="text-base font-medium text-gray-900 flex items-center gap-2">
                                <i class="fas fa-calendar text-gray-400"></i>
                                {{ \Carbon\Carbon::parse($asset->purchase_date)->format('F d, Y') }}
                            </dd>
                        </div>
                    </div>
                    
                    <div class="flex items-start justify-between py-3">
                        <div class="flex-1">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Asset Age</dt>
                            <dd class="text-base text-gray-700 flex items-center gap-2">
                                <i class="fas fa-clock text-gray-400"></i>
                                {{ \Carbon\Carbon::parse($asset->purchase_date)->diffForHumans() }}
                            </dd>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Disposal Information Card (only shown if disposed) -->
            @if($asset->status === 'Disposed' && $asset->disposes->isNotEmpty())
            @php
                $latestDisposal = $asset->disposes->sortByDesc('disposal_date')->first();
            @endphp
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-red-50 to-red-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-ban text-red-600"></i>
                        Disposal Information
                    </h2>
                </div>
                <div class="p-6 space-y-5">
                    <div class="flex items-start justify-between py-3 border-b border-gray-100">
                        <div class="flex-1">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Disposal Date</dt>
                            <dd class="text-base font-medium text-gray-900 flex items-center gap-2">
                                <i class="fas fa-calendar text-gray-400"></i>
                                {{ \Carbon\Carbon::parse($latestDisposal->disposal_date)->format('F d, Y') }}
                            </dd>
                            <div class="text-xs text-gray-600 mt-1">
                                {{ \Carbon\Carbon::parse($latestDisposal->disposal_date)->diffForHumans() }}
                            </div>
                        </div>
                        <div class="ml-4">
                            <i class="fas fa-calendar-times text-gray-400"></i>
                        </div>
                    </div>
                    
                    <div class="flex items-start justify-between py-3 border-b border-gray-100">
                        <div class="flex-1">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Disposed By</dt>
                            <dd class="text-base font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-user text-gray-400"></i>
                                {{ $latestDisposal->disposed_by }}
                            </dd>
                        </div>
                        <div class="ml-4">
                            <i class="fas fa-user-check text-gray-400"></i>
                        </div>
                    </div>
                    
                    <div class="flex items-start justify-between py-3">
                        <div class="flex-1">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Disposal Reason</dt>
                            <dd class="text-base text-gray-700 leading-relaxed bg-red-50 p-3 rounded-lg border border-red-100">
                                <i class="fas fa-exclamation-circle text-red-500 mr-2 float-left mt-1"></i>
                                {{ $latestDisposal->disposal_reason }}
                            </dd>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>


    <!-- QR Code Section -->
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-qrcode text-gray-600"></i>
                Asset QR Code
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- QR Code Display -->
                <div class="flex flex-col items-center">
                    <div class="bg-white p-4 rounded-lg border-2 border-gray-200 shadow-sm">
                        <img src="{{ route('qrcode.asset', $asset->asset_code) }}" 
                             alt="QR Code for {{ $asset->asset_code }}" 
                             class="w-48 h-48">
                    </div>
                    <div class="mt-4 text-center">
                        <div class="text-sm text-gray-600 mb-2">Scan to view asset details</div>
                        <div class="font-mono text-lg font-bold text-gray-900 bg-gray-100 px-3 py-1 rounded">
                            {{ $asset->asset_code }}
                        </div>
                    </div>
                </div>
                
                <!-- QR Code Actions -->
                <div class="flex flex-col justify-center space-y-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-600"></i>
                            QR Code Information
                        </h3>
                        <ul class="text-sm text-gray-700 space-y-1">
                            <li>• Contains asset code: <span class="font-mono">{{ $asset->asset_code }}</span></li>
                            <li>• Scan with any QR code reader</li>
                            <li>• Use for quick asset identification</li>
                            <li>• Print and attach to physical asset</li>
                        </ul>
                    </div>
                    
                    <div class="space-y-3">
                        <a href="{{ route('qrcode.asset.download', $asset->asset_code) }}" 
                           class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                            <i class="fas fa-download"></i>
                            Download PNG
                        </a>
                        
                        <button onclick="printQRCode()" 
                                class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                            <i class="fas fa-print"></i>
                            Print QR Code
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Asset History Section -->
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 px-4 md:px-6 py-3 md:py-4 border-b border-gray-200">
            <h2 class="text-base md:text-lg font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-history text-indigo-600"></i>
                Asset History
            </h2>
        </div>
        
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200 overflow-x-auto">
            <nav class="flex px-4 md:px-6" aria-label="Tabs">

                
                <button onclick="showTab('maintenance')" 
                        class="tab-button flex-shrink-0 {{ $activeTab === 'maintenance' ? 'active border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500' }} py-3 md:py-4 px-2 md:px-3 border-b-2 font-medium text-xs md:text-sm hover:text-gray-700 flex items-center gap-1 md:gap-2 whitespace-nowrap">
                    <i class="fas fa-tools text-xs md:text-sm"></i>
                    <span class="hidden sm:inline">Maintenance Records</span><span class="sm:hidden">Maintenance</span>
                    @if($maintenances->total() > 0)
                        <span class="bg-gray-100 text-gray-600 py-0.5 px-2.5 rounded-full text-xs font-medium">
                            {{ $maintenances->total() }}
                        </span>
                    @endif
                </button>
                
                <button onclick="showTab('disposal')" 
                        class="tab-button flex-shrink-0 {{ $activeTab === 'disposal' ? 'active border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500' }} py-3 md:py-4 px-2 md:px-3 border-b-2 font-medium text-xs md:text-sm hover:text-gray-700 flex items-center gap-1 md:gap-2 whitespace-nowrap">
                    <i class="fas fa-ban text-xs md:text-sm"></i>
                    <span class="hidden sm:inline">Disposal History</span><span class="sm:hidden">Disposal</span>
                    @if($disposes->total() > 0)
                        <span class="bg-gray-100 text-gray-600 py-0.5 px-2.5 rounded-full text-xs font-medium">
                            {{ $disposes->total() }}
                        </span>
                    @endif
                </button>
                
                <button onclick="showTab('changes')" 
                        class="tab-button flex-shrink-0 {{ $activeTab === 'changes' ? 'active border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500' }} py-3 md:py-4 px-2 md:px-3 border-b-2 font-medium text-xs md:text-sm hover:text-gray-700 flex items-center gap-1 md:gap-2 whitespace-nowrap">
                    <i class="fas fa-edit text-xs md:text-sm"></i>
                    <span class="hidden sm:inline">Asset Changes</span><span class="sm:hidden">Changes</span>
                    @if($changes->total() > 0)
                        <span class="bg-gray-100 text-gray-600 py-0.5 px-2.5 rounded-full text-xs font-medium">
                            {{ $changes->total() }}
                        </span>
                    @endif
                </button>
            </nav>
        </div>
        
        <!-- Tab Content -->
        <div class="p-6">

            
            <!-- Maintenance Records Tab -->
            <div id="maintenance-tab" class="tab-content" style="{{ $activeTab === 'maintenance' ? 'display: block;' : 'display: none;' }}">
                @if($maintenances->total() > 0)
                    <!-- Mobile Card View -->
                    <div class="block md:hidden space-y-3">
                        @foreach($maintenances as $maintenance)
                            <div class="bg-white rounded-xl p-4 border-2 border-gray-200 shadow-sm">
                                <div class="flex items-center justify-between mb-3 pb-3 border-b-2 border-gray-100">
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $maintenance->status_class }}">
                                        {{ $maintenance->end_status }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $maintenance->scanned_at?->format('M d, Y') }}
                                    </span>
                                </div>
                                
                                <div class="space-y-2 text-xs">
                                    <div class="flex justify-between py-2 border-b border-gray-100">
                                        <span class="text-gray-500">Checklist:</span>
                                        <span class="font-medium text-gray-900">#{{ $maintenance->maintenance_checklist_id }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-gray-100">
                                        <span class="text-gray-500">Scanned by:</span>
                                        <span class="font-medium text-gray-900">{{ $maintenance->scanned_by }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-gray-100">
                                        <span class="text-gray-500">From Status:</span>
                                        <span class="font-medium text-gray-900">{{ $maintenance->start_status }}</span>
                                    </div>
                                    <div class="flex justify-between py-2">
                                        <span class="text-gray-500">To Status:</span>
                                        <span class="font-medium text-gray-900">{{ $maintenance->end_status }}</span>
                                    </div>
                                    @if($maintenance->location_name)
                                    <div class="pt-2 border-t border-gray-100">
                                        <div class="text-gray-500 mb-1">Location:</div>
                                        <div class="bg-green-50 px-2 py-1 rounded text-green-700 text-xs">
                                            <i class="fas fa-map-marker-alt mr-1"></i>{{ $maintenance->location_name }}
                                        </div>
                                    </div>
                                    @endif
                                    @if(!empty($maintenance->notes))
                                    <div class="pt-2 border-t border-gray-100">
                                        <div class="text-gray-500 mb-1">Notes:</div>
                                        <div class="bg-blue-50 p-2 rounded border border-blue-100 text-gray-700">
                                            {{ $maintenance->notes }}
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Desktop Table View -->
                    <div class="hidden md:block space-y-4">
                        @foreach($maintenances as $maintenance)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full {{ $maintenance->status_class }}">
                                                {{ $maintenance->end_status }}
                                            </span>
                                            <span class="text-sm text-gray-500">
                                                {{ $maintenance->scanned_at?->format('M d, Y g:i A') }}
                                            </span>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 md:gap-4 text-xs md:text-sm">
                                            <div>
                                                <span class="font-medium text-gray-700">Checklist:</span>
                                                <span class="text-gray-900">#{{ $maintenance->maintenance_checklist_id }}</span>
                                            </div>
                                            
                                            <div>
                                                <span class="font-medium text-gray-700">Scanned by:</span>
                                                <span class="text-gray-900">{{ $maintenance->scanned_by }}</span>
                                            </div>
                                            
                                            <div>
                                                <span class="font-medium text-gray-700">From Status:</span>
                                                <span class="text-gray-900">{{ $maintenance->start_status }}</span>
                                            </div>
                                            
                                            <div>
                                                <span class="font-medium text-gray-700">To Status:</span>
                                                <span class="text-gray-900">{{ $maintenance->end_status }}</span>
                                            </div>
                                            
                                            @if($maintenance->location_name)
                                            <div class="md:col-span-2">
                                                <span class="font-medium text-gray-700">Maintenance Location:</span>
                                                <span class="text-gray-900 bg-green-50 px-2 py-1 rounded text-sm">
                                                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $maintenance->location_name }}
                                                </span>
                                            </div>
                                            @endif
                                            
                                            @if(!empty($maintenance->notes))
                                            <div class="md:col-span-2">
                                                <span class="font-medium text-gray-700">Notes:</span>
                                                <div class="text-gray-900 mt-1 bg-blue-50 p-3 rounded-lg border border-blue-100">
                                                    {{ $maintenance->notes }}
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination for Maintenance Records -->
                    @if($maintenances->hasPages())
                        <div class="mt-6">
                            {{ $maintenances->appends(['tab' => 'maintenance'])->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-tools text-4xl text-gray-300 mb-4"></i>
                        <div class="text-lg font-medium text-gray-600">No maintenance checklist history</div>
                        <div class="text-sm text-gray-500 mt-1">This asset has no scanning history yet</div>
                    </div>
                @endif
            </div>
            
            <!-- Disposal History Tab -->
            <div id="disposal-tab" class="tab-content" style="{{ $activeTab === 'disposal' ? 'display: block;' : 'display: none;' }}">
                @if($disposes->total() > 0)
                    <!-- Mobile Card View -->
                    <div class="block md:hidden space-y-3">
                        @foreach($disposes as $disposal)
                            <div class="bg-white rounded-xl p-4 border-2 border-gray-200 shadow-sm">
                                <div class="flex items-center justify-between mb-3 pb-3 border-b-2 border-gray-100">
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-red-100 text-red-800">
                                        Disposed
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $disposal->disposal_date->format('M d, Y') }}
                                    </span>
                                </div>
                                
                                <div class="space-y-2 text-xs">
                                    <div class="flex justify-between py-2 border-b border-gray-100">
                                        <span class="text-gray-500">Disposed by:</span>
                                        <span class="font-medium text-gray-900">{{ $disposal->disposed_by }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-gray-100">
                                        <span class="text-gray-500">Method:</span>
                                        <span class="font-medium text-gray-900">{{ $disposal->disposal_method }}</span>
                                    </div>
                                    <div class="pt-2 border-t border-gray-100">
                                        <div class="text-gray-500 mb-1">Reason:</div>
                                        <div class="bg-red-50 p-2 rounded border border-red-100 text-gray-700">
                                            {{ $disposal->disposal_reason }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Desktop Table View -->
                    <div class="hidden md:block space-y-4">
                        @foreach($disposes as $disposal)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                Disposed
                                            </span>
                                            <span class="text-sm text-gray-500">
                                                {{ $disposal->disposal_date->format('M d, Y') }}
                                            </span>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <span class="font-medium text-gray-700">Disposed by:</span>
                                                <span class="text-gray-900">{{ $disposal->disposed_by }}</span>
                                            </div>
                                            
                                            <div>
                                                <span class="font-medium text-gray-700">Disposal method:</span>
                                                <span class="text-gray-900">{{ $disposal->disposal_method }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <span class="font-medium text-gray-700">Reason:</span>
                                            <div class="text-gray-900 mt-1 bg-red-50 p-3 rounded-lg border border-red-100">
                                                {{ $disposal->disposal_reason }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination for Disposal History -->
                    @if($disposes->hasPages())
                        <div class="mt-6">
                            {{ $disposes->appends(['tab' => 'disposal'])->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-ban text-4xl text-gray-300 mb-4"></i>
                        <div class="text-lg font-medium text-gray-600">No disposal history</div>
                        <div class="text-sm text-gray-500 mt-1">This asset has not been disposed</div>
                    </div>
                @endif
            </div>

            <!-- Asset Changes Tab -->
            <div id="changes-tab" class="tab-content" style="{{ $activeTab === 'changes' ? 'display: block;' : 'display: none;' }}">
                @if($changes->total() > 0)
                    <!-- Mobile Card View -->
                    <div class="block md:hidden space-y-3">
                        @foreach($changes as $change)
                            <div class="bg-white rounded-xl p-4 border-2 border-gray-200 shadow-sm">
                                <div class="flex items-center justify-between mb-3 pb-3 border-b-2 border-gray-100">
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $change->change_type === 'transfer' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ $change->getChangeTypeLabel() }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $change->created_at->format('M d, Y') }}
                                    </span>
                                </div>
                                
                                @if($change->change_type === 'transfer')
                                    <div class="bg-blue-50 rounded-lg p-3 border border-blue-200 text-xs space-y-2">
                                        <div>
                                            <div class="text-blue-700 font-medium mb-1">From:</div>
                                            <div class="bg-white px-2 py-1 rounded border">{{ $change->getEnhancedPreviousValue() }}</div>
                                        </div>
                                        <div>
                                            <div class="text-blue-700 font-medium mb-1">To:</div>
                                            <div class="bg-white px-2 py-1 rounded border font-medium">{{ $change->getEnhancedNewValue() }}</div>
                                        </div>
                                        <div class="pt-2 border-t border-blue-200">
                                            <span class="text-blue-700 font-medium">By:</span> {{ $change->changed_by }}
                                        </div>
                                    </div>
                                @else
                                    <div class="space-y-2 text-xs">
                                        <div class="flex justify-between py-2 border-b border-gray-100">
                                            <span class="text-gray-500">Field:</span>
                                            <span class="font-medium text-gray-900">{{ $change->getFieldLabel() }}</span>
                                        </div>
                                        <div class="flex justify-between py-2 border-b border-gray-100">
                                            <span class="text-gray-500">Changed by:</span>
                                            <span class="font-medium text-gray-900">{{ $change->changed_by }}</span>
                                        </div>
                                        <div class="py-2 border-b border-gray-100">
                                            <div class="text-gray-500 mb-1">Previous:</div>
                                            <div class="font-medium text-gray-900">{{ $change->getEnhancedPreviousValue() }}</div>
                                        </div>
                                        <div class="py-2">
                                            <div class="text-gray-500 mb-1">New:</div>
                                            <div class="font-medium text-blue-600">{{ $change->getEnhancedNewValue() }}</div>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($change->notes)
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <div class="text-gray-500 mb-1 text-xs">Notes:</div>
                                    <div class="bg-{{ $change->change_type === 'transfer' ? 'blue' : 'purple' }}-50 p-2 rounded border border-{{ $change->change_type === 'transfer' ? 'blue' : 'purple' }}-100 text-gray-700 text-xs">
                                        {{ $change->notes }}
                                    </div>
                                </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Desktop Table View -->
                    <div class="hidden md:block space-y-4">
                        @foreach($changes as $change)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                                                                 <div class="flex items-center gap-3 mb-2">
                                             <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full 
                                                 {{ $change->change_type === 'transfer' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                 {{ $change->getChangeTypeLabel() }}
                                             </span>
                                             <span class="text-sm text-gray-500">
                                                 {{ $change->created_at->format('M d, Y H:i') }}
                                             </span>
                                         </div>
                                         
                                         @if($change->change_type === 'transfer')
                                             <!-- Special layout for transfers -->
                                             <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                                                 <div class="flex items-center gap-2 mb-2">
                                                     <i class="fas fa-exchange-alt text-blue-600"></i>
                                                     <span class="font-medium text-blue-900">Asset Transfer</span>
                                                 </div>
                                                 <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                                     <div>
                                                         <span class="font-medium text-blue-700">From:</span>
                                                         <div class="text-gray-900 bg-white px-2 py-1 rounded border">{{ $change->getEnhancedPreviousValue() }}</div>
                                                     </div>
                                                     <div>
                                                         <span class="font-medium text-blue-700">To:</span>
                                                         <div class="text-gray-900 bg-white px-2 py-1 rounded border font-medium">{{ $change->getEnhancedNewValue() }}</div>
                                                     </div>
                                                     <div class="md:col-span-2">
                                                         <span class="font-medium text-blue-700">Transferred by:</span>
                                                         <span class="text-gray-900">{{ $change->changed_by }}</span>
                                                     </div>
                                                 </div>
                                             </div>
                                         @else
                                             <!-- Regular layout for other changes -->
                                             <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm {{ $change->involvesUnverifiedStatus() ? 'bg-orange-50 p-3 rounded-lg border border-orange-200' : '' }}">
                                                 @if($change->involvesUnverifiedStatus())
                                                     <div class="md:col-span-2 mb-2">
                                                         <div class="flex items-center gap-2">
                                                             <i class="fas fa-exclamation-triangle text-orange-600"></i>
                                                             <span class="font-medium text-orange-800 text-sm">Status change involving verification process</span>
                                                         </div>
                                                     </div>
                                                 @endif
                                                 <div>
                                                     <span class="font-medium text-gray-700">Field:</span>
                                                     <span class="text-gray-900">{{ $change->getFieldLabel() }}</span>
                                                 </div>
                                                 
                                                 <div>
                                                     <span class="font-medium text-gray-700">Changed by:</span>
                                                     <span class="text-gray-900">{{ $change->changed_by }}</span>
                                                 </div>
                                                 
                                                 <div class="md:col-span-2">
                                                     <span class="font-medium text-gray-700">Previous Value:</span>
                                                     <span class="text-gray-900">{{ $change->getEnhancedPreviousValue() }}</span>
                                                 </div>
                                                 
                                                 <div class="md:col-span-2">
                                                     <span class="font-medium text-gray-700">New Value:</span>
                                                     <span class="text-gray-900 font-medium">{{ $change->getEnhancedNewValue() }}</span>
                                                 </div>
                                             </div>
                                         @endif
                                         
                                         @if($change->notes)
                                             <div class="mt-3">
                                                 <span class="font-medium text-gray-700">Notes:</span>
                                                 <div class="text-gray-900 mt-1 bg-{{ $change->change_type === 'transfer' ? 'blue' : 'purple' }}-50 p-3 rounded-lg border border-{{ $change->change_type === 'transfer' ? 'blue' : 'purple' }}-100">
                                                     {{ $change->notes }}
                                                 </div>
                                             </div>
                                         @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination for Asset Changes -->
                    @if($changes->hasPages())
                        <div class="mt-6">
                            {{ $changes->appends(['tab' => 'changes'])->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-edit text-4xl text-gray-300 mb-4"></i>
                        <div class="text-lg font-medium text-gray-600">No asset changes</div>
                        <div class="text-sm text-gray-500 mt-1">This asset has no history of changes</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

<!-- Dispose Modal -->
<div id="disposeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden p-4">
    <div id="disposeModalCard" class="bg-white rounded-xl shadow-xl p-6 md:p-8 w-full max-w-md relative animate-fade-in">
        <button onclick="closeDisposeModal()" class="absolute top-3 right-3 text-gray-400 hover:text-red-800 text-xl"><i class="fas fa-times"></i></button>
        <div class="flex flex-col items-center">
            <div class="bg-red-100 text-red-800 rounded-full p-4 mb-4">
                <i class="fas fa-exclamation-triangle text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold mb-2 text-gray-800">Dispose Asset</h3>
            <p class="text-gray-600 mb-6 text-center">Are you sure you want to dispose asset <span class="font-semibold text-red-800" id="dispose-asset-name">CODE</span>? This action cannot be undone.</p>
            <form id="disposeForm" method="POST" class="w-full flex flex-col gap-3">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Disposal Reason <span class="text-red-600">*</span></label>
                    <textarea name="disposal_reason" id="disposal_reason" rows="4" required
                              placeholder="Please provide a reason for disposing this asset..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
                </div>
                <button type="submit" class="w-full bg-red-800 hover:bg-red-900 text-white font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-trash-alt"></i> Dispose
                </button>
                <button type="button" onclick="closeDisposeModal()" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// Dispose Modal Functions (mirrors index page behavior)
let currentAssetId = null;

function openDisposeModal(assetId, assetCode) {
    currentAssetId = assetId;
    document.getElementById('dispose-asset-name').textContent = assetCode;
    // Set form action
    document.getElementById('disposeForm').action = `{{ url('assets') }}/${assetId}/dispose`;
    // Show modal
    document.getElementById('disposeModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    // Focus on textarea
    setTimeout(() => {
        document.getElementById('disposal_reason').focus();
    }, 100);
}

function closeDisposeModal() {
    document.getElementById('disposeModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    // Reset form
    document.getElementById('disposeForm').reset();
    document.getElementById('disposeForm').action = '';
    currentAssetId = null;
}

// Close modal when clicking outside
document.getElementById('disposeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDisposeModal();
    }
});
function printQRCode() {
    // Create a new window for printing
    const printWindow = window.open('', '_blank');
    
    // Create HTML content for printing
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Asset QR Code - {{ $asset->asset_code }}</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    text-align: center;
                    margin: 20px;
                }
                .qr-container {
                    border: 2px solid #ccc;
                    padding: 20px;
                    margin: 20px auto;
                    max-width: 300px;
                }
                .asset-code {
                    font-family: monospace;
                    font-size: 18px;
                    font-weight: bold;
                    margin-top: 10px;
                }
                @media print {
                    body { margin: 0; }
                }
            </style>
        </head>
        <body>
            <h2>Asset QR Code</h2>
            <div class="qr-container">
                <img src="{{ route('qrcode.asset', $asset->asset_code) }}" 
                     alt="QR Code" 
                     style="width: 200px; height: 200px;">
                <div class="asset-code">{{ $asset->asset_code }}</div>
            </div>
            <p><strong>Asset:</strong> {{ $asset->name }}</p>
            <p><strong>Category:</strong> {{ $asset->category->name }}</p>
            <p><strong>Location:</strong> 
                @if($asset->location)
                    {{ $asset->location->building }} - Floor {{ $asset->location->floor }} - Room {{ $asset->location->room }}
                @else
                    Not deployed yet
                @endif
            </p>
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
    
    // Wait for the image to load, then print
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 1000);
}

// Tab functionality
function showTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.style.display = 'none';
    });
    
    // Remove active class from all tab buttons
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.classList.remove('active', 'border-indigo-500', 'text-indigo-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById(tabName + '-tab').style.display = 'block';
    
    // Add active class to clicked button
    event.target.classList.add('active', 'border-indigo-500', 'text-indigo-600');
    event.target.classList.remove('border-transparent', 'text-gray-500');
    
    // Update URL with tab parameter for pagination
    const url = new URL(window.location);
    url.searchParams.set('tab', tabName);
    window.history.pushState({}, '', url);
}

// Initialize tab based on URL parameter for pagination
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab') || 'maintenance';
    
    // Update URL if no tab parameter is present
    if (!urlParams.get('tab')) {
        const url = new URL(window.location);
        url.searchParams.set('tab', activeTab);
        window.history.pushState({}, '', url);
    }
    
    // Ensure the correct tab is shown on page load
    showTabOnLoad(activeTab);
});

// Function to show tab on page load without event
function showTabOnLoad(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.style.display = 'none';
    });
    
    // Remove active class from all tab buttons
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.classList.remove('active', 'border-indigo-500', 'text-indigo-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    const targetTab = document.getElementById(tabName + '-tab');
    if (targetTab) {
        targetTab.style.display = 'block';
    }
    
    // Add active class to the correct button
    const targetButton = document.querySelector(`button[onclick="showTab('${tabName}')"]`);
    if (targetButton) {
        targetButton.classList.add('active', 'border-indigo-500', 'text-indigo-600');
        targetButton.classList.remove('border-transparent', 'text-gray-500');
    }
}
</script>

@endsection
