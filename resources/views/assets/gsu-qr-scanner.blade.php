@extends('layouts.gsu')

@section('content')
<div class="container mx-auto py-8">
    <!-- GSU QR Scanner Header -->
    <div class="bg-gradient-to-r from-red-800 to-red-900 text-white p-6 rounded-xl shadow-lg mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-white/20 p-3 rounded-full">
                    <i class="fas fa-qrcode text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold">QR Scanner</h1>
                    <p class="text-red-100 text-sm md:text-base">GSU Asset QR Code Scanner</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="startScanner()" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-camera mr-2"></i>Start Scanner
                </button>
                <button onclick="stopScanner()" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-stop mr-2"></i>Stop Scanner
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Scanner Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-camera text-red-600"></i>
                    QR Code Scanner
                </h2>
            </div>
            <div class="p-6">
                <div id="scanner-container" class="relative">
                    <!-- Scanner placeholder -->
                    <div class="bg-gray-100 rounded-lg p-8 text-center border-2 border-dashed border-gray-300">
                        <i class="fas fa-qrcode text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-600 mb-2">QR Scanner Ready</h3>
                        <p class="text-gray-500 text-sm mb-4">Click "Start Scanner" to begin scanning asset QR codes</p>
                        <button onclick="startScanner()" class="bg-red-800 text-white px-6 py-2 rounded-lg hover:bg-red-900 transition-colors">
                            <i class="fas fa-camera mr-2"></i>Start Scanner
                        </button>
                    </div>
                    
                    <!-- Scanner video element (hidden initially) -->
                    <video id="scanner-video" class="hidden w-full rounded-lg" autoplay></video>
                    
                    <!-- Scanner canvas for processing -->
                    <canvas id="scanner-canvas" class="hidden"></canvas>
                </div>
                
                <!-- Scanner controls -->
                <div class="mt-4 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <span id="scanner-status">Ready to scan</span>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="toggleFlash()" class="px-3 py-1 bg-blue-100 text-blue-600 rounded text-sm hover:bg-blue-200 transition-colors">
                            <i class="fas fa-lightbulb mr-1"></i>Flash
                        </button>
                        <button onclick="switchCamera()" class="px-3 py-1 bg-green-100 text-green-600 rounded text-sm hover:bg-green-200 transition-colors">
                            <i class="fas fa-sync mr-1"></i>Switch Camera
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-search text-red-600"></i>
                    Scan Results
                </h2>
            </div>
            <div class="p-6">
                <div id="scan-results" class="space-y-4">
                    <!-- No results message -->
                    <div id="no-results" class="text-center py-8">
                        <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-600 mb-2">No Scans Yet</h3>
                        <p class="text-gray-500 text-sm">Scan an asset QR code to see results here</p>
                    </div>
                    
                    <!-- Results will be populated here -->
                    <div id="results-container" class="hidden">
                        <!-- Dynamic results will be added here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Manual Entry Section -->
    <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <i class="fas fa-keyboard text-red-600"></i>
                Manual Asset Lookup
            </h2>
        </div>
        <div class="p-6">
            <div class="flex items-center space-x-4">
                <div class="flex-1">
                    <input type="text" id="manual-asset-code" 
                           placeholder="Enter asset code manually..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>
                <button onclick="lookupAsset()" class="bg-red-800 text-white px-6 py-2 rounded-lg hover:bg-red-900 transition-colors">
                    <i class="fas fa-search mr-2"></i>Lookup Asset
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Asset Details Modal -->
<div id="asset-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="bg-gradient-to-r from-red-50 to-red-100 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Asset Details</h3>
                    <button onclick="closeAssetModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div id="asset-modal-content" class="p-6">
                <!-- Asset details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Deploy Asset Modal -->
