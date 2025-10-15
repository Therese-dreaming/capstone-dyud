@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Depreciation Settings</h1>
        <p class="text-gray-600">{{ $asset->name }} ({{ $asset->asset_code }})</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <form method="POST" action="{{ route('depreciation.update', $asset) }}">
                    @csrf
                    @method('PUT')

                    <!-- Depreciation Method -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Depreciation Method <span class="text-red-500">*</span>
                        </label>
                        <select name="depreciation_method" id="depreciation_method" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('depreciation_method') border-red-500 @enderror"
                                required>
                            <option value="straight_line" {{ old('depreciation_method', $asset->depreciation_method) == 'straight_line' ? 'selected' : '' }}>
                                Straight-Line (Equal depreciation each year)
                            </option>
                            <option value="declining_balance" {{ old('depreciation_method', $asset->depreciation_method) == 'declining_balance' ? 'selected' : '' }}>
                                Declining Balance (Higher depreciation in early years)
                            </option>
                            <option value="sum_of_years_digits" {{ old('depreciation_method', $asset->depreciation_method) == 'sum_of_years_digits' ? 'selected' : '' }}>
                                Sum of Years Digits (Accelerated depreciation)
                            </option>
                        </select>
                        @error('depreciation_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Choose the depreciation calculation method</p>
                    </div>

                    <!-- Useful Life Years -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Useful Life (Years) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="useful_life_years" step="0.01" min="0.1" max="100"
                               value="{{ old('useful_life_years', $asset->useful_life_years) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('useful_life_years') border-red-500 @enderror"
                               required>
                        @error('useful_life_years')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Expected lifespan of the asset in years</p>
                    </div>

                    <!-- Salvage Value -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Salvage Value (₱) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="salvage_value" step="0.01" min="0"
                               value="{{ old('salvage_value', $asset->salvage_value) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('salvage_value') border-red-500 @enderror"
                               required>
                        @error('salvage_value')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">
                            Estimated residual value at end of useful life (max: ₱{{ number_format($asset->purchase_cost, 2) }})
                        </p>
                    </div>

                    <!-- Declining Balance Rate (conditional) -->
                    <div class="mb-6" id="declining_balance_rate_field" style="display: {{ old('depreciation_method', $asset->depreciation_method) == 'declining_balance' ? 'block' : 'none' }};">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Declining Balance Rate
                        </label>
                        <input type="number" name="declining_balance_rate" step="0.01" min="1" max="5"
                               value="{{ old('declining_balance_rate', $asset->declining_balance_rate) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('declining_balance_rate') border-red-500 @enderror">
                        @error('declining_balance_rate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">
                            Multiplier for declining balance method (2 = double declining, 1.5 = 150% declining)
                        </p>
                    </div>

                    <!-- Depreciation Start Date -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Depreciation Start Date
                        </label>
                        <input type="date" name="depreciation_start_date"
                               value="{{ old('depreciation_start_date', $asset->depreciation_start_date ? $asset->depreciation_start_date->format('Y-m-d') : '') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('depreciation_start_date') border-red-500 @enderror">
                        @error('depreciation_start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">
                            Leave blank to use purchase date ({{ $asset->purchase_date->format('M d, Y') }})
                        </p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                        <a href="{{ route('depreciation.show', $asset) }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="lg:col-span-1">
            <!-- Asset Info -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Asset Information</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Purchase Cost</p>
                        <p class="font-medium text-lg">₱{{ number_format($asset->purchase_cost, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Purchase Date</p>
                        <p class="font-medium">{{ $asset->purchase_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Category</p>
                        <p class="font-medium">{{ $asset->category->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Method Descriptions -->
            <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-3">
                    <i class="fas fa-info-circle mr-2"></i>Depreciation Methods
                </h3>
                <div class="space-y-4 text-sm">
                    <div>
                        <p class="font-semibold text-blue-900">Straight-Line</p>
                        <p class="text-blue-800">Equal depreciation expense each year. Simple and commonly used.</p>
                    </div>
                    <div>
                        <p class="font-semibold text-blue-900">Declining Balance</p>
                        <p class="text-blue-800">Higher depreciation in early years. Good for assets that lose value quickly.</p>
                    </div>
                    <div>
                        <p class="font-semibold text-blue-900">Sum of Years Digits</p>
                        <p class="text-blue-800">Accelerated depreciation with gradually decreasing amounts each year.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const methodSelect = document.getElementById('depreciation_method');
    const rateField = document.getElementById('declining_balance_rate_field');
    
    methodSelect.addEventListener('change', function() {
        if (this.value === 'declining_balance') {
            rateField.style.display = 'block';
        } else {
            rateField.style.display = 'none';
        }
    });
});
</script>
@endsection
