@extends('layouts.app')

@section('title', 'Event Booking Payment')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>Event Booking Payment
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('event-booking.payment-process-yf') }}" id="paymentForm">
                        @csrf
                        
                        <!-- Order Summary -->
                        <div class="mb-4">
                            <h5 class="mb-3">Order Summary</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Event</th>
                                            <th>Ticket Type</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cartItems as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item->event->name }}</strong><br>
                                                <small class="text-muted">
                                                    @if($item->event->start_date)
                                                        {{ $item->event->start_date->format('M d, Y') }}
                                                    @elseif($item->event->start_time)
                                                        {{ $item->event->start_time->format('M d, Y') }}
                                                    @else
                                                        Date TBA
                                                    @endif
                                                </small>
                                            </td>
                                            <td>General Admission</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>RM {{ number_format($item->price, 2) }}</td>
                                            <td>RM {{ number_format($item->quantity * $item->price, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="4" class="text-end">Total Amount:</th>
                                            <th>RM {{ number_format($total, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="mb-4">
                            <h5 class="mb-3">Customer Information</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="customer_name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                           id="customer_name" name="customer_name" 
                                           value="{{ old('customer_name', $user->name ?? '') }}" required>
                                    @error('customer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="customer_email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                                           id="customer_email" name="customer_email" 
                                           value="{{ old('customer_email', $user->email ?? '') }}" required>
                                    @error('customer_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="customer_phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror" 
                                           id="customer_phone" name="customer_phone" 
                                           value="{{ old('customer_phone', $user->phone ?? '') }}">
                                    @error('customer_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-4">
                            <h5 class="mb-3">Payment Method</h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="stripe" value="stripe" {{ old('payment_method') == 'stripe' ? 'checked' : 'checked' }}>
                                        <label class="form-check-label" for="stripe">
                                            <i class="fab fa-cc-stripe me-2"></i>Credit/Debit Card
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method"
                                               id="tng_ewallet" value="tng_ewallet" {{ old('payment_method') == 'tng_ewallet' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="tng_ewallet">
                                            <i class="fas fa-mobile-alt me-2"></i>TNG eWallet
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="bank_transfer" value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="bank_transfer">
                                            <i class="fas fa-university me-2"></i>Bank Transfer
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @error('payment_method')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Additional Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label">Additional Notes (Optional)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3" 
                                      placeholder="Any special requests or notes...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a>
                                </label>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Cart
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg" id="payButton">
                                <i class="fas fa-credit-card me-2"></i>Proceed to Payment
                            </button>
                        </div>
                    </form>
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
                    <li>Once payment is confirmed, tickets are non-refundable unless the event is cancelled.</li>
                    <li>Event organizers reserve the right to cancel or reschedule events.</li>
                    <li>In case of event cancellation, full refunds will be processed within 5-7 business days.</li>
                </ul>
                
                <h6>Ticket Terms</h6>
                <ul>
                    <li>Tickets are valid only for the specified event and date.</li>
                    <li>Lost or stolen tickets cannot be replaced.</li>
                    <li>Event organizers may require valid ID for entry.</li>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('paymentForm');
    const payButton = document.getElementById('payButton');
    
    form.addEventListener('submit', function(e) {
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
        const terms = document.getElementById('terms');
        
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
        
        // Disable button to prevent double submission
        payButton.disabled = true;
        payButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    });
});
</script>
@endpush