@extends('layouts.app')

@section('title', 'Bank Transfer Instructions')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-university me-2"></i>Bank Transfer Instructions
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Order Information</h6>
                        <p class="mb-1"><strong>Order Number:</strong> {{ $order->order_number }}</p>
                        <p class="mb-1"><strong>Event:</strong> {{ $order->event->name }}</p>
                        <p class="mb-1"><strong>Amount to Pay:</strong> {{ $order->formatted_total }}</p>
                        <p class="mb-0"><strong>Due Date:</strong> {{ now()->addDays(3)->format('F j, Y') }}</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">Bank Details</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>EventHub Official Account</h6>
                                    <p class="mb-1"><strong>Bank:</strong> Maybank Berhad</p>
                                    <p class="mb-1"><strong>Account Name:</strong> EventHub Sdn Bhd</p>
                                    <p class="mb-1"><strong>Account Number:</strong> 1234567890</p>
                                    <p class="mb-0"><strong>Swift Code:</strong> MBBEMYKL</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">Transfer Instructions</h5>
                            <ol class="list-group list-group-numbered">
                                <li class="list-group-item">Log in to your online banking</li>
                                <li class="list-group-item">Select "Transfer" or "Pay Bills"</li>
                                <li class="list-group-item">Enter the bank details above</li>
                                <li class="list-group-item">Enter amount: <strong>{{ $order->formatted_total }}</strong></li>
                                <li class="list-group-item">Add reference: <strong>{{ $order->order_number }}</strong></li>
                                <li class="list-group-item">Complete the transfer</li>
                            </ol>
                        </div>
                    </div>

                    <div class="alert alert-warning mt-4">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Important Notes</h6>
                        <ul class="mb-0">
                            <li>Please include the order number <strong>{{ $order->order_number }}</strong> as the transfer reference</li>
                            <li>Transfer must be completed within 3 days to secure your booking</li>
                            <li>Once payment is received, you will receive a confirmation email</li>
                            <li>Keep your bank transfer receipt as proof of payment</li>
                        </ul>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('event-booking.payment-receipt-yf', ['order' => $order->order_number]) }}" 
                           class="btn btn-outline-primary me-2">
                            <i class="fas fa-receipt me-2"></i>View Receipt
                        </a>
                        <a href="{{ route('events.show', $order->event->id) }}" 
                           class="btn btn-outline-secondary">
                            <i class="fas fa-calendar me-2"></i>Back to Event
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
