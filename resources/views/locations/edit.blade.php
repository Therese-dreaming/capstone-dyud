@extends('layouts.superadmin')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl shadow-lg p-10">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('locations.index') }}" 
           class="inline-flex items-center justify-center w-10 h-10 bg-gray-100 text-gray-600 rounded-full hover:bg-gray-200 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
            <i class="fas fa-edit text-red-800"></i> Edit Location
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
    
    <form action="{{ route('locations.update', $location) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2" for="building">Building</label>
            <input type="text" name="building" id="building" 
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" 
                   value="{{ old('building', $location->building) }}" required>
        </div>
        
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2" for="floor">Floor</label>
            <input type="text" name="floor" id="floor" 
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" 
                   value="{{ old('floor', $location->floor) }}" required>
        </div>
        
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2" for="room">Room</label>
            <input type="text" name="room" id="room" 
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" 
                   value="{{ old('room', $location->room) }}" required>
        </div>
        
        <div class="flex gap-4">
            <button type="submit" 
                    class="flex-1 bg-gradient-to-r from-red-800 to-red-900 hover:from-red-900 hover:to-red-950 text-white font-bold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                <i class="fas fa-save"></i> Update Location
            </button>
            
            <a href="{{ route('locations.index') }}" 
               class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold rounded-lg transition duration-200 flex items-center justify-center gap-2">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<style>
@keyframes fade-in { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: none; } }
.animate-fade-in { animation: fade-in 0.5s; }
</style>
@endsection
