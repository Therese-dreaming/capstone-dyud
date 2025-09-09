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
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-orange-600 to-orange-700 rounded-full mb-4">
                <i class="fas fa-edit text-white text-2xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Edit Maintenance Checklist</h1>
            <p class="text-lg text-gray-600">Update maintenance checklist information and settings</p>
            
            <!-- Maintenance ID Display -->
            <div class="mt-6 inline-flex items-center bg-blue-50 border border-blue-200 rounded-lg px-4 py-2">
                <i class="fas fa-tag text-blue-600 mr-2"></i>
                <span class="text-sm text-blue-800 font-medium">Maintenance ID:</span>
                <span class="ml-2 text-blue-900 font-bold text-lg">{{ $checklist->maintenance_id ?? $checklist->id }}</span>
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
            <form action="{{ route('maintenance-checklists.update', $checklist) }}" method="POST" id="checklistForm" class="space-y-0">
                @csrf
                @method('PUT')

                <!-- Basic Information Section -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-info-circle text-red-600 mr-3"></i>
                        Basic Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- School Year -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700" for="school_year">
                                School Year <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="school_year" 
                                   id="school_year" 
                                   value="{{ old('school_year', $checklist->school_year ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                   placeholder="e.g., 2024-2025"
                                   required>
                        </div>

                        <!-- Department -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700" for="department">
                                Department <span class="text-red-500">*</span>
                            </label>
                            <select name="department" 
                                    id="department" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                    required>
                                <option value="">Select Department</option>
                                <option value="Grade School" {{ old('department', $checklist->department ?? '') == 'Grade School' ? 'selected' : '' }}>Grade School</option>
                                <option value="Junior High School" {{ old('department', $checklist->department ?? '') == 'Junior High School' ? 'selected' : '' }}>Junior High School</option>
                                <option value="Senior High School" {{ old('department', $checklist->department ?? '') == 'Senior High School' ? 'selected' : '' }}>Senior High School</option>
                                <option value="College" {{ old('department', $checklist->department ?? '') == 'College' ? 'selected' : '' }}>College</option>
                                <option value="Administration" {{ old('department', $checklist->department ?? '') == 'Administration' ? 'selected' : '' }}>Administration</option>
                                <option value="Library" {{ old('department', $checklist->department ?? '') == 'Library' ? 'selected' : '' }}>Library</option>
                                <option value="Laboratory" {{ old('department', $checklist->department ?? '') == 'Laboratory' ? 'selected' : '' }}>Laboratory</option>
                                <option value="Other" {{ old('department', $checklist->department ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <!-- Date Reported -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700" for="date_reported">
                                Date Reported <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="date_reported" 
                                   id="date_reported" 
                                   value="{{ old('date_reported', $checklist->date_reported ? $checklist->date_reported->format('Y-m-d') : '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                   required>
                        </div>

                        <!-- Program -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700" for="program">
                                Program
                            </label>
                            <input type="text" 
                                   name="program" 
                                   id="program" 
                                   value="{{ old('program', $checklist->program ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                   placeholder="e.g., Computer Science">
                        </div>

                        <!-- Room/Location -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700" for="location_id">
                                Room/Location <span class="text-red-500">*</span>
                            </label>
                            <select name="location_id" 
                                    id="location_id" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                    required>
                                <option value="">Select Location</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" 
                                            data-building="{{ $location->building }}" 
                                            data-floor="{{ $location->floor }}"
                                            data-room="{{ $location->room }}"
                                            {{ old('location_id', $checklist->location_id) == $location->id ? 'selected' : '' }}>
                                        {{ $location->building }} - Floor {{ $location->floor }} - Room {{ $location->room }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Instructor Name -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700" for="instructor">
                                Instructor Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="instructor" 
                                   id="instructor" 
                                   value="{{ old('instructor', $checklist->instructor ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                   placeholder="Instructor name"
                                   required>
                        </div>

                        <!-- Digital Signature -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700" for="instructor_signature">
                                Digital Signature
                            </label>
                            <input type="text" 
                                   name="instructor_signature" 
                                   id="instructor_signature" 
                                   value="{{ old('instructor_signature', $checklist->instructor_signature ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                   placeholder="Digital signature">
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    <div class="mt-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2" for="notes">
                            Additional Notes
                        </label>
                        <textarea name="notes" 
                                  id="notes" 
                                  rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                  placeholder="Enter any additional notes about this checklist">{{ old('notes', $checklist->notes ?? '') }}</textarea>
                    </div>
                </div>

                <!-- Read-Only Information Section -->
                <div class="bg-gray-50 px-8 py-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-info-circle text-gray-600 mr-3"></i>
                        Maintenance Process Information
                        <span class="ml-3 text-sm font-normal text-gray-500">(Read-only - Updated during maintenance process)</span>
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Checked By -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Checked By</label>
                            <div class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-600">
                                {{ $checklist->checked_by ?? 'Not specified' }}
                            </div>
                        </div>

                        <!-- Date Checked -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Date Checked</label>
                            <div class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-600">
                                {{ $checklist->date_checked ? $checklist->date_checked->format('M d, Y') : 'Not specified' }}
                            </div>
                        </div>

                        <!-- GSU Staff -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">GSU Staff</label>
                            <div class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-600">
                                {{ $checklist->gsu_staff ?? 'Not specified' }}
                            </div>
                        </div>

                        <!-- Checked By Signature -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Checked By Signature</label>
                            <div class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-600">
                                {{ $checklist->checked_by_signature ?? 'Not provided' }}
                            </div>
                        </div>

                        <!-- GSU Staff Signature -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">GSU Staff Signature</label>
                            <div class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-600">
                                {{ $checklist->gsu_staff_signature ?? 'Not provided' }}
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Status</label>
                            <div class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $checklist->status === 'created' ? 'bg-blue-100 text-blue-800' : 
                                       ($checklist->status === 'acknowledged' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($checklist->status === 'in_progress' ? 'bg-orange-100 text-orange-800' : 
                                       ($checklist->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'))) }}">
                                    {{ ucfirst(str_replace('_', ' ', $checklist->status)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Information Section (Read-Only) -->
                <div class="px-8 py-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-list-check text-gray-600 mr-3"></i>
                        Maintenance Items
                        <span class="ml-3 text-sm font-normal text-gray-500">(Read-only - Managed during maintenance process)</span>
                    </h2>
                    
                    @if($checklist->items && $checklist->items->count() > 0)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-4">
                                <i class="fas fa-info-circle mr-2"></i>
                                This checklist has {{ $checklist->items->count() }} maintenance items. Items are managed during the maintenance process and cannot be edited here.
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($checklist->items as $item)
                                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                                        <div class="flex justify-between items-start mb-2">
                                            <h4 class="font-semibold text-gray-800">{{ $item->particulars ?? 'Unnamed Item' }}</h4>
                                            <span class="text-sm text-gray-500">Qty: {{ $item->quantity ?? 0 }}</span>
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            <div class="mb-1">
                                                <span class="font-medium">Asset Code:</span> {{ $item->asset_code ?? 'N/A' }}
                                            </div>
                                            <div class="mb-1">
                                                <span class="font-medium">Start Status:</span> 
                                                <span class="px-2 py-1 rounded-full text-xs {{ $item->start_status === 'OK' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ $item->start_status ?? 'N/A' }}
                                                </span>
                                            </div>
                                            @if($item->end_status)
                                                <div class="mb-1">
                                                    <span class="font-medium">End Status:</span> 
                                                    <span class="px-2 py-1 rounded-full text-xs {{ $item->end_status === 'OK' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                        {{ $item->end_status }}
                                                    </span>
                                                </div>
                                            @endif
                                            @if($item->notes)
                                                <div class="text-xs text-gray-500 mt-2">
                                                    <span class="font-medium">Notes:</span> {{ $item->notes }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-lg p-8 text-center">
                            <i class="fas fa-list text-gray-400 text-4xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-600 mb-2">No Maintenance Items</h3>
                            <p class="text-gray-500">Items will be added during the maintenance process.</p>
                        </div>
                    @endif
                </div>

                <!-- Form Actions -->
                <div class="bg-gray-50 px-8 py-6 flex justify-end gap-4">
                    <a href="{{ route('maintenance-checklists.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center gap-2">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" 
                            class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center gap-2">
                        <i class="fas fa-save"></i> Update Checklist
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple form validation for basic information
    document.getElementById('checklistForm').addEventListener('submit', function(e) {
        const requiredFields = [
            'school_year',
            'department', 
            'date_reported',
            'location_id',
            'instructor'
        ];
        
        let hasErrors = false;
        
        requiredFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (!field.value.trim()) {
                field.classList.add('border-red-500');
                hasErrors = true;
            } else {
                field.classList.remove('border-red-500');
            }
        });
        
        if (hasErrors) {
            e.preventDefault();
            alert('Please fill in all required fields.');
            return false;
        }
    });
});
</script>
@endsection