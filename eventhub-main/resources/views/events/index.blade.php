@extends('layouts.app')

@section('title', 'Events')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Available Events ({{ $events->total() }} found)</h1>
            </div>


            <!-- Events Grid -->
            <div class="row" id="eventsGrid">
                @forelse($events as $event)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 event-card" data-event-id="{{ $event->id }}">
                            @if($event->image_url)
                                <img src="{{ $event->image_url }}" class="card-img-top" alt="{{ $event->name }}" style="height: 200px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-calendar-alt fa-3x text-muted"></i>
                                </div>
                            @endif
                            
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0">{{ $event->name }}</h5>
                                    @if($event->is_featured)
                                        <span class="badge bg-warning">Featured</span>
                                    @endif
                                </div>
                                
                                <p class="card-text text-muted small mb-2">
                                    <i class="fas fa-calendar"></i> {{ $event->getFormattedDateTime() }}
                                </p>
                                
                                <p class="card-text text-muted small mb-2">
                                    <i class="fas fa-map-marker-alt"></i> {{ $event->venue }}, {{ $event->location }}
                                </p>
                                
                                <p class="card-text text-muted small mb-2">
                                    <i class="fas fa-tag"></i> {{ ucfirst($event->category) }}
                                </p>
                                
                                <p class="card-text">{{ Str::limit($event->description, 100) }}</p>
                                
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted small">
                                            {{ $event->getTotalAvailableTickets() }} tickets available
                                        </span>
                                        <span class="fw-bold text-primary">
                                            From {{ $event->ticketTypes->min('price') ? 'RM ' . number_format($event->ticketTypes->min('price'), 2) : 'TBA' }}
                                        </span>
                                    </div>
                                    
                                    <a href="{{ route('events.show', $event) }}" class="btn btn-primary w-100">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No events found</h4>
                            <p class="text-muted">Try adjusting your filters or search terms.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($events->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Cart Summary Modal -->
<div class="modal fade" id="cartModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cart Summary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="cartModalBody">
                <!-- Cart content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continue Shopping</button>
                <a href="{{ route('cart.index') }}" class="btn btn-primary">View Cart</a>
            </div>
        </div>
    </div>
</div>
@endsection

