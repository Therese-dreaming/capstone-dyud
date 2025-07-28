@extends('layouts.superadmin')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg p-10">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('users.index') }}" 
           class="inline-flex items-center justify-center w-10 h-10 bg-gray-100 text-gray-600 rounded-full hover:bg-gray-200 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
            <i class="fas fa-edit text-red-800"></i> Edit User
        </h2>
    </div>
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
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-gray-700 font-semibold mb-2" for="name">Name</label>
                <input type="text" name="name" id="name" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" value="{{ old('name', $user->name) }}" required>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2" for="email">Email</label>
                <input type="email" name="email" id="email" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" value="{{ old('email', $user->email) }}" required>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2" for="id_number">ID Number</label>
                <input type="text" name="id_number" id="id_number" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" value="{{ old('id_number', $user->id_number) }}" required>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2" for="password">Password (Leave blank to keep current)</label>
                <input type="password" name="password" id="password" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800">
            </div>
            <div class="md:col-span-2">
                <label class="block text-gray-700 font-semibold mb-2" for="role">Role</label>
                <select name="role" id="role" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" required>
                    <option value="superadmin" {{ old('role', $user->role) == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                </select>
            </div>
        </div>
        <button type="submit" class="mt-8 w-full bg-gradient-to-r from-red-800 to-red-900 hover:from-red-900 hover:to-red-950 text-white font-bold py-3 px-6 rounded-lg transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 flex items-center justify-center gap-2">
            <i class="fas fa-user-edit"></i> Update User
        </button>
    </form>
</div>

<style>
@keyframes fade-in { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: none; } }
.animate-fade-in { animation: fade-in 0.5s; }
</style>
@endsection
