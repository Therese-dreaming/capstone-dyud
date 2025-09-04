@extends('layouts.gsu')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg p-10">
    <h2 class="text-3xl font-bold mb-8 text-gray-800 flex items-center gap-3">
        <i class="fas fa-eye text-red-800"></i> Maintenance Record Details
    </h2>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Asset Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-red-50 to-red-100 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-cube text-red-600"></i>
                    Asset Information
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Asset Name:</span>
                        <p class="text-base font-semibold text-gray-900">{{ $asset->name }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Asset Code:</span>
                        <p class="text-base font-semibold text-gray-900">{{ $asset->asset_code }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-tools text-green-600"></i>
                    Maintenance Details
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Type:</span>
                        <span class="inline-block px-3 py-1 text-sm font-medium rounded-full
                            {{ $maintenance->type === 'Preventive' ? 'bg-blue-100 text-blue-800' : 
                               ($maintenance->type === 'Corrective' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ $maintenance->type }}
                        </span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Status:</span>
                        <span class="inline-block px-3 py-1 text-sm font-medium rounded-full
                            {{ $maintenance->status === 'Completed' ? 'bg-green-100 text-green-800' : 
                               ($maintenance->status === 'In Progress' ? 'bg-yellow-100 text-yellow-800' : 
                               ($maintenance->status === 'Scheduled' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                            {{ $maintenance->status }}
                        </span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Technician:</span>
                        <p class="text-base font-semibold text-gray-900">{{ $maintenance->technician }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedule Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-calendar text-purple-600"></i>
                    Schedule Information
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Scheduled Date:</span>
                        <p class="text-base font-semibold text-gray-900">
                            {{ \Carbon\Carbon::parse($maintenance->scheduled_date)->format('F d, Y') }}
                        </p>
                    </div>
                    @if($maintenance->completed_date)
                    <div>
                        <span class="text-sm font-medium text-gray-500">Completed Date:</span>
                        <p class="text-base font-semibold text-gray-900">
                            {{ \Carbon\Carbon::parse($maintenance->completed_date)->format('F d, Y') }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Cost Information -->
        @if($maintenance->cost)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-dollar-sign text-yellow-600"></i>
                    Cost Information
                </h3>
            </div>
            <div class="p-6">
                <div>
                    <span class="text-sm font-medium text-gray-500">Total Cost:</span>
                    <p class="text-2xl font-bold text-green-600">₱{{ number_format($maintenance->cost, 2) }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Description -->
        @if($maintenance->description)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden lg:col-span-2">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-file-alt text-gray-600"></i>
                    Description
                </h3>
            </div>
            <div class="p-6">
                <p class="text-gray-700 leading-relaxed">{{ $maintenance->description }}</p>
            </div>
        </div>
        @endif

        <!-- Notes -->
        @if($maintenance->notes)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden lg:col-span-2">
            <div class="bg-gradient-to-r from-orange-50 to-orange-100 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-sticky-note text-orange-600"></i>
                    Notes
                </h3>
            </div>
            <div class="p-6">
                <p class="text-gray-700 leading-relaxed">{{ $maintenance->notes }}</p>
            </div>
        </div>
        @endif
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end mt-8 gap-4">
        <a href="{{ route('gsu.maintenances.index', $asset) }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
        <a href="{{ route('gsu.maintenances.edit', $maintenance) }}" class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700">
            <i class="fas fa-edit"></i> Edit
        </a>
    </div>
</div>
@endsection
