@extends('layouts.admin')

@section('title', 'Edit Semester - Asset Management System')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-indigo-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 text-white p-4 md:p-6 mb-6 rounded-xl shadow-lg relative overflow-hidden mx-4 mt-4">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold flex items-center gap-3">
                        <i class="fas fa-edit text-2xl"></i>
                        Edit Semester
                    </h1>
                    <p class="text-indigo-100 text-sm md:text-base mt-2">
                        Modify {{ $semester->academic_year }} - {{ $semester->name }}
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('semesters.show', $semester) }}" 
                       class="inline-flex items-center px-4 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-colors">
                        <i class="fas fa-eye mr-2"></i>
                        View
                    </a>
                    <a href="{{ route('semesters.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <!-- Current Status Alert -->
        @if($semester->is_current)
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-3"></i>
                    <div>
                        <h4 class="text-green-800 font-medium">Current Active Semester</h4>
                        <p class="text-green-700 text-sm">This is the currently active semester. Assets are being registered to this semester.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 max-w-2xl mx-auto">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Semester Information</h2>
            </div>
            
            <form action="{{ route('semesters.update', $semester) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                
                <!-- Academic Year and Semester Name -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-2">
                            Academic Year <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="academic_year" 
                               name="academic_year" 
                               value="{{ old('academic_year', $semester->academic_year) }}" 
                               required
                               placeholder="e.g., 2025-2026"
                               pattern="\d{4}-\d{4}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('academic_year') border-red-500 ring-2 ring-red-200 @enderror">
                        @error('academic_year')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Format: YYYY-YYYY (e.g., 2025-2026)</p>
                    </div>
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Semester Name <span class="text-red-500">*</span>
                        </label>
                        <select id="name" 
                                name="name" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 ring-2 ring-red-200 @enderror">
                            <option value="">Select Semester</option>
                            <option value="1st Semester" {{ old('name', $semester->name) == '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                            <option value="2nd Semester" {{ old('name', $semester->name) == '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                            <option value="Summer" {{ old('name', $semester->name) == 'Summer' ? 'selected' : '' }}>Summer</option>
                            <option value="Intersession" {{ old('name', $semester->name) == 'Intersession' ? 'selected' : '' }}>Intersession</option>
                            <option value="Special Term" {{ old('name', $semester->name) == 'Special Term' ? 'selected' : '' }}>Special Term</option>
                        </select>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Date Range -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Start Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               id="start_date" 
                               name="start_date" 
                               value="{{ old('start_date', $semester->start_date->format('Y-m-d')) }}" 
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('start_date') border-red-500 ring-2 ring-red-200 @enderror">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            End Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               id="end_date" 
                               name="end_date" 
                               value="{{ old('end_date', $semester->end_date->format('Y-m-d')) }}" 
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('end_date') border-red-500 ring-2 ring-red-200 @enderror">
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                              placeholder="Optional description for this semester"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 ring-2 ring-red-200 @enderror">{{ old('description', $semester->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="mb-6">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="is_active" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', $semester->is_active) ? 'checked' : '' }}
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                            Active Semester
                        </label>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Only active semesters can be used for asset tracking and set as current
                    </p>
                    @if($semester->is_current)
                        <p class="mt-1 text-xs text-yellow-600">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Warning: Deactivating the current semester will require setting a new current semester.
                        </p>
                    @endif
                </div>

                <!-- Error Messages -->
                @if($errors->has('date_overlap'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex">
                            <i class="fas fa-exclamation-triangle text-red-400 mr-2 mt-0.5"></i>
                            <div>
                                <h3 class="text-sm font-medium text-red-800">Date Overlap Error</h3>
                                <p class="text-sm text-red-700 mt-1">{{ $errors->first('date_overlap') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Asset Usage Warning -->
                @if($semester->registeredAssets()->exists() || $semester->assetChanges()->exists())
                    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex">
                            <i class="fas fa-info-circle text-yellow-400 mr-2 mt-0.5"></i>
                            <div>
                                <h3 class="text-sm font-medium text-yellow-800">Semester In Use</h3>
                                <p class="text-sm text-yellow-700 mt-1">
                                    This semester has {{ $semester->registeredAssets()->count() }} registered assets and {{ $semester->assetChanges()->count() }} asset changes. 
                                    Modifying dates may affect historical tracking accuracy.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('semesters.show', $semester) }}" 
                       class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Update Semester
                    </button>
                </div>
            </form>
        </div>

        <!-- Danger Zone -->
        @if(!$semester->is_current && !$semester->registeredAssets()->exists() && !$semester->assetChanges()->exists())
            <div class="mt-8 bg-white rounded-xl shadow-lg border border-red-200 max-w-2xl mx-auto">
                <div class="px-6 py-4 border-b border-red-200 bg-red-50">
                    <h3 class="text-lg font-semibold text-red-900">Danger Zone</h3>
                    <p class="text-sm text-red-700">Irreversible actions for this semester</p>
                </div>
                
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium text-gray-900">Delete Semester</div>
                            <div class="text-sm text-gray-500">
                                Permanently delete this semester. This action cannot be undone.
                            </div>
                        </div>
                        
                        <form action="{{ route('semesters.destroy', $semester) }}" 
                              method="POST" 
                              class="inline"
                              onsubmit="return confirm('Are you sure you want to delete this semester? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                <i class="fas fa-trash mr-2"></i>
                                Delete Semester
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
