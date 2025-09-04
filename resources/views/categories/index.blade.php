@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-8" x-data="{ showToast: {{ session('success') || session('error') ? 'true' : 'false' }} }">
    @if(session('success'))
        <div x-show="showToast" x-transition class="fixed top-6 right-6 z-50 bg-green-900 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-4 animate-fade-in min-w-[300px] border border-green-700"
            x-init="setTimeout(() => showToast = false, 3000)">
            <i class="fas fa-check-circle text-2xl text-green-300"></i>
            <span class="font-semibold">{{ session('success') }}</span>
            <button @click="showToast = false" class="ml-auto text-green-200 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div x-show="showToast" x-transition class="fixed top-6 right-6 z-50 bg-red-900 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-4 animate-fade-in min-w-[300px] border border-red-700"
            x-init="setTimeout(() => showToast = false, 3000)">
            <i class="fas fa-times-circle text-2xl text-red-300"></i>
            <span class="font-semibold">{{ session('error') }}</span>
            <button @click="showToast = false" class="ml-auto text-red-200 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
    @endif
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold flex items-center gap-2"><i class="fas fa-folder-open text-red-800"></i> Categories</h1>
        <a href="{{ route('categories.create') }}" class="bg-red-800 hover:bg-red-900 text-white font-bold py-2 px-4 rounded transition duration-200 flex items-center gap-2">
            <i class="fas fa-folder-plus"></i> Add Category
        </a>
    </div>
    @if($categories->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($categories as $category)
            <a href="{{ route('categories.show', $category) }}" class="bg-white rounded-xl shadow p-6 flex flex-col items-center relative hover:shadow-lg transition-shadow cursor-pointer">
                <div class="mb-2">
                    <i class="fas fa-folder text-3xl text-red-800"></i>
                </div>
                <div class="text-center">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2 justify-center hover:text-red-800">
                        {{ $category->name }}
                    </h2>
                    <div class="text-gray-400 text-xs mt-1">
                        {{ $category->assets_count }}
                    </div>
                </div>
            </a>
        @endforeach
    </div>
    @else
    <div class="text-center text-gray-500 py-12">
        <i class="fas fa-folder-open text-4xl mb-4"></i>
        <div>No categories found.</div>
    </div>
    @endif
</div>
<style>
@keyframes fade-in { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: none; } }
.animate-fade-in { animation: fade-in 0.5s; }
</style>
@endsection 