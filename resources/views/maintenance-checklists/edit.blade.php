@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto bg-white rounded-xl shadow-lg p-10">
    <h2 class="text-3xl font-bold mb-8 text-gray-800 flex items-center gap-3">
        <i class="fas fa-edit text-red-800"></i> Edit Maintenance Checklist
    </h2>
    
    @if($errors->any())
        <div id="toast-error" class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-fade-in" style="min-width: 300px; max-width: 90vw;">
            <i class="fas fa-exclamation-circle text-xl"></i>
            <div class="flex-1">
                <strong class="block">Error</strong>
                <span class="block text-sm">{{ $errors->first() }}</span>
            </div>
            <button onclick="document.getElementById('toast-error').style.display='none'" class="ml-4 text-white hover:text-gray-200 focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <script>
            setTimeout(function() {
                var toast = document.getElementById('toast-error');
                if (toast) toast.style.display = 'none';
            }, 6000);
        </script>
    @endif

    <form action="{{ route('maintenance-checklists.update', $checklist) }}" method="POST" id="checklistForm">
        @csrf
        @method('PUT')
        
        <!-- Header Information Section -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Header Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="school_year">School Year *</label>
                    <input type="text" name="school_year" id="school_year" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" 
                           value="{{ old('school_year', $checklist->school_year) }}" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="department">Department *</label>
                    <select name="department" id="department" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" required>
                        <option value="">Select Department</option>
                        <option value="Grade School" {{ old('department', $checklist->department) == 'Grade School' ? 'selected' : '' }}>Grade School</option>
                        <option value="Junior High School" {{ old('department', $checklist->department) == 'Junior High School' ? 'selected' : '' }}>Junior High School</option>
                        <option value="Senior High School" {{ old('department', $checklist->department) == 'Senior High School' ? 'selected' : '' }}>Senior High School</option>
                        <option value="College" {{ old('department', $checklist->department) == 'College' ? 'selected' : '' }}>College</option>
                        <option value="Administration" {{ old('department', $checklist->department) == 'Administration' ? 'selected' : '' }}>Administration</option>
                        <option value="Library" {{ old('department', $checklist->department) == 'Library' ? 'selected' : '' }}>Library</option>
                        <option value="Laboratory" {{ old('department', $checklist->department) == 'Laboratory' ? 'selected' : '' }}>Laboratory</option>
                        <option value="Other" {{ old('department', $checklist->department) == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="date_reported">Date Reported *</label>
                    <input type="date" name="date_reported" id="date_reported" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" 
                           value="{{ old('date_reported', $checklist->date_reported->format('Y-m-d')) }}" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="program">Program</label>
                    <input type="text" name="program" id="program" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" 
                           value="{{ old('program', $checklist->program) }}">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="location_id">Room Number *</label>
                    <select name="location_id" id="room_number" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" required>
                        <option value="">Select Room</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" 
                                    data-building="{{ $location->building }}" 
                                    data-floor="{{ $location->floor }}"
                                    data-room="{{ $location->room }}"
                                    {{ old('location_id', $checklist->location_id) == $location->id ? 'selected' : '' }}>
                                {{ $location->building }} - {{ $location->floor }} - {{ $location->room }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="instructor">Instructor *</label>
                    <input type="text" name="instructor" id="instructor" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" 
                           value="{{ old('instructor', $checklist->instructor) }}" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="instructor_signature">Instructor Signature</label>
                    <input type="text" name="instructor_signature" id="instructor_signature" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" 
                           value="{{ old('instructor_signature', $checklist->instructor_signature) }}" placeholder="Enter signature or leave blank">
                </div>
            </div>
        </div>

        <!-- Footer Information Section -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Footer Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="checked_by">Checked By *</label>
                    <input type="text" name="checked_by" id="checked_by" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" 
                           value="{{ old('checked_by', $checklist->checked_by) }}" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="checked_by_signature">Checked By Signature</label>
                    <input type="text" name="checked_by_signature" id="checked_by_signature" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" 
                           value="{{ old('checked_by_signature', $checklist->checked_by_signature) }}" placeholder="Enter signature or leave blank">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="date_checked">Date Checked *</label>
                    <input type="date" name="date_checked" id="date_checked" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" 
                           value="{{ old('date_checked', $checklist->date_checked->format('Y-m-d')) }}" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="gsu_staff">GSU Staff *</label>
                    <input type="text" name="gsu_staff" id="gsu_staff" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" 
                           value="{{ old('gsu_staff', $checklist->gsu_staff) }}" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="gsu_staff_signature">GSU Staff Signature</label>
                    <input type="text" name="gsu_staff_signature" id="gsu_staff_signature" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" 
                           value="{{ old('gsu_staff_signature', $checklist->gsu_staff_signature) }}" placeholder="Enter signature or leave blank">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="notes">General Notes</label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800"
                              placeholder="Enter any general notes about this checklist">{{ old('notes', $checklist->notes) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Items Section -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Maintenance Items</h3>
                <button type="button" id="addItemBtn" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add Item
                </button>
            </div>
            
            <div id="itemsContainer">
                <!-- Items will be loaded here -->
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('maintenance-checklists.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                Cancel
            </a>
            <button type="submit" 
                    class="bg-gradient-to-r from-red-800 to-red-900 hover:from-red-900 hover:to-red-950 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center gap-2">
                <i class="fas fa-save"></i> Update Checklist
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemCounter = 0;
    
    // Load existing items
    @foreach($checklist->items as $item)
        addItem({
            asset_code: '{{ $item->asset_code }}',
            name: '{{ $item->particulars }}',
            quantity: {{ $item->quantity }},
            start_status: '{{ $item->start_status }}',
            end_status: '{{ $item->end_status }}',
            notes: '{{ $item->notes }}'
        });
    @endforeach
    
    // Add item button functionality
    document.getElementById('addItemBtn').addEventListener('click', function() {
        addItem();
    });
    
    function addItem(assetData = null) {
        const container = document.getElementById('itemsContainer');
        const itemDiv = document.createElement('div');
        itemDiv.className = 'border border-gray-300 rounded-lg p-4 mb-4 bg-white';
        
        const assetCode = assetData ? assetData.asset_code : '';
        const particulars = assetData ? assetData.name : '';
        const quantity = assetData ? assetData.quantity : '';
        const startStatus = assetData ? assetData.start_status : '';
        const endStatus = assetData ? assetData.end_status : '';
        const notes = assetData ? assetData.notes : '';
        
        itemDiv.innerHTML = `
            <div class="flex justify-between items-center mb-3">
                <h4 class="font-semibold text-gray-800">Item ${itemCounter + 1}</h4>
                <button type="button" class="text-red-600 hover:text-red-800" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Asset Code</label>
                    <input type="text" name="items[${itemCounter}][asset_code]" value="${assetCode}" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Particulars/Items *</label>
                    <input type="text" name="items[${itemCounter}][particulars]" value="${particulars}" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Quantity *</label>
                    <input type="number" name="items[${itemCounter}][quantity]" value="${quantity}" min="0" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Start of SY Status *</label>
                    <select name="items[${itemCounter}][start_status]" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" required>
                        <option value="">Select Status</option>
                        <option value="OK" ${startStatus === 'OK' ? 'selected' : ''}>OK</option>
                        <option value="FOR REPAIR" ${startStatus === 'FOR REPAIR' ? 'selected' : ''}>FOR REPAIR</option>
                        <option value="FOR REPLACEMENT" ${startStatus === 'FOR REPLACEMENT' ? 'selected' : ''}>FOR REPLACEMENT</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">End of SY Status</label>
                    <select name="items[${itemCounter}][end_status]" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800">
                        <option value="">Select Status</option>
                        <option value="OK" ${endStatus === 'OK' ? 'selected' : ''}>OK</option>
                        <option value="FOR REPAIR" ${endStatus === 'FOR REPAIR' ? 'selected' : ''}>FOR REPAIR</option>
                        <option value="FOR REPLACEMENT" ${endStatus === 'FOR REPLACEMENT' ? 'selected' : ''}>FOR REPLACEMENT</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <label class="block text-gray-700 font-semibold mb-2">Notes</label>
                <textarea name="items[${itemCounter}][notes]" rows="2" 
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800">${notes}</textarea>
            </div>
        `;
        container.appendChild(itemDiv);
        itemCounter++;
    }
    
    // Form validation
    document.getElementById('checklistForm').addEventListener('submit', function(e) {
        const itemsContainer = document.getElementById('itemsContainer');
        const items = itemsContainer.querySelectorAll('.border.border-gray-300.rounded-lg.p-4.mb-4.bg-white');
        
        if (items.length === 0) {
            e.preventDefault();
            alert('Please add at least one maintenance item.');
            return false;
        }
        
        // Check if all required fields are filled
        let hasErrors = false;
        items.forEach((item, index) => {
            const particulars = item.querySelector(`input[name="items[${index}][particulars]"]`);
            const quantity = item.querySelector(`input[name="items[${index}][quantity]"]`);
            const startStatus = item.querySelector(`select[name="items[${index}][start_status]"]`);
            
            if (!particulars.value.trim()) {
                alert(`Please fill in the particulars for item ${index + 1}`);
                hasErrors = true;
                return;
            }
            
            if (!quantity.value || quantity.value <= 0) {
                alert(`Please enter a valid quantity for item ${index + 1}`);
                hasErrors = true;
                return;
            }
            
            if (!startStatus.value) {
                alert(`Please select a start status for item ${index + 1}`);
                hasErrors = true;
                return;
            }
        });
        
        if (hasErrors) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endsection
