@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Shopping Cart</h1>
                <a href="{{ route('events.index') }}" class="btn btn-outline-primary">
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
                                    <div class="cart-item p-4 border-bottom {{ !$item->isValid() ? 'bg-light' : '' }}">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <h6 class="mb-1">{{ $item->event->name }}</h6>
                                                <p class="text-muted small mb-1">
                                                    <i class="fas fa-calendar"></i> {{ $item->event->getFormattedDateTime() }}
                                                </p>
                                                <p class="text-muted small mb-1">
                                                    <i class="fas fa-map-marker-alt"></i> {{ $item->event->venue }}
                                                </p>
                                                <p class="mb-0">
                                                    <strong>{{ $item->ticketType->name }}</strong>
                                                    @if($item->ticketType->description)
                                                        <br><small class="text-muted">{{ $item->ticketType->description }}</small>
                                                    @endif
                                                </p>
                                                
                                                @if(!$item->isValid())
                                                    <div class="alert alert-warning small mt-2 mb-0">
                                                        <i class="fas fa-exclamation-triangle"></i> This item is no longer available
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
                                                    <span class="fw-bold">{{ $item->ticketType->getFormattedPrice() }}</span>
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
                    <a href="{{ route('events.index') }}" class="btn btn-primary">
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
});

// Quantity update function removed - quantities are now display-only

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
    
    if (validItems.length === 0) {
        alert('Please add valid items to your cart before proceeding to checkout.');
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
    if (checkoutBtn) {
        // Check if there are any valid items in the cart
        const validItems = document.querySelectorAll('.cart-item:not(.bg-light)');
        const invalidItems = document.querySelectorAll('.cart-item.bg-light');
        
        // Only disable if there are NO valid items at all
        checkoutBtn.disabled = validItems.length === 0;
        
        if (invalidItems.length > 0 && validItems.length > 0) {
            // Mixed valid and invalid items - allow checkout but warn
            checkoutBtn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Proceed to Checkout (Some items unavailable)';
        } else if (invalidItems.length > 0 && validItems.length === 0) {
            // Only invalid items - disable checkout
            checkoutBtn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Remove Invalid Items First';
        } else {
            // All items valid - normal checkout
            checkoutBtn.innerHTML = '<i class="fas fa-credit-card"></i> Proceed to Checkout';
        }
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
