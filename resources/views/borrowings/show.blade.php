@extends('layouts.superadmin')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <i class="fas fa-handshake text-red-800"></i>
                Borrowing Request Details
            </h1>
            <p class="text-gray-600 mt-1">View detailed information about this borrowing request</p>
        </div>
        <a href="{{ route('borrowings.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors text-sm font-medium flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            Back to Borrowings
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Borrowing Details Card -->
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Borrowing Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full {{ $borrowing->getStatusBadgeClass() }} mt-1">
                            {{ ucfirst($borrowing->status) }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Request Date</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $borrowing->request_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Due Date</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $borrowing->due_date->format('M d, Y') }}</p>
                    </div>
                    @if($borrowing->return_date)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Return Date</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $borrowing->return_date->format('M d, Y') }}</p>
                    </div>
                    @endif
                    @if($borrowing->approved_at)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Approved At</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $borrowing->approved_at->format('M d, Y H:i') }}</p>
                    </div>
                    @endif
                    @if($borrowing->approvedBy)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Approved By</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $borrowing->approvedBy->name }}</p>
                    </div>
                    @endif
                </div>
                
                @if($borrowing->location)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">Usage Location</label>
                    <p class="mt-1 text-sm text-gray-900 font-medium">
                        <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                        {{ $borrowing->location->building }} - Floor {{ $borrowing->location->floor }} - Room {{ $borrowing->location->room }}
                    </p>
                </div>
                @elseif($borrowing->custom_location)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">Usage Location</label>
                    <p class="mt-1 text-sm text-gray-900 font-medium">
                        <i class="fas fa-map-marker-alt text-orange-600 mr-2"></i>
                        {{ $borrowing->custom_location }} <span class="text-gray-500">(Custom)</span>
                    </p>
                </div>
                @endif
                
                @if($borrowing->purpose)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">Purpose of Borrowing</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $borrowing->purpose }}</p>
                </div>
                @endif
                
                @if($borrowing->notes)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $borrowing->notes }}</p>
                </div>
                @endif
            </div>

            <!-- Asset Information -->
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Asset Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Asset Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $borrowing->asset->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Asset Code</label>
                        <p class="mt-1 text-sm text-gray-900 font-mono">{{ $borrowing->asset->asset_code }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Category</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $borrowing->asset->category->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Current Status</label>
                        <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                            {{ $borrowing->asset->status === 'Available' ? 'bg-green-100 text-green-800' : 
                               ($borrowing->asset->status === 'In Use' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }} mt-1">
                            {{ $borrowing->asset->status }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Condition</label>
                        <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                            {{ $borrowing->asset->condition === 'Good' ? 'bg-green-100 text-green-800' : 
                               ($borrowing->asset->condition === 'Fair' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }} mt-1">
                            {{ $borrowing->asset->condition }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Location</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $borrowing->asset->location->building }} - Floor {{ $borrowing->asset->location->floor }} - Room {{ $borrowing->asset->location->room }}
                        </p>
                    </div>
                </div>
                
                @if($borrowing->asset->description)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $borrowing->asset->description }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Borrower Information -->
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Borrower Information</h3>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12">
                            <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center">
                                <i class="fas fa-user text-red-600 text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $borrowing->borrower_name }}</div>
                            <div class="text-sm text-gray-500">{{ $borrowing->borrower_id_number }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                <div class="space-y-3">
                    @if($borrowing->status === 'pending')
                        <button onclick="openApproveModal({{ $borrowing->id }}, '{{ addslashes($borrowing->asset->asset_code) }}')"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                            <i class="fas fa-check"></i> Approve Request
                        </button>
                        
                        <button onclick="openRejectModal({{ $borrowing->id }})"
                                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                            <i class="fas fa-times"></i> Reject Request
                        </button>
                    @endif
                    
                    @if($borrowing->status === 'approved')
                        <button onclick="openReturnModal({{ $borrowing->id }}, '{{ addslashes($borrowing->asset->asset_code) }}')"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                            <i class="fas fa-undo"></i> Mark as Returned
                        </button>
                    @endif
                    
                    <button onclick="openDeleteModal({{ $borrowing->id }}, '{{ addslashes($borrowing->asset->asset_code) }}')"
                            class="w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-trash"></i> Delete Request
                    </button>
                </div>
            </div>

            <!-- Overdue Warning -->
            @if($borrowing->isOverdue())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Overdue Item</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>This item is {{ $borrowing->getOverdueText() }}.</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" style="display: none;">
    <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-md relative">
        <button onclick="closeApproveModal()" class="absolute top-3 right-3 text-gray-400 hover:text-green-800 text-xl">
            <i class="fas fa-times"></i>
        </button>
        <div class="flex flex-col items-center">
            <div class="bg-green-100 text-green-800 rounded-full p-4 mb-4">
                <i class="fas fa-check text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold mb-2 text-gray-800">Approve Borrowing Request</h3>
            <p class="text-gray-600 mb-6 text-center">Are you sure you want to approve the borrowing request for <span id="approveAssetCode" class="font-semibold text-green-800"></span>?</p>
            <form id="approveForm" method="POST" class="w-full flex flex-col items-center gap-3">
                @csrf
                @method('PUT')
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-check"></i> Approve Request
                </button>
                <button type="button" onclick="closeApproveModal()" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" style="display: none;">
    <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-md relative">
        <button onclick="closeRejectModal()" class="absolute top-3 right-3 text-gray-400 hover:text-red-800 text-xl">
            <i class="fas fa-times"></i>
        </button>
        <div class="flex flex-col items-center">
            <div class="bg-red-100 text-red-800 rounded-full p-4 mb-4">
                <i class="fas fa-times text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold mb-2 text-gray-800">Reject Borrowing Request</h3>
            <p class="text-gray-600 mb-6 text-center">Please provide a reason for rejecting this request.</p>
            <form id="rejectForm" method="POST" class="w-full flex flex-col items-center gap-3">
                @csrf
                @method('PUT')
                <div class="w-full mb-4">
                    <label for="rejectNotes" class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason</label>
                    <textarea name="notes" id="rejectNotes" rows="3" required
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"
                              placeholder="Please provide a reason for rejecting this request..."></textarea>
                </div>
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> Reject Request
                </button>
                <button type="button" onclick="closeRejectModal()" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Return Modal -->
<div id="returnModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" style="display: none;">
    <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-md relative">
        <button onclick="closeReturnModal()" class="absolute top-3 right-3 text-gray-400 hover:text-blue-800 text-xl">
            <i class="fas fa-times"></i>
        </button>
        <div class="flex flex-col items-center">
            <div class="bg-blue-100 text-blue-800 rounded-full p-4 mb-4">
                <i class="fas fa-undo text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold mb-2 text-gray-800">Mark Asset as Returned</h3>
            <p class="text-gray-600 mb-6 text-center">Are you sure you want to mark the asset <span id="returnAssetCode" class="font-semibold text-blue-800"></span> as returned? The asset will be restored to its original location.</p>
            <form id="returnForm" method="POST" class="w-full flex flex-col items-center gap-3">
                @csrf
                @method('PUT')
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
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
    function openApproveModal(borrowingId, assetCode) {
        document.getElementById('approveAssetCode').textContent = assetCode;
        document.getElementById('approveForm').action = '/borrowings/' + borrowingId + '/approve';
        document.getElementById('approveModal').style.display = 'flex';
    }

    function closeApproveModal() {
        document.getElementById('approveModal').style.display = 'none';
    }

    function openRejectModal(borrowingId) {
        document.getElementById('rejectForm').action = '/borrowings/' + borrowingId + '/reject';
        document.getElementById('rejectModal').style.display = 'flex';
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
        document.getElementById('rejectNotes').value = '';
    }

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
        const approveModal = document.getElementById('approveModal');
        const rejectModal = document.getElementById('rejectModal');
        const returnModal = document.getElementById('returnModal');
        const deleteModal = document.getElementById('deleteModal');
        
        if (event.target === approveModal) {
            closeApproveModal();
        }
        if (event.target === rejectModal) {
            closeRejectModal();
        }
        if (event.target === returnModal) {
            closeReturnModal();
        }
        if (event.target === deleteModal) {
            closeDeleteModal();
        }
    }
</script>
@endsection 