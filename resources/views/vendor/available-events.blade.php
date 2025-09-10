@extends('layouts.vendor')

@section('title', 'Available Events - EventHub')
@section('page-title', 'Available Events')
@section('page-description', 'Browse and apply to available events')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Available Events</h1>
    </div>

    @if($events->count() > 0)
        <div class="row">
            @foreach($events as $event)
                @php
                    // Get all applications for this event (excluding cancelled applications)
                    $existingApplications = collect();
                    if (isset($vendor) && $vendor) {
                        $existingApplications = $vendor->eventApplications()
                            ->where('event_id', $event->id)
                            ->where('status', '!=', 'cancelled')
                            ->get();
                    }
                    $hasApplications = $existingApplications->count() > 0;
                @endphp
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ $event->name }}</h6>
                            <div>
                                <span class="badge bg-success status-badge d-flex align-items-center">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Available
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Event Status -->
                            <div class="mb-2">
                                <span class="badge bg-{{ $event->status === 'active' ? 'success' : 'secondary' }} me-2">
                                    {{ ucfirst($event->status) }}
                                </span>
                                <span class="badge bg-info">Event</span>
                            </div>
                            
                            <!-- Event Details -->
                            <div class="mb-2">
                                <strong>Date:</strong> {{ $event->start_time->format('M d, Y') }}
                                @if($event->end_time && $event->start_time->format('M d') !== $event->end_time->format('M d'))
                                    - {{ $event->end_time->format('M d, Y') }}
                                @endif
                            </div>
                            <div class="mb-2">
                                <strong>Time:</strong> {{ $event->start_time->format('H:i') }}
                                @if($event->end_time)
                                    - {{ $event->end_time->format('H:i') }}
                                @endif
                            </div>
                            <div class="mb-2">
                                <strong>Location:</strong> {{ $event->venue->name ?? 'TBD' }}
                                @if($event->venue)
                                    <small class="text-muted">({{ $event->venue->type ?? 'Venue' }})</small>
                                @endif
                            </div>
                            <div class="mb-2">
                                <strong>Organizer:</strong> {{ $event->organizer ?? 'N/A' }}
                            </div>
                            
                            <!-- Event Description -->
                            @if($event->description)
                                <div class="mb-2">
                                    <strong>Description:</strong>
                                    <p class="text-muted small">{{ Str::limit($event->description, 100) }}</p>
                                </div>
                            @endif
                            
                            <!-- Pricing & Availability -->
                            <div class="mb-3">
                                <div class="pricing-info bg-light p-2 rounded">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong class="text-primary">RM {{ number_format($event->booth_price ?? 0, 2) }}</strong>
                                            <small class="text-muted d-block">Per booth</small>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted">
                                                <i class="fas fa-hashtag me-1"></i>
                                                {{ $event->available_booths }}/{{ $event->booth_quantity }} available
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Basic Event Info -->
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-2">
                                        <strong>Event Type:</strong><br>
                                        <span class="text-primary">General Event</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-2">
                                        <strong>Status:</strong><br>
                                        @if($event->hasAvailableSlots())
                                            <span class="text-success">Open for Applications</span>
                                        @else
                                            <span class="text-warning">Fully Booked</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex gap-2 flex-wrap">
                                @if($event->status === 'active')
                                    <a href="{{ route('vendor.events.apply', $event->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-paper-plane me-1"></i>Apply Now
                                    </a>
                                @else
                                    <button class="btn btn-secondary btn-sm" disabled>
                                        <i class="fas fa-times me-1"></i>Not Available
                                    </button>
                                @endif
                                
                                <a href="{{ route('vendor.events.show', $event->id) }}" class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-info-circle me-1"></i>Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
            <h4 class="text-muted">No Available Events</h4>
            <p class="text-muted">There are currently no events available for application.</p>
        </div>
    @endif
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

.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
</style>
@endsection

@section('scripts')
@endsection
