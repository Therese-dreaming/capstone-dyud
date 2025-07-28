@extends('layouts.superadmin')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('assets.index') }}" 
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
                        <div class="bg-green-50 rounded-lg p-4">
                            <a href="{{ route('locations.show', $asset->location) }}" 
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
                    </div>
                </div>
            </div>
            
            <!-- Warranty Information Card (only in left column if asset is disposed) -->
            @if($asset->warranty && $asset->status === 'Disposed' && $asset->disposes->isNotEmpty())
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-orange-50 to-orange-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-shield-alt text-orange-600"></i>
                        Warranty Information
                    </h2>
                </div>
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
                                {{ \Carbon\Carbon::parse($asset->warranty->warranty_expiry)->format('F d, Y') }}
                            </dd>
                            @php
                                $expiryDate = \Carbon\Carbon::parse($asset->warranty->warranty_expiry);
                                $isExpired = $expiryDate->isPast();
                                $isExpiringSoon = !$isExpired && $expiryDate->diffInDays() <= 30;
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
                                <span class="text-xs text-gray-500 ml-2">{{ $expiryDate->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
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

    <!-- Warranty Information Section (full width when not disposed) -->
    @if($asset->warranty && !($asset->status === 'Disposed' && $asset->disposes->isNotEmpty()))
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-orange-50 to-orange-100 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-shield-alt text-orange-600"></i>
                Warranty Information
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Manufacturer -->
                <div class="bg-orange-50 rounded-lg p-4 border border-orange-100">
                    <div class="flex items-center justify-between mb-2">
                        <dt class="text-sm font-medium text-orange-700">Manufacturer</dt>
                        <i class="fas fa-industry text-orange-500"></i>
                    </div>
                    <dd class="text-base font-semibold text-gray-900">
                        {{ $asset->warranty->manufacturer }}
                    </dd>
                </div>
                
                <!-- Model -->
                <div class="bg-orange-50 rounded-lg p-4 border border-orange-100">
                    <div class="flex items-center justify-between mb-2">
                        <dt class="text-sm font-medium text-orange-700">Model</dt>
                        <i class="fas fa-cog text-orange-500"></i>
                    </div>
                    <dd class="text-base font-semibold text-gray-900">
                        {{ $asset->warranty->model }}
                    </dd>
                </div>
                
                <!-- Warranty Status -->
                <div class="bg-orange-50 rounded-lg p-4 border border-orange-100">
                    <div class="flex items-center justify-between mb-2">
                        <dt class="text-sm font-medium text-orange-700">Warranty Status</dt>
                        <i class="fas fa-shield-alt text-orange-500"></i>
                    </div>
                    @php
                        $expiryDate = \Carbon\Carbon::parse($asset->warranty->warranty_expiry);
                        $isExpired = $expiryDate->isPast();
                        $isExpiringSoon = !$isExpired && $expiryDate->diffInDays() <= 30;
                    @endphp
                    <dd class="text-base font-semibold text-gray-900 mb-2">
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
                    </dd>
                    <div class="text-xs text-gray-600">
                        {{ $expiryDate->diffForHumans() }}
                    </div>
                </div>
            </div>
            
            <!-- Warranty Expiry (full width) -->
            <div class="mt-6">
                <dt class="text-sm font-medium text-gray-700 mb-3 flex items-center gap-2">
                    <i class="fas fa-calendar-times text-orange-500"></i>
                    Warranty Expiry Date
                </dt>
                <dd class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                    <div class="text-lg font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($asset->warranty->warranty_expiry)->format('F d, Y') }}
                    </div>
                </dd>
            </div>
        </div>
    </div>
    @endif

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

    <!-- Action Buttons -->
    <div class="mt-6 flex gap-4">
        <a href="{{ route('assets.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition duration-200 flex items-center gap-2">
            <i class="fas fa-list"></i> Back to Assets
        </a>
        <!-- Future: Edit and Delete buttons can be added here -->
    </div>
</div>

<script>
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
            <p><strong>Location:</strong> {{ $asset->location->building }} - Floor {{ $asset->location->floor }} - Room {{ $asset->location->room }}</p>
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
</script>

@endsection