<div id="deploy-modal" class="fixed inset-0 z-[9999] hidden" style="display: none;">
    <div class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full relative z-10 transform transition-all duration-300 scale-100">
            <!-- Header -->
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-5 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white/20 p-2 rounded-lg">
                            <i class="fas fa-map-marker-alt text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Deploy Asset</h3>
                            <p class="text-purple-100 text-sm">Assign location to asset</p>
                        </div>
                    </div>
                    <button onclick="closeDeployModal()" class="text-white/80 hover:text-white hover:bg-white/20 p-2 rounded-lg transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            
            <!-- Content -->
            <div class="p-6">
                <form id="deploy-form">
                    <input type="hidden" id="deploy-asset-id" name="asset_id">
                    
                    <!-- Asset Code Display -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-barcode mr-2 text-purple-600"></i>Asset Code
                        </label>
                        <div class="bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3">
                            <span id="deploy-asset-code" class="text-lg font-mono font-bold text-gray-800"></span>
                        </div>
                    </div>
                    
                    <!-- Location Autocomplete -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-building mr-2 text-purple-600"></i>Deploy to Location
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="deploy-location-input" 
                                   placeholder="Type to search locations..." 
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                   autocomplete="off">
                            <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            
                            <!-- Suggestions Dropdown -->
                            <div id="location-suggestions" class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                                <!-- Suggestions will be populated here -->
                            </div>
                            
                            <!-- Error Message -->
                            <div id="location-error" class="mt-2 text-sm text-red-600 hidden">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                <span>Please select a valid location from the suggestions</span>
                            </div>
                        </div>
                        <input type="hidden" id="deploy-location-id" name="location_id">
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex space-x-3">
                        <button type="button" 
                                onclick="closeDeployModal()" 
                                class="flex-1 px-4 py-3 text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors font-medium">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-xl hover:from-purple-700 hover:to-purple-800 transition-all font-medium shadow-lg">
                            <i class="fas fa-map-marker-alt mr-2"></i>Deploy Asset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Success Toast Notification -->
<div id="success-toast" class="fixed top-6 right-6 z-[9999] transform translate-x-full transition-transform duration-500 ease-in-out">
    <div class="bg-white rounded-xl shadow-2xl border border-green-200 overflow-hidden max-w-md">
        <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 p-2 rounded-full">
                        <i class="fas fa-check-circle text-white text-lg"></i>
                    </div>
                    <div>
                        <h4 class="text-white font-bold text-lg">Deployment Successful!</h4>
                        <p class="text-green-100 text-sm">Asset has been deployed</p>
                    </div>
                </div>
                <button onclick="hideSuccessToast()" class="text-white/80 hover:text-white hover:bg-white/20 p-2 rounded-lg transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="px-6 py-4">
            <p id="toast-message" class="text-gray-700 text-sm leading-relaxed">
                <!-- Message will be inserted here -->
            </p>
            <div class="mt-4 flex items-center space-x-2 text-xs text-gray-500">
                <i class="fas fa-info-circle"></i>
                <span>The asset is now available in the system inventory</span>
            </div>
        </div>
        <!-- Progress bar -->
        <div class="h-1 bg-gray-100">
            <div id="toast-progress" class="h-full bg-gradient-to-r from-green-400 to-green-500 transition-all duration-[5000ms] ease-linear w-full"></div>
        </div>
    </div>
</div>

<script>
let scanner = null;
let currentStream = null;
let isScanning = false;

// Scanner functions
function startScanner() {
    if (isScanning) return;
    
    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
        .then(function(stream) {
            currentStream = stream;
            const video = document.getElementById('scanner-video');
            video.srcObject = stream;
            video.classList.remove('hidden');
            
            // Hide placeholder
            document.querySelector('#scanner-container .bg-gray-100').classList.add('hidden');
            
            isScanning = true;
            document.getElementById('scanner-status').textContent = 'Scanning...';
            
            // Start scanning loop
            scanLoop();
        })
        .catch(function(err) {
            console.error('Error accessing camera:', err);
            alert('Unable to access camera. Please check permissions.');
        });
}

function stopScanner() {
    if (currentStream) {
        currentStream.getTracks().forEach(track => track.stop());
        currentStream = null;
    }
    
    const video = document.getElementById('scanner-video');
    video.classList.add('hidden');
    document.querySelector('#scanner-container .bg-gray-100').classList.remove('hidden');
    
    isScanning = false;
    document.getElementById('scanner-status').textContent = 'Scanner stopped';
}

