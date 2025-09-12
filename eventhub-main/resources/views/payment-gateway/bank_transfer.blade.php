@extends('layouts.app')

@section('title', 'Bank Transfer Payment Gateway')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="text-center mb-5">
                <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-university fa-2x text-success"></i>
                </div>
                <h1 class="display-6 fw-bold text-dark mb-2">Bank Transfer Payment</h1>
                <p class="text-muted">Complete your bank transfer details for payment processing</p>
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

            <!-- Bank Transfer Form -->
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

                    <form action="{{ route('payment-gateway.process') }}" method="POST" id="bank-transfer-form">
                        @csrf
                        <input type="hidden" name="payment_method" value="bank_transfer">
                        <input type="hidden" name="order_number" value="{{ $order->order_number }}">

                        <div class="row g-3">
                            <!-- Bank Name -->
                            <div class="col-12">
                                <label for="bank_name" class="form-label fw-medium">
                                    Bank Name
                                </label>
                                <select id="bank_name" 
                                        name="bank_name" 
                                        class="form-select form-select-lg"
                                        required>
                                    <option value="">Select your bank</option>
                                    <option value="Maybank">Maybank</option>
                                    <option value="CIMB Bank">CIMB Bank</option>
                                    <option value="Public Bank">Public Bank</option>
                                    <option value="RHB Bank">RHB Bank</option>
                                    <option value="Hong Leong Bank">Hong Leong Bank</option>
                                    <option value="AmBank">AmBank</option>
                                    <option value="Bank Islam">Bank Islam</option>
                                    <option value="Bank Rakyat">Bank Rakyat</option>
                                    <option value="Affin Bank">Affin Bank</option>
                                    <option value="Alliance Bank">Alliance Bank</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <!-- Account Holder Name -->
                            <div class="col-12">
                                <label for="account_holder_name" class="form-label fw-medium">
                                    Account Holder Name
                                </label>
                                <input type="text" 
                                       id="account_holder_name" 
                                       name="account_holder_name" 
                                       placeholder="John Doe"
                                       class="form-control form-control-lg"
                                       required>
                                <div class="form-text">Name as it appears on your bank account</div>
                            </div>

                            <!-- Account Number -->
                            <div class="col-12">
                                <label for="account_number" class="form-label fw-medium">
                                    Account Number
                                </label>
                                <input type="text" 
                                       id="account_number" 
                                       name="account_number" 
                                       placeholder="1234567890"
                                       class="form-control form-control-lg"
                                       minlength="8"
                                       maxlength="20"
                                       required>
                                <div class="form-text">Your bank account number (8-20 digits)</div>
                            </div>

                            <!-- Bank Transfer Instructions -->
                            <div class="col-12">
                                <div class="alert alert-primary">
                                    <h6 class="alert-heading mb-2">Bank Transfer Instructions</h6>
                                    <div class="small">
                                        <p class="mb-2"><strong>Transfer Details:</strong></p>
                                        <ul class="mb-2">
                                            <li>Amount: <strong>RM {{ number_format($order->total_amount, 2) }}</strong></li>
                                            <li>Reference: <strong>{{ $order->order_number }}</strong></li>
                                            <li>Beneficiary: EventHub Malaysia</li>
                                            <li>Bank: Maybank Berhad</li>
                                            <li>Account: 1234567890123</li>
                                        </ul>
                                        <p class="mb-0"><strong>Note:</strong> Please include the order number as reference when making the transfer.</p>
                                    </div>
                                </div>
                            </div>


                            <!-- Submit Button -->
                            <div class="col-12 pt-3">
                                <button type="submit" 
                                        class="btn btn-success btn-lg w-100">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Initiate Bank Transfer
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
// Format account number input
document.getElementById('account_number').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/[^0-9]/gi, '');
});

</script>
@endsection