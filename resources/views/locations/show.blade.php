@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-8">
    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
        <div class="flex items-start gap-2">
            <i class="fas fa-check-circle mt-0.5"></i>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
        <div class="flex items-start gap-2">
            <i class="fas fa-times-circle mt-0.5"></i>
            <span class="font-semibold">{{ session('error') }}</span>
        </div>
    </div>
    @endif
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route(request()->routeIs('gsu.*') ? 'gsu.locations.index' : 'locations.index') }}" class="text-gray-600 hover:text-red-800 transition-colors">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-3xl font-bold flex items-center gap-2">
                <i class="fas fa-building text-red-800"></i>
                {{ $location->building }}
            </h1>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-sm text-gray-600">
                <i class="fas fa-boxes mr-2"></i>{{ $assets->count() }} assets
            </div>
            @if(Auth::user()->role === 'purchasing')
                <a href="{{ route('purchasing.assets.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                    <i class="fas fa-plus"></i> Create Asset
                </a>
            @elseif(Auth::user()->role === 'admin')
                <div class="bg-gray-100 text-gray-500 font-semibold py-2 px-4 rounded-lg flex items-center gap-2" title="Only Purchasing can create assets">
                    <i class="fas fa-info-circle"></i> Asset creation restricted to Purchasing role
                </div>
            @elseif(Auth::user()->role === 'gsu')
                <div class="bg-gray-100 text-gray-500 font-semibold py-2 px-4 rounded-lg flex items-center gap-2" title="Only Purchasing can create assets">
                    <i class="fas fa-info-circle"></i> Asset creation restricted to Purchasing role
                </div>
            @endif
            <a href="{{ route('locations.date-range', $location) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                <i class="fas fa-calendar-alt"></i> Date Range View
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-building text-red-800"></i>
                <div>
                    <div class="text-sm text-gray-600">Building</div>
                    <div class="font-semibold">{{ $location->building }}</div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <i class="fas fa-layer-group text-red-800"></i>
                <div>
                    <div class="text-sm text-gray-600">Floor</div>
                    <div class="font-semibold">{{ $location->floor }}</div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <i class="fas fa-door-open text-red-800"></i>
                <div>
                    <div class="text-sm text-gray-600">Room</div>
                    <div class="font-semibold">{{ $location->room }}</div>
                </div>
            </div>
        </div>
    </div>

    @php $isGsu = request()->routeIs('gsu.*'); @endphp
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <div class="bg-gray-50 p-4 border-b border-gray-200">
            <div class="flex items-center gap-4">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="searchInput" placeholder="Search by asset code, name, category..." 
                           class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>
                @php $assetsTotal = method_exists($assets, 'total') ? $assets->total() : $assets->count(); @endphp
                <div class="text-sm text-gray-600 font-medium">
                    Total: <span class="text-red-800 font-bold">{{ $assetsTotal }}</span> assets
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 font-sans">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-barcode mr-1"></i>Code
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-tag mr-1"></i>Asset Name
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-folder mr-1"></i>Category
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-tools mr-1"></i>Condition
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-info-circle mr-1"></i>Status
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <i class="fas fa-cogs mr-1"></i>Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100" id="assetsTableBody">
                    @forelse($assets as $asset)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                <span class="font-mono text-sm font-bold text-gray-900 bg-gray-100 px-2 py-1 rounded">
                                    {{ $asset->asset_code }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                <div class="font-medium text-sm text-gray-900">{{ $asset->name }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                <span class="text-sm text-gray-700 bg-blue-50 px-2 py-1 rounded-full">
                                    {{ $asset->category->name }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                                    {{ $asset->condition === 'Good' ? 'bg-green-100 text-green-800' : 
                                       ($asset->condition === 'Fair' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $asset->condition }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full {{ $asset->getStatusBadgeClass() }}">
                                        {{ $asset->getStatusLabel() }}
                                    </span>
                                    @if($asset->needsRepairResolution())
                                        <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full bg-orange-100 text-orange-800">
                                            {{ $asset->getRepairResolutionDays() }}d
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ $isGsu ? route('gsu.assets.show', $asset) : route('assets.show', $asset) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors duration-150"
                                       title="View Details">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    @if(Auth::user()->role === 'purchasing' && $asset->isPending())
                                        <a href="{{ route('purchasing.assets.edit', $asset) }}" 
                                           class="inline-flex items-center justify-center w-8 h-8 bg-yellow-100 text-yellow-600 rounded-full hover:bg-yellow-200 transition-colors duration-150"
                                           title="Edit Asset">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                    @else
                                        <span class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 text-gray-400 rounded-full cursor-not-allowed"
                                              title="Asset cannot be edited">
                                            <i class="fas fa-lock text-xs"></i>
                                        </span>
                                    @endif
                                    @if($asset->needsRepairResolution())
                                    <button onclick="openRepairResolutionModal({{ $asset->id }}, '{{ $asset->asset_code }}', '{{ $asset->status }}')"
                                            class="inline-flex items-center justify-center w-8 h-8 bg-orange-100 text-orange-600 rounded-full hover:bg-orange-200 transition-colors duration-150"
                                            title="Resolve Repair Status">
                                        <i class="fas fa-wrench text-xs"></i>
                                    </button>
                                    @endif
                                    @if($asset->status !== 'Disposed' && $asset->status !== 'Lost')
                                    <a href="{{ route('lost-assets.create', $asset) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 bg-purple-100 text-purple-600 rounded-full hover:bg-purple-200 transition-colors duration-150"
                                       title="Report as Lost">
                                        <i class="fas fa-search text-xs"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-4"></i>
                                    <div class="text-lg font-medium text-gray-600">No assets found</div>
                                    <div class="text-sm text-gray-500 mt-1">Get started by adding your first asset</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($assets, 'links'))
    <div class="mt-6">
        {{ $assets->links() }}
    </div>
    @endif

    <!-- Repair Resolution Modal -->
    <div id="repairResolutionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-wrench text-orange-600 mr-2"></i>
                        Resolve Repair Status
                    </h3>
                    <button onclick="closeRepairResolutionModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <form id="repairResolutionForm" method="POST" class="mt-4">
                    @csrf
                    <div class="space-y-4">
                        <!-- Asset Info -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900" id="modalAssetCode">Asset Code</h4>
                                    <p class="text-sm text-gray-600" id="modalAssetStatus">Status</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm text-gray-500">Days Pending:</span>
                                    <span class="text-lg font-bold text-orange-600" id="modalDaysPending">0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Resolution Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Resolution Status *</label>
                            <select name="resolution_status" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                <option value="">Select resolution status</option>
                                <option value="Repaired">Repaired - Asset has been fixed and is operational</option>
                                <option value="Disposed">Disposed - Asset is beyond repair and has been disposed</option>
                                <option value="Replaced">Replaced - Asset has been replaced with a new one</option>
                                <option value="Returned to Service">Returned to Service - Asset is back in use</option>
                            </select>
                        </div>

                        <!-- Resolution Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Resolution Date *</label>
                            <input type="date" name="resolution_date" required max="{{ date('Y-m-d') }}" 
                                   value="{{ date('Y-m-d') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        </div>

                        <!-- Resolution Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Resolution Notes</label>
                            <textarea name="resolution_notes" rows="3" 
                                      placeholder="Describe what was done to resolve the issue..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"></textarea>
                        </div>

                        <!-- Actions Taken -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Actions Taken</label>
                            <textarea name="actions_taken" rows="3" 
                                      placeholder="Detail the specific actions taken to resolve the issue..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"></textarea>
                        </div>

                        <!-- Repair Cost -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Repair Cost (if applicable)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₱</span>
                                <input type="number" name="repair_cost" step="0.01" min="0" 
                                       placeholder="0.00"
                                       class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                        </div>

                        <!-- Vendor Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Vendor Name</label>
                                <input type="text" name="vendor_name" 
                                       placeholder="Name of repair vendor or service provider"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Number</label>
                                <input type="text" name="invoice_number" 
                                       placeholder="Invoice or receipt number"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                        <button type="button" onclick="closeRepairResolutionModal()" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-orange-600 border border-transparent rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <i class="fas fa-check mr-2"></i>
                            Resolve Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
<script>
document.getElementById('searchInput')?.addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('#assetsTableBody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    });
});

// Repair Resolution Modal Functions
let currentAssetId = null;

function openRepairResolutionModal(assetId, assetCode, status) {
    currentAssetId = assetId;
    document.getElementById('modalAssetCode').textContent = assetCode;
    document.getElementById('modalAssetStatus').textContent = `Current Status: ${status}`;
    
    // Set form action
    document.getElementById('repairResolutionForm').action = `/assets/${assetId}/resolve-repair`;
    
    // Show modal
    document.getElementById('repairResolutionModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeRepairResolutionModal() {
    document.getElementById('repairResolutionModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    
    // Reset form
    document.getElementById('repairResolutionForm').reset();
    document.getElementById('repairResolutionForm').action = '';
    currentAssetId = null;
}

// Close modal when clicking outside
document.getElementById('repairResolutionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRepairResolutionModal();
    }
});
</script>
@endsection
