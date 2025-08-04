@extends('layouts.superadmin')

@section('content')
<div class="container mx-auto py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                    <i class="fas fa-search text-red-800"></i>
                    Lost Asset Details
                </h1>
                <p class="text-gray-600 mt-1">Detailed information about the lost asset</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('lost-assets.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
                @if($lostAsset->isInvestigating())
                <button onclick="openUpdateStatusModal()" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                    <i class="fas fa-edit"></i> Update Status
                </button>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Asset Information -->
                <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Asset Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Asset Name</label>
                            <p class="mt-1 text-sm text-gray-900 font-medium">{{ $lostAsset->asset->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Asset Code</label>
                            <p class="mt-1 text-sm text-gray-900 font-mono">{{ $lostAsset->asset->asset_code }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Category</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $lostAsset->asset->category->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Condition</label>
                            <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                                {{ $lostAsset->asset->condition === 'Good' ? 'bg-green-100 text-green-800' : 
                                   ($lostAsset->asset->condition === 'Fair' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }} mt-1">
                                {{ $lostAsset->asset->condition }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Current Status</label>
                            <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                                {{ $lostAsset->asset->status === 'Available' ? 'bg-green-100 text-green-800' : 
                                   ($lostAsset->asset->status === 'In Use' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }} mt-1">
                                {{ $lostAsset->asset->status }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Original Location</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $lostAsset->asset->location->building }} - Floor {{ $lostAsset->asset->location->floor }} - Room {{ $lostAsset->asset->location->room }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Loss Report Information -->
                <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Loss Report Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Reported By</label>
                            <p class="mt-1 text-sm text-gray-900 font-medium">{{ $lostAsset->reportedBy->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Reported Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $lostAsset->reported_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last Seen Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $lostAsset->last_seen_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last Known Location</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if($lostAsset->last_known_location)
                                    {{ $lostAsset->last_known_location }}
                                @else
                                    <span class="text-gray-400">Not specified</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description of Loss</label>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-900">{{ $lostAsset->description }}</p>
                        </div>
                    </div>
                    
                    @if($lostAsset->investigation_notes)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Investigation Notes</label>
                        <div class="bg-blue-50 rounded-lg p-4">
                            <p class="text-sm text-gray-900">{{ $lostAsset->investigation_notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                @if($lostAsset->isFound())
                <!-- Found Information -->
                <div class="bg-green-50 rounded-lg p-6 border border-green-200">
                    <h3 class="text-lg font-semibold text-green-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-600"></i>
                        Found Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-green-700">Found Date</label>
                            <p class="mt-1 text-sm text-green-900 font-medium">{{ $lostAsset->found_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-green-700">Found Location</label>
                            <p class="mt-1 text-sm text-green-900">{{ $lostAsset->found_location }}</p>
                        </div>
                    </div>
                    @if($lostAsset->found_notes)
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-green-700 mb-2">Found Notes</label>
                        <div class="bg-white rounded-lg p-3">
                            <p class="text-sm text-green-900">{{ $lostAsset->found_notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Information -->
                <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Information</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Current Status</label>
                            <span class="px-3 py-2 inline-flex text-sm font-semibold rounded-full {{ $lostAsset->getStatusBadgeClass() }} mt-1">
                                {{ $lostAsset->getStatusLabel() }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Days Since Reported</label>
                            <p class="mt-1 text-sm text-gray-900 font-medium">{{ $lostAsset->reported_date->diffInDays(now()) }} days</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Days Since Last Seen</label>
                            <p class="mt-1 text-sm text-gray-900 font-medium">{{ $lostAsset->last_seen_date->diffInDays(now()) }} days</p>
                        </div>
                    </div>
                </div>

                @if($lostAsset->lastBorrower)
                <!-- Last Borrower Information -->
                <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Last Borrower</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Borrower Name</label>
                            <p class="mt-1 text-sm text-gray-900 font-medium">{{ $lostAsset->lastBorrower->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Borrower ID</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $lostAsset->lastBorrower->id_number }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Contact</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $lostAsset->lastBorrower->email }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                    <div class="space-y-3">
                        @if($lostAsset->isInvestigating())
                        <button onclick="openUpdateStatusModal()" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                            <i class="fas fa-edit"></i> Update Status
                        </button>
                        @endif
                        
                        <button onclick="openDeleteModal()" 
                                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                            <i class="fas fa-trash"></i> Delete Record
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div id="updateStatusModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" style="display: none;">
    <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-md relative">
        <button onclick="closeUpdateStatusModal()" class="absolute top-3 right-3 text-gray-400 hover:text-green-800 text-xl">
            <i class="fas fa-times"></i>
        </button>
        <div class="flex flex-col items-center">
            <div class="bg-green-100 text-green-800 rounded-full p-4 mb-4">
                <i class="fas fa-edit text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold mb-2 text-gray-800">Update Lost Asset Status</h3>
            <p class="text-gray-600 mb-6 text-center">Update the status for asset <span class="font-semibold text-green-800">{{ $lostAsset->asset->asset_code }}</span></p>
            <form action="{{ route('lost-assets.update-status', $lostAsset) }}" method="POST" class="w-full flex flex-col items-center gap-3">
                @csrf
                @method('PUT')
                <div class="w-full mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">New Status</label>
                    <select name="status" id="status" required onchange="toggleFoundFields()"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500">
                        <option value="">Select Status</option>
                        <option value="found">Found</option>
                        <option value="permanently_lost">Permanently Lost</option>
                    </select>
                </div>
                
                <div id="foundFields" class="w-full mb-4" style="display: none;">
                    <div class="mb-3">
                        <label for="found_date" class="block text-sm font-medium text-gray-700 mb-2">Found Date</label>
                        <input type="date" name="found_date" id="found_date" 
                               value="{{ now()->format('Y-m-d') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500">
                    </div>
                    <div class="mb-3">
                        <label for="found_location" class="block text-sm font-medium text-gray-700 mb-2">Found Location</label>
                        <input type="text" name="found_location" id="found_location" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500"
                               placeholder="Where was the asset found?">
                    </div>
                    <div>
                        <label for="found_notes" class="block text-sm font-medium text-gray-700 mb-2">Found Notes</label>
                        <textarea name="found_notes" id="found_notes" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500"
                                  placeholder="Additional notes about finding the asset..."></textarea>
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Update Status
                </button>
                <button type="button" onclick="closeUpdateStatusModal()" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
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
            <h3 class="text-xl font-bold mb-2 text-gray-800">Delete Lost Asset Record</h3>
            <p class="text-gray-600 mb-6 text-center">Are you sure you want to delete the lost asset record for <span class="font-semibold text-red-800">{{ $lostAsset->asset->asset_code }}</span>? This action cannot be undone.</p>
            <form action="{{ route('lost-assets.destroy', $lostAsset) }}" method="POST" class="w-full flex flex-col items-center gap-3">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full bg-red-800 hover:bg-red-900 text-white font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-trash-alt"></i> Delete Record
                </button>
                <button type="button" onclick="closeDeleteModal()" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // Modal functions
    function openUpdateStatusModal() {
        document.getElementById('updateStatusModal').style.display = 'flex';
    }

    function closeUpdateStatusModal() {
        document.getElementById('updateStatusModal').style.display = 'none';
        document.getElementById('status').value = '';
        document.getElementById('foundFields').style.display = 'none';
    }

    function openDeleteModal() {
        document.getElementById('deleteModal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }

    function toggleFoundFields() {
        const status = document.getElementById('status').value;
        const foundFields = document.getElementById('foundFields');
        const foundDate = document.getElementById('found_date');
        const foundLocation = document.getElementById('found_location');
        const foundNotes = document.getElementById('found_notes');

        if (status === 'found') {
            foundFields.style.display = 'block';
            foundDate.required = true;
            foundLocation.required = true;
        } else {
            foundFields.style.display = 'none';
            foundDate.required = false;
            foundLocation.required = false;
        }
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        const updateStatusModal = document.getElementById('updateStatusModal');
        const deleteModal = document.getElementById('deleteModal');
        
        if (event.target === updateStatusModal) {
            closeUpdateStatusModal();
        }
        if (event.target === deleteModal) {
            closeDeleteModal();
        }
    }
</script>
@endsection 