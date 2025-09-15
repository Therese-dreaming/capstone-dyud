@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.assets.pending') }}" class="text-gray-600 hover:text-blue-600 transition-colors">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                    <div class="bg-blue-100 text-blue-600 rounded-full p-3">
                        <i class="fas fa-cube text-xl"></i>
                    </div>
                    Asset Details
                </h1>
                <p class="text-gray-600 mt-1">Review asset information for approval</p>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex gap-3">
            <button onclick="showRejectModal({{ $asset->id }}, '{{ $asset->asset_code }}')"
                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                <i class="fas fa-times"></i> Reject
            </button>
            <button onclick="approveAsset({{ $asset->id }})"
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                <i class="fas fa-check"></i> Approve
            </button>
        </div>
    </div>

    <!-- Asset Information Card -->
    <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Asset Code -->
            <div class="bg-blue-50 rounded-lg p-6">
                <label class="flex items-center text-sm font-semibold text-blue-800 mb-3">
                    <i class="fas fa-barcode mr-2"></i>
                    Asset Code
                </label>
                <p class="text-xl font-mono font-bold text-blue-900">{{ $asset->asset_code }}</p>
            </div>
            
            <!-- Asset Name -->
            <div class="bg-purple-50 rounded-lg p-6">
                <label class="flex items-center text-sm font-semibold text-purple-800 mb-3">
                    <i class="fas fa-tag mr-2"></i>
                    Asset Name
                </label>
                <p class="text-xl font-semibold text-purple-900">{{ $asset->name }}</p>
            </div>
            
            <!-- Category -->
            <div class="bg-green-50 rounded-lg p-6">
                <label class="flex items-center text-sm font-semibold text-green-800 mb-3">
                    <i class="fas fa-tags mr-2"></i>
                    Category
                </label>
                <p class="text-xl font-semibold text-green-900">{{ $asset->category->name ?? 'No Category' }}</p>
            </div>
            
            <!-- Condition -->
            <div class="bg-orange-50 rounded-lg p-6">
                <label class="flex items-center text-sm font-semibold text-orange-800 mb-3">
                    <i class="fas fa-star mr-2"></i>
                    Condition
                </label>
                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold
                    @switch($asset->condition)
                        @case('Excellent')
                            bg-green-100 text-green-800
                            @break
                        @case('Good')
                            bg-blue-100 text-blue-800
                            @break
                        @case('Fair')
                            bg-yellow-100 text-yellow-800
                            @break
                        @case('Poor')
                            bg-red-100 text-red-800
                            @break
                        @default
                            bg-gray-100 text-gray-800
                    @endswitch">
                    {{ $asset->condition }}
                </span>
            </div>
            
            <!-- Purchase Cost -->
            <div class="bg-emerald-50 rounded-lg p-6">
                <label class="flex items-center text-sm font-semibold text-emerald-800 mb-3">
                    <i class="fas fa-peso-sign mr-2"></i>
                    Purchase Cost
                </label>
                <p class="text-2xl font-bold text-emerald-900">₱{{ number_format($asset->purchase_cost, 2) }}</p>
            </div>
            
            <!-- Purchase Date -->
            <div class="bg-indigo-50 rounded-lg p-6">
                <label class="flex items-center text-sm font-semibold text-indigo-800 mb-3">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    Purchase Date
                </label>
                <p class="text-xl font-semibold text-indigo-900">{{ \Carbon\Carbon::parse($asset->purchase_date)->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Submission Information -->
    <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <i class="fas fa-info-circle text-blue-600"></i>
            Submission Information
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Submitted By -->
            <div class="bg-violet-50 rounded-lg p-6">
                <label class="flex items-center text-sm font-semibold text-violet-800 mb-3">
                    <i class="fas fa-user mr-2"></i>
                    Submitted By
                </label>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr($asset->createdBy->name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-violet-900">{{ $asset->createdBy->name ?? 'Unknown' }}</p>
                        <p class="text-sm text-violet-700">{{ $asset->createdBy->email ?? 'No email' }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Submitted At -->
            <div class="bg-red-50 rounded-lg p-6">
                <label class="flex items-center text-sm font-semibold text-red-800 mb-3">
                    <i class="fas fa-clock mr-2"></i>
                    Submitted At
                </label>
                <p class="text-lg font-semibold text-red-900">{{ $asset->created_at->format('M d, Y \a\t g:i A') }}</p>
                <p class="text-sm text-red-700 mt-1">{{ $asset->created_at->diffForHumans() }}</p>
            </div>
        </div>
    </div>

    <!-- Description Section -->
    @if($asset->description)
    <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <i class="fas fa-align-left text-blue-600"></i>
            Description
        </h2>
        <div class="bg-gray-50 rounded-lg p-6">
            <p class="text-gray-900 leading-relaxed text-lg">{{ $asset->description }}</p>
        </div>
    </div>
    @endif

    <!-- Status Information -->
    <div class="bg-white rounded-xl shadow-lg p-8">
        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <i class="fas fa-flag text-blue-600"></i>
            Current Status
        </h2>
        
        <div class="flex items-center gap-4">
            <div class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-lg font-semibold flex items-center gap-2">
                <i class="fas fa-clock"></i>
                Pending Approval
            </div>
            <div class="text-gray-600">
                <i class="fas fa-info-circle mr-1"></i>
                This asset is waiting for admin approval before it can be deployed.
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
    <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-md relative mx-4">
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
<div id="approveModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
    <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-md relative mx-4">
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
