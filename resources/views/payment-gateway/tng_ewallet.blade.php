@extends('layouts.app')

@section('title', 'TNG eWallet Payment Gateway')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="text-center mb-5">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-mobile-alt fa-2x text-primary"></i>
                </div>
                <h1 class="display-6 fw-bold text-dark mb-2">TNG eWallet Payment</h1>
                <p class="text-muted">Complete your payment securely with TNG eWallet</p>
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

            <!-- TNG eWallet Login Form -->
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

                    <form action="{{ route('payment-gateway.process') }}" method="POST" id="tng-ewallet-form">
                        @csrf
                        <input type="hidden" name="payment_method" value="tng_ewallet">
                        <input type="hidden" name="order_number" value="{{ $order->order_number }}">

                        <div class="row g-3">
                            <!-- TNG eWallet Phone Number -->
                            <div class="col-12">
                                <label for="tng_phone" class="form-label fw-medium">
                                    <i class="fas fa-phone me-2"></i>TNG eWallet Phone Number
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">+60</span>
                                    <input type="tel" 
                                           id="tng_phone" 
                                           name="tng_phone" 
                                           placeholder="123456789"
                                           class="form-control form-control-lg"
                                           maxlength="10"
                                           required>
                                </div>
                                <div class="form-text">Enter your TNG eWallet registered phone number (without +60)</div>
                            </div>

                            <!-- TNG eWallet PIN -->
                            <div class="col-12">
                                <label for="tng_pin" class="form-label fw-medium">
                                    <i class="fas fa-lock me-2"></i>TNG eWallet PIN
                                </label>
                                <input type="password" 
                                       id="tng_pin" 
                                       name="tng_pin" 
                                       placeholder="Enter your 6-digit PIN"
                                       class="form-control form-control-lg"
                                       maxlength="6"
                                       minlength="6"
                                       required>
                                <div class="form-text">Enter your 6-digit TNG eWallet PIN</div>
                            </div>

                            <!-- TNG eWallet Security Notice -->
                            <div class="col-12">
                                <div class="alert alert-info d-flex align-items-start">
                                    <i class="fas fa-shield-alt me-3 mt-1"></i>
                                    <div>
                                        <h6 class="alert-heading mb-1">Secure Payment</h6>
                                        <p class="mb-0 small">Your payment information is encrypted and secure. This is a simulation for demonstration purposes.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- TNG eWallet Benefits -->
                            <div class="col-12">
                                <div class="alert alert-primary">
                                    <h6 class="alert-heading mb-2">Why choose TNG eWallet?</h6>
                                    <ul class="mb-0 small">
                                        <li>Fast and secure mobile payments</li>
                                        <li>No need to carry cash or cards</li>
                                        <li>Instant payment confirmation</li>
                                        <li>Widely accepted across Malaysia</li>
                                        <li>Easy top-up from bank accounts</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- TNG eWallet Balance Check -->
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title text-muted">Payment Amount</h6>
                                        <h3 class="text-primary mb-0">RM {{ number_format($order->total_amount, 2) }}</h3>
                                        <small class="text-muted">Will be deducted from your TNG eWallet balance</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-12 pt-3">
                                <button type="submit" 
                                        class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-mobile-alt me-2"></i>
                                    Pay RM {{ number_format($order->total_amount, 2) }} with TNG eWallet
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
// Format phone number input
document.getElementById('tng_phone').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/[^0-9]/gi, '');
});

// Format PIN input
document.getElementById('tng_pin').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/[^0-9]/gi, '');
});
</script>
@endsection