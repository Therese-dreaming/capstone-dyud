@extends('layouts.admin')

@section('title', 'Create Semester - Asset Management System')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-indigo-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 text-white p-4 md:p-6 mb-6 rounded-xl shadow-lg relative overflow-hidden mx-4 mt-4">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold flex items-center gap-3">
                        <i class="fas fa-plus-circle text-2xl"></i>
                        Create New Semester
                    </h1>
                    <p class="text-indigo-100 text-sm md:text-base mt-2">
                        Add a new academic semester for asset tracking
                    </p>
                </div>
                <a href="{{ route('semesters.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Semesters
                </a>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 max-w-2xl mx-auto">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Semester Information</h2>
            </div>
            
            <form action="{{ route('semesters.store') }}" method="POST" class="p-6">
                @csrf
                
                <!-- Academic Year and Semester Name -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-2">
                            Academic Year <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="academic_year" 
                               name="academic_year" 
                               value="{{ old('academic_year') }}" 
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
                            <option value="1st Semester" {{ old('name') == '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                            <option value="2nd Semester" {{ old('name') == '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                            <option value="Summer" {{ old('name') == 'Summer' ? 'selected' : '' }}>Summer</option>
                            <option value="Intersession" {{ old('name') == 'Intersession' ? 'selected' : '' }}>Intersession</option>
                            <option value="Special Term" {{ old('name') == 'Special Term' ? 'selected' : '' }}>Special Term</option>
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
                               value="{{ old('start_date') }}" 
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
                               value="{{ old('end_date') }}" 
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
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 ring-2 ring-red-200 @enderror">{{ old('description') }}</textarea>
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
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                            Active Semester
                        </label>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Only active semesters can be used for asset tracking and set as current
                    </p>
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

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('semesters.index') }}" 
                       class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Create Semester
                    </button>
                </div>
            </form>
        </div>

        <!-- Quick Create Templates -->
        <div class="mt-8 bg-white rounded-xl shadow-lg border border-gray-200 max-w-2xl mx-auto">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Quick Templates</h3>
                <p class="text-sm text-gray-600">Click to auto-fill common semester patterns</p>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button type="button" 
                            onclick="fillTemplate('1st', '2025-2026', '2025-08-01', '2025-12-31')"
                            class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-left">
                        <div class="font-medium text-gray-900">1st Semester</div>
                        <div class="text-sm text-gray-500">Aug - Dec</div>
                    </button>
                    
                    <button type="button" 
                            onclick="fillTemplate('2nd', '2025-2026', '2026-01-01', '2026-05-31')"
                            class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-left">
                        <div class="font-medium text-gray-900">2nd Semester</div>
                        <div class="text-sm text-gray-500">Jan - May</div>
                    </button>
                    
                    <button type="button" 
                            onclick="fillTemplate('Summer', '2025-2026', '2026-06-01', '2026-07-31')"
                            class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-left">
                        <div class="font-medium text-gray-900">Summer</div>
                        <div class="text-sm text-gray-500">Jun - Jul</div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function fillTemplate(semester, academicYear, startDate, endDate) {
    document.getElementById('name').value = semester + ' Semester';
    document.getElementById('academic_year').value = academicYear;
    document.getElementById('start_date').value = startDate;
    document.getElementById('end_date').value = endDate;
    document.getElementById('description').value = `${semester} semester for academic year ${academicYear}`;
}
</script>
@endsection
