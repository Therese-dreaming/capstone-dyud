@extends('layouts.gsu')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-red-50">
    <!-- GSU Welcome Banner -->
    <div class="bg-gradient-to-r from-red-800 to-red-900 text-white p-6 mb-6 rounded-xl shadow-lg relative overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-white/20 p-4 rounded-full">
                    <i class="fas fa-crown text-3xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold">Welcome, {{ Auth::user()->name }}! 👑</h1>
                    <p class="text-red-100 text-sm md:text-base">GSU Super Administrator Dashboard</p>
                    <p class="text-xs text-red-200 mt-1">Last login: {{ Auth::user()->last_login ? Auth::user()->last_login->diffForHumans() : 'Never' }}</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm text-red-200">System Status</div>
                <div class="text-lg font-bold text-green-300">Online</div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <!-- GSU Quick Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Assets -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-3 rounded-xl">
                        <i class="fas fa-boxes text-white text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($totalAssets ?? 0) }}</div>
                        <div class="text-sm text-gray-500">Total Assets</div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500">System-wide</span>
                    <a href="{{ route('gsu.assets.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        Manage <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Total Users -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-3 rounded-xl">
                        <i class="fas fa-users-cog text-white text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($totalUsers ?? 0) }}</div>
                        <div class="text-sm text-gray-500">System Users</div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500">All roles</span>
                    <a href="#" onclick="alert('User management is not available for GSU users')" class="text-gray-400 hover:text-gray-500 text-sm font-medium">
                        Not Available <i class="fas fa-lock ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Categories -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 p-3 rounded-xl">
                        <i class="fas fa-folder-open text-white text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($totalCategories ?? 0) }}</div>
                        <div class="text-sm text-gray-500">Categories</div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500">Asset types</span>
                    <a href="#" onclick="alert('Categories management is not available for GSU users')" class="text-gray-400 hover:text-gray-500 text-sm font-medium">
                        Not Available <i class="fas fa-lock ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Locations -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-r from-red-500 to-red-600 p-3 rounded-xl">
                        <i class="fas fa-map-marker-alt text-white text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($totalLocations ?? 0) }}</div>
                        <div class="text-sm text-gray-500">Locations</div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500">Asset locations</span>
                    <a href="#" onclick="alert('Locations management is not available for GSU users')" class="text-gray-400 hover:text-gray-500 text-sm font-medium">
                        Not Available <i class="fas fa-lock ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- GSU Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-red-50 to-red-100 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-crown text-red-600"></i>
                    GSU Quick Actions
                </h2>
                <p class="text-sm text-gray-600 mt-1">Super administrator controls</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Asset Management -->
                    <a href="{{ route('gsu.assets.create') }}" class="group">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 text-center hover:from-blue-100 hover:to-blue-200 transition-all duration-300 border border-blue-200 hover:border-blue-300">
                            <div class="bg-blue-500 p-3 rounded-full inline-block mb-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-plus-circle text-white text-xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Add Asset</h3>
                            <p class="text-sm text-gray-600">Register new assets</p>
                        </div>
                    </a>

                    <!-- QR Scanner -->
                    <a href="{{ route('gsu.qr.scanner') }}" class="group">
                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 text-center hover:from-green-100 hover:to-green-200 transition-all duration-300 border border-green-200 hover:border-green-300">
                            <div class="bg-green-500 p-3 rounded-full inline-block mb-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-qrcode text-white text-xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">QR Scanner</h3>
                            <p class="text-sm text-gray-600">Scan asset QR codes</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- System Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Activity -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-clock text-red-600"></i>
                        Recent System Activity
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg">
                            <div class="bg-blue-100 p-2 rounded-full">
                                <i class="fas fa-user-plus text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">New user registered</p>
                                <p class="text-xs text-gray-500">Admin user created by GSU</p>
                                <p class="text-xs text-gray-400 mt-1">2 hours ago</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg">
                            <div class="bg-green-100 p-2 rounded-full">
                                <i class="fas fa-box text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Asset added</p>
                                <p class="text-xs text-gray-500">New laptop registered in Electronics</p>
                                <p class="text-xs text-gray-400 mt-1">Yesterday</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg">
                            <div class="bg-yellow-100 p-2 rounded-full">
                                <i class="fas fa-tools text-yellow-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Maintenance scheduled</p>
                                <p class="text-xs text-gray-500">Projector maintenance in Conference Room</p>
                                <p class="text-xs text-gray-400 mt-1">3 days ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Health -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-heartbeat text-red-600"></i>
                        System Health
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="bg-green-100 p-2 rounded-full">
                                    <i class="fas fa-check-circle text-green-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Database</p>
                                    <p class="text-xs text-gray-500">All tables operational</p>
                                </div>
                            </div>
                            <span class="text-green-600 text-sm font-medium">Healthy</span>
                        </div>
                        
                        <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="bg-blue-100 p-2 rounded-full">
                                    <i class="fas fa-server text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Server</p>
                                    <p class="text-xs text-gray-500">Response time: 45ms</p>
                                </div>
                            </div>
                            <span class="text-blue-600 text-sm font-medium">Optimal</span>
                        </div>
                        
                        <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="bg-yellow-100 p-2 rounded-full">
                                    <i class="fas fa-shield-alt text-yellow-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Security</p>
                                    <p class="text-xs text-gray-500">2 failed login attempts</p>
                                </div>
                            </div>
                            <span class="text-yellow-600 text-sm font-medium">Warning</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openQRScanner() {
    alert('QR Scanner feature coming soon!');
}
</script>
@endsection 