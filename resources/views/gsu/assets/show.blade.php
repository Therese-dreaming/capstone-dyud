@extends('layouts.gsu')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Asset Details</h4>
                    <div>
                        @if($asset->isApproved() && !$asset->location_id)
                            <a href="{{ route('gsu.assets.assign-location', $asset) }}" class="btn btn-success">
                                <i class="fas fa-map-marker-alt"></i> Assign Location
                            </a>
                        @endif
                        <a href="{{ route('gsu.assets.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Assets
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Asset Code:</th>
                                    <td><strong>{{ $asset->asset_code }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Name:</th>
                                    <td>{{ $asset->name }}</td>
                                </tr>
                                <tr>
                                    <th>Category:</th>
                                    <td>
                                        @if($asset->category)
                                            <span class="badge bg-info">{{ $asset->category->name }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Condition:</th>
                                    <td>{{ $asset->condition }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge {{ $asset->getStatusBadgeClass() }}">
                                            {{ $asset->status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Approval Status:</th>
                                    <td>
                                        <span class="badge {{ $asset->getApprovalStatusBadgeClass() }}">
                                            {{ $asset->getApprovalStatusLabel() }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Purchase Cost:</th>
                                    <td>₱{{ number_format($asset->purchase_cost, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Purchase Date:</th>
                                    <td>{{ $asset->purchase_date ? $asset->purchase_date->format('M d, Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Created By:</th>
                                    <td>{{ $asset->createdBy->name ?? 'Unknown' }}</td>
                                </tr>
                                <tr>
                                    <th>Created At:</th>
                                    <td>{{ $asset->created_at->format('M d, Y g:i A') }}</td>
                                </tr>
                                @if($asset->approved_at)
                                <tr>
                                    <th>Approved At:</th>
                                    <td>{{ $asset->approved_at->format('M d, Y g:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Approved By:</th>
                                    <td>{{ $asset->approvedBy->name ?? 'Unknown' }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if($asset->description)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Description:</h6>
                            <p class="text-muted">{{ $asset->description }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Location Information -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Location Information</h5>
                            @if($asset->location_id)
                                <div class="alert alert-success">
                                    <h6><i class="fas fa-map-marker-alt"></i> Current Location</h6>
                                    <p class="mb-0">
                                        <strong>{{ $asset->location->building }}</strong> - 
                                        Floor {{ $asset->location->floor }} - 
                                        Room {{ $asset->location->room }}
                                        @if($asset->location->description)
                                            <br><small class="text-muted">{{ $asset->location->description }}</small>
                                        @endif
                                    </p>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <h6><i class="fas fa-exclamation-triangle"></i> Location Not Assigned</h6>
                                    @if($asset->isApproved())
                                        <p class="mb-2">This asset is approved and ready for deployment. Please assign a location to complete the deployment process.</p>
                                        <a href="{{ route('gsu.assets.assign-location', $asset) }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-map-marker-alt"></i> Assign Location Now
                                        </a>
                                    @else
                                        <p class="mb-0">This asset cannot be deployed until it is approved by an administrator.</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($asset->rejection_reason)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-danger">
                                <h6><i class="fas fa-times-circle"></i> Rejection Reason</h6>
                                <p class="mb-0">{{ $asset->rejection_reason }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Workflow Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Deployment Workflow</h5>
                </div>
                <div class="card-body">
                    <div class="workflow-steps">
                        <!-- Step 1: Created by Purchasing -->
                        <div class="step completed">
                            <div class="step-icon">
                                <i class="fas fa-plus-circle text-success"></i>
                            </div>
                            <div class="step-content">
                                <h6>Asset Created</h6>
                                <small class="text-muted">Created by {{ $asset->createdBy->name ?? 'Purchasing' }}</small>
                            </div>
                        </div>

                        <!-- Step 2: Admin Approval -->
                        <div class="step {{ $asset->isApproved() ? 'completed' : ($asset->isRejected() ? 'rejected' : 'pending') }}">
                            <div class="step-icon">
                                @if($asset->isApproved())
                                    <i class="fas fa-check-circle text-success"></i>
                                @elseif($asset->isRejected())
                                    <i class="fas fa-times-circle text-danger"></i>
                                @else
                                    <i class="fas fa-clock text-warning"></i>
                                @endif
                            </div>
                            <div class="step-content">
                                <h6>Admin Approval</h6>
                                <small class="text-muted">
                                    @if($asset->isApproved())
                                        Approved by {{ $asset->approvedBy->name ?? 'Admin' }}
                                    @elseif($asset->isRejected())
                                        Rejected by {{ $asset->approvedBy->name ?? 'Admin' }}
                                    @else
                                        Pending approval
                                    @endif
                                </small>
                            </div>
                        </div>

                        <!-- Step 3: GSU Deployment -->
                        <div class="step {{ $asset->location_id ? 'completed' : ($asset->isApproved() ? 'active' : 'pending') }}">
                            <div class="step-icon">
                                @if($asset->location_id)
                                    <i class="fas fa-map-marker-alt text-success"></i>
                                @elseif($asset->isApproved())
                                    <i class="fas fa-map-marker-alt text-primary"></i>
                                @else
                                    <i class="fas fa-map-marker-alt text-muted"></i>
                                @endif
                            </div>
                            <div class="step-content">
                                <h6>Location Assignment</h6>
                                <small class="text-muted">
                                    @if($asset->location_id)
                                        Deployed to {{ $asset->location->building }}
                                    @elseif($asset->isApproved())
                                        Ready for deployment
                                    @else
                                        Waiting for approval
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            @if($asset->isApproved() && !$asset->location_id)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('gsu.assets.assign-location', $asset) }}" class="btn btn-success">
                            <i class="fas fa-map-marker-alt"></i> Assign Location
                        </a>
                    </div>
                    <hr>
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        This asset is approved and ready for deployment. Assign a location to complete the process.
                    </small>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.workflow-steps .step {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.workflow-steps .step:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.workflow-steps .step-icon {
    margin-right: 0.75rem;
    margin-top: 0.25rem;
}

.workflow-steps .step-content h6 {
    margin-bottom: 0.25rem;
    font-weight: 600;
}

.workflow-steps .step.completed .step-content h6 {
    color: #28a745;
}

.workflow-steps .step.active .step-content h6 {
    color: #007bff;
}

.workflow-steps .step.rejected .step-content h6 {
    color: #dc3545;
}

.workflow-steps .step.pending .step-content h6 {
    color: #6c757d;
}
</style>
@endsection
