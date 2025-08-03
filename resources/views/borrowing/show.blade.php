@extends('layouts.superadmin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-red-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="bg-gradient-to-r from-red-600 to-red-800 text-white p-3 rounded-xl shadow-lg">
                        <i class="fas fa-eye text-xl"></i>
                    </div>
                    @if(auth()->user()->role === 'user')
                        My Borrowing Details
                    @else
                        Borrowing Details
                    @endif
                </h1>
                <p class="text-gray-600 mt-2 text-sm md:text-base">
                    @if(auth()->user()->role === 'user')
                        View detailed information about your borrowing request
                    @else
                        View detailed information about this borrowing
                    @endif
                </p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                <a href="{{ auth()->user()->role === 'user' ? route('user.borrowing.index') : route('borrowing.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors text-sm font-medium flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    @if(auth()->user()->role === 'user')
                        Back to My Borrowings
                    @else
                        Back to Borrowings
                    @endif
                </a>
            </div>
        </div>

        <!-- Borrowing Details Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Borrowing Information</h2>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if(auth()->user()->role !== 'user')
                    <!-- Borrower Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">Borrower Details</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Borrower Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrowing->borrower_name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">ID Number</label>
                            <p class="mt-1 text-sm text-gray-900 font-mono">{{ $borrowing->borrower_id_number }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Room</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrowing->room }}</p>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Borrowing Details -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">Borrowing Details</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Category</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrowing->category }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Items Borrowed</label>
                            <div class="mt-1 flex flex-wrap gap-2">
                                @foreach($borrowing->items as $item)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($item) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($borrowing->status === 'active') bg-green-100 text-green-800
                                @elseif($borrowing->status === 'returned') bg-blue-100 text-blue-800
                                @elseif($borrowing->status === 'overdue') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($borrowing->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Dates and Times -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Borrow Date</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $borrowing->borrow_date->format('M d, Y') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Borrow Time</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $borrowing->borrow_time }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Due Date</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $borrowing->due_date->format('M d, Y') }}</p>
                    </div>
                </div>
                
                @if($borrowing->return_date)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">Return Date</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $borrowing->return_date->format('M d, Y') }}</p>
                </div>
                @endif
                
                @if($borrowing->purpose)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">Purpose of Borrowing</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $borrowing->purpose }}</p>
                </div>
                @endif
                
                <!-- Overdue Warning -->
                @if($borrowing->isOverdue())
                <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Overdue Item</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>This item is overdue by {{ now()->diffInDays($borrowing->due_date) }} days.</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Action Buttons -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                <div class="flex space-x-3">
                    @if($borrowing->status === 'active')
                    <form action="{{ route('borrowing.return', $borrowing) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors text-sm font-medium flex items-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            Mark as Returned
                        </button>
                    </form>
                    @endif
                    
                    @if($borrowing->status === 'active')
                    <form action="{{ route('borrowing.cancel', $borrowing) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this borrowing?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors text-sm font-medium flex items-center gap-2">
                            <i class="fas fa-times-circle"></i>
                            Cancel Borrowing
                        </button>
                    </form>
                    @endif
                </div>
                
                <a href="{{ route('borrowing.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors text-sm font-medium flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 