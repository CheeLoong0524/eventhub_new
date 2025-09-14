@extends('layouts.vendor')

@section('title', 'Complete Payment - EventHub')
@section('page-title', 'Complete Payment')
@section('page-description', 'Complete your booth payment to confirm your participation')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="text-center mb-5">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-credit-card fa-2x text-primary"></i>
                </div>
                <h1 class="display-6 fw-bold text-dark mb-2">Complete Payment</h1>
                <p class="text-muted">Secure payment processing for your booth application</p>
    </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

            <div class="row">
                <!-- Application Summary -->
                <div class="col-lg-6 mb-4">
                    <div class="card bg-light h-100">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold mb-4">
                                <i class="fas fa-info-circle me-2 text-primary"></i>Application Summary
                            </h5>
                            
                            <div class="row g-3">
                                <div class="col-6">
                                    <small class="text-muted">Event:</small>
                                    <div class="fw-medium">{{ $application->event->name }}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Service Type:</small>
                                    <div class="fw-medium">{{ $application->service_type_label }}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Booth Size:</small>
                                    <div class="fw-medium">{{ $application->booth_size }}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Quantity:</small>
                                    <div class="fw-medium">{{ $application->booth_quantity }}</div>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted">Applied Date:</small>
                                    <div class="fw-medium">{{ $application->created_at->format('M d, Y H:i') }}</div>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <!-- Payment Breakdown -->
                            <h6 class="fw-semibold mb-3">Payment Breakdown</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <td>Base Amount:</td>
                                            <td class="text-end">RM {{ number_format($paymentBreakdown['base'] ?? $application->requested_price ?? 0, 2) }}</td>
                                        </tr>
                                        @if(isset($paymentBreakdown['tax']))
                                        <tr>
                                            <td class="text-muted">Tax (6%):</td>
                                            <td class="text-end text-muted">RM {{ number_format($paymentBreakdown['tax'], 2) }}</td>
                                        </tr>
                                        @endif
                                        @if(isset($paymentBreakdown['service_charge']))
                                        <tr>
                                            <td class="text-muted">Service Charge:</td>
                                            <td class="text-end text-muted">RM {{ number_format($paymentBreakdown['service_charge'], 2) }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th>Total Amount:</th>
                                            <th class="text-end text-primary">RM {{ number_format($paymentTotal, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <div class="col-lg-6">
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

                    <form method="POST" action="{{ route('vendor.payment.process', $application->id) }}" id="paymentForm">
                        @csrf
                        
                                <!-- Payment Method Selection -->
                        <div class="mb-4">
                                    <h5 class="fw-semibold mb-3">Payment Method</h5>
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="payment_method" 
                                                       id="debit_payment" value="debit_payment" {{ old('payment_method') == 'debit_payment' ? 'checked' : 'checked' }}>
                                                <label class="form-check-label" for="debit_payment">
                                                    <i class="fas fa-university me-2"></i>Debit Card Payment
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="payment_method" 
                                                       id="credit_payment" value="credit_payment" {{ old('payment_method') == 'credit_payment' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="credit_payment">
                                                    <i class="fas fa-credit-card me-2"></i>Credit Card Payment
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    @error('payment_method')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                        </div>

                        <!-- Debit Payment Details -->
                        <div id="debit-payment-details" class="payment-details" style="display: block;">
                            <h6 class="mb-3 text-primary">
                                <i class="fas fa-university me-2"></i>Debit Payment Information
                            </h6>
                            <div class="row g-3">
                                        <div class="col-12">
                                            <label for="bank_name" class="form-label fw-medium">Bank Name</label>
                                            <select name="bank_name" class="form-select form-select-lg" id="bank_name" required>
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
                                        <div class="col-12">
                                            <label for="account_holder_name" class="form-label fw-medium">Account Holder Name</label>
                                            <input type="text" name="account_holder_name" class="form-control form-control-lg" 
                                                   id="account_holder_name" placeholder="John Doe" required>
                                </div>
                                        <div class="col-12">
                                            <label for="account_number" class="form-label fw-medium">Account Number</label>
                                            <input type="text" name="account_number" class="form-control form-control-lg" 
                                                   id="account_number" placeholder="1234567890" minlength="8" maxlength="20" required>
                                            <div class="form-text">Your bank account number (8-20 digits)</div>
                                </div>
                            </div>
                        </div>

                        <!-- Credit Payment Details -->
                        <div id="credit-payment-details" class="payment-details" style="display: none;">
                            <h6 class="mb-3 text-success">
                                <i class="fas fa-credit-card me-2"></i>Credit Payment Information
                            </h6>
                            <div class="row g-3">
                                        <div class="col-12">
                                            <label for="credit_card_number" class="form-label fw-medium">Card Number</label>
                                            <input type="text" name="credit_card_number" class="form-control form-control-lg" 
                                                   id="credit_card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                                            <div class="form-text">Enter 16 digits, spaces will be added automatically</div>
                                    </div>
                                        <div class="col-md-6">
                                            <label for="credit_expiry_date" class="form-label fw-medium">Expiry Date</label>
                                            <input type="text" name="credit_expiry_date" class="form-control form-control-lg" 
                                                   id="credit_expiry_date" placeholder="MM/YY" maxlength="5">
                                            <div class="form-text">Format: MM/YY</div>
                                </div>
                                        <div class="col-md-6">
                                            <label for="credit_cvv" class="form-label fw-medium">CVV</label>
                                            <input type="text" name="credit_cvv" class="form-control form-control-lg" 
                                                   id="credit_cvv" placeholder="123" maxlength="4">
                                            <div class="form-text">3-4 digits on back of card</div>
                                    </div>
        <div class="col-12">
                                            <label for="credit_cardholder_name" class="form-label fw-medium">Cardholder Name</label>
                                            <input type="text" name="credit_cardholder_name" class="form-control form-control-lg" 
                                                   id="credit_cardholder_name" placeholder="John Doe">
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="terms_accepted" id="terms_accepted" required>
                                <label class="form-check-label" for="terms_accepted">
                                            I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal" class="text-decoration-none">Terms and Conditions</a>
                                </label>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                        <i class="fas fa-credit-card me-2"></i>Pay RM {{ number_format($paymentTotal, 2) }}
                                    </button>
                                    <a href="{{ route('vendor.applications.show', $application->id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Application
                            </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Terms and Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>Payment Terms</h6>
                <ul>
                    <li>All payments are processed securely through our payment partners.</li>
                    <li>Once payment is confirmed, booth bookings are non-refundable unless the event is cancelled.</li>
                    <li>Event organizers reserve the right to cancel or reschedule events.</li>
                    <li>In case of event cancellation, full refunds will be processed within 5-7 business days.</li>
                </ul>
                
                <h6>Booth Terms</h6>
                <ul>
                    <li>Booth assignments are valid only for the specified event and date.</li>
                    <li>Vendors must comply with all event rules and regulations.</li>
                    <li>Event organizers may require valid business documentation for entry.</li>
                    <li>Event details are subject to change without notice.</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.payment-details {
    background-color: #f8f9fa;
    border-radius: 0.5rem;
    padding: 1.5rem;
    margin-top: 1rem;
}

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

#submitBtn {
    background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
    border: none;
    transition: all 0.3s ease;
}

#submitBtn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(13, 110, 253, 0.3);
}

.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('paymentForm');
    const submitBtn = document.getElementById('submitBtn');
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const debitPaymentDetails = document.getElementById('debit-payment-details');
    const creditPaymentDetails = document.getElementById('credit-payment-details');

    // Payment method selection
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            if (debitPaymentDetails) debitPaymentDetails.style.display = 'none';
            if (creditPaymentDetails) creditPaymentDetails.style.display = 'none';

            // Remove required from all fields first
            const allFields = form.querySelectorAll('input[required], select[required]');
            allFields.forEach(field => field.removeAttribute('required'));

            if (this.value === 'debit_payment') {
                if (debitPaymentDetails) debitPaymentDetails.style.display = 'block';
                if (submitBtn) submitBtn.innerHTML = '<i class="fas fa-university me-2"></i>Pay RM {{ number_format($paymentTotal, 2) }}';
                
                // Set required for debit payment fields
                const bankName = form.querySelector('select[name="bank_name"]');
                const accountNumber = form.querySelector('input[name="account_number"]');
                const accountHolderName = form.querySelector('input[name="account_holder_name"]');
                if (bankName) bankName.setAttribute('required', 'required');
                if (accountNumber) accountNumber.setAttribute('required', 'required');
                if (accountHolderName) accountHolderName.setAttribute('required', 'required');
            } else if (this.value === 'credit_payment') {
                if (creditPaymentDetails) creditPaymentDetails.style.display = 'block';
                if (submitBtn) submitBtn.innerHTML = '<i class="fas fa-credit-card me-2"></i>Pay RM {{ number_format($paymentTotal, 2) }}';
                
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

    // Function to update submit button state based on form validation
    function updateSubmitButtonState() {
        const invalidFields = form.querySelectorAll('.is-invalid');
        const requiredFields = form.querySelectorAll('input[required], select[required]');
        const termsAccepted = document.getElementById('terms_accepted').checked;
        
        let allFieldsValid = true;
        
        // Check if all required fields are filled and valid
        requiredFields.forEach(field => {
            if (!field.value.trim() || field.classList.contains('is-invalid')) {
                allFieldsValid = false;
            }
        });
        
        // Enable/disable submit button
        if (submitBtn) {
            if (allFieldsValid && termsAccepted && invalidFields.length === 0) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('btn-secondary');
                submitBtn.classList.add('btn-primary');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-secondary');
            }
        }
    }

    // Set initial state for debit payment (default selected)
    const debitPaymentRadio = form.querySelector('input[name="payment_method"][value="debit_payment"]');
    if (debitPaymentRadio) {
        debitPaymentRadio.dispatchEvent(new Event('change'));
    }
    
    // Initialize submit button state
    updateSubmitButtonState();

    // Format card number input - auto add spaces every 4 digits
    const cardNumberInput = document.getElementById('credit_card_number');
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
            
            // Update submit button state
            updateSubmitButtonState();
        });
    }
    
    // Format expiry date input - auto add / after 2 digits
    const expiryInput = document.getElementById('credit_expiry_date');
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
            
            // Real-time validation with proper date check
            if (e.target.value.length === 5 && /^(0[1-9]|1[0-2])\/\d{2}$/.test(e.target.value)) {
                const [month, year] = e.target.value.split('/');
                const fullYear = 2000 + parseInt(year);
                const currentDate = new Date();
                const currentYear = currentDate.getFullYear();
                const currentMonth = currentDate.getMonth() + 1; // getMonth() returns 0-11
                
                // Check if year is current year or later
                if (fullYear > currentYear || (fullYear === currentYear && parseInt(month) >= currentMonth)) {
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
            
            // Update submit button state
            updateSubmitButtonState();
        });
    }
    
    // Format CVV input - only numbers, max 4 digits
    const cvvInput = document.getElementById('credit_cvv');
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
            
            // Update submit button state
            updateSubmitButtonState();
        });
    }

    // Format account number input
    const accountNumberInput = document.getElementById('account_number');
    if (accountNumberInput) {
        accountNumberInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/gi, '');
            
            // Real-time validation for account number
            if (e.target.value.length >= 8 && e.target.value.length <= 20) {
                e.target.classList.remove('is-invalid');
                e.target.classList.add('is-valid');
            } else if (e.target.value.length > 0) {
                e.target.classList.remove('is-valid');
                e.target.classList.add('is-invalid');
            } else {
                e.target.classList.remove('is-valid', 'is-invalid');
            }
            
            // Update submit button state
            updateSubmitButtonState();
        });
    }
    
    // Add validation for other required fields
    const allRequiredFields = form.querySelectorAll('input[required], select[required]');
    allRequiredFields.forEach(field => {
        field.addEventListener('input', function(e) {
            if (e.target.value.trim()) {
                e.target.classList.remove('is-invalid');
                e.target.classList.add('is-valid');
            } else {
                e.target.classList.remove('is-valid');
                e.target.classList.add('is-invalid');
            }
            
            // Update submit button state
            updateSubmitButtonState();
        });
    });
    
    // Add validation for terms checkbox
    const termsCheckbox = document.getElementById('terms_accepted');
    if (termsCheckbox) {
        termsCheckbox.addEventListener('change', function(e) {
            // Update submit button state
            updateSubmitButtonState();
        });
    }

    // Form submission handler
    if (form) {
        form.addEventListener('submit', function(e) {
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
            const terms = document.getElementById('terms_accepted');
            
            if (!paymentMethod) {
                e.preventDefault();
                alert('Please select a payment method.');
                return;
            }
            
            if (!terms.checked) {
                e.preventDefault();
                alert('Please agree to the terms and conditions.');
                return;
            }
            
            // Check for any invalid fields before proceeding
            const invalidFields = form.querySelectorAll('.is-invalid');
            if (invalidFields.length > 0) {
                e.preventDefault();
                alert('Please fix the highlighted fields before submitting.');
                invalidFields[0].focus();
                return;
            }
            
            // Additional validation for credit card payment
            if (paymentMethod.value === 'credit_payment') {
                const cardNumber = document.getElementById('credit_card_number').value.replace(/\s/g, '');
                const expiryDate = document.getElementById('credit_expiry_date').value;
                const cvv = document.getElementById('credit_cvv').value;
                const cardholderName = document.getElementById('credit_cardholder_name').value;
                
                // Validate card number (16 digits)
                if (cardNumber.length !== 16) {
                    e.preventDefault();
                    alert('Please enter a valid 16-digit card number.');
                    document.getElementById('credit_card_number').focus();
                    return;
                }
                
                // Validate expiry date
                if (expiryDate.length !== 5 || !/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiryDate)) {
                    e.preventDefault();
                    alert('Please enter a valid expiry date (MM/YY format).');
                    document.getElementById('credit_expiry_date').focus();
                    return;
                }
                
                // Check if expiry date is not in the past
                const [month, year] = expiryDate.split('/');
                const fullYear = 2000 + parseInt(year);
                const currentDate = new Date();
                const currentYear = currentDate.getFullYear();
                const currentMonth = currentDate.getMonth() + 1;
                
                if (fullYear < currentYear || (fullYear === currentYear && parseInt(month) < currentMonth)) {
                    e.preventDefault();
                    alert('Credit card has expired. Please enter a valid expiry date.');
                    document.getElementById('credit_expiry_date').focus();
                    return;
                }
                
                // Validate CVV (3-4 digits)
                if (cvv.length < 3 || cvv.length > 4) {
                    e.preventDefault();
                    alert('Please enter a valid CVV (3-4 digits).');
                    document.getElementById('credit_cvv').focus();
                    return;
                }
                
                if (!cardholderName.trim()) {
                    e.preventDefault();
                    alert('Please enter the cardholder name.');
                    document.getElementById('credit_cardholder_name').focus();
                    return;
                }
            }
            
            // Additional validation for debit payment
            if (paymentMethod.value === 'debit_payment') {
                const bankName = document.getElementById('bank_name').value;
                const accountNumber = document.getElementById('account_number').value;
                const accountHolderName = document.getElementById('account_holder_name').value;
                
                if (!bankName) {
                    e.preventDefault();
                    alert('Please select a bank.');
                    document.getElementById('bank_name').focus();
                    return;
                }
                
                if (accountNumber.length < 8 || accountNumber.length > 20) {
                    e.preventDefault();
                    alert('Please enter a valid account number (8-20 digits).');
                    document.getElementById('account_number').focus();
                    return;
                }
                
                if (!accountHolderName.trim()) {
                    e.preventDefault();
                    alert('Please enter the account holder name.');
                    document.getElementById('account_holder_name').focus();
                    return;
                }
            }
            
            // Disable button to prevent double submission
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
            }
        });
    }
});
</script>
@endsection