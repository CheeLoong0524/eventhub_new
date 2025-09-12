# Receipt Service API Documentation

## Overview
The Receipt Service API provides comprehensive receipt generation, PDF creation, and email delivery functionality for the EventHub platform. This service automatically generates receipts after successful payments and provides various endpoints for retrieving and managing receipts.

## Features
- ✅ Generate receipts after payment completion
- ✅ PDF receipt generation with professional formatting
- ✅ Email delivery with PDF attachments
- ✅ HTML receipt generation for web display
- ✅ Receipt retrieval by order ID
- ✅ User receipt history
- ✅ Admin statistics and reporting
- ✅ Secure access control

## API Endpoints

### 1. Generate Receipt
**POST** `/api/receipt`

Generates a receipt for a completed order.

**Request Body:**
```json
{
    "order_id": "ORD-ABC123",
    "send_email": true,
    "generate_pdf": true
}
```

**Response:**
```json
{
    "success": true,
    "message": "Receipt generated successfully",
    "data": {
        "receipt_id": "RCP-XYZ789",
        "order_number": "ORD-ABC123",
        "generated_at": "2025-01-09T10:44:00.000Z",
        "order": {
            "id": 1,
            "order_number": "ORD-ABC123",
            "status": "paid",
            "total_amount": "150.00",
            "formatted_total": "RM 150.00",
            "customer_name": "John Doe",
            "customer_email": "john@example.com",
            "created_at": "2025-01-09T10:30:00.000Z"
        },
        "event": {
            "id": 1,
            "name": "Tech Conference 2025",
            "date": "2025-02-15",
            "time": "09:00:00",
            "venue": "Convention Center"
        },
        "payment": {
            "id": 1,
            "payment_method": "stripe",
            "amount": "150.00",
            "currency": "MYR",
            "status": "completed",
            "processed_at": "2025-01-09T10:30:15.000Z"
        },
        "ticket_details": [
            {
                "ticket_type_id": 1,
                "ticket_type_name": "General Admission",
                "quantity": 2,
                "unit_price": "75.00",
                "total_price": "150.00"
            }
        ],
        "pdf_url": "http://localhost/storage/receipts/receipt_ORD-ABC123_1641234567.pdf",
        "pdf_download_url": "http://localhost/api/receipt/ORD-ABC123/download",
        "email_sent": true,
        "email_sent_at": "2025-01-09T10:44:05.000Z"
    }
}
```

### 2. Get Receipt by Order ID
**GET** `/api/receipt/{orderId}`

Retrieves a specific receipt by order number or ID.

**Response:**
```json
{
    "success": true,
    "message": "Receipt retrieved successfully",
    "data": {
        "receipt_id": "RCP-XYZ789",
        "order_number": "ORD-ABC123",
        "generated_at": "2025-01-09T10:44:00.000Z",
        "order": { /* order details */ },
        "event": { /* event details */ },
        "payment": { /* payment details */ },
        "ticket_details": [ /* ticket details */ ],
        "pdf_download_url": "http://localhost/api/receipt/ORD-ABC123/download",
        "html_url": "http://localhost/api/receipt/ORD-ABC123/html"
    }
}
```

### 3. Get User Receipts
**GET** `/api/receipts`

Retrieves all receipts for the authenticated user.

**Query Parameters:**
- `limit` (optional): Number of receipts per page (default: 10)

**Response:**
```json
{
    "success": true,
    "message": "Receipts retrieved successfully",
    "data": {
        "receipts": [
            {
                "id": 1,
                "order_number": "ORD-ABC123",
                "status": "paid",
                "total_amount": "150.00",
                "created_at": "2025-01-09T10:30:00.000Z",
                "event": { /* event details */ },
                "payment": { /* payment details */ }
            }
        ],
        "pagination": {
            "current_page": 1,
            "last_page": 5,
            "per_page": 10,
            "total": 50,
            "from": 1,
            "to": 10
        }
    }
}
```

### 4. Download PDF Receipt
**GET** `/api/receipt/{orderId}/download`

Downloads a PDF receipt for the specified order.

**Response:** PDF file download

### 5. Get HTML Receipt
**GET** `/api/receipt/{orderId}/html`

Retrieves a receipt as HTML for web display.

**Response:** HTML content

