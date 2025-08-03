@extends('layouts.user')

@section('title', 'Borrowing Details - Asset Management System')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-red-50">
    <!-- Page Header -->
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="bg-gradient-to-r from-red-600 to-red-800 text-white p-3 rounded-xl shadow-lg">
                        <i class="fas fa-eye text-xl"></i>
                    </div>
                    Borrowing Details
                </h1>
                <p class="text-gray-600 mt-2 text-sm md:text-base">View detailed information about your borrowing request</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                <a href="{{ route('user.borrowing.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors text-sm font-medium flex items-center gap-2 shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                    Back to My Borrowings
                </a>
                @if($borrowing->status === 'active' || $borrowing->status === 'overdue')
                    <button onclick="cancelBorrowing({{ $borrowing->id }})" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors text-sm font-medium flex items-center gap-2">
                        <i class="fas fa-times"></i>
                        Cancel Request
                    </button>
                @endif
            </div>
        </div>

        <!-- Status Banner -->
        <div class="mb-8">
            @if($borrowing->status === 'active')
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">Active Borrowing</h3>
                            <p class="text-sm text-green-700 mt-1">Your borrowing request is currently active. Please return the items by the due date.</p>
                        </div>
                    </div>
                </div>
            @elseif($borrowing->status === 'overdue')
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Overdue</h3>
                            <p class="text-sm text-red-700 mt-1">This borrowing is overdue. Please return the items as soon as possible.</p>
                        </div>
                    </div>
                </div>
            @elseif($borrowing->status === 'returned')
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-blue-400 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Returned</h3>
                            <p class="text-sm text-blue-700 mt-1">This borrowing has been successfully returned.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Borrowing Details -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Borrowing Information</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Borrower Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-user mr-2 text-red-600"></i>Borrower Details
                                </h3>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500">Full Name</label>
                                        <p class="text-sm text-gray-900">{{ $borrowing->borrower_name }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500">ID Number</label>
                                        <p class="text-sm text-gray-900">{{ $borrowing->borrower_id_number }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Location Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-map-marker-alt mr-2 text-red-600"></i>Location Details
                                </h3>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500">Room/Location</label>
                                        <p class="text-sm text-gray-900">{{ $borrowing->room }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Date Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-calendar mr-2 text-red-600"></i>Date Information
                                </h3>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500">Borrow Date</label>
                                        <p class="text-sm text-gray-900">{{ $borrowing->borrow_date->format('F d, Y') }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500">Borrow Time</label>
                                        <p class="text-sm text-gray-900">{{ $borrowing->borrow_time }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500">Due Date</label>
                                        <p class="text-sm text-gray-900 {{ $borrowing->isOverdue() ? 'text-red-600 font-medium' : '' }}">
                                            {{ $borrowing->due_date->format('F d, Y') }}
                                            @if($borrowing->isOverdue())
                                                <span class="ml-2 text-xs bg-red-100 text-red-800 px-2 py-1 rounded">Overdue</span>
                                            @endif
                                        </p>
                                    </div>
                                    @if($borrowing->return_date)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500">Return Date</label>
                                            <p class="text-sm text-gray-900">{{ $borrowing->return_date->format('F d, Y') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Status Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-info-circle mr-2 text-red-600"></i>Status Information
                                </h3>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500">Current Status</label>
                                        <div class="mt-1">
                                            @if($borrowing->status === 'active')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-clock mr-1"></i>Active
                                                </span>
                                            @elseif($borrowing->status === 'overdue')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>Overdue
                                                </span>
                                            @elseif($borrowing->status === 'returned')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <i class="fas fa-check-circle mr-1"></i>Returned
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ ucfirst($borrowing->status) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Items Borrowed -->
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-boxes mr-2 text-red-600"></i>Items Borrowed
                            </h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($borrowing->items as $item)
                                        <div class="bg-white rounded-lg p-3 border border-gray-200">
                                            <div class="flex items-center">
                                                <i class="fas fa-box text-red-600 mr-2"></i>
                                                <span class="text-sm font-medium text-gray-900">{{ $item }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Purpose -->
                        @if($borrowing->purpose)
                            <div class="mt-8">
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-comment mr-2 text-red-600"></i>Purpose
                                </h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-700">{{ $borrowing->purpose }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column - Actions and Timeline -->
            <div class="lg:col-span-1">
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @if($borrowing->status === 'active' || $borrowing->status === 'overdue')
                                <button onclick="cancelBorrowing({{ $borrowing->id }})" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors text-sm font-medium flex items-center justify-center">
                                    <i class="fas fa-times mr-2"></i>Cancel Request
                                </button>
                            @endif
                            
                            <a href="{{ route('user.borrowing.index') }}" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors text-sm font-medium flex items-center justify-center">
                                <i class="fas fa-list mr-2"></i>View All Borrowings
                            </a>
                            
                            <a href="{{ route('user.borrowing.create') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors text-sm font-medium flex items-center justify-center">
                                <i class="fas fa-plus mr-2"></i>New Request
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Timeline</h3>
                    </div>
                    <div class="p-6">
                        <div class="flow-root">
                            <ul class="-mb-8">
                                <li>
                                    <div class="relative pb-8">
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                    <i class="fas fa-check text-white text-sm"></i>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">Borrowing request <span class="font-medium text-gray-900">created</span></p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    <time>{{ $borrowing->borrow_date->format('M d, Y') }}</time>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                
                                @if($borrowing->status === 'returned')
                                    <li>
                                        <div class="relative pb-8">
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                        <i class="fas fa-check text-white text-sm"></i>
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div>
                                                        <p class="text-sm text-gray-500">Items <span class="font-medium text-gray-900">returned</span></p>
                                                    </div>
                                                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                        <time>{{ $borrowing->return_date->format('M d, Y') }}</time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function cancelBorrowing(borrowingId) {
    if (confirm('Are you sure you want to cancel this borrowing request? This action cannot be undone.')) {
        fetch(`/user/borrowing/${borrowingId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("user.borrowing.index") }}';
            } else {
                alert('Failed to cancel borrowing request');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while canceling the request');
        });
    }
}
</script>
@endsection 