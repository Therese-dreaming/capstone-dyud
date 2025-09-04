@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto bg-white rounded-xl shadow-lg p-10">
    <h2 class="text-3xl font-bold mb-8 text-gray-800 flex items-center gap-3">
        <i class="fas fa-tools text-red-800"></i> Batch Maintenance Records
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
            <h3 class="text-lg font-semibold text-blue-800 mb-2">Batch Maintenance Instructions</h3>
            <p class="text-blue-700 text-sm">
                Create maintenance records for multiple assets at once. Select the assets and set the maintenance details.
            </p>
        </div>
    </div>

    <form action="{{ route('maintenances.batch-store') }}" method="POST" id="batchMaintenanceForm">
        @csrf
        
        <!-- Batch Maintenance Details -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Maintenance Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="type">Type *</label>
                    <select name="type" id="type" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" required>
                        <option value="">Select Type</option>
                        <option value="Preventive">Preventive</option>
                        <option value="Corrective">Corrective</option>
                        <option value="Emergency">Emergency</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="technician">Technician *</label>
                    <input type="text" name="technician" id="technician" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="status">Status *</label>
                    <select name="status" id="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" required>
                        <option value="">Select Status</option>
                        <option value="Scheduled">Scheduled</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="scheduled_date">Scheduled Date *</label>
                    <input type="date" name="scheduled_date" id="scheduled_date" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="completed_date">Completed Date</label>
                    <input type="date" name="completed_date" id="completed_date" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="cost">Cost</label>
                    <input type="number" step="0.01" name="cost" id="cost" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2" for="description">Description</label>
                    <textarea name="description" id="description" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" rows="3"></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2" for="notes">Notes</label>
                    <textarea name="notes" id="notes" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" rows="3"></textarea>
                </div>
            </div>
        </div>

        <!-- Asset Selection -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Select Assets</h3>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="selectAllAssets" class="rounded border-gray-300 text-red-800 focus:ring-red-800">
                    <label for="selectAllAssets" class="text-sm font-medium text-gray-700">Select All</label>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="selectAllTableAssets" class="rounded border-gray-300 text-red-800 focus:ring-red-800">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset Code</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($assets as $asset)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <input type="checkbox" name="selected_assets[]" value="{{ $asset->id }}" class="asset-checkbox rounded border-gray-300 text-red-800 focus:ring-red-800">
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 font-mono">
                                {{ $asset->asset_code }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $asset->name }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $asset->category->name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $asset->location->building ?? 'N/A' }} - {{ $asset->location->room ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $asset->status === 'Available' ? 'bg-green-100 text-green-800' : 
                                       ($asset->status === 'In Use' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $asset->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('maintenances.history') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                Cancel
            </a>
            <button type="submit" 
                    class="bg-gradient-to-r from-red-800 to-red-900 hover:from-red-900 hover:to-red-950 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center gap-2">
                <i class="fas fa-save"></i> Create Batch Maintenance Records
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAllAssets');
    const selectAllTableCheckbox = document.getElementById('selectAllTableAssets');
    const assetCheckboxes = document.querySelectorAll('.asset-checkbox');

    // Select All functionality
    function updateSelectAll() {
        const checkedCount = document.querySelectorAll('.asset-checkbox:checked').length;
        const totalCount = assetCheckboxes.length;
        
        selectAllCheckbox.checked = checkedCount === totalCount;
        selectAllTableCheckbox.checked = checkedCount === totalCount;
    }

    selectAllCheckbox.addEventListener('change', function() {
        assetCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectAll();
    });

    selectAllTableCheckbox.addEventListener('change', function() {
        assetCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectAll();
    });

    assetCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectAll);
    });

    // Form validation
    document.getElementById('batchMaintenanceForm').addEventListener('submit', function(e) {
        const selectedAssets = document.querySelectorAll('.asset-checkbox:checked');
        if (selectedAssets.length === 0) {
            e.preventDefault();
            alert('Please select at least one asset.');
            return false;
        }
    });
});
</script>
@endsection
