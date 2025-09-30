@extends('layouts.admin')

@section('title', 'All Assets - Asset Management')

@section('content')
<div class="container mx-auto py-4 md:py-8 px-2 sm:px-4 lg:px-0">
    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-200 text-green-800 px-3 md:px-4 py-2 md:py-3 rounded-lg">
        <div class="flex items-start gap-2">
            <i class="fas fa-check-circle mt-0.5 text-sm md:text-base"></i>
            <span class="font-semibold text-sm md:text-base">{{ session('success') }}</span>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-200 text-red-800 px-3 md:px-4 py-2 md:py-3 rounded-lg">
        <div class="flex items-start gap-2">
            <i class="fas fa-times-circle mt-0.5 text-sm md:text-base"></i>
            <span class="font-semibold text-sm md:text-base">{{ session('error') }}</span>
        </div>
    </div>
    @endif
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-3">
        <div class="flex items-center gap-2 md:gap-4">
            <h1 class="text-xl md:text-2xl lg:text-3xl font-bold flex items-center gap-2">
                <i class="fas fa-box text-red-800 text-lg md:text-xl lg:text-2xl"></i>
                All Assets
            </h1>
        </div>
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 md:gap-3 w-full sm:w-auto">
            <div class="text-xs md:text-sm text-gray-600 whitespace-nowrap">
                <i class="fas fa-boxes mr-1 md:mr-2"></i>{{ $assets->total() }} assets
            </div>
            @if(Auth::user()->role === 'purchasing')
                <a href="{{ route('purchasing.assets.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-3 md:px-4 rounded-lg transition duration-200 flex items-center gap-2 text-sm md:text-base w-full sm:w-auto justify-center">
                    <i class="fas fa-plus text-xs md:text-sm"></i> Create Asset
                </a>
            @elseif(Auth::user()->role === 'admin' || Auth::user()->role === 'gsu')
                <div class="bg-gray-100 text-gray-500 font-semibold py-2 px-3 md:px-4 rounded-lg flex items-center gap-2 text-xs md:text-sm" title="Only Purchasing can create assets">
                    <i class="fas fa-info-circle"></i> <span class="hidden md:inline">Asset creation restricted to Purchasing role</span><span class="md:hidden">Purchasing only</span>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <div class="bg-gray-50 p-3 md:p-4 border-b border-gray-200">
            <div class="flex items-center gap-2 md:gap-4">
                <button type="button" id="toggleFilters" class="inline-flex items-center px-3 py-2 bg-red-800 text-white text-xs md:text-sm rounded-lg hover:bg-red-900 transition">
                    <i class="fas fa-sliders-h mr-1 md:mr-2 text-xs md:text-sm"></i> Filters
                </button>
                <div class="text-xs md:text-sm text-gray-600 font-medium ml-auto">
                    Total: <span class="text-red-800 font-bold">{{ $assets->total() }}</span> <span class="hidden sm:inline">assets</span>
                </div>
            </div>
            <!-- Collapsible Filters -->
            <div id="filtersPanel" class="mt-3 md:mt-4 hidden">
                <form method="GET" action="{{ route('assets.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-2 md:gap-3">
                    <div class="relative">
                        <label class="block text-xs text-gray-600 mb-1">Search</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="q" value="{{ $filters['q'] ?? request('q') }}" placeholder="Code, name, category, location..." 
                                   class="w-full pl-10 pr-3 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Category</label>
                        <select name="category_id" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="">All</option>
                            @isset($categories)
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ ($filters['category_id'] ?? request('category_id')) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="">All</option>
                            @foreach(['Available','pending','active','maintenance','Disposed','Lost'] as $st)
                                <option value="{{ $st }}" {{ ($filters['status'] ?? request('status')) == $st ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Condition</label>
                        <select name="condition" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="">All</option>
                            @foreach(['Good','Fair','Poor'] as $cond)
                                <option value="{{ $cond }}" {{ ($filters['condition'] ?? request('condition')) == $cond ? 'selected' : '' }}>{{ $cond }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Deployment</label>
                        <select name="deployment" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="">All</option>
                            <option value="deployed" {{ ($filters['deployment'] ?? request('deployment')) == 'deployed' ? 'selected' : '' }}>Deployed</option>
                            <option value="not_deployed" {{ ($filters['deployment'] ?? request('deployment')) == 'not_deployed' ? 'selected' : '' }}>Not Deployed</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs text-gray-600 mb-1">Specific Location</label>
                        <select name="location_id" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="">All</option>
                            @isset($locations)
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ ($filters['location_id'] ?? request('location_id')) == $loc->id ? 'selected' : '' }}>
                                        {{ $loc->building }} - Floor {{ $loc->floor }} - Room {{ $loc->room }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                    <div class="flex items-end gap-2 sm:col-span-2 md:col-span-2">
                        <button type="submit" class="px-3 md:px-4 py-2 bg-red-800 text-white text-xs md:text-sm rounded-lg hover:bg-red-900 transition flex-1 sm:flex-initial">
                            <i class="fas fa-filter mr-1 md:mr-2"></i> Apply
                        </button>
                        <a href="{{ route('assets.index') }}" class="px-3 md:px-4 py-2 bg-gray-100 text-gray-700 text-xs md:text-sm rounded-lg hover:bg-gray-200 transition flex-1 sm:flex-initial text-center">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <!-- Mobile Card View -->
        <div class="block md:hidden p-3 space-y-3">
            @forelse($assets as $asset)
                <div class="bg-white border-2 border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden">
                    <!-- Card Header with Asset Code -->
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 py-3 border-b-2 border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-red-800 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-box text-white text-xs"></i>
                                </div>
                                <div>
                                    <div class="font-mono text-xs font-bold text-red-800 uppercase">
                                        {{ $asset->asset_code }}
                                    </div>
                                    <div class="text-xs text-gray-500">Asset Code</div>
                                </div>
                            </div>
                            @if($asset->status === 'active')
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-green-500 text-white shadow-sm">
                                    <i class="fas fa-check-circle mr-1"></i>Active
                                </span>
                            @elseif($asset->status === 'pending')
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-yellow-500 text-white shadow-sm">
                                    <i class="fas fa-clock mr-1"></i>Pending
                                </span>
                            @elseif($asset->status === 'maintenance')
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-orange-500 text-white shadow-sm">
                                    <i class="fas fa-wrench mr-1"></i>Maintenance
                                </span>
                            @elseif($asset->status === 'disposed')
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-red-500 text-white shadow-sm">
                                    <i class="fas fa-trash mr-1"></i>Disposed
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-gray-500 text-white shadow-sm">
                                    {{ ucfirst($asset->status) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Card Body -->
                    <div class="p-4">
                        <!-- Asset Name -->
                        <div class="mb-3">
                            <div class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Asset Name</div>
                            <div class="font-bold text-base text-gray-900">{{ $asset->name }}</div>
                        </div>
                        
                        <!-- Asset Details Grid -->
                        <div class="space-y-2.5">
                            <!-- Category -->
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-folder text-blue-600 text-sm w-4"></i>
                                    <span class="text-xs font-medium text-gray-600">Category</span>
                                </div>
                                <span class="text-xs font-bold text-gray-900 bg-blue-50 px-3 py-1 rounded-full border border-blue-200">
                                    {{ $asset->category->name ?? 'No Category' }}
                                </span>
                            </div>
                            
                            <!-- Location -->
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-map-marker-alt text-purple-600 text-sm w-4"></i>
                                    <span class="text-xs font-medium text-gray-600">Location</span>
                                </div>
                                @if($asset->location)
                                    <div class="text-right">
                                        <div class="text-xs font-bold text-gray-900">{{ $asset->location->building }}</div>
                                        <div class="text-xs text-gray-500">{{ $asset->location->name }}</div>
                                    </div>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-300">
                                        <i class="fas fa-exclamation-circle mr-1"></i>Not Deployed
                                    </span>
                                @endif
                            </div>
                            
                            <!-- Condition (if available) -->
                            @if($asset->condition)
                            <div class="flex items-center justify-between py-2">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-star text-orange-600 text-sm w-4"></i>
                                    <span class="text-xs font-medium text-gray-600">Condition</span>
                                </div>
                                <span class="text-xs font-bold text-gray-900">{{ $asset->condition }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Card Footer with Actions -->
                    <div class="bg-gray-50 px-4 py-3 border-t-2 border-gray-200">
                        <div class="flex items-center gap-2">
                            <a href="{{ url('assets/' . $asset->id) }}" 
                               class="flex-1 inline-flex items-center justify-center px-3 py-2.5 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 active:bg-blue-800 transition-colors shadow-sm">
                                <i class="fas fa-eye mr-1.5"></i>View
                            </a>
                            @if($asset->isAvailable() && $asset->location_id && (Auth::user()->role === 'admin' || Auth::user()->role === 'gsu'))
                                <a href="{{ route('assets.transfer-form', $asset) }}" 
                                   class="flex-1 inline-flex items-center justify-center px-3 py-2.5 bg-purple-600 text-white text-xs font-bold rounded-lg hover:bg-purple-700 active:bg-purple-800 transition-colors shadow-sm">
                                    <i class="fas fa-exchange-alt mr-1.5"></i>Transfer
                                </a>
                            @endif
                            @if($asset->isAvailable())
                                <button onclick="showDisposeModal({{ $asset->id }}, '{{ $asset->asset_code }}')" 
                                        class="flex-1 inline-flex items-center justify-center px-3 py-2.5 bg-red-600 text-white text-xs font-bold rounded-lg hover:bg-red-700 active:bg-red-800 transition-colors shadow-sm">
                                    <i class="fas fa-trash mr-1.5"></i>Dispose
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center bg-white rounded-xl border-2 border-dashed border-gray-300">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-inbox text-3xl text-gray-400"></i>
                    </div>
                    <div class="text-base font-bold text-gray-900 mb-1">No assets found</div>
                    <div class="text-sm text-gray-500">Get started by adding your first asset</div>
                </div>
            @endforelse
        </div>
        
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
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
                                    @if($asset->isAvailable() && $asset->location_id && (Auth::user()->role === 'admin' || Auth::user()->role === 'gsu'))
                                        <a href="{{ route('assets.transfer-form', $asset) }}" 
                                           class="inline-flex items-center justify-center w-8 h-8 bg-purple-100 text-purple-600 rounded-full hover:bg-purple-200 transition-colors duration-150"
                                           title="Transfer Asset">
                                            <i class="fas fa-exchange-alt text-xs"></i>
                                        </a>
                                    @endif
                                    @if($asset->status !== 'Disposed' && $asset->status !== 'Lost')
                                        <a href="{{ route('lost-assets.create', $asset) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 bg-indigo-100 text-indigo-600 rounded-full hover:bg-indigo-200 transition-colors duration-150"
                                           title="Report as Lost">
                                            <i class="fas fa-search text-xs"></i>
                                        </a>
                                    @endif
                                    @if($asset->isAvailable())
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
<div id="disposeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden p-4">
    <div id="disposeModalCard" class="bg-white rounded-xl shadow-xl p-6 md:p-8 w-full max-w-md relative animate-fade-in">
        <button onclick="closeDisposeModal()" class="absolute top-3 right-3 text-gray-400 hover:text-red-800 text-xl"><i class="fas fa-times"></i></button>
        <div class="flex flex-col items-center">
            <div class="bg-red-100 text-red-800 rounded-full p-4 mb-4">
                <i class="fas fa-exclamation-triangle text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold mb-2 text-gray-800">Dispose Asset</h3>
            <p class="text-gray-600 mb-6 text-center">Are you sure you want to dispose asset <span class="font-semibold text-red-800" id="dispose-asset-name">CODE</span>? This action cannot be undone.</p>
            <form id="disposeForm" method="POST" class="w-full flex flex-col gap-3">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Disposal Reason <span class="text-red-600">*</span></label>
                    <textarea name="disposal_reason" id="disposal_reason" rows="4" required
                              placeholder="Please provide a reason for disposing this asset..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
                </div>
                <button type="submit" class="w-full bg-red-800 hover:bg-red-900 text-white font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-trash-alt"></i> Dispose
                </button>
                <button type="button" onclick="closeDisposeModal()" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </form>
        </div>
    </div>
</div>

</div>
<script>
// Toggle filters panel
document.getElementById('toggleFilters')?.addEventListener('click', function() {
    const panel = document.getElementById('filtersPanel');
    if (!panel) return;
    panel.classList.toggle('hidden');
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
