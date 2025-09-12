<?php

namespace App\Strategies;

use App\Models\EventOrderYf;
use App\Services\ReceiptService;
use Illuminate\Support\Facades\Log;

class HtmlReceiptStrategy implements ReceiptStrategy
{
    protected $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

    public function generateReceipt(EventOrderYf $order): ReceiptResult
    {
        try {
            Log::info("Generating HTML receipt for order: " . $order->order_number);
            
            $htmlContent = $this->receiptService->generateReceiptHtml($order);
            
            Log::info("HTML receipt generated successfully");
            
            return new ReceiptResult(
                success: true,
                message: 'HTML receipt generated successfully',
                filePath: null, // HTML is generated on-the-fly
                downloadUrl: route('api.receipt.html', ['orderId' => $order->order_number]),
                metadata: [
                    'receipt_type' => 'html',
                    'order_number' => $order->order_number,
                    'content_length' => strlen($htmlContent),
                    'generated_at' => now()->toISOString()
                ]
            );
            
        } catch (\Exception $e) {
            Log::error("HTML receipt generation failed: " . $e->getMessage());
            
            return new ReceiptResult(
                success: false,
                message: 'HTML receipt generation failed: ' . $e->getMessage(),
                metadata: ['error' => $e->getMessage()]
            );
        }
    }

    public function getReceiptType(): string
    {
        return 'html';
    }

    public function canHandle(string $receiptType): bool
    {
        return $receiptType === 'html';
    }
}
