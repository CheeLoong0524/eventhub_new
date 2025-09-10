@extends('layouts.admin')

@section('title', 'Event Applications - EventHub')
@section('page-title', 'Event Applications')
@section('page-description', 'Manage vendor applications for events')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card stats-card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-number">{{ $applications->total() }}</div>
                            <div class="stats-label">Total Applications</div>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card stats-card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-number">{{ $applications->where('status', 'pending')->count() }}</div>
                            <div class="stats-label">Pending</div>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card stats-card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-number">{{ $applications->where('status', 'approved')->count() }}</div>
                            <div class="stats-label">Approved</div>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card stats-card bg-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-number">{{ $applications->where('status', 'rejected')->count() }}</div>
                            <div class="stats-label">Rejected</div>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.event-applications.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Event</label>
                        <select name="event_id" class="form-select">
                            <option value="">All Events</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                    {{ $event->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search by vendor or event..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i>Filter
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
                <span class="badge bg-primary">{{ $applications->count() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Vendor</th>
                            <th>Event</th>
                            <th>Service Type</th>
                            <th>Booth Details</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Applied</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $application)
                        <tr>
                            <td>
                                <strong>#{{ $application->id }}</strong>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $application->vendor->business_name }}</strong>
                                </div>
                                <small class="text-muted">{{ $application->vendor->business_type }}</small>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $application->event->name }}</strong>
                                </div>
                                <small class="text-muted">{{ $application->event->start_time->format('M d, Y') }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $application->service_type_label }}</span>
                            </td>
                            <td>
                                <div><strong>{{ $application->booth_size }}</strong></div>
                                <small class="text-muted">Qty: {{ $application->booth_quantity }}</small>
                            </td>
                            <td>
                                <div><strong>RM {{ number_format($application->requested_price ?? 0, 2) }}</strong></div>
                                @if($application->approved_price)
                                    <small class="text-success">Approved: RM {{ number_format($application->approved_price, 2) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $application->status_badge_color }}">
                                    {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                                </span>
                            </td>
                            <td>
                                <div>{{ $application->created_at->format('M d, Y') }}</div>
                                <small class="text-muted">{{ $application->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.event-applications.show', $application->id) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @if($application->canBeApproved())
                                        <button class="btn btn-sm btn-outline-success" 
                                                onclick="approveApplication({{ $application->id }})">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    @endif
                                    @if($application->canBeRejected())
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="rejectApplication({{ $application->id }})">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-file-alt fa-3x mb-3"></i>
                                    <h5>No Applications Found</h5>
                                    <p>No event applications match your current filters.</p>
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
            {{ $applications->links() }}
        </div>
        @endif
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
                    <p class="mb-0">Are you sure you want to approve this application? The approved price will be set to the requested price.</p>
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
