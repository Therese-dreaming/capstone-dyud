@extends('layouts.gsu')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg p-10">
    <h2 class="text-3xl font-bold mb-8 text-gray-800 flex items-center gap-3">
        <i class="fas fa-edit text-red-800"></i> Edit Maintenance Record for {{ $asset->name }}
    </h2>
    
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('gsu.maintenances.update', $maintenance) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-gray-700 font-semibold mb-2" for="type">Type</label>
                <select name="type" id="type" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" required>
                    <option value="">Select Type</option>
                    <option value="Preventive" {{ old('type', $maintenance->type) == 'Preventive' ? 'selected' : '' }}>Preventive</option>
                    <option value="Corrective" {{ old('type', $maintenance->type) == 'Corrective' ? 'selected' : '' }}>Corrective</option>
                    <option value="Emergency" {{ old('type', $maintenance->type) == 'Emergency' ? 'selected' : '' }}>Emergency</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2" for="technician">Technician</label>
                <input type="text" name="technician" id="technician" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" value="{{ old('technician', $maintenance->technician) }}" required>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2" for="status">Status</label>
                <select name="status" id="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" required>
                    <option value="">Select Status</option>
                    <option value="Scheduled" {{ old('status', $maintenance->status) == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="In Progress" {{ old('status', $maintenance->status) == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="Completed" {{ old('status', $maintenance->status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="Cancelled" {{ old('status', $maintenance->status) == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2" for="scheduled_date">Scheduled Date</label>
                <input type="date" name="scheduled_date" id="scheduled_date" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" value="{{ old('scheduled_date', $maintenance->scheduled_date) }}" required>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2" for="completed_date">Completed Date</label>
                <input type="date" name="completed_date" id="completed_date" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" value="{{ old('completed_date', $maintenance->completed_date) }}">
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2" for="cost">Cost</label>
                <input type="number" step="0.01" name="cost" id="cost" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" value="{{ old('cost', $maintenance->cost) }}">
            </div>
            <div class="md:col-span-2">
                <label class="block text-gray-700 font-semibold mb-2" for="description">Description</label>
                <textarea name="description" id="description" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" rows="3">{{ old('description', $maintenance->description) }}</textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-gray-700 font-semibold mb-2" for="notes">Notes</label>
                <textarea name="notes" id="notes" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-800" rows="3">{{ old('notes', $maintenance->notes) }}</textarea>
            </div>
        </div>
        <div class="flex justify-end mt-8 gap-4">
            <a href="{{ route('gsu.maintenances.index', $asset) }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600">Cancel</a>
            <button type="submit" class="bg-red-800 text-white px-6 py-3 rounded-lg hover:bg-red-900">
                <i class="fas fa-save"></i> Update Maintenance Record
            </button>
        </div>
    </form>
</div>
@endsection
