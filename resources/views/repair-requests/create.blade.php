@extends('layouts.user')

@section('title', 'Submit Repair Request')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-yellow-50 to-orange-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4 mb-4">
                <a href="{{ url()->previous() }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-wrench text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Submit Repair Request</h1>
                    <p class="text-gray-600">Report damage or malfunction that needs immediate attention</p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-6 py-4">
                <h2 class="text-xl font-bold text-white">Repair Request Form</h2>
                <p class="text-yellow-100 text-sm">Please provide detailed information about the issue</p>
            </div>

            <form action="{{ route('repair-requests.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <!-- Asset Information -->
                <div class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-box text-yellow-600 mr-2"></i>
                        Asset Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="asset_code" class="block text-sm font-medium text-gray-700 mb-2">
                                Asset Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="asset_code" 
                                   name="asset_code" 
                                   value="{{ old('asset_code', request('asset_code', $asset->asset_code ?? '')) }}" 
                                   {{ $asset ? 'readonly' : '' }}
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 {{ $asset ? 'bg-gray-100' : '' }} @error('asset_code') border-red-500 @enderror">
                            @error('asset_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        @if($asset)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Asset Name</label>
                            <div class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-900">
                                {{ $asset->name }}
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($asset && $asset->location)
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Location</label>
                        <div class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-900">
                            @if($asset->location)
                                {{ $asset->location->building }} - Floor {{ $asset->location->floor }} - Room {{ $asset->location->room }}
                            @else
                                <span class="text-gray-500 italic">Location not assigned</span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Request Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="semester_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Semester <span class="text-red-500">*</span>
                        </label>
                        <select id="semester_id" 
                                name="semester_id" 
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('semester_id') border-red-500 @enderror">
                            <option value="">Select Semester</option>
                            @if($currentSemester)
                                <option value="{{ $currentSemester->id }}" {{ old('semester_id', $currentSemester->id) == $currentSemester->id ? 'selected' : '' }}>
                                    {{ $currentSemester->academic_year }} - {{ $currentSemester->name }} (Current)
                                </option>
                            @endif
                        </select>
                        @error('semester_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="school_year" class="block text-sm font-medium text-gray-700 mb-2">
                            School Year <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="school_year" 
                               name="school_year" 
                               value="{{ old('school_year') }}" 
                               required
                               placeholder="e.g., 2023-2024"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('school_year') border-red-500 @enderror">
                        @error('school_year')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                            Department <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="department" 
                               name="department" 
                               value="{{ old('department') }}" 
                               required
                               placeholder="Enter department name"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('department') border-red-500 @enderror">
                        @error('department')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="program" class="block text-sm font-medium text-gray-700 mb-2">
                            Program
                        </label>
                        <input type="text" 
                               id="program" 
                               name="program" 
                               value="{{ old('program') }}" 
                               placeholder="Enter program name (optional)"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('program') border-red-500 @enderror">
                        @error('program')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="instructor_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Your Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="instructor_name" 
                               name="instructor_name" 
                               value="{{ old('instructor_name', auth()->user()->name) }}" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('instructor_name') border-red-500 @enderror">
                        @error('instructor_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date_reported" class="block text-sm font-medium text-gray-700 mb-2">
                            Date Reported <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               id="date_reported" 
                               name="date_reported" 
                               value="{{ old('date_reported', now()->format('Y-m-d')) }}" 
                               required
                               max="{{ now()->format('Y-m-d') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('date_reported') border-red-500 @enderror">
                        @error('date_reported')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Urgency Level -->
                <div>
                    <label for="urgency_level" class="block text-sm font-medium text-gray-700 mb-2">
                        Urgency Level <span class="text-red-500">*</span>
                    </label>
                    <select id="urgency_level" 
                            name="urgency_level" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('urgency_level') border-red-500 @enderror">
                        <option value="">Select Urgency Level</option>
                        <option value="low" {{ old('urgency_level') == 'low' ? 'selected' : '' }}>Low - Minor issue, not affecting usage</option>
                        <option value="medium" {{ old('urgency_level') == 'medium' ? 'selected' : '' }}>Medium - Affecting some functionality</option>
                        <option value="high" {{ old('urgency_level') == 'high' ? 'selected' : '' }}>High - Significantly affecting usage</option>
                        <option value="critical" {{ old('urgency_level') == 'critical' ? 'selected' : '' }}>Critical - Asset is unusable or dangerous</option>
                    </select>
                    @error('urgency_level')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Issue Description -->
                <div>
                    <label for="issue_description" class="block text-sm font-medium text-gray-700 mb-2">
                        Issue Description <span class="text-red-500">*</span>
                    </label>
                    <textarea id="issue_description" 
                              name="issue_description" 
                              rows="6" 
                              required
                              placeholder="Please describe the issue in detail: What is broken? When did it happen? What symptoms are you observing?"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('issue_description') border-red-500 @enderror">{{ old('issue_description') }}</textarea>
                    @error('issue_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Be as specific as possible to help us address the issue quickly
                    </p>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ url()->previous() }}" 
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 text-white font-semibold rounded-lg hover:from-yellow-600 hover:to-orange-600 transition-all shadow-md hover:shadow-lg">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Submit Repair Request
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Box -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-6">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 text-xl mt-1 mr-4"></i>
                <div>
                    <h4 class="font-semibold text-blue-900 mb-2">What happens next?</h4>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• Your repair request will be reviewed by administrators</li>
                        <li>• You'll receive a notification once your request is approved or if more information is needed</li>
                        <li>• GSU staff will be assigned to handle the repair</li>
                        <li>• You can track the status of your request in your dashboard</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
