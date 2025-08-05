@extends('layouts.gsu')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-red-100 p-3 rounded-xl">
                    <i class="fas fa-handshake text-red-800 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Borrowing Requests</h1>
                    <p class="text-gray-600 mt-1">Manage and track all borrowing requests from users</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-clock mr-1"></i>
                    Last updated: {{ now()->format('M d, Y g:i A') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Pending Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-600">Pending Requests</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $borrowings->where('status', 'pending')->count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">Awaiting approval</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approved Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-check text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-600">Approved</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $borrowings->where('status', 'approved')->count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">Currently borrowed</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overdue Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-600">Overdue</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $borrowings->where('status', 'overdue')->count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">Past due date</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Returned Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-undo text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-600">Returned</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $borrowings->where('status', 'returned')->count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">Successfully returned</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-filter text-red-600 mr-2"></i>
                Search & Filter
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search Input -->
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="searchInput" placeholder="Search by borrower name, ID, or asset..." 
                               class="w-full pl-10 pr-4 py-2.5 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="statusFilter" class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="overdue">Overdue</option>
                        <option value="returned">Returned</option>
                    </select>
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select id="categoryFilter" class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Borrowing Requests Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-list text-red-600 mr-2"></i>
                    Borrowing Requests
                </h3>
                <div class="text-sm text-gray-500">
                    <span class="font-medium">{{ $borrowings->total() }}</span> total requests
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-user text-red-600 mr-2"></i>
                                Borrower
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-box text-red-600 mr-2"></i>
                                Asset
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-calendar text-red-600 mr-2"></i>
                                Request Date
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-check text-red-600 mr-2"></i>
                                Due Date
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-red-600 mr-2"></i>
                                Status
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-cogs text-red-600 mr-2"></i>
                                Actions
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($borrowings as $borrowing)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center">
                                            <i class="fas fa-user text-red-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900">{{ $borrowing->borrower_name }}</div>
                                        <div class="text-sm text-gray-500 font-mono">{{ $borrowing->borrower_id_number }}</div>
                                        <div class="text-xs text-gray-400">{{ $borrowing->borrower_email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ $borrowing->asset->name }}</div>
                                <div class="text-sm text-gray-500 font-mono bg-gray-100 px-2 py-1 rounded-md inline-block">{{ $borrowing->asset->asset_code }}</div>
                                <div class="text-xs text-gray-400 mt-1">{{ $borrowing->asset->category->name }}</div>
                                @if($borrowing->location)
                                <div class="text-xs text-blue-600 mt-2 flex items-center">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    {{ $borrowing->location->building }} - Floor {{ $borrowing->location->floor }} - Room {{ $borrowing->location->room }}
                                </div>
                                @elseif($borrowing->custom_location)
                                <div class="text-xs text-orange-600 mt-2 flex items-center">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    {{ $borrowing->custom_location }}
                                    <span class="text-gray-500 ml-1">(Custom)</span>
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $borrowing->request_date->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $borrowing->request_date->format('g:i A') }}</div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $borrowing->due_date->format('M d, Y') }}</div>
                                @if($borrowing->getOverdueText())
                                    <div class="text-xs text-red-600 font-medium mt-1 flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $borrowing->getOverdueText() }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <span class="px-3 py-1.5 inline-flex text-xs leading-4 font-semibold rounded-full {{ $borrowing->getStatusBadgeClass() }}">
                                    <i class="fas fa-circle mr-1.5 text-xs"></i>
                                    {{ ucfirst($borrowing->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('gsu.borrowings.show', $borrowing) }}" 
                                       class="inline-flex items-center justify-center w-9 h-9 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-all duration-200 hover:scale-105"
                                       title="View Details">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    
                                    @if($borrowing->status === 'approved')
                                        <button onclick="openReturnModal({{ $borrowing->id }}, '{{ addslashes($borrowing->asset->asset_code) }}')"
                                                class="inline-flex items-center justify-center w-9 h-9 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition-all duration-200 hover:scale-105"
                                                title="Mark as Returned">
                                            <i class="fas fa-undo text-sm"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="text-center">
                                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-inbox text-3xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No borrowing requests found</h3>
                                    <p class="text-gray-500 max-w-sm mx-auto">No borrowing requests match your current filters. Try adjusting your search criteria or check back later.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($borrowings->hasPages())
    <div class="mt-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing <span class="font-medium">{{ $borrowings->firstItem() }}</span> to <span class="font-medium">{{ $borrowings->lastItem() }}</span> of <span class="font-medium">{{ $borrowings->total() }}</span> results
                </div>
                <div class="flex items-center space-x-2">
                    {{ $borrowings->links() }}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Return Modal -->
<div id="returnModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md relative transform transition-all">
        <button onclick="closeReturnModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-xl transition-colors">
            <i class="fas fa-times"></i>
        </button>
        <div class="flex flex-col items-center text-center">
            <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-undo text-blue-600 text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold mb-3 text-gray-900">Mark Asset as Returned</h3>
            <p class="text-gray-600 mb-8 leading-relaxed">
                Are you sure you want to mark the asset <span id="returnAssetCode" class="font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded-md"></span> as returned? 
                <br><br>
                The asset will be restored to its original location and status will be updated to "Available".
            </p>
            <form id="returnForm" method="POST" class="w-full space-y-3">
                @csrf
                @method('PUT')
                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 flex items-center justify-center gap-3 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <i class="fas fa-undo"></i> 
                    Mark as Returned
                </button>
                <button type="button" onclick="closeReturnModal()" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-xl transition-all duration-200 flex items-center justify-center gap-3">
                    <i class="fas fa-times"></i> 
                    Cancel
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Toast Messages -->
@if(session('success'))
    <div class="fixed top-6 right-6 z-50 bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-4 animate-fade-in min-w-[350px] border border-green-500 backdrop-blur-sm"
         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
            <i class="fas fa-check text-white text-lg"></i>
        </div>
        <div class="flex-1">
            <p class="font-semibold text-white">Success!</p>
            <p class="text-green-100 text-sm">{{ session('success') }}</p>
        </div>
        <button @click="show = false" class="text-green-200 hover:text-white transition-colors">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="fixed top-6 right-6 z-50 bg-gradient-to-r from-red-600 to-red-700 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-4 animate-fade-in min-w-[350px] border border-red-500 backdrop-blur-sm"
         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
        <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
            <i class="fas fa-exclamation-triangle text-white text-lg"></i>
        </div>
        <div class="flex-1">
            <p class="font-semibold text-white">Error!</p>
            <p class="text-red-100 text-sm">{{ session('error') }}</p>
        </div>
        <button @click="show = false" class="text-red-200 hover:text-white transition-colors">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

<style>
@keyframes fade-in { 
    from { 
        opacity: 0; 
        transform: translateY(-20px) scale(0.95); 
    } 
    to { 
        opacity: 1; 
        transform: translateY(0) scale(1); 
    } 
}
.animate-fade-in { 
    animation: fade-in 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
}

/* Custom scrollbar for table */
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

<script>
    function openReturnModal(borrowingId, assetCode) {
        document.getElementById('returnAssetCode').textContent = assetCode;
        document.getElementById('returnForm').action = '/gsu/borrowings/' + borrowingId + '/return';
        document.getElementById('returnModal').style.display = 'flex';
    }

    function closeReturnModal() {
        document.getElementById('returnModal').style.display = 'none';
    }

    window.onclick = function(event) {
        const returnModal = document.getElementById('returnModal');
        if (event.target === returnModal) {
            closeReturnModal();
        }
    }

    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchValue) ? '' : 'none';
        });
    });

    document.getElementById('statusFilter').addEventListener('change', function() {
        const status = this.value;
        const currentUrl = new URL(window.location);
        if (status) {
            currentUrl.searchParams.set('status', status);
        } else {
            currentUrl.searchParams.delete('status');
        }
        window.location.href = currentUrl.toString();
    });

    document.getElementById('categoryFilter').addEventListener('change', function() {
        const categoryId = this.value;
        const currentUrl = new URL(window.location);
        if (categoryId) {
            currentUrl.searchParams.set('category_id', categoryId);
        } else {
            currentUrl.searchParams.delete('category_id');
        }
        window.location.href = currentUrl.toString();
    });
</script>
@endsection
