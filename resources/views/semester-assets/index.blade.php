@extends('layouts.admin')

@section('title', 'Semester Asset Tracking - Asset Management System')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-blue-50" x-data="semesterAssetData()">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-4 md:p-6 mb-6 rounded-xl shadow-lg relative overflow-hidden mx-4 mt-4">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative z-10">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold flex items-center gap-3">
                        <i class="fas fa-calendar-alt text-2xl"></i>
                        Semester Asset Tracking
                    </h1>
                    <p class="text-blue-100 text-sm md:text-base mt-2">
                        Track asset movements and status changes by academic semester
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                    <div class="bg-white/20 rounded-lg px-4 py-2">
                        <div class="text-xs text-blue-200">Current Period</div>
                        <div class="text-sm font-medium">
                            @if($selectedSemester)
                                {{ $selectedSemester->academic_year }} - {{ $selectedSemester->name }}
                            @else
                                No Semester Selected
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <!-- Semester Selection -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-filter text-blue-600"></i>
                Select Academic Period
            </h3>
            
            <form method="GET" action="{{ route('semester-assets.index') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Semester Selection -->
                <div>
                    <label for="semester_id" class="block text-sm font-medium text-gray-700 mb-2">Select Semester</label>
                    <select name="semester_id" id="semester_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Select a Semester --</option>
                        @foreach($availableSemesters as $semester)
                            <option value="{{ $semester->id }}" {{ $selectedSemester && $selectedSemester->id == $semester->id ? 'selected' : '' }}>
                                {{ $semester->academic_year }} - {{ $semester->name }}
                                ({{ $semester->start_date->format('M d') }} - {{ $semester->end_date->format('M d, Y') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-search"></i>
                        View Semester
                    </button>
                </div>
            </form>
        </div>

        <!-- Summary Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Registered -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Assets Registered</p>
                            <p class="text-3xl font-bold text-green-600">{{ number_format($assetStats['summary']['total_registered']) }}</p>
                            <p class="text-sm text-gray-500 mt-1">New additions</p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <i class="fas fa-plus-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-green-500 to-green-600"></div>
            </div>

            <!-- Total Transferred -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Assets Transferred</p>
                            <p class="text-3xl font-bold text-blue-600">{{ number_format($assetStats['summary']['total_transferred']) }}</p>
                            <p class="text-sm text-gray-500 mt-1">Location changes</p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i class="fas fa-exchange-alt text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-blue-500 to-blue-600"></div>
            </div>

            <!-- Total Disposed -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Assets Disposed</p>
                            <p class="text-3xl font-bold text-orange-600">{{ number_format($assetStats['summary']['total_disposed']) }}</p>
                            <p class="text-sm text-gray-500 mt-1">Removed from service</p>
                        </div>
                        <div class="bg-orange-100 p-3 rounded-full">
                            <i class="fas fa-trash-alt text-orange-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-orange-500 to-orange-600"></div>
            </div>

            <!-- Total Lost -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Assets Lost</p>
                            <p class="text-3xl font-bold text-red-600">{{ number_format($assetStats['summary']['total_lost']) }}</p>
                            <p class="text-sm text-gray-500 mt-1">Missing assets</p>
                        </div>
                        <div class="bg-red-100 p-3 rounded-full">
                            <i class="fas fa-question-circle text-red-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-red-500 to-red-600"></div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Monthly Activity Chart -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-chart-line text-blue-600"></i>
                        Monthly Asset Activity
                    </h3>
                </div>
                <div class="p-6">
                    <div class="relative">
                        <canvas id="monthlyActivityChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Category Distribution Chart -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-chart-pie text-purple-600"></i>
                        Activity by Category
                    </h3>
                </div>
                <div class="p-6">
                    <div class="relative">
                        <canvas id="categoryActivityChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Category Breakdown -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-table text-green-600"></i>
                    Asset Activity by Category
                </h3>
                <div class="flex items-center space-x-3">
                    <button @click="exportReport()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                        <i class="fas fa-download"></i>
                        Export Report
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-plus-circle text-green-600 mr-1"></i>Registered
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-exchange-alt text-blue-600 mr-1"></i>Transferred
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-trash-alt text-orange-600 mr-1"></i>Disposed
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-question-circle text-red-600 mr-1"></i>Lost
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Activity</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($assetStats['by_category'] as $category)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                        <i class="fas fa-folder text-blue-600"></i>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">{{ $category['name'] }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ number_format($category['registered']) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ number_format($category['transferred']) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    {{ number_format($category['disposed']) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ number_format($category['lost']) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-bold text-gray-900">{{ number_format($category['total']) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="bg-gray-100 p-4 rounded-full mb-4">
                                        <i class="fas fa-calendar-times text-gray-400 text-2xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Activity Found</h3>
                                    <p class="text-gray-500 text-sm">No asset activity recorded for this semester period.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function semesterAssetData() {
    return {
        monthlyActivityChart: null,
        categoryActivityChart: null,
        
        init() {
            this.$nextTick(() => {
                this.initCharts();
            });
        },
        
        initCharts() {
            // Monthly Activity Chart
            const monthlyCtx = document.getElementById('monthlyActivityChart');
            if (monthlyCtx) {
                const monthlyData = @json($assetStats['monthly_breakdown']);
                
                this.monthlyActivityChart = new Chart(monthlyCtx, {
                    type: 'line',
                    data: {
                        labels: monthlyData.map(item => item.month),
                        datasets: [
                            {
                                label: 'Registered',
                                data: monthlyData.map(item => item.registered),
                                borderColor: '#10B981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'Transferred',
                                data: monthlyData.map(item => item.transferred),
                                borderColor: '#3B82F6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'Disposed',
                                data: monthlyData.map(item => item.disposed),
                                borderColor: '#F59E0B',
                                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'Lost',
                                data: monthlyData.map(item => item.lost),
                                borderColor: '#EF4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                fill: true,
                                tension: 0.4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });
            }

            // Category Activity Chart
            const categoryCtx = document.getElementById('categoryActivityChart');
            if (categoryCtx) {
                const categoryData = @json($assetStats['by_category']);
                
                this.categoryActivityChart = new Chart(categoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: categoryData.map(item => item.name),
                        datasets: [{
                            data: categoryData.map(item => item.total),
                            backgroundColor: [
                                '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
                                '#EC4899', '#14B8A6', '#F97316', '#84CC16', '#6366F1'
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    usePointStyle: true,
                                    font: { size: 11 }
                                }
                            }
                        }
                    }
                });
            }
        },
        
        exportReport() {
            @if($selectedSemester)
            const semesterId = '{{ $selectedSemester->id }}';
            window.location.href = `{{ route('semester-assets.export') }}?semester_id=${semesterId}`;
            @else
            alert('Please select a semester first.');
            @endif
        }
    }
}
</script>
@endsection
