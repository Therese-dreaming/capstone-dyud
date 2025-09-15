@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Assign Location to Asset</h4>
                </div>
                <div class="card-body">
                    <!-- Asset Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> Asset Information</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Asset Code:</strong> {{ $asset->asset_code }}<br>
                                        <strong>Name:</strong> {{ $asset->name }}<br>
                                        <strong>Category:</strong> {{ $asset->category->name ?? 'N/A' }}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Purchase Cost:</strong> ₱{{ number_format($asset->purchase_cost, 2) }}<br>
                                        <strong>Condition:</strong> {{ $asset->condition }}<br>
                                        <strong>Status:</strong> 
                                        <span class="badge {{ $asset->getApprovalStatusBadgeClass() }}">
                                            {{ $asset->getApprovalStatusLabel() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('gsu.assets.update-location', $asset) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="location_id" class="form-label">
                                <i class="fas fa-map-marker-alt"></i> Select Location <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('location_id') is-invalid @enderror" 
                                    id="location_id" name="location_id" required>
                                <option value="">Choose a location for deployment...</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                        {{ $location->building }} - Floor {{ $location->floor }} - Room {{ $location->room }}
                                        @if($location->description)
                                            ({{ $location->description }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('location_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-lightbulb"></i> 
                                This will be the asset's permanent location. Choose carefully as this affects inventory tracking.
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Important:</strong> Once you assign a location, the asset will be marked as "Available" and deployed in the system. 
                            Make sure the physical asset is actually placed at the selected location.
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('gsu.assets.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Assets
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-map-marker-alt"></i> Deploy Asset to Location
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
