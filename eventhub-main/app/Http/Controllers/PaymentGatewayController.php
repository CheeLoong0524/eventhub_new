<?php

namespace App\Http\Controllers;

use App\Models\EventOrderYf;
use App\Models\EventPaymentYf;
use App\Models\TicketType;
use App\Services\PaymentService;
use App\Services\ReceiptService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentGatewayController extends Controller
{
    protected $paymentService;
    protected $receiptService;

    public function __construct(PaymentService $paymentService, ReceiptService $receiptService)
    {
        $this->paymentService = $paymentService;
        $this->receiptService = $receiptService;
    }

    /**
     * Show payment gateway form based on payment method
     */
    public function showGateway(Request $request): View|RedirectResponse
    {
        $paymentMethod = $request->input('payment_method');
        $orderNumber = $request->input('order_number');
        
        if (!$paymentMethod || !$orderNumber) {
            return redirect()->route('cart.index')->with('error', 'Invalid payment request.');
        }

        $order = EventOrderYf::where('order_number', $orderNumber)->first();
        if (!$order) {
            return redirect()->route('cart.index')->with('error', 'Order not found.');
        }

        return view('payment-gateway.' . $paymentMethod, compact('order', 'paymentMethod'));
    }

    /**
     * Process payment gateway submission
     */
    public function processGateway(Request $request): RedirectResponse
    {
        Log::info("Payment gateway processing started", $request->all());
        
        try {
            $request->validate([
                'payment_method' => 'required|in:stripe,tng_ewallet,bank_transfer',
                'order_number' => 'required|string',
            ]);

            $paymentMethod = $request->input('payment_method');
            $orderNumber = $request->input('order_number');
            
            Log::info("Payment method: {$paymentMethod}, Order number: {$orderNumber}");
            
            $order = EventOrderYf::where('order_number', $orderNumber)->first();
            if (!$order) {
                Log::error("Order not found: {$orderNumber}");
                return redirect()->route('cart.index')->with('error', 'Order not found.');
            }

            // Validate payment method specific fields
            $validationRules = $this->getValidationRules($paymentMethod);
            Log::info("Validation rules for {$paymentMethod}:", $validationRules);
            
            $request->validate($validationRules);
            
            Log::info("Validation passed for {$paymentMethod}");
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Validation failed: " . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error("Error in payment gateway processing: " . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }

        // Simulate payment processing with gateway
        $result = $this->simulateGatewayProcessing($paymentMethod, $request, $order);

        if ($result['success']) {
            // Mark order as paid and update ticket availability
            $this->completePayment($order, $request);
            
            return redirect()->route('event-booking.payment-success-yf', ['order' => $orderNumber])
                ->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    /**
     * Get validation rules based on payment method
     */
    private function getValidationRules(string $paymentMethod): array
    {
        $baseRules = [
            'payment_method' => 'required|in:stripe,tng_ewallet,bank_transfer',
            'order_number' => 'required|string',
        ];

        switch ($paymentMethod) {
            case 'stripe':
                return array_merge($baseRules, [
                    'card_number' => 'required|string|min:16|max:19|regex:/^[0-9\s]+$/',
                    'expiry_month' => 'required|integer|min:1|max:12',
                    'expiry_year' => 'required|integer|min:' . date('Y'),
                    'cvv' => 'required|string|min:3|max:4|regex:/^[0-9]+$/',
                    'cardholder_name' => 'required|string|max:255',
                ]);

            case 'tng_ewallet':
                return array_merge($baseRules, [
                    'tng_phone' => 'required|string|min:9|max:10|regex:/^[0-9]+$/',
                    'tng_pin' => 'required|string|min:6|max:6|regex:/^[0-9]+$/',
                ]);

            case 'bank_transfer':
                return array_merge($baseRules, [
                    'bank_name' => 'required|string|max:255',
                    'account_number' => 'required|string|min:8|max:20',
                    'account_holder_name' => 'required|string|max:255',
                ]);

            default:
                return $baseRules;
        }
    }

    /**
     * Simulate gateway processing
     */
    private function simulateGatewayProcessing(string $paymentMethod, Request $request, EventOrderYf $order): array
    {
        Log::info("Simulating {$paymentMethod} gateway processing for order: " . $order->order_number);

        // Simulate processing delay
        usleep(500000); // 0.5 seconds

        switch ($paymentMethod) {
            case 'stripe':
                return $this->simulateStripeProcessing($request, $order);
            
            case 'tng_ewallet':
                return $this->simulateTngEwalletProcessing($request, $order);
            
            case 'bank_transfer':
                return $this->simulateBankTransferProcessing($request, $order);
            
            default:
                return [
                    'success' => false,
                    'message' => 'Unsupported payment method'
                ];
        }
    }

    /**
     * Simulate Stripe processing
     */
    private function simulateStripeProcessing(Request $request, EventOrderYf $order): array
    {
        $cardNumber = str_replace(' ', '', $request->input('card_number')); // Remove spaces
        $expiryMonth = $request->input('expiry_month');
        $expiryYear = $request->input('expiry_year');
        $cvv = $request->input('cvv');

        Log::info("Stripe processing - Card: {$cardNumber}, Month: {$expiryMonth}, Year: {$expiryYear}, CVV: {$cvv}");

        // Simulate card validation
        if (strlen($cardNumber) < 16) {
            return [
                'success' => false,
                'message' => 'Invalid card number. Please check your card details.'
            ];
        }

        // Simulate expired card
        if ($expiryYear < date('Y') || ($expiryYear == date('Y') && $expiryMonth < date('n'))) {
            return [
                'success' => false,
                'message' => 'Card has expired. Please use a different card.'
            ];
        }

        // Simulate CVV validation
        if (strlen($cvv) < 3) {
            return [
                'success' => false,
                'message' => 'Invalid CVV. Please check your card details.'
            ];
        }

        // Simulate random failure (5% chance)
        if (rand(1, 100) <= 5) {
            return [
                'success' => false,
                'message' => 'Payment declined by bank. Please try a different card or contact your bank.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Payment processed successfully via Stripe! Your card ending in ' . substr($cardNumber, -4) . ' has been charged.'
        ];
    }

    /**
     * Simulate TNG eWallet processing
     */
    private function simulateTngEwalletProcessing(Request $request, EventOrderYf $order): array
    {
        $phone = $request->input('tng_phone');
        $pin = $request->input('tng_pin');

        Log::info("TNG eWallet processing - Phone: {$phone}, PIN: {$pin}");

        // Simulate TNG eWallet phone validation
        if (strlen($phone) < 9 || strlen($phone) > 10) {
            return [
                'success' => false,
                'message' => 'Invalid phone number. Please enter a valid Malaysian phone number.'
            ];
        }

        // Simulate PIN validation
        if (strlen($pin) !== 6) {
            return [
                'success' => false,
                'message' => 'Invalid PIN. Please enter your 6-digit TNG eWallet PIN.'
            ];
        }

        // Simulate random failure (2% chance)
        if (rand(1, 100) <= 2) {
            return [
                'success' => false,
                'message' => 'TNG eWallet payment failed. Please check your balance or try again.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Payment processed successfully via TNG eWallet! Amount deducted from +60' . $phone
        ];
    }

    /**
     * Simulate Bank Transfer processing
     */
    private function simulateBankTransferProcessing(Request $request, EventOrderYf $order): array
    {
        $bankName = $request->input('bank_name');
        $accountNumber = $request->input('account_number');
        $accountHolderName = $request->input('account_holder_name');

        // Simulate bank account validation
        if (strlen($accountNumber) < 8) {
            return [
                'success' => false,
                'message' => 'Invalid account number. Please check your bank account details.'
            ];
        }

        // Simulate random failure (2% chance)
        if (rand(1, 100) <= 2) {
            return [
                'success' => false,
                'message' => 'Bank transfer verification failed. Please check your account details.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Bank transfer initiated successfully! Please complete the transfer using the provided details.'
        ];
    }

    /**
     * Complete the payment process after successful gateway processing
     */
    private function completePayment(EventOrderYf $order, Request $request): void
    {
        Log::info("Completing payment for order: " . $order->order_number);
        
        DB::beginTransaction();
        
        try {
            // Mark order as paid
            $order->markAsPaid();
            
            // Update payment record
            $payment = $order->payment ?? EventPaymentYf::where('order_id', $order->id)->first();
            if ($payment) {
                $payment->update([
                    'status' => 'completed',
                    'transaction_id' => $request->input('payment_method') . '_' . uniqid(),
                    'processed_at' => now()
                ]);
            }
            
            // Update ticket availability
            $this->updateTicketAvailability($order);
            
            // Generate receipt
            try {
                $pdfPath = $this->receiptService->generatePdfReceipt($order);
                Log::info("PDF receipt generated for order: " . $order->order_number . " at: " . $pdfPath);
            } catch (\Exception $e) {
                Log::error("Error generating receipt for order " . $order->order_number . ": " . $e->getMessage());
            }
            
            // Clear user's cart
            $totalItemsCleared = \App\Models\Cart::clearAllForUser($order->user_id);
            Log::info("Total items cleared for user " . $order->user_id . ": " . $totalItemsCleared);
            
            // Also clear the cart from session if it exists
            session()->forget('cart_id');
            
            // Force clear any cached cart data
            if (Auth::check() && Auth::id() == $order->user_id) {
                // Clear any cached cart data for the current user
                \Cache::forget('cart_' . $order->user_id);
            }
            
            DB::commit();
            Log::info("Payment completed successfully for order: " . $order->order_number);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error completing payment for order " . $order->order_number . ": " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update ticket availability after successful payment
     */
    private function updateTicketAvailability(EventOrderYf $order): void
    {
        Log::info("Starting ticket availability update for order: {$order->order_number}");
        
        $order->refresh();
        
        if ($order->status !== 'paid') {
            Log::warning("Order {$order->order_number} is not paid yet - skipping ticket availability update");
            return;
        }
        
        if ($order->tickets_processed) {
            Log::warning("Order {$order->order_number} tickets have already been processed - skipping ticket availability update");
            return;
        }
        
        DB::beginTransaction();
        
        try {
            $freshOrder = EventOrderYf::where('id', $order->id)
                ->where('status', 'paid')
                ->where('tickets_processed', false)
                ->lockForUpdate()
                ->first();
                
            if (!$freshOrder) {
                Log::warning("Order {$order->order_number} is not eligible for ticket processing");
                DB::rollBack();
                return;
            }
        
            foreach ($freshOrder->ticket_details as $detail) {
                $ticketType = TicketType::find($detail['ticket_type_id']);
                if ($ticketType) {
                    $beforeAvailable = $ticketType->available_quantity;
                    $beforeSold = $ticketType->sold_quantity;
                    
                    Log::info("Before update - Ticket type {$ticketType->id} ({$ticketType->name}): available={$beforeAvailable}, sold={$beforeSold}");
                    
                    $affectedRows = DB::table('ticket_types')
                        ->where('id', $ticketType->id)
                        ->where('available_quantity', '>=', $detail['quantity'])
                        ->update([
                            'sold_quantity' => DB::raw('sold_quantity + ' . $detail['quantity']),
                            'available_quantity' => DB::raw('available_quantity - ' . $detail['quantity']),
                            'updated_at' => now()
                        ]);
                    
                    if ($affectedRows === 0) {
                        Log::error("Failed to update ticket availability for ticket type {$ticketType->id}");
                        throw new \Exception("Insufficient tickets available for {$ticketType->name}");
                    }
                    
                    $ticketType->refresh();
                    $afterAvailable = $ticketType->available_quantity;
                    $afterSold = $ticketType->sold_quantity;
                    
                    Log::info("After update - Ticket type {$ticketType->id} ({$ticketType->name}): available={$afterAvailable}, sold={$afterSold}");
                } else {
                    Log::error("Ticket type not found for ID: {$detail['ticket_type_id']}");
                    throw new \Exception("Ticket type not found");
                }
            }
            
            $freshOrder->update(['tickets_processed' => true]);
            
            DB::commit();
            Log::info("Completed ticket availability update for order: {$freshOrder->order_number}");
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to update ticket availability for order {$order->order_number}: " . $e->getMessage());
            throw $e;
        }
    }
}
