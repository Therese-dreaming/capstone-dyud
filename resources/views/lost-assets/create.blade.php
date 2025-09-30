@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-8">
    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
        <div class="flex items-start gap-2">
            <i class="fas fa-check-circle mt-0.5"></i>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
        <div class="flex items-start gap-2">
            <i class="fas fa-times-circle mt-0.5"></i>
            <span class="font-semibold">{{ session('error') }}</span>
        </div>
    </div>
    @endif
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                    <i class="fas fa-search text-red-800"></i>
                    Report Asset as Lost
                </h1>
                <p class="text-gray-600 mt-1">Report missing asset with detailed information</p>
            </div>
            <a href="{{ route(request()->routeIs('gsu.*') ? 'gsu.locations.index' : 'locations.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Back to Assets
            </a>
        </div>

        <!-- Asset Information -->
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Asset Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Asset Name</label>
                    <p class="mt-1 text-sm text-gray-900 font-medium">{{ $asset->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Asset Code</label>
                    <p class="mt-1 text-sm text-gray-900 font-mono">{{ $asset->asset_code }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Category</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $asset->category->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Current Location</label>
                    <p class="mt-1 text-sm text-gray-900">
                        {{ $asset->location->building }} - Floor {{ $asset->location->floor }} - Room {{ $asset->location->room }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Condition</label>
                    <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                        {{ $asset->condition === 'Good' ? 'bg-green-100 text-green-800' : 
                           ($asset->condition === 'Fair' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }} mt-1">
                        {{ $asset->condition }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                        {{ $asset->status === 'Available' ? 'bg-green-100 text-green-800' : 
                           ($asset->status === 'In Use' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }} mt-1">
                        {{ $asset->status }}
                    </span>
                </div>
            </div>
        </div>

        @if($lastBorrowing)
        <!-- Last Borrower Information -->
        <div class="bg-blue-50 rounded-lg p-6 border border-blue-200 mb-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-4 flex items-center gap-2">
                <i class="fas fa-user-clock text-blue-600"></i>
                Last Borrower Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-blue-700">Borrower Name</label>
                    <p class="mt-1 text-sm text-blue-900 font-medium">{{ $lastBorrowing->borrower_name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-blue-700">Borrower ID</label>
                    <p class="mt-1 text-sm text-blue-900">{{ $lastBorrowing->borrower_id_number }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-blue-700">Borrowing Period</label>
                    <p class="mt-1 text-sm text-blue-900">
                        {{ $lastBorrowing->request_date->format('M d, Y') }} - {{ $lastBorrowing->due_date->format('M d, Y') }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-blue-700">Borrowing Status</label>
                    <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full {{ $lastBorrowing->getStatusBadgeClass() }} mt-1">
                        {{ ucfirst($lastBorrowing->status) }}
                    </span>
                </div>
                @if($lastBorrowing->location)
                <div>
                    <label class="block text-sm font-medium text-blue-700">Borrowed To Location</label>
                    <p class="mt-1 text-sm text-blue-900">
                        {{ $lastBorrowing->location->building }} - Floor {{ $lastBorrowing->location->floor }} - Room {{ $lastBorrowing->location->room }}
                    </p>
                </div>
                @elseif($lastBorrowing->custom_location)
                <div>
                    <label class="block text-sm font-medium text-blue-700">Borrowed To Location</label>
                    <p class="mt-1 text-sm text-blue-900">
                        {{ $lastBorrowing->custom_location }} <span class="text-gray-500">(Custom)</span>
                    </p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Lost Asset Report Form -->
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Lost Asset Report</h2>
            
            <form action="{{ route('lost-assets.store', $asset) }}" method="POST">
                @csrf
                
                <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Note:</strong> The asset will be marked as lost and the last known location will be automatically recorded as: 
                        <strong>{{ $asset->location->building ?? 'Unknown' }} - Floor {{ $asset->location->floor ?? 'N/A' }} - Room {{ $asset->location->room ?? 'N/A' }}</strong>
                    </p>
                </div>

                <div class="mt-6">
                    <label for="investigation_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-search mr-1"></i>Investigation Notes (Optional)
                    </label>
                    <textarea name="investigation_notes" id="investigation_notes" rows="5" 
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"
                              placeholder="Add any notes about the circumstances of the loss, when it was discovered missing, potential leads, or areas to search...">{{ old('investigation_notes') }}</textarea>
                    @error('investigation_notes')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route(request()->routeIs('gsu.*') ? 'gsu.locations.index' : 'locations.index') }}" 
                       class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center gap-2">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle"></i> Report as Lost
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 