<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub - Receipt #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #6c757d;
            margin: 5px 0 0 0;
            font-size: 14px;
        }
        .receipt-title {
            text-align: center;
            color: #28a745;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .receipt-number {
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 25px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h3 {
            color: #495057;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-cell {
            display: table-cell;
            width: 50%;
            padding: 5px 10px 5px 0;
            vertical-align: top;
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
            padding: 15px;
            border-radius: 4px;
            border-left: 3px solid #007bff;
        }
        .event-title {
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 8px;
        }
        .event-details {
            color: #6c757d;
            font-size: 11px;
        }
        .ticket-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .ticket-table th,
        .ticket-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
            font-size: 11px;
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
            padding: 12px;
            border-radius: 4px;
            border-left: 3px solid #28a745;
        }
        .footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
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
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
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
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Order Number:</div>
                    <div class="info-value">{{ $order->order_number }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Customer Name:</div>
                    <div class="info-value">{{ $order->customer_name }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Order Date:</div>
                    <div class="info-value">{{ $order->created_at->format('F j, Y g:i A') }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Email:</div>
                    <div class="info-value">{{ $order->customer_email }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Status:</div>
                    <div class="info-value"><span class="status-badge status-paid">Paid</span></div>
                </div>
                @if($order->customer_phone)
                <div class="info-cell">
                    <div class="info-label">Phone:</div>
                    <div class="info-value">{{ $order->customer_phone }}</div>
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
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-cell">
                        <div class="info-label">Payment Method:</div>
                        <div class="info-value">{{ ucfirst(str_replace('_', ' ', $order->payment->payment_method)) }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-label">Amount Paid:</div>
                        <div class="info-value">{{ $order->payment->formatted_amount }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-cell">
                        <div class="info-label">Transaction Date:</div>
                        <div class="info-value">{{ $order->payment->processed_at->format('F j, Y g:i A') }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-label">Status:</div>
                        <div class="info-value"><span class="status-badge status-completed">Completed</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($order->notes)
    <!-- Additional Notes -->
    <div class="section">
        <h3>Additional Notes</h3>
        <div style="color: #6c757d; background-color: #f8f9fa; padding: 10px; border-radius: 4px; font-size: 11px;">{{ $order->notes }}</div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>Thank you for choosing EventHub!</strong></p>
        <p>This is an automated receipt. Please keep this for your records.</p>
        <p>If you have any questions, please contact our support team.</p>
        <p>Generated on: {{ $generated_at->format('F j, Y g:i A') }}</p>
    </div>
</body>
</html>
