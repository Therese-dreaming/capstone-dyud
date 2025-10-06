@extends('layouts.purchasing')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Hero Header -->
    <div class="mb-8">
        <div class="bg-purple-600 rounded-2xl p-8 text-white border-4 border-purple-800 shadow-lg">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-start space-x-6 mb-6">
                        <!-- Asset Icon -->
                        <div class="w-20 h-20 bg-purple-700 rounded-2xl flex items-center justify-center border-2 border-purple-800">
                            @php
                                $categoryIcons = [
                                    'Computer' => 'fas fa-desktop',
                                    'Laptop' => 'fas fa-laptop',
                                    'Printer' => 'fas fa-print',
                                    'Monitor' => 'fas fa-tv',
                                    'Phone' => 'fas fa-phone',
                                    'Tablet' => 'fas fa-tablet-alt',
                                    'Camera' => 'fas fa-camera',
                                    'Projector' => 'fas fa-video',
                                    'Scanner' => 'fas fa-scanner',
                                    'Router' => 'fas fa-wifi',
                                    'Server' => 'fas fa-server',
                                    'Storage' => 'fas fa-hdd',
                                ];
                                $categoryName = $asset->category->name ?? 'Unknown';
                                $iconClass = $categoryIcons[$categoryName] ?? 'fas fa-box';
                            @endphp
                            <i class="{{ $iconClass }} text-3xl"></i>
                        </div>
                        
                        <!-- Asset Info -->
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <h1 class="text-4xl font-bold">{{ $asset->name }}</h1>
                                <div class="w-2 h-2 bg-white/60 rounded-full"></div>
                                <span class="text-xl text-purple-100 font-medium">{{ $asset->asset_code }}</span>
                            </div>
                            <p class="text-purple-100 text-lg mb-4">{{ $asset->category->name ?? 'Uncategorized' }} Asset</p>
                            
                            <!-- Status Badges -->
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold {{ $asset->getApprovalStatusBadgeClass() }} shadow-lg">
                                    <i class="fas fa-{{ $asset->isPending() ? 'hourglass-half' : ($asset->isApproved() ? 'check-circle' : 'times-circle') }} mr-2"></i>
                                    {{ $asset->getApprovalStatusLabel() }}
                                </span>
                                
                                @if($asset->warranty)
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold {{ $asset->warranty->getStatusBadgeClass() }} shadow-lg">
                                        <i class="fas fa-shield-alt mr-2"></i>
                                        {{ $asset->warranty->getStatusLabel() }} Warranty
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-gray-100 text-gray-800 shadow-lg">
                                        <i class="fas fa-shield-alt mr-2"></i>
                                        No Warranty Data
                                    </span>
                                @endif
                                
                                @if($asset->location)
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800 shadow-lg">
                                        <i class="fas fa-map-marker-alt mr-2"></i>
                                        Deployed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 shadow-lg">
                                        <i class="fas fa-clock mr-2"></i>
                                        Awaiting Deployment
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="grid grid-cols-3 gap-6">
                        <div class="bg-purple-700 rounded-xl p-4 border-2 border-purple-800">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-calendar-alt text-2xl text-purple-200"></i>
                                <div>
                                    <p class="text-purple-200 text-sm">Purchase Date</p>
                                    <p class="text-white font-semibold">{{ $asset->purchase_date->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-purple-700 rounded-xl p-4 border-2 border-purple-800">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-peso-sign text-2xl text-purple-200"></i>
                                <div>
                                    <p class="text-purple-200 text-sm">Purchase Cost</p>
                                    <p class="text-white font-semibold">₱{{ number_format($asset->purchase_cost, 2) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-purple-700 rounded-xl p-4 border-2 border-purple-800">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-star text-2xl text-purple-200"></i>
                                <div>
                                    <p class="text-purple-200 text-sm">Condition</p>
                                    <p class="text-white font-semibold">{{ $asset->condition }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-col space-y-3 ml-8">
                    @if($asset->isPending())
                        <a href="{{ route('purchasing.assets.edit', $asset) }}" 
                           class="inline-flex items-center px-6 py-3 bg-purple-700 hover:bg-purple-800 rounded-xl transition-all duration-200 border-2 border-purple-800 group">
                            <i class="fas fa-edit mr-3 group-hover:scale-110 transition-transform"></i>
                            <span class="font-medium">Edit Asset</span>
                        </a>
                    @endif
                    <a href="{{ route('purchasing.assets.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-purple-700 hover:bg-purple-800 rounded-xl transition-all duration-200 border-2 border-purple-800 group">
                        <i class="fas fa-arrow-left mr-3 group-hover:-translate-x-1 transition-transform"></i>
                        <span class="font-medium">Back to Assets</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
        <!-- Asset Details -->
        <div class="lg:col-span-3 space-y-6">
            
            <!-- 📋 Asset Information -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-info-circle text-blue-600 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Asset Information</h3>
                                <p class="text-sm text-gray-600">Basic asset details and specifications</p>
                            </div>
                        </div>
                        <div class="w-12 h-12 bg-blue-100/50 rounded-xl flex items-center justify-center">
                            <i class="{{ $iconClass }} text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-barcode text-gray-600"></i>
                                </div>
                                <div class="flex-1">
                                    <label class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Asset Code</label>
                                    <p class="text-xl font-bold text-gray-900 mt-1">{{ $asset->asset_code }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-tag text-gray-600"></i>
                                </div>
                                <div class="flex-1">
                                    <label class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Asset Name</label>
                                    <p class="text-lg font-medium text-gray-900 mt-1">{{ $asset->name }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-folder text-gray-600"></i>
                                </div>
                                <div class="flex-1">
                                    <label class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Category</label>
                                    <div class="mt-2">
                                        <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold bg-purple-100 text-purple-800 border border-purple-200">
                                            <i class="{{ $iconClass }} mr-2"></i>
                                            {{ $asset->category->name ?? 'Uncategorized' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column -->
                        <div class="space-y-6">
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-star text-gray-600"></i>
                                </div>
                                <div class="flex-1">
                                    <label class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Condition</label>
                                    <div class="mt-2">
                                        @php
                                            $conditionConfig = [
                                                'Excellent' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-200', 'icon' => 'fas fa-star'],
                                                'Good' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'border' => 'border-blue-200', 'icon' => 'fas fa-thumbs-up'],
                                                'Fair' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'border' => 'border-yellow-200', 'icon' => 'fas fa-exclamation-triangle'],
                                                'Poor' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-200', 'icon' => 'fas fa-times-circle'],
                                            ];
                                            $config = $conditionConfig[$asset->condition] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'border' => 'border-gray-200', 'icon' => 'fas fa-question'];
                                        @endphp
                                        <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold {{ $config['bg'] }} {{ $config['text'] }} border {{ $config['border'] }}">
                                            <i class="{{ $config['icon'] }} mr-2"></i>
                                            {{ $asset->condition }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-peso-sign text-gray-600"></i>
                                </div>
                                <div class="flex-1">
                                    <label class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Purchase Cost</label>
                                    <p class="text-2xl font-bold text-green-600 mt-1">₱{{ number_format($asset->purchase_cost, 2) }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-calendar-alt text-gray-600"></i>
                                </div>
                                <div class="flex-1">
                                    <label class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Purchase Date</label>
                                    <p class="text-lg font-medium text-gray-900 mt-1">{{ $asset->purchase_date->format('F d, Y') }}</p>
                                    <p class="text-sm text-gray-500">{{ $asset->purchase_date->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 🛡️ Warranty Information -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-shield-alt text-green-600 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Warranty Protection</h3>
                                <p class="text-sm text-gray-600">Manufacturer warranty coverage details</p>
                            </div>
                        </div>
                        @if($asset->warranty)
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold {{ $asset->warranty->getStatusBadgeClass() }} border">
                                    <i class="fas fa-{{ $asset->warranty->isExpired() ? 'times-circle' : ($asset->warranty->isExpiringSoon() ? 'exclamation-triangle' : 'check-circle') }} mr-2"></i>
                                    {{ $asset->warranty->getStatusLabel() }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
                
                @if($asset->warranty)
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <div class="flex items-start space-x-4">
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-industry text-gray-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <label class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Manufacturer</label>
                                        <p class="text-lg font-bold text-gray-900 mt-1">{{ $asset->warranty->manufacturer }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start space-x-4">
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-cog text-gray-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <label class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Model</label>
                                        <p class="text-lg font-medium text-gray-900 mt-1">{{ $asset->warranty->model }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right Column -->
                            <div class="space-y-6">
                                <div class="flex items-start space-x-4">
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-calendar-times text-gray-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <label class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Warranty Expiry</label>
                                        <p class="text-lg font-medium text-gray-900 mt-1">{{ $asset->warranty->warranty_expiry->format('F d, Y') }}</p>
                                        <p class="text-sm text-gray-500">{{ $asset->warranty->warranty_expiry->diffForHumans() }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start space-x-4">
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-hourglass-half text-gray-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <label class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Time Remaining</label>
                                        @if($asset->warranty->getDaysUntilExpiry() < 0)
                                            <p class="text-xl font-bold text-red-600 mt-1">Expired</p>
                                            <p class="text-sm text-red-500">{{ abs($asset->warranty->getDaysUntilExpiry()) }} days ago</p>
                                        @else
                                            <p class="text-xl font-bold text-green-600 mt-1">{{ $asset->warranty->getDaysUntilExpiry() }} days</p>
                                            <p class="text-sm text-green-500">remaining</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Warranty Status Alert -->
                        <div class="mt-8 p-6 rounded-2xl {{ $asset->warranty->getStatusBadgeClass() === 'bg-red-100 text-red-800' ? 'bg-red-50 border-2 border-red-200' : ($asset->warranty->getStatusBadgeClass() === 'bg-yellow-100 text-yellow-800' ? 'bg-yellow-50 border-2 border-yellow-200' : 'bg-green-50 border-2 border-green-200') }}">
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 {{ $asset->warranty->getStatusBadgeClass() === 'bg-red-100 text-red-800' ? 'bg-red-100' : ($asset->warranty->getStatusBadgeClass() === 'bg-yellow-100 text-yellow-800' ? 'bg-yellow-100' : 'bg-green-100') }} rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-{{ $asset->warranty->isExpired() ? 'exclamation-triangle' : ($asset->warranty->isExpiringSoon() ? 'clock' : 'check-circle') }} {{ $asset->warranty->getStatusBadgeClass() === 'bg-red-100 text-red-800' ? 'text-red-600' : ($asset->warranty->getStatusBadgeClass() === 'bg-yellow-100 text-yellow-800' ? 'text-yellow-600' : 'text-green-600') }} text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-lg font-bold {{ $asset->warranty->getStatusBadgeClass() === 'bg-red-100 text-red-800' ? 'text-red-900' : ($asset->warranty->getStatusBadgeClass() === 'bg-yellow-100 text-yellow-800' ? 'text-yellow-900' : 'text-green-900') }} mb-2">
                                        @if($asset->warranty->isExpired())
                                            ⚠️ Warranty Has Expired
                                        @elseif($asset->warranty->isExpiringSoon())
                                            ⏰ Warranty Expiring Soon
                                        @else
                                            ✅ Warranty is Active
                                        @endif
                                    </h4>
                                    <p class="text-sm {{ $asset->warranty->getStatusBadgeClass() === 'bg-red-100 text-red-800' ? 'text-red-700' : ($asset->warranty->getStatusBadgeClass() === 'bg-yellow-100 text-yellow-800' ? 'text-yellow-700' : 'text-green-700') }}">
                                        @if($asset->warranty->isExpired())
                                            The manufacturer warranty has expired. Consider purchasing extended warranty or service plans for continued protection.
                                        @elseif($asset->warranty->isExpiringSoon())
                                            The warranty will expire soon. Plan for warranty renewal or consider extended service options to maintain coverage.
                                        @else
                                            This asset is fully covered under the manufacturer warranty. Any defects or issues should be covered by the manufacturer.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    @empty($asset->warranty)
                        <div class="p-12 text-center">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-shield-alt text-4xl text-gray-400"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 mb-3">No Warranty Information</h4>
                            <p class="text-gray-600 mb-6 max-w-md mx-auto">
                                Warranty details haven't been added for this asset yet. This information is important for tracking coverage and support eligibility.
                            </p>
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 max-w-lg mx-auto">
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                                    <div class="text-left">
                                        <p class="text-sm font-medium text-blue-900 mb-1">Why add warranty information?</p>
                                        <ul class="text-xs text-blue-700 space-y-1">
                                            <li>• Track warranty expiration dates</li>
                                            <li>• Ensure timely renewals</li>
                                            <li>• Maintain service eligibility</li>
                                            <li>• Plan for replacements</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @if($asset->isPending())
                                <div class="mt-6">
                                    <a href="{{ route('purchasing.assets.edit', $asset) }}" 
                                       class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-medium transition-colors">
                                        <i class="fas fa-plus mr-2"></i>
                                        Add Warranty Information
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endempty
                @endif
            </div>
        </div>

        <!-- 📊 Sidebar -->
        <div class="space-y-6">
            <!-- 📈 Status & Tracking -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-50 to-indigo-50 px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-chart-line text-purple-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Status & Tracking</h3>
                            <p class="text-sm text-gray-600">Asset lifecycle monitoring</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Approval Status -->
                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-{{ $asset->isPending() ? 'hourglass-half' : ($asset->isApproved() ? 'check-circle' : 'times-circle') }} text-gray-600"></i>
                        </div>
                        <div class="flex-1">
                            <label class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Approval Status</label>
                            <div class="mt-2">
                                <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold {{ $asset->getApprovalStatusBadgeClass() }} border shadow-sm">
                                    <i class="fas fa-{{ $asset->isPending() ? 'clock' : ($asset->isApproved() ? 'check-circle' : 'times-circle') }} mr-2"></i>
                                    {{ $asset->getApprovalStatusLabel() }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Location Status -->
                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-map-marker-alt text-gray-600"></i>
                        </div>
                        <div class="flex-1">
                            <label class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Location Status</label>
                            @if($asset->location)
                                <div class="mt-2">
                                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold bg-green-100 text-green-800 border border-green-200 shadow-sm">
                                        <i class="fas fa-map-marker-alt mr-2"></i>
                                        Deployed
                                    </span>
                                    <div class="mt-2 p-3 bg-green-50 rounded-lg border border-green-200">
                                        @if($asset->location)
                                            <p class="text-sm font-medium text-green-900">{{ $asset->location->building }}</p>
                                            <p class="text-xs text-green-700">Floor {{ $asset->location->floor }} - Room {{ $asset->location->room }}</p>
                                        @else
                                            <p class="text-sm font-medium text-gray-500">Location not assigned</p>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="mt-2">
                                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold bg-yellow-100 text-yellow-800 border border-yellow-200 shadow-sm">
                                        <i class="fas fa-clock mr-2"></i>
                                        Awaiting Deployment
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1">GSU will assign location after approval</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Creation Info -->
                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user-plus text-gray-600"></i>
                        </div>
                        <div class="flex-1">
                            <label class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Created By</label>
                            <p class="text-lg font-medium text-gray-900 mt-1">{{ $asset->createdBy->name ?? 'Unknown' }}</p>
                            <p class="text-sm text-gray-500">{{ $asset->created_at->format('M d, Y \a\t H:i') }}</p>
                            <p class="text-xs text-gray-400">{{ $asset->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    @if($asset->approved_at)
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user-check text-gray-600"></i>
                            </div>
                            <div class="flex-1">
                                <label class="text-sm font-semibold text-gray-500 uppercase tracking-wide">{{ $asset->isApproved() ? 'Approved' : 'Rejected' }} By</label>
                                <p class="text-lg font-medium text-gray-900 mt-1">{{ $asset->approvedBy->name ?? 'Unknown' }}</p>
                                <p class="text-sm text-gray-500">{{ $asset->approved_at->format('M d, Y \a\t H:i') }}</p>
                                <p class="text-xs text-gray-400">{{ $asset->approved_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ⚡ Quick Actions -->
            @if($asset->isPending())
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-50 to-red-50 px-6 py-4 border-b border-gray-100">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-bolt text-orange-600 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Quick Actions</h3>
                                <p class="text-sm text-gray-600">Available while pending</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        <a href="{{ route('purchasing.assets.edit', $asset) }}" 
                           class="flex items-center w-full px-6 py-4 bg-gradient-to-r from-purple-50 to-indigo-50 hover:from-purple-100 hover:to-indigo-100 text-purple-700 rounded-xl transition-all duration-200 border border-purple-200 group">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-edit text-purple-600"></i>
                            </div>
                            <div>
                                <span class="font-bold text-purple-900">Edit Asset Details</span>
                                <p class="text-sm text-purple-600">Modify asset information</p>
                            </div>
                        </a>
                        
                        <button onclick="if(confirm('⚠️ Are you sure you want to delete this asset? This action cannot be undone.')) { document.getElementById('delete-form').submit(); }" 
                                class="flex items-center w-full px-6 py-4 bg-gradient-to-r from-red-50 to-pink-50 hover:from-red-100 hover:to-pink-100 text-red-700 rounded-xl transition-all duration-200 border border-red-200 group">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-trash text-red-600"></i>
                            </div>
                            <div>
                                <span class="font-bold text-red-900">Delete Asset</span>
                                <p class="text-sm text-red-600">Permanently remove asset</p>
                            </div>
                        </button>
                        <form id="delete-form" action="{{ route('purchasing.assets.destroy', $asset) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            @endif

            <!-- 📊 Asset Statistics -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-slate-50 px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-chart-bar text-gray-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Asset Statistics</h3>
                            <p class="text-sm text-gray-600">Key metrics and timeline</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-xl border border-blue-200">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-calendar-plus text-blue-600"></i>
                            <span class="text-sm font-medium text-blue-900">Time Since Creation</span>
                        </div>
                        @php
                            $totalMinutes = $asset->created_at->diffInMinutes(now());
                            $daysSinceCreation = floor($totalMinutes / (24 * 60));
                            $hoursSinceCreation = floor(($totalMinutes % (24 * 60)) / 60);
                            $minutesSinceCreation = $totalMinutes % 60;
                            
                            if ($daysSinceCreation > 0) {
                                $creationTimeText = $daysSinceCreation . ' day' . ($daysSinceCreation > 1 ? 's' : '');
                                if ($hoursSinceCreation > 0) {
                                    $creationTimeText .= ', ' . $hoursSinceCreation . ' hour' . ($hoursSinceCreation > 1 ? 's' : '');
                                }
                            } elseif ($hoursSinceCreation > 0) {
                                $creationTimeText = $hoursSinceCreation . ' hour' . ($hoursSinceCreation > 1 ? 's' : '');
                                if ($minutesSinceCreation > 0) {
                                    $creationTimeText .= ', ' . $minutesSinceCreation . ' min' . ($minutesSinceCreation > 1 ? 's' : '');
                                }
                            } else {
                                $creationTimeText = $minutesSinceCreation . ' minute' . ($minutesSinceCreation > 1 ? 's' : '');
                            }
                        @endphp
                        <span class="text-lg font-bold text-blue-600">{{ $creationTimeText }}</span>
                    </div>
                    
                    @if($asset->approved_at)
                        <div class="flex items-center justify-between p-4 bg-green-50 rounded-xl border border-green-200">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-clock text-green-600"></i>
                                <span class="text-sm font-medium text-green-900">Processing Time</span>
                            </div>
                            @php
                                $totalProcessingSeconds = $asset->created_at->diffInSeconds($asset->approved_at);
                                $totalProcessingMinutes = floor($totalProcessingSeconds / 60);
                                $processingDays = floor($totalProcessingMinutes / (24 * 60));
                                $processingHours = floor(($totalProcessingMinutes % (24 * 60)) / 60);
                                $processingMinutes = $totalProcessingMinutes % 60;
                                $processingSeconds = $totalProcessingSeconds % 60;
                                
                                if ($processingDays > 0) {
                                    $processingTimeText = $processingDays . ' day' . ($processingDays > 1 ? 's' : '');
                                    if ($processingHours > 0) {
                                        $processingTimeText .= ', ' . $processingHours . ' hour' . ($processingHours > 1 ? 's' : '');
                                    }
                                } elseif ($processingHours > 0) {
                                    $processingTimeText = $processingHours . ' hour' . ($processingHours > 1 ? 's' : '');
                                    if ($processingMinutes > 0) {
                                        $processingTimeText .= ', ' . $processingMinutes . ' min' . ($processingMinutes > 1 ? 's' : '');
                                    }
                                } elseif ($processingMinutes > 0) {
                                    $processingTimeText = $processingMinutes . ' minute' . ($processingMinutes > 1 ? 's' : '');
                                } else {
                                    $processingTimeText = $processingSeconds . ' second' . ($processingSeconds > 1 ? 's' : '');
                                }
                            @endphp
                            <span class="text-lg font-bold text-green-600">{{ $processingTimeText }}</span>
                        </div>
                    @endif
                    
                    @if($asset->warranty)
                        <div class="flex items-center justify-between p-4 {{ $asset->warranty->isExpired() ? 'bg-red-50 border-red-200' : ($asset->warranty->isExpiringSoon() ? 'bg-yellow-50 border-yellow-200' : 'bg-green-50 border-green-200') }} rounded-xl border">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-shield-alt {{ $asset->warranty->isExpired() ? 'text-red-600' : ($asset->warranty->isExpiringSoon() ? 'text-yellow-600' : 'text-green-600') }}"></i>
                                <span class="text-sm font-medium {{ $asset->warranty->isExpired() ? 'text-red-900' : ($asset->warranty->isExpiringSoon() ? 'text-yellow-900' : 'text-green-900') }}">Warranty Days</span>
                            </div>
                            <span class="text-lg font-bold {{ $asset->warranty->isExpired() ? 'text-red-600' : ($asset->warranty->isExpiringSoon() ? 'text-yellow-600' : 'text-green-600') }}">
                                {{ $asset->warranty->getDaysUntilExpiry() < 0 ? 'Expired' : $asset->warranty->getDaysUntilExpiry() }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 📝 Description Section -->
    @if($asset->description)
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b border-gray-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-alt text-indigo-600 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Asset Description</h3>
                        <p class="text-sm text-gray-600">Additional details and specifications</p>
                    </div>
                </div>
            </div>
            <div class="p-8">
                <div class="prose prose-gray max-w-none">
                    <p class="text-gray-700 leading-relaxed text-lg">{{ $asset->description }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- 🚨 Status Messages & Alerts -->
    @if($asset->isRejected() && $asset->rejection_reason)
        <div class="bg-gradient-to-r from-red-50 to-pink-50 border-2 border-red-200 rounded-2xl p-8 mb-8 shadow-lg">
            <div class="flex items-start space-x-6">
                <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-3">
                        <h4 class="text-2xl font-bold text-red-900">❌ Asset Rejected</h4>
                        <span class="px-3 py-1 bg-red-200 text-red-800 rounded-full text-sm font-medium">Action Required</span>
                    </div>
                    <div class="bg-white/60 rounded-xl p-4 border border-red-200">
                        <p class="text-red-800 font-medium mb-2">Rejection Reason:</p>
                        <p class="text-red-700 leading-relaxed">{{ $asset->rejection_reason }}</p>
                    </div>
                    <div class="mt-4 flex items-center space-x-4">
                        <div class="flex items-center text-sm text-red-600">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span>You can edit and resubmit this asset for approval</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($asset->isPending())
        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border-2 border-blue-200 rounded-2xl p-8 shadow-lg">
            <div class="flex items-start space-x-6">
                <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-hourglass-half text-blue-600 text-2xl animate-pulse"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-3">
                        <h4 class="text-2xl font-bold text-blue-900">⏳ Pending Admin Approval</h4>
                        <span class="px-3 py-1 bg-blue-200 text-blue-800 rounded-full text-sm font-medium">In Review</span>
                    </div>
                    <div class="bg-white/60 rounded-xl p-4 border border-blue-200 mb-4">
                        <p class="text-blue-800 leading-relaxed">
                            Your asset submission is currently under review by the admin team. You'll receive a notification once a decision has been made.
                        </p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center space-x-3 text-sm text-blue-700">
                            <i class="fas fa-edit text-blue-600"></i>
                            <span>You can still edit this asset while pending</span>
                        </div>
                        <div class="flex items-center space-x-3 text-sm text-blue-700">
                            <i class="fas fa-trash text-blue-600"></i>
                            <span>You can delete this asset if needed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif($asset->isApproved())
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-2xl p-8 shadow-lg">
            <div class="flex items-start space-x-6">
                <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-3">
                        <h4 class="text-2xl font-bold text-green-900">✅ Asset Approved</h4>
                        <span class="px-3 py-1 bg-green-200 text-green-800 rounded-full text-sm font-medium">Ready for Deployment</span>
                    </div>
                    <div class="bg-white/60 rounded-xl p-4 border border-green-200 mb-4">
                        <p class="text-green-800 leading-relaxed">
                            Congratulations! Your asset has been approved and is now ready for GSU deployment and location assignment.
                        </p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex items-center space-x-3 text-sm text-green-700">
                            <i class="fas fa-check text-green-600"></i>
                            <span>Admin approved</span>
                        </div>
                        <div class="flex items-center space-x-3 text-sm text-green-700">
                            <i class="fas fa-{{ $asset->location ? 'map-marker-alt' : 'clock' }} text-green-600"></i>
                            <span>{{ $asset->location ? 'Location assigned' : 'Awaiting GSU deployment' }}</span>
                        </div>
                        <div class="flex items-center space-x-3 text-sm text-green-700">
                            <i class="fas fa-shield-alt text-green-600"></i>
                            <span>{{ $asset->warranty ? 'Warranty tracked' : 'Ready for use' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
