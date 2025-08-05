@extends('layouts.gsu')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <i class="fas fa-handshake text-red-800"></i>
                Borrowing Request Details
            </h1>
            <p class="text-gray-600 mt-1">View detailed information about this borrowing request</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('gsu.borrowings.index') }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200 flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details Card -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md border border-gray-200">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Borrowing Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Borrower Information -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-user text-red-600"></i>
                                Borrower Details
                            </h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Name</label>
                                    <p class="text-gray-900 font-medium">{{ $borrowing->borrower_name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">ID Number</label>
                                    <p class="text-gray-900 font-mono">{{ $borrowing->borrower_id_number }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Email</label>
                                    <p class="text-gray-900">{{ $borrowing->borrower_email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Contact Number</label>
                                    <p class="text-gray-900">{{ $borrowing->borrower_contact_number }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Request Information -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-calendar-alt text-red-600"></i>
                                Request Details
                            </h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Request Date</label>
                                    <p class="text-gray-900">{{ $borrowing->request_date->format('F d, Y') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Due Date</label>
                                    <p class="text-gray-900">{{ $borrowing->due_date->format('F d, Y') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Status</label>
                                    <span class="px-3 py-1 inline-flex text-sm leading-4 font-semibold rounded-full {{ $borrowing->getStatusBadgeClass() }}">
                                        {{ ucfirst($borrowing->status) }}
                                    </span>
                                </div>
                                @if($borrowing->return_date)
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Return Date</label>
                                    <p class="text-gray-900">{{ $borrowing->return_date->format('F d, Y') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Purpose and Notes -->
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-sticky-note text-red-600"></i>
                            Purpose & Notes
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Purpose</label>
                                <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $borrowing->purpose }}</p>
                            </div>
                            @if($borrowing->notes)
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Notes</label>
                                <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $borrowing->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Asset Information Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md border border-gray-200">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Asset Information</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Asset Name</label>
                            <p class="text-gray-900 font-medium">{{ $borrowing->asset->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Asset Code</label>
                            <p class="text-gray-900 font-mono">{{ $borrowing->asset->asset_code }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Category</label>
                            <p class="text-gray-900">{{ $borrowing->asset->category->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Condition</label>
                            <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                                {{ $borrowing->asset->condition === 'good' ? 'bg-green-100 text-green-800' : 
                                   ($borrowing->asset->condition === 'fair' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($borrowing->asset->condition) }}
                            </span>
                        </div>
                        @if($borrowing->asset->location)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Original Location</label>
                            <p class="text-gray-900">
                                {{ $borrowing->asset->location->building }} - Floor {{ $borrowing->asset->location->floor }} - Room {{ $borrowing->asset->location->room }}
                            </p>
                        </div>
                        @endif
                        @if($borrowing->location)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Borrowed To</label>
                            <p class="text-gray-900">
                                {{ $borrowing->location->building }} - Floor {{ $borrowing->location->floor }} - Room {{ $borrowing->location->room }}
                            </p>
                        </div>
                        @elseif($borrowing->custom_location)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Borrowed To</label>
                            <p class="text-gray-900">{{ $borrowing->custom_location }}</p>
                            <span class="text-xs text-gray-500">(Custom Location)</span>
                        </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        @if($borrowing->status === 'approved')
                            <button onclick="openReturnModal({{ $borrowing->id }}, '{{ addslashes($borrowing->asset->asset_code) }}')"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                                <i class="fas fa-undo"></i>
                                Mark as Returned
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Return Modal -->
<div id="returnModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" style="display: none;">
    <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-md relative">
        <button onclick="closeReturnModal()" class="absolute top-3 right-3 text-gray-400 hover:text-blue-800 text-xl">
            <i class="fas fa-times"></i>
        </button>
        <div class="flex flex-col items-center">
            <div class="bg-blue-100 text-blue-800 rounded-full p-4 mb-4">
                <i class="fas fa-undo text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold mb-2 text-gray-800">Mark Asset as Returned</h3>
            <p class="text-gray-600 mb-6 text-center">Are you sure you want to mark the asset <span id="returnAssetCode" class="font-semibold text-blue-800"></span> as returned? The asset will be restored to its original location.</p>
            <form id="returnForm" method="POST" class="w-full flex flex-col items-center gap-3">
                @csrf
                @method('PUT')
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-undo"></i> Mark as Returned
                </button>
                <button type="button" onclick="closeReturnModal()" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </form>
        </div>
    </div>
</div>

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

<script>
    function openReturnModal(borrowingId, assetCode) {
        document.getElementById('returnAssetCode').textContent = assetCode;
        document.getElementById('returnForm').action = '/gsu/borrowings/' + borrowingId + '/return';
        document.getElementById('returnModal').style.display = 'flex';
    }

    function closeReturnModal() {
        document.getElementById('returnModal').style.display = 'none';
    }

    window.onclick = function(event) {
        const returnModal = document.getElementById('returnModal');
        if (event.target === returnModal) {
            closeReturnModal();
        }
    }
</script>
@endsection 