function scanLoop() {
    if (!isScanning) return;
    
    const video = document.getElementById('scanner-video');
    const canvas = document.getElementById('scanner-canvas');
    const context = canvas.getContext('2d');
    
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Try to decode with jsQR if available
    try {
        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
        if (window.jsQR) {
            const result = window.jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: 'dontInvert' });
            if (result && result.data) {
                isScanning = false;
                document.getElementById('scanner-status').textContent = 'QR detected';
                // Expect QR to contain asset code only
                const assetCode = result.data.trim();
                fetchAssetDetails(assetCode);
                return;
            }
        }
    } catch (e) {
        console.warn('QR decode error:', e);
    }
    requestAnimationFrame(scanLoop);
}

function toggleFlash() {
    // Implement flash toggle functionality
    console.log('Flash toggled');
}

function switchCamera() {
    // Implement camera switching functionality
    console.log('Camera switched');
}

function lookupAsset() {
    const assetCode = document.getElementById('manual-asset-code').value.trim();
    if (!assetCode) {
        alert('Please enter an asset code');
        return;
    }
    fetchAssetDetails(assetCode);
}

function viewAssetDetails(assetCode) {
    // Simulate loading asset details
    const modalContent = document.getElementById('asset-modal-content');
    modalContent.innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-4"></i>
            <p class="text-gray-600">Loading asset details...</p>
        </div>
    `;
    
    document.getElementById('asset-modal').classList.remove('hidden');
    
    // Simulate API call delay
    setTimeout(() => {
        modalContent.innerHTML = `
            <div class="space-y-4">
                <div class="text-center mb-6">
                    <div class="bg-red-100 p-4 rounded-full inline-block mb-4">
                        <i class="fas fa-box text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">${assetCode}</h3>
                    <p class="text-gray-600">Asset Details</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-2">Basic Information</h4>
                        <div class="space-y-2 text-sm">
                            <div><strong>Name:</strong> Sample Asset</div>
                            <div><strong>Category:</strong> Electronics</div>
                            <div><strong>Status:</strong> <span class="text-green-600 font-medium">Available</span></div>
                            <div><strong>Condition:</strong> Good</div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-2">Location</h4>
                        <div class="space-y-2 text-sm">
                            <div><strong>Building:</strong> Building A</div>
                            <div><strong>Floor:</strong> 1</div>
                            <div><strong>Room:</strong> 101</div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-900 mb-2">Purchase Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div><strong>Purchase Date:</strong> January 15, 2024</div>
                        <div><strong>Purchase Cost:</strong> $1,500.00</div>
                        <div><strong>Warranty:</strong> Until Dec 2025</div>
                    </div>
                </div>
            </div>
        `;
    }, 1000);
}

function closeAssetModal() {
    document.getElementById('asset-modal').classList.add('hidden');
}

// Deploy modal functions
let allLocations = [];
let selectedLocationId = null;

function openDeployModal(assetId, assetCode) {
    document.getElementById('deploy-asset-id').value = assetId;
    document.getElementById('deploy-asset-code').textContent = assetCode;
    document.getElementById('deploy-location-input').value = '';
    document.getElementById('deploy-location-id').value = '';
    selectedLocationId = null;
    hideLocationError();
    loadLocations();
    const modal = document.getElementById('deploy-modal');
    modal.classList.remove('hidden');
    modal.style.display = 'block';
}

function closeDeployModal() {
    const modal = document.getElementById('deploy-modal');
    modal.classList.add('hidden');
    modal.style.display = 'none';
    document.getElementById('deploy-form').reset();
    document.getElementById('deploy-location-input').value = '';
    document.getElementById('deploy-location-id').value = '';
    selectedLocationId = null;
    hideSuggestions();
    hideLocationError();
}

async function loadLocations() {
    try {
        const response = await fetch('/api/locations');
        allLocations = await response.json();
        setupLocationAutocomplete();
    } catch (error) {
        console.error('Error loading locations:', error);
        alert('Failed to load locations');
    }
}

function setupLocationAutocomplete() {
    const input = document.getElementById('deploy-location-input');
    const suggestions = document.getElementById('location-suggestions');
    
    input.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        hideLocationError();
        
        if (query.length === 0) {
            hideSuggestions();
            return;
        }
        
        const filtered = allLocations.filter(location => {
            const building = location.building?.toLowerCase() || '';
            const floor = location.floor?.toString() || '';
            const room = location.room?.toLowerCase() || '';
            const searchText = `${building} ${floor} ${room}`;
            return searchText.includes(query);
        });
        
        if (filtered.length > 0) {
            showSuggestions(filtered);
        } else {
            hideSuggestions();
        }
    });
    
    input.addEventListener('blur', function() {
        // Delay hiding suggestions to allow clicking on them
        setTimeout(() => {
            hideSuggestions();
        }, 200);
    });
    
    input.addEventListener('focus', function() {
        if (this.value.trim().length > 0) {
            const query = this.value.toLowerCase().trim();
            const filtered = allLocations.filter(location => {
                const building = location.building?.toLowerCase() || '';
                const floor = location.floor?.toString() || '';
                const room = location.room?.toLowerCase() || '';
                const searchText = `${building} ${floor} ${room}`;
                return searchText.includes(query);
            });
            if (filtered.length > 0) {
                showSuggestions(filtered);
            }
        }
    });
}

function showSuggestions(locations) {
    const suggestions = document.getElementById('location-suggestions');
    suggestions.innerHTML = '';
    
    locations.forEach(location => {
        const item = document.createElement('div');
        item.className = 'px-4 py-3 hover:bg-purple-50 cursor-pointer border-b border-gray-100 last:border-b-0';
        item.innerHTML = `
            <div class="flex items-center space-x-3">
                <i class="fas fa-building text-purple-600"></i>
                <div>
                    <div class="font-medium text-gray-900">${location.building}</div>
                    <div class="text-sm text-gray-500">Floor ${location.floor} - Room ${location.room}</div>
                </div>
            </div>
        `;
        
        item.addEventListener('click', function() {
            selectLocation(location);
        });
        
        suggestions.appendChild(item);
    });
    
    suggestions.classList.remove('hidden');
}

function hideSuggestions() {
    document.getElementById('location-suggestions').classList.add('hidden');
}

function selectLocation(location) {
    const input = document.getElementById('deploy-location-input');
    const hiddenInput = document.getElementById('deploy-location-id');
    
    input.value = `${location.building} - Floor ${location.floor} - Room ${location.room}`;
    hiddenInput.value = location.id;
    selectedLocationId = location.id;
    
    hideSuggestions();
    hideLocationError();
}

function showLocationError() {
    document.getElementById('location-error').classList.remove('hidden');
}

function hideLocationError() {
    document.getElementById('location-error').classList.add('hidden');
}

// Date formatting function
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });
}

function generateQR(assetCode) {
    // Generate QR code for the asset
    const qrUrl = `/gsu/qrcode/asset/${assetCode}`;
    window.open(qrUrl, '_blank');
}

// Deploy form submission
document.getElementById('deploy-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Validate location selection
    const locationInput = document.getElementById('deploy-location-input').value.trim();
    const locationId = document.getElementById('deploy-location-id').value;
    
    if (!locationId || !selectedLocationId) {
        showLocationError();
        return;
    }
    
    // Verify the input matches a valid location
    const isValidLocation = allLocations.some(location => 
        location.id == locationId && 
        `${location.building} - Floor ${location.floor} - Room ${location.room}` === locationInput
    );
    
    if (!isValidLocation) {
        showLocationError();
        return;
    }
    
    const formData = new FormData(this);
    const assetId = formData.get('asset_id');
    
    try {
        const response = await fetch(`/gsu/assets/${assetId}/location`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ location_id: locationId })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            showSuccessToast(data.success || 'Asset deployed successfully! The asset is now available in the system.');
            closeDeployModal();
            // Refresh the scan results to show updated location
            const assetCode = document.getElementById('deploy-asset-code').textContent;
            fetchAssetDetails(assetCode);
        } else {
            throw new Error(data.error || 'Failed to deploy asset');
        }
    } catch (error) {
        console.error('Error deploying asset:', error);
        alert('Failed to deploy asset. Please try again.');
    }
});

// Initialize scanner when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('GSU QR Scanner initialized');
    // Load jsQR if not present
    if (!window.jsQR) {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js';
        script.async = true;
        script.onload = () => console.log('jsQR loaded');
        document.body.appendChild(script);
    }
});

async function fetchAssetDetails(assetCode) {
    document.getElementById('scanner-status').textContent = `Fetching ${assetCode}...`;
    try {
        const res = await fetch(`/api/assets/code/${encodeURIComponent(assetCode)}`);
        if (!res.ok) throw new Error('Asset not found');
        const asset = await res.json();
        renderScanResult(asset);
        document.getElementById('scanner-status').textContent = 'Scan complete';
    } catch (err) {
        document.getElementById('scanner-status').textContent = 'Asset not found';
        alert('Asset not found for code: ' + assetCode);
        isScanning = true; // allow continue scanning
        requestAnimationFrame(scanLoop);
    }
}

function renderScanResult(asset) {
    const resultsContainer = document.getElementById('results-container');
    const noResults = document.getElementById('no-results');
    noResults.classList.add('hidden');
    resultsContainer.classList.remove('hidden');
    const html = `
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-semibold text-gray-900">${asset.asset_code}</h4>
                <span class="px-2 py-1 text-xs font-medium rounded-full ${asset.status === 'Available' ? 'bg-green-100 text-green-800' : (asset.status === 'In Use' ? 'bg-blue-100 text-blue-800' : (asset.status === 'Lost' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'))}">
                    ${asset.status}
                </span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div><strong>Name:</strong> ${asset.name}</div>
                <div><strong>Category:</strong> ${asset.category?.name ?? ''}</div>
                <div class="md:col-span-2"><strong>Location:</strong> ${asset.location ? `${asset.location.building} - Floor ${asset.location.floor} - Room ${asset.location.room}` : ''}</div>
                <div><strong>Condition:</strong> ${asset.condition}</div>
                <div><strong>Purchase Date:</strong> ${formatDate(asset.purchase_date)}</div>
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
                <a href="/gsu/assets/${asset.id}" class="px-3 py-1 bg-blue-100 text-blue-600 rounded text-sm hover:bg-blue-200 transition-colors"><i class="fas fa-eye mr-1"></i>View</a>
                ${asset.location_id ? 
                    `<span class="px-3 py-1 bg-gray-100 text-gray-500 rounded text-sm cursor-not-allowed"><i class="fas fa-check-circle mr-1"></i>Already Deployed</span>` :
                    `<button onclick="openDeployModal('${asset.id}', '${asset.asset_code}')" class="px-3 py-1 bg-purple-100 text-purple-700 rounded text-sm hover:bg-purple-200 transition-colors"><i class="fas fa-map-marker-alt mr-1"></i>Deploy</button>`
                }
                <a href="/gsu/qrcode/asset/${asset.asset_code}" target="_blank" class="px-3 py-1 bg-green-100 text-green-700 rounded text-sm hover:bg-green-200 transition-colors"><i class="fas fa-qrcode mr-1"></i>QR</a>
            </div>
        </div>
    `;
    resultsContainer.innerHTML = html;
}

// Toast notification functions
function showSuccessToast(message) {
    const toast = document.getElementById('success-toast');
    const messageElement = document.getElementById('toast-message');
    const progressBar = document.getElementById('toast-progress');
    
    // Set the message
    messageElement.textContent = message;
    
    // Reset progress bar
    progressBar.style.width = '100%';
    progressBar.style.transition = 'none';
    
    // Show toast with slide-in animation
    toast.classList.remove('translate-x-full');
    toast.classList.add('translate-x-0');
    
    // Start progress bar animation after a brief delay
    setTimeout(() => {
        progressBar.style.transition = 'width 5000ms ease-linear';
        progressBar.style.width = '0%';
    }, 100);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        hideSuccessToast();
    }, 5000);
}

function hideSuccessToast() {
    const toast = document.getElementById('success-toast');
    const progressBar = document.getElementById('toast-progress');
    
    // Hide toast with slide-out animation
    toast.classList.remove('translate-x-0');
    toast.classList.add('translate-x-full');
    
    // Reset progress bar after animation
    setTimeout(() => {
        progressBar.style.width = '100%';
        progressBar.style.transition = 'none';
    }, 500);
}
</script>
@endsection 