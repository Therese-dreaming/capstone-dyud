@extends('layouts.admin')

@section('title', 'Set Current Semester - Asset Management System')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-green-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-green-600 to-green-800 text-white p-4 md:p-6 mb-6 rounded-xl shadow-lg relative overflow-hidden mx-4 mt-4">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold flex items-center gap-3">
                        <i class="fas fa-check-circle text-2xl"></i>
                        Set Current Semester
                    </h1>
                    <p class="text-green-100 text-sm md:text-base mt-2">
                        Choose which semester is currently active for asset registration
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
        <!-- Current Status Card -->
        @if($currentSemester)
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Currently Active Semester</h3>
                            <p class="text-sm text-gray-600">Assets are being registered to this semester</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <div class="text-sm text-gray-500">Academic Year</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $currentSemester->academic_year }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Semester</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $currentSemester->name }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Period</div>
                            <div class="text-lg font-semibold text-gray-900">
                                {{ $currentSemester->start_date->format('M d') }} - {{ $currentSemester->end_date->format('M d, Y') }}
                            </div>
                        </div>
                    </div>
                    
                    @if($currentSemester->description)
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <div class="text-sm text-gray-500">Description</div>
                            <div class="text-gray-900">{{ $currentSemester->description }}</div>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-yellow-50">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">No Current Semester Set</h3>
                            <p class="text-sm text-gray-600">Please select a semester to activate for asset registration</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Set Current Semester Form -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Select New Current Semester</h3>
                <p class="text-sm text-gray-600">Choose from available active semesters</p>
            </div>
            
            <div class="p-6">
                @if($semesters->count() > 0)
                    <form action="{{ route('semesters.set-current-action', '') }}" method="POST">
                        @csrf
                        
                        <div class="space-y-4 mb-6">
                            @foreach($semesters as $semester)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" 
                                               name="semester_id" 
                                               value="{{ $semester->id }}"
                                               {{ $currentSemester && $currentSemester->id == $semester->id ? 'checked' : '' }}
                                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                        
                                        <div class="ml-4 flex-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="text-lg font-semibold text-gray-900">
                                                        {{ $semester->academic_year }} - {{ $semester->name }}
                                                    </div>
                                                    <div class="text-sm text-gray-600">
                                                        {{ $semester->start_date->format('M d, Y') }} - {{ $semester->end_date->format('M d, Y') }}
                                                        ({{ $semester->start_date->diffInDays($semester->end_date) + 1 }} days)
                                                    </div>
                                                    @if($semester->description)
                                                        <div class="text-sm text-gray-500 mt-1">{{ $semester->description }}</div>
                                                    @endif
                                                </div>
                                                
                                                <div class="flex flex-col items-end gap-1">
                                                    @if($semester->is_current)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <i class="fas fa-check-circle mr-1"></i>
                                                            Current
                                                        </span>
                                                    @endif
                                                    
                                                    @if($semester->isWithinDateRange())
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            <i class="fas fa-calendar-check mr-1"></i>
                                                            Active Period
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                Only active semesters are shown. New assets will be registered to the selected semester.
                            </div>
                            
                            <button type="submit" 
                                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-check-circle mr-2"></i>
                                Set as Current
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-calendar-times text-2xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Active Semesters Available</h3>
                        <p class="text-sm text-gray-500 mb-4">You need to create and activate at least one semester before setting it as current.</p>
                        <a href="{{ route('semesters.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Create First Semester
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Auto-Detection Card -->
        <div class="mt-6 bg-white rounded-xl shadow-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Auto-Detection</h3>
                <p class="text-sm text-gray-600">Let the system automatically detect the current semester based on today's date</p>
            </div>
            
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-900 font-medium">Automatic Detection</div>
                        <div class="text-sm text-gray-500">
                            The system will find the semester that contains today's date ({{ now()->format('M d, Y') }}) and set it as current.
                        </div>
                    </div>
                    
                    <a href="{{ route('semesters.index') }}?auto_detect=1" 
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-magic mr-2"></i>
                        Auto-Detect
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
