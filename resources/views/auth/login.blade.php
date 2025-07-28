<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-white flex items-center justify-center min-h-screen">
    <div class="container mx-auto">
        <div class="flex rounded-xl shadow-2xl overflow-hidden max-w-6xl mx-auto">
            <!-- Left Column - Form -->
            <div class="w-1/2 p-12">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-800">Welcome Back</h2>
                    <p class="text-gray-600 mt-2">Please sign in to continue</p>
                </div>
                <form action="/login" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label for="id_number" class="block text-gray-700 text-sm font-semibold mb-2">
                            <i class="fas fa-id-card mr-2 text-red-800"></i>ID Number
                        </label>
                        <input type="text" id="id_number" name="id_number" 
                            class="w-full border border-gray-300 rounded-lg py-3 px-4 text-gray-700 placeholder-gray-400 focus:outline-none focus:border-red-800 focus:ring-1 focus:ring-red-800 transition duration-200"
                            required placeholder="Enter your ID number">
                    </div>
                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">
                            <i class="fas fa-lock mr-2 text-red-800"></i>Password
                        </label>
                        <input type="password" id="password" name="password" 
                            class="w-full border border-gray-300 rounded-lg py-3 px-4 text-gray-700 placeholder-gray-400 focus:outline-none focus:border-red-800 focus:ring-1 focus:ring-red-800 transition duration-200"
                            required placeholder="Enter your password">
                    </div>
                    <div class="mb-6">
                        <button type="submit" 
                            class="w-full bg-gradient-to-r from-red-800 to-red-900 hover:from-red-900 hover:to-red-950 text-white font-bold py-3 px-6 rounded-lg transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                            <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                        </button>
                    </div>
                    <div class="text-center">
                        <a class="text-gray-600 hover:text-red-800 font-semibold transition duration-200" href="/register">
                            <i class="fas fa-user-plus mr-2"></i>Don't have an account?
                        </a>
                    </div>
                </form>
            </div>
            
            <!-- Right Column - Logo and Design -->
            <div class="w-1/2 bg-gradient-to-br from-red-800 to-red-900 p-12 flex flex-col items-center justify-center text-white">
                <div class="text-center">
                    <div class="flex justify-center items-center">
                        <img src="{{ asset('images/logo-small.png') }}" alt="Logo" class="w-52 h-52 mb-8">
                    </div>
                    <h1 class="text-3xl font-bold mb-4">Inventory Management System</h1>
                    <p class="text-xl opacity-90">GSU Department</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>