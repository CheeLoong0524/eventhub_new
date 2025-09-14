@extends('layouts.admin')

{{-- Author  : Choong Yoong Sheng (Vendor module) --}}

@section('title', 'Approved Vendors')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Approved Vendors</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.vendor.applications') }}" class="btn btn-outline-warning">
            <i class="fas fa-store"></i> Applications
        </a>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.vendor.vendors') }}" class="row g-3">
            <div class="col-md-3">
                <label for="business_type" class="form-label">Business Type</label>
                <select name="business_type" id="business_type" class="form-select">
                    <option value="">All Types</option>
                    <option value="restaurant" {{ request('business_type') == 'restaurant' ? 'selected' : '' }}>Restaurant</option>
                    <option value="catering" {{ request('business_type') == 'catering' ? 'selected' : '' }}>Catering</option>
                    <option value="entertainment" {{ request('business_type') == 'entertainment' ? 'selected' : '' }}>Entertainment</option>
                    <option value="decoration" {{ request('business_type') == 'decoration' ? 'selected' : '' }}>Decoration</option>
                    <option value="photography" {{ request('business_type') == 'photography' ? 'selected' : '' }}>Photography</option>
                    <option value="other" {{ request('business_type') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="is_verified" class="form-label">Verification Status</label>
                <select name="is_verified" id="is_verified" class="form-select">
                    <option value="">All</option>
                    <option value="1" {{ request('is_verified') == '1' ? 'selected' : '' }}>Verified</option>
                    <option value="0" {{ request('is_verified') == '0' ? 'selected' : '' }}>Not Verified</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" name="search" id="search" class="form-control" 
                       placeholder="Search by business name..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Vendors Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            Approved Vendors 
            <span class="badge bg-success">{{ $vendors->total() }}</span>
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Business Name</th>
                        <th>Business Type</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Approved</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $vendor)
                    <tr>
                        <td>
                            <strong>#{{ $vendor->id }}</strong>
                        </td>
                        <td>
                            <div>
                                <strong>{{ $vendor->business_name }}</strong>
                                @if($vendor->is_verified)
                                    <i class="fas fa-check-circle text-success ms-1" title="Verified"></i>
                                @endif
                            </div>
                            <small class="text-muted">{{ $vendor->product_category }}</small>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ ucfirst($vendor->business_type) }}</span>
                        </td>
                        <td>
                            <div>
                                <div><i class="fas fa-envelope"></i> {{ $vendor->business_email }}</div>
                                <div><i class="fas fa-phone"></i> {{ $vendor->business_phone }}</div>
                            </div>
                        </td>
                        <td>
                            @if($vendor->status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($vendor->status === 'suspended')
                                <span class="badge bg-warning">Suspended</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($vendor->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <div>{{ $vendor->approved_at ? $vendor->approved_at->format('M d, Y') : 'N/A' }}</div>
                            <small class="text-muted">{{ $vendor->approved_at ? $vendor->approved_at->diffForHumans() : '' }}</small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.vendor.vendors.show', $vendor->id) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-users fa-3x mb-3"></i>
                                <p>No approved vendors found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($vendors->hasPages())
        <div class="card-footer">
            {{ $vendors->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>


<style>
.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.badge {
    font-size: 0.75em;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>

@endsection
