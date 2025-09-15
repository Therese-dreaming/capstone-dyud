<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Purchasing Dashboard') - Asset Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font Awesome Fallback CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @yield('styles')
</head>

<body class="bg-gray-50 min-h-screen font-['Inter']">
    <div class="flex min-h-screen" x-data="{ sidebarOpen: false }">
        <!-- Mobile sidebar overlay -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
             @click="sidebarOpen = false"
             style="display: none;">
        </div>

        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-xl flex flex-col transition-all duration-300 transform lg:translate-x-0 lg:static lg:inset-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               x-data="{ expanded: true }">
            <!-- Profile Section -->
            <a href="#" class="block p-4 border-b border-gray-100 hover:bg-purple-50 transition" title="Profile Settings">
                <div class="flex items-center space-x-3 cursor-pointer">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Purchasing') }}&background=7C3AED&color=fff"
                        alt="Profile" class="w-10 h-10 rounded-lg">
                    <div class="flex-1">
                        <h2 class="font-semibold text-gray-800">{{ Auth::user()->name ?? 'Purchasing' }}</h2>
                        <p class="text-sm text-gray-500 capitalize">{{ Auth::user()->role ?? 'purchasing' }}</p>
                    </div>
                </div>
            </a>

            <!-- Search Bar -->
            <div class="p-4">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" placeholder="Search assets..."
                        class="w-full pl-10 pr-4 py-2 bg-gray-50 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-800/20">
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-4">
                <div class="py-4 overflow-y-auto">
                    <!-- Dashboard link -->
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center px-4 py-3 text-gray-700 hover:bg-purple-50 rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-purple-100 text-purple-800' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path
                                d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                        <span class="ml-3">Dashboard</span>
                    </a>
                </div>
                <ul class="space-y-2">

                    <!-- Asset Registration (Purchasing Priority) -->
                    <li>
                        <a href="{{ route('purchasing.assets.create') }}"
                            class="flex items-center px-4 py-2.5 text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-800 focus:outline-none transition {{ request()->routeIs('purchasing.assets.create') ? 'bg-purple-100 text-purple-800' : '' }}">
                            <i class="fas fa-plus-circle w-5 text-purple-600"></i>
                            <span class="ml-3 text-sm">Register New Asset</span>
                        </a>
                    </li>

                    <!-- My Assets -->
                    <li>
                        <a href="{{ route('purchasing.assets.index') }}"
                            class="flex items-center px-4 py-2.5 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-800 focus:outline-none transition {{ request()->routeIs('purchasing.assets.index') || request()->routeIs('purchasing.assets.show') ? 'bg-blue-100 text-blue-800' : '' }}">
                            <i class="fas fa-box w-5"></i>
                            <span class="ml-3 text-sm">My Assets</span>
                        </a>
                    </li>

                    <!-- Asset Status Tracking -->
                    <li x-data="{ open: false }">
                        <button @click="open = !open" type="button"
                            class="flex items-center w-full px-4 py-2.5 text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-800 focus:outline-none transition justify-between"
                            :class="{ 'bg-purple-50 text-purple-800': open }">
                            <span class="flex items-center">
                                <i class="fas fa-chart-line w-5"></i>
                                <span class="ml-3 text-sm">Asset Status</span>
                            </span>
                            <i class="fas fa-chevron-down ml-2 transition-transform" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <ul x-show="open" x-transition class="ml-8 mt-2 space-y-1" style="display: none;">
                            <li>
                                <a href="{{ route('purchasing.assets.index', ['status' => 'pending']) }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-orange-100 hover:text-orange-800 {{ request()->get('status') == 'pending' ? 'bg-orange-100 text-orange-800' : 'text-gray-600' }}">
                                    <i class="fas fa-clock mr-2 w-4"></i> Pending Approval
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('purchasing.assets.index', ['status' => 'approved']) }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-green-100 hover:text-green-800 {{ request()->get('status') == 'approved' ? 'bg-green-100 text-green-800' : 'text-gray-600' }}">
                                    <i class="fas fa-check-circle mr-2 w-4"></i> Approved Assets
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('purchasing.assets.index', ['status' => 'rejected']) }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->get('status') == 'rejected' ? 'bg-red-100 text-red-800' : 'text-gray-600' }}">
                                    <i class="fas fa-times-circle mr-2 w-4"></i> Rejected Assets
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Notifications -->
                    <li>
                        <a href="{{ route('notifications.index') }}"
                            class="flex items-center px-4 py-2.5 text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-800 focus:outline-none transition {{ request()->routeIs('notifications.*') ? 'bg-purple-100 text-purple-800' : '' }}">
                            <i class="fas fa-bell w-5"></i>
                            <span class="ml-3 text-sm">Notifications</span>
                        </a>
                    </li>

                </ul>
            </nav>

            <!-- Bottom Section -->
            <div class="p-4 border-t border-gray-100">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="flex items-center w-full px-4 py-2.5 text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-800">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span class="ml-3" x-show="expanded">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col lg:ml-0">
            <!-- Header -->
            <header class="bg-white shadow-sm h-16 flex items-center px-4 lg:px-6">
                <!-- Mobile menu button -->
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-purple-500">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                
                <div class="flex-1 ml-2 lg:ml-0">
                    <h1 class="text-lg lg:text-xl font-semibold text-gray-800">Purchasing Dashboard</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Notifications Dropdown -->
                    <div class="relative" x-data="{ open: false, notifications: [], unreadCount: 0 }" 
                         x-init="
                            loadNotifications();
                            setInterval(() => {
                                loadUnreadCount();
                            }, 30000);
                         ">
                        <button @click="open = !open; if(open) loadNotifications()" 
                                class="relative p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="fas fa-bell text-gray-600"></i>
                            <span x-show="unreadCount > 0" 
                                  x-text="unreadCount > 9 ? '9+' : unreadCount"
                                  class="absolute -top-1 -right-1 w-5 h-5 bg-purple-600 text-white text-xs rounded-full flex items-center justify-center font-bold"></span>
                        </button>
                        
                        <!-- Notifications Dropdown Panel -->
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                             style="display: none;">
                            
                            <!-- Header -->
                            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                                <button @click="markAllAsRead()" 
                                        class="text-sm text-purple-600 hover:text-purple-800 font-medium">
                                    Mark all as read
                                </button>
                            </div>
                            
                            <!-- Notifications List -->
                            <div class="max-h-96 overflow-y-auto">
                                <template x-for="notification in notifications" :key="notification.id">
                                    <div @click="markAsRead(notification.id)" 
                                         class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors"
                                         :class="{ 'bg-purple-50': !notification.is_read }">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0">
                                                <i :class="notification.icon" 
                                                   :class="notification.color"
                                                   class="text-lg"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900" x-text="notification.title"></p>
                                                <p class="text-sm text-gray-600 mt-1" x-text="notification.message"></p>
                                                <div class="flex items-center justify-between mt-2">
                                                    <p class="text-xs text-gray-500" x-text="notification.created_at"></p>
                                                    <span x-show="!notification.is_read" 
                                                          class="w-2 h-2 bg-purple-600 rounded-full"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                
                                <!-- Empty State -->
                                <div x-show="notifications.length === 0" 
                                     class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-bell-slash text-3xl mb-2"></i>
                                    <p>No notifications yet</p>
                                </div>
                            </div>
                            
                            <!-- Footer -->
                            <div class="px-4 py-3 border-t border-gray-200">
                                <a href="{{ route('notifications.index') }}" 
                                   class="block text-center text-sm text-purple-600 hover:text-purple-800 font-medium">
                                    View all notifications
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="flex space-x-2">
                        <a href="{{ route('purchasing.assets.create') }}" class="px-3 py-1 bg-purple-800 text-white rounded-lg text-sm hover:bg-purple-900 transition">
                            <i class="fas fa-plus mr-1"></i> New Asset
                        </a>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 p-4 lg:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
    
    <script>
        // Notification functions
        function loadNotifications() {
            fetch('{{ route("notifications.recent") }}')
                .then(response => response.json())
                .then(data => {
                    this.notifications = data.notifications;
                })
                .catch(error => console.error('Error loading notifications:', error));
        }

        function loadUnreadCount() {
            fetch('{{ route("notifications.unread-count") }}')
                .then(response => response.json())
                .then(data => {
                    this.unreadCount = data.count;
                })
                .catch(error => console.error('Error loading unread count:', error));
        }

        function markAsRead(notificationId) {
            fetch('{{ route("notifications.mark-read") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ notification_id: notificationId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the notification in the list
                    const notification = this.notifications.find(n => n.id === notificationId);
                    if (notification) {
                        notification.is_read = true;
                    }
                    // Reload unread count
                    loadUnreadCount();
                }
            })
            .catch(error => console.error('Error marking notification as read:', error));
        }

        function markAllAsRead() {
            fetch('{{ route("notifications.mark-all-read") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mark all notifications as read in the UI
                    this.notifications.forEach(notification => {
                        notification.is_read = true;
                    });
                    this.unreadCount = 0;
                }
            })
            .catch(error => console.error('Error marking all notifications as read:', error));
        }

        // Make functions globally available
        window.loadNotifications = loadNotifications;
        window.loadUnreadCount = loadUnreadCount;
        window.markAsRead = markAsRead;
        window.markAllAsRead = markAllAsRead;
    </script>
    
    @yield('scripts')
</body>

</html>
