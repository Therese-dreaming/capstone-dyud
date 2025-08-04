@extends('layouts.user')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <i class="fas fa-handshake text-red-800"></i>
                New Borrowing Request
            </h1>
            <p class="text-gray-600 mt-1">Submit a request to borrow available assets</p>
        </div>
        <a href="{{ route('user.borrowings.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors text-sm font-medium flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            Back to My Requests
        </a>
    </div>

    <div class="max-w-6xl mx-auto">
        <!-- Borrower Information -->
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Borrower Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" value="{{ auth()->user()->name }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ID Number</label>
                    <input type="text" value="{{ auth()->user()->id_number }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50" readonly>
                </div>
            </div>
        </div>

        <!-- Asset Selection with Search and Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Asset Selection</h3>
            
            <!-- Information Note -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-3"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-1">Asset Availability</p>
                        <p>Only assets that are currently available and have no pending or approved borrowing requests are shown. This ensures fair access to all users.</p>
                    </div>
                </div>
            </div>
            
            <!-- Search and Filters -->
            <div class="mb-6">
                <form method="GET" action="{{ route('user.borrowings.create') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search Assets</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Search by name, code, or category..."
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Category</label>
                        <select name="category_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-500">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-red-800 hover:bg-red-900 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                    </div>
                </form>
            </div>

            <!-- Available Assets Grid -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-md font-medium text-gray-900">Available Assets ({{ $availableAssets->total() }} total)</h4>
                    <div class="flex items-center space-x-4">
                        <div class="text-sm text-gray-600">
                            Showing {{ $availableAssets->firstItem() ?? 0 }} - {{ $availableAssets->lastItem() ?? 0 }} of {{ $availableAssets->total() }}
                        </div>
                        <div class="flex items-center space-x-2">
                            <button type="button" onclick="selectAllVisible()" class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded transition-colors">
                                <i class="fas fa-check-square mr-1"></i>Select All
                            </button>
                            <button type="button" onclick="deselectAll()" class="text-xs bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded transition-colors">
                                <i class="fas fa-square mr-1"></i>Deselect All
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="assetsContainer">
                    @forelse($availableAssets as $asset)
                        <div class="asset-card border border-gray-200 rounded-lg p-4 hover:border-red-300 transition-colors cursor-pointer"
                             data-asset-id="{{ $asset->id }}"
                             data-asset-name="{{ $asset->name }}"
                             data-asset-code="{{ $asset->asset_code }}"
                             onclick="toggleAssetCard(this)">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" 
                                           class="asset-checkbox w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
                                           data-asset-id="{{ $asset->id }}"
                                           data-asset-name="{{ $asset->name }}"
                                           data-asset-code="{{ $asset->asset_code }}"
                                           onchange="toggleAssetSelection(this)"
                                           onclick="event.stopPropagation()">
                                    <h4 class="font-medium text-gray-900 text-sm">{{ $asset->name }}</h4>
                                </div>
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Available</span>
                            </div>
                            <p class="text-xs text-gray-600 font-mono mb-2">{{ $asset->asset_code }}</p>
                            <p class="text-xs text-gray-500 mb-1">{{ $asset->category->name }}</p>
                            <p class="text-xs text-gray-500 mb-2">{{ $asset->location->building }} - Floor {{ $asset->location->floor }} - Room {{ $asset->location->room }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-xs px-2 py-1 rounded-full 
                                    {{ $asset->condition === 'Good' ? 'bg-green-100 text-green-800' : 
                                       ($asset->condition === 'Fair' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $asset->condition }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-8">
                            <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-600">No available assets found matching your criteria.</p>
                            <p class="text-sm text-gray-500 mt-2">Note: Assets with pending or approved borrowing requests are not shown.</p>
                        </div>
                    @endforelse
                </div>
                
                <!-- Pagination -->
                @if($availableAssets->hasPages())
                    <div class="mt-6">
                        {{ $availableAssets->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Selected Assets Cart -->
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 mb-6" id="cartSection" style="display: none;">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Selected Assets</h3>
            <div id="selectedAssetsList" class="space-y-2 mb-4 max-h-96 overflow-y-auto">
                <!-- Selected assets will be displayed here -->
            </div>
            <div id="cartPagination" class="mb-4" style="display: none;">
                <div class="flex justify-center space-x-2">
                    <button type="button" onclick="changeCartPage(-1)" class="px-3 py-1 text-sm bg-gray-200 hover:bg-gray-300 rounded">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span class="px-3 py-1 text-sm">Page <span id="currentCartPage">1</span> of <span id="totalCartPages">1</span></span>
                    <button type="button" onclick="changeCartPage(1)" class="px-3 py-1 text-sm bg-gray-200 hover:bg-gray-300 rounded">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Total assets selected: <span id="totalItems">0</span></span>
                <button type="button" onclick="clearCart()" class="text-sm text-red-600 hover:text-red-800">
                    <i class="fas fa-trash mr-1"></i>Clear All
                </button>
            </div>
        </div>

        <!-- Borrowing Details Form -->
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200" id="borrowingForm" style="display: none;">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Borrowing Details</h3>
            
            <form action="{{ route('user.borrowings.store-bulk') }}" method="POST" id="bulkBorrowingForm">
                @csrf
                
                <!-- Hidden inputs for selected assets -->
                <div id="hiddenInputs"></div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Request Date</label>
                        <input type="date" name="request_date" value="{{ date('Y-m-d') }}" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                        <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+7 days')) }}" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-500" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Where will you use the asset?</label>
                    <select name="location_id" id="location_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-500" required onchange="toggleCustomLocation()">
                        <option value="">Select a location</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->building }} - Floor {{ $location->floor }} - Room {{ $location->room }}
                            </option>
                        @endforeach
                        <option value="custom" {{ old('location_id') == 'custom' ? 'selected' : '' }}>Custom Location (Not in list)</option>
                    </select>
                    <p class="text-sm text-gray-600 mt-1">Select where you will be using the borrowed asset</p>
                </div>
                
                <!-- Custom Location Fields (hidden by default) -->
                <div id="customLocationFields" class="mb-4" style="display: none;">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-yellow-600 mt-0.5 mr-3"></i>
                            <div class="text-sm text-yellow-800">
                                <p class="font-medium mb-1">Custom Location</p>
                                <p>Please provide the details of where you will be using the asset. This information will be recorded for tracking purposes.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="custom_building">Building</label>
                            <input type="text" name="custom_building" id="custom_building" 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-500" 
                                   placeholder="e.g., Main Building, Annex, etc." 
                                   value="{{ old('custom_building') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="custom_floor">Floor</label>
                            <input type="text" name="custom_floor" id="custom_floor" 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-500" 
                                   placeholder="e.g., 1st, 2nd, Ground, etc." 
                                   value="{{ old('custom_floor') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="custom_room">Room/Area</label>
                            <input type="text" name="custom_room" id="custom_room" 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-500" 
                                   placeholder="e.g., Room 101, Lab A, Office, etc." 
                                   value="{{ old('custom_room') }}">
                        </div>
                    </div>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Purpose of Borrowing</label>
                    <textarea name="purpose" rows="3" 
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-500"
                              placeholder="Please describe the purpose for borrowing these assets..." required></textarea>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('user.borrowings.index') }}" 
                       class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg transition duration-200">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-red-800 hover:bg-red-900 text-white font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i> Submit Request(s)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<style>
@keyframes fade-in { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: none; } }
.animate-fade-in { animation: fade-in 0.5s; }
.asset-card.selected { border-color: #dc2626; background-color: #fef2f2; }
</style>

<script>
let selectedAssets = new Set(); // Set of asset IDs
let cartPage = 1;
const itemsPerCartPage = 30;

// Load selections from localStorage
function loadSelections() {
    const saved = localStorage.getItem('borrowingSelections');
    if (saved) {
        const savedAssets = JSON.parse(saved);
        selectedAssets = new Set(savedAssets);
        
        // Update checkboxes to reflect saved state
        const checkboxes = document.querySelectorAll('.asset-checkbox');
        checkboxes.forEach(checkbox => {
            if (selectedAssets.has(checkbox.dataset.assetId)) {
                checkbox.checked = true;
            }
        });
    }
}

// Save selections to localStorage
function saveSelections() {
    localStorage.setItem('borrowingSelections', JSON.stringify([...selectedAssets]));
}

// Clear selections from localStorage
function clearSelections() {
    localStorage.removeItem('borrowingSelections');
}

function toggleAssetCard(card) {
    const checkbox = card.querySelector('.asset-checkbox');
    checkbox.checked = !checkbox.checked;
    toggleAssetSelection(checkbox);
}

function toggleAssetSelection(checkbox) {
    const assetId = checkbox.dataset.assetId;
    
    if (checkbox.checked) {
        selectedAssets.add(assetId);
    } else {
        selectedAssets.delete(assetId);
    }
    
    saveSelections();
    updateCartDisplay();
    updateFormVisibility();
}

function selectAllVisible() {
    const checkboxes = document.querySelectorAll('.asset-checkbox');
    checkboxes.forEach(checkbox => {
        if (!checkbox.checked) {
            checkbox.checked = true;
            toggleAssetSelection(checkbox);
        }
    });
}

function deselectAll() {
    const checkboxes = document.querySelectorAll('.asset-checkbox');
    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            checkbox.checked = false;
            toggleAssetSelection(checkbox);
        }
    });
}

