@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Shopping Cart</h1>
                <a href="{{ route('customer.events.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
            </div>

            @if($cart->items->count() > 0)
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Cart Items -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Cart Items ({{ $cart->getTotalItems() }})</h5>
                            </div>
                            <div class="card-body p-0">
                                @foreach($cart->items as $item)
                                    @php
                                        $eventDate = $item->event->start_date ? $item->event->start_date : $item->event->start_time;
                                        $isEventExpired = $eventDate && $eventDate->isPast();
                                        $isSoldOut = $item->event->available_tickets < $item->quantity;
                                    @endphp
                                    <div class="cart-item p-4 border-bottom {{ !$item->isValid() ? 'bg-light' : '' }}">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <h6 class="mb-1">{{ $item->event->name }}</h6>
                                                <p class="text-muted small mb-1">
                                                    <i class="fas fa-calendar"></i> 
                                                    @if($item->event->start_date)
                                                        {{ $item->event->start_date->format('M d, Y') }}
                                                    @elseif($item->event->start_time)
                                                        {{ $item->event->start_time->format('M d, Y') }}
                                                    @else
                                                        Date TBA
                                                    @endif
                                                </p>
                                                <p class="text-muted small mb-1">
                                                    <i class="fas fa-map-marker-alt"></i> 
                                                    @if($item->event->venue)
                                                        {{ $item->event->venue->name }}
                                                    @else
                                                        Venue TBA
                                                    @endif
                                                </p>
                                                <p class="mb-0">
                                                    <strong>General Admission</strong>
                                                    <br><small class="text-muted">Standard event ticket</small>
                                                </p>
                                                
                                                @if($isEventExpired)
                                                    <div class="alert alert-danger small mt-2 mb-0">
                                                        <i class="fas fa-calendar-times"></i> <strong>Event has passed</strong> - This event is no longer available for booking
                                                    </div>
                                                @elseif($isSoldOut)
                                                    <div class="alert alert-warning small mt-2 mb-0">
                                                        <i class="fas fa-exclamation-triangle"></i> <strong>Sold Out</strong> - Not enough tickets available
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="text-muted">Quantity:</span>
                                                    <span class="fw-bold">{{ $item->quantity }}</span>
                                                </div>
                                                
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="text-muted">Price:</span>
                                                    <span class="fw-bold">RM {{ number_format($item->price, 2) }}</span>
                                                </div>
                                                
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span class="fw-bold">Total:</span>
                                                    <span class="fw-bold text-primary">{{ $item->getFormattedTotalPrice() }}</span>
                                                </div>
                                                
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-outline-danger btn-sm" 
                                                            onclick="removeItem({{ $item->id }})">
                                                        <i class="fas fa-trash"></i> Remove
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Order Summary -->
                        <div class="card sticky-top" style="top: 20px;">
                            <div class="card-header">
                                <h5 class="mb-0">Order Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Items ({{ $cart->getTotalItems() }}):</span>
                                    <span id="subtotal">{{ $cart->getFormattedTotalPrice() }}</span>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Service Fee:</span>
                                    <span id="serviceFee">RM 0.00</span>
                                </div>
                                
                                <hr>
                                
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <span class="fw-bold">Total:</span>
                                    <span class="fw-bold text-primary" id="total">{{ $cart->getFormattedTotalPrice() }}</span>
                                </div>

                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary btn-lg" id="checkoutBtn" onclick="proceedToCheckout()" type="button">
                                        <i class="fas fa-credit-card"></i> Proceed to Checkout
                                    </button>
                                    
                                    <button class="btn btn-outline-warning" id="removeExpiredBtn" onclick="removeExpiredEvents()" style="display: none;">
                                        <i class="fas fa-calendar-times"></i> Remove Expired Events
                                    </button>
                                    
                                    <button class="btn btn-outline-danger" onclick="clearCart()">
                                        <i class="fas fa-trash"></i> Clear Cart
                                    </button>
                                </div>

                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt"></i> Secure checkout powered by our payment partners
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty Cart -->
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">Your cart is empty</h4>
                    <p class="text-muted mb-4">Looks like you haven't added any events to your cart yet.</p>
                    <a href="{{ route('customer.events.index') }}" class="btn btn-primary">
                        <i class="fas fa-calendar"></i> Browse Events
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="confirmModalBody">
                <!-- Content will be set dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmAction">Confirm</button>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    updateCheckoutButton();
    checkForExpiredEvents();
});

function checkForExpiredEvents() {
    const expiredItems = document.querySelectorAll('.cart-item .alert-danger');
    if (expiredItems.length > 0) {
        // Show a notification about expired events
        const notification = document.createElement('div');
        notification.className = 'alert alert-danger alert-dismissible fade show position-fixed';
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);';
        notification.innerHTML = `
            <i class="fas fa-calendar-times me-2"></i>
            <strong>Expired Events Detected!</strong><br>
            <small>Some events in your cart have passed their date. Please remove them to proceed to checkout.</small>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(notification);
        
        // Auto-hide after 10 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 10000);
    }
}

function removeItem(itemId) {
    showConfirmModal(
        'Remove Item',
        'Are you sure you want to remove this item from your cart?',
        function() {
            fetch(`/cart/items/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the page without full reload
                    location.reload();
                } else {
                    alert(data.error || 'An error occurred. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }
    );
}

