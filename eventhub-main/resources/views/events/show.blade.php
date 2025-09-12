@extends('layouts.app')

@section('title', $event->name)

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8">
            <!-- Event Image -->
            @if($event->image_url)
                <img src="{{ $event->image_url }}" class="img-fluid rounded mb-4" alt="{{ $event->name }}" style="width: 100%; height: 400px; object-fit: cover;">
            @else
                <div class="bg-light rounded mb-4 d-flex align-items-center justify-content-center" style="height: 400px;">
                    <i class="fas fa-calendar-alt fa-5x text-muted"></i>
                </div>
            @endif

            <!-- Event Details -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h1 class="card-title">{{ $event->name }}</h1>
                        @if($event->is_featured)
                            <span class="badge bg-warning fs-6">Featured</span>
                        @endif
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">
                                <i class="fas fa-calendar text-primary"></i> Date & Time
                            </h6>
                            <p class="mb-3">{{ $event->getFormattedDateTime() }}</p>

                            <h6 class="text-muted mb-1">
                                <i class="fas fa-map-marker-alt text-primary"></i> Venue
                            </h6>
                            <p class="mb-3">{{ $event->venue }}, {{ $event->location }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">
                                <i class="fas fa-tag text-primary"></i> Category
                            </h6>
                            <p class="mb-3">{{ ucfirst($event->category) }}</p>

                            <h6 class="text-muted mb-1">
                                <i class="fas fa-ticket-alt text-primary"></i> Available Tickets
                            </h6>
                            <p class="mb-3">{{ $event->getTotalAvailableTickets() }} tickets</p>
                        </div>
                    </div>

                    <h5 class="mb-3">About This Event</h5>
                    <p class="card-text">{{ $event->description }}</p>

                    @if($event->creator)
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="text-muted mb-2">Organized by</h6>
                            <p class="mb-0">{{ $event->creator->name }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Ticket Selection -->
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header">
                    <h5 class="mb-0">Select Tickets</h5>
                </div>
                <div class="card-body">
                    @if($event->ticketTypes->count() > 0)
                        <form id="ticketSelectionForm">
                            @csrf
                            <input type="hidden" name="event_id" value="{{ $event->id }}">
                            
                            @foreach($event->ticketTypes as $ticketType)
                                <div class="ticket-type mb-4 p-3 border rounded {{ !$ticketType->isAvailableForSale() ? 'opacity-50' : '' }}">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-1">{{ $ticketType->name }}</h6>
                                        <span class="fw-bold text-primary">{{ $ticketType->getFormattedPrice() }}</span>
                                    </div>
                                    
                                    @if($ticketType->description)
                                        <p class="text-muted small mb-2">{{ $ticketType->description }}</p>
                                    @endif
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="quantity-selector">
                                            <label for="quantity_{{ $ticketType->id }}" class="form-label small mb-1">Quantity:</label>
                                            <div class="input-group" style="width: 120px;">
                                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="decreaseQuantity({{ $ticketType->id }})">-</button>
                                                <input type="number" 
                                                       class="form-control form-control-sm text-center" 
                                                       id="quantity_{{ $ticketType->id }}" 
                                                       name="quantities[{{ $ticketType->id }}]" 
                                                       value="0" 
                                                       min="0" 
                                                       max="{{ $ticketType->getRemainingQuantity() }}"
                                                       {{ !$ticketType->isAvailableForSale() ? 'disabled' : '' }}>
                                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="increaseQuantity({{ $ticketType->id }}, {{ $ticketType->getRemainingQuantity() }})">+</button>
                                            </div>
                                        </div>
                                        
                                        <div class="text-end">
                                            <div class="small text-muted">
                                                {{ $ticketType->getRemainingQuantity() }} available
                                                @if($ticketType->max_per_order)
                                                    <br>Max {{ $ticketType->max_per_order }} per order
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if(!$ticketType->isAvailableForSale())
                                        <div class="alert alert-warning small mt-2 mb-0">
                                            @if($ticketType->getRemainingQuantity() <= 0)
                                                Sold Out
                                            @elseif($ticketType->sale_start_date && now() < $ticketType->sale_start_date)
                                                Sales start {{ $ticketType->sale_start_date->format('M d, Y g:i A') }}
                                            @elseif($ticketType->sale_end_date && now() > $ticketType->sale_end_date)
                                                Sales ended {{ $ticketType->sale_end_date->format('M d, Y g:i A') }}
                                            @else
                                                Not available
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            <div class="mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-bold">Total:</span>
                                    <span class="fw-bold text-primary" id="totalPrice">$0.00</span>
                                </div>
                                
                                @auth
                                    <button type="button" class="btn btn-primary w-100" id="addToCartBtn" disabled>
                                        <i class="fas fa-shopping-cart"></i> Add to Cart
                                    </button>
                                @else
                                    <a href="{{ route('auth.firebase') }}" class="btn btn-primary w-100">
                                        <i class="fas fa-sign-in-alt"></i> Login to Book Tickets
                                    </a>
                                @endauth
                            </div>
                        </form>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No tickets available for this event.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-success">
                    <i class="fas fa-check-circle"></i> Success
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tickets have been added to your cart successfully!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continue Shopping</button>
                <a href="{{ route('cart.index') }}" class="btn btn-primary">View Cart</a>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ticketTypes = @json($event->ticketTypes->map(function($ticketType) {
        return [
            'id' => $ticketType->id,
            'price' => (float) $ticketType->price,
            'available' => $ticketType->isAvailableForSale()
        ];
    }));

    function updateTotalPrice() {
        let total = 0;
        ticketTypes.forEach(ticketType => {
            const quantity = parseInt(document.getElementById(`quantity_${ticketType.id}`).value) || 0;
            total += quantity * ticketType.price;
        });
        
        document.getElementById('totalPrice').textContent = 'RM ' + total.toFixed(2);
        
        const addToCartBtn = document.getElementById('addToCartBtn');
        if (addToCartBtn) {
            addToCartBtn.disabled = total === 0;
        }
    }

    // Update total price when quantities change
    ticketTypes.forEach(ticketType => {
        const input = document.getElementById(`quantity_${ticketType.id}`);
        if (input) {
            input.addEventListener('change', updateTotalPrice);
            input.addEventListener('input', updateTotalPrice);
        }
    });

    // Add to cart functionality
    const addToCartBtn = document.getElementById('addToCartBtn');
    const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
    
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            
            if (!isAuthenticated) {
                alert('Please login to add tickets to cart.');
                window.location.href = '{{ route("auth.firebase") }}';
                return;
            }
            
            // User is authenticated, proceed with cart logic
            const selectedTickets = [];
            ticketTypes.forEach(ticketType => {
                const quantity = parseInt(document.getElementById(`quantity_${ticketType.id}`).value) || 0;
                if (quantity > 0) {
                    selectedTickets.push({
                        ticket_type_id: ticketType.id,
                        quantity: quantity
                    });
                }
            });

            if (selectedTickets.length === 0) {
                alert('Please select at least one ticket.');
                return;
            }

            console.log('Selected tickets:', selectedTickets);
            
            // Add each ticket type separately
            let completedRequests = 0;
            let hasError = false;
            const totalRequests = selectedTickets.length;

            selectedTickets.forEach((ticket, index) => {
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('event_id', {{ $event->id }});
                formData.append('ticket_type_id', ticket.ticket_type_id);
                formData.append('quantity', ticket.quantity);

                fetch('{{ route("cart.add") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    completedRequests++;
                    
                    if (!data.success) {
                        hasError = true;
                        console.error('Error adding ticket:', data.error);
                    }
                    
                    // Check if all requests are completed
                    if (completedRequests === totalRequests) {
                        if (hasError) {
                            alert('Some tickets could not be added to cart. Please try again.');
                        } else {
                            // Reset form
                            ticketTypes.forEach(ticketType => {
                                document.getElementById(`quantity_${ticketType.id}`).value = 0;
                            });
                            updateTotalPrice();
                            
                            // Show success modal
                            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                            successModal.show();
                        }
                    }
                })
                .catch(error => {
                    completedRequests++;
                    hasError = true;
                    console.error('Error:', error);
                    
                    if (completedRequests === totalRequests) {
                        alert('An error occurred. Please try again.');
                    }
                });
            });
        });
    }
});

function increaseQuantity(ticketTypeId, maxQuantity) {
    const input = document.getElementById(`quantity_${ticketTypeId}`);
    const currentValue = parseInt(input.value) || 0;
    if (currentValue < maxQuantity) {
        input.value = currentValue + 1;
        input.dispatchEvent(new Event('change'));
    }
}

function decreaseQuantity(ticketTypeId) {
    const input = document.getElementById(`quantity_${ticketTypeId}`);
    const currentValue = parseInt(input.value) || 0;
    if (currentValue > 0) {
        input.value = currentValue - 1;
        input.dispatchEvent(new Event('change'));
    }
}
</script>
