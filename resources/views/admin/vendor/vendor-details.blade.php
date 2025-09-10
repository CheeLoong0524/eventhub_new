@extends('layouts.admin')

@section('title', 'Vendor Details - EventHub')
@section('page-title', 'Vendor Details')
@section('page-description', 'View detailed information about vendor')

@section('content')
<div class="container-fluid">
    <!-- Back Button -->
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('admin.vendor.vendors') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Vendors
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Vendor Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-store me-2"></i>Vendor Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Business Details</h6>
                            <p><strong>Business Name:</strong> {{ $vendor->business_name }}</p>
                            <p><strong>Business Type:</strong> {{ ucfirst(str_replace('_', ' ', $vendor->business_type)) }}</p>
                            <p><strong>Business Email:</strong> {{ $vendor->business_email }}</p>
                            <p><strong>Business Phone:</strong> {{ $vendor->business_phone }}</p>
                            <p><strong>Years in Business:</strong> {{ $vendor->years_in_business }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Contact Information</h6>
                            <p><strong>Contact Person:</strong> {{ $vendor->contact_person }}</p>
                            <p><strong>Contact Email:</strong> {{ $vendor->contact_email }}</p>
                            <p><strong>Contact Phone:</strong> {{ $vendor->contact_phone }}</p>
                            <p><strong>Website:</strong> 
                                @if($vendor->website)
                                    <a href="{{ $vendor->website }}" target="_blank">{{ $vendor->website }}</a>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    @if($vendor->business_description)
                    <div class="mt-3">
                        <h6>Business Description</h6>
                        <div class="bg-light p-3 rounded">
                            {{ $vendor->business_description }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- User Account Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>User Account Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> {{ $vendor->user->name }}</p>
                            <p><strong>Email:</strong> {{ $vendor->user->email }}</p>
                            <p><strong>Role:</strong> 
                                <span class="badge bg-info">{{ ucfirst($vendor->user->role) }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Account Status:</strong> 
                                <span class="badge bg-{{ $vendor->user->is_active ? 'success' : 'danger' }}">
                                    {{ $vendor->user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                            <p><strong>Email Verified:</strong> 
                                <span class="badge bg-{{ $vendor->user->email_verified_at ? 'success' : 'warning' }}">
                                    {{ $vendor->user->email_verified_at ? 'Verified' : 'Not Verified' }}
                                </span>
                            </p>
                            <p><strong>Last Login:</strong> 
                                {{ $vendor->user->last_login_at ? $vendor->user->last_login_at->format('M d, Y H:i') : 'Never' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Event Applications -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>Event Applications
                    </h5>
                </div>
                <div class="card-body">
                    @if($vendor->eventApplications && $vendor->eventApplications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Event</th>
                                        <th>Service Type</th>
                                        <th>Status</th>
                                        <th>Applied Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vendor->eventApplications as $application)
                                    <tr>
                                        <td>
                                            <strong>{{ $application->event->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $application->event->start_time->format('M d, Y') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $application->service_type_label }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $application->status_badge_color }}">
                                                {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $application->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.event-applications.show', $application->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Event Applications</h5>
                            <p class="text-muted">This vendor hasn't applied for any events yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Status & Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-cog me-2"></i>Status & Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Vendor Status:</strong>
                        <span class="badge bg-{{ $vendor->status === 'approved' ? 'success' : ($vendor->status === 'pending' ? 'warning' : 'danger') }} ms-2">
                            {{ ucfirst($vendor->status) }}
                        </span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Registration Date:</strong>
                        <p class="mb-0">{{ $vendor->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Last Updated:</strong>
                        <p class="mb-0">{{ $vendor->updated_at->format('M d, Y H:i') }}</p>
                    </div>

                    <hr>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.card {
    border-radius: 15px;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px 15px 0 0 !important;
    border-bottom: 1px solid #dee2e6;
}

.badge {
    font-size: 0.75rem;
    padding: 0.5em 0.75em;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
}

.bg-light {
    background-color: #f8f9fa !important;
}
</style>
@endsection
