@extends('layouts.app')

@section('title', 'Stripe Payment')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h2 class="mb-0">
                        <i class="fab fa-stripe me-2"></i>Stripe Payment
                    </h2>
                    <p class="mb-0 mt-2">Secure payment processing</p>
                </div>
                
                <div class="card-body p-5">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-primary mb-4">Order Details</h5>
                            <div class="bg-light p-3 rounded mb-4">
                                <p class="mb-2"><strong>Order Number:</strong> {{ $order->order_number }}</p>
                                <p class="mb-2"><strong>Event:</strong> {{ $order->event->name }}</p>
                                <p class="mb-2"><strong>Customer:</strong> {{ $order->customer_name }}</p>
                                <p class="mb-2"><strong>Email:</strong> {{ $order->customer_email }}</p>
                                <p class="mb-2"><strong>Quantity:</strong> {{ $order->ticket_quantity }} ticket(s)</p>
                                <p class="mb-0"><strong>Total Amount:</strong> <span class="text-success fw-bold">RM {{ number_format($order->total_amount, 2) }}</span></p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="text-primary mb-4">Payment Information</h5>
                            <form id="stripePaymentForm" method="POST" action="{{ route('payment-gateway.process') }}">
                                @csrf
                                <input type="hidden" name="payment_method" value="stripe">
                                <input type="hidden" name="order_number" value="{{ $order->order_number }}">
                                
                                <div class="mb-3">
                                    <label for="card_number" class="form-label">Card Number</label>
                                    <input type="text" class="form-control card-number-input" id="card_number" name="card_number" 
                                           placeholder="1234 5678 9012 3456" maxlength="19" required>
                                    <div class="form-text">Enter 16 digits, spaces will be added automatically</div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="expiry_date" class="form-label">Expiry Date</label>
                                        <input type="text" class="form-control" id="expiry_date" name="expiry_date" 
                                               placeholder="MM/YY" maxlength="5" required>
                                        <div class="form-text">Format: MM/YY</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="cvv" class="form-label">CVV</label>
                                        <input type="text" class="form-control" id="cvv" name="cvv" 
                                               placeholder="123" maxlength="4" required>
                                        <div class="form-text">3-4 digits on back of card</div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="cardholder_name" class="form-label">Cardholder Name</label>
                                    <input type="text" class="form-control" id="cardholder_name" name="cardholder_name" 
                                           value="{{ $order->customer_name }}" required>
                                </div>
                                
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg" id="processPaymentBtn">
                                        <i class="fas fa-credit-card me-2"></i>Pay RM {{ number_format($order->total_amount, 2) }}
                                    </button>
                                    <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Cart
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer bg-light text-center py-3">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i>
                        Your payment information is secure and encrypted. We use Stripe for secure payment processing.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    .form-control.is-valid {
        border-color: #198754;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='m2.3 6.73.94-.94 1.06 1.06L6.73 4.3l.94.94L4.3 8.73z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    
    .form-control.is-invalid {
        border-color: #dc3545;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6 1.4 1.4 1.4-1.4'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    
    .form-text {
        font-size: 0.875em;
        color: #6c757d;
    }
    
    .card-number-input {
        font-family: 'Courier New', monospace;
        letter-spacing: 1px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('stripePaymentForm');
    const processBtn = document.getElementById('processPaymentBtn');
    
    if (!form || !processBtn) {
        return;
    }
    
    // Format card number input - auto add spaces every 4 digits
    const cardNumberInput = document.getElementById('card_number');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
        let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
        e.target.value = formattedValue;
        
        // Limit to 19 characters (16 digits + 3 spaces)
        if (e.target.value.length > 19) {
            e.target.value = e.target.value.substring(0, 19);
        }
        
        // Real-time validation
        const cardNumber = value.replace(/\s/g, '');
        if (cardNumber.length === 16) {
            e.target.classList.remove('is-invalid');
            e.target.classList.add('is-valid');
        } else if (cardNumber.length > 0) {
            e.target.classList.remove('is-valid');
            e.target.classList.add('is-invalid');
        } else {
            e.target.classList.remove('is-valid', 'is-invalid');
        }
    });
    }
    
    // Format expiry date input - auto add / after 2 digits
    const expiryInput = document.getElementById('expiry_date');
    if (expiryInput) {
        expiryInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        
        // Auto-add slash after 2 digits
        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        
        e.target.value = value;
        
        // Limit to 5 characters (MM/YY)
        if (e.target.value.length > 5) {
            e.target.value = e.target.value.substring(0, 5);
        }
        
        // Real-time validation with year check (>=2026)
        if (e.target.value.length === 5 && /^(0[1-9]|1[0-2])\/\d{2}$/.test(e.target.value)) {
            const [month, year] = e.target.value.split('/');
            const fullYear = 2000 + parseInt(year);
            if (fullYear >= 2026) {
                e.target.classList.remove('is-invalid');
                e.target.classList.add('is-valid');
            } else {
                e.target.classList.remove('is-valid');
                e.target.classList.add('is-invalid');
            }
        } else if (e.target.value.length > 0) {
            e.target.classList.remove('is-valid');
            e.target.classList.add('is-invalid');
        } else {
            e.target.classList.remove('is-valid', 'is-invalid');
        }
    });
    }
    
    // Format CVV input - only numbers, max 4 digits
    const cvvInput = document.getElementById('cvv');
    if (cvvInput) {
        cvvInput.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
        
        // Limit to 4 characters
        if (e.target.value.length > 4) {
            e.target.value = e.target.value.substring(0, 4);
        }
        
        // Real-time validation
        if (e.target.value.length >= 3 && e.target.value.length <= 4) {
            e.target.classList.remove('is-invalid');
            e.target.classList.add('is-valid');
        } else if (e.target.value.length > 0) {
            e.target.classList.remove('is-valid');
            e.target.classList.add('is-invalid');
        } else {
            e.target.classList.remove('is-valid', 'is-invalid');
        }
    });
    }
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Disable button and show loading
        processBtn.disabled = true;
        processBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing Payment...';
        
        // Basic validation
        const cardNumber = cardNumberInput ? cardNumberInput.value.replace(/\s/g, '') : '';
        const expiry = expiryInput ? expiryInput.value : '';
        const cvv = cvvInput ? cvvInput.value : '';
        const cardholderName = document.getElementById('cardholder_name').value;
        
        // Validate card number (16 digits)
        if (cardNumber.length !== 16) {
            alert('Please enter a valid 16-digit card number');
            cardNumberInput.focus();
            resetButton();
            return;
        }
        
        // Validate expiry date (MM/YY format) and year (>=2025)
        if (expiry.length !== 5 || !/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiry)) {
            alert('Please enter a valid expiry date (MM/YY format)');
            expiryInput.focus();
            resetButton();
            return;
        }
        
        // Check if year is 2026 or later
        const [month, year] = expiry.split('/');
        const fullYear = 2000 + parseInt(year);
        if (fullYear < 2026) {
            alert('Expiry year must be 2026 or later');
            expiryInput.focus();
            resetButton();
            return;
        }
        
        // Validate CVV (3-4 digits)
        if (cvv.length < 3 || cvv.length > 4) {
            alert('Please enter a valid CVV (3-4 digits)');
            cvvInput.focus();
            resetButton();
            return;
        }
        
        if (!cardholderName.trim()) {
            alert('Please enter the cardholder name');
            document.getElementById('cardholder_name').focus();
            resetButton();
            return;
        }
        
        // Submit the form
        form.submit();
    });
    
    function resetButton() {
        processBtn.disabled = false;
        processBtn.innerHTML = '<i class="fas fa-credit-card me-2"></i>Pay RM {{ number_format($order->total_amount, 2) }}';
    }
    
    function clearValidationStates() {
        if (cardNumberInput) cardNumberInput.classList.remove('is-valid', 'is-invalid');
        if (expiryInput) expiryInput.classList.remove('is-valid', 'is-invalid');
        if (cvvInput) cvvInput.classList.remove('is-valid', 'is-invalid');
    }
    
});
</script>
