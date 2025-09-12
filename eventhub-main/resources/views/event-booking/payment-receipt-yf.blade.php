@extends('layouts.app')

@section('title', 'Event Booking Receipt')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <!-- Receipt Header -->
                    <div class="receipt-header text-center">
                        <h2 class="mb-2">
                            <i class="fas fa-calendar-alt me-2"></i>EventHub
                        </h2>
                        <p class="text-muted mb-0">Your Premier Event Management Platform</p>
                        <hr class="my-3">
                        <h4 class="text-success mb-0">EVENT BOOKING RECEIPT</h4>
                        <p class="text-muted">Receipt #{{ $order->order_number }}</p>
                    </div>

                    <!-- Order Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Order Information</h6>
                            <p class="mb-1"><strong>Order Number:</strong> {{ $order->order_number }}</p>
                            <p class="mb-1"><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y g:i A') }}</p>
                            <p class="mb-1"><strong>Status:</strong> <span class="badge bg-success">Paid</span></p>
                            <p class="mb-0"><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment->payment_method)) }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Customer Information</h6>
                            <p class="mb-1"><strong>Name:</strong> {{ $order->customer_name }}</p>
                            <p class="mb-1"><strong>Email:</strong> {{ $order->customer_email }}</p>
                            @if($order->customer_phone)
                            <p class="mb-0"><strong>Phone:</strong> {{ $order->customer_phone }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Event Information -->
                    <div class="mb-4">
                        <h6 class="text-muted">Event Information</h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">{{ $order->event->name }}</h5>
                                <p class="card-text">
                                    <i class="fas fa-calendar me-2"></i>{{ $order->event->getFormattedDateTime() }}<br>
                                    <i class="fas fa-map-marker-alt me-2"></i>{{ $order->event->venue }}<br>
                                    <i class="fas fa-info-circle me-2"></i>{{ $order->event->description }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Ticket Details -->
                    <div class="mb-4">
                        <h6 class="text-muted">Ticket Details</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ticket Type</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-end">Unit Price</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->ticket_details as $detail)
                                    <tr>
                                        <td>{{ $detail['ticket_type_name'] }}</td>
                                        <td class="text-center">{{ $detail['quantity'] }}</td>
                                        <td class="text-end">RM {{ number_format($detail['unit_price'], 2) }}</td>
                                        <td class="text-end">RM {{ number_format($detail['total_price'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="3" class="text-end">Total Amount:</th>
                                        <th class="text-end">{{ $order->formatted_total }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="mb-4">
                        <h6 class="text-muted">Payment Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment->payment_method)) }}</p>
                                <p class="mb-1"><strong>Amount Paid:</strong> {{ $order->payment->formatted_amount }}</p>
                                <p class="mb-0"><strong>Transaction Date:</strong> {{ $order->payment->processed_at->format('F j, Y g:i A') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Transaction ID:</strong> {{ $order->payment->id }}</p>
                                <p class="mb-0"><strong>Status:</strong> <span class="badge bg-success">Completed</span></p>
                            </div>
                        </div>
                    </div>

                    @if($order->notes)
                    <!-- Additional Notes -->
                    <div class="mb-4">
                        <h6 class="text-muted">Additional Notes</h6>
                        <p class="text-muted">{{ $order->notes }}</p>
                    </div>
                    @endif

                    <!-- Footer -->
                    <div class="text-center mt-4 pt-4 border-top">
                        <p class="text-muted mb-2">
                            <strong>Thank you for choosing EventHub!</strong><br>
                            This is an automated receipt. Please keep this for your records.
                        </p>
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <button onclick="window.print()" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-print me-2"></i>Print Receipt
                            </button>
                            <a href="{{ route('events.show', $order->event->id) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-calendar me-2"></i>View Event
                            </a>
                            <a href="{{ route('customer.dashboard') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-ticket-alt me-2"></i>My Bookings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .navbar, .footer {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .receipt-header {
        border-bottom: 2px solid #000 !important;
        margin-bottom: 20px !important;
    }
}

.receipt-header {
    border-bottom: 2px solid #dee2e6;
    margin-bottom: 20px;
    padding-bottom: 15px;
}
</style>
@endsection
