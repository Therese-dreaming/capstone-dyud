@extends('layouts.purchasing')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Register New Asset</h1>
                <p class="mt-1 text-sm text-gray-600">Create a new asset entry for admin approval</p>
            </div>
            <a href="{{ route('purchasing.assets.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Assets
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Asset Information</h2>
        </div>
        
        <form action="{{ route('purchasing.assets.store') }}" method="POST" class="p-6">
            @csrf
            
            <!-- Asset Code and Name -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Asset Code
                    </label>
                    <div class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 flex items-center">
                        <i class="fas fa-magic text-purple-600 mr-2"></i>
                        <span class="text-gray-600 font-medium">Auto-generated upon creation</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        A unique asset code will be automatically assigned when you create this asset
                    </p>
                </div>
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Asset Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required
                           placeholder="Enter descriptive asset name"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('name') border-red-500 ring-2 ring-red-200 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Category and Condition -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select id="category_id" 
                            name="category_id" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('category_id') border-red-500 ring-2 ring-red-200 @enderror">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="condition" class="block text-sm font-medium text-gray-700 mb-2">
                        Condition <span class="text-red-500">*</span>
                    </label>
                    <select id="condition" 
                            name="condition" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('condition') border-red-500 ring-2 ring-red-200 @enderror">
                        <option value="">Select Condition</option>
                        <option value="Excellent" {{ old('condition') == 'Excellent' ? 'selected' : '' }}>Excellent</option>
                        <option value="Good" {{ old('condition') == 'Good' ? 'selected' : '' }}>Good</option>
                        <option value="Fair" {{ old('condition') == 'Fair' ? 'selected' : '' }}>Fair</option>
                        <option value="Poor" {{ old('condition') == 'Poor' ? 'selected' : '' }}>Poor</option>
                    </select>
                    @error('condition')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Purchase Cost and Date -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="purchase_cost" class="block text-sm font-medium text-gray-700 mb-2">
                        Purchase Cost <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-sm">₱</span>
                        </div>
                        <input type="number" 
                               step="0.01" 
                               min="0" 
                               id="purchase_cost" 
                               name="purchase_cost" 
                               value="{{ old('purchase_cost') }}" 
                               required
                               class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('purchase_cost') border-red-500 ring-2 ring-red-200 @enderror">
                    </div>
                    @error('purchase_cost')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="purchase_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Purchase Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="purchase_date" 
                           name="purchase_date" 
                           value="{{ old('purchase_date') }}" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('purchase_date') border-red-500 ring-2 ring-red-200 @enderror">
                    @error('purchase_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Warranty Information -->
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <i class="fas fa-shield-alt text-purple-600 mr-2"></i>
                    <h3 class="text-lg font-semibold text-gray-900">Warranty Information</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="manufacturer" class="block text-sm font-medium text-gray-700 mb-2">
                            Manufacturer <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="manufacturer" 
                               name="manufacturer" 
                               value="{{ old('manufacturer') }}" 
                               required
                               placeholder="e.g., Dell, HP, Canon"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('manufacturer') border-red-500 ring-2 ring-red-200 @enderror">
                        @error('manufacturer')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="model" class="block text-sm font-medium text-gray-700 mb-2">
                            Model <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="model" 
                               name="model" 
                               value="{{ old('model') }}" 
                               required
                               placeholder="e.g., Latitude 5520, LaserJet Pro"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('model') border-red-500 ring-2 ring-red-200 @enderror">
                        @error('model')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="warranty_expiry" class="block text-sm font-medium text-gray-700 mb-2">
                            Warranty Expiry Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               id="warranty_expiry" 
                               name="warranty_expiry" 
                               value="{{ old('warranty_expiry') }}" 
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('warranty_expiry') border-red-500 ring-2 ring-red-200 @enderror">
                        @error('warranty_expiry')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Date when the manufacturer's warranty expires
                        </p>
                    </div>
                    
                    <!-- Warranty Status Indicator -->
                    <div class="flex items-center">
                        <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg w-full">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-check text-purple-600 mr-2"></i>
                                <div>
                                    <p class="text-sm font-medium text-purple-900">Warranty Tracking</p>
                                    <p class="text-xs text-purple-700">System will monitor warranty status automatically</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="3" 
                          placeholder="Enter asset description (optional)"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('description') border-red-500 ring-2 ring-red-200 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Notice -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-3"></i>
                    <div>
                        <h4 class="text-sm font-medium text-blue-900 mb-1">Approval Process</h4>
                        <p class="text-sm text-blue-700">
                            This asset will be submitted for admin approval. Location assignment will be handled by GSU after approval.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('purchasing.assets.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-colors">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Submit for Approval
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
