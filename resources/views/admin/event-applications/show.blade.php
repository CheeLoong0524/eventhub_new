@extends('layouts.admin')

@section('title', 'Application Details - EventHub')
@section('page-title', 'Application Details')
@section('page-description', 'Review and manage vendor event application')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <!-- Application Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        Application #{{ $application->id }}
                        <span class="badge bg-{{ $application->status_badge_color }} ms-2">
                            {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Vendor Information</h6>
                            <p><strong>Business Name:</strong> {{ $application->vendor->business_name }}</p>
                            <p><strong>Business Type:</strong> {{ ucfirst(str_replace('_', ' ', $application->vendor->business_type)) }}</p>
                            <p><strong>Contact Email:</strong> {{ $application->vendor->business_email }}</p>
                            <p><strong>Phone:</strong> {{ $application->vendor->business_phone }}</p>
                            <p><strong>Years in Business:</strong> {{ $application->vendor->years_in_business }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Event Information</h6>
                            <p><strong>Event Name:</strong> {{ $application->event->name }}</p>
                            <p><strong>Date:</strong> {{ $application->event->start_time->format('M d, Y') }}</p>
                            <p><strong>Time:</strong> {{ $application->event->start_time->format('H:i') }} - {{ $application->event->end_time->format('H:i') }}</p>
                            <p><strong>Location:</strong> {{ $application->event->venue->name ?? 'TBD' }}</p>
                            <p><strong>Organizer:</strong> {{ $application->event->organizer ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Application Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Application Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Service Type:</strong> 
                                <span class="badge bg-info">{{ $application->service_type_label }}</span>
                            </p>
                            <p><strong>Booth Size:</strong> {{ $application->booth_size }}</p>
                            <p><strong>Booth Quantity:</strong> {{ $application->booth_quantity }}</p>
                            <p><strong>Requested Price:</strong> RM {{ number_format($application->requested_price ?? 0, 2) }}</p>
                            @if($application->approved_price)
                                <p><strong>Approved Price:</strong> RM {{ number_format($application->approved_price, 2) }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p><strong>Applied Date:</strong> {{ $application->created_at->format('M d, Y H:i') }}</p>
                            @if($application->reviewed_at)
                                <p><strong>Reviewed Date:</strong> {{ $application->reviewed_at->format('M d, Y H:i') }}</p>
                            @endif
                            @if($application->reviewer)
                                <p><strong>Reviewed By:</strong> {{ $application->reviewer->name }}</p>
                            @endif
                            @if($application->approved_at)
                                <p><strong>Approved Date:</strong> {{ $application->approved_at->format('M d, Y H:i') }}</p>
                            @endif
                            @if($application->rejected_at)
                                <p><strong>Rejected Date:</strong> {{ $application->rejected_at->format('M d, Y H:i') }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <h6>Service Description</h6>
                        <div class="bg-light p-3 rounded">
                            {{ $application->service_description }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Actions -->
            @if($application->canBeApproved() || $application->canBeRejected())
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Admin Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2">
                        @if($application->canBeApproved())
                            <button class="btn btn-success" onclick="approveApplication({{ $application->id }})">
                                <i class="fas fa-check me-1"></i>Approve Application
                            </button>
                        @endif
                        @if($application->canBeRejected())
                            <button class="btn btn-danger" onclick="rejectApplication({{ $application->id }})">
                                <i class="fas fa-times me-1"></i>Reject Application
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Status Timeline -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Status Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6>Application Submitted</h6>
                                <p class="small text-muted">{{ $application->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @if($application->reviewed_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6>Under Review</h6>
                                <p class="small text-muted">{{ $application->reviewed_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                        @if($application->approved_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6>Approved</h6>
                                <p class="small text-muted">{{ $application->approved_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                        @if($application->rejected_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-danger"></div>
                            <div class="timeline-content">
                                <h6>Rejected</h6>
                                <p class="small text-muted">{{ $application->rejected_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Admin Notes -->
            @if($application->admin_notes)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Admin Notes</h5>
                </div>
                <div class="card-body">
                    <p>{{ $application->admin_notes }}</p>
                </div>
            </div>
            @endif

            <!-- Rejection Reason -->
            @if($application->rejection_reason)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Rejection Reason</h5>
                </div>
                <div class="card-body">
                    <p>{{ $application->rejection_reason }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Application</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Confirmation</h6>
                    <p class="mb-0">Are you sure you want to approve this application? The approved price will be set to the requested price: <strong>RM {{ number_format($application->requested_price, 2) }}</strong></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="approveForm" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">Confirm Approval</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Application</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="3" required 
                                  placeholder="Please provide a reason for rejection..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Admin Notes</label>
                        <textarea name="admin_notes" class="form-control" rows="3" 
                                  placeholder="Optional internal notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Application</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-weight: 600;
}
</style>
@endsection

@section('scripts')
<script>
function approveApplication(id) {
    document.getElementById('approveForm').action = `/admin/event-applications/${id}/approve`;
    new bootstrap.Modal(document.getElementById('approveModal')).show();
}

function rejectApplication(id) {
    document.getElementById('rejectForm').action = `/admin/event-applications/${id}/reject`;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
@endsection
