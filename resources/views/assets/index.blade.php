@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-8" x-data="{ showToast: {{ session('success') || session('error') ? 'true' : 'false' }}, showModal: false, deleteAssetId: null, deleteAssetCode: '', showDisposeModal: false, disposeAssetId: null, disposeAssetCode: '' }">
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
            <i class="fas fa-boxes text-red-800"></i>
            Asset Management
        </h1>
        <p class="text-gray-600 mt-1">Manage and track all your assets</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('maintenances.batch-create') }}" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center gap-2">
            <i class="fas fa-tasks"></i> Batch Maintenance
        </a>
        <a href="{{ route('assets.create') }}" class="bg-gradient-to-r from-red-800 to-red-900 hover:from-red-900 hover:to-red-950 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center gap-2 shadow-lg">
            <i class="fas fa-plus"></i> Add New Asset
        </a>
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
                        <i class="fas fa-map-marker-alt mr-1"></i>Current Location
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
            <tbody class="bg-white divide-y divide-gray-100">
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
                            <div class="text-xs text-gray-600">
                                <div class="font-medium">{{ $asset->location->building }}</div>
                                <div class="text-gray-500">Floor {{ $asset->location->floor }} • Room {{ $asset->location->room }}</div>
                                @if($asset->status === 'In Use' && $asset->original_location_id && $asset->original_location_id !== $asset->location_id)
                                    <div class="text-blue-600 font-medium mt-1">
                                        <i class="fas fa-map-marker-alt mr-1"></i>Currently borrowed
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                            <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                                {{ $asset->condition === 'Good' ? 'bg-green-100 text-green-800' : 
                                   ($asset->condition === 'Fair' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $asset->condition }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                            <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                                {{ $asset->status === 'Available' ? 'bg-green-100 text-green-800' : 
                                   ($asset->status === 'In Use' ? 'bg-blue-100 text-blue-800' : 
                                   ($asset->status === 'Lost' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                {{ $asset->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('assets.show', $asset) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors duration-150"
                                   title="View Details">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('maintenances.index', $asset) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 bg-green-100 text-green-600 rounded-full hover:bg-green-200 transition-colors duration-150"
                                   title="Maintenance Records">
                                    <i class="fas fa-tools text-xs"></i>
                                </a>
                                <a href="{{ route('assets.edit', $asset) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 bg-yellow-100 text-yellow-600 rounded-full hover:bg-yellow-200 transition-colors duration-150"
                                   title="Edit Asset">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                @if($asset->status !== 'Disposed' && $asset->status !== 'Lost')
                                <button @click="showDisposeModal = true; disposeAssetId = {{ $asset->id }}; disposeAssetCode = '{{ addslashes($asset->asset_code) }}'"
                                        class="inline-flex items-center justify-center w-8 h-8 bg-orange-100 text-orange-600 rounded-full hover:bg-orange-200 transition-colors duration-150"
                                        title="Dispose Asset">
                                    <i class="fas fa-ban text-xs"></i>
                                </button>
                                @endif
                                @if($asset->status !== 'Disposed' && $asset->status !== 'Lost')
                                <a href="{{ route('lost-assets.create', $asset) }}"
                                   class="inline-flex items-center justify-center w-8 h-8 bg-purple-100 text-purple-600 rounded-full hover:bg-purple-200 transition-colors duration-150"
                                   title="Report as Lost">
                                    <i class="fas fa-search text-xs"></i>
                                </a>
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
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $assets->links() }}
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

    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchValue) ? '' : 'none';
        });
    });

    // Delete confirmation
    function confirmDelete(assetCode, assetId) {
        if (confirm(`Are you sure you want to delete asset "${assetCode}"? This action cannot be undone.`)) {
            // Create a form and submit it for deletion
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/assets/${assetId}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>

    <!-- Dispose Modal -->
    <div x-show="showDisposeModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" style="display: none;">
        <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-md relative">
            <button @click="showDisposeModal = false" class="absolute top-3 right-3 text-gray-400 hover:text-orange-800 text-xl"><i class="fas fa-times"></i></button>
            <div class="flex flex-col items-center">
                <div class="bg-orange-100 text-orange-800 rounded-full p-4 mb-4">
                    <i class="fas fa-ban text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2 text-gray-800">Dispose Asset</h3>
                <p class="text-gray-600 mb-6 text-center">Are you sure you want to dispose <span class="font-semibold text-orange-800" x-text="disposeAssetCode"></span>? Please provide a reason for disposal.</p>
                <form :action="'/assets/' + disposeAssetId + '/dispose'" method="POST" class="w-full flex flex-col items-center gap-3">
                    @csrf
                    @method('PUT')
                    <div class="w-full mb-4">
                        <label for="disposal_reason" class="block text-sm font-medium text-gray-700 mb-2">Disposal Reason</label>
                        <textarea name="disposal_reason" id="disposal_reason" rows="3" required
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                                  placeholder="Please provide a reason for disposing this asset..."></textarea>
                    </div>
                    <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-ban"></i> Dispose Asset
                    </button>
                    <button type="button" @click="showDisposeModal = false" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div x-show="showModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" style="display: none;">
        <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-md relative">
            <button @click="showModal = false" class="absolute top-3 right-3 text-gray-400 hover:text-red-800 text-xl"><i class="fas fa-times"></i></button>
            <div class="flex flex-col items-center">
                <div class="bg-red-100 text-red-800 rounded-full p-4 mb-4">
                    <i class="fas fa-exclamation-triangle text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2 text-gray-800">Delete Asset</h3>
                <p class="text-gray-600 mb-6 text-center">Are you sure you want to delete <span class="font-semibold text-red-800" x-text="deleteAssetCode"></span>? This action cannot be undone.</p>
                <form :action="'/assets/' + deleteAssetId" method="POST" class="w-full flex flex-col items-center gap-3">
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

</div>
@endsection
