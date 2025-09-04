@extends('layouts.superadmin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-red-50">
    <!-- Super Admin Welcome Banner -->
    <div class="bg-gradient-to-r from-red-800 to-red-900 text-white p-6 mb-6 rounded-xl shadow-lg relative overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-white/20 p-4 rounded-full">
                    <i class="fas fa-users-cog text-3xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold">Welcome, {{ Auth::user()->name }}! 👑</h1>
                    <p class="text-red-100 text-sm md:text-base">Super Administrator - User Management</p>
                    <p class="text-xs text-red-200 mt-1">Last login: {{ Auth::user()->last_login ? Auth::user()->last_login->diffForHumans() : 'Never' }}</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm text-red-200">Access Level</div>
                <div class="text-lg font-bold text-yellow-300">User Management Only</div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <!-- User Management Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-3 rounded-xl">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($totalUsers ?? 0) }}</div>
                        <div class="text-sm text-gray-500">Total Users</div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500">All roles</span>
                    <a href="{{ route('users.index') }}" class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                        Manage <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Super Admins -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-r from-red-500 to-red-600 p-3 rounded-xl">
                        <i class="fas fa-crown text-white text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">{{ $userCounts['superadmin'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Super Admins</div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500">User managers</span>
                    <span class="text-gray-400 text-sm">System access</span>
                </div>
            </div>

            <!-- GSU Users -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-3 rounded-xl">
                        <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">{{ $userCounts['gsu'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">GSU Users</div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500">Asset managers</span>
                    <span class="text-gray-400 text-sm">Limited access</span>
                </div>
            </div>

            <!-- Regular Users -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 p-3 rounded-xl">
                        <i class="fas fa-user text-white text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">{{ ($userCounts['admin'] ?? 0) + ($userCounts['user'] ?? 0) }}</div>
                        <div class="text-sm text-gray-500">Regular Users</div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500">Admins + Users</span>
                    <span class="text-gray-400 text-sm">Basic access</span>
                </div>
            </div>
        </div>

        <!-- User Management Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-users-cog text-purple-600"></i>
                    User Management Actions
                </h2>
                <p class="text-sm text-gray-600 mt-1">Manage system users and permissions</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Create User -->
                    <a href="{{ route('users.create') }}" class="group">
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 text-center hover:from-purple-100 hover:to-purple-200 transition-all duration-300 border border-purple-200 hover:border-purple-300">
                            <div class="bg-purple-500 p-3 rounded-full inline-block mb-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-user-plus text-white text-xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Create User</h3>
                            <p class="text-sm text-gray-600">Add new system users</p>
                        </div>
                    </a>

                    <!-- Manage Users -->
                    <a href="{{ route('users.index') }}" class="group">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 text-center hover:from-blue-100 hover:to-blue-200 transition-all duration-300 border border-blue-200 hover:border-blue-300">
                            <div class="bg-blue-500 p-3 rounded-full inline-block mb-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-users text-white text-xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Manage Users</h3>
                            <p class="text-sm text-gray-600">View and edit all users</p>
                        </div>
                    </a>

                    <!-- User Roles -->
                    <div class="group">
                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 text-center border border-green-200">
                            <div class="bg-green-500 p-3 rounded-full inline-block mb-4">
                                <i class="fas fa-user-shield text-white text-xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Role Management</h3>
                            <p class="text-sm text-gray-600">Assign and modify user roles</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Management Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Users -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-user-clock text-purple-600"></i>
                        Recent Users
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($recentUsers as $user)
                        <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg">
                            <div class="bg-{{ $user->role === 'superadmin' ? 'red' : ($user->role === 'gsu' ? 'blue' : ($user->role === 'admin' ? 'green' : 'gray')) }}-100 p-2 rounded-full">
                                <i class="fas fa-{{ $user->role === 'superadmin' ? 'crown' : ($user->role === 'gsu' ? 'shield-alt' : ($user->role === 'admin' ? 'user-shield' : 'user')) }} text-{{ $user->role === 'superadmin' ? 'red' : ($user->role === 'gsu' ? 'blue' : ($user->role === 'admin' ? 'green' : 'gray')) }}-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ ucfirst($user->role) }} • {{ $user->id_number }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                            </div>
                            <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:text-blue-700 text-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                        @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-users text-4xl mb-4"></i>
                            <p>No users found</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- User Role Summary -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-chart-pie text-purple-600"></i>
                        User Role Summary
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Super Admin -->
                        <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="bg-red-100 p-2 rounded-full">
                                    <i class="fas fa-crown text-red-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Super Admin</p>
                                    <p class="text-xs text-gray-500">User management only</p>
                                </div>
                            </div>
                            <span class="text-red-600 text-lg font-bold">{{ $userCounts['superadmin'] ?? 0 }}</span>
                        </div>
                        
                        <!-- GSU -->
                        <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="bg-blue-100 p-2 rounded-full">
                                    <i class="fas fa-shield-alt text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">GSU</p>
                                    <p class="text-xs text-gray-500">Asset management</p>
                                </div>
                            </div>
                            <span class="text-blue-600 text-lg font-bold">{{ $userCounts['gsu'] ?? 0 }}</span>
                        </div>
                        
                        <!-- Admin -->
                        <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="bg-green-100 p-2 rounded-full">
                                    <i class="fas fa-user-shield text-green-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Admin</p>
                                    <p class="text-xs text-gray-500">Asset operations</p>
                                </div>
                            </div>
                            <span class="text-green-600 text-lg font-bold">{{ $userCounts['admin'] ?? 0 }}</span>
                        </div>
                        
                        <!-- User -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="bg-gray-100 p-2 rounded-full">
                                    <i class="fas fa-user text-gray-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">User</p>
                                    <p class="text-xs text-gray-500">Basic access</p>
                                </div>
                            </div>
                            <span class="text-gray-600 text-lg font-bold">{{ $userCounts['user'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
