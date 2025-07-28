@extends('layouts.superadmin')

@section('content')
<div class="container mx-auto py-8" x-data="{ showToast: {{ session('success') || session('error') ? 'true' : 'false' }} }">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <i class="fas fa-history text-red-800"></i>
                Maintenance History
            </h1>
            <p class="text-gray-600 mt-1">View and filter all maintenance records across all assets</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <!-- Filter Section -->
        <div class="bg-gray-50 p-4 border-b border-gray-200">
            <form class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4" method="GET">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="type" id="type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-sm p-2" onchange="this.form.submit()">
                        <option value="all">All Types</option>
                        <option value="Preventive" {{ request('type') == 'Preventive' ? 'selected' : '' }}>Preventive</option>
                        <option value="Corrective" {{ request('type') == 'Corrective' ? 'selected' : '' }}>Corrective</option>
                        <option value="Emergency" {{ request('type') == 'Emergency' ? 'selected' : '' }}>Emergency</option>
                    </select>
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-sm p-2" onchange="this.form.submit()">
                        <option value="all">All Status</option>
                        <option value="Scheduled" {{ request('status') == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                
                <div>
                    <label for="scheduled_from" class="block text-sm font-medium text-gray-700 mb-1">Scheduled From</label>
                    <input type="date" name="scheduled_from" id="scheduled_from" value="{{ request('scheduled_from') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-sm p-2" onchange="this.form.submit()">
                </div>

                <div>
                    <label for="scheduled_to" class="block text-sm font-medium text-gray-700 mb-1">Scheduled To</label>
                    <input type="date" name="scheduled_to" id="scheduled_to" value="{{ request('scheduled_to') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-sm p-2" onchange="this.form.submit()">
                </div>
                
                <div>
                    <label for="completed_from" class="block text-sm font-medium text-gray-700 mb-1">Completed From</label>
                    <input type="date" name="completed_from" id="completed_from" value="{{ request('completed_from') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-sm p-2" onchange="this.form.submit()">
                </div>

                <div>
                    <label for="completed_to" class="block text-sm font-medium text-gray-700 mb-1">Completed To</label>
                    <input type="date" name="completed_to" id="completed_to" value="{{ request('completed_to') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-sm p-2" onchange="this.form.submit()">
                </div>
            </form>
        </div>

        <!-- Results Summary -->
        <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
            <div class="text-sm text-gray-600 font-medium">
                Total: <span class="text-red-800 font-bold">{{ $maintenances->total() }}</span> maintenance records
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 font-sans">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-tag mr-1"></i>Type
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-calendar mr-1"></i>Scheduled Date
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-calendar-check mr-1"></i>Completed Date
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-info-circle mr-1"></i>Status
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-cube mr-1"></i>Asset
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
                                <div class="text-sm text-gray-900 font-bold">{{ \Carbon\Carbon::parse($maintenance->scheduled_date)->format('F d, Y') }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                <div class="text-sm text-gray-900 font-bold">{{ $maintenance->completed_date ? \Carbon\Carbon::parse($maintenance->completed_date)->format('F d, Y') : 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                                    {{ $maintenance->status === 'Completed' ? 'bg-green-100 text-green-800' : 
                                       ($maintenance->status === 'In Progress' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ $maintenance->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                <div class="text-sm text-gray-900 font-medium">{{ $maintenance->asset->name }}</div>
                                <div class="text-xs text-gray-500">{{ $maintenance->asset->asset_code }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('maintenances.show', [$maintenance->asset, $maintenance]) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors duration-150"
                                       title="View Details">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-tools text-4xl mb-4"></i>
                                    <div class="text-lg font-medium text-gray-600">No maintenance records found</div>
                                    <div class="text-sm text-gray-500 mt-1">Please try adjusting your filters or add new records</div>
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
</div>
@endsection
