@extends('layouts.admin')

@section('content')
<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
}

/* Custom scrollbar for better UX */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Enhanced focus states */
input:focus, select:focus, textarea:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

/* Signature pad hover effects */
#signature-pad:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease;
}

/* Button hover animations */
button:hover {
    transform: translateY(-1px);
    transition: transform 0.2s ease;
}

/* Card hover effects */
.bg-gradient-to-br:hover {
    transform: translateY(-2px);
    transition: transform 0.3s ease;
}
</style>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-red-600 to-red-700 rounded-full mb-4">
                <i class="fas fa-plus text-white text-2xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Create Maintenance Checklist</h1>
            <p class="text-lg text-gray-600">Set up a new maintenance checklist for asset inspection</p>
            
            <!-- Maintenance ID Preview -->
            <div class="mt-6 inline-flex items-center bg-blue-50 border border-blue-200 rounded-lg px-4 py-2">
                <i class="fas fa-tag text-blue-600 mr-2"></i>
                <span class="text-sm text-blue-800 font-medium">Maintenance ID:</span>
                <span id="maintenance-id-preview" class="ml-2 text-blue-900 font-bold text-lg">MNT-2024-0001</span>
            </div>
        </div>

        <!-- Error Toast -->
    @if($errors->any())
            <div id="toast-error" class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50 bg-red-600 text-white px-6 py-4 rounded-xl shadow-xl flex items-center gap-3 animate-fade-in max-w-md">
                <div class="flex-shrink-0">
            <i class="fas fa-exclamation-circle text-xl"></i>
                </div>
            <div class="flex-1">
                    <h4 class="font-semibold">Validation Error</h4>
                    <p class="text-sm opacity-90">{{ $errors->first() }}</p>
            </div>
                <button onclick="document.getElementById('toast-error').style.display='none'" class="flex-shrink-0 text-white hover:text-gray-200 focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <script>
            setTimeout(function() {
                var toast = document.getElementById('toast-error');
                if (toast) toast.style.display = 'none';
                }, 8000);
        </script>
    @endif

        <!-- Main Form Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <form action="{{ route('maintenance-checklists.store') }}" method="POST" id="checklistForm" class="space-y-0">
        @csrf
                @if(isset($maintenanceRequestId))
                    <input type="hidden" name="maintenance_request_id" value="{{ $maintenanceRequestId }}">
                @endif
                <!-- Progress Steps -->
                <div class="bg-gradient-to-r from-red-600 to-red-700 px-8 py-6">
                    <div class="flex items-center justify-center">
                        <div class="flex items-center space-x-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <span class="text-red-900 font-semibold">1</span>
                                </div>
                                <span class="text-white font-medium text-lg">Form Details</span>
                            </div>
                            <div class="w-12 h-px bg-white bg-opacity-30"></div>
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <span class="text-red-900 font-semibold">2</span>
                                </div>
                                <span class="text-white font-medium text-lg">Review & Create</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Two Column Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-0 min-h-[600px]">
                    <!-- Left Column: Basic Information -->
                    <div class="p-6 lg:p-8 border-r-0 lg:border-r border-gray-200">
                        <div class="flex items-center mb-6">
                            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-info-circle text-red-600"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">Basic Information</h2>
                                <p class="text-gray-600">Essential details for this checklist</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700" for="school_year">
                                    School Year <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="school_year" id="school_year" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200" 
                                       value="{{ old('school_year', '2024-2025') }}" 
                                       placeholder="e.g., 2024-2025" required>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700" for="start_of_sy_date">
                                        Start of SY Date
                                    </label>
                                    <input type="date" name="start_of_sy_date" id="start_of_sy_date" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200" 
                                           value="{{ old('start_of_sy_date') }}">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700" for="end_of_sy_date">
                                        End of SY Date
                                    </label>
                                    <input type="date" name="end_of_sy_date" id="end_of_sy_date" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200" 
                                           value="{{ old('end_of_sy_date') }}">
                                </div>
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

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700" for="location_id">
                                    Room/Location <span class="text-red-500">*</span>
                                </label>
                                <select name="location_id" id="room_number" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200" required>
                                    <option value="">Select a room or location</option>
                                    @foreach($locations as $location)
                                        @php
                                            $isPending = in_array($location->id, $pendingLocationIds);
                                            $hasRepairAssets = in_array($location->id, $repairMaintenanceLocationIds);
                                        @endphp
                                        <option value="{{ $location->id }}" 
                                                data-building="{{ $location->building }}" 
                                                data-floor="{{ $location->floor }}"
                                                data-room="{{ $location->room }}"
                                                data-pending="{{ $isPending ? 'true' : 'false' }}"
                                                data-repair="{{ $hasRepairAssets ? 'true' : 'false' }}"
                                                {{ old('location_id') == $location->id || (isset($prefillLocationId) && (int)$prefillLocationId === $location->id) ? 'selected' : '' }}
                                                {{ ($isPending || $hasRepairAssets) ? 'disabled' : '' }}>
                                            {{ $location->building }} - {{ $location->floor }} - {{ $location->room }}
                                            @if($isPending)
                                                (⚠️ Pending Maintenance)
                                            @elseif($hasRepairAssets)
                                                (🔧 Has Repair/Maintenance Assets)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <div id="location-warning" class="hidden text-sm text-amber-600 bg-amber-50 border border-amber-200 rounded-lg p-3 mt-2">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <span class="font-medium">Warning:</span>
                                        <span>This location already has a pending or in-progress maintenance checklist.</span>
                                    </div>
                                </div>
                                <div id="repair-warning" class="hidden text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg p-3 mt-2">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-tools"></i>
                                        <span class="font-medium">Error:</span>
                                        <span>This location has assets marked as 'For Repair' or 'For Maintenance'. Please resolve these assets first before creating a maintenance checklist.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Instructor Details & Signature -->
                    <div class="p-6 lg:p-8 border-t lg:border-t-0 border-gray-200">
                        <div class="flex items-center mb-6">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">Instructor Details</h2>
                                <p class="text-gray-600">Name and digital signature</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700" for="instructor">
                                    Instructor Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="instructor" id="instructor" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200" 
                                       value="{{ old('instructor', $prefillInstructor ?? '') }}" 
                                       placeholder="Enter instructor's full name" required>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700" for="instructor_signature">
                                    Digital Signature
                                </label>
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 lg:p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                                    <div class="text-center mb-4 lg:mb-6">
                                        <canvas id="signature-pad" width="500" height="200" class="border border-gray-200 rounded-lg cursor-crosshair bg-white shadow-sm w-full max-w-full h-32 lg:h-48"></canvas>
                                    </div>
                                    <div class="flex flex-col sm:flex-row justify-center gap-3 lg:gap-4">
                                        <button type="button" id="clear-signature" class="px-4 lg:px-6 py-2 lg:py-3 bg-gray-500 hover:bg-gray-600 text-white text-sm rounded-lg transition duration-200 flex items-center justify-center gap-2">
                                            <i class="fas fa-eraser"></i> <span class="hidden sm:inline">Clear Signature</span><span class="sm:hidden">Clear</span>
                                        </button>
                                        <button type="button" id="save-signature" class="px-4 lg:px-6 py-2 lg:py-3 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-lg transition duration-200 flex items-center justify-center gap-2">
                                            <i class="fas fa-save"></i> <span class="hidden sm:inline">Save Signature</span><span class="sm:hidden">Save</span>
                                        </button>
                                    </div>
                                    <input type="hidden" name="instructor_signature" id="instructor_signature" value="{{ old('instructor_signature') }}">
                                    <div id="signature-status" class="text-center text-sm text-gray-600 mt-4"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Review & Create -->
                <div class="p-8">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div>
                            <h2 class="text-2xl font-bold text-gray-900">Review & Create</h2>
                            <p class="text-gray-600">Add any additional notes and create your maintenance checklist</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- Notes Section -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700" for="notes">
                                Additional Notes
                            </label>
                            <textarea name="notes" id="notes" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200 resize-none"
                                      placeholder="Enter any additional notes or special instructions for this maintenance checklist...">{{ old('notes') }}</textarea>
                </div>

                        <!-- Information Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Asset Information Card -->
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-6">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-cubes text-white text-lg"></i>
                </div>
                </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-blue-900 mb-2">Automatic Asset Population</h3>
                                        <p class="text-blue-800 text-sm leading-relaxed">
                                            All active assets in the selected room (excluding disposed and missing assets) will be automatically added to this checklist with "OK" as the initial status. 
                                            GSU staff will scan each asset's QR code during maintenance to determine the final status.
                                        </p>
                </div>
            </div>
        </div>

                            <!-- Workflow Information Card -->
                            <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-6">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-users text-white text-lg"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-green-900 mb-2">GSU Staff Workflow</h3>
                                        <p class="text-green-800 text-sm leading-relaxed">
                                            After creation, GSU staff will acknowledge this checklist, scan assets using QR codes, 
                                            and provide their signatures upon completion of the maintenance process.
                                        </p>
                                    </div>
                                    </div>
            </div>
        </div>

                            <!-- Restrictions Information Card -->
                            <div class="bg-gradient-to-br from-amber-50 to-amber-100 border border-amber-200 rounded-xl p-6">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-amber-500 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-amber-900 mb-2">Important Restrictions</h3>
                                        <ul class="text-amber-800 text-sm leading-relaxed space-y-1">
                                            <li>• Only one maintenance checklist per location at a time</li>
                                            <li>• Disposed and missing assets are automatically excluded</li>
                                            <li>• Locations with pending checklists are disabled</li>
                                            <li>• Locations with 'For Repair' or 'For Maintenance' assets are blocked</li>
                                        </ul>
                                    </div>
            </div>
        </div>

                        <!-- Form Actions -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
            <a href="{{ route('maintenance-checklists.index') }}" 
                               class="flex-1 sm:flex-none px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all duration-200 flex items-center justify-center gap-2">
                                <i class="fas fa-arrow-left"></i>
                                <span>Cancel</span>
            </a>
            <button type="submit" 
                                    class="flex-1 sm:flex-none px-8 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <i class="fas fa-plus-circle"></i>
                                <span>Create Maintenance Checklist</span>
            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include Signature Pad Library -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Signature Pad
    const canvas = document.getElementById('signature-pad');
    const signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgba(255, 255, 255, 0)',
        penColor: 'rgb(0, 0, 0)',
        minWidth: 1,
        maxWidth: 2,
        throttle: 16,
        minDistance: 5
    });

    // Make canvas responsive
    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        const rect = canvas.getBoundingClientRect();
        
        // Set canvas size to match container
        canvas.width = rect.width * ratio;
        canvas.height = rect.height * ratio;
        
        // Scale the drawing context to match device pixel ratio
        const ctx = canvas.getContext('2d');
        ctx.scale(ratio, ratio);
        
        // Clear and redraw if there was existing content
        if (!signaturePad.isEmpty()) {
            const data = signaturePad.toDataURL();
            signaturePad.clear();
            const img = new Image();
            img.onload = function() {
                ctx.drawImage(img, 0, 0, rect.width, rect.height);
            };
            img.src = data;
        }
    }

    // Resize canvas on load and resize
    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();

    // Clear signature button
    document.getElementById('clear-signature').addEventListener('click', function() {
        signaturePad.clear();
        document.getElementById('instructor_signature').value = '';
        document.getElementById('signature-status').textContent = 'Signature cleared';
        document.getElementById('signature-status').className = 'text-center text-sm text-gray-600 mt-3';
    });

    // Save signature button
    document.getElementById('save-signature').addEventListener('click', function() {
        if (signaturePad.isEmpty()) {
            document.getElementById('signature-status').textContent = 'Please draw a signature first';
            document.getElementById('signature-status').className = 'text-center text-sm text-red-600 mt-3';
            return;
        }

        const signatureData = signaturePad.toDataURL('image/png');
        document.getElementById('instructor_signature').value = signatureData;
        document.getElementById('signature-status').textContent = 'Signature saved successfully';
        document.getElementById('signature-status').className = 'text-center text-sm text-green-600 mt-3';
    });

    // Auto-save signature when drawing stops
    signaturePad.addEventListener('endStroke', function() {
        if (!signaturePad.isEmpty()) {
            const signatureData = signaturePad.toDataURL('image/png');
            document.getElementById('instructor_signature').value = signatureData;
            document.getElementById('signature-status').textContent = 'Signature auto-saved';
            document.getElementById('signature-status').className = 'text-center text-sm text-blue-600 mt-3';
        }
    });

    // Load existing signature if available
    const existingSignature = document.getElementById('instructor_signature').value;
    if (existingSignature && existingSignature.startsWith('data:image')) {
        const img = new Image();
        img.onload = function() {
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        };
        img.src = existingSignature;
    }

    // Form validation with enhanced UX
    document.getElementById('checklistForm').addEventListener('submit', function(e) {
        const roomSelect = document.getElementById('room_number');
        const instructorInput = document.getElementById('instructor');
        const selectedOption = roomSelect.options[roomSelect.selectedIndex];
        const isPending = selectedOption.getAttribute('data-pending') === 'true';
        const hasRepairAssets = selectedOption.getAttribute('data-repair') === 'true';
        
        // Check required fields
        if (!roomSelect.value) {
            e.preventDefault();
            showFieldError(roomSelect, 'Please select a room first.');
            roomSelect.focus();
            return false;
        }
        
        // Check if selected location has pending maintenance
        if (isPending) {
            e.preventDefault();
            showFieldError(roomSelect, 'This location already has a pending or in-progress maintenance checklist. Please select a different location.');
            roomSelect.focus();
            return false;
        }
        
        // Check if selected location has repair/maintenance assets
        if (hasRepairAssets) {
            e.preventDefault();
            showFieldError(roomSelect, 'This location has assets marked as "For Repair" or "For Maintenance". Please resolve these assets first before creating a maintenance checklist.');
            roomSelect.focus();
            return false;
        }
        
        if (!instructorInput.value.trim()) {
            e.preventDefault();
            showFieldError(instructorInput, 'Please enter the instructor name.');
            instructorInput.focus();
            return false;
        }
        
        // Auto-save signature before form submission if not empty
        if (!signaturePad.isEmpty() && !document.getElementById('instructor_signature').value) {
            const signatureData = signaturePad.toDataURL('image/png');
            document.getElementById('instructor_signature').value = signatureData;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
        submitBtn.disabled = true;
        
        console.log('Form submission started');
    });

    // Helper function to show field errors
    function showFieldError(field, message) {
        // Remove existing error styling
        field.classList.remove('border-red-500', 'ring-red-500');
        field.classList.add('border-gray-300');
        
        // Add error styling
        field.classList.add('border-red-500', 'ring-2', 'ring-red-500');
        
        // Show error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'text-red-600 text-sm mt-1';
        errorDiv.textContent = message;
        
        // Remove existing error message
        const existingError = field.parentNode.querySelector('.text-red-600');
        if (existingError) {
            existingError.remove();
        }
        
        field.parentNode.appendChild(errorDiv);
        
        // Remove error styling after 3 seconds
        setTimeout(() => {
            field.classList.remove('border-red-500', 'ring-2', 'ring-red-500');
            field.classList.add('border-gray-300');
            if (errorDiv.parentNode) {
                errorDiv.remove();
            }
        }, 3000);
    }

    // Generate and display maintenance ID preview
    function generateMaintenanceIdPreview() {
        const year = new Date().getFullYear();
        const randomNumber = Math.floor(Math.random() * 9999) + 1;
        const paddedNumber = randomNumber.toString().padStart(4, '0');
        const maintenanceId = `MNT-${year}-${paddedNumber}`;
        
        document.getElementById('maintenance-id-preview').textContent = maintenanceId;
    }

    // Generate initial preview
    generateMaintenanceIdPreview();

    // Handle location selection and show warnings for pending locations or repair assets
    document.getElementById('room_number').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const isPending = selectedOption.getAttribute('data-pending') === 'true';
        const hasRepairAssets = selectedOption.getAttribute('data-repair') === 'true';
        const warningDiv = document.getElementById('location-warning');
        const repairWarningDiv = document.getElementById('repair-warning');
        
        // Hide both warnings first
        warningDiv.classList.add('hidden');
        repairWarningDiv.classList.add('hidden');
        
        // Show appropriate warning
        if (isPending) {
            warningDiv.classList.remove('hidden');
        } else if (hasRepairAssets) {
            repairWarningDiv.classList.remove('hidden');
        }
    });

    // Add smooth scrolling to form sections
    const formSections = document.querySelectorAll('.p-8');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    formSections.forEach(section => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(20px)';
        section.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(section);
    });
});
</script>
@endsection 