@extends('layouts.vendor')

{{-- Author  : Choong Yoong Sheng (Vendor module) --}}

@section('title', 'Application Status - EventHub')

@section('page-title', 'Application Status')
@section('page-description', 'Track and update your vendor application')
@section('content')
<div class="container py-5">
    <!-- Header Section -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-8 text-center">
            <div class="mb-4">
                <i class="fas fa-file-alt fa-3x text-primary mb-3"></i>
                <h1 class="display-5 fw-bold text-dark">Application Status</h1>
                <p class="lead text-muted">Track your vendor application progress</p>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Status Card -->
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-gradient-primary text-white py-4">
                    <h4 class="card-title mb-0 text-center">
                        <i class="fas fa-clipboard-check me-2"></i>Application Details
                    </h4>
                </div>
                
                <div class="card-body p-5">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <i class="fas fa-building fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Company Name</h6>
                                    <h5 class="mb-0">{{ $vendor->business_name ?? $vendor->company_name ?? 'N/A' }}</h5>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <i class="fas fa-calendar-alt fa-2x text-info"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Application Date</h6>
                                    <h5 class="mb-0">{{ $vendor->created_at ? $vendor->created_at->format('M d, Y') : 'N/A' }}</h5>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <i class="fas fa-tag fa-2x text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Business Type</h6>
                                    <h5 class="mb-0">{{ ucfirst(str_replace('_', ' ', $vendor->business_type ?? 'N/A')) }}</h5>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <i class="fas fa-envelope fa-2x text-success"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Contact Email</h6>
                                    <h5 class="mb-0">{{ $vendor->business_email ?? 'N/A' }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Progress -->
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-white py-4">
                    <h4 class="card-title mb-0 text-center">
                        <i class="fas fa-tasks me-2"></i>Application Progress
                    </h4>
                </div>
                
                <div class="card-body p-5">
                    @php
                        $status = $vendor->status ?? 'pending';
                        $statusSteps = [
                            'pending' => ['Submitted', 'Under Review', 'Approved'],
                            'under_review' => ['Submitted', 'Under Review', 'Approved'],
                            'approved' => ['Submitted', 'Under Review', 'Approved'],
                            'rejected' => ['Submitted', 'Under Review', 'Rejected']
                        ];
                        $currentStep = array_search($status, ['pending', 'under_review', 'approved', 'rejected']);
                        $steps = $statusSteps[$status] ?? $statusSteps['pending'];
                    @endphp
                    
                    <div class="timeline">
                        @foreach($steps as $index => $step)
                            <div class="timeline-item {{ $index <= $currentStep ? 'completed' : '' }} {{ $index == $currentStep ? 'current' : '' }}">
                                <div class="timeline-marker">
                                    @if($index < $currentStep)
                                        <i class="fas fa-check"></i>
                                    @elseif($index == $currentStep)
                                        <i class="fas fa-clock"></i>
                                    @else
                                        <i class="fas fa-circle"></i>
                                    @endif
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">{{ $step }}</h6>
                                    @if($index == 0)
                                        <p class="text-muted small mb-0">Application submitted successfully</p>
                                    @elseif($index == 1)
                                        <p class="text-muted small mb-0">Our team is reviewing your application</p>
                                    @elseif($index == 2)
                                        @if($status == 'approved')
                                            <p class="text-success small mb-0">Congratulations! Your application has been approved</p>
                                        @elseif($status == 'rejected')
                                            <p class="text-danger small mb-0">Unfortunately, your application was not approved</p>
                                        @else
                                            <p class="text-muted small mb-0">Final decision pending</p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Current Status Badge -->
            <div class="text-center mb-4">
                <div class="d-inline-block">
                    <span class="badge bg-{{ $vendor->status_badge_color ?? 'secondary' }} fs-6 px-4 py-3 rounded-pill">
                        <i class="fas fa-{{ $status == 'approved' ? 'check-circle' : ($status == 'rejected' ? 'times-circle' : 'clock') }} me-2"></i>
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </span>
                </div>
            </div>

            <!-- Status Messages -->
            @if($status == 'pending')
                <div class="alert alert-info border-0 shadow-sm">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle fa-2x me-3"></i>
                        <div>
                            <h6 class="alert-heading mb-1">Application Submitted</h6>
                            <p class="mb-0">Your application has been received and is waiting to be reviewed by our team. We'll get back to you within 3-5 business days.</p>
                        </div>
                    </div>
                </div>
            @elseif($status == 'under_review')
                <div class="alert alert-warning border-0 shadow-sm">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-search fa-2x me-3"></i>
                        <div>
                            <h6 class="alert-heading mb-1">Under Review</h6>
                            <p class="mb-0">Our team is currently reviewing your application. This process typically takes 1-2 business days. You'll be notified once a decision is made.</p>
                        </div>
                    </div>
                </div>
            @elseif($status == 'approved')
                <div class="alert alert-success border-0 shadow-sm">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle fa-2x me-3"></i>
                        <div>
                            <h6 class="alert-heading mb-1">Application Approved!</h6>
                            <p class="mb-0">Congratulations! Your vendor application has been approved. You can now access the vendor dashboard and start applying for events.</p>
                        </div>
                    </div>
                </div>
            @elseif($status == 'rejected')
                <div class="alert alert-danger border-0 shadow-sm">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-times-circle fa-2x me-3"></i>
                        <div>
                            <h6 class="alert-heading mb-1">Application Not Approved</h6>
                            <p class="mb-0">Unfortunately, your application was not approved at this time. You may reapply after addressing any concerns or improving your application.</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Admin Notes (if any) -->
            @if($vendor->admin_notes)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-sticky-note me-2"></i>Admin Notes
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $vendor->admin_notes }}</p>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="text-center">
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="{{ route('vendor.dashboard') }}" class="btn btn-primary btn-lg px-4">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    
                    <a href="{{ route('vendor.apply') }}" class="btn btn-outline-primary btn-lg px-4">
                        <i class="fas fa-pen-to-square me-2"></i>Update Form
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.card {
    border-radius: 15px;
    overflow: hidden;
}

.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 1rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
    padding-left: 2rem;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -1.5rem;
    top: 0.5rem;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: bold;
    z-index: 2;
}

.timeline-item.completed .timeline-marker {
    background: #28a745;
    color: white;
}

.timeline-item.current .timeline-marker {
    background: #007bff;
    color: white;
    animation: pulse 2s infinite;
}

.timeline-item:not(.completed):not(.current) .timeline-marker {
    background: #e9ecef;
    color: #6c757d;
}

.timeline-content h6 {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.timeline-item.completed .timeline-content h6 {
    color: #28a745;
}

.timeline-item.current .timeline-content h6 {
    color: #007bff;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(0, 123, 255, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(0, 123, 255, 0);
    }
}

.badge {
    font-size: 1rem;
    padding: 0.75rem 1.5rem;
}

.alert {
    border-radius: 15px;
    border: none;
}

.btn {
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
}

.btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
}

.btn-primary:hover {
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
}

@media (max-width: 768px) {
    .container {
        padding: 1rem;
    }
    
    .card-body {
        padding: 2rem !important;
    }
    
    .btn-lg {
        padding: 10px 20px !important;
        font-size: 1rem;
    }
    
    .timeline {
        padding-left: 1.5rem;
    }
    
    .timeline::before {
        left: 0.75rem;
    }
    
    .timeline-item {
        padding-left: 1.5rem;
    }
    
    .timeline-marker {
        left: -1.25rem;
        width: 1.5rem;
        height: 1.5rem;
        font-size: 0.625rem;
    }
}
</style>
@endsection


