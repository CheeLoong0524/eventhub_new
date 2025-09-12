@extends('layouts.app')

@section('title', 'Bank Transfer Instructions')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-university me-2"></i>Bank Transfer Instructions
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Payment Pending</h6>
                        <p class="mb-0">Your order has been created but payment is pending. Please complete the bank transfer below to confirm your booking.</p>
                    </div>
                    
                    <!-- Order Summary -->
                    <div class="mb-4">
                        <h5>Order Summary</h5>
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Order Number:</strong> {{ $order->order_number }}</p>
                                        <p class="mb-1"><strong>Event:</strong> {{ $order->event->name }}</p>
                                        <p class="mb-1"><strong>Date:</strong> {{ $order->event->getFormattedDateTime() }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Customer:</strong> {{ $order->customer_name }}</p>
                                        <p class="mb-1"><strong>Email:</strong> {{ $order->customer_email }}</p>
                                        <p class="mb-1"><strong>Total Amount:</strong> {{ $order->formatted_total }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bank Transfer Details -->
                    <div class="mb-4">
                        <h5>Bank Transfer Details</h5>
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Bank Information</h6>
                                        <p class="mb-1"><strong>Bank Name:</strong> Maybank Berhad</p>
                                        <p class="mb-1"><strong>Account Name:</strong> EventHub Sdn Bhd</p>
                                        <p class="mb-1"><strong>Account Number:</strong> 1234567890</p>
                                        <p class="mb-1"><strong>Swift Code:</strong> MBBEMYKL</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Transfer Details</h6>
                                        <p class="mb-1"><strong>Amount:</strong> {{ $order->formatted_total }}</p>
                                        <p class="mb-1"><strong>Reference:</strong> {{ $order->order_number }}</p>
                                        <p class="mb-1"><strong>Currency:</strong> MYR</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Instructions -->
                    <div class="mb-4">
                        <h5>Transfer Instructions</h5>
                        <ol>
                            <li>Log in to your online banking or visit your nearest bank branch</li>
                            <li>Make a transfer to the bank account details provided above</li>
                            <li>Use the order number <strong>{{ $order->order_number }}</strong> as the reference</li>
                            <li>Transfer the exact amount: <strong>{{ $order->formatted_total }}</strong></li>
                            <li>Keep the transaction receipt for your records</li>
                            <li>Payment confirmation may take 1-2 business days</li>
                        </ol>
                    </div>

                    <!-- Alternative Payment Methods -->
                    <div class="mb-4">
                        <h5>Alternative Payment Methods</h5>
                        <p>If you prefer to pay by credit card or PayPal, you can cancel this order and choose a different payment method.</p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Cart
                            </a>
                            <a href="{{ route('events.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-home me-2"></i>Browse Events
                            </a>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="alert alert-light">
                        <h6><i class="fas fa-phone me-2"></i>Need Help?</h6>
                        <p class="mb-1">If you have any questions about this payment, please contact us:</p>
                        <p class="mb-1"><strong>Email:</strong> support@eventhub.com</p>
                        <p class="mb-0"><strong>Phone:</strong> +60 3-1234 5678</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
