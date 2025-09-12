<?php

namespace App\Strategies;

use App\Models\EventOrderYf;
use App\Services\ReceiptService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PdfReceiptStrategy implements ReceiptStrategy
{
    protected $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

    public function generateReceipt(EventOrderYf $order): ReceiptResult
    {
        try {
            Log::info("Generating PDF receipt for order: " . $order->order_number);
            
            $pdfPath = $this->receiptService->generatePdfReceipt($order);
            
            Log::info("PDF receipt generated successfully: " . $pdfPath);
            
            return new ReceiptResult(
                success: true,
                message: 'PDF receipt generated successfully',
                filePath: $pdfPath,
                downloadUrl: Storage::disk('public')->url($pdfPath),
                metadata: [
                    'receipt_type' => 'pdf',
                    'order_number' => $order->order_number,
                    'file_size' => Storage::disk('public')->size($pdfPath),
                    'generated_at' => now()->toISOString()
                ]
            );
            
        } catch (\Exception $e) {
            Log::error("PDF receipt generation failed: " . $e->getMessage());
            
            return new ReceiptResult(
                success: false,
                message: 'PDF receipt generation failed: ' . $e->getMessage(),
                metadata: ['error' => $e->getMessage()]
            );
        }
    }

    public function getReceiptType(): string
    {
        return 'pdf';
    }

    public function canHandle(string $receiptType): bool
    {
        return $receiptType === 'pdf';
    }
}
