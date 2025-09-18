@extends('layouts.purchasing')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{ showDeleteModal: false, deleteAssetId: null, deleteAssetCode: '', deleteAssetName: '' }">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">My Assets</h1>
                <p class="mt-1 text-sm text-gray-600">Manage your registered assets and track approval status</p>
            </div>
            <a href="{{ route('purchasing.assets.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Register New Asset
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg" x-data="{ show: true }" x-show="show">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-3"></i>
                    <p class="text-green-800">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-green-600 hover:text-green-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg" x-data="{ show: true }" x-show="show">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-600 mr-3"></i>
                    <p class="text-red-800">{{ session('error') }}</p>
                </div>
                <button @click="show = false" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- Filter Tabs -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <a href="{{ route('purchasing.assets.index') }}" 
                   class="py-2 px-1 border-b-2 font-medium text-sm {{ !request()->get('status') ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    All Assets
                </a>
                <a href="{{ route('purchasing.assets.index', ['status' => 'pending']) }}" 
                   class="py-2 px-1 border-b-2 font-medium text-sm {{ request()->get('status') == 'pending' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Pending Approval
                </a>
                <a href="{{ route('purchasing.assets.index', ['status' => 'approved']) }}" 
                   class="py-2 px-1 border-b-2 font-medium text-sm {{ request()->get('status') == 'approved' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Approved
                </a>
                <a href="{{ route('purchasing.assets.index', ['status' => 'rejected']) }}" 
                   class="py-2 px-1 border-b-2 font-medium text-sm {{ request()->get('status') == 'rejected' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Rejected
                </a>
            </nav>
        </div>
    </div>

    <!-- Assets Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Asset Details
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Category
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Purchase Info
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Created
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($assets as $asset)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $asset->asset_code }}</div>
                                    <div class="text-sm text-gray-500">{{ $asset->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $asset->category->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">₱{{ number_format($asset->purchase_cost, 2) }}</div>
                                    <div class="text-sm text-gray-500">{{ $asset->purchase_date->format('M d, Y') }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-orange-100 text-orange-800',
                                        'approved' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                    ];
                                    $status = strtolower($asset->getApprovalStatusLabel());
                                    $statusClass = $statusClasses[$status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                    {{ $asset->getApprovalStatusLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $asset->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('purchasing.assets.show', $asset) }}" 
                                       class="inline-flex items-center p-2 bg-purple-100 text-purple-600 hover:text-purple-900 hover:bg-purple-200 rounded-lg transition-colors"
                                       title="View Asset">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($asset->isPending())
                                        <a href="{{ route('purchasing.assets.edit', $asset) }}" 
                                           class="inline-flex items-center p-2 bg-blue-100 text-blue-600 hover:text-blue-900 hover:bg-blue-200 rounded-lg transition-colors"
                                           title="Edit Asset">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <button @click="showDeleteModal = true; deleteAssetId = {{ $asset->id }}; deleteAssetCode = '{{ addslashes($asset->asset_code) }}'; deleteAssetName = '{{ addslashes($asset->name) }}'" 
                                                class="inline-flex items-center p-2 bg-red-100 text-red-600 hover:text-red-900 hover:bg-red-200 rounded-lg transition-colors"
                                                title="Delete Asset">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-box-open text-4xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No assets found</h3>
                                    <p class="text-gray-500 mb-4">Get started by registering your first asset.</p>
                                    <a href="{{ route('purchasing.assets.create') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                        <i class="fas fa-plus mr-2"></i>
                                        Register New Asset
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($assets->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $assets->links() }}
        </div>
    @endif

    <!-- Delete Modal -->
    <div x-show="showDeleteModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" style="display: none;">
        <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-md relative">
            <button @click="showDeleteModal = false" class="absolute top-3 right-3 text-gray-400 hover:text-red-600 text-xl">
                <i class="fas fa-times"></i>
            </button>
            <div class="flex flex-col items-center">
                <div class="bg-red-100 text-red-600 rounded-full p-4 mb-4">
                    <i class="fas fa-exclamation-triangle text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2 text-gray-800">Delete Asset</h3>
                <p class="text-gray-600 mb-2 text-center">
                    Are you sure you want to delete asset <span class="font-semibold text-purple-600" x-text="deleteAssetCode"></span>?
                </p>
                <p class="text-sm text-gray-500 mb-6 text-center" x-text="deleteAssetName"></p>
                <p class="text-sm text-red-600 mb-6 text-center font-medium">
                    This action cannot be undone.
                </p>
                <form :action="'{{ route('purchasing.assets.index') }}/' + deleteAssetId" method="POST" class="w-full flex flex-col items-center gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-trash-alt"></i> Delete Asset
                    </button>
                    <button type="button" @click="showDeleteModal = false" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
