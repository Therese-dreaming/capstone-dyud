@extends('layouts.gsu')

@section('content')
<div class="container mx-auto py-4 md:py-8 px-2 sm:px-4 overflow-x-hidden">
    <!-- GSU QR Scanner Header -->
    <div class="bg-gradient-to-r from-red-800 to-red-900 text-white p-4 md:p-6 rounded-xl shadow-lg mb-4 md:mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center space-x-3 md:space-x-4">
                <div class="bg-white/20 p-2 md:p-3 rounded-full flex-shrink-0">
                    <i class="fas fa-qrcode text-xl md:text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-xl md:text-2xl lg:text-3xl font-bold">QR Scanner</h1>
                    <p class="text-red-100 text-xs md:text-sm">GSU Asset QR Code Scanner</p>
                </div>
            </div>
            <div class="flex items-center space-x-2 md:space-x-3 w-full sm:w-auto">
                <button onclick="startScanner()" class="flex-1 sm:flex-initial bg-white/20 hover:bg-white/30 text-white px-3 md:px-4 py-2 rounded-lg transition-colors text-xs md:text-sm">
                    <i class="fas fa-camera mr-1 md:mr-2"></i><span class="hidden sm:inline">Start </span>Scan
                </button>
                <button onclick="stopScanner()" class="flex-1 sm:flex-initial bg-white/20 hover:bg-white/30 text-white px-3 md:px-4 py-2 rounded-lg transition-colors text-xs md:text-sm">
                    <i class="fas fa-stop mr-1 md:mr-2"></i>Stop
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-8">
        <!-- Scanner Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 md:px-6 py-3 md:py-4 border-b border-gray-200">
                <h2 class="text-base md:text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-camera text-red-600"></i>
                    QR Code Scanner
                </h2>
            </div>
            <div class="p-4 md:p-6">
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
                    <video id="scanner-video" class="hidden w-full rounded-lg" autoplay playsinline muted></video>
                    
                    <!-- Scanner canvas for processing -->
                    <canvas id="scanner-canvas" class="hidden"></canvas>

                    <!-- Scan complete panel -->
                    <div id="scan-complete" class="hidden bg-green-50 rounded-lg p-8 text-center border-2 border-green-200">
                        <div class="mx-auto mb-4 w-16 h-16 rounded-full bg-green-100 flex items-center justify-center">
                            <i class="fas fa-check text-2xl text-green-600"></i>
                        </div>
                        <h3 class="text-lg font-bold text-green-800 mb-2">Scan is complete</h3>
                        <p class="text-green-700 text-sm mb-4">You can review the results or scan another asset.</p>
                        <button onclick="resetScannerUIForRescan()" class="bg-red-800 text-white px-6 py-2 rounded-lg hover:bg-red-900 transition-colors">
                            <i class="fas fa-redo mr-2"></i>Scan Again
                        </button>
                    </div>
                </div>
                
                <!-- Scanner controls -->
                <div class="mt-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                    <div class="text-xs md:text-sm text-gray-600">
                        <span id="scanner-status">Ready to scan</span>
                    </div>
                    <div class="flex space-x-2 w-full sm:w-auto">
                        <button onclick="toggleFlash()" class="flex-1 sm:flex-initial px-2 md:px-3 py-1 bg-blue-100 text-blue-600 rounded text-xs md:text-sm hover:bg-blue-200 transition-colors">
                            <i class="fas fa-lightbulb mr-1"></i>Flash
                        </button>
                        <button onclick="switchCamera()" class="flex-1 sm:flex-initial px-2 md:px-3 py-1 bg-green-100 text-green-600 rounded text-xs md:text-sm hover:bg-green-200 transition-colors">
                            <i class="fas fa-sync mr-1"></i>Switch
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 md:px-6 py-3 md:py-4 border-b border-gray-200">
                <h2 class="text-base md:text-lg font-semibold text-gray-900 flex items-center gap-2">
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
    <div class="mt-4 md:mt-8 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 md:px-6 py-3 md:py-4 border-b border-gray-200">
            <h2 class="text-base md:text-lg font-semibold text-gray-900 flex items-center gap-2">
                <i class="fas fa-keyboard text-red-600"></i>
                Manual Asset Lookup
            </h2>
        </div>
        <div class="p-4 md:p-6">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 md:gap-4">
                <div class="flex-1">
                    <input type="text" id="manual-asset-code" 
                           placeholder="Enter asset code..." 
                           class="w-full px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm md:text-base">
                </div>
                <button onclick="lookupAsset()" class="bg-red-800 text-white px-4 md:px-6 py-2 rounded-lg hover:bg-red-900 transition-colors text-sm md:text-base whitespace-nowrap">
                    <i class="fas fa-search mr-1 md:mr-2"></i>Lookup
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

