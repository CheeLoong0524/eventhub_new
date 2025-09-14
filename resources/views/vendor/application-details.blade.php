@extends('layouts.vendor')

@section('title', 'Application Details - EventHub')
@section('page-title', 'Application Details')
@section('page-description', 'View your event application status and details')

@section('content')
<div class="container">
    <!-- Enhanced Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-1">Application #{{ $application->id }}</h1>
                    <p class="text-muted mb-0">Track your application status and details</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ url()->previous() ?: route('vendor.applications') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </a>
                    <a href="{{ route('vendor.applications') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-1"></i>My Applications
                    </a>
                    <a href="{{ route('vendor.events') }}" class="btn btn-primary">
                        <i class="fas fa-calendar me-1"></i>Browse Events
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <!-- Enhanced Application Status -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 text-white">
                                <i class="{{ $application->status_icon }} me-2"></i>
                                Application Status
                            </h5>
                            <p class="mb-0 text-white-50">Track your application progress</p>
                        </div>
                        <span class="badge bg-white text-dark fs-6 px-3 py-2">
                            {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if($application->isPaid())
                        <div class="alert alert-success border-0 shadow-sm">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-credit-card fa-2x text-success"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="alert-heading mb-1">Payment Completed!</h6>
                                    <p class="mb-0">Congratulations! Your payment has been processed successfully. Your booth is now confirmed for the event.</p>
                                </div>
                            </div>
                        </div>
                    @elseif($application->isApproved())
                        <div class="alert alert-success border-0 shadow-sm">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle fa-2x text-success"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="alert-heading mb-1">Application Approved!</h6>
                                    <p class="mb-0">Congratulations! Your application has been approved. You can now proceed with payment to confirm your participation.</p>
                                </div>
                            </div>
                        </div>
                    @elseif($application->isRejected())
                        <div class="alert alert-danger border-0 shadow-sm">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-times-circle fa-2x text-danger"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="alert-heading mb-1">Application Rejected</h6>
                                    <p class="mb-0">Unfortunately, your application has been rejected. Please review the reason below and consider applying for other events.</p>
                                </div>
                            </div>
                        </div>
                    @elseif($application->isCancelled())
                        <div class="alert alert-secondary border-0 shadow-sm">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-ban fa-2x text-secondary"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="alert-heading mb-1">Application Cancelled</h6>
                                    <p class="mb-0">This application has been cancelled. You can apply for other events or create a new application for this event.</p>
                                </div>
                            </div>
                        </div>
                    @elseif($application->isUnderReview())
                        <div class="alert alert-info border-0 shadow-sm">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-search fa-2x text-info"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="alert-heading mb-1">Under Review</h6>
                                    <p class="mb-0">Your application is currently being reviewed by our team. You will be notified once a decision is made.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning border-0 shadow-sm">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-clock fa-2x text-warning"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="alert-heading mb-1">Pending Review</h6>
                                    <p class="mb-0">Your application has been submitted and is waiting for review. We will notify you once it's been processed.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Enhanced Event Information -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt me-2 text-primary"></i>
                        Event Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-section">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-info-circle me-2"></i>Event Details
                                </h6>
                                <div class="info-item">
                                    <span class="label">Event Name:</span>
                                    <span class="value">{{ $application->event->name }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Event Type:</span>
                                    <span class="value">General Event</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Start Date:</span>
                                    <span class="value">{{ $application->event->start_time->format('M d, Y') }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">End Date:</span>
                                    <span class="value">{{ $application->event->end_time->format('M d, Y') }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Time:</span>
                                    <span class="value">{{ $application->event->start_time->format('H:i') }} - {{ $application->event->end_time->format('H:i') }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Duration:</span>
                                    <span class="value">{{ $application->event->start_time->diffForHumans($application->event->end_time, true) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-section">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-map-marker-alt me-2"></i>Venue Details
                                </h6>
                                <div class="info-item">
                                    <span class="label">Location:</span>
                                    <span class="value">{{ $application->event->venue->name ?? 'TBD' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Venue Type:</span>
                                    <span class="value">{{ $application->event->venue->type ?? 'N/A' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Capacity:</span>
                                    <span class="value">{{ $application->event->venue->capacity ?? 'N/A' }} people</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Organizer:</span>
                                    <span class="value">{{ $application->event->organizer ?? 'N/A' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Status:</span>
                                    <span class="badge bg-{{ $application->event->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($application->event->status) }}</span>
                                </div>
                                @if($application->event->venue && $application->event->venue->address)
                                    <div class="info-item">
                                        <span class="label">Address:</span>
                                        <span class="value">{{ $application->event->venue->address }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($application->event->description)
                        <hr class="my-4">
                        <div class="info-section">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-align-left me-2"></i>Event Description
                            </h6>
                            <div class="description-box">
                                {{ $application->event->description }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Enhanced Application Details -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2 text-primary"></i>
                        Your Application Details
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-section">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-cogs me-2"></i>Service Details
                                </h6>
                                <div class="info-item">
                                    <span class="label">Service Type:</span>
                                    <span class="badge bg-info fs-6">{{ $application->service_type_label }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Booth Size:</span>
                                    <span class="value">{{ $application->booth_size }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Booth Quantity:</span>
                                    <span class="value">{{ $application->booth_quantity }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-section">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-dollar-sign me-2"></i>Pricing
                                </h6>
                                <div class="info-item">
                                    <span class="label">Total Price:</span>
                                    <span class="value fw-bold text-primary fs-5">RM {{ number_format($application->approved_price ?? $application->requested_price ?? 0, 2) }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Calculation:</span>
                                    <span class="value text-muted small">
                                        {{ $application->booth_size }} × {{ $application->booth_quantity }} booth(s)
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="info-section">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-align-left me-2"></i>Service Description
                        </h6>
                        <div class="description-box">
                            {{ $application->service_description }}
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="info-section">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-history me-2"></i>Application Timeline
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="timeline-item-small">
                                    <i class="fas fa-paper-plane text-primary me-2"></i>
                                    <span class="label">Applied:</span>
                                    <span class="value">{{ $application->created_at->format('M d, Y H:i') }}</span>
                                </div>
                            </div>
                            @if($application->reviewed_at)
                            <div class="col-md-6">
                                <div class="timeline-item-small">
                                    <i class="fas fa-search text-info me-2"></i>
                                    <span class="label">Reviewed:</span>
                                    <span class="value">{{ $application->reviewed_at->format('M d, Y H:i') }}</span>
                                </div>
                            </div>
                            @endif
                            @if($application->approved_at)
                            <div class="col-md-6">
                                <div class="timeline-item-small">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span class="label">Approved:</span>
                                    <span class="value">{{ $application->approved_at->format('M d, Y H:i') }}</span>
                                </div>
                            </div>
                            @endif
                            @if($application->rejected_at)
                            <div class="col-md-6">
                                <div class="timeline-item-small">
                                    <i class="fas fa-times-circle text-danger me-2"></i>
                                    <span class="label">Rejected:</span>
                                    <span class="value">{{ $application->rejected_at->format('M d, Y H:i') }}</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-tools me-2 text-primary"></i>
                        Actions
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap gap-3">
                        @if($application->isPaid())
                            <div class="alert alert-info border-0 shadow-sm mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Payment completed! Your booth is confirmed for the event.
                            </div>
                        @elseif($application->isApproved())
                            <a href="{{ route('vendor.payment', $application->id) }}" class="btn btn-success btn-lg">
                                <i class="fas fa-credit-card me-2"></i>Proceed to Payment
                            </a>
                        @endif
                        @if($application->canBeCancelled())
                            <form method="POST" action="{{ route('vendor.applications.cancel', $application->id) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-lg" 
                                        onclick="return confirm('Are you sure you want to cancel this application?')">
                                    <i class="fas fa-times me-2"></i>Cancel Application
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('vendor.applications') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i>Back to Applications
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Enhanced Status Timeline -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-route me-2"></i>
                        Status Timeline
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="timeline-enhanced">
                        <div class="timeline-item-enhanced">
                            <div class="timeline-marker-enhanced bg-primary">
                                <i class="fas fa-paper-plane text-white"></i>
                            </div>
                            <div class="timeline-content-enhanced">
                                <h6 class="mb-1">Application Submitted</h6>
                                <p class="small text-muted mb-0">{{ $application->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @if($application->reviewed_at)
                        <div class="timeline-item-enhanced">
                            <div class="timeline-marker-enhanced bg-info">
                                <i class="fas fa-search text-white"></i>
                            </div>
                            <div class="timeline-content-enhanced">
                                <h6 class="mb-1">Under Review</h6>
                                <p class="small text-muted mb-0">{{ $application->reviewed_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                        @if($application->approved_at)
                        <div class="timeline-item-enhanced">
                            <div class="timeline-marker-enhanced bg-success">
                                <i class="fas fa-check text-white"></i>
                            </div>
                            <div class="timeline-content-enhanced">
                                <h6 class="mb-1">Approved</h6>
                                <p class="small text-muted mb-0">{{ $application->approved_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                        @if($application->rejected_at)
                        <div class="timeline-item-enhanced">
                            <div class="timeline-marker-enhanced bg-danger">
                                <i class="fas fa-times text-white"></i>
                            </div>
                            <div class="timeline-content-enhanced">
                                <h6 class="mb-1">Rejected</h6>
                                <p class="small text-muted mb-0">{{ $application->rejected_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                        @if($application->isCancelled())
                        <div class="timeline-item-enhanced">
                            <div class="timeline-marker-enhanced bg-secondary">
                                <i class="fas fa-ban text-white"></i>
                            </div>
                            <div class="timeline-content-enhanced">
                                <h6 class="mb-1">Cancelled</h6>
                                <p class="small text-muted mb-0">{{ $application->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Admin Notes -->
            @if($application->admin_notes)
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-sticky-note me-2 text-warning"></i>
                        Admin Notes
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="admin-notes-box">
                        <i class="fas fa-quote-left text-muted me-2"></i>
                        {{ $application->admin_notes }}
                        <i class="fas fa-quote-right text-muted ms-2"></i>
                    </div>
                </div>
            </div>
            @endif

            <!-- Rejection Reason -->
            @if($application->rejection_reason)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2 text-danger"></i>
                        Rejection Reason
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="rejection-reason-box">
                        <i class="fas fa-quote-left text-muted me-2"></i>
                        {{ $application->rejection_reason }}
                        <i class="fas fa-quote-right text-muted ms-2"></i>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
/* Enhanced Timeline Styles */
.timeline-enhanced {
    position: relative;
    padding-left: 40px;
}

.timeline-enhanced::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(to bottom, #667eea, #764ba2);
    border-radius: 2px;
}

.timeline-item-enhanced {
    position: relative;
    margin-bottom: 25px;
}

.timeline-marker-enhanced {
    position: absolute;
    left: -30px;
    top: 5px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    font-size: 10px;
}

.timeline-content-enhanced h6 {
    margin-bottom: 5px;
    font-weight: 600;
    color: #495057;
}

/* Info Section Styles */
.info-section {
    margin-bottom: 1.5rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item .label {
    font-weight: 600;
    color: #6c757d;
    min-width: 120px;
}

.info-item .value {
    color: #495057;
    text-align: right;
}

/* Description Box */
.description-box {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1.5rem;
    font-style: italic;
    line-height: 1.6;
    color: #495057;
}

/* Admin Notes Box */
.admin-notes-box {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border: 1px solid #ffc107;
    border-radius: 8px;
    padding: 1.5rem;
    font-style: italic;
    line-height: 1.6;
    color: #856404;
}

/* Rejection Reason Box */
.rejection-reason-box {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    border: 1px solid #dc3545;
    border-radius: 8px;
    padding: 1.5rem;
    font-style: italic;
    line-height: 1.6;
    color: #721c24;
}

/* Timeline Item Small */
.timeline-item-small {
    display: flex;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.timeline-item-small:last-child {
    border-bottom: none;
}

.timeline-item-small .label {
    font-weight: 600;
    color: #6c757d;
    min-width: 80px;
    margin-right: 0.5rem;
}

.timeline-item-small .value {
    color: #495057;
}

/* Card Enhancements */
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
}

/* Button Enhancements */
.btn {
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Responsive Design */
@media (max-width: 768px) {
    .info-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .info-item .value {
        text-align: left;
        margin-top: 0.25rem;
    }
    
    .timeline-enhanced {
        padding-left: 30px;
    }
    
    .timeline-marker-enhanced {
        left: -25px;
        width: 16px;
        height: 16px;
    }
}
</style>
@endsection