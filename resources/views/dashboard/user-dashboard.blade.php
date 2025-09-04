@extends('layouts.user')

@section('title', 'Dashboard - Asset Management System')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-red-50">
    <!-- Page Header -->
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 flex items-center gap-3">
                <div class="bg-gradient-to-r from-red-600 to-red-800 text-white p-3 rounded-xl shadow-lg">
                    <i class="fas fa-tachometer-alt text-xl"></i>
                </div>
                Welcome, {{ auth()->user()->name }}!
            </h1>
            <p class="text-gray-600 mt-2 text-sm md:text-base">View your assigned assets and recent activity</p>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                

                            <a href="{{ route('user.borrowings.index') }}" class="bg-white rounded-xl shadow-md border border-gray-200 p-6 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-list text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">My Borrowings</h3>
                        <p class="text-gray-600 text-sm">View your borrowing history</p>
                    </div>
                </div>
            </a>

                            <a href="{{ route('user.borrowings.index', ['status' => 'approved']) }}" class="bg-white rounded-xl shadow-md border border-gray-200 p-6 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Current Items</h3>
                        <p class="text-gray-600 text-sm">Items you currently have</p>
                    </div>
                </div>
            </a>

                            <a href="{{ route('user.borrowings.index', ['status' => 'overdue']) }}" class="bg-white rounded-xl shadow-md border border-gray-200 p-6 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="bg-red-100 rounded-full p-3 mr-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Overdue Items</h3>
                        <p class="text-gray-600 text-sm">Items past due date</p>
                    </div>
                </div>
            </a>
        </div>



        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Recent Activity</h2>
            </div>
            <div class="p-6 text-gray-600">
                <p>No recent activity to show.</p>
            </div>
        </div>
    </div>
</div>
@endsection 