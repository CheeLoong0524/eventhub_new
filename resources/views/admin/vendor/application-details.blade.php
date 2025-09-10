@extends('layouts.admin')

@section('title', 'Application Details')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Application #{{ $application->id }}</h1>
        <div>
            <a href="{{ route('admin.vendor.applications') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Applications
            </a>
        </div>
    </div>

    <!-- Status Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-1">{{ $application->business_name }}</h4>
                    <p class="text-muted mb-2">{{ $application->business_type }} • {{ $application->product_category }}</p>
                    @php
                        $badgeClass = match($application->status) {
                            'pending' => 'warning',
                            'under_review' => 'info',
                            'approved' => 'success',
                            'rejected' => 'danger',
                            'suspended' => 'secondary',
                            default => 'secondary'
                        };
                    @endphp
                    <span class="badge bg-{{ $badgeClass }} fs-6">
                        {{ str_replace('_', ' ', ucfirst($application->status)) }}
                    </span>
                    @if($application->is_verified)
                        <span class="badge bg-success ms-2">
                            <i class="fas fa-check-circle"></i> Verified
                        </span>
                    @endif
                </div>
                <div class="col-md-4 text-end">
                    <div class="text-muted">
                        <small>Submitted: {{ $application->created_at->format('M d, Y H:i') }}</small>
                        @if($application->reviewed_at)
                            <br><small>Reviewed: {{ $application->reviewed_at->format('M d, Y H:i') }}</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Application Details -->
        <div class="col-lg-8">
            <!-- Business Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-building"></i> Business Information
                    </h5>
                </div>
                 <div class="card-body">
                     <!-- Basic Information Row -->
                     <div class="row mb-4">
                         <div class="col-md-6">
                             <div class="info-item mb-3">
                                 <label class="info-label">Business Name</label>
                                 <div class="info-value">{{ $application->business_name }}</div>
                             </div>
                         </div>
                         <div class="col-md-6">
                             <div class="info-item mb-3">
                                 <label class="info-label">Business Type</label>
                                 <div class="info-value">
                                     <span class="badge bg-info">{{ ucfirst($application->business_type) }}</span>
                                 </div>
                             </div>
                         </div>
                     </div>
                     
                     <!-- Product & Target Row -->
                     <div class="row mb-4">
                         <div class="col-md-6">
                             <div class="info-item mb-3">
                                 <label class="info-label">Product Category</label>
                                 <div class="info-value">{{ ucfirst($application->product_category) }}</div>
                             </div>
                         </div>
                         <div class="col-md-6">
                             <div class="info-item mb-3">
                                 <label class="info-label">Target Audience</label>
                                 <div class="info-value">{{ ucfirst($application->target_audience) }}</div>
                             </div>
                         </div>
                     </div>
                     
                     <!-- Business Details Row -->
                     <div class="row mb-4">
                         <div class="col-md-3">
                             <div class="info-item mb-3">
                                 <label class="info-label">Years in Business</label>
                                 <div class="info-value">{{ $application->years_in_business }}</div>
                             </div>
                         </div>
                         <div class="col-md-3">
                             <div class="info-item mb-3">
                                 <label class="info-label">Business Size</label>
                                 <div class="info-value">{{ ucfirst($application->business_size) }}</div>
                             </div>
                         </div>
                         <div class="col-md-3">
                             <div class="info-item mb-3">
                                 <label class="info-label">Annual Revenue</label>
                                 <div class="info-value">{{ ucfirst($application->annual_revenue) }}</div>
                             </div>
                         </div>
                         <div class="col-md-3">
                             <div class="info-item mb-3">
                                 <label class="info-label">Event Experience</label>
                                 <div class="info-value">{{ ucfirst($application->event_experience) }}</div>
                             </div>
                         </div>
                     </div>
                     
                     <!-- Business Description -->
                     <div class="info-item">
                         <label class="info-label">Business Description</label>
                         <div class="info-value description-text">{{ $application->business_description }}</div>
                     </div>
                 </div>
            </div>

            <!-- Contact Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-address-book"></i> Contact Information
                    </h5>
                </div>
        <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Email:</dt>
                                <dd class="col-sm-8">
                                    <a href="mailto:{{ $application->business_email }}">{{ $application->business_email }}</a>
                                </dd>
                                
                                <dt class="col-sm-4">Phone:</dt>
                                <dd class="col-sm-8">
                                    <a href="tel:{{ $application->business_phone }}">{{ $application->business_phone }}</a>
                                </dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">User:</dt>
                                <dd class="col-sm-8">
                                    <a href="{{ route('admin.users.show', $application->user->id) }}">
                                        {{ $application->user->name }}
                                    </a>
                                </dd>
                                
                                <dt class="col-sm-4">User Email:</dt>
                                <dd class="col-sm-8">{{ $application->user->email }}</dd>
            </dl>
        </div>
    </div>
                </div>
            </div>

            <!-- Marketing Strategy -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line"></i> Marketing Strategy
                    </h5>
                </div>
                <div class="card-body">
                    <p>{{ $application->marketing_strategy }}</p>
                </div>
            </div>

            <!-- Admin Notes -->
            @if($application->admin_notes)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-sticky-note"></i> Admin Notes
                    </h5>
                </div>
                <div class="card-body">
                    <p>{{ $application->admin_notes }}</p>
                </div>
            </div>
            @endif

            <!-- Rejection Reason -->
            @if($application->rejection_reason)
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-times-circle"></i> Rejection Reason
                    </h5>
                </div>
                <div class="card-body">
                    <p>{{ $application->rejection_reason }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Action Panel -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-cogs"></i> Actions
                    </h5>
                </div>
                <div class="card-body">

                    @if($application->status === 'pending')
                        <!-- Approve Form -->
                        <form method="POST" action="{{ route('admin.vendor.applications.approve', $application->id) }}" class="mb-3">
                            @csrf
                            <div class="alert alert-info mb-3">
                                <h6><i class="fas fa-info-circle me-2"></i>Confirmation</h6>
                                <p class="mb-0">Are you sure you want to approve this application? This action cannot be undone.</p>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check"></i> Approve Application
                            </button>
                        </form>

                        <!-- Reject Form -->
                        <form method="POST" action="{{ route('admin.vendor.applications.reject', $application->id) }}">
            @csrf
                            <div class="mb-3">
                                <label for="rejection_reason" class="form-label">Rejection Reason *</label>
                                <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3" 
                                          placeholder="Please provide a reason for rejection..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger w-100" 
                                    onclick="return confirm('Reject this application? This action cannot be undone.')">
                                <i class="fas fa-times"></i> Reject Application
                            </button>
        </form>
                    @elseif($application->status === 'approved')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> This application has been approved.
                        </div>
                        
                        @if($application->is_verified)
                            <div class="alert alert-info">
                                <i class="fas fa-shield-alt"></i> This vendor is verified.
                            </div>
                        @endif
                    @elseif($application->status === 'rejected')
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle"></i> This application has been rejected.
                        </div>
                    @elseif($application->status === 'suspended')
                        <div class="alert alert-warning">
                            <i class="fas fa-pause-circle"></i> This vendor account is suspended.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Quick Stats</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h4 text-primary">{{ $application->years_in_business }}</div>
                            <small class="text-muted">Years in Business</small>
                        </div>
                        <div class="col-6">
                            <div class="h4 text-success">{{ ucfirst($application->business_size) }}</div>
                            <small class="text-muted">Business Size</small>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>

<style>
.card-header h5 {
    color: #495057;
}

.badge {
    font-size: 0.8em;
}

.alert {
    border: none;
    border-radius: 0.5rem;
}

.btn {
    border-radius: 0.5rem;
}

.form-control {
    border-radius: 0.5rem;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

 .card-header {
     background-color: #f8f9fa;
     border-bottom: 1px solid #dee2e6;
 }

 /* Business Information Styling */
 .info-item {
     padding: 0.75rem;
     background-color: #f8f9fa;
     border-radius: 0.5rem;
     border-left: 4px solid #007bff;
     transition: all 0.3s ease;
 }

 .info-item:hover {
     background-color: #e9ecef;
     transform: translateY(-1px);
     box-shadow: 0 2px 8px rgba(0,0,0,0.1);
 }

 .info-label {
     font-weight: 600;
     color: #495057;
     font-size: 0.875rem;
     margin-bottom: 0.25rem;
     display: block;
     text-transform: uppercase;
     letter-spacing: 0.5px;
 }

 .info-value {
     color: #212529;
     font-size: 1rem;
     font-weight: 500;
     word-break: break-word;
 }

 .description-text {
     background-color: white;
     padding: 1rem;
     border-radius: 0.5rem;
     border: 1px solid #dee2e6;
     line-height: 1.6;
     min-height: 100px;
 }

 /* Responsive adjustments */
 @media (max-width: 768px) {
     .info-item {
         margin-bottom: 1rem;
     }
     
     .row.mb-4 .col-md-3 {
         margin-bottom: 1rem;
     }
 }
 </style>
@endsection