@extends('layouts.vendor')

{{-- Author  : Choong Yoong Sheng (Vendor module) --}}

@section('title', 'Event Details - ' . $event->name)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">{{ $event->name }}</h4>
                        @if(isset($existingApplications) && $existingApplications->count() > 0)
                            <span class="badge bg-info status-badge fs-6 d-flex align-items-center">
                                <i class="fas fa-list me-1"></i>
                                {{ $existingApplications->count() }} Application{{ $existingApplications->count() > 1 ? 's' : '' }}
                            </span>
                        @else
                            <span class="badge bg-success status-badge fs-6 d-flex align-items-center">
                                <i class="fas fa-check-circle me-1"></i>
                                Available
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Event Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6><i class="fas fa-calendar-alt me-2"></i>Event Details</h6>
                            <p class="mb-1"><strong>Start Date:</strong> {{ $event->start_time ? $event->start_time->format('M d, Y H:i') : 'TBD' }}</p>
                            <p class="mb-1"><strong>End Date:</strong> {{ $event->end_time ? $event->end_time->format('M d, Y H:i') : 'TBD' }}</p>
                            @if($event->start_time && $event->end_time)
                                <p class="mb-1"><strong>Duration:</strong> {{ $event->start_time->diffInDays($event->end_time) + 1 }} day{{ $event->start_time->diffInDays($event->end_time) + 1 > 1 ? 's' : '' }}</p>
                            @endif
                            @if($event->application_deadline)
                                <p class="mb-1"><strong>Application Deadline:</strong> 
                                    <span class="text-warning">{{ $event->application_deadline ? $event->application_deadline->format('M d, Y H:i') : 'TBD' }}</span>
                                </p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-map-marker-alt me-2"></i>Location & Organizer</h6>
                            <p class="mb-1"><strong>Venue:</strong> {{ $event->venue->name ?? 'TBD' }}</p>
                            <p class="mb-1"><strong>Organizer:</strong> {{ $event->organizer ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Available Booths:</strong> 
                                <span class="text-success">
                                    {{ $event->available_booths }} booths available
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Booth Pricing Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6><i class="fas fa-dollar-sign me-2"></i>Booth Pricing</h6>
                            <div class="pricing-card bg-light p-3 rounded">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h5 class="text-primary mb-2">RM {{ number_format($event->booth_price ?? 0, 2) }}</h5>
                                        <p class="text-muted mb-0">Per booth rental</p>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <small class="text-muted">
                                            <i class="fas fa-hashtag me-1"></i>
                                            {{ $event->booth_quantity }} booths available
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Event Status -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6><i class="fas fa-info-circle me-2"></i>Event Status</h6>
                            <div class="status-info">
                                @if($event->isAcceptingApplications())
                                    <span class="badge bg-success fs-6">Accepting Applications</span>
                                @else
                                    <span class="badge bg-danger fs-6">Not Accepting Applications</span>
                                @endif
                                
                                @if($event->hasAvailableSlots())
                                    <span class="badge bg-info fs-6 ms-2">Booths Available</span>
                                @else
                                    <span class="badge bg-warning fs-6 ms-2">Fully Booked</span>
                                @endif

                                @if($event->booth_quantity > 0)
                                    <span class="badge bg-secondary fs-6 ms-2">
                                        {{ $event->booth_sold }} booths sold
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Event Description -->
                    <div class="mb-4">
                        <h6><i class="fas fa-align-left me-2"></i>Event Description</h6>
                        <div class="description-content">
                            <p>{{ $event->description }}</p>
                        </div>
                    </div>

                    <!-- Application Status -->
                    @if(isset($existingApplications) && $existingApplications->count() > 0)
                        <div class="alert alert-info">
                            <h6><i class="fas fa-clipboard-check me-2"></i>Your Applications ({{ $existingApplications->count() }})</h6>
                            <p class="mb-3">You have {{ $existingApplications->count() }} application{{ $existingApplications->count() > 1 ? 's' : '' }} for this event:</p>
                            
                            <div class="row">
                                @foreach($existingApplications as $index => $application)
                                <div class="col-md-6 mb-3">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0">Application #{{ $application->id }}</h6>
                                                <span class="badge bg-{{ $application->status_badge_color }} status-badge">
                                                    <i class="{{ $application->status_icon }} me-1"></i>
                                                    {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                                                </span>
                                            </div>
                                            <ul class="list-unstyled mb-2 small">
                                                <li><strong>Booth:</strong> {{ $application->booth_size }} ({{ $application->booth_quantity }} unit{{ $application->booth_quantity > 1 ? 's' : '' }})</li>
                                                <li><strong>Service:</strong> {{ $application->service_type_label }}</li>
                                                <li><strong>Applied:</strong> {{ $application->created_at ? $application->created_at->format('M d, Y H:i') : 'N/A' }}</li>
                                                @if($application->approved_price)
                                                    <li><strong>Price:</strong> RM {{ number_format($application->approved_price, 2) }}</li>
                                                @endif
                                            </ul>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('vendor.applications.show', $application->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i>View
                                                </a>
                                                @if($application->status === 'approved')
                                                    <a href="{{ route('vendor.payment', $application->id) }}" class="btn btn-sm btn-success">
                                                        <i class="fas fa-credit-card me-1"></i>Pay
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    @if(isset($existingApplication) && $existingApplication)
                        <div class="d-grid gap-2">
                            <a href="{{ route('vendor.applications.show', $existingApplication->id) }}" class="btn btn-info">
                                <i class="fas fa-eye me-2"></i>View Application
                            </a>
                            @if($existingApplication->isApproved())
                                <a href="{{ route('vendor.applications.payment', $existingApplication->id) }}" class="btn btn-success">
                                    <i class="fas fa-credit-card me-2"></i>Make Payment
                                </a>
                            @endif
                            @if($existingApplication->canBeCancelled())
                                <button type="button" class="btn btn-outline-danger" 
                                        data-bs-toggle="modal" data-bs-target="#cancelModal">
                                    <i class="fas fa-times me-2"></i>Cancel Application
                                </button>
                            @endif
                        </div>
                    @else
                        <div class="d-grid gap-2">
                            @if($event->isAcceptingApplications() && $event->hasAvailableSlots())
                                <a href="{{ route('vendor.events.apply', $event->id) }}" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Apply for This Event
                                </a>
                            @else
                                <button class="btn btn-secondary" disabled>
                                    <i class="fas fa-times me-2"></i>Not Available for Application
                                </button>
                            @endif
                        </div>
                    @endif
                    
                    <hr>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('vendor.events') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Events
                        </a>
                        <a href="{{ route('vendor.applications') }}" class="btn btn-outline-info">
                            <i class="fas fa-clipboard-list me-2"></i>My Applications
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Event Requirements</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small">
                        <li><i class="fas fa-check text-success me-2"></i>Valid vendor account required</li>
                        <li><i class="fas fa-check text-success me-2"></i>Approved vendor status</li>
                        <li><i class="fas fa-check text-success me-2"></i>Complete application form</li>
                        <li><i class="fas fa-check text-success me-2"></i>Payment required after approval</li>
                        @if($event->application_deadline)
                            <li><i class="fas fa-check text-success me-2"></i>Application before deadline</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Application Modal -->
@if(isset($existingApplication) && $existingApplication && $existingApplication->canBeCancelled())
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('vendor.applications.cancel', $existingApplication->id) }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Cancel Application</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to cancel your application for <strong>{{ $event->name }}</strong>?</p>
                        <div class="alert alert-warning">
                            <strong>Note:</strong> This action cannot be undone. You will need to submit a new application if you want to participate in this event.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Application</button>
                        <button type="submit" class="btn btn-danger">Cancel Application</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection

@section('styles')
<style>
.pricing-card {
    border-left: 4px solid #0d6efd;
}

.status-info .badge {
    font-size: 0.9rem;
}

.status-badge {
    font-size: 0.7em !important;
    padding: 0.35em 0.6em !important;
    white-space: nowrap;
    max-width: fit-content;
}

.description-content {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.375rem;
    border-left: 4px solid #6c757d;
}

.alert {
    border-left: 4px solid;
}

.alert-success {
    border-left-color: #28a745;
}

.alert-danger {
    border-left-color: #dc3545;
}

.alert-info {
    border-left-color: #17a2b8;
}
</style>
@endsection
