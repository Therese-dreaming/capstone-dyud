@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Approved Assets for Deployment</h4>
                    <small class="text-muted">Assign locations to approved assets from Purchasing</small>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Asset Code</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Purchase Cost</th>
                                    <th>Created By</th>
                                    <th>Location Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assets as $asset)
                                    <tr>
                                        <td>
                                            <strong>{{ $asset->asset_code }}</strong>
                                        </td>
                                        <td>{{ $asset->name }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $asset->category->name ?? 'N/A' }}</span>
                                        </td>
                                        <td>₱{{ number_format($asset->purchase_cost, 2) }}</td>
                                        <td>{{ $asset->createdBy->name ?? 'Unknown' }}</td>
                                        <td>
                                            @if($asset->location_id)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    {{ $asset->location->building }} - Floor {{ $asset->location->floor }} - Room {{ $asset->location->room }}
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock"></i>
                                                    Awaiting Location Assignment
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('gsu.assets.show', $asset) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if(!$asset->location_id)
                                                    <a href="{{ route('gsu.assets.assign-location', $asset) }}" 
                                                       class="btn btn-sm btn-success" title="Assign Location">
                                                        <i class="fas fa-map-marker-alt"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-box-open fa-3x mb-3"></i>
                                                <p>No approved assets available for deployment.</p>
                                                <small>Assets will appear here once approved by Admin.</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $assets->links() }}
                    </div>

                    <!-- Info Box -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> GSU Workflow</h6>
                                <p class="mb-0">
                                    <strong>Your Role:</strong> Assign locations to approved assets from Purchasing. 
                                    Once you assign a location, the asset becomes "Available" and is deployed in the system.
                                </p>
                                <hr>
                                <small>
                                    <i class="fas fa-lightbulb"></i> 
                                    <strong>Tip:</strong> Only approved assets without locations are eligible for deployment.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
