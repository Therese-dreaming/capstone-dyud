@extends('layouts.user')

@section('title', 'Request Maintenance')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-red-600 to-red-700 rounded-full mb-4">
            <i class="fas fa-tools text-white text-2xl"></i>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Request Maintenance</h1>
        <p class="text-lg text-gray-600">Submit a maintenance request for asset inspection</p>
    </div>

    @if(isset($noLocations) && $noLocations)
    <!-- No Locations Message -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="p-8 text-center">
            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">No Locations Assigned</h2>
            <p class="text-gray-600 mb-6">
                You don't have any locations assigned to you. To submit maintenance requests, 
                you need to be assigned to at least one location by an administrator.
            </p>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                    <div class="text-left">
                        <h3 class="font-medium text-blue-900 mb-1">What does this mean?</h3>
                        <p class="text-sm text-blue-700">
                            Location assignment allows you to manage assets in specific rooms or areas. 
                            Once assigned, you can view assets in those locations and submit maintenance requests for them.
                        </p>
                    </div>
                </div>
            </div>
            <div class="space-y-3">
                <a href="{{ route('user-assets.index') }}" 
                   class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                    <i class="fas fa-boxes mr-2"></i> View My Assets
                </a>
                <div class="text-sm text-gray-500">
                    Contact your administrator to get locations assigned to you.
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <form method="POST" action="{{ route('maintenance-requests.store') }}" class="p-8" id="maintenanceRequestForm">
            @csrf
            <input type="hidden" name="request_scope" id="request_scope" value="location">

            <!-- Scope Selection -->
            <div class="mb-8">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-sliders-h text-purple-600"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">What would you like to maintain?</h2>
                        <p class="text-gray-600">Choose the scope of this maintenance request</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="scopeOptions">
                    <label class="cursor-pointer border rounded-xl p-4 flex items-start gap-3 hover:border-red-300 transition-colors" id="scope_location_option">
                        <input type="radio" name="scope_choice" value="location" class="mt-1" checked>
                        <div>
                            <div class="font-semibold text-gray-900">Maintenance the whole location</div>
                            <div class="text-sm text-gray-600">Schedule maintenance for all assets within a selected room/location.</div>
                        </div>
                    </label>
                    <label class="cursor-pointer border rounded-xl p-4 flex items-start gap-3 hover:border-red-300 transition-colors" id="scope_assets_option">
                        <input type="radio" name="scope_choice" value="assets" class="mt-1">
                        <div>
                            <div class="font-semibold text-gray-900">Maintenance specific assets</div>
                            <div class="text-sm text-gray-600">Add assets using QR scanning or manual entry in a single step.</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Basic Information Section -->
            <div class="mb-8">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-info-circle text-red-600"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Basic Information</h2>
                        <p class="text-gray-600">Essential details for the maintenance request</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700" for="semester_id">
                            Semester <span class="text-red-500">*</span>
                        </label>
                        <select name="semester_id" id="semester_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200" required>
                            <option value="">Select Semester</option>
                            @foreach($semesters as $semester)
                                <option value="{{ $semester->id }}" 
                                        data-academic-year="{{ $semester->academic_year }}"
                                        data-start-date="{{ $semester->start_date->format('Y-m-d') }}"
                                        data-end-date="{{ $semester->end_date->format('Y-m-d') }}"
                                        {{ old('semester_id') == $semester->id ? 'selected' : '' }}>
                                    {{ $semester->full_name }} ({{ $semester->start_date->format('M j, Y') }} - {{ $semester->end_date->format('M j, Y') }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-blue-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            School year and semester dates will be automatically filled based on your selection.
                        </p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700" for="school_year">
                            School Year <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="school_year" id="school_year" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200" 
                               value="{{ old('school_year') }}" 
                               placeholder="Will be filled automatically" readonly required>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-lock mr-1"></i>
                            Automatically filled when you select a semester.
                        </p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700" for="department">
                            Department <span class="text-red-500">*</span>
                        </label>
                        <select name="department" id="department" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200" required>
                            <option value="">Choose Department</option>
                            <option value="Grade School" {{ old('department') == 'Grade School' ? 'selected' : '' }}>Grade School</option>
                            <option value="Junior High School" {{ old('department') == 'Junior High School' ? 'selected' : '' }}>Junior High School</option>
                            <option value="Senior High School" {{ old('department') == 'Senior High School' ? 'selected' : '' }}>Senior High School</option>
                            <option value="College" {{ old('department') == 'College' ? 'selected' : '' }}>College</option>
                            <option value="Administration" {{ old('department') == 'Administration' ? 'selected' : '' }}>Administration</option>
                            <option value="Library" {{ old('department') == 'Library' ? 'selected' : '' }}>Library</option>
                            <option value="Laboratory" {{ old('department') == 'Laboratory' ? 'selected' : '' }}>Laboratory</option>
                            <option value="Other" {{ old('department') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700" for="date_reported">
                            Date Reported <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_reported" id="date_reported" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200" 
                               value="{{ old('date_reported', date('Y-m-d')) }}" required>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700" for="program">
                            Program
                        </label>
                        <input type="text" name="program" id="program" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200" 
                               value="{{ old('program', 'N/A') }}" 
                               placeholder="e.g., Computer Science">
                    </div>
                </div>
            </div>

            <!-- Specific Assets Section (Shown when scope = assets) -->
            <div class="mb-8 hidden" id="specificAssetsSection">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-qrcode text-indigo-600"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Select Specific Assets</h2>
                        <p class="text-gray-600">Scan a QR code or enter an asset code manually</p>
                        <div class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-xs text-blue-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                <strong>Security Notice:</strong> You can only add assets from locations you manage.
                            </p>
                            <p class="text-xs text-blue-600 mt-1">
                                ✅ Assets from your locations will be added<br>
                                ❌ Assets from other locations will be rejected with an error message
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Scanner/Manual Panel -->
                    <div class="bg-gray-50 border rounded-xl p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="font-semibold text-gray-900">QR Scanner</div>
                            <div class="space-x-2">
                                <button type="button" id="startScannerBtn" class="px-3 py-1 bg-red-800 text-white text-sm rounded-lg hover:bg-red-900"><i class="fas fa-camera mr-1"></i>Start</button>
                                <button type="button" id="stopScannerBtn" class="px-3 py-1 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300"><i class="fas fa-stop mr-1"></i>Stop</button>
                            </div>
                        </div>
                        <div class="rounded-lg overflow-hidden border">
                            <div id="scannerPlaceholder" class="bg-white p-6 text-center">
                                <i class="fas fa-qrcode text-3xl text-gray-400 mb-2"></i>
                                <div class="text-sm text-gray-600">Scanner idle. Click Start to scan.</div>
                            </div>
                            <video id="scannerVideo" class="hidden w-full" autoplay></video>
                            <canvas id="scannerCanvas" class="hidden"></canvas>
                            <!-- Scan Complete Panel -->
                            <div id="scannerCompletePanel" class="hidden bg-green-50 p-6 text-center">
                                <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-check text-2xl text-green-600"></i>
                                </div>
                                <div class="text-lg font-semibold text-green-800 mb-1">Scan Completed</div>
                                <div class="text-sm text-green-700 mb-4">The asset has been added to your list.</div>
                                <button type="button" onclick="scanAgainCreatePage()" class="px-4 py-2 bg-red-800 text-white rounded-lg hover:bg-red-900">
                                    <i class="fas fa-redo mr-1"></i> Scan Again
                                </button>
                            </div>
                            <!-- Scan Error Panel -->
                            <div id="scannerErrorPanel" class="hidden bg-red-50 p-6 text-center">
                                <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
                                </div>
                                <div id="scannerErrorPanelTitle" class="text-lg font-semibold text-red-800 mb-1">Scan Error</div>
                                <div id="scannerErrorPanelMessage" class="text-sm text-red-700 mb-4">There was a problem validating the asset.</div>
                                <button type="button" onclick="scanAgainCreatePage()" class="px-4 py-2 bg-red-800 text-white rounded-lg hover:bg-red-900">
                                    <i class="fas fa-redo mr-1"></i> Scan Again
                                </button>
                            </div>
                        </div>
                        <div id="scannerError" class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700 hidden">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-circle mt-0.5 mr-2 text-red-500"></i>
                                <span id="scannerErrorText"></span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Manual Entry</label>
                            <div class="flex gap-2">
                                <input type="text" id="manualAssetCode" placeholder="Enter asset code" class="flex-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                <button type="button" id="addManualAssetBtn" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900">Add</button>
                            </div>
                            <div id="manualError" class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700 hidden">
                                <div class="flex items-start">
                                    <i class="fas fa-exclamation-circle mt-0.5 mr-2 text-red-500"></i>
                                    <span id="manualErrorText">Asset not found. Please enter a valid asset code.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Selected Assets List -->
                    <div class="bg-gray-50 border rounded-xl p-4">
                        <div class="font-semibold text-gray-900 mb-3">Selected Assets</div>
                        <div id="selectedAssetsEmpty" class="text-sm text-gray-500">No assets added yet.</div>
                        <ul id="selectedAssetsList" class="divide-y divide-gray-200 hidden"></ul>
                        <p id="selectedAssetsError" class="mt-3 text-sm text-red-600 hidden"><i class="fas fa-exclamation-triangle mr-1"></i>Please add at least one asset.</p>
                    </div>
                </div>
            </div>

            <!-- Location and Instructor Section -->
            <div class="mb-8">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-map-marker-alt text-blue-600"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Location & Instructor</h2>
                        <p class="text-gray-600">Where maintenance is needed and who is requesting</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700" for="location_id">
                            Room/Location <span class="text-red-500">*</span>
                        </label>
                        <select name="location_id" id="location_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200" required>
                            <option value="">Select a room or location</option>
                            @foreach($locations as $location)
                                @if(!$location)
                                    @continue
                                @endif
                                <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                    {{ $location->building ?? 'Unknown' }} - Floor {{ $location->floor ?? 'N/A' }} - Room {{ $location->room ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                        @if($locations->count() > 0)
                        <p class="text-xs text-blue-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            You can only submit requests for locations you manage ({{ $locations->count() }} location{{ $locations->count() !== 1 ? 's' : '' }}).
                        </p>
                        @endif
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700" for="instructor_name">
                            Instructor Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="instructor_name" id="instructor_name" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200" 
                               value="{{ old('instructor_name', auth()->user()->name) }}" 
                               placeholder="Enter instructor's full name" required>
                    </div>
                </div>
            </div>

            <!-- Additional Notes Section -->
            <div class="mb-8">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-sticky-note text-green-600"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Additional Information</h2>
                        <p class="text-gray-600">Any special notes or requirements</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700" for="notes">
                        Notes
                    </label>
                    <textarea name="notes" id="notes" rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200 resize-none"
                              placeholder="Enter any additional notes or special instructions for this maintenance request...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors duration-200">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl hover:from-red-700 hover:to-red-800 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <i class="fas fa-paper-plane mr-2"></i>Submit Request
                </button>
            </div>
        </form>
    </div>
</div>
<script>
(function() {
    const scopeRadios = document.querySelectorAll('input[name="scope_choice"]');
    const requestScope = document.getElementById('request_scope');
    const specificAssetsSection = document.getElementById('specificAssetsSection');
    const locationSelect = document.getElementById('location_id');
    const locationContainer = locationSelect ? locationSelect.closest('.space-y-2') : null;
    const selectedAssetsList = document.getElementById('selectedAssetsList');
    const selectedAssetsEmpty = document.getElementById('selectedAssetsEmpty');
    const manualInput = document.getElementById('manualAssetCode');
    const addManualBtn = document.getElementById('addManualAssetBtn');
    const manualError = document.getElementById('manualError');
    const selectedAssetsError = document.getElementById('selectedAssetsError');
    const form = document.getElementById('maintenanceRequestForm');

    let selectedAssetCodes = new Set();
    let mediaStream = null;

    function renderSelectedAssets() {
        if (selectedAssetCodes.size === 0) {
            selectedAssetsEmpty.classList.remove('hidden');
            selectedAssetsList.classList.add('hidden');
            selectedAssetsList.innerHTML = '';
            return;
        }
        selectedAssetsEmpty.classList.add('hidden');
        selectedAssetsList.classList.remove('hidden');
        selectedAssetsList.innerHTML = '';
        Array.from(selectedAssetCodes).forEach(code => {
            const li = document.createElement('li');
            li.className = 'py-2 flex items-center justify-between';
            li.innerHTML = `<div class="font-mono text-sm">${code}</div>
                            <div class="flex items-center gap-2">
                                <input type=\"hidden\" name=\"asset_codes[]\" value=\"${code}\">
                                <button type=\"button\" class=\"text-red-600 hover:text-red-800\" data-code=\"${code}\"><i class=\"fas fa-times\"></i></button>
                            </div>`;
            selectedAssetsList.appendChild(li);
        });
        // bind remove
        selectedAssetsList.querySelectorAll('button[data-code]').forEach(btn => {
            btn.addEventListener('click', () => {
                selectedAssetCodes.delete(btn.getAttribute('data-code'));
                renderSelectedAssets();
            });
        });
    }

    function setScope(scope) {
        requestScope.value = scope;
        if (scope === 'assets') {
            specificAssetsSection.classList.remove('hidden');
            if (locationContainer) locationContainer.classList.add('hidden');
            if (locationSelect) { locationSelect.dataset.wasRequired = 'true'; locationSelect.required = false; locationSelect.disabled = true; }
        } else {
            specificAssetsSection.classList.add('hidden');
            if (locationContainer) locationContainer.classList.remove('hidden');
            if (locationSelect) { if (locationSelect.dataset.wasRequired) locationSelect.required = true; locationSelect.disabled = false; }
        }
    }

    scopeRadios.forEach(r => {
        r.addEventListener('change', e => setScope(e.target.value));
    });

    async function validateAndAdd(code, isFromScanner = false) {
        // Clear all error messages
        manualError.classList.add('hidden');
        document.getElementById('scannerError').classList.add('hidden');
        
        if (!code) return;
        
        // Show loading state
        if (isFromScanner) {
            document.getElementById('scannerErrorText').innerHTML = '⏳ Validating asset...';
            document.getElementById('scannerError').classList.remove('hidden');
            document.getElementById('scannerError').className = 'mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700';
        } else {
            document.getElementById('manualErrorText').innerHTML = '⏳ Validating asset...';
            manualError.classList.remove('hidden');
            manualError.className = 'mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700';
        }
        
        try {
            // Add timeout to prevent hanging
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout
            
            const apiUrl = `/api/assets/code/${encodeURIComponent(code)}`;
            
            const res = await fetch(apiUrl, {
                signal: controller.signal,
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            clearTimeout(timeoutId);
            
            // Handle HTTP errors
            if (!res.ok) {
                if (res.status === 403) {
                    throw new Error('not-authorized');
                } else if (res.status === 404) {
                    throw new Error('not-found');
                } else {
                    throw new Error('server-error');
                }
            }
            
            const asset = await res.json();
            
            if (!asset || !asset.asset_code) {
                throw new Error('invalid-response');
            }
            
            // Check if asset is already added
            if (selectedAssetCodes.has(asset.asset_code)) {
                if (isFromScanner) {
                    // Stop camera and show a clear Already Added error panel
                    stopCameraOnlyCreatePage();
                    const copy = getScannerErrorCopy('already-added');
                    showScanErrorPanelCreatePage(copy.title, copy.message);
                    isProcessingScan = false;
                    return;
                }
                throw new Error('already-added');
            }
            
            selectedAssetCodes.add(asset.asset_code);
            renderSelectedAssets();
            
            // Show success feedback
            if (isFromScanner) {
                // Ensure we do not also show an 'already added' message from a parallel path
                showScannerSuccess(asset);
                isProcessingScan = false; // reset guard for next scan attempt
            } else {
                showManualSuccess(asset);
            }
            
        } catch (e) {
            console.error('Asset validation error:', e); // Debug logging
            
            // Reset loading state and show error
            if (isFromScanner) {
                // For scanner flow, stop camera and show the error panel instead of inline banner
                const errorTypeForPanel = normalizeScannerErrorType(e);
                const { title, message } = getScannerErrorCopy(errorTypeForPanel);
                stopCameraOnlyCreatePage();
                showScanErrorPanelCreatePage(title, message);
                return; // Do not rethrow for scanner flow
            } else {
                manualError.className = 'mt-2 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700';
            }
            
            // Handle different error types
            let errorType = e.message;
            if (e.name === 'AbortError') {
                errorType = 'timeout';
            } else if (e instanceof TypeError || e.message.includes('fetch')) {
                errorType = 'network-error';
            }
            
            showAssetError(errorType, isFromScanner);
            throw e; // Re-throw for manual flow
        }
    }

    function showAssetError(errorType, isFromScanner = false) {
        let errorMessage = '';
        let errorIcon = 'fas fa-exclamation-circle';
        
        switch (errorType) {
            case 'not-authorized':
                errorMessage = '🚫 Access Denied: You do not have permission to access this asset. You can only add assets from locations you manage.';
                errorIcon = 'fas fa-shield-alt';
                break;
            case 'not-found':
                errorMessage = '🔍 Asset Not Found: Please check the asset code and try again.';
                errorIcon = 'fas fa-search';
                break;
            case 'already-added':
                errorMessage = '📋 Already Added: This asset is already in your maintenance request list.';
                errorIcon = 'fas fa-info-circle';
                break;
            case 'server-error':
                errorMessage = '⚠️ Server Error: Unable to validate asset. Please try again later.';
                errorIcon = 'fas fa-server';
                break;
            case 'invalid-response':
                errorMessage = '❌ Invalid Response: Received unexpected data from server.';
                errorIcon = 'fas fa-exclamation-triangle';
                break;
            case 'timeout':
                errorMessage = '⏰ Request Timeout: The server took too long to respond. Please try again.';
                errorIcon = 'fas fa-clock';
                break;
            case 'network-error':
                errorMessage = '🌐 Network Error: Unable to connect to server. Please check your connection.';
                errorIcon = 'fas fa-wifi';
                break;
            default:
                errorMessage = '🔧 Validation Error: Unable to add asset. Please check the code and try again.';
                errorIcon = 'fas fa-exclamation-triangle';
        }
        
        const errorHtml = `<i class="${errorIcon} mr-1"></i>${errorMessage}`;
        
        if (isFromScanner) {
            document.getElementById('scannerErrorText').innerHTML = errorMessage;
            document.getElementById('scannerError').classList.remove('hidden');
            document.getElementById('scannerError').className = 'mt-2 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700';
        } else {
            document.getElementById('manualErrorText').innerHTML = errorMessage;
            manualError.classList.remove('hidden');
            manualError.className = 'mt-2 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700';
        }
    }

    function showScannerSuccess(asset) {
        // On success, stop camera and show completion panel
        stopCameraOnlyCreatePage();
        showScanCompletePanelCreatePage();
    }

    addManualBtn?.addEventListener('click', async () => {
        const code = (manualInput.value || '').trim();
        if (code) {
            try {
                await validateAndAdd(code, false);
                manualInput.value = ''; // Clear input on success
            } catch (e) {
                // Error already handled in validateAndAdd function
                // Input remains for user to correct
                console.log('Manual add failed:', e.message);
            }
        }
    });

    // Allow Enter key to add asset
    manualInput?.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            addManualBtn?.click();
        }
    });

    // Clear errors when user starts typing
    manualInput?.addEventListener('input', () => {
        manualError.classList.add('hidden');
    });

    // QR scanner (lightweight similar approach)
    const startBtn = document.getElementById('startScannerBtn');
    const stopBtn = document.getElementById('stopScannerBtn');
    const video = document.getElementById('scannerVideo');
    const canvas = document.getElementById('scannerCanvas');
    const placeholder = document.getElementById('scannerPlaceholder');
    let scanning = false;
    let isProcessingScan = false;
    let lastScannedCode = null;
    let lastScanAt = 0;
    const SCAN_COOLDOWN_MS = 1500;

    function loadJsQR(cb) {
        if (window.jsQR) return cb();
        const s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js';
        s.async = true;
        s.onload = cb;
        document.body.appendChild(s);
    }

    async function startScanner() {
        try {
            await new Promise(resolve => loadJsQR(resolve));
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert('Camera API not available in this browser.');
                return;
            }
            mediaStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: { ideal: 'environment' } }, audio: false });
            video.srcObject = mediaStream;
            video.setAttribute('playsinline', 'true');
            video.muted = true;
            await video.play().catch(() => {});
            placeholder.classList.add('hidden');
            document.getElementById('scannerCompletePanel').classList.add('hidden');
            video.classList.remove('hidden');
            scanning = true;
            isProcessingScan = false;
            requestAnimationFrame(scanLoop);
        } catch (e) {
            const isInsecureContext = location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1';
            let msg = 'Unable to access camera. Please check permissions.';
            if (isInsecureContext) msg += ' Note: Camera requires HTTPS or localhost.';
            alert(msg);
        }
    }

    function stopScanner() {
        if (mediaStream) {
            mediaStream.getTracks().forEach(t => t.stop());
            mediaStream = null;
        }
        scanning = false;
        video.classList.add('hidden');
        placeholder.classList.remove('hidden');
    }

    // Stop only the camera and hide video without showing the default placeholder
    function stopCameraOnlyCreatePage() {
        try {
            if (mediaStream) {
                mediaStream.getTracks().forEach(t => t.stop());
            }
        } catch {}
        mediaStream = null;
        scanning = false;
        video.classList.add('hidden');
        // Ensure placeholder is hidden while showing completion panel
        placeholder.classList.add('hidden');
    }

    function showScanCompletePanelCreatePage() {
        const panel = document.getElementById('scannerCompletePanel');
        if (panel) panel.classList.remove('hidden');
    }

    function showScanErrorPanelCreatePage(title, message) {
        const ep = document.getElementById('scannerErrorPanel');
        const et = document.getElementById('scannerErrorPanelTitle');
        const em = document.getElementById('scannerErrorPanelMessage');
        if (et) et.textContent = title || 'Scan Error';
        if (em) em.textContent = message || 'There was a problem validating the asset.';
        if (ep) ep.classList.remove('hidden');
        // Also hide inline error banner if visible
        const se = document.getElementById('scannerError');
        if (se) se.classList.add('hidden');
    }

    function normalizeScannerErrorType(err) {
        if (!err) return 'validation-error';
        if (typeof err === 'string') return err;
        if (err.name === 'AbortError') return 'timeout';
        if (err instanceof TypeError || (err.message && err.message.includes('fetch'))) return 'network-error';
        return err.message || 'validation-error';
    }

    function getScannerErrorCopy(errorType) {
        switch (errorType) {
            case 'not-authorized':
                return { title: 'Access Denied', message: 'You can only scan assets from locations you manage.' };
            case 'not-found':
                return { title: 'Asset Not Found', message: 'Please check the asset code and try again.' };
            case 'already-added':
                return { title: 'Already Added', message: 'This asset is already in your list.' };
            case 'server-error':
                return { title: 'Server Error', message: 'Unable to validate asset right now. Please try again later.' };
            case 'invalid-response':
                return { title: 'Invalid Response', message: 'Received unexpected data from the server.' };
            case 'timeout':
                return { title: 'Request Timeout', message: 'The server took too long to respond. Please try again.' };
            case 'network-error':
                return { title: 'Network Error', message: 'Unable to connect to the server. Check your connection and try again.' };
            default:
                return { title: 'Scan Error', message: 'Unable to add asset. Please check the code and try again.' };
        }
    }

    // Expose to global for inline onclick handler
    window.scanAgainCreatePage = function() {
        // Hide complete panel and start the scanner again
        const panel = document.getElementById('scannerCompletePanel');
        if (panel) panel.classList.add('hidden');
        const errorPanel = document.getElementById('scannerErrorPanel');
        if (errorPanel) errorPanel.classList.add('hidden');
        // Clear inline banner as well
        const se = document.getElementById('scannerError');
        if (se) se.classList.add('hidden');
        startScanner();
    }

    function scanLoop() {
        if (!scanning) return;
        const ctx = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        try {
            const img = ctx.getImageData(0, 0, canvas.width, canvas.height);
            if (window.jsQR) {
                const res = window.jsQR(img.data, img.width, img.height, { inversionAttempts: 'dontInvert' });
                if (res && res.data) {
                    const code = (res.data || '').trim();
                    if (code) {
                        const now = Date.now();
                        if (isProcessingScan || (lastScannedCode === code && (now - lastScanAt) < SCAN_COOLDOWN_MS)) {
                            // Ignore duplicate detections in cooldown window
                            return requestAnimationFrame(scanLoop);
                        }
                        isProcessingScan = true;
                        lastScannedCode = code;
                        lastScanAt = now;
                        // Stop loop immediately to prevent duplicate validations
                        scanning = false;
                        validateAndAdd(code, true); // true indicates this is from scanner
                    }
                }
            }
        } catch {}
        if (scanning) requestAnimationFrame(scanLoop);
    }

    startBtn?.addEventListener('click', startScanner);
    stopBtn?.addEventListener('click', stopScanner);

    // Handle semester selection
    const semesterSelect = document.getElementById('semester_id');
    const schoolYearInput = document.getElementById('school_year');

    semesterSelect?.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const academicYear = selectedOption.getAttribute('data-academic-year');
            schoolYearInput.value = academicYear;
        } else {
            schoolYearInput.value = '';
        }
    });

    // Initialize school year if semester is already selected (for form validation errors)
    if (semesterSelect?.value) {
        const selectedOption = semesterSelect.options[semesterSelect.selectedIndex];
        if (selectedOption.value) {
            const academicYear = selectedOption.getAttribute('data-academic-year');
            schoolYearInput.value = academicYear;
        }
    }

    // Initialize default scope
    setScope('location');

    // Validate on submit
    form.addEventListener('submit', (e) => {
        selectedAssetsError.classList.add('hidden');
        if (requestScope.value === 'assets' && selectedAssetCodes.size === 0) {
            e.preventDefault();
            selectedAssetsError.classList.remove('hidden');
            return false;
        }
        if (mediaStream) {
            mediaStream.getTracks().forEach(t => t.stop());
        }
    });
})();
</script>
    @endif
</div>
@endsection


