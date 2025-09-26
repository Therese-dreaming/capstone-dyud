@extends('layouts.admin')

@section('title', 'Semester Management - Asset Management System')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-indigo-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 text-white p-4 md:p-6 mb-6 rounded-xl shadow-lg relative overflow-hidden mx-4 mt-4">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative z-10">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold flex items-center gap-3">
                        <i class="fas fa-calendar-alt text-2xl"></i>
                        Semester Management
                    </h1>
                    <p class="text-indigo-100 text-sm md:text-base mt-2">
                        Manage academic semesters and periods for asset tracking
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                    @if($currentSemester)
                        <div class="bg-white/20 rounded-lg px-4 py-2">
                            <div class="text-xs text-indigo-200">Current Semester</div>
                            <div class="text-sm font-medium">{{ $currentSemester->academic_year }} - {{ $currentSemester->name }}</div>
                        </div>
                    @else
                        <div class="bg-yellow-500/20 rounded-lg px-4 py-2">
                            <div class="text-xs text-yellow-200">No Current Semester Set</div>
                            <div class="text-sm font-medium">Please set a current semester</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <a href="{{ route('semesters.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Add New Semester
            </a>
            
            <a href="{{ route('semesters.set-current') }}" 
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-check-circle mr-2"></i>
                Set Current Semester
            </a>
            
            <a href="{{ route('semester-assets.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-chart-line mr-2"></i>
                View Semester Tracking
            </a>
        </div>

        <!-- Semesters Table -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">All Semesters</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Academic Year
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Semester
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Period
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Assets
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($semesters as $semester)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $semester->academic_year }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $semester->name }}</div>
                                    @if($semester->description)
                                        <div class="text-xs text-gray-500">{{ Str::limit($semester->description, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $semester->start_date->format('M d, Y') }} - {{ $semester->end_date->format('M d, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $semester->start_date->diffInDays($semester->end_date) + 1 }} days
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-1">
                                        @if($semester->is_current)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Current
                                            </span>
                                        @endif
                                        
                                        @if($semester->is_active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-play-circle mr-1"></i>
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <i class="fas fa-pause-circle mr-1"></i>
                                                Inactive
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium">{{ $semester->registeredAssets()->count() }} registered</span>
                                        <span class="text-xs text-gray-500">{{ $semester->assetChanges()->count() }} changes</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('semesters.show', $semester) }}" 
                                           class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <a href="{{ route('semesters.edit', $semester) }}" 
                                           class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        @if(!$semester->is_current)
                                            <form action="{{ route('semesters.set-current-action', $semester) }}" 
                                                  method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="text-green-600 hover:text-green-900"
                                                        title="Set as Current">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if(!$semester->registeredAssets()->exists() && !$semester->assetChanges()->exists() && !$semester->is_current)
                                            <form action="{{ route('semesters.destroy', $semester) }}" 
                                                  method="POST" 
                                                  class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this semester?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <i class="fas fa-calendar-times text-4xl mb-4"></i>
                                        <h3 class="text-lg font-medium mb-2">No Semesters Found</h3>
                                        <p class="text-sm mb-4">Get started by creating your first semester.</p>
                                        <a href="{{ route('semesters.create') }}" 
                                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                            <i class="fas fa-plus mr-2"></i>
                                            Add First Semester
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($semesters->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $semesters->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