function clearCart() {
    if (confirm('Are you sure you want to remove all items from your cart?')) {
        fetch('/cart/clear', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.error || 'An error occurred. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
}

function showConfirmModal(title, message, confirmCallback) {
    document.querySelector('#confirmModal .modal-title').textContent = title;
    document.getElementById('confirmModalBody').textContent = message;
    
    const confirmAction = document.getElementById('confirmAction');
    confirmAction.onclick = function() {
        confirmCallback();
        bootstrap.Modal.getInstance(document.getElementById('confirmModal')).hide();
    };
    
    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    modal.show();
}

function proceedToCheckout() {
    // Check if there are any valid items in the cart
    const validItems = document.querySelectorAll('.cart-item:not(.bg-light)');
    const invalidItems = document.querySelectorAll('.cart-item.bg-light');
    const expiredItems = document.querySelectorAll('.cart-item .alert-danger');
    
    if (validItems.length === 0) {
        alert('Please add valid items to your cart before proceeding to checkout.');
        return;
    }
    
    // Check if there are any expired events
    if (expiredItems.length > 0) {
        alert('You cannot proceed to checkout with expired events in your cart. Please remove the expired events first.');
        return;
    }
    
    // Get the payment form URL
    const paymentUrl = '{{ route("event-booking.payment-form-yf") }}';
    
    if (invalidItems.length > 0) {
        if (confirm('Some items in your cart are no longer available. Do you want to proceed with only the available items?')) {
            window.location.href = paymentUrl;
        }
    } else {
        window.location.href = paymentUrl;
    }
}

function updateCheckoutButton() {
    const checkoutBtn = document.getElementById('checkoutBtn');
    const removeExpiredBtn = document.getElementById('removeExpiredBtn');
    
    if (checkoutBtn) {
        // Check if there are any valid items in the cart
        const validItems = document.querySelectorAll('.cart-item:not(.bg-light)');
        const invalidItems = document.querySelectorAll('.cart-item.bg-light');
        const expiredItems = document.querySelectorAll('.cart-item .alert-danger');
        
        // Disable checkout if there are expired events or no valid items
        const hasExpiredEvents = expiredItems.length > 0;
        const hasNoValidItems = validItems.length === 0;
        
        checkoutBtn.disabled = hasExpiredEvents || hasNoValidItems;
        
        // Show/hide remove expired button
        if (removeExpiredBtn) {
            removeExpiredBtn.style.display = hasExpiredEvents ? 'block' : 'none';
        }
        
        if (hasExpiredEvents) {
            // Expired events present - disable checkout
            checkoutBtn.innerHTML = '<i class="fas fa-calendar-times"></i> Remove Expired Events First';
            checkoutBtn.className = 'btn btn-danger btn-lg';
        } else if (invalidItems.length > 0 && validItems.length > 0) {
            // Mixed valid and invalid items - allow checkout but warn
            checkoutBtn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Proceed to Checkout (Some items unavailable)';
            checkoutBtn.className = 'btn btn-warning btn-lg';
        } else if (invalidItems.length > 0 && validItems.length === 0) {
            // Only invalid items - disable checkout
            checkoutBtn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Remove Invalid Items First';
            checkoutBtn.className = 'btn btn-danger btn-lg';
        } else {
            // All items valid - normal checkout
            checkoutBtn.innerHTML = '<i class="fas fa-credit-card"></i> Proceed to Checkout';
            checkoutBtn.className = 'btn btn-primary btn-lg';
        }
    }
}

function removeExpiredEvents() {
    const expiredItems = document.querySelectorAll('.cart-item .alert-danger');
    if (expiredItems.length === 0) {
        return;
    }
    
    if (confirm(`Are you sure you want to remove ${expiredItems.length} expired event(s) from your cart?`)) {
        // Get all expired cart items
        const expiredCartItems = [];
        expiredItems.forEach(alert => {
            const cartItem = alert.closest('.cart-item');
            const removeBtn = cartItem.querySelector('button[onclick*="removeItem"]');
            if (removeBtn) {
                const onclickAttr = removeBtn.getAttribute('onclick');
                const itemIdMatch = onclickAttr.match(/removeItem\((\d+)\)/);
                if (itemIdMatch) {
                    expiredCartItems.push(parseInt(itemIdMatch[1]));
                }
            }
        });
        
        // Remove all expired items
        let removedCount = 0;
        const removePromises = expiredCartItems.map(itemId => {
            return fetch(`/cart/items/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    removedCount++;
                }
            });
        });
        
        Promise.all(removePromises).then(() => {
            if (removedCount > 0) {
                location.reload();
            } else {
                alert('Failed to remove some expired events. Please try again.');
            }
        });
    }
}

// Add checkout button click handler (backup)
document.addEventListener('DOMContentLoaded', function() {
    const checkoutBtn = document.getElementById('checkoutBtn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function(e) {
            if (!this.disabled) {
                window.location.href = '{{ route("event-booking.payment-form-yf") }}';
            } else {
                e.preventDefault();
            }
        });
    }
});
</script>