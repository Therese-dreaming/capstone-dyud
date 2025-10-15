@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-8" x-data="{ showFilters: false }">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <i class="fas fa-trash-restore text-red-800"></i>
                Disposal History
            </h1>
            <p class="text-gray-600 mt-1">Track all disposed assets and disposal records</p>
        </div>
        <div class="flex gap-4">
            <button @click="showFilters = !showFilters" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                <i class="fas fa-filter"></i> 
                <span x-text="showFilters ? 'Hide Filters' : 'Show Filters'"></span>
            </button>
            <a href="{{ route(request()->routeIs('gsu.*') ? 'gsu.disposals.export' : 'disposals.export', request()->query()) }}" 
               class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                <i class="fas fa-file-excel"></i> Export to Excel
            </a>
        </div>
    </div>

    <!-- Filters Panel -->
    <div x-show="showFilters" x-transition class="bg-white rounded-lg shadow-md p-6 mb-6 border border-gray-200">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-filter text-blue-600"></i>
            Filters
        </h3>
        
        <form method="GET" action="{{ route(request()->routeIs('gsu.*') ? 'gsu.disposals.history' : 'disposals.history') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Asset Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Asset Search</label>
                    <input type="text" name="asset_search" value="{{ request('asset_search') }}" 
                           placeholder="Asset name or code..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Disposal Date From -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Disposal Date From</label>
                    <input type="date" name="disposal_date_from" value="{{ request('disposal_date_from') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Disposal Date To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Disposal Date To</label>
                    <input type="date" name="disposal_date_to" value="{{ request('disposal_date_to') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Disposed By -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Disposed By</label>
                    <input type="text" name="disposed_by" value="{{ request('disposed_by') }}" 
                           placeholder="Person who disposed..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200 flex items-center gap-2">
                    <i class="fas fa-search"></i> Apply Filters
                </button>
                <a href="{{ route(request()->routeIs('gsu.*') ? 'gsu.disposals.history' : 'disposals.history') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-6 rounded-lg transition duration-200 flex items-center gap-2">
                    <i class="fas fa-times"></i> Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Disposal History Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <div class="bg-gray-50 p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800">Disposal Records</h3>
                <div class="text-sm text-gray-600 font-medium">
                    Total: <span class="text-red-800 font-bold">{{ $disposals->total() }}</span> disposal records
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 font-sans">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-hash mr-1"></i>#
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-barcode mr-1"></i>Asset Code
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-tag mr-1"></i>Asset Name
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-folder mr-1"></i>Category
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-map-marker-alt mr-1"></i>Location
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-calendar mr-1"></i>Disposal Date
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <i class="fas fa-user mr-1"></i>Disposed By
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <i class="fas fa-comment mr-1"></i>Reason
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($disposals as $index => $disposal)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100 text-sm text-gray-600">
                                {{ $disposals->firstItem() + $index }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                <span class="font-mono text-sm font-bold text-gray-900 bg-gray-100 px-2 py-1 rounded">
                                    {{ $disposal->asset->asset_code }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                <div class="font-medium text-sm text-gray-900">{{ $disposal->asset->name }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                <span class="text-sm text-gray-700 bg-blue-50 px-2 py-1 rounded-full">
                                    {{ $disposal->asset->category->name }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                <div class="text-xs text-gray-600">
                                    <div class="font-medium">{{ $disposal->asset->location->building }}</div>
                                    <div class="text-gray-500">Floor {{ $disposal->asset->location->floor }} • Room {{ $disposal->asset->location->room }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                <div class="text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($disposal->disposal_date)->format('M d, Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($disposal->disposal_date)->diffForHumans() }}
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                <div class="text-sm font-medium text-gray-900">{{ $disposal->disposed_by }}</div>
                            </td>
                            <td class="px-4 py-3 border-r border-gray-100">
                                <div class="text-sm text-gray-900 max-w-xs">
                                    {{ Str::limit($disposal->disposal_reason, 100) }}
                                </div>
                                @if(strlen($disposal->disposal_reason) > 100)
                                    <button class="text-xs text-blue-600 hover:text-blue-800 mt-1" 
                                            onclick="alert('{{ addslashes($disposal->disposal_reason) }}')">
                                        Read more...
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-4"></i>
                                    <div class="text-lg font-medium text-gray-600">No disposal records found</div>
                                    <div class="text-sm text-gray-500 mt-1">No assets have been disposed yet</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $disposals->appends(request()->query())->links() }}
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white !important; }
    .shadow-md { box-shadow: none !important; }
}
</style>
@endsection
