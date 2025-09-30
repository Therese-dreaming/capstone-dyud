@extends('layouts.admin')

@section('title', 'User Location Assignments')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-4 md:py-8">
    <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-6 md:mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 md:w-16 md:h-16 bg-gradient-to-r from-purple-600 to-purple-700 rounded-full mb-3 md:mb-4">
                <i class="fas fa-users text-white text-lg md:text-2xl"></i>
            </div>
            <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-2">User Location Assignments</h1>
            <p class="text-sm md:text-base lg:text-lg text-gray-600 px-4">Manage which users can access specific locations</p>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-4 md:mb-6 bg-green-50 border border-green-200 text-green-800 px-3 md:px-4 py-2 md:py-3 rounded-lg animate-fade-in text-sm md:text-base">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2 text-sm md:text-base"></i>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 md:mb-6 bg-red-50 border border-red-200 text-red-800 px-3 md:px-4 py-2 md:py-3 rounded-lg text-sm md:text-base">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle mr-2 mt-0.5 text-sm md:text-base"></i>
                    <div class="flex-1">
                        <div class="font-medium">Please fix the following errors:</div>
                        <ul class="text-xs md:text-sm mt-1 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Add Assignment Form -->
        <div class="bg-white rounded-xl md:rounded-2xl shadow-xl overflow-hidden mb-6 md:mb-8">
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-4 md:px-6 py-3 md:py-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-3 md:mr-4 flex-shrink-0">
                        <i class="fas fa-plus text-white text-base md:text-xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-base md:text-lg lg:text-xl font-bold text-white">Assign User to Location</h2>
                        <p class="text-green-100 text-xs md:text-sm">Grant location access to users</p>
                    </div>
                </div>
            </div>
            <div class="p-4 md:p-6">
                <form method="POST" action="{{ route('admin.user-locations.store') }}" class="space-y-4 md:space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">User</label>
                            <select name="user_id" id="user_id" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">Select a user...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->id_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="location_id" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                            <select name="location_id" id="location_id" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">Select a location...</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                        {{ $location->building }} - Floor {{ $location->floor }} - Room {{ $location->room }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                        <textarea name="notes" id="notes" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                  placeholder="Any additional notes about this assignment...">{{ old('notes') }}</textarea>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" 
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 md:px-6 py-2 md:py-3 border border-transparent text-sm md:text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                            <i class="fas fa-plus mr-2 text-sm md:text-base"></i> Assign Location
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Assignments Table -->
        <div class="bg-white rounded-xl md:rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-4 md:px-6 py-3 md:py-4">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center flex-1 min-w-0">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-3 md:mr-4 flex-shrink-0">
                            <i class="fas fa-list text-white text-base md:text-xl"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h2 class="text-base md:text-lg lg:text-xl font-bold text-white">Current Assignments</h2>
                            <p class="text-purple-100 text-xs md:text-sm hidden sm:block">Active user-location assignments</p>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <div class="text-lg md:text-2xl font-bold text-white">{{ $assignments->total() }}</div>
                        <div class="text-purple-100 text-xs md:text-sm">Total</div>
                    </div>
                </div>
            </div>

            @if($assignments->count() > 0)
            <!-- Mobile Card View -->
            <div class="block md:hidden p-3 space-y-3">
                @foreach($assignments as $assignment)
                    <div class="bg-white rounded-xl p-4 border-2 border-gray-200 shadow-sm">
                        <!-- User Info -->
                        <div class="flex items-center mb-3 pb-3 border-b-2 border-gray-100">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-user text-purple-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-bold text-gray-900 truncate">{{ $assignment->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $assignment->user->id_number }}</div>
                            </div>
                        </div>
                        
                        <!-- Details -->
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-500">Location:</span>
                                <div class="text-right">
                                    <div class="font-medium text-gray-900">{{ $assignment->location->building }}</div>
                                    <div class="text-gray-500">Floor {{ $assignment->location->floor }} - Room {{ $assignment->location->room }}</div>
                                </div>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-500">Assets:</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $assignment->location->assets->count() }} assets
                                </span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-500">Assigned By:</span>
                                <span class="font-medium text-gray-900">{{ $assignment->assignedBy->name ?? 'System' }}</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-500">Date:</span>
                                <div class="text-right">
                                    <div class="font-medium text-gray-900">{{ $assignment->assigned_at->format('M j, Y') }}</div>
                                    <div class="text-gray-500">{{ $assignment->assigned_at->format('g:i A') }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Button -->
                        <div class="mt-3 pt-3 border-t-2 border-gray-100">
                            <form method="POST" action="{{ route('admin.user-locations.destroy', $assignment) }}" 
                                  onsubmit="return confirm('Are you sure you want to remove this assignment?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-full inline-flex items-center justify-center px-3 py-2 border border-red-300 text-xs font-medium rounded-lg text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                    <i class="fas fa-trash mr-1.5"></i> Remove Assignment
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assets</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($assignments as $assignment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-purple-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $assignment->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $assignment->user->id_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $assignment->location->building }}</div>
                                <div class="text-sm text-gray-500">Floor {{ $assignment->location->floor }} - Room {{ $assignment->location->room }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $assignment->location->assets->count() }} assets
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $assignment->assignedBy->name ?? 'System' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $assignment->assigned_at->format('M j, Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $assignment->assigned_at->format('g:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <form method="POST" action="{{ route('admin.user-locations.destroy', $assignment) }}" 
                                      onsubmit="return confirm('Are you sure you want to remove this assignment?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center px-3 py-1 border border-red-300 text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                        <i class="fas fa-trash mr-1"></i> Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-gray-50 px-4 md:px-6 py-3 border-t border-gray-200">
                {{ $assignments->links() }}
            </div>
            @else
            <div class="p-8 md:p-12 text-center">
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 md:w-20 md:h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-users text-3xl md:text-4xl text-gray-300"></i>
                    </div>
                    <h3 class="text-base md:text-lg font-medium text-gray-900 mb-2">No assignments found</h3>
                    <p class="text-sm md:text-base text-gray-500 px-4">Start by assigning users to locations using the form above.</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
