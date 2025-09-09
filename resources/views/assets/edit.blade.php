@extends(request()->routeIs('gsu.*') ? 'layouts.gsu' : 'layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg p-10">
    <h2 class="text-3xl font-bold mb-8 text-gray-800 flex items-center gap-3">
        <i class="fas fa-edit text-red-800"></i> Edit Asset
    </h2>
    
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
    <form action="{{ request()->routeIs('gsu.*') ? route('gsu.assets.update', $asset) : route('assets.update', $asset) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-gray-700 font-semibold mb-2" for="name">Name</label>
                <input type="text" name="name" id="name" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" value="{{ old('name', $asset->name) }}" required>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2" for="category_id">Category</label>
                <select name="category_id" id="category_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $asset->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2" for="location_id">
                    @if($asset->status === 'In Use')
                        Original Location (Where asset will return)
                    @else
                        Location
                    @endif
                </label>
                <select name="location_id" id="location_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" required>
                    <option value="">Select Location</option>
                    @foreach($locations as $location)
                        @php
                            // For borrowed assets, show original location. For available assets, show current location
                            $displayLocationId = $asset->status === 'In Use' ? $asset->original_location_id : $asset->location_id;
                        @endphp
                        <option value="{{ $location->id }}" {{ old('location_id', $displayLocationId) == $location->id ? 'selected' : '' }}>
                            {{ $location->building }} - Floor {{ $location->floor }} - Room {{ $location->room }}
                        </option>
                    @endforeach
                </select>
                @if($asset->status === 'In Use')
                    <p class="text-sm text-blue-600 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Asset is currently borrowed and located at: 
                        <strong>{{ $asset->location->building }} - Floor {{ $asset->location->floor }} - Room {{ $asset->location->room }}</strong>
                    </p>
                @endif
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2" for="condition">Condition</label>
                <select name="condition" id="condition" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" required>
                    <option value="">Select Condition</option>
                    <option value="Good" {{ old('condition', $asset->condition) == 'Good' ? 'selected' : '' }}>Good</option>
                    <option value="Fair" {{ old('condition', $asset->condition) == 'Fair' ? 'selected' : '' }}>Fair</option>
                    <option value="Poor" {{ old('condition', $asset->condition) == 'Poor' ? 'selected' : '' }}>Poor</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-gray-700 font-semibold mb-2" for="description">Description</label>
                <textarea name="description" id="description" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" rows="3">{{ old('description', $asset->description) }}</textarea>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2" for="purchase_cost">Purchase Cost</label>
                <input type="number" step="0.01" name="purchase_cost" id="purchase_cost" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" value="{{ old('purchase_cost', $asset->purchase_cost) }}" required>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2" for="purchase_date">Purchase Date</label>
                <input type="date" name="purchase_date" id="purchase_date" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" value="{{ old('purchase_date', $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : '') }}" required>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2" for="status">Status</label>
                <select name="status" id="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" required>
                    <option value="Available" {{ old('status', $asset->status) == 'Available' ? 'selected' : '' }}>Available</option>
                    <option value="Disposed" {{ old('status', $asset->status) == 'Disposed' ? 'selected' : '' }}>Disposed</option>
                </select>
            </div>
        </div>
        
        <!-- Warranty Information Section -->
        <div class="mt-8 mb-6">
            <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2 mb-4">
                <i class="fas fa-shield-alt text-red-800"></i> Warranty Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="manufacturer">Manufacturer</label>
                    <input type="text" name="manufacturer" id="manufacturer" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" value="{{ old('manufacturer', $asset->warranty->manufacturer ?? '') }}" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="model">Model</label>
                    <input type="text" name="model" id="model" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" value="{{ old('model', $asset->warranty->model ?? '') }}" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2" for="warranty_expiry">Warranty Expiry Date</label>
                    <input type="date" name="warranty_expiry" id="warranty_expiry" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" value="{{ old('warranty_expiry', $asset->warranty && $asset->warranty->warranty_expiry ? \Carbon\Carbon::parse($asset->warranty->warranty_expiry)->format('Y-m-d') : '') }}" required>
                </div>
            </div>
        </div>
        <button type="submit" class="mt-8 w-full bg-gradient-to-r from-red-800 to-red-900 hover:from-red-900 hover:to-red-950 text-white font-bold py-3 px-6 rounded-lg transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 flex items-center justify-center gap-2">
            <i class="fas fa-save"></i> Update Asset
        </button>
    </form>
</div>

<style>
@keyframes fade-in { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: none; } }
.animate-fade-in { animation: fade-in 0.5s; }
</style>
@endsection
