@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="mb-6 lg:mb-0">
                    <div class="flex items-center mb-3">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-clipboard-check text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">
                                Pending Asset Approvals
                            </h1>
                            <p class="text-gray-600 mt-1">Review and approve assets submitted by the purchasing team</p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
                    <div class="flex items-center space-x-3">
                        <div class="bg-orange-600 text-white px-4 py-2 rounded-lg shadow-sm">
                            <div class="flex items-center">
                                <i class="fas fa-clock mr-2"></i>
                                <span class="font-semibold">{{ $assets->total() }}</span>
                                <span class="ml-1">Pending</span>
                            </div>
                        </div>
                        <div class="bg-green-600 text-white px-4 py-2 rounded-lg shadow-sm">
                            <div class="flex items-center">
                                <i class="fas fa-chart-line mr-2"></i>
                                <span class="font-semibold">{{ $assets->total() > 0 ? 'Action Required' : 'All Clear' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="mb-8 relative" id="success-alert">
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 bg-green-500 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-check text-white"></i>
                        </div>
                        <div>
                            <h4 class="text-green-800 font-semibold">Success!</h4>
                            <p class="text-green-700 mt-1">{{ session('success') }}</p>
                        </div>
                    </div>
                    <button onclick="closeAlert('success-alert')" class="text-green-500 hover:text-green-700 transition-colors duration-200">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-8 relative" id="error-alert">
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 bg-red-500 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-exclamation-triangle text-white"></i>
                        </div>
                        <div>
                            <h4 class="text-red-800 font-semibold">Error</h4>
                            <p class="text-red-700 mt-1">{{ session('error') }}</p>
                        </div>
                    </div>
                    <button onclick="closeAlert('error-alert')" class="text-red-500 hover:text-red-700 transition-colors duration-200">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Assets Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- Table Header -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Assets Awaiting Approval</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">{{ $assets->count() }} of {{ $assets->total() }} items</span>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-box mr-2 text-blue-500"></i>
                                    Asset Details
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-tags mr-2 text-purple-500"></i>
                                    Category
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-peso-sign mr-2 text-green-500"></i>
                                    Cost
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar mr-2 text-orange-500"></i>
                                    Purchase Date
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-user mr-2 text-indigo-500"></i>
                                    Submitted By
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-clock mr-2 text-red-500"></i>
                                    Submitted
                                </div>
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-cogs mr-2 text-gray-500"></i>
                                    Actions
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-50">
                        @forelse($assets as $asset)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-6 py-6">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mr-4">
                                            <i class="fas fa-cube text-white"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900 group-hover:text-blue-700 transition-colors">
                                                {{ $asset->asset_code }}
                                            </div>
                                            <div class="text-sm text-gray-600 mt-1">{{ $asset->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-purple-100 text-purple-800">
                                        <i class="fas fa-tag mr-1.5"></i>
                                        {{ $asset->category->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex items-center">
                                        <div class="bg-green-100 px-3 py-2 rounded-lg">
                                            <span class="text-sm font-bold text-green-800">₱{{ number_format($asset->purchase_cost, 2) }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex items-center text-sm text-gray-700">
                                        <i class="fas fa-calendar-alt mr-2 text-orange-500"></i>
                                        {{ $asset->purchase_date->format('M d, Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-white text-xs font-semibold">
                                                {{ strtoupper(substr($asset->createdBy->name ?? 'U', 0, 1)) }}
                                            </span>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $asset->createdBy->name ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-clock mr-2 text-red-400"></i>
                                        {{ $asset->created_at->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex items-center justify-center space-x-2">
                                        <!-- View Details Button -->
                                        <a href="{{ route('admin.assets.show', $asset->id) }}"
                                           class="group/btn relative inline-flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                                           title="View Details">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover/btn:opacity-100 transition-opacity whitespace-nowrap">
                                            View Details
                                        </div>
                                        
                                        <!-- Approve Button -->
                                        <button onclick="approveAsset({{ $asset->id }}, '{{ $asset->asset_code }}')"
                                                class="group/btn relative inline-flex items-center justify-center w-10 h-10 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors"
                                                title="Approve Asset">
                                            <i class="fas fa-check text-sm"></i>
                                            <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover/btn:opacity-100 transition-opacity whitespace-nowrap">
                                                Approve
                                            </div>
                                        </button>
                                        
                                        <!-- Reject Button -->
                                        <button onclick="showRejectModal({{ $asset->id }}, '{{ $asset->asset_code }}')"
                                                class="group/btn relative inline-flex items-center justify-center w-10 h-10 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors"
                                                title="Reject Asset">
                                            <i class="fas fa-times text-sm"></i>
                                            <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover/btn:opacity-100 transition-opacity whitespace-nowrap">
                                                Reject
                                            </div>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-24 h-24 bg-green-500 rounded-full flex items-center justify-center mb-6 shadow-sm">
                                            <i class="fas fa-check-circle text-white text-4xl"></i>
                                        </div>
                                        <h3 class="text-2xl font-bold text-gray-900 mb-3">All Caught Up!</h3>
                                        <p class="text-gray-600 text-lg mb-4">No pending assets for approval at the moment.</p>
                                        <div class="bg-green-50 border border-green-200 rounded-lg px-6 py-4">
                                            <p class="text-green-700 text-sm">
                                                <i class="fas fa-info-circle mr-2"></i>
                                                New asset submissions will appear here for your review.
                                            </p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($assets->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Showing {{ $assets->firstItem() }} to {{ $assets->lastItem() }} of {{ $assets->total() }} results
                    </div>
                    <div class="pagination-wrapper">
                        {{ $assets->links() }}
                    </div>
                </div>
            </div>
        @endif
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
            <p class="text-red-600 font-semibold text-center" id="reject-asset-name"></p>
        </div>

        <form id="rejectForm" method="POST" class="w-full">
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
                <button type="button" onclick="submitRejectForm()" id="reject-submit-btn"
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
            <p class="text-green-600 font-semibold text-center" id="approve-asset-name"></p>
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
// Global variables
let currentAssetId = null;
let currentAssetCode = null;

// Alert functions
function closeAlert(alertId) {
    const alert = document.getElementById(alertId);
    if (alert) {
        alert.remove();
    }
}

// Asset approval functions
function approveAsset(assetId, assetCode) {
    currentAssetId = assetId;
    currentAssetCode = assetCode;
    
    const approveAssetNameElement = document.getElementById('approve-asset-name');
    if (approveAssetNameElement) {
        approveAssetNameElement.textContent = assetCode;
    }
    
    document.getElementById('approveModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    currentAssetId = null;
    currentAssetCode = null;
}

function confirmApproveAsset() {
    if (currentAssetId) {
        const form = document.getElementById('approveForm');
        form.action = `{{ url('admin/assets') }}/${currentAssetId}/approve`;
        form.submit();
    }
}

// Reject modal functions

function showRejectModal(assetId, assetCode) {
    currentAssetId = assetId;
    currentAssetCode = assetCode;
    
    const rejectAssetNameElement = document.getElementById('reject-asset-name');
    if (rejectAssetNameElement) {
        rejectAssetNameElement.textContent = assetCode;
    }
    
    document.getElementById('rejection_reason').value = '';
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
    currentAssetId = null;
    currentAssetCode = null;
}

function submitRejectForm() {
    const rejectionReason = document.getElementById('rejection_reason').value.trim();
    
    if (!rejectionReason) {
        alert('Please provide a rejection reason.');
        return;
    }
    
    if (confirm('Are you sure you want to reject this asset? This action cannot be undone.')) {
        const form = document.getElementById('rejectForm');
        form.action = `{{ url('admin/assets') }}/${currentAssetId}/reject`;
        form.submit();
    }
}

// Enable/disable reject button based on textarea content
document.addEventListener('DOMContentLoaded', function() {
    const rejectionTextarea = document.getElementById('rejection_reason');
    const rejectButton = document.getElementById('reject-submit-btn');
    
    // Add null checks to prevent errors
    if (rejectionTextarea && rejectButton) {
        rejectionTextarea.addEventListener('input', function() {
            if (this.value.trim()) {
                rejectButton.disabled = false;
                rejectButton.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                rejectButton.disabled = true;
                rejectButton.classList.add('opacity-50', 'cursor-not-allowed');
            }
        });
        
        // Initial state
        rejectButton.disabled = true;
        rejectButton.classList.add('opacity-50', 'cursor-not-allowed');
    }
});
</script>
@endsection
