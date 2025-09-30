@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto py-4 md:py-6 px-2 sm:px-4 lg:px-8">
    <!-- 🎯 Enhanced Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl md:rounded-2xl shadow-xl p-4 md:p-8 mb-4 md:mb-8 border-2 md:border-4 border-blue-800">
        <div class="flex flex-col lg:flex-row items-start justify-between gap-4 lg:gap-0">
            <!-- Asset Icon & Info -->
            <div class="flex items-start space-x-3 md:space-x-6 flex-1 min-w-0">
                <!-- Asset Category Icon -->
                <div class="w-14 h-14 md:w-20 md:h-20 bg-blue-700 rounded-xl md:rounded-2xl flex items-center justify-center border-2 border-blue-800 flex-shrink-0">
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
                    <i class="{{ $iconClass }} text-xl md:text-3xl text-white"></i>
                </div>
                
                <!-- Asset Info -->
                <div class="flex-1 min-w-0">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3 mb-2">
                        <h1 class="text-xl md:text-2xl lg:text-4xl font-bold text-white truncate">{{ $asset->name }}</h1>
                        <div class="w-2 h-2 bg-white/60 rounded-full hidden sm:block flex-shrink-0"></div>
                        <span class="text-sm md:text-base lg:text-xl text-blue-100 font-medium font-mono truncate">{{ $asset->asset_code }}</span>
                    </div>
                    <p class="text-blue-100 text-xs md:text-sm lg:text-lg mb-3 md:mb-4">{{ $asset->category->name ?? 'Uncategorized' }} Asset • Pending Admin Review</p>
                    
                    <!-- Status & Submission Info -->
                    <div class="flex flex-wrap items-center gap-2 md:gap-4">
                        <span class="inline-flex items-center px-3 md:px-4 py-1.5 md:py-2 rounded-full text-xs md:text-sm font-semibold bg-yellow-100 text-yellow-800 shadow-lg">
                            <i class="fas fa-hourglass-half mr-1.5 md:mr-2 text-xs md:text-sm"></i>
                            Pending Approval
                        </span>
                        
                        <div class="text-blue-100 text-xs md:text-sm">
                            <i class="fas fa-user mr-1"></i>
                            <span class="hidden sm:inline">Submitted by </span><span class="font-semibold">{{ $asset->createdBy->name ?? 'Unknown' }}</span>
                        </div>
                        
                        <div class="text-blue-100 text-xs md:text-sm">
                            <i class="fas fa-clock mr-1"></i>
                            {{ $asset->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row lg:flex-col space-y-2 sm:space-y-0 sm:space-x-2 lg:space-x-0 lg:space-y-3 w-full lg:w-auto">
                <button onclick="approveAsset({{ $asset->id }})"
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 md:py-3 px-4 md:px-6 rounded-lg md:rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow-lg border-2 border-green-800 text-sm md:text-base">
                    <i class="fas fa-check text-sm md:text-base"></i> Approve Asset
                </button>
                <button onclick="showRejectModal({{ $asset->id }}, '{{ $asset->asset_code }}')"
                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 md:py-3 px-4 md:px-6 rounded-lg md:rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow-lg border-2 border-red-800 text-sm md:text-base">
                    <i class="fas fa-times text-sm md:text-base"></i> Reject Asset
                </button>
                <a href="{{ route('admin.assets.pending') }}" 
                   class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2.5 md:py-3 px-4 md:px-6 rounded-lg md:rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow-lg border-2 border-blue-800 text-sm md:text-base">
                    <i class="fas fa-arrow-left text-sm md:text-base"></i> Back to Pending
                </a>
            </div>
        </div>
    </div>

    <!-- 📊 Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 md:gap-8 mb-4 md:mb-8">
        <!-- Left Column - Asset Details (3/4 width) -->
        <div class="lg:col-span-3 space-y-4 md:space-y-8">
            <!-- 📋 Basic Asset Information -->
            <div class="bg-white rounded-xl md:rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-slate-50 to-slate-100 px-4 md:px-8 py-4 md:py-6 border-b border-gray-200">
                    <h2 class="text-lg md:text-2xl font-bold text-gray-900 flex items-center gap-2 md:gap-3">
                        <i class="fas fa-info-circle text-blue-600 text-base md:text-xl"></i>
                        Asset Information
                    </h2>
                </div>
                
                <div class="p-4 md:p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                        <!-- Asset Code -->
                        <div class="bg-blue-50 rounded-lg md:rounded-xl p-4 md:p-6 border border-blue-200">
                            <div class="flex items-center justify-between mb-2 md:mb-3">
                                <label class="text-xs md:text-sm font-bold text-blue-800">Asset Code</label>
                                <i class="fas fa-barcode text-blue-600 text-sm md:text-base"></i>
                            </div>
                            <p class="text-base md:text-xl font-mono font-bold text-blue-900 truncate">{{ $asset->asset_code }}</p>
                        </div>
                        
                        <!-- Asset Name -->
                        <div class="bg-purple-50 rounded-lg md:rounded-xl p-4 md:p-6 border border-purple-200">
                            <div class="flex items-center justify-between mb-2 md:mb-3">
                                <label class="text-xs md:text-sm font-bold text-purple-800">Asset Name</label>
                                <i class="fas fa-tag text-purple-600 text-sm md:text-base"></i>
                            </div>
                            <p class="text-base md:text-xl font-bold text-purple-900 truncate">{{ $asset->name }}</p>
                        </div>
                        
                        <!-- Category -->
                        <div class="bg-green-50 rounded-lg md:rounded-xl p-4 md:p-6 border border-green-200">
                            <div class="flex items-center justify-between mb-2 md:mb-3">
                                <label class="text-xs md:text-sm font-bold text-green-800">Category</label>
                                <i class="fas fa-tags text-green-600 text-sm md:text-base"></i>
                            </div>
                            <p class="text-base md:text-xl font-bold text-green-900">{{ $asset->category->name ?? 'No Category' }}</p>
                        </div>
                        
                        <!-- Condition -->
                        <div class="bg-orange-50 rounded-lg md:rounded-xl p-4 md:p-6 border border-orange-200">
                            <div class="flex items-center justify-between mb-2 md:mb-3">
                                <label class="text-xs md:text-sm font-bold text-orange-800">Condition</label>
                                <i class="fas fa-star text-orange-600 text-sm md:text-base"></i>
                            </div>
                            <span class="inline-flex items-center px-3 md:px-4 py-1.5 md:py-2 rounded-lg md:rounded-xl text-xs md:text-sm font-bold border-2
                                @switch($asset->condition)
                                    @case('Excellent')
                                        bg-green-100 text-green-800 border-green-300
                                        @break
                                    @case('Good')
                                        bg-blue-100 text-blue-800 border-blue-300
                                        @break
                                    @case('Fair')
                                        bg-yellow-100 text-yellow-800 border-yellow-300
                                        @break
                                    @case('Poor')
                                        bg-red-100 text-red-800 border-red-300
                                        @break
                                    @default
                                        bg-gray-100 text-gray-800 border-gray-300
                                @endswitch">
                                <i class="fas fa-circle mr-2 text-xs"></i>
                                {{ $asset->condition }}
                            </span>
                        </div>
                        
                        <!-- Purchase Cost -->
                        <div class="bg-emerald-50 rounded-lg md:rounded-xl p-4 md:p-6 border border-emerald-200">
                            <div class="flex items-center justify-between mb-2 md:mb-3">
                                <label class="text-xs md:text-sm font-bold text-emerald-800">Purchase Cost</label>
                                <i class="fas fa-peso-sign text-emerald-600 text-sm md:text-base"></i>
                            </div>
                            <p class="text-lg md:text-2xl font-bold text-emerald-900">₱{{ number_format($asset->purchase_cost, 2) }}</p>
                        </div>
                        
                        <!-- Purchase Date -->
                        <div class="bg-indigo-50 rounded-lg md:rounded-xl p-4 md:p-6 border border-indigo-200">
                            <div class="flex items-center justify-between mb-2 md:mb-3">
                                <label class="text-xs md:text-sm font-bold text-indigo-800">Purchase Date</label>
                                <i class="fas fa-calendar-alt text-indigo-600 text-sm md:text-base"></i>
                            </div>
                            <p class="text-base md:text-xl font-bold text-indigo-900">{{ \Carbon\Carbon::parse($asset->purchase_date)->format('M d, Y') }}</p>
                            <p class="text-xs md:text-sm text-indigo-700 mt-1">{{ \Carbon\Carbon::parse($asset->purchase_date)->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 🛡️ Warranty Information Section -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-orange-50 to-orange-100 px-8 py-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-shield-alt text-orange-600"></i>
                        Warranty Protection
                    </h2>
                </div>
                
                @if($asset->warranty)
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                            <!-- Manufacturer -->
                            <div class="bg-orange-50 rounded-xl p-6 border border-orange-200">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="text-sm font-bold text-orange-800">Manufacturer</label>
                                    <i class="fas fa-industry text-orange-600"></i>
                                </div>
                                <p class="text-xl font-bold text-orange-900">{{ $asset->warranty->manufacturer }}</p>
                            </div>
                            
                            <!-- Model -->
                            <div class="bg-orange-50 rounded-xl p-6 border border-orange-200">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="text-sm font-bold text-orange-800">Model</label>
                                    <i class="fas fa-cog text-orange-600"></i>
                                </div>
                                <p class="text-xl font-bold text-orange-900">{{ $asset->warranty->model }}</p>
                            </div>
                            
                            <!-- Warranty Status -->
                            <div class="bg-orange-50 rounded-xl p-6 border border-orange-200">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="text-sm font-bold text-orange-800">Status</label>
                                    <i class="fas fa-shield-alt text-orange-600"></i>
                                </div>
                                <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold border-2 {{ $asset->warranty->getStatusBadgeClass() }} border-opacity-50">
                                    <i class="fas fa-{{ $asset->warranty->isExpired() ? 'exclamation-triangle' : ($asset->warranty->isExpiringSoon() ? 'clock' : 'check') }} mr-2"></i>
                                    {{ $asset->warranty->getStatusLabel() }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Warranty Expiry (Full Width) -->
                        <div class="bg-gradient-to-r from-orange-50 to-yellow-50 rounded-xl p-6 border border-orange-200">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-orange-900 flex items-center gap-2">
                                    <i class="fas fa-calendar-times text-orange-600"></i>
                                    Warranty Expiry Date
                                </h3>
                                <div class="text-sm text-orange-700">
                                    {{ $asset->warranty->warranty_expiry->diffForHumans() }}
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <p class="text-2xl font-bold text-orange-900">
                                    {{ $asset->warranty->warranty_expiry->format('F d, Y') }}
                                </p>
                                @if($asset->warranty->getDaysUntilExpiry() > 0)
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-orange-800">{{ $asset->warranty->getDaysUntilExpiry() }} days remaining</p>
                                        <p class="text-sm text-orange-600">Until warranty expires</p>
                                    </div>
                                @else
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-red-800">Expired {{ abs($asset->warranty->getDaysUntilExpiry()) }} days ago</p>
                                        <p class="text-sm text-red-600">Warranty has ended</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    @empty($asset->warranty)
                        <div class="p-12 text-center">
                            <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-shield-alt text-3xl text-orange-400"></i>
                            </div>
                            <h4 class="text-2xl font-bold text-gray-900 mb-3">No Warranty Information</h4>
                            <p class="text-gray-600 mb-6 max-w-md mx-auto text-lg">
                                Warranty details are not available for this asset. This may affect support and maintenance coverage.
                            </p>
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 max-w-lg mx-auto">
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                                    <div class="text-left">
                                        <p class="text-sm font-bold text-blue-900 mb-2">Warranty Benefits Include:</p>
                                        <ul class="text-sm text-blue-700 space-y-1">
                                            <li>• Manufacturer support coverage</li>
                                            <li>• Repair and replacement protection</li>
                                            <li>• Maintenance scheduling alerts</li>
                                            <li>• Extended product lifecycle support</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endempty
                @endif
            </div>

            <!-- 📝 Description Section -->
            @if($asset->description)
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-slate-50 to-slate-100 px-8 py-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-align-left text-blue-600"></i>
                        Asset Description
                    </h2>
                </div>
                <div class="p-8">
                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                        <p class="text-gray-900 leading-relaxed text-lg">{{ $asset->description }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Sidebar (1/4 width) -->
        <div class="lg:col-span-1 space-y-4 md:space-y-8">
            <!-- 👤 Submission Information -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-violet-50 to-violet-100 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-user-check text-violet-600"></i>
                        Submission Details
                    </h3>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Submitted By -->
                    <div>
                        <label class="text-sm font-bold text-violet-800 mb-3 block">Submitted By</label>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-violet-600 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                {{ strtoupper(substr($asset->createdBy->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-bold text-violet-900">{{ $asset->createdBy->name ?? 'Unknown' }}</p>
                                <p class="text-sm text-violet-700">{{ $asset->createdBy->email ?? 'No email' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Submitted At -->
                    <div>
                        <label class="text-sm font-bold text-violet-800 mb-3 block">Submission Time</label>
                        <div class="bg-violet-50 rounded-lg p-4 border border-violet-200">
                            <p class="font-bold text-violet-900">{{ $asset->created_at->format('M d, Y') }}</p>
                            <p class="text-sm text-violet-700">{{ $asset->created_at->format('g:i A') }}</p>
                            <p class="text-xs text-violet-600 mt-1">{{ $asset->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 🚦 Current Status -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-flag text-yellow-600"></i>
                        Approval Status
                    </h3>
                </div>
                
                <div class="p-6">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-hourglass-half text-2xl text-yellow-600"></i>
                        </div>
                        <h4 class="text-lg font-bold text-yellow-800 mb-2">Pending Approval</h4>
                        <p class="text-sm text-yellow-700 mb-4">
                            This asset is waiting for admin review and approval before deployment.
                        </p>
                        <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                            <p class="text-xs text-blue-800 font-medium">
                                <i class="fas fa-info-circle mr-1"></i>
                                Once approved, GSU team can deploy this asset to locations.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ⏱️ Processing Time -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-stopwatch text-blue-600"></i>
                        Processing Time
                    </h3>
                </div>
                
                <div class="p-6 text-center">
                    @php
                        $totalMinutes = $asset->created_at->diffInMinutes(now());
                        $daysSinceCreation = floor($totalMinutes / (24 * 60));
                        $hoursSinceCreation = floor(($totalMinutes % (24 * 60)) / 60);
                        $minutesSinceCreation = $totalMinutes % 60;
                        
                        if ($daysSinceCreation > 0) {
                            $timeText = $daysSinceCreation . ' day' . ($daysSinceCreation > 1 ? 's' : '');
                            if ($hoursSinceCreation > 0) {
                                $timeText .= ', ' . $hoursSinceCreation . ' hour' . ($hoursSinceCreation > 1 ? 's' : '');
                            }
                        } elseif ($hoursSinceCreation > 0) {
                            $timeText = $hoursSinceCreation . ' hour' . ($hoursSinceCreation > 1 ? 's' : '');
                            if ($minutesSinceCreation > 0) {
                                $timeText .= ', ' . $minutesSinceCreation . ' min' . ($minutesSinceCreation > 1 ? 's' : '');
                            }
                        } else {
                            $timeText = $minutesSinceCreation . ' minute' . ($minutesSinceCreation > 1 ? 's' : '');
                        }
                    @endphp
                    
                    <div class="text-3xl font-bold text-blue-600 mb-2">{{ $timeText }}</div>
                    <p class="text-sm text-blue-700">in review queue</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden p-4">
    <div class="bg-white rounded-xl shadow-xl p-6 md:p-8 w-full max-w-md relative">
        <button onclick="closeRejectModal()" class="absolute top-3 right-3 text-gray-400 hover:text-red-600 text-xl">
            <i class="fas fa-times"></i>
        </button>
        
        <div class="flex flex-col items-center mb-6">
            <div class="bg-red-100 text-red-600 rounded-full p-4 mb-4">
                <i class="fas fa-exclamation-triangle text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold mb-2 text-gray-800">Reject Asset</h3>
            <p class="text-gray-600 text-center mb-2">You are about to reject:</p>
            <p class="text-red-600 font-semibold text-center" id="reject-asset-name">{{ $asset->asset_code }}</p>
        </div>

        <form id="rejectForm" method="POST" action="{{ route('admin.assets.reject', $asset->id) }}" class="w-full">
            @csrf
            @method('PUT')
            
            <div class="mb-6">
                <label for="rejection_reason" class="block text-sm font-semibold text-gray-800 mb-3">
                    <i class="fas fa-comment-alt mr-2 text-red-600"></i>
                    Reason for Rejection <span class="text-red-500">*</span>
                </label>
                <textarea 
                    id="rejection_reason" 
                    name="rejection_reason" 
                    rows="4" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none transition-colors duration-200" 
                    placeholder="Please provide a detailed reason for rejecting this asset..."
                    required></textarea>
                <p class="text-sm text-gray-600 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    This reason will be sent to the purchasing team.
                </p>
            </div>

            <div class="flex flex-col gap-3">
                <button type="submit"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-ban"></i> Reject Asset
                </button>
                <button type="button" onclick="closeRejectModal()"
                        class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden p-4">
    <div class="bg-white rounded-xl shadow-xl p-6 md:p-8 w-full max-w-md relative">
        <button onclick="closeApproveModal()" class="absolute top-3 right-3 text-gray-400 hover:text-green-600 text-xl">
            <i class="fas fa-times"></i>
        </button>
        
        <div class="flex flex-col items-center mb-6">
            <div class="bg-green-100 text-green-600 rounded-full p-4 mb-4">
                <i class="fas fa-check-circle text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold mb-2 text-gray-800">Approve Asset</h3>
            <p class="text-gray-600 text-center mb-2">You are about to approve:</p>
            <p class="text-green-600 font-semibold text-center">{{ $asset->asset_code }}</p>
        </div>

        <div class="mb-6">
            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-green-600 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-semibold text-green-800">Approval Confirmation</h4>
                        <p class="text-sm text-green-700 mt-1">
                            Once approved, this asset will be available for deployment by the GSU team.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-3">
            <button type="button" onclick="confirmApproveAsset()"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                <i class="fas fa-check"></i> Approve Asset
            </button>
            <button type="button" onclick="closeApproveModal()"
                    class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                <i class="fas fa-times"></i> Cancel
            </button>
        </div>
    </div>
</div>

<!-- Hidden Approve Form -->
<form id="approveForm" method="POST" style="display: none;">
    @csrf
</form>

<script>
// Asset approval functions
function approveAsset(assetId) {
    document.getElementById('approveModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function confirmApproveAsset() {
    const form = document.getElementById('approveForm');
    form.action = `{{ url('admin/assets') }}/{{ $asset->id }}/approve`;
    form.submit();
}

// Reject modal functions
function showRejectModal(assetId, assetCode) {
    document.getElementById('rejectModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Focus on textarea
    setTimeout(() => {
        document.getElementById('rejection_reason').focus();
    }, 100);
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('rejection_reason').value = '';
}

// Close modals when clicking outside
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});

document.getElementById('approveModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeApproveModal();
    }
});
</script>
@endsection
