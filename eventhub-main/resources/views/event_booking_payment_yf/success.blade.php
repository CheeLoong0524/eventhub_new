@extends('layouts.app')

@section('title', 'Payment Successful')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>Payment Successful!
                    </h4>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h5 class="text-success mb-3">Thank you for your purchase!</h5>
                    <p class="text-muted mb-4">Your payment has been processed successfully and your tickets have been confirmed.</p>
                    
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Order Details</h6>
                        <p class="mb-1"><strong>Order Number:</strong> {{ $order->order_number }}</p>
                        <p class="mb-1"><strong>Event:</strong> {{ $order->event->name }}</p>
                        <p class="mb-1"><strong>Date:</strong> {{ $order->event->getFormattedDateTime() }}</p>
                        <p class="mb-1"><strong>Total Amount:</strong> {{ $order->formatted_total }}</p>
                        <p class="mb-0"><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment->payment_method)) }}</p>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-envelope me-2"></i>Confirmation Email</h6>
                        <p class="mb-0">A confirmation email with your ticket details has been sent to <strong>{{ $order->customer_email }}</strong></p>
                    </div>
                    
                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <a href="{{ route('payment.receipt', ['order' => $order->order_number]) }}" 
                           class="btn btn-outline-primary" target="_blank">
                            <i class="fas fa-receipt me-2"></i>View Receipt
                        </a>
                        <a href="{{ route('events.show', $order->event) }}" 
                           class="btn btn-primary">
                            <i class="fas fa-calendar me-2"></i>View Event Details
                        </a>
                        <a href="{{ route('events.index') }}" 
                           class="btn btn-outline-secondary">
                            <i class="fas fa-home me-2"></i>Browse More Events
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Ticket Details -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-ticket-alt me-2"></i>Your Tickets
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Ticket Type</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->ticket_details as $ticket)
                                <tr>
                                    <td>{{ $ticket['ticket_type_name'] }}</td>
                                    <td>{{ $ticket['quantity'] }}</td>
                                    <td>RM {{ number_format($ticket['unit_price'], 2) }}</td>
                                    <td>RM {{ number_format($ticket['total_price'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th>{{ $order->formatted_total }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Important Information -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Important Information
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Please bring a valid ID and this confirmation to the event
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Arrive at least 30 minutes before the event starts
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Keep your ticket details safe - they cannot be replaced if lost
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Contact us if you have any questions about your booking
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
