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

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <form method="POST" action="{{ route('maintenance-requests.store') }}" class="p-8">
            @csrf
            
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
                        <label class="block text-sm font-semibold text-gray-700" for="school_year">
                            School Year <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="school_year" id="school_year" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200" 
                               value="{{ old('school_year', '2024-2025') }}" 
                               placeholder="e.g., 2024-2025" required>
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
                                <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                    {{ $location->building }} - Floor {{ $location->floor }} - Room {{ $location->room }}
                                </option>
                            @endforeach
                        </select>
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
@endsection


