@extends('layouts.vendor')

@section('title', 'My Bookings - EventHub')
@section('page-title', 'My Bookings')
@section('page-description', 'Manage your confirmed booth bookings and reservations')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>My Bookings</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('vendor.events') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Book New Booth
        </a>
    </div>
</div>

<!-- Bookings Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            Bookings 
            <span class="badge bg-success">{{ $bookings->count() }}</span>
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Event</th>
                        <th>Requested Booth</th>
                        <th>Event Date</th>
                        <th>Venue</th>
                        <th>Paid Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr>
                        <td>
                            <strong>#{{ $booking->id }}</strong>
                        </td>
                        <td>
                            <div>
                                <strong>{{ $booking->event->name ?? 'Event' }}</strong>
                            </div>
                            <small class="text-muted">{{ $booking->event->description ?? 'No description available' }}</small>
                        </td>
                        <td>
                            <div><strong>{{ strtoupper($booking->booth_size ?? 'N/A') }}</strong></div>
                            <small class="text-muted">Qty: {{ $booking->booth_quantity ?? 1 }}</small>
                        </td>
                        <td>
                            <div>{{ optional($booking->event->start_time)->format('Y-m-d') ?? 'TBD' }}</div>
                            <small class="text-muted">{{ optional($booking->event->start_time)->format('H:i') ?? '' }}</small>
                        </td>
                        <td>
                            <div>{{ $booking->event->venue->name ?? 'TBD' }}</div>
                            <small class="text-muted">{{ $booking->event->venue->address ?? '' }}</small>
                        </td>
                        <td>
                            @php
                                $amount = $booking->approved_price ?? $booking->requested_price ?? 0;
                            @endphp
                            <div><strong>RM {{ number_format($amount, 2) }}</strong></div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $booking->status_badge_color ?? 'secondary' }} status-badge d-flex align-items-center">
                                <i class="{{ $booking->status_icon ?? 'fas fa-question-circle' }} me-1"></i>
                                {{ ucfirst(str_replace('_', ' ', $booking->status ?? 'unknown')) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('vendor.applications.show', $booking->id) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-calendar-check fa-3x mb-3"></i>
                                <h5>No Bookings Yet</h5>
                                <p>You don't have any confirmed booth bookings yet. Apply to events to get started!</p>
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
function cancelBooking(bookingId) {
    if (confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) {
        fetch(`/vendor/bookings/${bookingId}/cancel`, {
            method: 'POST',
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
                alert('Failed to cancel booking: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while canceling the booking');
        });
    }
}
</script>
@endsection