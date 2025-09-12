<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub - Receipt #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .receipt-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            color: #6c757d;
            margin: 5px 0 0 0;
        }
        .receipt-title {
            text-align: center;
            color: #28a745;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .receipt-number {
            text-align: center;
            color: #6c757d;
            font-size: 16px;
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section h3 {
            color: #495057;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .info-item {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
        }
        .info-value {
            color: #6c757d;
        }
        .event-card {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            border-left: 4px solid #007bff;
        }
        .event-title {
            font-size: 20px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .event-details {
            color: #6c757d;
        }
        .ticket-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .ticket-table th,
        .ticket-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        .ticket-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }
        .ticket-table .text-right {
            text-align: right;
        }
        .ticket-table .text-center {
            text-align: center;
        }
        .total-row {
            font-weight: bold;
            background-color: #e9ecef;
        }
        .payment-info {
            background-color: #d4edda;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #28a745;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }
        .status-completed {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            .receipt-container {
                box-shadow: none;
                padding: 0;
            }
            .btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="header">
            <h1>🎫 EventHub</h1>
            <p>Your Premier Event Management Platform</p>
        </div>

        <!-- Receipt Title -->
        <div class="receipt-title">EVENT BOOKING RECEIPT</div>
        <div class="receipt-number">Receipt #{{ $order->order_number }}</div>

        <!-- Order Information -->
        <div class="section">
            <h3>Order Information</h3>
            <div class="info-grid">
                <div>
                    <div class="info-item">
                        <span class="info-label">Order Number:</span><br>
                        <span class="info-value">{{ $order->order_number }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Order Date:</span><br>
                        <span class="info-value">{{ $order->created_at->format('F j, Y g:i A') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status:</span><br>
                        <span class="status-badge status-paid">Paid</span>
                    </div>
                </div>
                <div>
                    <div class="info-item">
                        <span class="info-label">Customer Name:</span><br>
                        <span class="info-value">{{ $order->customer_name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email:</span><br>
                        <span class="info-value">{{ $order->customer_email }}</span>
                    </div>
                    @if($order->customer_phone)
                    <div class="info-item">
                        <span class="info-label">Phone:</span><br>
                        <span class="info-value">{{ $order->customer_phone }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Event Information -->
        <div class="section">
            <h3>Event Information</h3>
            <div class="event-card">
                <div class="event-title">{{ $order->event->name }}</div>
                <div class="event-details">
                    📅 {{ $order->event->getFormattedDateTime() }}<br>
                    📍 {{ $order->event->venue }}<br>
                    ℹ️ {{ $order->event->description }}
                </div>
            </div>
        </div>

        <!-- Ticket Details -->
        <div class="section">
            <h3>Ticket Details</h3>
            <table class="ticket-table">
                <thead>
                    <tr>
                        <th>Ticket Type</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-right">Unit Price</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->ticket_details as $detail)
                    <tr>
                        <td>{{ $detail['ticket_type_name'] }}</td>
                        <td class="text-center">{{ $detail['quantity'] }}</td>
                        <td class="text-right">RM {{ number_format($detail['unit_price'], 2) }}</td>
                        <td class="text-right">RM {{ number_format($detail['total_price'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="3" class="text-right">Total Amount:</td>
                        <td class="text-right">{{ $order->formatted_total }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Payment Information -->
        <div class="section">
            <h3>Payment Information</h3>
            <div class="payment-info">
                <div class="info-item">
                    <span class="info-label">Payment Method:</span>
                    <span class="info-value">{{ ucfirst(str_replace('_', ' ', $order->payment->payment_method)) }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Amount Paid:</span>
                    <span class="info-value">{{ $order->payment->formatted_amount }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Transaction Date:</span>
                    <span class="info-value">{{ $order->payment->processed_at->format('F j, Y g:i A') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span class="status-badge status-completed">Completed</span>
                </div>
            </div>
        </div>

        @if($order->notes)
        <!-- Additional Notes -->
        <div class="section">
            <h3>Additional Notes</h3>
            <p style="color: #6c757d; background-color: #f8f9fa; padding: 15px; border-radius: 6px;">{{ $order->notes }}</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p><strong>Thank you for choosing EventHub!</strong></p>
            <p>This is an automated receipt. Please keep this for your records.</p>
            <p>Generated on: {{ $generated_at->format('F j, Y g:i A') }}</p>
            
            <div style="margin-top: 20px;">
                <button onclick="window.print()" class="btn">Print Receipt</button>
                <a href="{{ route('events.show', $order->event->id) }}" class="btn">View Event</a>
                <a href="{{ route('customer.dashboard') }}" class="btn">My Bookings</a>
            </div>
        </div>
    </div>
</body>
</html>
