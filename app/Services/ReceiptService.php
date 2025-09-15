<?php
// Author: Gooi Ye Fan

namespace App\Services;

use App\Models\EventOrderYf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ReceiptService
{
    public function __construct()
    {
        // PDF facade will be used directly
    }

    /**
     * Generate a receipt for an order
     */
    public function generateReceipt(EventOrderYf $order): array
    {
        try {
            $order->load(['event.venue', 'payment', 'user']);
            
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
     * Generate receipt data for API
     */
    public function generateReceiptData(EventOrderYf $order): array
    {
        try {
            $receiptData = $this->generateReceipt($order);
            
            // Format data for API response
            return [
                'success' => true,
                'receipt' => [
                    'receipt_id' => $receiptData['receipt_id'],
                    'order_number' => $order->order_number,
                    'order_date' => $order->created_at->format('Y-m-d H:i:s'),
                    'customer' => [
                        'name' => $order->customer_name,
                        'email' => $order->customer_email,
                        'phone' => $order->customer_phone,
                    ],
                    'event' => [
                        'name' => $order->event->name,
                        'date' => $order->event->start_date,
                        'time' => $order->event->start_time,
                        'venue' => $order->event->venue->name ?? 'TBA',
                    ],
                    'payment' => [
                        'method' => $order->payment->payment_method,
                        'amount' => $order->payment->amount,
                        'status' => $order->payment->status,
                        'transaction_id' => $order->payment->transaction_id,
                    ],
                    'tickets' => [
                        'quantity' => $order->ticket_details ? array_sum(array_column($order->ticket_details, 'quantity')) : 0,
                        'unit_price' => $order->ticket_details && count($order->ticket_details) > 0 ? $order->ticket_details[0]['unit_price'] : 0,
                        'total_amount' => $order->total_amount,
                    ],
                    'generated_at' => $receiptData['generated_at']->format('Y-m-d H:i:s'),
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error generating receipt data: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Failed to generate receipt data: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get receipt by order ID
     */
    public function getReceiptByOrderId(string $orderId): ?EventOrderYf
    {
        return EventOrderYf::where('order_number', $orderId)
            ->orWhere('id', $orderId)
            ->with(['event.venue', 'payment', 'user'])
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
     * Get all receipts for a customer
     */
    public function getCustomerReceipts(string $customerId): array
    {
        try {
            $orders = EventOrderYf::where('user_id', $customerId)
                ->with(['event', 'payment', 'user'])
                ->orderBy('created_at', 'desc')
                ->get();

            $receipts = [];
            foreach ($orders as $order) {
                $receiptData = $this->generateReceiptData($order);
                if ($receiptData['success']) {
                    $receipts[] = $receiptData['receipt'];
                }
            }

            return $receipts;
        } catch (\Exception $e) {
            Log::error('Error getting customer receipts: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all receipts for an event
     */
    public function getEventReceipts(string $eventId): array
    {
        try {
            $orders = EventOrderYf::where('event_id', $eventId)
                ->with(['event', 'payment', 'user'])
                ->orderBy('created_at', 'desc')
                ->get();

            $receipts = [];
            foreach ($orders as $order) {
                $receiptData = $this->generateReceiptData($order);
                if ($receiptData['success']) {
                    $receipts[] = $receiptData['receipt'];
                }
            }

            return $receipts;
        } catch (\Exception $e) {
            Log::error('Error getting event receipts: ' . $e->getMessage());
            return [];
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