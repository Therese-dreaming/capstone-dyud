@extends('layouts.superadmin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-red-50" x-data="{ 
    showToast: {{ session('success') || session('error') || $errors->any() ? 'true' : 'false' }},
    activeTab: 'current',
    showFilters: false
}">
    <!-- Page Header -->
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="bg-gradient-to-r from-red-600 to-red-800 text-white p-3 rounded-xl shadow-lg">
                        <i class="fas fa-exchange-alt text-xl"></i>
                    </div>
                    Asset Borrowing
                </h1>
                <p class="text-gray-600 mt-2 text-sm md:text-base">Manage asset borrowing and returns</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                <a href="{{ route('borrowing.create') }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors text-sm font-medium flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    New Borrowing
                </a>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8 overflow-hidden">
            <div class="flex border-b border-gray-200">
                <button @click="activeTab = 'current'" 
                        :class="activeTab === 'current' ? 'bg-red-50 text-red-700 border-b-2 border-red-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
                        class="flex-1 px-6 py-4 text-sm font-medium transition-all duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-clock"></i>
                    Current Borrowings
                </button>
                <button @click="activeTab = 'history'" 
                        :class="activeTab === 'history' ? 'bg-red-50 text-red-700 border-b-2 border-red-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
                        class="flex-1 px-6 py-4 text-sm font-medium transition-all duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-history"></i>
                    Borrowing History
                </button>
                <button @click="activeTab = 'overdue'" 
                        :class="activeTab === 'overdue' ? 'bg-red-50 text-red-700 border-b-2 border-red-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
                        class="flex-1 px-6 py-4 text-sm font-medium transition-all duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    Overdue Items
                </button>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <form action="{{ route('borrowing.index') }}" method="GET" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
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
                        <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="status-filter" name="status" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-md">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
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
                </div>
            </form>
        </div>

        <!-- Current Borrowings Tab -->
        <div x-show="activeTab === 'current'" x-transition>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Asset
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Borrower
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Borrowed Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Due Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($borrowings->where('status', 'active') as $borrowing)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                                <i class="fa fa-box text-white"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $borrowing->category }}</div>
                                            <div class="text-sm text-gray-500 font-mono">{{ implode(', ', $borrowing->items) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $borrowing->borrower_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $borrowing->borrower_id_number }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $borrowing->borrow_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $borrowing->due_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <form action="{{ route('borrowing.return', $borrowing) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="text-green-600 hover:text-green-900 bg-green-100 hover:bg-green-200 p-1.5 rounded-lg transition-colors">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('borrowing.show', $borrowing) }}" class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 p-1.5 rounded-lg transition-colors">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('borrowing.cancel', $borrowing) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 p-1.5 rounded-lg transition-colors">
                                                <i class="fas fa-times-circle"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    No active borrowings found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        Showing {{ $borrowings->where('status', 'active')->count() }} active borrowings
                    </div>
                    <div>
                        {{ $borrowings->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- History Tab -->
        <div x-show="activeTab === 'history'" x-transition>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Asset
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Borrower
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Borrowed Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Return Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($borrowings->where('status', 'returned') as $borrowing)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-lg bg-gradient-to-r from-green-500 to-green-600 flex items-center justify-center">
                                                <i class="fa fa-box text-white"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $borrowing->category }}</div>
                                            <div class="text-sm text-gray-500 font-mono">{{ implode(', ', $borrowing->items) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $borrowing->borrower_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $borrowing->borrower_id_number }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $borrowing->borrow_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $borrowing->return_date ? $borrowing->return_date->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Returned
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <a href="{{ route('borrowing.show', $borrowing) }}" class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 p-1.5 rounded-lg transition-colors">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    No borrowing history found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        Showing {{ $borrowings->where('status', 'returned')->count() }} returned items
                    </div>
                    <div>
                        {{ $borrowings->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Overdue Tab -->
        <div x-show="activeTab === 'overdue'" x-transition>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Asset
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Borrower
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Borrowed Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Due Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Days Overdue
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($borrowings->where('status', 'overdue') as $borrowing)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-lg bg-gradient-to-r from-red-500 to-red-600 flex items-center justify-center">
                                                <i class="fa fa-box text-white"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $borrowing->category }}</div>
                                            <div class="text-sm text-gray-500 font-mono">{{ implode(', ', $borrowing->items) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $borrowing->borrower_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $borrowing->borrower_id_number }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $borrowing->borrow_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $borrowing->due_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        {{ now()->diffInDays($borrowing->due_date) }} days
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <form action="{{ route('borrowing.return', $borrowing) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="text-green-600 hover:text-green-900 bg-green-100 hover:bg-green-200 p-1.5 rounded-lg transition-colors">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('borrowing.show', $borrowing) }}" class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 p-1.5 rounded-lg transition-colors">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 p-1.5 rounded-lg transition-colors">
                                            <i class="fas fa-bell"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    No overdue items found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        Showing {{ $borrowings->where('status', 'overdue')->count() }} overdue items
                    </div>
                    <div>
                        {{ $borrowings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div x-show="showToast" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-90"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-90"
     @click="showToast = false"
     class="fixed bottom-4 right-4 z-50">
    <div class="bg-white rounded-lg border-l-4 border-green-500 shadow-lg p-4 flex items-center space-x-4 max-w-md">
        <div class="flex-shrink-0">
            <i class="fas fa-check-circle text-green-500 text-xl"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900">
                {{ session('success') ?? session('error') ?? 'Operation completed successfully' }}
            </p>
        </div>
        <div class="flex-shrink-0">
            <button @click="showToast = false" class="inline-flex text-gray-400 hover:text-gray-500">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Borrowing page loaded successfully');
    
    // Set default date values for the form
    const today = new Date().toISOString().split('T')[0];
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const tomorrowStr = tomorrow.toISOString().split('T')[0];
    
    // Function to set default values
    function setDefaultValues() {
        const form = document.querySelector('form[action*="borrowing"]');
        if (form) {
            const dateInput = form.querySelector('input[name="date"]');
            const dueDateInput = form.querySelector('input[name="due_date"]');
            const timeInput = form.querySelector('input[name="time"]');
            
            if (dateInput && !dateInput.value) {
                dateInput.value = today;
                console.log('Set default borrow date:', today);
            }
            if (dueDateInput && !dueDateInput.value) {
                dueDateInput.value = tomorrowStr;
                console.log('Set default due date:', tomorrowStr);
            }
            if (timeInput && !timeInput.value) {
                timeInput.value = new Date().toTimeString().slice(0, 5);
                console.log('Set default time:', timeInput.value);
            }
        }
    }
    
    // Set default values when form is shown
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'x-show') {
                setTimeout(setDefaultValues, 100); // Small delay to ensure Alpine.js has updated
            }
        });
    });
    
    // Observe the modal for changes
    const modal = document.querySelector('[x-show="showBorrowingForm"]');
    if (modal) {
        observer.observe(modal, { attributes: true });
        console.log('Observer set up for modal');
    }
    
    // Form validation and submission
    const borrowingForm = document.querySelector('form[action*="borrowing"]');
    if (borrowingForm) {
        borrowingForm.addEventListener('submit', function(e) {
            console.log('Form submission attempted');
            
            // Check if category is selected
            const categoryInput = this.querySelector('input[name="category"]');
            if (!categoryInput || !categoryInput.value) {
                e.preventDefault();
                alert('Please select a category first.');
                console.log('Category validation failed');
                return false;
            }
            
            // Check if items are selected
            const itemsSelect = this.querySelector('select[name="items[]"]');
            if (itemsSelect && itemsSelect.selectedOptions.length === 0) {
                e.preventDefault();
                alert('Please select at least one item to borrow.');
                console.log('Items validation failed');
                return false;
            }
            
            // Check dates
            const dueDate = this.querySelector('input[name="due_date"]').value;
            const borrowDate = this.querySelector('input[name="date"]').value;
            
            if (dueDate <= borrowDate) {
                e.preventDefault();
                alert('Due date must be after the borrow date.');
                console.log('Date validation failed');
                return false;
            }
            
            console.log('Form validation passed, submitting...');
            console.log('Form data:', {
                category: categoryInput.value,
                items: Array.from(itemsSelect.selectedOptions).map(opt => opt.value),
                borrowDate: borrowDate,
                dueDate: dueDate
            });
        });
    }
    
    // Debug logging
    console.log('Available routes:', {
        index: '{{ route("borrowing.index") }}',
        store: '{{ route("borrowing.store") }}',
        show: '{{ route("borrowing.show", 1) }}'.replace('/1', '/{id}'),
        return: '{{ route("borrowing.return", 1) }}'.replace('/1', '/{id}'),
        cancel: '{{ route("borrowing.cancel", 1) }}'.replace('/1', '/{id}')
    });
    
    // Test Alpine.js data
    setTimeout(() => {
        if (window.Alpine) {
            console.log('Alpine.js is loaded');
        } else {
            console.log('Alpine.js not detected');
        }
    }, 1000);
    
    // Restore selected items if there are validation errors
    const oldItems = @json(old('items', []));
    if (oldItems.length > 0) {
        setTimeout(() => {
            const itemsSelect = document.querySelector('select[name="items[]"]');
            if (itemsSelect) {
                oldItems.forEach(item => {
                    const option = itemsSelect.querySelector(`option[value="${item}"]`);
                    if (option) {
                        option.selected = true;
                    }
                });
            }
        }, 500);
    }
});
</script>
@endsection