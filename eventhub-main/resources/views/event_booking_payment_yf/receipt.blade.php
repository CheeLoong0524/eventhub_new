<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $order->order_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            .card { border: none !important; box-shadow: none !important; }
        }
        .receipt-header {
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        .receipt-footer {
            border-top: 2px solid #dee2e6;
            padding-top: 1rem;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
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
                            <h4 class="text-success mb-0">PAYMENT RECEIPT</h4>
                            <p class="text-muted">Receipt #{{ $order->order_number }}</p>
                        </div>

                        <!-- Order Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">Order Information</h6>
                                <p class="mb-1"><strong>Order Number:</strong> {{ $order->order_number }}</p>
                                <p class="mb-1"><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
                                <p class="mb-1"><strong>Status:</strong> 
                                    <span class="badge bg-success">{{ ucfirst($order->status) }}</span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Customer Information</h6>
                                <p class="mb-1"><strong>Name:</strong> {{ $order->customer_name }}</p>
                                <p class="mb-1"><strong>Email:</strong> {{ $order->customer_email }}</p>
                                @if($order->customer_phone)
                                <p class="mb-1"><strong>Phone:</strong> {{ $order->customer_phone }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Event Information -->
                        <div class="mb-4">
                            <h6 class="text-muted">Event Information</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title mb-2">{{ $order->event->name }}</h5>
                                    <p class="card-text mb-1">
                                        <i class="fas fa-calendar me-2"></i>
                                        {{ $order->event->getFormattedDateTime() }}
                                    </p>
                                    <p class="card-text mb-1">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        {{ $order->event->venue }}, {{ $order->event->location }}
                                    </p>
                                    <p class="card-text mb-0">
                                        <i class="fas fa-tag me-2"></i>
                                        {{ $order->event->category }}
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
                                        @foreach($order->ticket_details as $ticket)
                                        <tr>
                                            <td>{{ $ticket['ticket_type_name'] }}</td>
                                            <td class="text-center">{{ $ticket['quantity'] }}</td>
                                            <td class="text-end">RM {{ number_format($ticket['unit_price'], 2) }}</td>
                                            <td class="text-end">RM {{ number_format($ticket['total_price'], 2) }}</td>
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
                                    <p class="mb-1"><strong>Payment Status:</strong> 
                                        <span class="badge bg-success">{{ ucfirst($order->payment->status) }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Amount Paid:</strong> {{ $order->payment->formatted_amount }}</p>
                                    @if($order->payment->processed_at)
                                    <p class="mb-1"><strong>Processed At:</strong> {{ $order->payment->processed_at->format('M d, Y \a\t g:i A') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        @if($order->notes)
                        <div class="mb-4">
                            <h6 class="text-muted">Notes</h6>
                            <p class="text-muted">{{ $order->notes }}</p>
                        </div>
                        @endif

                        <!-- Receipt Footer -->
                        <div class="receipt-footer text-center">
                            <p class="text-muted mb-2">
                                <strong>Thank you for choosing EventHub!</strong>
                            </p>
                            <p class="text-muted small mb-0">
                                Please bring this receipt and a valid ID to the event.<br>
                                For any questions, contact us at support@eventhub.com
                            </p>
                        </div>

                        <!-- Print Button -->
                        <div class="text-center mt-4 no-print">
                            <button onclick="window.print()" class="btn btn-primary me-2">
                                <i class="fas fa-print me-2"></i>Print Receipt
                            </button>
                            <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-home me-2"></i>Back to Events
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
