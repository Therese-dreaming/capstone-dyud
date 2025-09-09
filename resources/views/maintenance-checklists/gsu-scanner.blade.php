@extends('layouts.gsu')

@section('content')
<div class="max-w-6xl mx-auto py-4 lg:py-8">
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 flex items-center gap-3">
                <i class="fas fa-qrcode text-red-800"></i>
                Asset Scanner
            </h1>
            <p class="text-gray-600 mt-1">{{ $checklist->room_number }} - {{ $checklist->school_year }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('maintenance-checklists.show', $checklist) }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> <span class="hidden sm:inline">Back</span>
            </a>
        </div>
    </div>

    <!-- Progress Section -->
    <div class="bg-white rounded-lg shadow-lg p-4 lg:p-6 mb-6">
        <h2 class="text-lg lg:text-xl font-semibold text-gray-800 mb-4">Scanning Progress</h2>
        @php
            $progress = $checklist->scanning_progress;
        @endphp
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-4">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $progress['total'] }}</div>
                <div class="text-sm text-blue-800">Total Assets</div>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ $progress['scanned'] }}</div>
                <div class="text-sm text-green-800">Scanned</div>
            </div>
            <div class="text-center p-4 bg-red-50 rounded-lg">
                <div class="text-2xl font-bold text-red-600">{{ $progress['missing'] }}</div>
                <div class="text-sm text-red-800">Missing</div>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <div class="text-2xl font-bold text-yellow-600">{{ $progress['remaining'] }}</div>
                <div class="text-sm text-yellow-800">Remaining</div>
            </div>
        </div>
        
        <!-- Progress Bar -->
        <div class="w-full bg-gray-200 rounded-full h-4">
            <div class="bg-blue-600 h-4 rounded-full transition-all duration-300" 
                 style="width: {{ $progress['percentage'] }}%"></div>
        </div>
        <p class="text-sm text-gray-600 mt-2">{{ $progress['percentage'] }}% Complete</p>
    </div>

    <!-- Scanner Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
        <!-- QR Scanner -->
        <div class="bg-white rounded-lg shadow-lg p-4 lg:p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">QR Code Scanner</h3>
            
            <div id="scanner-container" class="mb-4">
                <div id="qr-reader" style="width: 100%;"></div>
            </div>
            
            <div class="text-center">
                <button id="start-scanner" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    <i class="fas fa-camera"></i> Start Scanner
                </button>
                <button id="stop-scanner" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 ml-2" style="display: none;">
                    <i class="fas fa-stop"></i> Stop Scanner
                </button>
            </div>
        </div>

        <!-- Manual Entry -->
        <div class="bg-white rounded-lg shadow-lg p-4 lg:p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Manual Entry</h3>
            
            <form id="manual-scan-form">
                @csrf
                <input type="hidden" name="maintenance_checklist_id" value="{{ $checklist->id }}">
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Asset Code</label>
                    <input type="text" name="asset_code" id="manual-asset-code" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" 
                           placeholder="Enter asset code manually">
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">End Status</label>
                    <div class="grid grid-cols-1 gap-3">
                        <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="end_status" value="OK" class="sr-only" required>
                            <div class="flex items-center w-full">
                                <div class="w-4 h-4 border-2 border-gray-300 rounded-full mr-3 flex items-center justify-center">
                                    <div class="w-2 h-2 bg-green-600 rounded-full hidden"></div>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-800">OK</div>
                                    <div class="text-sm text-gray-500">Asset is in good condition</div>
                                </div>
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="end_status" value="FOR REPAIR" class="sr-only" required>
                            <div class="flex items-center w-full">
                                <div class="w-4 h-4 border-2 border-gray-300 rounded-full mr-3 flex items-center justify-center">
                                    <div class="w-2 h-2 bg-yellow-600 rounded-full hidden"></div>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-800">FOR REPAIR</div>
                                    <div class="text-sm text-gray-500">Asset needs maintenance or repair</div>
                                </div>
                                <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="end_status" value="FOR REPLACEMENT" class="sr-only" required>
                            <div class="flex items-center w-full">
                                <div class="w-4 h-4 border-2 border-gray-300 rounded-full mr-3 flex items-center justify-center">
                                    <div class="w-2 h-2 bg-red-600 rounded-full hidden"></div>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-800">FOR REPLACEMENT</div>
                                    <div class="text-sm text-gray-500">Asset needs to be replaced</div>
                                </div>
                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Notes</label>
                    <textarea name="notes" id="manual-notes" rows="3" 
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800"
                              placeholder="Optional notes"></textarea>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-2">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex-1">
                        <i class="fas fa-check"></i> Scan Asset
                    </button>
                    <button type="button" id="mark-missing-btn" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-times"></i> Mark Missing
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Unscanned Assets List -->
    @if($checklist->unscanned_assets->count() > 0)
    <div class="bg-white rounded-lg shadow-lg p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Remaining Assets to Scan</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($checklist->unscanned_assets as $item)
            <div class="border border-gray-300 rounded-lg p-4 bg-yellow-50">
                <div class="font-semibold text-gray-800">{{ $item->asset_code }}</div>
                <div class="text-sm text-gray-600">{{ $item->particulars }}</div>
                <div class="text-xs text-gray-500 mt-1">Start Status: {{ $item->start_status }}</div>
                @if($item->location_name)
                <div class="text-xs text-blue-600 mt-1">
                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $item->location_name }}
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Missing Assets List -->
    @if($checklist->missing_assets->count() > 0)
    <div class="bg-white rounded-lg shadow-lg p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Missing Assets</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($checklist->missing_assets as $item)
            <div class="border border-gray-300 rounded-lg p-4 bg-red-50">
                <div class="font-semibold text-gray-800">{{ $item->asset_code }}</div>
                <div class="text-sm text-gray-600">{{ $item->particulars }}</div>
                <div class="text-xs text-red-600 mt-1">Reason: {{ $item->missing_reason }}</div>
                @if($item->location_name)
                <div class="text-xs text-blue-600 mt-1">
                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $item->location_name }}
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Completion Footer (Visible and Required) -->
    @if($checklist->status === 'in_progress')
    <div class="mt-6">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Completion Details (Required)</h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Checked By (Instructor/Representative) -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Checked By</label>
                    <input type="text" id="checked_by" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" value="{{ auth()->user()->name }}" placeholder="Enter name" required>
                    <div class="mt-3">
                        <label class="block text-gray-700 font-semibold mb-2">Checked By Signature</label>
                        <div class="border border-gray-300 rounded-lg p-3 bg-gray-50">
                            <div class="w-full h-40 bg-white border border-gray-200 rounded" style="height: 160px;">
                                <canvas id="checked-signature-pad" class="w-full h-full"></canvas>
                            </div>
                            <div class="mt-2 flex justify-end">
                                <button type="button" id="clear-checked-signature" class="text-sm px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded">Clear</button>
                            </div>
                            <input type="hidden" id="checked_by_signature">
                        </div>
                    </div>
                </div>
                <!-- GSU Staff -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">GSU Staff</label>
                    <input type="text" id="gsu_staff" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" value="{{ auth()->user()->name }}" placeholder="Enter name" required>
                    <div class="mt-3">
                        <label class="block text-gray-700 font-semibold mb-2">GSU Staff Signature</label>
                        <div class="border border-gray-300 rounded-lg p-3 bg-gray-50">
                            <div class="w-full h-40 bg-white border border-gray-200 rounded" style="height: 160px;">
                                <canvas id="gsu-signature-pad" class="w-full h-full"></canvas>
                            </div>
                            <div class="mt-2 flex justify-end">
                                <button type="button" id="clear-gsu-signature" class="text-sm px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded">Clear</button>
                            </div>
                            <input type="hidden" id="gsu_staff_signature">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <p class="text-gray-600 mb-4">
                    @if($checklist->unscanned_assets->count() > 0)
                        You have {{ $checklist->unscanned_assets->count() }} unscanned assets. These will be automatically marked as lost when you complete the checklist.
                    @else
                        All assets have been processed. You can now complete the maintenance checklist.
                    @endif
                </p>
                <div class="text-center">
                    <button type="button" id="complete-maintenance-btn" class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold py-3 px-8 rounded-lg transition duration-200 flex items-center gap-2 mx-auto">
                        <i class="fas fa-check-circle"></i> Complete Maintenance Checklist
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Success/Error Messages -->
<div id="message-container" class="fixed top-4 right-4 z-50"></div>

