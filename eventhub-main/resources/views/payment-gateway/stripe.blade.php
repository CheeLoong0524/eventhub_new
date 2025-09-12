@extends('layouts.app')

@section('title', 'Stripe Payment Gateway')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="text-center mb-5">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-credit-card fa-2x text-primary"></i>
                </div>
                <h1 class="display-6 fw-bold text-dark mb-2">Stripe Payment Gateway</h1>
                <p class="text-muted">Complete your payment securely with Stripe</p>
            </div>

            <!-- Order Summary -->
            <div class="card bg-light mb-4">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-3">Order Summary</h5>
                    <div class="row">
                        <div class="col-sm-6">
                            <small class="text-muted">Order Number:</small>
                            <div class="fw-medium">{{ $order->order_number }}</div>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted">Event:</small>
                            <div class="fw-medium">{{ $order->event->name }}</div>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Total Amount:</span>
                        <span class="h4 fw-bold text-success mb-0">RM {{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="card shadow">
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6 class="alert-heading">Please fix the following errors:</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('payment-gateway.process') }}" method="POST" id="stripe-form">
                        @csrf
                        <input type="hidden" name="payment_method" value="stripe">
                        <input type="hidden" name="order_number" value="{{ $order->order_number }}">

                        <div class="row g-3">
                            <!-- Card Number -->
                            <div class="col-12">
                                <label for="card_number" class="form-label fw-medium">
                                    Card Number
                                </label>
                                <input type="text" 
                                       id="card_number" 
                                       name="card_number" 
                                       placeholder="1234 5678 9012 3456"
                                       class="form-control form-control-lg"
                                       maxlength="19"
                                       required>
                                <div class="form-text">Enter your 16-digit card number</div>
                            </div>

                            <!-- Cardholder Name -->
                            <div class="col-12">
                                <label for="cardholder_name" class="form-label fw-medium">
                                    Cardholder Name
                                </label>
                                <input type="text" 
                                       id="cardholder_name" 
                                       name="cardholder_name" 
                                       placeholder="John Doe"
                                       class="form-control form-control-lg"
                                       required>
                            </div>

                            <!-- Expiry Date and CVV -->
                            <div class="col-md-8">
                                <label class="form-label fw-medium">Expiry Date</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <select name="expiry_month" 
                                                class="form-select form-select-lg"
                                                required>
                                            <option value="">Month</option>
                                            @for($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <select name="expiry_year" 
                                                class="form-select form-select-lg"
                                                required>
                                            <option value="">Year</option>
                                            @for($i = date('Y'); $i <= date('Y') + 10; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="cvv" class="form-label fw-medium">CVV</label>
                                <input type="text" 
                                       id="cvv" 
                                       name="cvv" 
                                       placeholder="123"
                                       class="form-control form-control-lg"
                                       maxlength="4"
                                       required>
                            </div>

                            <!-- Security Notice -->
                            <div class="col-12">
                                <div class="alert alert-info d-flex align-items-start">
                                    <i class="fas fa-shield-alt me-3 mt-1"></i>
                                    <div>
                                        <h6 class="alert-heading mb-1">Secure Payment</h6>
                                        <p class="mb-0 small">Your payment information is encrypted and secure. This is a simulation for demonstration purposes.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-12 pt-3">
                                <button type="submit" 
                                        class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-lock me-2"></i>
                                    Pay RM {{ number_format($order->total_amount, 2) }} with Stripe
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Back Button -->
            <div class="text-center mt-4">
                <a href="{{ route('event-booking.payment-form-yf') }}" 
                   class="text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i>Back to Payment Method Selection
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Format card number input
document.getElementById('card_number').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
    e.target.value = formattedValue;
});

// Format CVV input
document.getElementById('cvv').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/[^0-9]/gi, '');
});
</script>
@endsection