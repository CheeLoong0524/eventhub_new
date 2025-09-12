@extends('layouts.app')

@section('title', 'Event Booking Payment Success')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-success">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h5 class="text-success mb-3">Thank you for your event booking!</h5>
                    <p class="text-muted mb-4">Your payment has been processed successfully and your tickets have been confirmed.</p>
                    
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Booking Details</h6>
                        <p class="mb-1"><strong>Order Number:</strong> {{ $order->order_number }}</p>
                        <p class="mb-1"><strong>Event:</strong> {{ $order->event->name }}</p>
                        <p class="mb-1"><strong>Date:</strong> {{ $order->event->getFormattedDateTime() }}</p>
                        <p class="mb-1"><strong>Total Amount:</strong> {{ $order->formatted_total }}</p>
                        <p class="mb-0"><strong>Status:</strong> <span class="badge bg-success">Paid</span></p>
                    </div>

                    <div class="alert alert-light">
                        <h6><i class="fas fa-check-circle me-2"></i>Booking Confirmed</h6>
                        <p class="mb-0">Your booking has been confirmed! You can view your receipt and booking details below.</p>
                    </div>


                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ route('event-booking.payment-receipt-yf', ['order' => $order->order_number]) }}" 
                           class="btn btn-outline-primary" target="_blank">
                            <i class="fas fa-receipt me-2"></i>View Receipt
                        </a>
                        
                        <a href="{{ route('events.show', $order->event->id) }}" 
                           class="btn btn-outline-secondary">
                            <i class="fas fa-calendar me-2"></i>View Event Details
                        </a>
                        
                        <a href="{{ route('customer.dashboard') }}" 
                           class="btn btn-primary">
                            <i class="fas fa-ticket-alt me-2"></i>My Bookings
                        </a>
                    </div>

                    <hr class="my-4">
                    
                    <div class="text-muted">
                        <h6>What's Next?</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Save your receipt for your records</li>
                            <li><i class="fas fa-check text-success me-2"></i>Arrive at the event venue on time</li>
                            <li><i class="fas fa-check text-success me-2"></i>Bring a valid ID for verification</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