<!-- Mark Asset as Found Modal -->
<div id="found-modal" class="fixed inset-0 z-[9999] hidden" style="display: none;">
    <div class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full relative z-10 transform transition-all duration-300 scale-100">
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-5 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white/20 p-2 rounded-lg">
                            <i class="fas fa-check-circle text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Mark Asset as Found</h3>
                            <p class="text-green-100 text-sm">Resolve lost asset status</p>
                        </div>
                    </div>
                    <button onclick="closeFoundModal()" class="text-white/80 hover:text-white hover:bg-white/20 p-2 rounded-lg transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            
            <!-- Content -->
            <div class="p-6">
                <form id="found-form">
                    <input type="hidden" id="found-asset-code" name="asset_code">
                    
                    <!-- Asset Code Display -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-barcode mr-2 text-green-600"></i>Asset Code
                        </label>
                        <div class="bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3">
                            <span id="found-asset-code-display" class="text-lg font-mono font-bold text-gray-800"></span>
                        </div>
                    </div>
                    
                    <!-- Found Date -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar mr-2 text-green-600"></i>Found Date
                        </label>
                        <input type="date" 
                               id="found-date" 
                               name="found_date"
                               value="{{ now()->format('Y-m-d') }}"
                               max="{{ now()->format('Y-m-d') }}"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                               required>
                    </div>
                    
                    <!-- Found Notes -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-sticky-note mr-2 text-green-600"></i>Found Notes (Optional)
                        </label>
                        <textarea id="found-notes" 
                                  name="found_notes"
                                  rows="3"
                                  class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                  placeholder="Enter any notes about where or how the asset was found..."></textarea>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex space-x-3">
                        <button type="button" 
                                onclick="closeFoundModal()" 
                                class="flex-1 px-4 py-3 text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors font-medium">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:from-green-700 hover:to-green-800 transition-all font-medium shadow-lg">
                            <i class="fas fa-check-circle mr-2"></i>Mark as Found
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Success Toast Notification -->
<div id="success-toast" class="fixed top-4 right-4 md:top-6 md:right-6 z-[9999] hidden transform translate-x-[calc(100%+2rem)] transition-transform duration-500 ease-in-out max-w-[calc(100vw-2rem)] md:max-w-md">
    <div class="bg-white rounded-xl shadow-2xl border border-green-200 overflow-hidden">
        <div class="bg-gradient-to-r from-green-500 to-green-600 px-4 md:px-6 py-3 md:py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2 md:space-x-3">
                    <div class="bg-white/20 p-1.5 md:p-2 rounded-full flex-shrink-0">
                        <i class="fas fa-check-circle text-white text-base md:text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-white font-bold text-sm md:text-lg">Deployment Successful!</h4>
                        <p class="text-green-100 text-xs md:text-sm">Asset has been deployed</p>
                    </div>
                </div>
                <button onclick="hideSuccessToast()" class="text-white/80 hover:text-white hover:bg-white/20 p-2 rounded-lg transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="px-4 md:px-6 py-3 md:py-4">
            <p id="toast-message" class="text-gray-700 text-xs md:text-sm leading-relaxed">
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
let videoDevices = [];
let currentDeviceIndex = 0;
let torchOn = false;
let currentVideoTrack = null;
let lastScannedCode = null;
let lastScanAt = 0;
const SCAN_COOLDOWN_MS = 3000;

