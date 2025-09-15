@extends('layouts.app')

@section('title', 'Event Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('customer.events.index') }}">Events</a></li>
                    <li class="breadcrumb-item active">{{ $event->name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Event Details -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div id="event-content">
                        <h1 class="card-title">{{ $event->name }}</h1>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6><i class="fas fa-calendar text-primary"></i> Date & Time</h6>
                                <p>
                                    @if($event->start_date)
                                        <i class="fas fa-calendar"></i> {{ $event->start_date->format('M d, Y') }}
                                    @elseif($event->start_time)
                                        <i class="fas fa-calendar"></i> {{ $event->start_time->format('M d, Y') }}
                                    @else
                                        <i class="fas fa-calendar"></i> Date TBA
                                    @endif
                                    <br>
                                    @if($event->start_time)
                                        <i class="fas fa-clock"></i> {{ $event->start_time->format('g:i A') }}
                                    @else
                                        <i class="fas fa-clock"></i> Time TBA
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-map-marker-alt text-primary"></i> Venue</h6>
                                <p>
                                    @if($event->venue)
                                        <i class="fas fa-map-marker-alt"></i> {{ $event->venue->name }}
                                    @else
                                        <i class="fas fa-map-marker-alt"></i> TBA
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if($event->description)
                        <div class="mb-4">
                            <h6>Event Description</h6>
                            <p>{{ $event->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Add to Cart Section -->
        <div class="col-lg-4">
            @if($ticketInfo)
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-ticket-alt me-2"></i>Ticket Information</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h6 class="text-success">General Admission</h6>
                        <p class="text-muted mb-2">Standard event ticket</p>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="h4 text-success mb-0">RM {{ $ticketInfo ? number_format($ticketInfo['ticket_price'], 2) : '0.00' }}</span>
                            <span class="badge bg-success">Available</span>
                        </div>
                    </div>

                    <!-- Ticket Statistics - Improved Design -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Ticket Availability</span>
                            <span class="badge bg-light text-dark">{{ $ticketInfo ? $ticketInfo['ticket_quantity'] : 0 }} Total</span>
                        </div>
                        
                        
                        <!-- Statistics Cards -->
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="card bg-success text-white h-100">
                                    <div class="card-body text-center p-2">
                                        <div class="h2 text-white mb-1 fw-bold">{{ $ticketInfo ? $ticketInfo['ticket_quantity'] : 0 }}</div>
                                        <div class="small text-white fw-bold">Tickets Available</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-secondary text-white h-100">
                                    <div class="card-body text-center p-2">
                                        <div class="h2 text-white mb-1 fw-bold">{{ $ticketInfo ? $ticketInfo['ticket_sold'] : 0 }}</div>
                                        <div class="small text-white fw-bold">Sold</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add to Cart Form - Improved Quantity Input -->
                    @php
                        $eventDate = $event->start_date ? $event->start_date : $event->start_time;
                        $isEventPassed = $eventDate && $eventDate->isPast();
                    @endphp
                    
                    @if(isset($ticketInfo) && $ticketInfo && $ticketInfo['ticket_quantity'] > 0 && !$isEventPassed)
                        <form id="addToCartForm">
                            @csrf
                            <input type="hidden" name="event_id" value="{{ $event->id }}">
                            
                            <!-- Maximum Ticket Limit Message -->
                            <div class="alert alert-info alert-sm py-2 mb-3">
                                <i class="fas fa-info-circle me-1"></i>
                                <small>Maximum {{ $ticketInfo ? min(5, $ticketInfo['ticket_quantity']) : 0 }} tickets per order ({{ $ticketInfo ? $ticketInfo['ticket_quantity'] : 0 }} available)</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="quantity" class="form-label small">Quantity:</label>
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" id="decreaseBtn">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" 
                                           name="quantity" 
                                           id="quantity" 
                                           class="form-control form-control-sm text-center" 
                                           value="1" 
                                           min="1" 
                                           max="{{ $ticketInfo ? min(5, $ticketInfo['ticket_quantity']) : 0 }}" 
                                           required>
                                    <button class="btn btn-outline-secondary btn-sm" type="button" id="increaseBtn">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <div class="form-text small text-muted">
                                    Available: {{ $ticketInfo ? $ticketInfo['available_tickets'] : 0 }} tickets
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-success btn-sm w-100" id="addToCartBtn">
                                <i class="fas fa-shopping-cart me-1"></i>Add to Cart
                            </button>
                        </form>
                    @elseif($isEventPassed)
                        <div class="alert alert-danger text-center">
                            <i class="fas fa-calendar-times me-2"></i>
                            <strong>Event has passed</strong><br>
                            <small>This event is no longer available for booking</small>
                        </div>
                    @else
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Sold Out</strong><br>
                            This event is sold out.
                        </div>
                    @endif
                </div>
            </div>
            @else
            <!-- No tickets available -->
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Ticket Information</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Sold Out</strong><br>
                        This event is sold out.
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addToCartForm = document.getElementById('addToCartForm');
    const addToCartBtn = document.getElementById('addToCartBtn');
    const quantityInput = document.getElementById('quantity');
    const decreaseBtn = document.getElementById('decreaseBtn');
    const increaseBtn = document.getElementById('increaseBtn');
    
    // Quantity input controls
    if (decreaseBtn && increaseBtn && quantityInput) {
        decreaseBtn.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        });
        
        increaseBtn.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value);
            let maxValue = parseInt(quantityInput.getAttribute('max'));
            if (currentValue < maxValue) {
                quantityInput.value = currentValue + 1;
            }
        });
        
        // Direct input validation
        quantityInput.addEventListener('input', function() {
            let value = parseInt(this.value);
            let maxValue = parseInt(this.getAttribute('max'));
            let minValue = parseInt(this.getAttribute('min'));
            
            if (isNaN(value) || value < minValue) {
                this.value = minValue;
            } else if (value > maxValue) {
                this.value = maxValue;
            }
        });
    }
    
    if (addToCartForm) {
        addToCartForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Disable button to prevent double submission
            addToCartBtn.disabled = true;
            addToCartBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';
            
            const formData = new FormData(this);
            
            fetch('{{ route("cart.add") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showAlert('success', data.message);
                    
                    // Update sold tickets display
                    updateAvailableTickets(data.cart || { 
                        total_items: 0, 
                        ticket_sold: data.ticket_sold,
                        total_tickets: data.total_tickets || {{ $ticketInfo ? $ticketInfo['ticket_quantity'] : 0 }}
                    });
                } else {
                    showAlert('error', data.error || 'Failed to add item to cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'An error occurred. Please try again.');
            })
            .finally(() => {
                // Re-enable button
                addToCartBtn.disabled = false;
                addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart me-2"></i>Add to Cart';
            });
        });
    }
});

function showAlert(type, message) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert-custom');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create new alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show alert-custom position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);';
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at the end of body
    document.body.appendChild(alertDiv);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

function updateAvailableTickets(cartData) {
    // Update cart count in navigation if it exists
    const cartCount = document.querySelector('.cart-count');
    if (cartCount) {
        cartCount.textContent = cartData.total_items || 0;
    }
    
    // Update sold tickets display if we have the updated count
    if (cartData.ticket_sold !== undefined) {
        const soldTicketsElement = document.querySelector('.card.bg-secondary .h2');
        if (soldTicketsElement) {
            soldTicketsElement.textContent = cartData.ticket_sold;
        }
        
        // Progress bar removed - no need to update
    }
    
    // Note: Available tickets always equals total ticket quantity
    // No need to update available tickets display or cart validation
}
</script>
@endsection
