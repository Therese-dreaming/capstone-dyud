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
                    <a href="{{ route('maintenance-requests.create') }}" class="text-gray-700 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('maintenance-requests.create') ? 'text-red-600 bg-red-50' : '' }}">
                        <i class="fas fa-tools mr-2"></i>Maintenance Request
                    </a>
                    <a href="{{ route('maintenance-requests.user-index') }}" class="text-gray-700 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('maintenance-requests.user-index') || request()->routeIs('maintenance-requests.user-show') ? 'text-red-600 bg-red-50' : '' }}">
                        <i class="fas fa-history mr-2"></i>My Requests
                    </a>
                    <a href="{{ route('notifications.index') }}" class="text-gray-700 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('notifications.*') ? 'text-red-600 bg-red-50' : '' }}">
                        <i class="fas fa-bell mr-2"></i>Notifications
                    </a>
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
                    
                    <!-- User Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center jugstify-center">
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
                            <a href="{{ route('maintenance-requests.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-tools mr-2"></i>Maintenance Request
                            </a>
                            <a href="{{ route('maintenance-requests.user-index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-history mr-2"></i>My Requests
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
                <a href="{{ route('maintenance-requests.create') }}" class="text-gray-700 hover:text-red-600 block px-3 py-2 rounded-md text-base font-medium transition-colors {{ request()->routeIs('maintenance-requests.create') ? 'text-red-600 bg-red-50' : '' }}">
                    <i class="fas fa-tools mr-2"></i>Maintenance Request
                </a>
                <a href="{{ route('maintenance-requests.user-index') }}" class="text-gray-700 hover:text-red-600 block px-3 py-2 rounded-md text-base font-medium transition-colors {{ request()->routeIs('maintenance-requests.user-index') || request()->routeIs('maintenance-requests.user-show') ? 'text-red-600 bg-red-50' : '' }}">
                    <i class="fas fa-history mr-2"></i>My Requests
                </a>
                <a href="{{ route('notifications.index') }}" class="text-gray-700 hover:text-red-600 block px-3 py-2 rounded-md text-base font-medium transition-colors {{ request()->routeIs('notifications.*') ? 'text-red-600 bg-red-50' : '' }}">
                    <i class="fas fa-bell mr-2"></i>Notifications
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
</body>
</html> 