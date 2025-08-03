@extends('layouts.user')

@section('title', 'My Borrowings - Asset Management System')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-red-50" x-data="{ 
    activeTab: '{{ request('tab', 'all') }}',
    showFilters: false
}">
    <!-- Page Header -->
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="bg-gradient-to-r from-red-600 to-red-800 text-white p-3 rounded-xl shadow-lg">
                        <i class="fas fa-list text-xl"></i>
                    </div>
                    My Borrowings
                </h1>
                <p class="text-gray-600 mt-2 text-sm md:text-base">Track your asset borrowing history and current items</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('user.borrowing.create') }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors text-sm font-medium flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    New Borrowing Request
                </a>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8 overflow-hidden">
            <div class="flex border-b border-gray-200">
                <button @click="activeTab = 'all'" 
                        :class="activeTab === 'all' ? 'bg-red-50 text-red-700 border-b-2 border-red-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
                        class="flex-1 px-6 py-4 text-sm font-medium transition-all duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-list"></i>
                    All Borrowings
                </button>
                <button @click="activeTab = 'current'" 
                        :class="activeTab === 'current' ? 'bg-red-50 text-red-700 border-b-2 border-red-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
                        class="flex-1 px-6 py-4 text-sm font-medium transition-all duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-clock"></i>
                    Current Items
                </button>
                <button @click="activeTab = 'overdue'" 
                        :class="activeTab === 'overdue' ? 'bg-red-50 text-red-700 border-b-2 border-red-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
                        class="flex-1 px-6 py-4 text-sm font-medium transition-all duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    Overdue Items
                </button>
                <button @click="activeTab = 'returned'" 
                        :class="activeTab === 'returned' ? 'bg-red-50 text-red-700 border-b-2 border-red-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
                        class="flex-1 px-6 py-4 text-sm font-medium transition-all duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    Returned Items
                </button>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <form action="{{ route('user.borrowing.index') }}" method="GET" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <input type="hidden" name="tab" x-bind:value="activeTab">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-red-500 focus:border-red-500 sm:text-sm" placeholder="Search borrowings...">
                </div>
                <div class="flex items-center gap-2">
                    <button @click="showFilters = !showFilters" type="button" class="bg-white border border-gray-300 rounded-md px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 flex items-center gap-2">
                        <i class="fas fa-filter"></i>
                        <span>Filters</span>
                        <i :class="showFilters ? 'fa-chevron-up' : 'fa-chevron-down'" class="fas text-xs"></i>
                    </button>
                    <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2">
                        <i class="fas fa-search"></i>
                        Apply Filters
                    </button>
                </div>
            
                <!-- Filters Panel -->
                <div x-show="showFilters" x-transition class="mt-4 pt-4 border-t border-gray-200 grid grid-cols-1 md:grid-cols-3 gap-4 w-full">
                    <div>
                        <label for="category-filter" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select id="category-filter" name="category" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-md">
                            <option value="">All Categories</option>
                            <option value="Electronics & IT Equipments" {{ request('category') == 'Electronics & IT Equipments' ? 'selected' : '' }}>Electronics & IT Equipments</option>
                            <option value="Fixtures" {{ request('category') == 'Fixtures' ? 'selected' : '' }}>Fixtures</option>
                            <option value="Furnitures" {{ request('category') == 'Furnitures' ? 'selected' : '' }}>Furnitures</option>
                            <option value="Religious or Institutional Items" {{ request('category') == 'Religious or Institutional Items' ? 'selected' : '' }}>Religious or Institutional Items</option>
                            <option value="Teaching & Presentation Tools" {{ request('category') == 'Teaching & Presentation Tools' ? 'selected' : '' }}>Teaching & Presentation Tools</option>
                        </select>
                    </div>
                    <div>
                        <label for="date-filter" class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                        <select id="date-filter" name="date_range" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-md">
                            <option value="">All Time</option>
                            <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>This Week</option>
                            <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
                        </select>
                    </div>
                    <div>
                        <label for="sort-filter" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                        <select id="sort-filter" name="sort" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-md">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest First</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            <option value="due_date" {{ request('sort') == 'due_date' ? 'selected' : '' }}>Due Date</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <!-- Borrowings List -->
        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                @if($borrowings->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Details</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrow Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($borrowings as $borrowing)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $borrowing->category }}</div>
                                        <div class="text-sm text-gray-500">{{ implode(', ', $borrowing->items) }}</div>
                                        @if($borrowing->purpose)
                                            <div class="text-xs text-gray-400 mt-1">
                                                <i class="fas fa-info-circle mr-1"></i>{{ $borrowing->purpose }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $borrowing->room }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $borrowing->borrow_date->format('M d, Y') }}
                                        <div class="text-xs text-gray-500">{{ $borrowing->borrow_time }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $borrowing->due_date->format('M d, Y') }}
                                        @if($borrowing->isOverdue())
                                            <div class="text-xs text-red-600 font-medium">Overdue</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
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
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('user.borrowing.show', $borrowing) }}" class="text-red-600 hover:text-red-900 mr-3">
                                            <i class="fas fa-eye mr-1"></i>View
                                        </a>
                                        @if($borrowing->status === 'active' || $borrowing->status === 'overdue')
                                            <button onclick="cancelBorrowing({{ $borrowing->id }})" class="text-gray-600 hover:text-red-600">
                                                <i class="fas fa-times mr-1"></i>Cancel
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <!-- Pagination -->
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $borrowings->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-inbox text-6xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No borrowings found</h3>
                        <p class="text-gray-600 mb-4">Try adjusting your search criteria or create a new borrowing request</p>
                        <a href="{{ route('user.borrowing.create') }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors text-sm font-medium">
                            <i class="fas fa-plus mr-2"></i>New Borrowing Request
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function cancelBorrowing(borrowingId) {
    if (confirm('Are you sure you want to cancel this borrowing request?')) {
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
                location.reload();
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