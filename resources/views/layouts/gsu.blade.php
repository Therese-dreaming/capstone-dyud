<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GSU Dashboard - Inventory Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font Awesome Fallback CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
                        class="flex items-center px-4 py-2.5 text-gray-600 rounded-lg hover:bg-red-50 hover:text-red-800 focus:outline-none transition {{ request()->routeIs('dashboard') ? 'bg-red-50 text-red-800' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path
                                d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                        <span class="ml-3 text-sm">Dashboard</span>
                    </a>
                </div>
                
                <ul class="space-y-2">
                    <!-- Asset Deployment (GSU Priority) -->
                    <li>
                        <a href="{{ route('gsu.assets.index') }}"
                            class="flex items-center px-4 py-2.5 text-gray-600 rounded-lg hover:bg-green-50 hover:text-green-800 focus:outline-none transition {{ request()->routeIs('gsu.assets.*') ? 'bg-green-100 text-green-800' : '' }}">
                            <i class="fas fa-map-marker-alt w-5 text-green-600"></i>
                            <span class="ml-3 text-sm">Asset Deployment</span>
                            <span class="ml-auto bg-green-600 text-white text-xs px-2 py-1 rounded-full" id="deployment-count">
                                <!-- Will be populated by JavaScript -->
                            </span>
                        </a>
                    </li>
                    
                    <!-- Location Management (View Only) -->
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
                                <a href="{{ route('gsu.locations.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->routeIs('gsu.locations.*') ? 'bg-red-100 text-red-800' : 'text-gray-600' }}">
                                    <i class="fas fa-list mr-2 w-4"></i> Location List
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- Asset Scanner -->
                    <li>
                        <a href="{{ route('gsu.qr.scanner') }}"
                            class="flex items-center px-4 py-2.5 text-gray-600 rounded-lg hover:bg-red-50 hover:text-red-800 focus:outline-none transition {{ request()->routeIs('gsu.qr.scanner') ? 'bg-red-50 text-red-800' : '' }}">
                            <i class="fas fa-qrcode w-5"></i>
                            <span class="ml-3 text-sm">Asset Scanner</span>
                        </a>
                    </li>
                    
                    <!-- Maintenance -->
                    <li x-data="{ open: false }">
                        <button @click="open = !open" type="button"
                            class="flex items-center w-full px-4 py-2.5 text-gray-600 rounded-lg hover:bg-red-50 hover:text-red-800 focus:outline-none transition justify-between"
                            :class="{ 'bg-red-50 text-red-800': open }">
                            <span class="flex items-center">
                                <i class="fas fa-clipboard-check w-5"></i>
                                <span class="ml-3 text-sm">Maintenance</span>
                            </span>
                            <i class="fas fa-chevron-down ml-2 transition-transform" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <ul x-show="open" x-transition class="ml-8 mt-2 space-y-1" style="display: none;">
                            <li>
                                <a href="{{ route('maintenance-checklists.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->routeIs('maintenance-checklists.index') ? 'bg-red-100 text-red-800' : 'text-gray-600' }}">
                                    <i class="fas fa-list mr-2 w-4"></i> All Checklists
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('maintenance-checklists.index', ['status' => 'created']) }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->get('status') == 'created' ? 'bg-red-100 text-red-800' : 'text-gray-600' }}">
                                    <i class="fas fa-clock mr-2 w-4"></i> Pending Acknowledgment
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('maintenance-checklists.index', ['status' => 'acknowledged']) }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->get('status') == 'acknowledged' ? 'bg-red-100 text-red-800' : 'text-gray-600' }}">
                                    <i class="fas fa-play mr-2 w-4"></i> Ready to Start
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('maintenance-checklists.index', ['status' => 'in_progress']) }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->get('status') == 'in_progress' ? 'bg-red-100 text-red-800' : 'text-gray-600' }}">
                                    <i class="fas fa-qrcode mr-2 w-4"></i> In Progress
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('maintenance-checklists.index', ['status' => 'completed']) }}"
                                    class="flex items-center px-3 py-2 text-sm rounded hover:bg-red-100 hover:text-red-800 {{ request()->get('status') == 'completed' ? 'bg-red-100 text-red-800' : 'text-gray-600' }}">
                                    <i class="fas fa-check-circle mr-2 w-4"></i> Completed
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- Lost Assets -->
                    <li>
                        <a href="{{ route('gsu.lost-assets.index') }}"
                            class="flex items-center px-4 py-2.5 text-gray-600 rounded-lg hover:bg-red-50 hover:text-red-800 focus:outline-none transition {{ request()->routeIs('gsu.lost-assets.*') ? 'bg-red-50 text-red-800' : '' }}">
                            <i class="fas fa-search w-5"></i>
                            <span class="ml-3 text-sm">Lost Assets</span>
                        </a>
                    </li>
                    
                    <!-- Notifications -->
                    <li>
                        <a href="{{ route('notifications.index') }}"
                            class="flex items-center px-4 py-2.5 text-gray-600 rounded-lg hover:bg-red-50 hover:text-red-800 focus:outline-none transition {{ request()->routeIs('notifications.*') ? 'bg-red-50 text-red-800' : '' }}">
                            <i class="fas fa-bell w-5"></i>
                            <span class="ml-3 text-sm">Notifications</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Bottom Section - Sticky Logout -->
            <div class="p-4 border-t border-gray-100 flex-shrink-0 sticky bottom-0 bg-white">
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
        <div class="flex-1 flex flex-col lg:ml-0">
            <!-- Header -->
            <header class="bg-white shadow-sm h-16 flex items-center px-4 lg:px-6">
                <!-- Mobile menu button -->
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-red-500">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                
                <div class="flex-1 ml-2 lg:ml-0">
                    <h1 class="text-lg lg:text-xl font-semibold text-gray-800">GSU Inventory Management System</h1>
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
                                  class="absolute -top-1 -right-1 w-5 h-5 bg-red-600 text-white text-xs rounded-full flex items-center justify-center font-bold"></span>
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
                                        class="text-sm text-red-600 hover:text-red-800 font-medium">
                                    Mark all as read
                                </button>
                            </div>
                            
                            <!-- Notifications List -->
                            <div class="max-h-96 overflow-y-auto">
                                <template x-for="notification in notifications" :key="notification.id">
                                    <div @click="markAsRead(notification.id)" 
                                         class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors"
                                         :class="{ 'bg-red-50': !notification.is_read }">
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
                                                          class="w-2 h-2 bg-red-600 rounded-full"></span>
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
                                   class="block text-center text-sm text-red-600 hover:text-red-800 font-medium">
                                    View all notifications
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="flex space-x-2">
                        <a href="{{ route('gsu.qr.scanner') }}" class="px-3 py-1 bg-red-800 text-white rounded-lg text-sm hover:bg-red-900 transition">
                            <i class="fas fa-qrcode mr-1"></i> Scan QR
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

        // Load deployment count for GSU
        function loadDeploymentCount() {
            fetch('{{ route("gsu.assets.deployment-count") }}')
                .then(response => response.json())
                .then(data => {
                    const countElement = document.getElementById('deployment-count');
                    if (countElement) {
                        countElement.textContent = data.count;
                        countElement.style.display = data.count > 0 ? 'inline' : 'none';
                    }
                })
                .catch(error => console.error('Error loading deployment count:', error));
        }

        // Load deployment count on page load and refresh every 30 seconds
        document.addEventListener('DOMContentLoaded', function() {
            loadDeploymentCount();
            setInterval(loadDeploymentCount, 30000);
        });

        function openQRScanner() {
            window.location.href = "{{ route('gsu.qr.scanner') }}";
        }
    </script>
</body>

</html> 