### 6. Get Receipt Statistics (Admin Only)
**GET** `/api/receipts/stats`

Retrieves receipt statistics for admin dashboard.

**Response:**
```json
{
    "success": true,
    "message": "Receipt statistics retrieved successfully",
    "data": {
        "total_receipts": 150,
        "total_revenue": "22500.00",
        "today_receipts": 5,
        "today_revenue": "750.00"
    }
}
```

## Authentication
All API endpoints require authentication. Include the authentication token in the request headers:

```
Authorization: Bearer {your-token}
```

## Error Responses

### 404 - Receipt Not Found
```json
{
    "success": false,
    "message": "Receipt not found"
}
```

### 403 - Unauthorized Access
```json
{
    "success": false,
    "message": "Unauthorized access to this receipt"
}
```

### 422 - Validation Error
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "order_id": ["The order_id field is required."]
    }
}
```

### 500 - Server Error
```json
{
    "success": false,
    "message": "Failed to generate receipt",
    "error": "Database connection failed"
}
```

## Integration Examples

### JavaScript/Frontend Integration

```javascript
// Generate receipt after payment
async function generateReceipt(orderId) {
    try {
        const response = await fetch('/api/receipt', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${authToken}`
            },
            body: JSON.stringify({
                order_id: orderId,
                send_email: true,
                generate_pdf: true
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            console.log('Receipt generated:', data.data);
            // Show success message
            showNotification('Receipt generated and sent to your email!');
        } else {
            console.error('Error:', data.message);
        }
    } catch (error) {
        console.error('Network error:', error);
    }
}

// Download PDF receipt
function downloadReceipt(orderId) {
    window.open(`/api/receipt/${orderId}/download`, '_blank');
}

// Get user receipts
async function getUserReceipts() {
    try {
        const response = await fetch('/api/receipts', {
            headers: {
                'Authorization': `Bearer ${authToken}`
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            displayReceipts(data.data.receipts);
        }
    } catch (error) {
        console.error('Error fetching receipts:', error);
    }
}
```

### PHP/Laravel Integration

```php
use App\Services\ReceiptService;

// Generate receipt
$receiptService = app(ReceiptService::class);
$order = EventOrderYf::find($orderId);

// Generate PDF
$pdfPath = $receiptService->generatePdfReceipt($order);

// Send email
$emailSent = $receiptService->sendReceiptEmail($order, $pdfPath);

// Get user receipts
$receipts = $receiptService->getUserReceipts($userId, 10);
```

## Email Templates

The service includes three email templates:

1. **receipt-email.blade.php** - Main email template with styling
2. **receipt-pdf.blade.php** - PDF generation template
3. **receipt-html.blade.php** - HTML display template

## File Storage

PDF receipts are stored in `storage/app/public/receipts/` and are accessible via the public URL:
- Storage path: `storage/app/public/receipts/receipt_ORD-ABC123_1641234567.pdf`
- Public URL: `http://your-domain.com/storage/receipts/receipt_ORD-ABC123_1641234567.pdf`

## Security Features

- **Authentication Required**: All endpoints require valid authentication
- **Authorization Checks**: Users can only access their own receipts
- **Admin Access**: Admin users can access all receipts and statistics
- **Input Validation**: All inputs are validated before processing
- **Error Handling**: Comprehensive error handling and logging

## Configuration

The service uses the following configuration:

- **PDF Generation**: Uses DomPDF library
- **Email Delivery**: Uses Laravel Mail system
- **File Storage**: Uses Laravel Storage system
- **Database**: Uses existing EventOrderYf and EventPaymentYf models

## Troubleshooting

### Common Issues

1. **PDF Generation Fails**
   - Check if DomPDF is properly installed
   - Verify storage permissions
   - Check Laravel logs for detailed error messages

2. **Email Not Sending**
   - Verify mail configuration in `.env`
   - Check SMTP settings
   - Verify email addresses are valid

3. **Storage Issues**
   - Ensure storage directory exists and is writable
   - Run `php artisan storage:link` to create symlink
   - Check file permissions

### Logs

Check the following logs for debugging:
- `storage/logs/laravel.log` - General application logs
- Receipt generation logs include detailed information about the process

## Support

For technical support or questions about the Receipt Service API, please contact the development team or refer to the Laravel documentation for additional guidance.
