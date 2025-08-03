@extends('layouts.gsu')

@section('content')
<div class="container mx-auto py-8" x-data="{ showToast: {{ session('success') || session('error') ? 'true' : 'false' }} }">
    <!-- GSU Category Management Header -->
    <div class="bg-gradient-to-r from-red-800 to-red-900 text-white p-6 rounded-xl shadow-lg mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-white/20 p-3 rounded-full">
                    <i class="fas fa-folder-open text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold">Category Management</h1>
                    <p class="text-red-100 text-sm md:text-base">GSU Asset Category Control Panel</p>
                </div>
            </div>
            <a href="{{ route('categories.create') }}" class="bg-white text-red-800 font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center gap-2 shadow-lg hover:bg-gray-100">
                <i class="fas fa-folder-plus"></i> Add Category
            </a>
        </div>
    </div>

    <!-- Category Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-blue-100 p-3 rounded-xl">
                    <i class="fas fa-folder-open text-blue-600 text-xl"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">{{ $categories->count() }}</div>
                    <div class="text-sm text-gray-500">Total Categories</div>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500">Asset types</span>
                <span class="text-blue-600 text-sm font-medium">Active</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-green-100 p-3 rounded-xl">
                    <i class="fas fa-boxes text-green-600 text-xl"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">{{ $categories->sum('assets_count') }}</div>
                    <div class="text-sm text-gray-500">Total Assets</div>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500">Across categories</span>
                <span class="text-green-600 text-sm font-medium">Distributed</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-purple-100 p-3 rounded-xl">
                    <i class="fas fa-chart-pie text-purple-600 text-xl"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">{{ $categories->where('assets_count', '>', 0)->count() }}</div>
                    <div class="text-sm text-gray-500">Active Categories</div>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500">With assets</span>
                <span class="text-purple-600 text-sm font-medium">Populated</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-yellow-100 p-3 rounded-xl">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">{{ $categories->where('assets_count', 0)->count() }}</div>
                    <div class="text-sm text-gray-500">Empty Categories</div>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500">No assets</span>
                <span class="text-yellow-600 text-sm font-medium">Attention</span>
            </div>
        </div>
    </div>

    <!-- Categories Grid -->
    @if($categories->count())
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-folder-open text-red-600"></i>
                    Asset Categories
                </h2>
                <div class="text-sm text-gray-600">
                    Showing {{ $categories->count() }} categories
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($categories as $category)
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 hover:shadow-lg transition-all duration-300 border border-gray-200 hover:border-red-300 group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-red-100 p-3 rounded-lg group-hover:bg-red-200 transition-colors">
                                <i class="fas fa-folder text-red-600 text-xl"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-gray-900">{{ $category->assets_count }}</div>
                                <div class="text-xs text-gray-500">assets</div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $category->name }}</h3>
                            <div class="text-sm text-gray-600 mb-3">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-code text-gray-400"></i>
                                    <span class="font-mono">{{ $category->code }}</span>
                                </div>
                            </div>
                            
                            @if($category->assets_count > 0)
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-3">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-check-circle text-green-600"></i>
                                        <span class="text-sm font-medium text-green-800">{{ $category->assets_count }} assets</span>
                                    </div>
                                </div>
                            @else
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-3">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                                        <span class="text-sm font-medium text-yellow-800">No assets</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="text-xs text-gray-500">
                                Created {{ $category->created_at->diffForHumans() }}
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('categories.show', $category) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors duration-150"
                                   title="View Category">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('categories.edit', $category) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 bg-yellow-100 text-yellow-600 rounded-full hover:bg-yellow-200 transition-colors duration-150"
                                   title="Edit Category">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                @if($category->assets_count == 0)
                                <button onclick="confirmDelete('{{ $category->name }}', {{ $category->id }})"
                                        class="inline-flex items-center justify-center w-8 h-8 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-150"
                                        title="Delete Category">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <div class="text-gray-400 mb-6">
            <i class="fas fa-folder-open text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">No categories found</h3>
            <p class="text-gray-500">Get started by creating your first asset category</p>
        </div>
        <a href="{{ route('categories.create') }}" class="inline-block bg-red-800 text-white px-6 py-3 rounded-lg hover:bg-red-900 transition-colors font-medium">
            <i class="fas fa-folder-plus mr-2"></i>Create First Category
        </a>
    </div>
    @endif

    <!-- Category Analytics -->
    @if($categories->where('assets_count', '>', 0)->count() > 0)
    <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <i class="fas fa-chart-bar text-red-600"></i>
                Category Analytics
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($categories->where('assets_count', '>', 0)->take(5) as $category)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="bg-red-100 p-2 rounded-lg">
                            <i class="fas fa-folder text-red-600"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">{{ $category->name }}</div>
                            <div class="text-sm text-gray-500">{{ $category->code }}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-gray-900">{{ $category->assets_count }}</div>
                        <div class="text-xs text-gray-500">assets</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
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

<script>
function confirmDelete(categoryName, categoryId) {
    if (confirm(`Are you sure you want to delete category "${categoryName}"? This action cannot be undone.`)) {
        // Create a form and submit it for deletion
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/categories/${categoryId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection 