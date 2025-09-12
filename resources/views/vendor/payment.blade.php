@extends('layouts.vendor')

@section('title', 'Payment - EventHub')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Payment</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('vendor.applications') }}">Applications</a></li>
                        <li class="breadcrumb-item active">Payment</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-credit-card me-2"></i>Complete Payment
                    </h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>There were some problems with your input:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('vendor.payment.process', $application->id) }}" id="paymentForm">
                        @csrf
                        
                        <!-- Application Summary -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Application Summary</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Event:</strong> {{ $application->event->name }}</p>
                                    <p class="mb-1"><strong>Booth Size:</strong> {{ $application->booth_size }}</p>
                                    <p class="mb-1"><strong>Quantity:</strong> {{ $application->booth_quantity }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Service Type:</strong> {{ $application->service_type_label }}</p>
                                    <p class="mb-1"><strong>Applied Date:</strong> {{ $application->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Summary -->
                        <div class="mb-4">
                            <h6 class="mb-3">Payment Summary</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="d-flex justify-content-between">
                                                <span>Base Amount:</span>
                                                <span>RM {{ number_format($application->event->booth_price, 2) }}</span>
                                            </div>
                                            @if(isset($paymentBreakdown['tax']))
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted">Tax (6%):</span>
                                                    <span>RM {{ number_format($paymentBreakdown['tax'], 2) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-6">
                                            @if(isset($paymentBreakdown['service_charge']))
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted">Service Charge:</span>
                                                    <span>RM {{ number_format($paymentBreakdown['service_charge'], 2) }}</span>
                                                </div>
                                            @endif
                                            <hr>
                                            <div class="d-flex justify-content-between">
                                                <strong>Total Amount:</strong>
                                                <strong class="text-primary">RM {{ number_format($finalAmount, 2) }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold mb-3">Choose Payment Method <span class="text-danger">*</span></label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="payment-method-card">
                                        <input class="form-check-input" type="radio" name="payment_method" value="debit_payment" id="debit_payment" checked>
                                        <label class="form-check-label w-100" for="debit_payment">
                                            <div class="card h-100 border-2 payment-option" data-method="debit_payment">
                                                <div class="card-body text-center p-3">
                                                    <i class="fas fa-university text-primary fs-2 mb-2"></i>
                                                    <h6 class="mb-1">Debit Payment</h6>
                                                    <small class="text-muted">Direct bank transfer</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="payment-method-card">
                                        <input class="form-check-input" type="radio" name="payment_method" value="credit_payment" id="credit_payment">
                                        <label class="form-check-label w-100" for="credit_payment">
                                            <div class="card h-100 border-2 payment-option" data-method="credit_payment">
                                                <div class="card-body text-center p-3">
                                                    <i class="fas fa-credit-card text-success fs-2 mb-2"></i>
                                                    <h6 class="mb-1">Credit Payment</h6>
                                                    <small class="text-muted">Credit card payment</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Debit Payment Details -->
                        <div id="debit-payment-details" class="payment-details" style="display: block;">
                            <h6 class="mb-3 text-primary">
                                <i class="fas fa-university me-2"></i>Debit Payment Information
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select name="bank_name" class="form-select" id="bank_name" required>
                                            <option value="">Select Bank</option>
                                            <option value="maybank">Maybank</option>
                                            <option value="cimb">CIMB Bank</option>
                                            <option value="public_bank">Public Bank</option>
                                            <option value="hong_leong">Hong Leong Bank</option>
                                            <option value="ambank">AmBank</option>
                                            <option value="rhb">RHB Bank</option>
                                            <option value="uob">UOB Bank</option>
                                            <option value="ocbc">OCBC Bank</option>
                                        </select>
                                        <label for="bank_name">Bank Name <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" name="account_number" class="form-control" id="account_number" placeholder="1234567890" required>
                                        <label for="account_number">Account Number <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3 mt-2">
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" name="account_holder_name" class="form-control" id="account_holder_name" placeholder="John Doe" required>
                                        <label for="account_holder_name">Account Holder Name <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Credit Payment Details -->
                        <div id="credit-payment-details" class="payment-details" style="display: none;">
                            <h6 class="mb-3 text-success">
                                <i class="fas fa-credit-card me-2"></i>Credit Payment Information
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <div class="form-floating">
                                        <input type="text" name="credit_card_number" class="form-control" id="credit_card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                                        <label for="credit_card_number">Card Number <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-floating">
                                        <input type="text" name="credit_expiry_date" class="form-control" id="credit_expiry_date" placeholder="MM/YY" maxlength="5">
                                        <label for="credit_expiry_date">Expiry <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-floating">
                                        <input type="text" name="credit_cvv" class="form-control" id="credit_cvv" placeholder="123" maxlength="4">
                                        <label for="credit_cvv">CVV <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3 mt-2">
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" name="credit_cardholder_name" class="form-control" id="credit_cardholder_name" placeholder="John Doe">
                                        <label for="credit_cardholder_name">Cardholder Name <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="terms_accepted" id="terms_accepted" required>
                                <label class="form-check-label" for="terms_accepted">
                                    I agree to the <a href="#" target="_blank" class="text-decoration-none">Terms and Conditions</a> and <a href="#" target="_blank" class="text-decoration-none">Privacy Policy</a>
                                </label>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-3 justify-content-end">
                            <a href="{{ route('vendor.applications.show', $application->id) }}" class="btn btn-outline-secondary px-4">
                                <i class="fas fa-arrow-left me-2"></i>Back to Application
                            </a>
                            <button type="submit" class="btn btn-success btn-lg px-5" id="submitBtn">
                                <i class="fas fa-university me-2"></i>Process Debit Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.payment-method-card {
    position: relative;
}

.payment-method-card input[type="radio"] {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}

.payment-option {
    transition: all 0.3s ease;
    cursor: pointer;
    border-color: #e9ecef !important;
}

.payment-option:hover {
    border-color: #0d6efd !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.payment-method-card input[type="radio"]:checked + label .payment-option {
    border-color: #0d6efd !important;
    background-color: #f8f9ff;
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
}

.payment-details {
    background-color: #f8f9fa;
    border-radius: 0.5rem;
    padding: 1.5rem;
    margin-top: 1rem;
}

.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label {
    color: #0d6efd;
}

#submitBtn {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    transition: all 0.3s ease;
}

#submitBtn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3);
}

.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.alert {
    border-radius: 0.75rem;
}
</style>
@endsection

@section('scripts')
<script>
console.log('=== PAYMENT SCRIPT LOADED ===');

document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DOM LOADED ===');
    
    // Get elements
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const debitPaymentDetails = document.getElementById('debit-payment-details');
    const creditPaymentDetails = document.getElementById('credit-payment-details');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('paymentForm');

    console.log('Elements found:', {
        paymentMethods: paymentMethods.length,
        debitPaymentDetails: !!debitPaymentDetails,
        creditPaymentDetails: !!creditPaymentDetails,
        submitBtn: !!submitBtn,
        form: !!form
    });

    // Payment method selection
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            console.log('Payment method changed to:', this.value);
            
            if (debitPaymentDetails) debitPaymentDetails.style.display = 'none';
            if (creditPaymentDetails) creditPaymentDetails.style.display = 'none';

            // Remove required from all fields first
            const allFields = form.querySelectorAll('input[required], select[required]');
            allFields.forEach(field => field.removeAttribute('required'));

            if (this.value === 'debit_payment') {
                if (debitPaymentDetails) debitPaymentDetails.style.display = 'block';
                if (submitBtn) submitBtn.innerHTML = '<i class="fas fa-university me-2"></i>Process Debit Payment';
                
                // Set required for debit payment fields
                const bankName = form.querySelector('select[name="bank_name"]');
                const accountNumber = form.querySelector('input[name="account_number"]');
                const accountHolderName = form.querySelector('input[name="account_holder_name"]');
                if (bankName) bankName.setAttribute('required', 'required');
                if (accountNumber) accountNumber.setAttribute('required', 'required');
                if (accountHolderName) accountHolderName.setAttribute('required', 'required');
            } else if (this.value === 'credit_payment') {
                if (creditPaymentDetails) creditPaymentDetails.style.display = 'block';
                if (submitBtn) submitBtn.innerHTML = '<i class="fas fa-credit-card me-2"></i>Process Credit Payment';
                
                // Set required for credit payment fields
                const cardNumber = form.querySelector('input[name="credit_card_number"]');
                const expiryDate = form.querySelector('input[name="credit_expiry_date"]');
                const cvv = form.querySelector('input[name="credit_cvv"]');
                const cardholderName = form.querySelector('input[name="credit_cardholder_name"]');
                if (cardNumber) cardNumber.setAttribute('required', 'required');
                if (expiryDate) expiryDate.setAttribute('required', 'required');
                if (cvv) cvv.setAttribute('required', 'required');
                if (cardholderName) cardholderName.setAttribute('required', 'required');
            }
        });
    });

    // Set initial state for debit payment (default selected)
    const debitPaymentRadio = form.querySelector('input[name="payment_method"][value="debit_payment"]');
    if (debitPaymentRadio) {
        debitPaymentRadio.dispatchEvent(new Event('change'));
    }

    // Button click handler
    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            console.log('=== BUTTON CLICKED ===');
        });
    }

    // Form submission handler
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('=== FORM SUBMIT ===');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
            }
        });
    }
});
</script>
@endsection