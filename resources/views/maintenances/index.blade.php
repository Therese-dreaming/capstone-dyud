@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-8" x-data="{ showToast: {{ session('success') || session('error') ? 'true' : 'false' }}, showModal: false, deleteMaintenanceId: null }">    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <i class="fas fa-tools text-red-800"></i>
                Maintenance Records
            </h1>
            <p class="text-gray-600 mt-1">Maintenance history for {{ $asset->name }} ({{ $asset->asset_code }})</p>
        </div>
        <a href="{{ route('maintenances.create', $asset) }}" class="bg-gradient-to-r from-red-800 to-red-900 hover:from-red-900 hover:to-red-950 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center gap-2 shadow-lg">
            <i class="fas fa-plus"></i> Add Maintenance Record
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <div class="bg-gray-50 p-4 border-b border-gray-200">
            <div class="flex items-center gap-4">
                <div class="text-sm text-gray-600 font-medium">
                    Total: <span class="text-red-800 font-bold">{{ $maintenances->total() }}</span> maintenance records
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 font-sans">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-tag mr-1"></i>Type
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-user mr-1"></i>Technician
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-info-circle mr-1"></i>Status
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-calendar mr-1"></i>Scheduled Date
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <i class="fas fa-cogs mr-1"></i>Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($maintenances as $maintenance)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                                    {{ $maintenance->type === 'Preventive' ? 'bg-orange-100 text-orange-800' : 
                                       ($maintenance->type === 'Corrective' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $maintenance->type }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                <div class="font-medium text-sm text-gray-900">{{ $maintenance->technician }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                                    {{ $maintenance->status === 'Completed' ? 'bg-green-100 text-green-800' : 
                                       ($maintenance->status === 'In Progress' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ $maintenance->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                <div class="text-sm text-gray-900 font-bold">{{ \Carbon\Carbon::parse($maintenance->scheduled_date)->format('F d, Y') }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('maintenances.show', [$asset, $maintenance]) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors duration-150"
                                       title="View Details">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('maintenances.edit', [$asset, $maintenance]) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 bg-yellow-100 text-yellow-600 rounded-full hover:bg-yellow-200 transition-colors duration-150"
                                       title="Edit Maintenance">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <button @click="showModal = true; deleteMaintenanceId = {{ $maintenance->id }}"
                                            class="inline-flex items-center justify-center w-8 h-8 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-150"
                                            title="Delete Maintenance">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-tools text-4xl mb-4"></i>
                                    <div class="text-lg font-medium text-gray-600">No maintenance records found</div>
                                    <div class="text-sm text-gray-500 mt-1">Get started by adding the first maintenance record</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $maintenances->links() }}
    </div>

    <!-- Scanner History Section -->
    <div class="mt-10 bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <div class="bg-gray-50 p-4 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <i class="fas fa-qrcode text-gray-600"></i>
                <h2 class="text-lg font-semibold text-gray-800">Scanner History</h2>
                <span class="ml-2 text-sm text-gray-600">Total: <span class="text-red-800 font-bold">{{ $scanHistory->total() }}</span></span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 font-sans">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">When</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">End Status</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">Scanned By</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Notes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($scanHistory as $record)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100 text-sm text-gray-900">{{ $record->scanned_at ? $record->scanned_at->format('M d, Y H:i') : '—' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100 text-sm">
                                <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full {{ $record->status_class }}">{{ $record->end_status ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100 text-sm text-gray-900">{{ $record->scanned_by ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $record->notes ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">No scanner history</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($scanHistory->hasPages())
            <div class="p-4">{{ $scanHistory->appends(['scan_page' => $scanHistory->currentPage()])->links() }}</div>
        @endif
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

    <!-- Delete Modal -->
    <div x-show="showModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" style="display: none;">
        <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-md relative">
            <button @click="showModal = false" class="absolute top-3 right-3 text-gray-400 hover:text-red-800 text-xl"><i class="fas fa-times"></i></button>
            <div class="flex flex-col items-center">
                <div class="bg-red-100 text-red-800 rounded-full p-4 mb-4">
                    <i class="fas fa-exclamation-triangle text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2 text-gray-800">Delete Maintenance Record</h3>
                <p class="text-gray-600 mb-6 text-center">Are you sure you want to delete this maintenance record? This action cannot be undone.</p>
                <form :action="'/assets/{{ $asset->id }}/maintenances/' + deleteMaintenanceId" method="POST" class="w-full flex flex-col items-center gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-800 hover:bg-red-900 text-white font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                    <button type="button" @click="showModal = false" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
