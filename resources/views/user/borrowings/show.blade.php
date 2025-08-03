@extends('layouts.user')

@section('content')
<div class="container mx-auto py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                    <i class="fas fa-handshake text-red-800"></i>
                    Borrowing Request Details
                </h1>
                <p class="text-gray-600 mt-1">View details about your borrowing request</p>
            </div>
            <a href="{{ route('user.borrowings.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors text-sm font-medium flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Back to My Requests
            </a>
        </div>

        <!-- Status Banner -->
        <div class="bg-gradient-to-r from-red-50 to-red-100 border border-red-200 rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center shadow-sm">
                            <i class="fas fa-file-alt text-red-600 text-xl"></i>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Request #{{ $borrowing->id }}</h2>
                        <p class="text-gray-600">Submitted on {{ $borrowing->request_date->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full {{ $borrowing->getStatusBadgeClass() }} shadow-sm">
                        {{ ucfirst($borrowing->status) }}
                    </span>
                    @if($borrowing->isOverdue())
                        <div class="mt-2 text-sm text-red-600 font-medium">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Overdue by {{ now()->diffInDays($borrowing->due_date) }} days
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Asset Information Card -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-box text-blue-600"></i>
                        Asset Information
                    </h2>
                </div>
                <div class="p-6">
                    <div class="flex items-start space-x-4 mb-6">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg">
                                <i class="fas fa-box text-white text-xl"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $borrowing->asset->name }}</h3>
                            <p class="text-sm text-gray-500 font-mono mb-2">{{ $borrowing->asset->asset_code }}</p>
                            <div class="flex items-center space-x-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $borrowing->asset->category->name }}
                                </span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $borrowing->asset->status === 'Available' ? 'bg-green-100 text-green-800' : 
                                       ($borrowing->asset->status === 'In Use' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $borrowing->asset->status }}
                                </span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $borrowing->asset->condition === 'Good' ? 'bg-green-100 text-green-800' : 
                                       ($borrowing->asset->condition === 'Fair' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $borrowing->asset->condition }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-map-marker-alt text-gray-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Location</p>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $borrowing->asset->location->building }} - Floor {{ $borrowing->asset->location->floor }} - Room {{ $borrowing->asset->location->room }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        @if($borrowing->asset->description)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-info text-gray-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Description</p>
                                    <p class="text-sm text-gray-900">{{ $borrowing->asset->description }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4">
                        <button onclick="openAssetDetailsModal({{ $borrowing->asset->id }})" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            <i class="fas fa-eye mr-2"></i>
                            View Full Asset Details
                        </button>
                    </div>
                </div>
            </div>

            <!-- Borrowing Details Card -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-green-600"></i>
                        Borrowing Details
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-calendar-plus text-green-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Request Date</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $borrowing->request_date->format('M d, Y') }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-calendar-check text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Due Date</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $borrowing->due_date->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            @if($borrowing->return_date)
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                    <i class="fas fa-undo text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Return Date</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $borrowing->return_date->format('M d, Y') }}</p>
                                </div>
                            </div>
                            @endif
                            
                            @if($borrowing->approved_at)
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                    <i class="fas fa-clock text-yellow-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Approved At</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $borrowing->approved_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    @if($borrowing->location)
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 rounded-full bg-blue-200 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-blue-700 uppercase tracking-wide mb-1">Usage Location</p>
                                <p class="text-sm text-blue-900 font-medium">
                                    {{ $borrowing->location->building }} - Floor {{ $borrowing->location->floor }} - Room {{ $borrowing->location->room }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($borrowing->purpose)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-bullseye text-gray-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Purpose of Borrowing</p>
                                <p class="text-sm text-gray-900">{{ $borrowing->purpose }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($borrowing->notes)
                    <div class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 rounded-full bg-yellow-200 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-sticky-note text-yellow-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-yellow-700 uppercase tracking-wide mb-1">Admin Notes</p>
                                <p class="text-sm text-yellow-800">{{ $borrowing->notes }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Borrower Information -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-user text-purple-600"></i>
                        Your Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-lg">
                                <i class="fas fa-user text-white text-xl"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-lg font-semibold text-gray-900">{{ $borrowing->borrower_name }}</h4>
                            <p class="text-sm text-gray-500 font-mono">{{ $borrowing->borrower_id_number }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Timeline -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-history text-indigo-600"></i>
                        Request Timeline
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-paper-plane text-green-600 text-xs"></i>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-900">Request Submitted</p>
                                <p class="text-xs text-gray-500">{{ $borrowing->request_date->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        
                        @if($borrowing->approved_at)
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-blue-600 text-xs"></i>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-900">Request Approved</p>
                                <p class="text-xs text-gray-500">{{ $borrowing->approved_at->format('M d, Y H:i') }}</p>
                                @if($borrowing->approvedBy)
                                <p class="text-xs text-gray-400">by {{ $borrowing->approvedBy->name }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        @if($borrowing->return_date)
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-undo text-purple-600 text-xs"></i>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-900">Asset Returned</p>
                                <p class="text-xs text-gray-500">{{ $borrowing->return_date->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-red-50 to-red-100 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-cogs text-red-600"></i>
                        Actions
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @if($borrowing->status === 'pending')
                            <button onclick="openCancelRequestModal({{ $borrowing->id }})" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2 shadow-lg">
                                <i class="fas fa-times"></i> Cancel Request
                            </button>
                        @endif
                        
                        @if($borrowing->status === 'approved')
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle text-green-400 text-xl"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-green-800">Request Approved</h3>
                                        <div class="mt-2 text-sm text-green-700">
                                            <p>Your borrowing request has been approved. You can now collect the asset.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        @if($borrowing->status === 'rejected')
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-times-circle text-red-400 text-xl"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">Request Rejected</h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <p>Your borrowing request has been rejected. Please check the notes for details.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Overdue Warning -->
            @if($borrowing->isOverdue())
            <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-400 text-2xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Overdue Item</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>This item is overdue by <strong>{{ now()->diffInDays($borrowing->due_date) }} days</strong>. Please return it as soon as possible to avoid any penalties.</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Include Modals -->
@include('components.borrowing-modals')

<!-- Toast Messages -->
@if(session('success'))
    <div class="fixed top-6 right-6 z-50 bg-green-900 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-4 animate-fade-in min-w-[300px] border border-green-700"
         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
        <i class="fas fa-check-circle text-2xl text-green-300"></i>
        <span class="font-semibold">{{ session('success') }}</span>
        <button @click="show = false" class="ml-auto text-green-200 hover:text-white"><i class="fas fa-times"></i></button>
    </div>
@endif
@if(session('error'))
    <div class="fixed top-6 right-6 z-50 bg-red-900 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-4 animate-fade-in min-w-[300px] border border-red-700"
         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
        <i class="fas fa-times-circle text-2xl text-red-300"></i>
        <span class="font-semibold">{{ session('error') }}</span>
        <button @click="show = false" class="ml-auto text-red-200 hover:text-white"><i class="fas fa-times"></i></button>
    </div>
@endif

<style>
@keyframes fade-in { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: none; } }
.animate-fade-in { animation: fade-in 0.5s; }
</style>
@endsection 