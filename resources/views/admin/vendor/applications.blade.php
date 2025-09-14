@extends('layouts.admin')

{{-- Author  : Choong Yoong Sheng (Vendor module) --}}

@section('title', 'Vendor Applications')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Vendor Applications</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.vendor.vendors') }}" class="btn btn-outline-success">
                <i class="fas fa-users"></i> Approved Vendors
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.vendor.applications') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
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

    <!-- Applications Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                Applications 
                <span class="badge bg-primary">{{ $applications->total() }}</span>
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
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $app)
                        <tr>
                            <td>
                                <strong>#{{ $app->id }}</strong>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $app->business_name }}</strong>
                                    @if($app->is_verified)
                                        <i class="fas fa-check-circle text-success ms-1" title="Verified"></i>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $app->product_category }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst($app->business_type) }}</span>
                            </td>
                            <td>
                                <div>
                                    <div><i class="fas fa-envelope"></i> {{ $app->business_email }}</div>
                                    <div><i class="fas fa-phone"></i> {{ $app->business_phone }}</div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($app->status) {
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'suspended' => 'secondary',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">
                                    {{ str_replace('_', ' ', ucfirst($app->status)) }}
                                </span>
                            </td>
                            <td>
                                <div>{{ $app->created_at->format('M d, Y') }}</div>
                                <small class="text-muted">{{ $app->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.vendor.applications.show', $app->id) }}" 
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
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>No applications found.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($applications->hasPages())
        <div class="card-footer">
            {{ $applications->appends(request()->query())->links() }}
        </div>
        @endif
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