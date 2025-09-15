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
            <h1 class="text-3xl font-bold flex items-center gap-2">
                <i class="fas fa-box text-red-800"></i>
                All Assets
            </h1>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-sm text-gray-600">
                <i class="fas fa-boxes mr-2"></i>{{ $assets->total() }} assets
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
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <div class="bg-gray-50 p-4 border-b border-gray-200">
            <div class="flex items-center gap-4">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="searchInput" placeholder="Search by asset code, name, category..." 
                           class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>
                <div class="text-sm text-gray-600 font-medium">
                    Total: <span class="text-red-800 font-bold">{{ $assets->total() }}</span> assets
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
                            <i class="fas fa-map-marker-alt mr-1"></i>Location
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
                            <td class="px-4 py-3 border-r border-gray-100">
                                <div class="font-medium text-sm text-gray-900">{{ $asset->name }}</div>
                                @if($asset->description)
                                    <div class="text-xs text-gray-500 mt-1">{{ Str::limit($asset->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                <span class="text-sm text-gray-700 bg-blue-50 px-2 py-1 rounded-full">
                                    {{ $asset->category->name ?? 'No Category' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                @if($asset->location)
                                    <div class="text-sm text-gray-900">{{ $asset->location->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $asset->location->building }}</div>
                                @else
                                    <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Not Deployed
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                @if($asset->status === 'active')
                                    <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @elseif($asset->status === 'pending')
                                    <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @elseif($asset->status === 'maintenance')
                                    <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full bg-orange-100 text-orange-800">
                                        Maintenance
                                    </span>
                                @elseif($asset->status === 'disposed')
                                    <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full bg-red-100 text-red-800">
                                        Disposed
                                    </span>
                                @else
                                    <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ ucfirst($asset->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ url('assets/' . $asset->id) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors duration-150"
                                       title="View Details">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    @if($asset->status === 'active')
                                        <button onclick="showDisposeModal({{ $asset->id }}, '{{ $asset->asset_code }}')"
                                                class="inline-flex items-center justify-center w-8 h-8 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-150"
                                                title="Dispose Asset">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
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

    @if($assets->hasPages())
    <div class="mt-6">
        {{ $assets->links() }}
    </div>
    @endif
</div>

<!-- Dispose Modal -->
<div id="disposeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-trash text-red-600 mr-2"></i>
                    Dispose Asset
                </h3>
                <button onclick="closeDisposeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="disposeForm" method="POST" class="mt-4">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <!-- Asset Info -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-gray-900" id="dispose-asset-name">Asset Code</h4>
                                <p class="text-sm text-gray-600">You are about to dispose this asset</p>
                            </div>
                        </div>
                    </div>

                    <!-- Disposal Reason -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Disposal Reason *</label>
                        <textarea name="disposal_reason" id="disposal_reason" rows="4" required
                                  placeholder="Please provide a reason for disposing this asset..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                    <button type="button" onclick="closeDisposeModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <i class="fas fa-trash mr-2"></i>
                        Dispose Asset
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

// Dispose Modal Functions
let currentAssetId = null;

function showDisposeModal(assetId, assetCode) {
    currentAssetId = assetId;
    document.getElementById('dispose-asset-name').textContent = assetCode;
    
    // Set form action
    document.getElementById('disposeForm').action = `{{ url('assets') }}/${assetId}/dispose`;
    
    // Show modal
    document.getElementById('disposeModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Focus on textarea
    setTimeout(() => {
        document.getElementById('disposal_reason').focus();
    }, 100);
}

function closeDisposeModal() {
    document.getElementById('disposeModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    
    // Reset form
    document.getElementById('disposeForm').reset();
    document.getElementById('disposeForm').action = '';
    currentAssetId = null;
}

// Close modal when clicking outside
document.getElementById('disposeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDisposeModal();
    }
});
</script>
@endsection
