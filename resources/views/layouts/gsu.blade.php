<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GSU Dashboard - Inventory Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font Awesome Fallback CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-gray-50 min-h-screen font-['Inter']">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-xl flex flex-col transition-all duration-300" x-data="{ expanded: true }">
            <!-- GSU Profile Section -->
            <div class="p-4 border-b border-gray-100 flex-shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-red-800 rounded-lg flex items-center justify-center">
                        <i class="fas fa-crown text-white text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h2 class="font-semibold text-gray-800">{{ Auth::user()->name ?? 'GSU Admin' }}</h2>
                        <p class="text-sm text-gray-500">Super Administrator</p>
                        <p class="text-xs text-gray-400">Last login: {{ Auth::user()->last_login ? Auth::user()->last_login->diffForHumans() : 'Never' }}</p>
                    </div>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="p-4 flex-shrink-0">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" placeholder="Search assets..."
                        class="w-full pl-10 pr-4 py-2 bg-gray-50 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-800/20">
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-4 overflow-y-auto">
                <div class="py-4">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center px-4 py-3 text-gray-700 hover:bg-red-50 rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-red-100 text-red-800' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path
                                d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                        <span class="ml-3">Dashboard</span>
                    </a>
                </div>
                
                <ul class="space-y-2">
                    <!-- Asset Management -->
                    <li x-data="{ open: false }">
                        <button @click="open = !open" type="button"
                            class="flex items-center w-full px-4 py-2.5 text-gray-600 rounded-lg hover:bg-red-50 hover:text-red-800 focus:outline-none transition justify-between"
                            :class="{ 'bg-red-50 text-red-800': open }">
                            <span class="flex items-center">
                                <i class="fas fa-boxes w-5"></i>
                                <span class="ml-3 text-sm">Asset Management</span>
                            </span>
                            <i class="fas fa-chevron-down ml-2 transition-transform" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <ul x-show="open" x-transition class="ml-8 mt-2 space-y-1" style="display: none;">
                            <li>
                                <a href="{{ route('gsu.assets.create') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->routeIs('gsu.assets.create') ? 'bg-red-100 text-red-800' : 'text-gray-600' }}">
                                    <i class="fas fa-plus mr-2 w-4"></i> Create Asset
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('gsu.assets.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->routeIs('gsu.assets.index') ? 'bg-red-100 text-red-800' : 'text-gray-600' }}">
                                    <i class="fas fa-list mr-2 w-4"></i> Asset List
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('gsu.qr.scanner') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->routeIs('gsu.qr.scanner') ? 'bg-red-100 text-red-800' : 'text-gray-600' }}">
                                    <i class="fas fa-qrcode mr-2 w-4"></i> QR Scanner
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>

            <!-- Bottom Section -->
            <div class="p-4 border-t border-gray-100 flex-shrink-0">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="flex items-center w-full px-4 py-2.5 text-gray-600 rounded-lg hover:bg-red-50 hover:text-red-800 transition">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span class="ml-3">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow-sm h-16 flex items-center px-6">
                <div class="flex-1">
                    <h1 class="text-xl font-semibold text-gray-800">GSU Inventory Management System</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <button class="relative p-2 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-bell text-gray-600"></i>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-600 rounded-full"></span>
                    </button>
                    
                    <!-- Quick Actions -->
                    <div class="flex space-x-2">
                        <a href="{{ route('gsu.qr.scanner') }}" class="px-3 py-1 bg-red-800 text-white rounded-lg text-sm hover:bg-red-900 transition">
                            <i class="fas fa-qrcode mr-1"></i> Scan QR
                        </a>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        function openQRScanner() {
            window.location.href = "{{ route('gsu.qr.scanner') }}";
        }
    </script>
</body>

</html> 