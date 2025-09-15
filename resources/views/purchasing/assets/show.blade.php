@extends('layouts.purchasing')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Asset Details</h1>
                <p class="mt-1 text-sm text-gray-600">View detailed information about this asset</p>
            </div>
            <div class="flex items-center space-x-3">
                @if($asset->isPending())
                    <a href="{{ route('purchasing.assets.edit', $asset) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-600 hover:text-blue-900 hover:bg-blue-200 rounded-lg transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Asset
                    </a>
                @endif
                <a href="{{ route('purchasing.assets.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Assets
                </a>
            </div>
        </div>
    </div>

    <!-- Asset Information Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-500">Asset Code</span>
                    <span class="text-sm font-bold text-gray-900">{{ $asset->asset_code }}</span>
                </div>
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-500">Asset Name</span>
                    <span class="text-sm text-gray-900">{{ $asset->name }}</span>
                </div>
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-500">Category</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $asset->category->name ?? 'N/A' }}
                    </span>
                </div>
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-500">Condition</span>
                    @php
                        $conditionClasses = [
                            'Excellent' => 'bg-green-100 text-green-800',
                            'Good' => 'bg-blue-100 text-blue-800',
                            'Fair' => 'bg-yellow-100 text-yellow-800',
                            'Poor' => 'bg-red-100 text-red-800',
                        ];
                        $conditionClass = $conditionClasses[$asset->condition] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $conditionClass }}">
                        {{ $asset->condition }}
                    </span>
                </div>
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-500">Purchase Cost</span>
                    <span class="text-sm font-bold text-gray-900">₱{{ number_format($asset->purchase_cost, 2) }}</span>
                </div>
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-500">Purchase Date</span>
                    <span class="text-sm text-gray-900">{{ $asset->purchase_date->format('F d, Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Status & Tracking -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Status & Tracking</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-500">Approval Status</span>
                    @php
                        $statusClasses = [
                            'pending' => 'bg-orange-100 text-orange-800',
                            'approved' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800',
                        ];
                        $status = strtolower($asset->getApprovalStatusLabel());
                        $statusClass = $statusClasses[$status] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                        {{ $asset->getApprovalStatusLabel() }}
                    </span>
                </div>
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-500">Created By</span>
                    <span class="text-sm text-gray-900">{{ $asset->createdBy->name ?? 'Unknown' }}</span>
                </div>
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-500">Created At</span>
                    <span class="text-sm text-gray-900">{{ $asset->created_at->format('F d, Y H:i') }}</span>
                </div>
                @if($asset->approved_at)
                    <div class="flex justify-between items-start">
                        <span class="text-sm font-medium text-gray-500">{{ $asset->isApproved() ? 'Approved' : 'Rejected' }} By</span>
                        <span class="text-sm text-gray-900">{{ $asset->approvedBy->name ?? 'Unknown' }}</span>
                    </div>
                    <div class="flex justify-between items-start">
                        <span class="text-sm font-medium text-gray-500">{{ $asset->isApproved() ? 'Approved' : 'Rejected' }} At</span>
                        <span class="text-sm text-gray-900">{{ $asset->approved_at->format('F d, Y H:i') }}</span>
                    </div>
                @endif
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-500">Location</span>
                    @if($asset->location)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $asset->location->building }} - Floor {{ $asset->location->floor }} - Room {{ $asset->location->room }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Pending GSU Assignment
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Description -->
    @if($asset->description)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Description</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-700 leading-relaxed">{{ $asset->description }}</p>
            </div>
        </div>
    @endif

    <!-- Rejection Reason -->
    @if($asset->isRejected() && $asset->rejection_reason)
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-red-600 mt-1 mr-3"></i>
                <div>
                    <h4 class="text-sm font-medium text-red-900 mb-2">Rejection Reason</h4>
                    <p class="text-sm text-red-700">{{ $asset->rejection_reason }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Status Information -->
    @if($asset->isPending())
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start">
                <i class="fas fa-clock text-blue-600 mt-1 mr-3"></i>
                <div>
                    <h4 class="text-sm font-medium text-blue-900 mb-2">Pending Approval</h4>
                    <p class="text-sm text-blue-700">This asset is pending admin approval. You can edit or delete it while it's pending.</p>
                </div>
            </div>
        </div>
    @elseif($asset->isApproved())
        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
            <div class="flex items-start">
                <i class="fas fa-check-circle text-green-600 mt-1 mr-3"></i>
                <div>
                    <h4 class="text-sm font-medium text-green-900 mb-2">Approved</h4>
                    <p class="text-sm text-green-700">This asset has been approved and is ready for GSU deployment.</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
