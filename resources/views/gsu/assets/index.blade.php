@extends('layouts.gsu')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-red-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-red-800 to-red-900 text-white p-6 mb-6 rounded-xl shadow-lg relative overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-white/20 p-3 rounded-full">
                        <i class="fas fa-boxes text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold">Asset Deployment Center</h1>
                        <p class="text-red-100 text-sm md:text-base">Deploy approved assets to their designated locations</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-red-200">Assets Ready</div>
                    <div class="text-2xl font-bold text-white">{{ $assets->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-xl shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="bg-green-100 p-2 rounded-full">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold">Success!</h4>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-xl shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="bg-red-100 p-2 rounded-full">
                        <i class="fas fa-exclamation-circle text-red-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold">Error!</h4>
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div class="bg-blue-100 p-3 rounded-xl">
                        <i class="fas fa-clock text-blue-600 text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">{{ $assets->where('location_id', null)->count() }}</div>
                        <div class="text-sm text-gray-500">Pending Deployment</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div class="bg-green-100 p-3 rounded-xl">
                        <i class="fas fa-map-marker-alt text-green-600 text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">{{ $assets->where('location_id', '!=', null)->count() }}</div>
                        <div class="text-sm text-gray-500">Deployed</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div class="bg-purple-100 p-3 rounded-xl">
                        <i class="fas fa-boxes text-purple-600 text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">{{ $assets->count() }}</div>
                        <div class="text-sm text-gray-500">Total Assets</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div class="bg-yellow-100 p-3 rounded-xl">
                        <i class="fas fa-dollar-sign text-yellow-600 text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-gray-900">₱{{ number_format($assets->sum('purchase_cost'), 0) }}</div>
                        <div class="text-sm text-gray-500">Total Value</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assets Grid -->
        @if($assets->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                @foreach($assets as $asset)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-300">
                        <!-- Asset Header -->
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $asset->name }}</h3>
                                    <p class="text-sm text-gray-600">Code: <span class="font-mono font-medium">{{ $asset->asset_code }}</span></p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $asset->category->name ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Asset Details -->
                        <div class="p-6">
                            <div class="space-y-4">
                                <!-- Purchase Info -->
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Purchase Cost</span>
                                    <span class="text-lg font-semibold text-gray-900">₱{{ number_format($asset->purchase_cost, 2) }}</span>
                                </div>

                                <!-- Created By -->
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Created By</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $asset->createdBy->name ?? 'Unknown' }}</span>
                                </div>

                                <!-- Location Status -->
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Status</span>
                                    @if($asset->location_id)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                            Deployed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            Pending
                                        </span>
                                    @endif
                                </div>

                                <!-- Location Details -->
                                @if($asset->location_id)
                                    <div class="bg-green-50 rounded-lg p-3">
                                        <div class="text-sm text-green-800">
                                            <i class="fas fa-building mr-2"></i>
                                            <strong>{{ $asset->location->building }}</strong>
                                        </div>
                                        <div class="text-xs text-green-600 mt-1">
                                            Floor {{ $asset->location->floor }} • Room {{ $asset->location->room }}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-3 mt-6 pt-4 border-t border-gray-200">
                                <a href="{{ route('gsu.assets.show', $asset) }}" 
                                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg transition-colors duration-200 text-sm font-medium">
                                    <i class="fas fa-eye mr-2"></i>View Details
                                </a>
                                
                                @if(!$asset->location_id)
                                    <a href="{{ route('gsu.assets.assign-location', $asset) }}" 
                                       class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-2 px-4 rounded-lg transition-colors duration-200 text-sm font-medium">
                                        <i class="fas fa-map-marker-alt mr-2"></i>Deploy
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex justify-center mb-8">
                {{ $assets->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-box-open text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Assets Available</h3>
                <p class="text-gray-600 mb-4">There are currently no approved assets ready for deployment.</p>
                <p class="text-sm text-gray-500">Assets will appear here once they are approved by the Admin team.</p>
            </div>
        @endif

        <!-- Info Panel -->
        <div class="bg-blue-50 rounded-xl border border-blue-200 p-6">
            <div class="flex items-start gap-4">
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h4 class="text-lg font-semibold text-blue-900 mb-2">GSU Deployment Workflow</h4>
                    <div class="space-y-2 text-sm text-blue-800">
                        <p><strong>Your Role:</strong> Deploy approved assets by assigning them to specific locations within the university.</p>
                        <p><strong>Process:</strong> Review asset details → Assign location → Confirm deployment → Asset becomes available in the system.</p>
                        <p><strong>Important:</strong> Ensure the physical asset is placed at the assigned location before confirming deployment.</p>
                    </div>
                    <div class="mt-4 p-3 bg-blue-100 rounded-lg">
                        <div class="flex items-center gap-2 text-blue-700">
                            <i class="fas fa-lightbulb"></i>
                            <span class="font-medium">Pro Tip:</span>
                        </div>
                        <p class="text-sm text-blue-600 mt-1">Only approved assets without assigned locations are eligible for deployment.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