// Scanner functions
async function startScanner(deviceId = null) {
    if (isScanning) return;
    try {
        // Ensure we have a list of video devices
        if (videoDevices.length === 0) {
            // A dummy getUserMedia call is often needed before enumerateDevices returns labels
            try {
                const tempStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                tempStream.getTracks().forEach(t => t.stop());
            } catch (_) {}
            const devices = await navigator.mediaDevices.enumerateDevices();
            videoDevices = devices.filter(d => d.kind === 'videoinput');
            // Prefer a back/environment camera if available
            const backIndex = videoDevices.findIndex(d => /back|rear|environment/i.test(d.label));
            currentDeviceIndex = backIndex >= 0 ? backIndex : 0;
        }

        // Determine constraints
        const useDeviceId = deviceId || (videoDevices[currentDeviceIndex] && videoDevices[currentDeviceIndex].deviceId) || undefined;
        const constraints = {
            video: useDeviceId ? { deviceId: { exact: useDeviceId } } : { facingMode: 'environment' }
        };

        // If a stream is already active, stop it first (safety)
        if (currentStream) {
            try { currentStream.getTracks().forEach(t => t.stop()); } catch (_) {}
        }

        const stream = await navigator.mediaDevices.getUserMedia(constraints);
        currentStream = stream;
        currentVideoTrack = stream.getVideoTracks()[0] || null;

        const video = document.getElementById('scanner-video');
        video.srcObject = stream;
        video.classList.remove('hidden');
        // Ensure playback begins when metadata is ready (fixes white/blank video on switch)
        video.onloadedmetadata = () => {
            try { video.play(); } catch(_) {}
        };

        // Hide placeholder
        document.querySelector('#scanner-container .bg-gray-100').classList.add('hidden');
        // Hide scan complete panel if visible
        const completePanel = document.getElementById('scan-complete');
        if (completePanel) completePanel.classList.add('hidden');

        // Reset torch state when (re)starting
        torchOn = false;

        isScanning = true;
        document.getElementById('scanner-status').textContent = 'Scanning...';

        // Start scanning loop after a brief tick to ensure frames are available
        setTimeout(() => requestAnimationFrame(scanLoop), 50);
    } catch (err) {
        console.error('Error accessing camera:', err);
        alert('Unable to access camera. Please check permissions.');
    }
}

function stopScanner() {
    if (currentStream) {
        currentStream.getTracks().forEach(track => track.stop());
        currentStream = null;
    }
    currentVideoTrack = null;
    torchOn = false;
    
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
                const raw = result.data.trim();
                const assetCode = parseAssetCodeFromQR(raw);

                const now = Date.now();
                if (assetCode && !(assetCode === lastScannedCode && (now - lastScanAt) < SCAN_COOLDOWN_MS)) {
                    lastScannedCode = assetCode;
                    lastScanAt = now;
                    isScanning = false;
                    document.getElementById('scanner-status').textContent = 'QR detected';
                    fetchAssetDetails(assetCode);
                    return;
                }
            }
        }
    } catch (e) {
        console.warn('QR decode error:', e);
    }
    requestAnimationFrame(scanLoop);
}

async function toggleFlash() {
    try {
        if (!currentVideoTrack) {
            alert('Start the scanner first.');
            return;
        }
        const capabilities = currentVideoTrack.getCapabilities ? currentVideoTrack.getCapabilities() : {};
        // Some browsers expose torch via fillLightMode
        const torchCapable = capabilities.torch || (Array.isArray(capabilities.fillLightMode) && capabilities.fillLightMode.includes('flash'));
        if (!torchCapable) {
            alert('Flashlight is not supported on this device/browser.');
            return;
        }
        torchOn = !torchOn;
        const advanced = capabilities.torch ? { torch: torchOn } : { fillLightMode: torchOn ? 'flash' : 'off' };
        await currentVideoTrack.applyConstraints({ advanced: [advanced] });
        document.getElementById('scanner-status').textContent = torchOn ? 'Flashlight on' : 'Flashlight off';
    } catch (e) {
        console.warn('Torch toggle failed:', e);
        alert('Unable to toggle flashlight on this device.');
    }
}

function switchCamera() {
    try {
        if (videoDevices.length <= 1) {
            alert('No alternate camera found.');
            return;
        }
        // Move to next camera
        currentDeviceIndex = (currentDeviceIndex + 1) % videoDevices.length;

        // Stop current stream before switching
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
        }
        currentVideoTrack = null;
        torchOn = false;
        // Stop scanning loop so startScanner can re-init
        isScanning = false;

        const nextDeviceId = videoDevices[currentDeviceIndex].deviceId;
        startScanner(nextDeviceId);
        document.getElementById('scanner-status').textContent = 'Switched camera';
    } catch (e) {
        console.warn('Camera switch failed:', e);
        alert('Unable to switch camera.');
    }
}

// Try to normalize the QR content to an asset code
function parseAssetCodeFromQR(data) {
    // 1) JSON payload case: { asset_code: "..." }
    try {
        const obj = JSON.parse(data);
        if (obj && (obj.asset_code || obj.code)) {
            return String(obj.asset_code || obj.code).trim();
        }
    } catch (_) {}

    // 2) URL case: extract ?code=... or last path segment
    try {
        const url = new URL(data);
        const qp = url.searchParams.get('code') || url.searchParams.get('asset_code');
        if (qp) return String(qp).trim();
        const parts = url.pathname.split('/').filter(Boolean);
        if (parts.length) return parts[parts.length - 1].trim();
    } catch (_) {}

    // 3) Plain text case: return as-is
    return data;
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
        // Prefer GSU API which returns full asset details without user-ownership restriction
        const res = await fetch(`/gsu/api/assets/code/${encodeURIComponent(assetCode)}`);
        if (!res.ok) throw new Error('Asset not found');
        const asset = await res.json();
        renderScanResult(asset);
        document.getElementById('scanner-status').textContent = 'Scan complete';
        // Stop camera and show completion design
        stopCameraOnly();
        showScanCompletePanel();
    } catch (err) {
        document.getElementById('scanner-status').textContent = `Asset not found for code: ${assetCode}`;
        // Resume scanning after a short cooldown without spamming alerts
        setTimeout(() => {
            isScanning = true;
            requestAnimationFrame(scanLoop);
        }, 800);
    }
}

