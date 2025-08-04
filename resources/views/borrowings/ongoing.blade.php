@extends('layouts.superadmin')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <i class="fas fa-clock text-red-800"></i>
                Ongoing Borrowings
            </h1>
            <p class="text-gray-600 mt-1">Manage currently borrowed assets and mark them as returned</p>
        </div>
        <a href="{{ route('borrowings.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center gap-2 shadow-lg">
            <i class="fas fa-arrow-left"></i> Back to All Requests
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Borrowings</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $borrowings->where('status', 'approved')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Overdue</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $borrowings->where('status', 'overdue')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-map-marker-alt text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Active</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $borrowings->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 mb-6">
        <div class="bg-gray-50 p-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="searchInput" placeholder="Search by borrower name, ID, or asset..." 
                               class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>
                <div class="flex gap-2">
                    <select id="statusFilter" class="px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">All Active</option>
                        <option value="approved">Approved</option>
                        <option value="overdue">Overdue</option>
                    </select>
                    <select id="categoryFilter" class="px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Ongoing Borrowings Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <i class="fas fa-user mr-1"></i>Borrower
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <i class="fas fa-box mr-1"></i>Asset
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <i class="fas fa-calendar-check mr-1"></i>Due Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <i class="fas fa-info-circle mr-1"></i>Status
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <i class="fas fa-cogs mr-1"></i>Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($borrowings as $borrowing)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                            <i class="fas fa-user text-red-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $borrowing->borrower_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $borrowing->borrower_id_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $borrowing->asset->name }}</div>
                                <div class="text-sm text-gray-500 font-mono">{{ $borrowing->asset->asset_code }}</div>
                                <div class="text-xs text-gray-400">{{ $borrowing->asset->category->name }}</div>
                                @if($borrowing->location)
                                <div class="text-xs text-blue-600 mt-1">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    {{ $borrowing->location->building }} - Floor {{ $borrowing->location->floor }} - Room {{ $borrowing->location->room }}
                                </div>
                                @elseif($borrowing->custom_location)
                                <div class="text-xs text-orange-600 mt-1">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    {{ $borrowing->custom_location }} <span class="text-gray-500">(Custom)</span>
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $borrowing->due_date->format('M d, Y') }}</div>
                                @if($borrowing->getOverdueText())
                                    <div class="text-xs text-red-600 font-medium">{{ $borrowing->getOverdueText() }}</div>
                                @elseif($borrowing->getDueInText())
                                    <div class="text-xs text-gray-500">{{ $borrowing->getDueInText() }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full {{ $borrowing->getStatusBadgeClass() }}">
                                    {{ ucfirst($borrowing->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('borrowings.show', $borrowing) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors duration-150"
                                       title="View Details">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    
                                    <button onclick="openReturnModal({{ $borrowing->id }}, '{{ addslashes($borrowing->asset->asset_code) }}')"
                                            class="inline-flex items-center justify-center w-8 h-8 bg-green-100 text-green-600 rounded-full hover:bg-green-200 transition-colors duration-150"
                                            title="Mark as Returned">
                                        <i class="fas fa-undo text-xs"></i>
                                    </button>
                                    
                                    <button onclick="openDeleteModal({{ $borrowing->id }}, '{{ addslashes($borrowing->asset->asset_code) }}')"
                                            class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 text-gray-600 rounded-full hover:bg-gray-200 transition-colors duration-150"
                                            title="Delete Request">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-4"></i>
                                    <div class="text-lg font-medium text-gray-600">No ongoing borrowings found</div>
                                    <div class="text-sm text-gray-500 mt-1">All assets have been returned or no active borrowings exist</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $borrowings->links() }}
    </div>
</div>

<!-- Return Modal -->
<div id="returnModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" style="display: none;">
    <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-md relative">
        <button onclick="closeReturnModal()" class="absolute top-3 right-3 text-gray-400 hover:text-green-800 text-xl">
            <i class="fas fa-times"></i>
        </button>
        <div class="flex flex-col items-center">
            <div class="bg-green-100 text-green-800 rounded-full p-4 mb-4">
                <i class="fas fa-undo text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold mb-2 text-gray-800">Mark Asset as Returned</h3>
            <p class="text-gray-600 mb-6 text-center">Are you sure you want to mark the asset <span id="returnAssetCode" class="font-semibold text-green-800"></span> as returned? The asset will be restored to its original location.</p>
            <form id="returnForm" method="POST" class="w-full flex flex-col items-center gap-3">
                @csrf
                @method('PUT')
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-undo"></i> Mark as Returned
                </button>
                <button type="button" onclick="closeReturnModal()" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" style="display: none;">
    <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-md relative">
        <button onclick="closeDeleteModal()" class="absolute top-3 right-3 text-gray-400 hover:text-red-800 text-xl">
            <i class="fas fa-times"></i>
        </button>
        <div class="flex flex-col items-center">
            <div class="bg-red-100 text-red-800 rounded-full p-4 mb-4">
                <i class="fas fa-exclamation-triangle text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold mb-2 text-gray-800">Delete Borrowing Request</h3>
            <p class="text-gray-600 mb-6 text-center">Are you sure you want to delete the borrowing request for <span id="deleteAssetCode" class="font-semibold text-red-800"></span>? This action cannot be undone.</p>
            <form id="deleteForm" method="POST" class="w-full flex flex-col items-center gap-3">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full bg-red-800 hover:bg-red-900 text-white font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-trash-alt"></i> Delete Request
                </button>
                <button type="button" onclick="closeDeleteModal()" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Toast Messages -->
@if(session('success'))
    <div class="fixed top-6 right-6 z-50 bg-green-900 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-4 animate-fade-in min-w-[300px] border border-green-700"
         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
        <i class="fas fa-check-circle text-2xl text-green-300"></i>
        <span class="font-semibold">{{ session('success') }}</span>
        <button @click="show = false" class="ml-auto text-green-200 hover:text-white"><i class="fas fa-times"></i></button>
    </div>
@endif
@if(session('error'))
    <div class="fixed top-6 right-6 z-50 bg-red-900 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-4 animate-fade-in min-w-[300px] border border-red-700"
         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
        <i class="fas fa-times-circle text-2xl text-red-300"></i>
        <span class="font-semibold">{{ session('error') }}</span>
        <button @click="show = false" class="ml-auto text-red-200 hover:text-white"><i class="fas fa-times"></i></button>
    </div>
@endif

<style>
@keyframes fade-in { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: none; } }
.animate-fade-in { animation: fade-in 0.5s; }
</style>

<script>
    // Modal functions
    function openReturnModal(borrowingId, assetCode) {
        document.getElementById('returnAssetCode').textContent = assetCode;
        document.getElementById('returnForm').action = '/borrowings/' + borrowingId + '/return';
        document.getElementById('returnModal').style.display = 'flex';
    }

    function closeReturnModal() {
        document.getElementById('returnModal').style.display = 'none';
    }

    function openDeleteModal(borrowingId, assetCode) {
        document.getElementById('deleteAssetCode').textContent = assetCode;
        document.getElementById('deleteForm').action = '/borrowings/' + borrowingId;
        document.getElementById('deleteModal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        const returnModal = document.getElementById('returnModal');
        const deleteModal = document.getElementById('deleteModal');
        
        if (event.target === returnModal) {
            closeReturnModal();
        }
        if (event.target === deleteModal) {
            closeDeleteModal();
        }
    }

    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchValue) ? '' : 'none';
        });
    });

    // Status filter
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

    // Category filter
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