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

function generateQR(assetCode) {
    // Generate QR code for the asset
    const qrUrl = `/gsu/qrcode/asset/${assetCode}`;
    window.open(qrUrl, '_blank');
}

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
                <div><strong>Purchase Date:</strong> ${asset.purchase_date}</div>
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
                <a href="/gsu/assets/${asset.id}" class="px-3 py-1 bg-blue-100 text-blue-600 rounded text-sm hover:bg-blue-200 transition-colors"><i class="fas fa-eye mr-1"></i>View</a>
                <a href="/gsu/assets/${asset.id}/edit" class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded text-sm hover:bg-yellow-200 transition-colors"><i class="fas fa-edit mr-1"></i>Edit</a>
                <a href="/gsu/qrcode/asset/${asset.asset_code}" target="_blank" class="px-3 py-1 bg-green-100 text-green-700 rounded text-sm hover:bg-green-200 transition-colors"><i class="fas fa-qrcode mr-1"></i>QR</a>
            </div>
        </div>
    `;
    resultsContainer.innerHTML = html;
}
</script>
@endsection 