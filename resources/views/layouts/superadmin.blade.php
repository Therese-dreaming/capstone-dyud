<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font Awesome Fallback CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-gray-50 min-h-screen font-['Inter']">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-xl flex flex-col transition-all duration-300" x-data="{ expanded: true }">
            <!-- Profile Section -->
            <a href="#" class="block p-4 border-b border-gray-100 hover:bg-red-50 transition" title="Profile Settings">
                <div class="flex items-center space-x-3 cursor-pointer">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Superadmin') }}&background=8B0000&color=fff"
                        alt="Profile" class="w-10 h-10 rounded-lg">
                    <div class="flex-1">
                        <h2 class="font-semibold text-gray-800">{{ Auth::user()->name ?? 'Superadmin' }}</h2>
                        <p class="text-sm text-gray-500 capitalize">{{ Auth::user()->role ?? 'superadmin' }}</p>
                    </div>
                </div>
            </a>

            <!-- Search Bar -->
            <div class="p-4">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" placeholder="Search..."
                        class="w-full pl-10 pr-4 py-2 bg-gray-50 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-800/20">
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-4">
                <div class="py-4 overflow-y-auto">
                    <!-- Update the dashboard link in the navigation section -->
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center px-4 py-3 text-gray-700 hover:bg-pink-50 rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-red-100 text-red-800' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path
                                d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                        <span class="ml-3">Dashboard</span>
                    </a>
                </div>
                <ul class="space-y-2">
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
                                <a href="{{ route('assets.create') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->routeIs('assets.create') ? 'bg-red-100 text-red-800' : 'text-gray-600' }}">
                                    <i class="fas fa-plus mr-2 w-4"></i> Create Asset
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('assets.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->routeIs('assets.index') ? 'bg-red-100 text-red-800' : 'text-gray-600' }}">
                                    <i class="fas fa-list mr-2 w-4"></i> Asset List
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li x-data="{ open: false }">
                        <button @click="open = !open" type="button"
                            class="flex items-center w-full px-4 py-2.5 text-gray-600 rounded-lg hover:bg-red-50 hover:text-red-800 focus:outline-none transition justify-between"
                            :class="{ 'bg-red-50 text-red-800': open }">
                            <span class="flex items-center">
                                <i class="fas fa-handshake w-5"></i>
                                <span class="ml-3 text-sm">Borrowings</span>
                            </span>
                            <i class="fas fa-chevron-down ml-2 transition-transform" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <ul x-show="open" x-transition class="ml-8 mt-2 space-y-1" style="display: none;">
                            <li>
                                <a href="{{ route('borrowings.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->routeIs('borrowings.index') ? 'bg-red-100 text-red-800' : 'text-gray-600' }}">
                                    <i class="fas fa-list mr-2 w-4"></i> All Requests
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('borrowings.ongoing') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->routeIs('borrowings.ongoing') ? 'bg-red-100 text-red-800' : 'text-gray-600' }}">
                                    <i class="fas fa-clock mr-2 w-4"></i> Ongoing Borrowings
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('borrowings.index', ['status' => 'pending']) }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->get('status') === 'pending' ? 'bg-red-100 text-red-800' : 'text-gray-600' }}">
                                    <i class="fas fa-clock mr-2 w-4"></i> Pending Approval
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('borrowings.index', ['status' => 'approved']) }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->get('status') === 'approved' ? 'bg-red-100 text-red-800' : 'text-gray-800' }}">
                                    <i class="fas fa-check mr-2 w-4"></i> Approved
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('borrowings.index', ['status' => 'overdue']) }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->get('status') === 'overdue' ? 'bg-red-100 text-red-800' : 'text-gray-600' }}">
                                    <i class="fas fa-exclamation-triangle mr-2 w-4"></i> Overdue
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li x-data="{ open: false }">
                        <button @click="open = !open" type="button"
                            class="flex items-center w-full px-4 py-2.5 text-gray-600 rounded-lg hover:bg-red-50 hover:text-red-800 focus:outline-none transition justify-between"
                            :class="{ 'bg-red-50 text-red-800': open }">
                            <span class="flex items-center">
                                <i class="fas fa-chart-bar w-5"></i>
                                <span class="ml-3 text-sm">Reports</span>
                            </span>
                            <i class="fas fa-chevron-down ml-2 transition-transform" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <ul x-show="open" x-transition class="ml-8 mt-2 space-y-1" style="display: none;">
                            <li>
                                <a href="{{ route('assets.report') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->routeIs('assets.report') ? 'bg-red-100 text-red-800' : 'text-gray-600' }}">
                                    <i class="fas fa-file-alt mr-2 w-4"></i> Asset Report
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('disposals.history') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->routeIs('disposals.history') ? 'bg-red-100 text-red-800' : 'text-gray-600' }}">
                                    <i class="fas fa-trash-restore mr-2 w-4"></i> Disposal History
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('maintenances.history') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->routeIs('maintenances.history') ? 'bg-red-100 text-red-800' : 'text-gray-600' }}">
                                    <i class="fas fa-history mr-2 w-4"></i> Maintenance History
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('lost-assets.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->routeIs('lost-assets.*') ? 'bg-red-100 text-red-800' : 'text-gray-600' }}">
                                    <i class="fas fa-search mr-2 w-4"></i> Lost Assets
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li x-data="{ open: false }">
                        <button @click="open = !open" type="button"
                            class="flex items-center w-full px-4 py-2.5 text-gray-600 rounded-lg hover:bg-red-50 hover:text-red-800 focus:outline-none transition justify-between"
                            :class="{ 'bg-red-50 text-red-800': open }">
                            <span class="flex items-center">
                                <i class="fas fa-users w-5"></i>
                                <span class="ml-3 text-sm">User Management</span>
                            </span>
                            <i class="fas fa-chevron-down ml-2 transition-transform" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <ul x-show="open" x-transition class="ml-8 mt-2 space-y-1" style="display: none;">
                            <li>
                                <a href="{{ route('users.create') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->routeIs('users.create') ? 'bg-red-100 text-red-800' : 'text-gray-600' }}">
                                    <i class="fas fa-user-plus mr-2 w-4"></i> Create User
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('users.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->routeIs('users.index') ? 'bg-red-100 text-red-800' : 'text-gray-600' }}">
                                    <i class="fas fa-list mr-2 w-4"></i> User List
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li x-data="{ open: false }">
                        <button @click="open = !open" type="button"
                            class="flex items-center w-full px-4 py-2.5 text-gray-600 rounded-lg hover:bg-red-50 hover:text-red-800 focus:outline-none transition justify-between"
                            :class="{ 'bg-red-50 text-red-800': open }">
                            <span class="flex items-center">
                                <i class="fas fa-folder-open w-5"></i>
                                <span class="ml-3 text-sm">Categories</span>
                            </span>
                            <i class="fas fa-chevron-down ml-2 transition-transform" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <ul x-show="open" x-transition class="ml-8 mt-2 space-y-1" style="display: none;">
                            <li>
                                <a href="{{ route('categories.create') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->routeIs('categories.create') ? 'bg-red-100 text-red-800' : 'text-gray-600' }}">
                                    <i class="fas fa-folder-plus mr-2 w-4"></i> Add Category
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('categories.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->routeIs('categories.index') ? 'bg-red-100 text-red-800' : 'text-gray-600' }}">
                                    <i class="fas fa-list mr-2 w-4"></i> Category List
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li x-data="{ open: false }">
                        <button @click="open = !open" type="button"
                            class="flex items-center w-full px-4 py-2.5 text-gray-600 rounded-lg hover:bg-red-50 hover:text-red-800 focus:outline-none transition justify-between"
                            :class="{ 'bg-red-50 text-red-800': open }">
                            <span class="flex items-center">
                                <i class="fas fa-map-marker-alt w-5"></i>
                                <span class="ml-3 text-sm">Locations</span>
                            </span>
                            <i class="fas fa-chevron-down ml-2 transition-transform" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <ul x-show="open" x-transition class="ml-8 mt-2 space-y-1" style="display: none;">
                            <li>
                                <a href="{{ route('locations.create') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->routeIs('locations.create') ? 'bg-red-100 text-red-800' : 'text-gray-600' }}">
                                    <i class="fas fa-plus-circle mr-2 w-4"></i> Add Location
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('locations.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->routeIs('locations.index') ? 'bg-red-100 text-red-800' : 'text-gray-600' }}">
                                    <i class="fas fa-list mr-2 w-4"></i> Location List
                                </a>
                            </li>
                        </ul>
                    </li>

                    

                </ul>
            </nav>

            <!-- Bottom Section -->
            <div class="p-4 border-t border-gray-100">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="flex items-center w-full px-4 py-2.5 text-gray-600 rounded-lg hover:bg-red-50 hover:text-red-800">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span class="ml-3" x-show="expanded">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Rest of your content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow-sm h-16 flex items-center px-6">
                <div class="flex-1"></div>
                <div class="flex items-center space-x-4">
                    <button class="relative p-2 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-bell text-gray-600"></i>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-600 rounded-full"></span>
                    </button>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
</body>

</html>