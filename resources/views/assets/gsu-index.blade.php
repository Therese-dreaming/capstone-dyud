@extends('layouts.gsu')

@section('content')
<div class="container mx-auto py-8" x-data="{ 
    showToast: {{ session('success') || session('error') ? 'true' : 'false' }}, 
    showModal: false, 
    deleteAssetId: null, 
    deleteAssetCode: '', 
    showDisposeModal: false, 
    disposeAssetId: null, 
    disposeAssetCode: '',
    showQRModal: false,
    selectedAsset: null
}">
    <!-- GSU Asset Management Header -->
    <div class="bg-gradient-to-r from-red-800 to-red-900 text-white p-6 rounded-xl shadow-lg mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-white/20 p-3 rounded-full">
                    <i class="fas fa-boxes text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold">Asset Management</h1>
                    <p class="text-red-100 text-sm md:text-base">GSU Super Administrator Control Panel</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="openQRScanner()" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-qrcode mr-2"></i>QR Scanner
                </button>
                <a href="{{ route('gsu.assets.create') }}" class="bg-white text-red-800 font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center gap-2 shadow-lg hover:bg-gray-100">
                    <i class="fas fa-plus"></i> Add New Asset
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-boxes text-blue-600"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">{{ $assets->total() }}</div>
                    <div class="text-sm text-gray-500">Total Assets</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">{{ $assets->where('status', 'Available')->count() }}</div>
                    <div class="text-sm text-gray-500">Available</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-tools text-yellow-600"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">{{ $assets->where('status', 'In Use')->count() }}</div>
                    <div class="text-sm text-gray-500">In Use</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div class="bg-red-100 p-3 rounded-lg">
                    <i class="fas fa-ban text-red-600"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">{{ $assets->where('status', 'Disposed')->count() }}</div>
                    <div class="text-sm text-gray-500">Disposed</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Asset Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="relative flex-1 max-w-md">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="searchInput" placeholder="Search assets..." 
                               class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div class="text-sm text-gray-600 font-medium">
                        Showing <span class="text-red-800 font-bold">{{ $assets->count() }}</span> of {{ $assets->total() }} assets
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button class="bg-red-800 text-white px-3 py-1 rounded text-sm hover:bg-red-900 transition-colors">
                        <i class="fas fa-download mr-1"></i>Export
                    </button>
                    <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition-colors">
                        <i class="fas fa-print mr-1"></i>Print
                    </button>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <i class="fas fa-barcode mr-1"></i>Code
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <i class="fas fa-tag mr-1"></i>Asset Name
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <i class="fas fa-folder mr-1"></i>Category
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <i class="fas fa-map-marker-alt mr-1"></i>Location
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <i class="fas fa-tools mr-1"></i>Condition
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <i class="fas fa-info-circle mr-1"></i>Status
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <i class="fas fa-cogs mr-1"></i>GSU Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($assets as $asset)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <span class="font-mono text-sm font-bold text-gray-900 bg-gray-100 px-2 py-1 rounded">
                                        {{ $asset->asset_code }}
                                    </span>
                                    <button onclick="showQRCode('{{ $asset->asset_code }}')" 
                                            class="text-blue-600 hover:text-blue-800 transition-colors"
                                            title="View QR Code">
                                        <i class="fas fa-qrcode text-xs"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="font-medium text-sm text-gray-900">{{ $asset->name }}</div>
                                <div class="text-xs text-gray-500">Added {{ $asset->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-sm text-gray-700 bg-blue-50 px-2 py-1 rounded-full">
                                    {{ $asset->category->name }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-xs text-gray-600">
                                    <div class="font-medium">{{ $asset->location->building }}</div>
                                    <div class="text-gray-500">Floor {{ $asset->location->floor }} • Room {{ $asset->location->room }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                                    {{ $asset->condition === 'Good' ? 'bg-green-100 text-green-800' : 
                                       ($asset->condition === 'Fair' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $asset->condition }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                                    {{ $asset->status === 'Available' ? 'bg-green-100 text-green-800' : 
                                       ($asset->status === 'In Use' ? 'bg-blue-100 text-blue-800' : 
                                       ($asset->status === 'Lost' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                    {{ $asset->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-1">
                                    <a href="{{ route('gsu.assets.show', $asset) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors duration-150"
                                       title="View Details">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('gsu.maintenances.index', $asset) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 bg-green-100 text-green-600 rounded-full hover:bg-green-200 transition-colors duration-150"
                                       title="Maintenance Records">
                                        <i class="fas fa-tools text-xs"></i>
                                    </a>
                                    <a href="{{ route('gsu.assets.edit', $asset) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 bg-yellow-100 text-yellow-600 rounded-full hover:bg-yellow-200 transition-colors duration-150"
                                       title="Edit Asset">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    @if($asset->status !== 'Disposed')
                                    <button @click="showDisposeModal = true; disposeAssetId = {{ $asset->id }}; disposeAssetCode = '{{ addslashes($asset->asset_code) }}'"
                                            class="inline-flex items-center justify-center w-8 h-8 bg-orange-100 text-orange-600 rounded-full hover:bg-orange-200 transition-colors duration-150"
                                            title="Dispose Asset">
                                        <i class="fas fa-ban text-xs"></i>
                                    </button>
                                    @endif
                                    <button @click="showModal = true; deleteAssetId = {{ $asset->id }}; deleteAssetCode = '{{ addslashes($asset->asset_code) }}'"
                                            class="inline-flex items-center justify-center w-8 h-8 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-150"
                                            title="Delete Asset">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-4"></i>
                                    <div class="text-lg font-medium text-gray-600">No assets found</div>
                                    <div class="text-sm text-gray-500 mt-1">Get started by adding your first asset</div>
                                    <a href="{{ route('gsu.assets.create') }}" class="mt-4 inline-block bg-red-800 text-white px-4 py-2 rounded-lg hover:bg-red-900 transition-colors">
                                        Add First Asset
                                    </a>
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
        {{ $assets->links() }}
    </div>

    <!-- QR Code Modal -->
    <div x-show="showQRModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50" 
         style="display: none;"
         @click.self="showQRModal = false">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm" 
             @click="showQRModal = false"></div>
        
        <!-- Modal Container -->
        <div class="flex min-h-full items-center justify-center p-4">
            <!-- Modal Panel -->
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                
                <!-- Header -->
                <div class="px-8 pt-8 pb-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-50 mb-4">
                        <i class="fas fa-qrcode text-blue-500 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Asset QR Code</h3>
                    <p class="text-gray-600">Scan to view asset details</p>
                </div>
                
                <!-- Body -->
                <div class="px-8 pb-6">
                    <div class="bg-gray-50 rounded-xl p-6 text-center">
                        <div id="qrcode-container" class="flex justify-center mb-4"></div>
                        <p class="text-sm text-gray-500">Use your mobile device to scan this QR code</p>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="px-8 pb-8">
                    <button @click="showQRModal = false" 
                            type="button" 
                            class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-times mr-2"></i>
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Dispose Modal -->
    <div x-show="showDisposeModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50" 
         style="display: none;"
         @click.self="showDisposeModal = false">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm" 
             @click="showDisposeModal = false"></div>
        
        <!-- Modal Container -->
        <div class="flex min-h-full items-center justify-center p-4">
            <!-- Modal Panel -->
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                
                <form :action="`/gsu/assets/${disposeAssetId}/dispose`" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Header -->
                    <div class="px-8 pt-8 pb-6 text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-orange-50 mb-4">
                            <i class="fas fa-ban text-orange-500 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Dispose Asset</h3>
                        <p class="text-gray-600">Mark asset as disposed with reason</p>
                    </div>
                    
                    <!-- Body -->
                    <div class="px-8 pb-6">
                        <div class="bg-gray-50 rounded-xl p-4 text-center mb-6">
                            <p class="text-gray-700 mb-2">
                                Are you sure you want to dispose of
                            </p>
                            <p class="text-lg font-semibold text-gray-900" x-text="disposeAssetCode"></p>
                        </div>
                        <div>
                            <label for="disposal_reason" class="block text-sm font-medium text-gray-700 mb-3">Disposal Reason</label>
                            <textarea name="disposal_reason" 
                                      id="disposal_reason" 
                                      rows="4" 
                                      class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-orange-500 focus:border-orange-500 resize-none" 
                                      placeholder="Enter reason for disposal..."
                                      required></textarea>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="px-8 pb-8 flex gap-3">
                        <button type="button" 
                                @click="showDisposeModal = false" 
                                class="flex-1 px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl font-medium transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="flex-1 px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-medium transition-colors duration-200 flex items-center justify-center">
                            <i class="fas fa-ban mr-2"></i>
                            Dispose
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal (aligned with Admin design) -->
    <div x-show="showModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" style="display: none;">
        <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-md relative">
            <button @click="showModal = false" class="absolute top-3 right-3 text-gray-400 hover:text-red-800 text-xl"><i class="fas fa-times"></i></button>
            <div class="flex flex-col items-center">
                <div class="bg-red-100 text-red-800 rounded-full p-4 mb-4">
                    <i class="fas fa-exclamation-triangle text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2 text-gray-800">Delete Asset</h3>
                <p class="text-gray-600 mb-6 text-center">Are you sure you want to delete <span class="font-semibold text-red-800" x-text="deleteAssetCode"></span>? This action cannot be undone.</p>
                <form :action="'/gsu/assets/' + deleteAssetId" method="POST" class="w-full flex flex-col items-center gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-800 hover:bg-red-900 text-white font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                    <button type="button" @click="showModal = false" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
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
</div>

<style>
@keyframes fade-in { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: none; } }
.animate-fade-in { animation: fade-in 0.5s; }
</style>

<script>
// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    });
});

// QR Code functionality
function showQRCode(assetCode) {
    const container = document.getElementById('qrcode-container');
    container.innerHTML = '';
    
    // Create QR code (you can use a QR code library here)
    const qrDiv = document.createElement('div');
    qrDiv.className = 'bg-gray-100 p-4 rounded-lg';
    qrDiv.innerHTML = `
        <div class="text-center">
            <div class="text-2xl font-bold text-gray-800 mb-2">${assetCode}</div>
            <div class="text-sm text-gray-600">Asset QR Code</div>
        </div>
    `;
    container.appendChild(qrDiv);
    
    // Show modal
    document.querySelector('[x-data]').__x.$data.showQRModal = true;
}

function openQRScanner() {
    alert('QR Scanner feature coming soon!');
}

//
</script>
@endsection 