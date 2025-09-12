<?php

namespace App\Http\Controllers;

use App\Services\ReceiptService;
use App\Models\EventOrderYf;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReceiptController extends Controller
{
    protected $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

    /**
     * Generate receipt after payment
     * POST /api/receipt
     */
    public function generateReceipt(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|string',
            'send_email' => 'boolean',
            'generate_pdf' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $order = $this->receiptService->getReceiptByOrderId($request->order_id);
            
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            // Check if user has permission to access this order
            if (Auth::id() !== $order->user_id && !Auth::user()->hasRole('admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this order'
                ], 403);
            }

            $receiptData = $this->receiptService->generateReceipt($order);
            
            $response = [
                'success' => true,
                'message' => 'Receipt generated successfully',
                'data' => [
                    'receipt_id' => $receiptData['receipt_id'],
                    'order_number' => $order->order_number,
                    'generated_at' => $receiptData['generated_at']->toISOString(),
                    'order' => [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'status' => $order->status,
                        'total_amount' => $order->total_amount,
                        'formatted_total' => $order->formatted_total,
                        'customer_name' => $order->customer_name,
                        'customer_email' => $order->customer_email,
                        'created_at' => $order->created_at->toISOString(),
                    ],
                    'event' => [
                        'id' => $order->event->id,
                        'name' => $order->event->name,
                        'date' => $order->event->date,
                        'time' => $order->event->time,
                        'venue' => $order->event->venue,
                    ],
                    'payment' => [
                        'id' => $order->payment->id,
                        'payment_method' => $order->payment->payment_method,
                        'amount' => $order->payment->amount,
                        'currency' => $order->payment->currency,
                        'status' => $order->payment->status,
                        'processed_at' => $order->payment->processed_at?->toISOString(),
                    ],
                    'ticket_details' => $order->ticket_details,
                ]
            ];

            // Generate PDF if requested
            if ($request->boolean('generate_pdf')) {
                try {
                    $pdfPath = $this->receiptService->generatePdfReceipt($order);
                    $response['data']['pdf_url'] = asset('storage/' . $pdfPath);
                    $response['data']['pdf_download_url'] = route('api.receipt.download', ['order' => $order->order_number]);
                } catch (\Exception $e) {
                    $response['warnings'][] = 'PDF generation failed: ' . $e->getMessage();
                }
            }

            // Send email if requested
            if ($request->boolean('send_email')) {
                try {
                    $emailSent = $this->receiptService->sendReceiptEmail($order);
                    $response['data']['email_sent'] = $emailSent;
                    if ($emailSent) {
                        $response['data']['email_sent_at'] = now()->toISOString();
                    }
                } catch (\Exception $e) {
                    $response['warnings'][] = 'Email sending failed: ' . $e->getMessage();
                }
            }

            return response()->json($response, 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate receipt',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retrieve past receipts
     * GET /api/receipt/{orderId}
     */
    public function getReceipt(string $orderId): JsonResponse
    {
        try {
            $order = $this->receiptService->getReceiptByOrderId($orderId);
            
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Receipt not found'
                ], 404);
            }

            // Check if user has permission to access this order
            if (Auth::id() !== $order->user_id && !Auth::user()->hasRole('admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this receipt'
                ], 403);
            }

            $receiptData = $this->receiptService->generateReceipt($order);
            
            return response()->json([
                'success' => true,
                'message' => 'Receipt retrieved successfully',
                'data' => [
                    'receipt_id' => $receiptData['receipt_id'],
                    'order_number' => $order->order_number,
                    'generated_at' => $receiptData['generated_at']->toISOString(),
                    'order' => [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'status' => $order->status,
                        'total_amount' => $order->total_amount,
                        'formatted_total' => $order->formatted_total,
                        'customer_name' => $order->customer_name,
                        'customer_email' => $order->customer_email,
                        'created_at' => $order->created_at->toISOString(),
                    ],
                    'event' => [
                        'id' => $order->event->id,
                        'name' => $order->event->name,
                        'date' => $order->event->date,
                        'time' => $order->event->time,
                        'venue' => $order->event->venue,
                    ],
                    'payment' => [
                        'id' => $order->payment->id,
                        'payment_method' => $order->payment->payment_method,
                        'amount' => $order->payment->amount,
                        'currency' => $order->payment->currency,
                        'status' => $order->payment->status,
                        'processed_at' => $order->payment->processed_at?->toISOString(),
                    ],
                    'ticket_details' => $order->ticket_details,
                    'pdf_download_url' => route('api.receipt.download', ['order' => $order->order_number]),
                    'html_url' => route('api.receipt.html', ['order' => $order->order_number]),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve receipt',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's receipts
     * GET /api/receipts
     */
    public function getUserReceipts(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);
            $receipts = $this->receiptService->getUserReceipts(Auth::id(), $limit);
            
            return response()->json([
                'success' => true,
                'message' => 'Receipts retrieved successfully',
                'data' => [
                    'receipts' => $receipts->items(),
                    'pagination' => [
                        'current_page' => $receipts->currentPage(),
                        'last_page' => $receipts->lastPage(),
                        'per_page' => $receipts->perPage(),
                        'total' => $receipts->total(),
                        'from' => $receipts->firstItem(),
                        'to' => $receipts->lastItem(),
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve receipts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download PDF receipt
     * GET /api/receipt/{orderId}/download
     */
    public function downloadReceipt(string $orderId): Response
    {
        try {
            $order = $this->receiptService->getReceiptByOrderId($orderId);
            
            if (!$order) {
                abort(404, 'Receipt not found');
            }

            // Check if user has permission to access this order
            if (Auth::id() !== $order->user_id && !Auth::user()->hasRole('admin')) {
                abort(403, 'Unauthorized access to this receipt');
            }

            return $this->receiptService->downloadPdfReceipt($order);

        } catch (\Exception $e) {
            abort(500, 'Failed to download receipt: ' . $e->getMessage());
        }
    }

    /**
     * Get receipt as HTML
     * GET /api/receipt/{orderId}/html
     */
    public function getReceiptHtml(string $orderId): Response
    {
        try {
            $order = $this->receiptService->getReceiptByOrderId($orderId);
            
            if (!$order) {
                abort(404, 'Receipt not found');
            }

            // Check if user has permission to access this order
            if (Auth::id() !== $order->user_id && !Auth::user()->hasRole('admin')) {
                abort(403, 'Unauthorized access to this receipt');
            }

            $html = $this->receiptService->generateReceiptHtml($order);
            
            return response($html, 200, [
                'Content-Type' => 'text/html',
                'Content-Disposition' => 'inline; filename="receipt_' . $order->order_number . '.html"'
            ]);

        } catch (\Exception $e) {
            abort(500, 'Failed to generate HTML receipt: ' . $e->getMessage());
        }
    }

    /**
     * Get receipt statistics (Admin only)
     * GET /api/receipts/stats
     */
    public function getReceiptStats(): JsonResponse
    {
        try {
            // Check if user is admin
            if (!Auth::user()->hasRole('admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $stats = $this->receiptService->getReceiptStats();
            
            return response()->json([
                'success' => true,
                'message' => 'Receipt statistics retrieved successfully',
                'data' => $stats
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve receipt statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
