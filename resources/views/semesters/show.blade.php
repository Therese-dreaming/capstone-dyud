@extends('layouts.admin')

@section('title', 'Semester Details - Asset Management System')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-indigo-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 text-white p-4 md:p-6 mb-6 rounded-xl shadow-lg relative overflow-hidden mx-4 mt-4">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold flex items-center gap-3">
                        <i class="fas fa-calendar-alt text-2xl"></i>
                        {{ $semester->academic_year }} - {{ $semester->name }}
                    </h1>
                    <p class="text-indigo-100 text-sm md:text-base mt-2">
                        {{ $semester->start_date->format('M d, Y') }} - {{ $semester->end_date->format('M d, Y') }}
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('semesters.edit', $semester) }}" 
                       class="inline-flex items-center px-4 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit
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
        <!-- Status Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Current Status -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 {{ $semester->is_current ? 'bg-green-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-check-circle text-xl {{ $semester->is_current ? 'text-green-600' : 'text-gray-400' }}"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Status</div>
                        <div class="text-lg font-semibold {{ $semester->is_current ? 'text-green-600' : 'text-gray-600' }}">
                            {{ $semester->is_current ? 'Current Semester' : 'Not Current' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Status -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 {{ $semester->is_active ? 'bg-blue-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-play-circle text-xl {{ $semester->is_active ? 'text-blue-600' : 'text-gray-400' }}"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Activity</div>
                        <div class="text-lg font-semibold {{ $semester->is_active ? 'text-blue-600' : 'text-gray-600' }}">
                            {{ $semester->is_active ? 'Active' : 'Inactive' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Duration -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-clock text-xl text-purple-600"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Duration</div>
                        <div class="text-lg font-semibold text-purple-600">
                            {{ $semester->start_date->diffInDays($semester->end_date) + 1 }} days
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Semester Information -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Semester Information</h3>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="text-sm text-gray-500 mb-1">Academic Year</div>
                        <div class="text-lg font-medium text-gray-900">{{ $semester->academic_year }}</div>
                    </div>
                    
                    <div>
                        <div class="text-sm text-gray-500 mb-1">Semester Name</div>
                        <div class="text-lg font-medium text-gray-900">{{ $semester->name }}</div>
                    </div>
                    
                    <div>
                        <div class="text-sm text-gray-500 mb-1">Start Date</div>
                        <div class="text-lg font-medium text-gray-900">{{ $semester->start_date->format('F d, Y') }}</div>
                        <div class="text-sm text-gray-500">{{ $semester->start_date->format('l') }}</div>
                    </div>
                    
                    <div>
                        <div class="text-sm text-gray-500 mb-1">End Date</div>
                        <div class="text-lg font-medium text-gray-900">{{ $semester->end_date->format('F d, Y') }}</div>
                        <div class="text-sm text-gray-500">{{ $semester->end_date->format('l') }}</div>
                    </div>
                </div>
                
                @if($semester->description)
                    <div class="mt-6">
                        <div class="text-sm text-gray-500 mb-2">Description</div>
                        <div class="text-gray-900 bg-gray-50 p-4 rounded-lg">{{ $semester->description }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Asset Statistics -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Asset Activity Statistics</h3>
                <p class="text-sm text-gray-600">Asset activities that occurred during this semester</p>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-plus-circle text-2xl text-green-600"></i>
                        </div>
                        <div class="text-2xl font-bold text-green-600">{{ $stats['registered_assets'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Assets Registered</div>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-exchange-alt text-2xl text-blue-600"></i>
                        </div>
                        <div class="text-2xl font-bold text-blue-600">{{ $stats['transferred_assets'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Assets Transferred</div>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-trash-alt text-2xl text-orange-600"></i>
                        </div>
                        <div class="text-2xl font-bold text-orange-600">{{ $stats['disposed_assets'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Assets Disposed</div>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-search text-2xl text-red-600"></i>
                        </div>
                        <div class="text-2xl font-bold text-red-600">{{ $stats['lost_assets'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Assets Lost</div>
                    </div>
                </div>
                
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-lg font-semibold text-gray-900">Total Activity</div>
                            <div class="text-sm text-gray-500">All asset activities during this semester</div>
                        </div>
                        <div class="text-3xl font-bold text-indigo-600">{{ $stats['total_activity'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Actions</h3>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @if(!$semester->is_current && $semester->is_active)
                        <form action="{{ route('semesters.set-current-action', $semester) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                Set as Current
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('semester-assets.index', ['semester_id' => $semester->id]) }}" 
                       class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-center inline-flex items-center justify-center">
                        <i class="fas fa-chart-line mr-2"></i>
                        View Asset Tracking
                    </a>
                    
                    <a href="{{ route('semesters.edit', $semester) }}" 
                       class="w-full px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-center inline-flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Semester
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
