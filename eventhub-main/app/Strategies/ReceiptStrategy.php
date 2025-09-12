<?php

namespace App\Strategies;

use App\Models\EventOrderYf;

interface ReceiptStrategy
{
    /**
     * Generate receipt for the given order
     */
    public function generateReceipt(EventOrderYf $order): ReceiptResult;

    /**
     * Get the receipt type name
     */
    public function getReceiptType(): string;

    /**
     * Check if this strategy can handle the receipt type
     */
    public function canHandle(string $receiptType): bool;
}

class ReceiptResult
{
    public function __construct(
        public bool $success,
        public string $message,
        public ?string $filePath = null,
        public ?string $downloadUrl = null,
        public array $metadata = []
    ) {}
}