// Stop only the camera stream without resetting the placeholder UI
function stopCameraOnly() {
    try {
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
        }
    } catch (_) {}
    currentStream = null;
    currentVideoTrack = null;
    torchOn = false;
    isScanning = false;
    const video = document.getElementById('scanner-video');
    if (video) video.classList.add('hidden');
}

function showScanCompletePanel() {
    // Hide default placeholder
    const placeholder = document.querySelector('#scanner-container .bg-gray-100');
    if (placeholder) placeholder.classList.add('hidden');
    // Show complete panel
    const completePanel = document.getElementById('scan-complete');
    if (completePanel) completePanel.classList.remove('hidden');
}

function resetScannerUIForRescan() {
    // Hide complete panel, show placeholder and allow starting again
    const completePanel = document.getElementById('scan-complete');
    if (completePanel) completePanel.classList.add('hidden');
    const placeholder = document.querySelector('#scanner-container .bg-gray-100');
    if (placeholder) placeholder.classList.remove('hidden');
    document.getElementById('scanner-status').textContent = 'Ready to scan';
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
                ${asset.status === 'Lost' ? 
                    `<button onclick="openFoundModal('${asset.asset_code}')" class="px-3 py-1 bg-green-100 text-green-700 rounded text-sm hover:bg-green-200 transition-colors font-semibold"><i class="fas fa-check-circle mr-1"></i>Mark as Found</button>` :
                    (asset.location_id ? 
                        `<span class="px-3 py-1 bg-gray-100 text-gray-500 rounded text-sm cursor-not-allowed"><i class="fas fa-check-circle mr-1"></i>Already Deployed</span>` :
                        `<button onclick="openDeployModal('${asset.id}', '${asset.asset_code}')" class="px-3 py-1 bg-purple-100 text-purple-700 rounded text-sm hover:bg-purple-200 transition-colors"><i class="fas fa-map-marker-alt mr-1"></i>Deploy</button>`
                    )
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
    
    // Ensure toast is visible and slide-in
    toast.classList.remove('hidden');
    toast.classList.remove('translate-x-[calc(100%+2rem)]');
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
    toast.classList.add('translate-x-[calc(100%+2rem)]');
    
    // Reset progress bar after animation
    setTimeout(() => {
        progressBar.style.width = '100%';
        progressBar.style.transition = 'none';
        toast.classList.add('hidden');
    }, 500);
}

// Mark as Found modal functions
function openFoundModal(assetCode) {
    document.getElementById('found-asset-code').value = assetCode;
    document.getElementById('found-asset-code-display').textContent = assetCode;
    document.getElementById('found-date').value = '{{ now()->format("Y-m-d") }}';
    document.getElementById('found-notes').value = '';
    const modal = document.getElementById('found-modal');
    modal.classList.remove('hidden');
    modal.style.display = 'block';
}

function closeFoundModal() {
    const modal = document.getElementById('found-modal');
    modal.classList.add('hidden');
    modal.style.display = 'none';
    document.getElementById('found-form').reset();
}

// Found form submission
document.getElementById('found-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const assetCode = formData.get('asset_code');
    const foundDate = formData.get('found_date');
    const foundNotes = formData.get('found_notes');
    
    if (!foundDate) {
        alert('Please select a found date');
        return;
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('/gsu/asset-scanner/mark-found', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                asset_code: assetCode,
                found_date: foundDate,
                found_notes: foundNotes || ''
            })
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            showSuccessToast(data.message || 'Asset marked as found successfully!');
            closeFoundModal();
            // Refresh the asset details
            setTimeout(() => {
                fetchAssetDetails(assetCode);
            }, 1000);
        } else {
            throw new Error(data.message || 'Failed to mark asset as found');
        }
    } catch (error) {
        console.error('Error marking asset as found:', error);
        alert(error.message || 'Failed to mark asset as found. Please try again.');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});
</script>
@endsection 