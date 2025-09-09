@extends('layouts.gsu')

@section('content')
<div class="max-w-6xl mx-auto bg-white rounded-xl shadow-lg p-10">
    <h2 class="text-3xl font-bold mb-8 text-gray-800 flex items-center gap-3">
        <i class="fas fa-tasks text-red-800"></i> Batch Status Update
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

    <div class="mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-blue-800 mb-2">Batch Update Instructions</h3>
            <p class="text-blue-700 text-sm">
                Use this feature to quickly update the "End of SY Status" for multiple items at once. 
                Select the items you want to update and choose the new status.
            </p>
        </div>
    </div>

    <form action="{{ route('maintenance-checklists.batch-update', $checklist) }}" method="POST" id="batchForm">
        @csrf
        @method('PUT')
        
        <!-- Batch Actions -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Batch Actions</h3>
            <div class="flex flex-wrap gap-4 items-center">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-red-800 focus:ring-red-800">
                    <label for="selectAll" class="text-sm font-medium text-gray-700">Select All</label>
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700">Set Status for Selected:</label>
                    <select name="batch_status" id="batchStatus" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-red-800">
                        <option value="">Choose Status</option>
                        <option value="OK">OK</option>
                        <option value="FOR REPAIR">FOR REPAIR</option>
                        <option value="FOR REPLACEMENT">FOR REPLACEMENT</option>
                    </select>
                </div>
                <button type="button" id="applyBatchBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                    Apply to Selected
                </button>
            </div>
        </div>

        <!-- Items List -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Maintenance Items</h3>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="selectAllTable" class="rounded border-gray-300 text-red-800 focus:ring-red-800">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset Code</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($checklist->items as $index => $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <input type="checkbox" name="selected_items[]" value="{{ $index }}" class="item-checkbox rounded border-gray-300 text-red-800 focus:ring-red-800">
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $item->particulars }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                                {{ $item->asset_code ?? '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $item->start_status === 'OK' ? 'bg-green-100 text-green-800' : 
                                       ($item->start_status === 'FOR REPAIR' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $item->start_status }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <select name="items[{{ $index }}][end_status]" 
                                        class="end-status-select border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-red-800">
                                    <option value="">Select Status</option>
                                    <option value="OK" {{ $item->end_status === 'OK' ? 'selected' : '' }}>OK</option>
                                    <option value="FOR REPAIR" {{ $item->end_status === 'FOR REPAIR' ? 'selected' : '' }}>FOR REPAIR</option>
                                    <option value="FOR REPLACEMENT" {{ $item->end_status === 'FOR REPLACEMENT' ? 'selected' : '' }}>FOR REPLACEMENT</option>
                                </select>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900">
                                <textarea name="items[{{ $index }}][notes]" rows="2" 
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-red-800"
                                          placeholder="Notes">{{ $item->notes }}</textarea>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('maintenance-checklists.show', $checklist) }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                Cancel
            </a>
            <button type="submit" 
                    class="bg-gradient-to-r from-red-800 to-red-900 hover:from-red-900 hover:to-red-950 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center gap-2">
                <i class="fas fa-save"></i> Update All Items
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const selectAllTableCheckbox = document.getElementById('selectAllTable');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const batchStatusSelect = document.getElementById('batchStatus');
    const applyBatchBtn = document.getElementById('applyBatchBtn');
    const endStatusSelects = document.querySelectorAll('.end-status-select');

    // Select All functionality
    function updateSelectAll() {
        const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
        const totalCount = itemCheckboxes.length;
        
        selectAllCheckbox.checked = checkedCount === totalCount;
        selectAllTableCheckbox.checked = checkedCount === totalCount;
    }

    selectAllCheckbox.addEventListener('change', function() {
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectAll();
    });

    selectAllTableCheckbox.addEventListener('change', function() {
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectAll();
    });

    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectAll);
    });

    // Batch apply functionality
    applyBatchBtn.addEventListener('click', function() {
        const selectedStatus = batchStatusSelect.value;
        if (!selectedStatus) {
            alert('Please select a status to apply.');
            return;
        }

        const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
        if (selectedCheckboxes.length === 0) {
            alert('Please select at least one item.');
            return;
        }

        if (confirm(`Are you sure you want to set "${selectedStatus}" for ${selectedCheckboxes.length} selected item(s)?`)) {
            selectedCheckboxes.forEach(checkbox => {
                const row = checkbox.closest('tr');
                const endStatusSelect = row.querySelector('.end-status-select');
                endStatusSelect.value = selectedStatus;
            });
        }
    });

    // Form validation
    document.getElementById('batchForm').addEventListener('submit', function(e) {
        const endStatusSelects = document.querySelectorAll('.end-status-select');
        let hasEmptyStatus = false;

        endStatusSelects.forEach((select, index) => {
            if (!select.value) {
                hasEmptyStatus = true;
            }
        });

        if (hasEmptyStatus) {
            if (!confirm('Some items do not have an end status selected. Do you want to continue anyway?')) {
                e.preventDefault();
                return false;
            }
        }
    });
});
</script>
@endsection
