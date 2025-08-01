@extends('layouts.superadmin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-red-50" x-data="{ 
    selectedCategory: '{{ old('category') }}'
}">
    <!-- Page Header -->
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="bg-gradient-to-r from-red-600 to-red-800 text-white p-3 rounded-xl shadow-lg">
                        <i class="fas fa-plus-circle text-xl"></i>
                    </div>
                    New Borrowing
                </h1>
                <p class="text-gray-600 mt-2 text-sm md:text-base">Create a new asset borrowing request</p>
            </div>
                <div class="mt-4 sm:mt-0">
                <a href="{{ route('borrowing.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors text-sm font-medium flex items-center gap-2 shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                    Back to Borrowings
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
            <div class="p-6 md:p-8">
                <!-- Category Selection -->
                <div x-show="!selectedCategory" class="animate__animated animate__fadeIn">
                    <h3 class="text-xl leading-6 font-medium text-gray-900 mb-6 border-b pb-3">
                        Select Asset Category to Borrow
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                        <button @click="selectedCategory = 'Electronics & IT Equipments'" class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 hover:shadow-md hover:border-red-300 transition-all duration-200 flex flex-col items-center transform hover:-translate-y-1">
                            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mb-4 shadow-inner">
                                <i class="fas fa-laptop text-blue-600 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">Electronics & IT Equipments</h3>
                        </button>
                        
                        <button @click="selectedCategory = 'Fixtures'" class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 hover:shadow-md hover:border-red-300 transition-all duration-200 flex flex-col items-center transform hover:-translate-y-1">
                            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-4 shadow-inner">
                                <i class="fas fa-lightbulb text-green-600 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">Fixtures</h3>
                        </button>
                        
                        <button @click="selectedCategory = 'Furnitures'" class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 hover:shadow-md hover:border-red-300 transition-all duration-200 flex flex-col items-center transform hover:-translate-y-1">
                            <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mb-4 shadow-inner">
                                <i class="fas fa-chair text-yellow-600 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">Furnitures</h3>
                        </button>
                        
                        <button @click="selectedCategory = 'Religious or Institutional Items'" class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 hover:shadow-md hover:border-red-300 transition-all duration-200 flex flex-col items-center transform hover:-translate-y-1">
                            <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mb-4 shadow-inner">
                                <i class="fas fa-place-of-worship text-purple-600 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">Religious or Institutional Items</h3>
                        </button>
                        
                        <button @click="selectedCategory = 'Teaching & Presentation Tools'" class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 hover:shadow-md hover:border-red-300 transition-all duration-200 flex flex-col items-center transform hover:-translate-y-1">
                            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mb-4 shadow-inner">
                                <i class="fas fa-chalkboard-teacher text-red-600 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">Teaching & Presentation Tools</h3>
                        </button>
                    </div>
                </div>
                
                <!-- Borrowing Form -->
                <div x-show="selectedCategory" class="animate__animated animate__fadeIn">
                    <h3 class="text-xl leading-6 font-medium text-gray-900 mb-6 border-b pb-3 flex items-center">
                        <button @click="selectedCategory = ''" class="mr-3 text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-full p-2 transition-colors">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        Borrowing Form - <span x-text="selectedCategory" class="text-red-600 ml-2"></span>
                    </h3>
                    
                    <form action="{{ route('borrowing.store') }}" method="POST" class="mt-6">
                        @csrf
                        <input type="hidden" name="category" x-bind:value="selectedCategory">
                        
                        @if ($errors->any())
                        <div class="mb-6">
                            <div class="bg-red-50 border-l-4 border-red-500 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <ul class="list-disc pl-5 space-y-1">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-6">
                            <h4 class="font-medium text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-info-circle mr-2 text-red-500"></i>
                                Borrowing Information
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Borrow Date</label>
                                    <div class="relative">
                                        <input type="date" id="date" name="date" value="{{ old('date') }}" 
                                            class="px-4 py-3 bg-white border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 block w-full rounded-md transition-all text-gray-700" required>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="time" class="block text-sm font-medium text-gray-700 mb-2">Borrow Time</label>
                                    <div class="relative">
                                        <input type="time" id="time" name="time" value="{{ old('time') }}" 
                                            class="px-4 py-3 bg-white border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 block w-full rounded-md transition-all text-gray-700" required>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="location_id" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                                    <div class="relative">
                                        <select id="location_id" name="location_id" 
                                            class="px-4 py-3 bg-white border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 block w-full rounded-md transition-all text-gray-700" required>
                                            <option value="">Select a location</option>
                                            @foreach($locations as $location)
                                                <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                                    {{ $location->building }} - Floor {{ $location->floor }} - Room {{ $location->room }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                                    <div class="relative">
                                        <input type="date" id="due_date" name="due_date" value="{{ old('due_date') }}" 
                                            class="px-4 py-3 bg-white border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 block w-full rounded-md transition-all text-gray-700" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-6">
                            <h4 class="font-medium text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-user mr-2 text-red-500"></i>
                                Borrower Information
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                    <div class="relative">
                                        <input type="text" id="name" name="name" value="{{ old('name') }}" 
                                            class="px-4 py-3 bg-white border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 block w-full rounded-md transition-all text-gray-700" 
                                            placeholder="Enter your full name" required>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="id_number" class="block text-sm font-medium text-gray-700 mb-2">ID Number</label>
                                    <div class="relative">
                                        <input type="text" id="id_number" name="id_number" value="{{ old('id_number') }}" 
                                            class="px-4 py-3 bg-white border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 block w-full rounded-md transition-all text-gray-700" 
                                            placeholder="Enter your ID number" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-6">
                            <h4 class="font-medium text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-box-open mr-2 text-red-500"></i>
                                Asset Information
                            </h4>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Selected Category</label>
                                <div class="p-3 bg-red-50 rounded-md border border-red-200 text-red-700 font-medium">
                                    <span x-text="selectedCategory"></span>
                                </div>
                            </div>
                            
                            <div>
                                <label for="items" class="block text-sm font-medium text-gray-700 mb-2">Select Items</label>
                                <div class="relative">
                                    <select id="items" name="items[]" 
                                        class="px-4 py-3 bg-white border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 block w-full rounded-md transition-all text-gray-700" 
                                        multiple required size="5">
                                        <optgroup label="Electronics & IT Equipments" x-show="selectedCategory === 'Electronics & IT Equipments'">
                                            <option value="laptop">Laptop</option>
                                            <option value="projector">Projector</option>
                                            <option value="camera">Camera</option>
                                            <option value="microphone">Microphone</option>
                                        </optgroup>
                                        
                                        <optgroup label="Fixtures" x-show="selectedCategory === 'Fixtures'">
                                            <option value="lamp">Lamp</option>
                                            <option value="fan">Fan</option>
                                            <option value="whiteboard">Whiteboard</option>
                                        </optgroup>
                                        
                                        <optgroup label="Furnitures" x-show="selectedCategory === 'Furnitures'">
                                            <option value="chair">Chair</option>
                                            <option value="table">Table</option>
                                            <option value="cabinet">Cabinet</option>
                                        </optgroup>
                                        
                                        <optgroup label="Religious or Institutional Items" x-show="selectedCategory === 'Religious or Institutional Items'">
                                            <option value="cross">Cross</option>
                                            <option value="flag">Flag</option>
                                            <option value="banner">Banner</option>
                                        </optgroup>
                                        
                                        <optgroup label="Teaching & Presentation Tools" x-show="selectedCategory === 'Teaching & Presentation Tools'">
                                            <option value="pointer">Laser Pointer</option>
                                            <option value="markers">Markers</option>
                                            <option value="flipchart">Flip Chart</option>
                                        </optgroup>
                                    </select>
                                </div>
                                <p class="mt-2 text-xs text-gray-500 flex items-center">
                                    <i class="fas fa-info-circle mr-1 text-red-500"></i>
                                    Hold Ctrl/Cmd to select multiple items
                                </p>
                            </div>
                            
                            <div class="mt-4">
                                <label for="purpose" class="block text-sm font-medium text-gray-700 mb-2">Purpose of Borrowing</label>
                                <div class="relative">
                                    <textarea id="purpose" name="purpose" rows="3" 
                                        class="px-4 py-3 bg-white border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 block w-full rounded-md transition-all text-gray-700" 
                                        placeholder="Explain why you need to borrow these items">{{ old('purpose') }}</textarea>
                                </div>
                            </div>
                        </div>
                    
                        <div class="flex justify-end space-x-3 mt-8">
                            <a href="{{ route('borrowing.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm transition-colors">
                                <i class="fas fa-times mr-2"></i> Cancel
                            </a>
                            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-6 py-3 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm transition-colors">
                                <i class="fas fa-paper-plane mr-2"></i> Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set default date values for the form
    const today = new Date().toISOString().split('T')[0];
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const tomorrowStr = tomorrow.toISOString().split('T')[0];
    
    // Function to set default values
    function setDefaultValues() {
        const dateInput = document.querySelector('input[name="date"]');
        const dueDateInput = document.querySelector('input[name="due_date"]');
        const timeInput = document.querySelector('input[name="time"]');
        
        if (dateInput && !dateInput.value) {
            dateInput.value = today;
        }
        if (dueDateInput && !dueDateInput.value) {
            dueDateInput.value = tomorrowStr;
        }
        if (timeInput && !timeInput.value) {
            timeInput.value = new Date().toTimeString().slice(0, 5);
        }
    }
    
    // Set default values when page loads
    setDefaultValues();
    
    // Form validation
    const borrowingForm = document.querySelector('form[action*="borrowing"]');
    if (borrowingForm) {
        borrowingForm.addEventListener('submit', function(e) {
            // Check dates
            const dueDate = this.querySelector('input[name="due_date"]').value;
            const borrowDate = this.querySelector('input[name="date"]').value;
            
            if (dueDate <= borrowDate) {
                e.preventDefault();
                alert('Due date must be after the borrow date.');
                return false;
            }
        });
    }
});
</script>
@endsection