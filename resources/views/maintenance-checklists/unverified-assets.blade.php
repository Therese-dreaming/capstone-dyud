@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-orange-600 to-orange-700 rounded-full mb-4">
                <i class="fas fa-question-circle text-white text-2xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Unverified Assets</h1>
            <p class="text-lg text-gray-600">Assets that require admin confirmation after maintenance checklist completion</p>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-question-circle text-orange-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Unverified</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $unverifiedAssets->total() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Require Action</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $unverifiedAssets->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Status</p>
                        <p class="text-lg font-bold text-gray-900">Pending Review</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unverified Assets Table -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-orange-600 to-orange-700">
                <h2 class="text-xl font-bold text-white">Assets Requiring Verification</h2>
                <p class="text-orange-100">Review and confirm the status of assets that were not scanned during maintenance</p>
            </div>

            @if($unverifiedAssets->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($unverifiedAssets as $asset)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center">
                                                    <i class="fas fa-cube text-orange-600"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $asset->asset_code }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $asset->name }}</div>
                                        @if($asset->description)
                                            <div class="text-sm text-gray-500">{{ Str::limit($asset->description, 50) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $asset->category->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $asset->location->building ?? 'N/A' }}
                                        </div>
                                        @if($asset->location)
                                            <div class="text-sm text-gray-500">
                                                Floor {{ $asset->location->floor }} - Room {{ $asset->location->room }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $asset->updated_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $asset->getStatusBadgeClass() }}">
                                            {{ $asset->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <!-- Mark as Found Button -->
                                            <button onclick="openFoundModal({{ $asset->id }}, '{{ $asset->asset_code }}', '{{ $asset->name }}')"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                                <i class="fas fa-check mr-1"></i>
                                                Found
                                            </button>

                                            <!-- Confirm as Lost Button -->
                                            <button onclick="openLostModal({{ $asset->id }}, '{{ $asset->asset_code }}', '{{ $asset->name }}', '{{ $asset->location ? $asset->location->building . ' - Floor ' . $asset->location->floor . ', Room ' . $asset->location->room : 'Unknown Location' }}')"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                                                <i class="fas fa-times mr-1"></i>
                                                Lost
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $unverifiedAssets->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="mx-auto h-24 w-24 text-gray-400">
                        <i class="fas fa-check-circle text-6xl"></i>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No Unverified Assets</h3>
                    <p class="mt-2 text-gray-500">All assets have been verified or there are no unverified assets at this time.</p>
                    <div class="mt-6">
                        <a href="{{ route('maintenance-checklists.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Maintenance Checklists
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Information Card -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">About Unverified Assets</h3>
                    <div class="text-blue-800 text-sm space-y-2">
                        <p>Assets marked as "Unverified" were not scanned during maintenance checklist completion. This could happen due to:</p>
                        <ul class="list-disc list-inside ml-4 space-y-1">
                            <li>Assets being temporarily moved or in storage</li>
                            <li>Human error during the scanning process</li>
                            <li>Assets being overlooked during maintenance</li>
                            <li>Technical issues with scanning equipment</li>
                        </ul>
                        <p class="mt-2"><strong>Action Required:</strong> Review each asset and either mark it as "Found" (if it's actually available) or "Lost" (if it's genuinely missing).</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Found Asset Modal -->
<div id="foundModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                    Mark Asset as Found
                </h3>
                <button onclick="closeFoundModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="foundForm" method="POST" class="mt-4">
                @csrf
                <div class="space-y-4">
                    <!-- Asset Info -->
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-gray-900" id="foundAssetCode">Asset Code</h4>
                                <p class="text-sm text-gray-600" id="foundAssetName">Asset Name</p>
                            </div>
                            <div class="text-right">
                                <span class="text-sm text-green-600 font-medium">Status: Unverified</span>
                            </div>
                        </div>
                    </div>

                    <!-- End of SY Status -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">End of SY Status <span class="text-red-500">*</span></label>
                        <select name="end_status" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                            <option value="OK">OK</option>
                            <option value="FOR REPAIR">For Repair</option>
                            <option value="FOR MAINTENANCE">For Maintenance</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">How should this asset be recorded for the checklist's end-of-year status?</p>
                    </div>

                    <!-- Resolution Notes -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Resolution Notes</label>
                        <textarea name="resolution_notes" 
                                  rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                  placeholder="Describe where the asset was found and any relevant details..."
                                  required></textarea>
                    </div>

                    <!-- Actions Taken -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Actions Taken</label>
                        <textarea name="actions_taken" 
                                  rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                  placeholder="Detail the specific actions taken to locate the asset..."></textarea>
                    </div>

                    <!-- Resolution Date -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Resolution Date</label>
                        <input type="date" 
                               name="resolution_date" 
                               value="{{ date('Y-m-d') }}"
                               max="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                               required>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                    <button type="button" onclick="closeFoundModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <i class="fas fa-check mr-2"></i>
                        Mark as Found
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Lost Asset Modal -->
<div id="lostModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                    Confirm Asset as Lost
                </h3>
                <button onclick="closeLostModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="lostForm" method="POST" class="mt-4">
                @csrf
                <div class="space-y-4">
                    <!-- Asset Info -->
                    <div class="bg-red-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-gray-900" id="lostAssetCode">Asset Code</h4>
                                <p class="text-sm text-gray-600" id="lostAssetName">Asset Name</p>
                            </div>
                            <div class="text-right">
                                <span class="text-sm text-red-600 font-medium">Status: Unverified</span>
                            </div>
                        </div>
                    </div>

                    <!-- Resolution Notes -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Investigation Notes <span class="text-red-500">*</span></label>
                        <textarea name="investigation_notes" 
                                  rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                  placeholder="Describe the investigation conducted and why the asset is confirmed as lost..."
                                  required></textarea>
                    </div>

                    <!-- Actions Taken -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Actions Taken</label>
                        <textarea name="actions_taken" 
                                  rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                  placeholder="Detail the specific actions taken to locate the asset..."></textarea>
                    </div>

                    <!-- Last Known Location -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Last Known Location</label>
                        <input type="text" 
                               name="last_known_location" 
                               id="lostLastKnownLocation"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed"
                               readonly>
                        <p class="text-xs text-gray-500 mt-1">This is the asset's location before it was marked as unverified</p>
                    </div>

                    <!-- Resolution Date -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Resolution Date</label>
                        <input type="date" 
                               name="resolution_date" 
                               value="{{ date('Y-m-d') }}"
                               max="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                               required>
                    </div>

                    <!-- Warning -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-3"></i>
                            <div class="text-sm text-yellow-800">
                                <strong>Warning:</strong> This action will create a lost asset record and cannot be easily undone. Please ensure you have conducted a thorough investigation before confirming the asset as lost.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                    <button type="button" onclick="closeLostModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <i class="fas fa-times mr-2"></i>
                        Confirm as Lost
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Found Modal Functions
let currentFoundAssetId = null;

function openFoundModal(assetId, assetCode, assetName) {
    currentFoundAssetId = assetId;
    document.getElementById('foundAssetCode').textContent = assetCode;
    document.getElementById('foundAssetName').textContent = assetName;
    
    // Set form action
    document.getElementById('foundForm').action = `/assets/${assetId}/mark-found`;
    
    // Show modal
    document.getElementById('foundModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeFoundModal() {
    document.getElementById('foundModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    
    // Reset form
    document.getElementById('foundForm').reset();
    document.getElementById('foundForm').action = '';
    currentFoundAssetId = null;
}

// Lost Modal Functions
let currentLostAssetId = null;

function openLostModal(assetId, assetCode, assetName, lastKnownLocation) {
    currentLostAssetId = assetId;
    document.getElementById('lostAssetCode').textContent = assetCode;
    document.getElementById('lostAssetName').textContent = assetName;
    
    // Set form action
    document.getElementById('lostForm').action = `/assets/${assetId}/confirm-lost`;
    
    // Populate last known location automatically
    document.getElementById('lostLastKnownLocation').value = lastKnownLocation;
    
    // Show modal
    document.getElementById('lostModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeLostModal() {
    document.getElementById('lostModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    
    // Reset form
    document.getElementById('lostForm').reset();
    document.getElementById('lostForm').action = '';
    currentLostAssetId = null;
}

// Close modals when clicking outside
document.getElementById('foundModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeFoundModal();
    }
});

document.getElementById('lostModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeLostModal();
    }
});
</script>
@endsection