function removeFromCart(assetId) {
    selectedAssets.delete(assetId);
    
    // Uncheck the corresponding checkbox
    const checkbox = document.querySelector(`.asset-checkbox[data-asset-id="${assetId}"]`);
    if (checkbox) {
        checkbox.checked = false;
    }
    
    saveSelections();
    updateCartDisplay();
    updateFormVisibility();
}

function changeCartPage(direction) {
    const totalPages = Math.ceil(selectedAssets.size / itemsPerCartPage);
    cartPage = Math.max(1, Math.min(totalPages, cartPage + direction));
    updateCartDisplay();
}

function updateCartDisplay() {
    const cartSection = document.getElementById('cartSection');
    const selectedAssetsList = document.getElementById('selectedAssetsList');
    const totalItemsSpan = document.getElementById('totalItems');
    const cartPagination = document.getElementById('cartPagination');
    const currentCartPageSpan = document.getElementById('currentCartPage');
    const totalCartPagesSpan = document.getElementById('totalCartPages');
    
    if (selectedAssets.size === 0) {
        cartSection.style.display = 'none';
        return;
    }
    
    cartSection.style.display = 'block';
    
    // Convert Set to Array for pagination
    const selectedAssetsArray = Array.from(selectedAssets);
    const totalPages = Math.ceil(selectedAssetsArray.length / itemsPerCartPage);
    
    // Show/hide pagination
    if (totalPages > 1) {
        cartPagination.style.display = 'block';
        currentCartPageSpan.textContent = cartPage;
        totalCartPagesSpan.textContent = totalPages;
    } else {
        cartPagination.style.display = 'none';
        cartPage = 1;
    }
    
    // Calculate start and end indices for current page
    const startIndex = (cartPage - 1) * itemsPerCartPage;
    const endIndex = Math.min(startIndex + itemsPerCartPage, selectedAssetsArray.length);
    const currentPageAssets = selectedAssetsArray.slice(startIndex, endIndex);
    
    let html = '';
    
    currentPageAssets.forEach(assetId => {
        const checkbox = document.querySelector(`.asset-checkbox[data-asset-id="${assetId}"]`);
        if (checkbox) {
            const assetName = checkbox.dataset.assetName;
            const assetCode = checkbox.dataset.assetCode;
            
            html += `
                <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-sm text-gray-900 truncate">${assetName}</div>
                        <div class="text-xs text-gray-500 font-mono">${assetCode}</div>
                    </div>
                    <button type="button" onclick="removeFromCart('${assetId}')" 
                            class="text-red-600 hover:text-red-800 ml-2 flex-shrink-0">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
            `;
        }
    });
    
    selectedAssetsList.innerHTML = html;
    totalItemsSpan.textContent = selectedAssets.size;
}