<!-- Missing Asset Modal -->
<div id="missing-asset-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Mark Asset as Lost</h3>
            <form id="missing-asset-form">
                @csrf
                <input type="hidden" name="maintenance_checklist_id" value="{{ $checklist->id }}">
                <input type="hidden" name="asset_code" id="missing-asset-code">
                <input type="hidden" name="reported_by" value="{{ auth()->id() }}">
                <input type="hidden" name="reported_date" value="{{ now()->format('Y-m-d') }}">
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Asset Code</label>
                    <input type="text" id="missing-asset-code-display" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-100" readonly>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Investigation Notes</label>
                    <textarea name="investigation_notes" id="investigation-notes" rows="3" 
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800"
                              placeholder="Enter any investigation notes or additional details"></textarea>
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex-1">
                        Mark as Lost
                    </button>
                    <button type="button" id="cancel-missing" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Complete Maintenance Confirmation Modal -->
<div id="complete-maintenance-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg p-6 max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                Complete Maintenance Checklist
            </h3>
            
            <div class="mb-6">
                <p class="text-gray-700 mb-4">
                    You are about to complete the maintenance checklist. The following assets will be automatically marked as lost:
                </p>
                
                @if($checklist->unscanned_assets->count() > 0)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-red-800 mb-3">Assets to be marked as lost ({{ $checklist->unscanned_assets->count() }}):</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-60 overflow-y-auto">
                        @foreach($checklist->unscanned_assets as $item)
                        <div class="bg-white border border-red-200 rounded p-3">
                            <div class="font-medium text-gray-800">{{ $item->asset_code }}</div>
                            <div class="text-sm text-gray-600">{{ $item->particulars }}</div>
                            <div class="text-xs text-gray-500">Start Status: {{ $item->start_status }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <p class="text-green-800 font-medium">All assets have been processed. No assets will be marked as lost.</p>
                </div>
                @endif
            </div>
            
            <div class="flex gap-3 justify-end">
                <button type="button" id="cancel-complete" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                    Cancel
                </button>
                <button type="button" id="confirm-complete" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                    <i class="fas fa-check-circle"></i> Complete Checklist
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize Signature Pads for GSU completion form
    const checkedCanvas = document.getElementById('checked-signature-pad');
    const gsuCanvas = document.getElementById('gsu-signature-pad');
    
    let checkedSignaturePad = null;
    let gsuSignaturePad = null;
    
    if (checkedCanvas) {
        checkedSignaturePad = new SignaturePad(checkedCanvas, {
            backgroundColor: 'rgba(255, 255, 255, 0)',
            penColor: 'rgb(0, 0, 0)',
            minWidth: 1,
            maxWidth: 2,
            throttle: 16,
            minDistance: 5
        });
        
        // Make canvas responsive
        function resizeCheckedCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            const rect = checkedCanvas.getBoundingClientRect();
            checkedCanvas.width = rect.width * ratio;
            checkedCanvas.height = rect.height * ratio;
            checkedCanvas.getContext('2d').scale(ratio, ratio);
            checkedSignaturePad.clear();
        }
        
        window.addEventListener('resize', resizeCheckedCanvas);
        resizeCheckedCanvas();
        
        // Clear checked signature button
        document.getElementById('clear-checked-signature').addEventListener('click', function() {
            checkedSignaturePad.clear();
            document.getElementById('checked_by_signature').value = '';
        });
        
        // Auto-save checked signature
        checkedSignaturePad.addEventListener('endStroke', function() {
            if (!checkedSignaturePad.isEmpty()) {
                const signatureData = checkedSignaturePad.toDataURL('image/png');
                document.getElementById('checked_by_signature').value = signatureData;
            }
        });
    }
    
    if (gsuCanvas) {
        gsuSignaturePad = new SignaturePad(gsuCanvas, {
            backgroundColor: 'rgba(255, 255, 255, 0)',
            penColor: 'rgb(0, 0, 0)',
            minWidth: 1,
            maxWidth: 2,
            throttle: 16,
            minDistance: 5
        });
        
        // Make canvas responsive
        function resizeGsuCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            const rect = gsuCanvas.getBoundingClientRect();
            gsuCanvas.width = rect.width * ratio;
            gsuCanvas.height = rect.height * ratio;
            gsuCanvas.getContext('2d').scale(ratio, ratio);
            gsuSignaturePad.clear();
        }
        
        window.addEventListener('resize', resizeGsuCanvas);
        resizeGsuCanvas();
        
        // Clear GSU signature button
        document.getElementById('clear-gsu-signature').addEventListener('click', function() {
            gsuSignaturePad.clear();
            document.getElementById('gsu_staff_signature').value = '';
        });
        
        // Auto-save GSU signature
        gsuSignaturePad.addEventListener('endStroke', function() {
            if (!gsuSignaturePad.isEmpty()) {
                const signatureData = gsuSignaturePad.toDataURL('image/png');
                document.getElementById('gsu_staff_signature').value = signatureData;
            }
        });
    }
    let html5QrcodeScanner = null;
    let isScanning = false;

    // Scanner functionality
    const startScannerBtn = document.getElementById('start-scanner');
    const stopScannerBtn = document.getElementById('stop-scanner');
    const scannerContainer = document.getElementById('scanner-container');

    startScannerBtn.addEventListener('click', function() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear();
        }

        html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader",
            { fps: 10, qrbox: { width: 250, height: 250 } },
            false
        );

        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        isScanning = true;
        
        startScannerBtn.style.display = 'none';
        stopScannerBtn.style.display = 'inline-block';
    });

    stopScannerBtn.addEventListener('click', function() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear();
        }
        isScanning = false;
        
        startScannerBtn.style.display = 'inline-block';
        stopScannerBtn.style.display = 'none';
    });

    function onScanSuccess(decodedText, decodedResult) {
        console.log(`QR Code detected: ${decodedText}`);
        
        // Stop scanning
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear();
        }
        isScanning = false;
        startScannerBtn.style.display = 'inline-block';
        stopScannerBtn.style.display = 'none';
        
        // Show status selection modal
        showStatusModal(decodedText);
    }

    function onScanFailure(error) {
        // Handle scan failure, but don't show error to user
        console.log(`QR Code scan failed: ${error}`);
    }

    // Handle radio button card interactions
    // Track previous state to detect unselect clicks
    let previousCheckedState = {};
    let isProcessingClick = false;
    
    document.querySelectorAll('input[name="end_status"]').forEach((radio, index) => {
        previousCheckedState[radio.value] = false;
        
        radio.addEventListener('change', function() {
            updateRadioCardSelection();
        });
        
        // Add click handler to the label for unselecting
        const label = radio.closest('label');
        
        label.addEventListener('click', function(e) {
            // Prevent duplicate processing
            if (isProcessingClick) {
                return;
            }
            
            isProcessingClick = true;
            
            // If this radio was already checked before the click, user wants to unselect
            if (previousCheckedState[radio.value]) {
                // Prevent the default radio button behavior
                e.preventDefault();
                // Uncheck the radio
                radio.checked = false;
                previousCheckedState[radio.value] = false;
                // Update visual state
                setTimeout(() => {
                    updateRadioCardSelection();
                    isProcessingClick = false;
                }, 10);
            } else {
                // Update previous state for all radios
                Object.keys(previousCheckedState).forEach(key => {
                    previousCheckedState[key] = (key === radio.value);
                });
                // Let the default behavior happen, then update visual state
                setTimeout(() => {
                    updateRadioCardSelection();
                    isProcessingClick = false;
                }, 10);
            }
        });
    });
    
    function updateRadioCardSelection() {
        // Remove selected class from all cards
        document.querySelectorAll('input[name="end_status"]').forEach((radio, index) => {
            const label = radio.closest('label');
            label.classList.remove('border-red-800', 'bg-red-50');
            label.classList.add('border-gray-300');
            const innerDot = label.querySelector('.w-2.h-2');
            if (innerDot) {
                innerDot.classList.add('hidden');
            }
        });
        
        // Add selected class to checked card
        const checkedRadio = document.querySelector('input[name="end_status"]:checked');
        if (checkedRadio) {
            const label = checkedRadio.closest('label');
            label.classList.remove('border-gray-300');
            label.classList.add('border-red-800', 'bg-red-50');
            const innerDot = label.querySelector('.w-2.h-2');
            if (innerDot) {
                innerDot.classList.remove('hidden');
            }
        }
    }

    // Manual scan form
    document.getElementById('manual-scan-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const assetCode = formData.get('asset_code');
        const endStatus = formData.get('end_status');
        const notes = formData.get('notes');
        
        if (!assetCode) {
            showMessage('Please enter an asset code', 'error');
            return;
        }
        
        if (!endStatus) {
            showMessage('Please select an end status', 'error');
            return;
        }
        
        scanAsset(assetCode, endStatus, notes || '');
    });

    // Mark missing button
    document.getElementById('mark-missing-btn').addEventListener('click', function() {
        const assetCode = document.getElementById('manual-asset-code').value;
        
        if (!assetCode) {
            showMessage('Please enter an asset code first', 'error');
            return;
        }
        
        showMissingModal(assetCode);
    });

    // Status selection modal
    function showStatusModal(assetCode) {
        // Set the asset code in the manual form
        document.getElementById('manual-asset-code').value = assetCode;
        
        // Show a message to select status
        showMessage(`Asset ${assetCode} detected. Please select the end status below.`, 'info');
        
        // Scroll to the manual entry section
        document.querySelector('.bg-white.rounded-lg.shadow-lg.p-6:last-of-type').scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start' 
        });
    }

    // Missing asset modal
    function showMissingModal(assetCode) {
        document.getElementById('missing-asset-code').value = assetCode;
        document.getElementById('missing-asset-code-display').value = assetCode;
        document.getElementById('missing-asset-modal').classList.remove('hidden');
    }

    // Close missing modal
    document.getElementById('cancel-missing').addEventListener('click', function() {
        document.getElementById('missing-asset-modal').classList.add('hidden');
    });

    // Missing asset form
    document.getElementById('missing-asset-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('{{ route("asset-scanner.mark-missing") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                asset_code: formData.get('asset_code'),
                maintenance_checklist_id: formData.get('maintenance_checklist_id'),
                reported_by: formData.get('reported_by'),
                reported_date: formData.get('reported_date'),
                investigation_notes: formData.get('investigation_notes')
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                document.getElementById('missing-asset-modal').classList.add('hidden');
                setTimeout(() => location.reload(), 1000);
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('An error occurred', 'error');
        });
    });

    // Scan asset function
    function scanAsset(assetCode, endStatus, notes) {
        fetch('{{ route("asset-scanner.scan") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                asset_code: assetCode,
                maintenance_checklist_id: {{ $checklist->id }},
                end_status: endStatus,
                notes: notes
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                // Clear manual form
                document.getElementById('manual-asset-code').value = '';
                document.getElementById('manual-notes').value = '';
                // Clear radio button selection
                document.querySelectorAll('input[name="end_status"]').forEach(radio => {
                    radio.checked = false;
                });
                updateRadioCardSelection();
                setTimeout(() => location.reload(), 1000);
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('An error occurred', 'error');
        });
    }

    // Show message function
    function showMessage(message, type) {
        const container = document.getElementById('message-container');
        const messageDiv = document.createElement('div');
        
        let bgColor, icon;
        switch(type) {
            case 'success':
                bgColor = 'bg-green-600';
                icon = 'fas fa-check-circle';
                break;
            case 'error':
                bgColor = 'bg-red-600';
                icon = 'fas fa-exclamation-circle';
                break;
            case 'info':
                bgColor = 'bg-blue-600';
                icon = 'fas fa-info-circle';
                break;
            default:
                bgColor = 'bg-gray-600';
                icon = 'fas fa-info-circle';
        }
        
        messageDiv.className = `${bgColor} text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 mb-2`;
        messageDiv.innerHTML = `
            <i class="${icon} text-xl"></i>
            <span>${message}</span>
        `;
        
        container.appendChild(messageDiv);
        
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }

    // Complete maintenance button
    document.getElementById('complete-maintenance-btn').addEventListener('click', function() {
        document.getElementById('complete-maintenance-modal').classList.remove('hidden');
    });

    // Cancel complete modal
    document.getElementById('cancel-complete').addEventListener('click', function() {
        document.getElementById('complete-maintenance-modal').classList.add('hidden');
    });

    // Confirm complete maintenance
    document.getElementById('confirm-complete').addEventListener('click', function() {
        // Show loading state
        const btn = this;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Completing...';
        btn.disabled = true;

        // Validate required fields
        const checkedBy = document.getElementById('checked_by').value.trim();
        const gsuStaff = document.getElementById('gsu_staff').value.trim();
        const checkedBySig = document.getElementById('checked_by_signature').value.trim();
        const gsuStaffSig = document.getElementById('gsu_staff_signature').value.trim();

        if (!checkedBy || !gsuStaff || !checkedBySig || !gsuStaffSig) {
            showMessage('Please fill in names and provide both signatures before completing.', 'error');
            btn.innerHTML = originalText;
            btn.disabled = false;
            return;
        }

        // Submit completion with auto-marking missing assets and signatures
        fetch('{{ route("maintenance-checklists.complete-with-missing", $checklist) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                checked_by: checkedBy,
                gsu_staff: gsuStaff,
                checked_by_signature: checkedBySig,
                gsu_staff_signature: gsuStaffSig
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                document.getElementById('complete-maintenance-modal').classList.add('hidden');
                setTimeout(() => {
                    window.location.href = '{{ route("maintenance-checklists.show", $checklist) }}';
                }, 1500);
            } else {
                showMessage(data.message, 'error');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('An error occurred while completing the checklist', 'error');
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    });
});
</script>
@endsection
