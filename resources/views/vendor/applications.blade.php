@extends('layouts.vendor')

@section('title', 'My Applications - EventHub')
@section('page-title', 'My Applications')
@section('page-description', 'Track your event application status and history')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>My Applications</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('vendor.events') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Apply to Events
        </a>
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
                        <th>Event Name</th>
                        <th>Event Date</th>
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
                            <strong>#{{ $application->id ?? 'N/A' }}</strong>
                        </td>
                        <td>
                            <div>
                                <strong>{{ $application->event->name ?? 'Event Application' }}</strong>
                            </div>
                            <small class="text-muted">{{ Str::limit($application->event->description ?? 'No description available', 50) }}</small>
                        </td>
                        <td>
                            <div>{{ $application->event->start_time ? $application->event->start_time->format('M d, Y') : 'TBD' }}</div>
                            <small class="text-muted">{{ $application->event->venue->name ?? 'TBD' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $application->booth_size ?? 'Standard' }}</span>
                            @if($application->booth_quantity > 1)
                                <small class="text-muted d-block">Qty: {{ $application->booth_quantity }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="text-primary fw-bold fs-6">RM {{ number_format($application->approved_price ?? $application->requested_price ?? 0, 2) }}</div>
                            <small class="text-muted">{{ $application->booth_size }} × {{ $application->booth_quantity }}</small>
                        </td>
                        <td>
                            <span class="badge bg-{{ $application->status_badge_color ?? 'secondary' }} status-badge">
                                <i class="{{ $application->status_icon ?? 'fas fa-question-circle' }} me-1"></i>
                                {{ ucfirst(str_replace('_', ' ', $application->status ?? 'Pending')) }}
                            </span>
                        </td>
                        <td>
                            <div>{{ $application->created_at->format('M d, Y') ?? 'N/A' }}</div>
                            <small class="text-muted">{{ $application->created_at->diffForHumans() ?? '' }}</small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('vendor.applications.show', $application->id ?? '#') }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                
                                @if(($application->status ?? 'pending') === 'paid')
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Paid
                                    </span>
                                @elseif(($application->status ?? 'pending') === 'approved')
                                    <a href="{{ route('vendor.payment', $application->id) }}" 
                                       class="btn btn-sm btn-success">
                                        <i class="fas fa-credit-card"></i> Payment
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" 
                                            onclick="cancelApplication({{ $application->id ?? 0 }})">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                @elseif(($application->status ?? 'pending') === 'pending')
                                    <button class="btn btn-sm btn-outline-danger" 
                                            onclick="cancelApplication({{ $application->id ?? 0 }})">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-file-alt fa-3x mb-3"></i>
                                <h5>No Applications Yet</h5>
                                <p>You haven't applied to any events yet. Start by browsing available events!</p>
                                <a href="{{ route('vendor.events') }}" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>Browse Events
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.badge {
    font-size: 0.75em;
}

.status-badge {
    font-size: 0.7em !important;
    padding: 0.35em 0.6em !important;
    white-space: nowrap;
    max-width: fit-content;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>
@endsection

@section('scripts')
<script>
function cancelApplication(applicationId) {
    if (confirm('Are you sure you want to cancel this application?')) {
        fetch(`/vendor/applications/${applicationId}/cancel`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to cancel application: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while canceling the application');
        });
    }
}
</script>
@endsection