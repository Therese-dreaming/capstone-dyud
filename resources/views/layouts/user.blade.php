<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Asset Management System')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom Styles -->
    <style>
        .animate__animated {
            animation-duration: 0.5s;
        }
        .animate__fadeIn {
            animation-name: fadeIn;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <div class="bg-gradient-to-r from-red-600 to-red-800 text-white p-2 rounded-lg">
                            <i class="fas fa-boxes text-xl"></i>
                        </div>
                        <span class="ml-3 text-xl font-bold text-gray-900">Asset Management</span>
                    </div>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'text-red-600 bg-red-50' : '' }}">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-red-600"></i>
                            </div>
                            <span class="hidden md:block">{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                            <div class="px-4 py-2 text-sm text-gray-700 border-b">
                                <div class="font-medium">{{ auth()->user()->name }}</div>
                                <div class="text-gray-500">{{ auth()->user()->id_number }}</div>
                            </div>
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobile Navigation Menu -->
        <div class="md:hidden" x-data="{ open: false }">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 border-t border-gray-200">
                <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-red-600 block px-3 py-2 rounded-md text-base font-medium transition-colors {{ request()->routeIs('dashboard') ? 'text-red-600 bg-red-50' : '' }}">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </a>

            </div>
        </div>
    </nav>

    <!-- Breadcrumb Navigation -->
    @if(isset($breadcrumbs))
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-4">
                    <li>
                        <div>
                            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-500">
                                <i class="fas fa-home"></i>
                                <span class="sr-only">Home</span>
                            </a>
                        </div>
                    </li>
                    @foreach($breadcrumbs as $breadcrumb)
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            @if($loop->last)
                                <span class="text-gray-500">{{ $breadcrumb['title'] }}</span>
                            @else
                                <a href="{{ $breadcrumb['url'] }}" class="text-gray-700 hover:text-red-600">{{ $breadcrumb['title'] }}</a>
                            @endif
                        </div>
                    </li>
                    @endforeach
                </ol>
            </nav>
        </div>
    </div>
    @endif

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Toast Notifications -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
             class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate__animated animate__fadeIn max-w-md">
            <div class="flex items-start">
                <i class="fas fa-check-circle mr-2 mt-0.5 flex-shrink-0"></i>
                <div class="flex-1 min-w-0">
                    <div class="text-sm break-words">{{ session('success') }}</div>
                </div>
                <button @click="show = false" class="ml-3 text-white hover:text-gray-200 flex-shrink-0">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
             class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate__animated animate__fadeIn max-w-md">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle mr-2 mt-0.5 flex-shrink-0"></i>
                <div class="flex-1 min-w-0">
                    <div class="text-sm break-words">{{ session('error') }}</div>
                </div>
                <button @click="show = false" class="ml-3 text-white hover:text-gray-200 flex-shrink-0">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
             class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate__animated animate__fadeIn">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <div>
                    <div class="font-medium">Please fix the following errors:</div>
                    <ul class="text-sm mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif
</body>
</html> 