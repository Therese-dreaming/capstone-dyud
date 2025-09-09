@extends('layouts.gsu')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <i class="fas fa-search text-red-800"></i>
                Lost Assets
            </h1>
            <p class="text-gray-600 mt-1">Track and manage missing assets</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-search text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Under Investigation</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $lostAssets->where('status', 'investigating')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Found</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $lostAssets->where('status', 'found')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-times text-red-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Permanently Lost</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $lostAssets->where('status', 'permanently_lost')->count() }}</p>
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
                        <input type="text" id="searchInput" placeholder="Search by asset name, code, or reporter..." 
                               class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>
                <div class="flex gap-2">
                    <select id="statusFilter" class="px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">All Status</option>
                        <option value="investigating">Under Investigation</option>
                        <option value="found">Found</option>
                        <option value="permanently_lost">Permanently Lost</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Lost Assets Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <i class="fas fa-box mr-1"></i>Asset
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <i class="fas fa-user mr-1"></i>Reported By
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <i class="fas fa-calendar mr-1"></i>Last Seen
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <i class="fas fa-map-marker-alt mr-1"></i>Last Known Location
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
                    @forelse($lostAssets as $lostAsset)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                            <i class="fas fa-box text-red-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $lostAsset->asset->name }}</div>
                                        <div class="text-sm text-gray-500 font-mono">{{ $lostAsset->asset->asset_code }}</div>
                                        <div class="text-xs text-gray-400">{{ $lostAsset->asset->category->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $lostAsset->reportedBy->name }}</div>
                                <div class="text-sm text-gray-500">{{ $lostAsset->reported_date->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $lostAsset->last_seen_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($lostAsset->last_known_location)
                                        {{ $lostAsset->last_known_location }}
                                    @else
                                        <span class="text-gray-400">Not specified</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full {{ $lostAsset->getStatusBadgeClass() }}">
                                    {{ $lostAsset->getStatusLabel() }}
                                </span>
                                @if($lostAsset->isFound())
                                    <div class="text-xs text-green-600 mt-1">Found on {{ $lostAsset->found_date->format('M d, Y') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('lost-assets.show', $lostAsset) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors duration-150"
                                       title="View Details">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    
                                    @if($lostAsset->isInvestigating())
                                        <button onclick="openUpdateStatusModal({{ $lostAsset->id }}, '{{ addslashes($lostAsset->asset->asset_code) }}')"
                                                class="inline-flex items-center justify-center w-8 h-8 bg-green-100 text-green-600 rounded-full hover:bg-green-200 transition-colors duration-150"
                                                title="Update Status">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                    @endif
                                    
                                    <button onclick="openDeleteModal({{ $lostAsset->id }}, '{{ addslashes($lostAsset->asset->asset_code) }}')"
                                            class="inline-flex items-center justify-center w-8 h-8 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-150"
                                            title="Delete Record">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-search text-4xl mb-4"></i>
                                    <div class="text-lg font-medium text-gray-600">No lost assets found</div>
                                    <div class="text-sm text-gray-500 mt-1">No assets have been reported as lost</div>
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
        {{ $lostAssets->links() }}
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
            <p class="text-gray-600 mb-6 text-center">Update the status for asset <span id="updateStatusAssetCode" class="font-semibold text-green-800"></span></p>
            <form id="updateStatusForm" method="POST" class="w-full flex flex-col items-center gap-3">
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
            <p class="text-gray-600 mb-6 text-center">Are you sure you want to delete the lost asset record for <span id="deleteAssetCode" class="font-semibold text-red-800"></span>? This action cannot be undone.</p>
            <form id="deleteForm" method="POST" class="w-full flex flex-col items-center gap-3">
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
    function openUpdateStatusModal(lostAssetId, assetCode) {
        document.getElementById('updateStatusAssetCode').textContent = assetCode;
        document.getElementById('updateStatusForm').action = '/lost-assets/' + lostAssetId + '/status';
        document.getElementById('updateStatusModal').style.display = 'flex';
    }

    function closeUpdateStatusModal() {
        document.getElementById('updateStatusModal').style.display = 'none';
        document.getElementById('status').value = '';
        document.getElementById('foundFields').style.display = 'none';
    }

    function openDeleteModal(lostAssetId, assetCode) {
        document.getElementById('deleteAssetCode').textContent = assetCode;
        document.getElementById('deleteForm').action = '/lost-assets/' + lostAssetId;
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
</script>
@endsection
