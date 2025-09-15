<?php

namespace App\Http\Controllers;

use App\Models\EventOrderYf;
use App\Models\EventPaymentYf;
use App\Services\PaymentService;
use App\Services\PaymentStrategyFactory;
use App\Services\ReceiptService;
use App\Services\EventService;
use Illuminate\Support\Facades\Http;
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
            
            // Additional custom validation for Stripe
            if ($paymentMethod === 'stripe') {
                $this->validateStripeData($request);
            }
            
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
            
            Log::info("Redirecting to payment success for order: " . $orderNumber);
            return redirect()->route('event-booking.payment-success-yf', ['order' => $orderNumber])
                ->with('success', $result['message']);
        } else {
            Log::error("Payment failed for order: " . $orderNumber . " - " . $result['message']);
            return redirect()->back()->with('error', $result['message']);
        }
    }

    /**
     * Get validation rules based on payment method
     */
    private function validateStripeData(Request $request): void
    {
        $expiryDate = $request->input('expiry_date');
        
        // Validate expiry date format (MM/YY)
        if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiryDate)) {
            throw new \Illuminate\Validation\ValidationException(
                validator([], []),
                ['expiry_date' => ['The expiry date must be in MM/YY format.']]
            );
        }
        
        // Validate year (must be 2025 or later)
        $parts = explode('/', $expiryDate);
        $year = 2000 + (int)$parts[1];
        if ($year < 2025) {
            throw new \Illuminate\Validation\ValidationException(
                validator([], []),
                ['expiry_date' => ['The expiry year must be 2025 or later.']]
            );
        }
        
        // Validate card number (remove spaces and check length)
        $cardNumber = str_replace(' ', '', $request->input('card_number'));
        if (strlen($cardNumber) !== 16 || !ctype_digit($cardNumber)) {
            throw new \Illuminate\Validation\ValidationException(
                validator([], []),
                ['card_number' => ['The card number must be exactly 16 digits.']]
            );
        }
    }

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
                    'expiry_date' => 'required|string|size:5',
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
     * Simulate gateway processing using Strategy Pattern
     */
    private function simulateGatewayProcessing(string $paymentMethod, Request $request, EventOrderYf $order): array
    {
        Log::info("Simulating {$paymentMethod} gateway processing for order: " . $order->order_number);

        try {
            // Use Strategy Pattern
            $strategy = PaymentStrategyFactory::create($paymentMethod);
            $result = $strategy->processPayment([$order], $request);
            
            Log::info("Strategy gateway processing completed", [
                'payment_method' => $paymentMethod,
                'order_number' => $order->order_number,
                'success' => $result->success,
                'message' => $result->message
            ]);
            
            return [
                'success' => $result->success,
                'message' => $result->message
            ];
            
        } catch (\Exception $e) {
            Log::error("Gateway processing failed: " . $e->getMessage(), [
                'payment_method' => $paymentMethod,
                'order_number' => $order->order_number,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Simulate Stripe processing
     */
    private function simulateStripeProcessing(Request $request, EventOrderYf $order): array
    {
        try {
            Log::info("Starting Stripe processing for order: " . $order->order_number);
            
            $cardNumber = str_replace(' ', '', $request->input('card_number')); // Remove spaces
            $expiryDate = $request->input('expiry_date'); // MM/YY format
            $cvv = $request->input('cvv');
            $cardholderName = $request->input('cardholder_name');

            Log::info("Stripe processing - Card: {$cardNumber}, Expiry: {$expiryDate}, CVV: {$cvv}, Name: {$cardholderName}");

            // Simulate card validation
            if (strlen($cardNumber) < 16) {
                Log::error("Stripe validation failed - Card number too short: " . strlen($cardNumber));
                return [
                    'success' => false,
                    'message' => 'Invalid card number. Please check your card details.'
                ];
            }

            // Parse expiry date (MM/YY format)
            if (!preg_match('/^(\d{2})\/(\d{2})$/', $expiryDate, $matches)) {
                Log::error("Stripe validation failed - Invalid expiry date format: " . $expiryDate);
                return [
                    'success' => false,
                    'message' => 'Invalid expiry date format. Please use MM/YY format.'
                ];
            }

            $expiryMonth = (int)$matches[1];
            $expiryYear = 2000 + (int)$matches[2]; // Convert YY to YYYY

            Log::info("Parsed expiry - Month: {$expiryMonth}, Year: {$expiryYear}");

            // Simulate expired card
            if ($expiryYear < date('Y') || ($expiryYear == date('Y') && $expiryMonth < date('n'))) {
                Log::error("Stripe validation failed - Card expired: {$expiryMonth}/{$expiryYear}");
                return [
                    'success' => false,
                    'message' => 'Card has expired. Please use a different card.'
                ];
            }

            // Simulate CVV validation
            if (strlen($cvv) < 3) {
                Log::error("Stripe validation failed - CVV too short: " . strlen($cvv));
                return [
                    'success' => false,
                    'message' => 'Invalid CVV. Please check your card details.'
                ];
            }


            Log::info("Stripe processing successful for order: " . $order->order_number);
            return [
                'success' => true,
                'message' => 'Payment processed successfully via Stripe! Your card ending in ' . substr($cardNumber, -4) . ' has been charged.'
            ];
            
        } catch (Exception $e) {
            Log::error("Stripe processing exception: " . $e->getMessage());
            Log::error("Stripe processing stack trace: " . $e->getTraceAsString());
            return [
                'success' => false,
                'message' => 'An error occurred during payment processing. Please try again.'
            ];
        }
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
            // Order is already marked as paid, just ensure payment is completed
            if ($order->status !== 'paid') {
                $order->markAsPaid();
            }
            
            // Update payment record if not already completed
            $payment = $order->payment ?? EventPaymentYf::where('order_id', $order->id)->first();
            if ($payment && $payment->status !== 'completed') {
                $payment->update([
                    'status' => 'completed',
                    'transaction_id' => $request->input('payment_method') . '_' . uniqid(),
                    'processed_at' => now()
                ]);
            }
            
            // Update ticket availability
            $this->updateTicketAvailability($order, $request);
            
            // Generate receipt data (no PDF generation)
            try {
                $receiptData = $this->receiptService->generateReceiptData($order);
                Log::info("Receipt data generated for order: " . $order->order_number);
            } catch (\Exception $e) {
                Log::error("Error generating receipt data for order " . $order->order_number . ": " . $e->getMessage());
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
    private function updateTicketAvailability(EventOrderYf $order, Request $request = null): void
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
        
            // Update event ticket quantities
            $event = \App\Models\Event::find($freshOrder->event_id);
            if ($event) {
                $totalQuantity = array_sum(array_column($freshOrder->ticket_details, 'quantity'));
                
                Log::info("Before update - Event {$event->id} ({$event->name}): available={$event->available_tickets}, sold={$event->ticket_sold}");
                
                // Check if we should use API or internal service
                $useApi = $request ? $request->query('use_api', false) : false;
                
                try {
                    if ($useApi) {
                        // External API consumption (simulate another module)
                        Log::info("Using API to update ticket quantity for event {$event->id}");
                        $response = Http::timeout(10)->patch(url("/api/v1/ticketing/events/{$event->id}/tickets/quantity"), [
                            'quantity' => $totalQuantity,
                            'operation' => 'subtract'
                        ]);
                        
                        if ($response->failed()) {
                            throw new \Exception('Failed to update ticket quantity via API: ' . $response->body());
                        }
                        
                        $result = $response->json();
                        Log::info("API ticket update result: " . json_encode($result));
                        
                    } else {
                        // Internal service consumption
                        Log::info("Using internal service to update ticket quantity for event {$event->id}");
                        $eventService = new EventService();
                        $result = $eventService->updateTicketQuantity($event->id, $totalQuantity);
                        Log::info("Internal service ticket update result: " . json_encode($result));
                    }
                    
                    $event->refresh();
                    Log::info("After update - Event {$event->id} ({$event->name}): available={$event->available_tickets}, sold={$event->ticket_sold}");
                    
                } catch (\Exception $e) {
                    Log::error("Failed to update ticket availability for event {$event->id}: " . $e->getMessage());
                    throw new \Exception("Failed to update ticket availability: " . $e->getMessage());
                }
            } else {
                Log::error("Event not found for ID: {$freshOrder->event_id}");
                throw new \Exception("Event not found");
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
