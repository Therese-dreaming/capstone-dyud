<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-white flex items-center justify-center min-h-screen p-4">
    <div class="container mx-auto">
        <div class="flex flex-col lg:flex-row rounded-xl shadow-2xl overflow-hidden max-w-6xl mx-auto">
            <!-- Right Column - Logo and Design (shown first on mobile) -->
            <div class="w-full lg:w-1/2 bg-gradient-to-br from-red-800 to-red-900 p-8 md:p-12 flex flex-col items-center justify-center text-white order-1 lg:order-2">
                <div class="text-center">
                    <div class="flex justify-center items-center">
                        <img src="{{ asset('images/logo-small.png') }}" alt="Logo" class="w-32 h-32 md:w-52 md:h-52 mb-4 md:mb-8">
                    </div>
                    <h1 class="text-2xl md:text-3xl font-bold mb-2 md:mb-4">Inventory Management System</h1>
                    <p class="text-lg md:text-xl opacity-90">GSU Department</p>
                </div>
            </div>
            
            <!-- Left Column - Form (shown second on mobile) -->
            <div class="w-full lg:w-1/2 p-6 md:p-8 lg:p-12 order-2 lg:order-1">
                <div class="mb-6 md:mb-8">
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Welcome Back</h2>
                    <p class="text-sm md:text-base text-gray-600 mt-2">Please sign in to continue</p>
                </div>

                <!-- Success Message -->
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Error Messages -->
                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="/login" method="POST">
                    @csrf
                    <div class="mb-4 md:mb-6">
                        <label for="id_number" class="block text-gray-700 text-sm font-semibold mb-2">
                            <i class="fas fa-id-card mr-2 text-red-800"></i>ID Number
                        </label>
                        <input type="text" id="id_number" name="id_number" 
                            class="w-full border border-gray-300 rounded-lg py-2.5 md:py-3 px-4 text-sm md:text-base text-gray-700 placeholder-gray-400 focus:outline-none focus:border-red-800 focus:ring-1 focus:ring-red-800 transition duration-200"
                            required placeholder="Enter your ID number" value="{{ old('id_number') }}">
                    </div>
                    <div class="mb-4 md:mb-6">
                        <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">
                            <i class="fas fa-lock mr-2 text-red-800"></i>Password
                        </label>
                        <input type="password" id="password" name="password" 
                            class="w-full border border-gray-300 rounded-lg py-2.5 md:py-3 px-4 text-sm md:text-base text-gray-700 placeholder-gray-400 focus:outline-none focus:border-red-800 focus:ring-1 focus:ring-red-800 transition duration-200"
                            required placeholder="Enter your password">
                    </div>
                    <div class="mb-4 md:mb-6">
                        <button type="submit" 
                            class="w-full bg-gradient-to-r from-red-800 to-red-900 hover:from-red-900 hover:to-red-950 text-white font-bold py-2.5 md:py-3 px-6 rounded-lg transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 text-sm md:text-base">
                            <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                        </button>
                    </div>
                    <div class="text-center">
                        <a class="text-sm md:text-base text-gray-600 hover:text-red-800 font-semibold transition duration-200" href="/register">
                            <i class="fas fa-user-plus mr-2"></i>Don't have an account?
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>