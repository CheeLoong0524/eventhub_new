<?php

namespace App\Services;

use App\Models\EventOrderYf;
use App\Models\EventPaymentYf;
use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ReceiptService
{
    protected $pdf;

    public function __construct(PDF $pdf)
    {
        $this->pdf = $pdf;
    }
    /**
     * Generate a receipt for an order
     */
    public function generateReceipt(EventOrderYf $order): array
    {
        try {
            $order->load(['event', 'payment', 'user']);
            
            $receiptData = [
                'order' => $order,
                'event' => $order->event,
                'payment' => $order->payment,
                'user' => $order->user,
                'generated_at' => now(),
                'receipt_id' => 'RCP-' . strtoupper(uniqid()),
            ];

            return $receiptData;
        } catch (\Exception $e) {
            Log::error('Error generating receipt: ' . $e->getMessage());
            throw new \Exception('Failed to generate receipt: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF receipt
     */
    public function generatePdfReceipt(EventOrderYf $order): string
    {
        try {
            $receiptData = $this->generateReceipt($order);
            
            $pdf = $this->pdf->loadView('emails.receipt-pdf', $receiptData);
            $pdf->setPaper('A4', 'portrait');
            
            // Generate unique filename
            $filename = 'receipt_' . $order->order_number . '_' . time() . '.pdf';
            $filepath = 'receipts/' . $filename;
            
            // Store PDF in storage
            Storage::disk('public')->put($filepath, $pdf->output());
            
            return $filepath;
        } catch (\Exception $e) {
            Log::error('Error generating PDF receipt: ' . $e->getMessage());
            throw new \Exception('Failed to generate PDF receipt: ' . $e->getMessage());
        }
    }


    /**
     * Get receipt by order ID
     */
    public function getReceiptByOrderId(string $orderId): ?EventOrderYf
    {
        return EventOrderYf::where('order_number', $orderId)
            ->orWhere('id', $orderId)
            ->with(['event', 'payment', 'user'])
            ->first();
    }

    /**
     * Get all receipts for a user
     */
    public function getUserReceipts(int $userId, int $limit = 10): \Illuminate\Pagination\LengthAwarePaginator
    {
        return EventOrderYf::where('user_id', $userId)
            ->where('status', 'paid')
            ->with(['event', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    /**
     * Get receipt statistics
     */
    public function getReceiptStats(): array
    {
        $totalReceipts = EventOrderYf::where('status', 'paid')->count();
        $totalRevenue = EventOrderYf::where('status', 'paid')->sum('total_amount');
        $todayReceipts = EventOrderYf::where('status', 'paid')
            ->whereDate('created_at', today())
            ->count();
        $todayRevenue = EventOrderYf::where('status', 'paid')
            ->whereDate('created_at', today())
            ->sum('total_amount');

        return [
            'total_receipts' => $totalReceipts,
            'total_revenue' => $totalRevenue,
            'today_receipts' => $todayReceipts,
            'today_revenue' => $todayRevenue,
        ];
    }

    /**
     * Download PDF receipt
     */
    public function downloadPdfReceipt(EventOrderYf $order): \Illuminate\Http\Response
    {
        try {
            $receiptData = $this->generateReceipt($order);
            
            $pdf = $this->pdf->loadView('emails.receipt-pdf', $receiptData);
            $pdf->setPaper('A4', 'portrait');
            
            $filename = 'receipt_' . $order->order_number . '.pdf';
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Error downloading PDF receipt: ' . $e->getMessage());
            throw new \Exception('Failed to download PDF receipt: ' . $e->getMessage());
        }
    }

    /**
     * Generate receipt HTML for API response
     */
    public function generateReceiptHtml(EventOrderYf $order): string
    {
        try {
            $receiptData = $this->generateReceipt($order);
            
            return view('emails.receipt-html', $receiptData)->render();
        } catch (\Exception $e) {
            Log::error('Error generating receipt HTML: ' . $e->getMessage());
            throw new \Exception('Failed to generate receipt HTML: ' . $e->getMessage());
        }
    }
}