function updateFormVisibility() {
    const borrowingForm = document.getElementById('borrowingForm');
    const hiddenInputs = document.getElementById('hiddenInputs');
    
    if (selectedAssets.size === 0) {
        borrowingForm.style.display = 'none';
        return;
    }
    
    borrowingForm.style.display = 'block';
    
    // Generate hidden inputs for form submission
    let html = '';
    let index = 0;
    selectedAssets.forEach(assetId => {
        html += `
            <input type="hidden" name="items[${index}][asset_id]" value="${assetId}">
        `;
        index++;
    });
    
    hiddenInputs.innerHTML = html;
}

function clearCart() {
    selectedAssets.clear();
    clearSelections();
    
    // Uncheck all checkboxes
    const checkboxes = document.querySelectorAll('.asset-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    
    updateCartDisplay();
    updateFormVisibility();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadSelections();
    updateCartDisplay();
    updateFormVisibility();
    toggleCustomLocation(); // Initialize custom location fields
});

// Toggle custom location fields
function toggleCustomLocation() {
    const locationSelect = document.getElementById('location_id');
    const customFields = document.getElementById('customLocationFields');
    const customBuilding = document.getElementById('custom_building');
    const customFloor = document.getElementById('custom_floor');
    const customRoom = document.getElementById('custom_room');
    
    if (locationSelect.value === 'custom') {
        customFields.style.display = 'block';
        // Make custom fields required
        customBuilding.required = true;
        customFloor.required = true;
        customRoom.required = true;
        // Remove required from location_id
        locationSelect.required = false;
    } else {
        customFields.style.display = 'none';
        // Remove required from custom fields
        customBuilding.required = false;
        customFloor.required = false;
        customRoom.required = false;
        // Add required back to location_id
        locationSelect.required = true;
    }
}
</script>
@endsection 