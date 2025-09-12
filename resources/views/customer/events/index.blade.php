@extends('layouts.app')

@section('title', 'Events - Customer Dashboard')

@push('styles')
<style>
.event-card {
    transition: all 0.3s ease;
    border-radius: 15px;
    overflow: hidden;
}

.event-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.event-card .card-img-top {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transition: all 0.3s ease;
}

.event-card:hover .card-img-top {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

.event-card .btn {
    transition: all 0.3s ease;
}

.event-card .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,123,255,0.3);
}

.event-meta i {
    width: 16px;
    text-align: center;
}

.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    border-radius: 0 0 20px 20px;
}

.page-header h1 {
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.page-header p {
    opacity: 0.9;
    margin-bottom: 0;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1>Available Events</h1>
                    <p>Discover amazing events happening around you</p>
                </div>
            </div>
        </div>
    </div>


    <!-- Events Grid -->
    <div class="container">
        <div class="row" id="events-container">
        @forelse($events as $event)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 event-card shadow-sm border-0" data-event-id="{{ $event->id }}" style="transition: all 0.3s ease; border-radius: 15px; overflow: hidden;">
                <!-- Event Image Placeholder -->
                <div class="card-img-top bg-gradient-primary text-white d-flex align-items-center justify-content-center" style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="text-center">
                        <i class="fas fa-calendar-alt fa-3x mb-2"></i>
                        <h6 class="mb-0">Event</h6>
                    </div>
                </div>
                
                <div class="card-body d-flex flex-column p-4">
                    <div class="flex-grow-1">
                        <h5 class="card-title fw-bold text-dark mb-3" style="font-size: 1.1rem; line-height: 1.3;">
                            {{ Str::limit($event->name, 50) }}
                        </h5>
                        
                        <div class="event-meta mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-calendar text-primary me-2"></i>
                                <span class="text-muted small">
                                    @if($event->start_date)
                                        {{ $event->start_date->format('M d, Y') }}
                                    @elseif($event->start_time)
                                        {{ $event->start_time->format('M d, Y') }}
                                    @else
                                        Date TBA
                                    @endif
                                </span>
                            </div>
                            
                            @if($event->start_time)
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-clock text-success me-2"></i>
                                <span class="text-muted small">{{ $event->start_time->format('g:i A') }}</span>
                            </div>
                            @endif
                            
                            @if($event->venue)
                            <div class="d-flex align-items-center">
                                <i class="fas fa-map-marker-alt text-warning me-2"></i>
                                <span class="text-muted small">{{ Str::limit($event->venue->name, 30) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-auto">
                        <a href="{{ route('customer.events.show', $event) }}" class="btn btn-primary w-100 rounded-pill py-2" style="font-weight: 600; transition: all 0.3s ease;">
                            <i class="fas fa-eye me-2"></i>View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                <h4>No Events Available</h4>
                <p>There are currently no upcoming events. Please check back later.</p>
            </div>
        </div>
        @endforelse
        </div>

        <!-- Pagination -->
        @if($events->hasPages())
        <div class="row">
            <div class="col-12">
                {{ $events->links() }}
            </div>
        </div>
        @endif
    </div>
</div>


@endsection

@push('scripts')
<script>
// All data is now loaded server-side, no JavaScript needed for data loading
// The page displays immediately with all information
</script>
@endpush