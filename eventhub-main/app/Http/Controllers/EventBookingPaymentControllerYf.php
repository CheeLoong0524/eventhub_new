<?php

namespace App\Http\Controllers;

use App\Models\EventOrderYf;
use App\Models\EventPaymentYf;
use App\Models\Event;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\TicketType;
use App\Services\ReceiptService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EventBookingPaymentControllerYf extends Controller
{
    protected $receiptService;
    protected $paymentService;

    public function __construct(ReceiptService $receiptService, PaymentService $paymentService)
    {
        $this->receiptService = $receiptService;
        $this->paymentService = $paymentService;
    }
    /**
     * Show the payment page
     */
    public function show(Request $request): View|RedirectResponse
    {
        $user = Auth::user();
        \Log::info("Payment form access - User authenticated: " . ($user ? 'Yes' : 'No'));
        
        if (!$user) {
            \Log::info("Payment form access - User not authenticated, redirecting to login");
            return redirect()->route('auth.firebase')->with('error', 'Please login to proceed with payment.');
        }

        // Use the same cart lookup logic as CartController
        $cart = $this->getOrCreateCart($user->id);
        \Log::info("Payment form - Using cart ID: {$cart->id} for user {$user->id}");
        
        // Load cart items with relationships
        if ($cart) {
            $cart->load(['items.ticketType', 'items.event']);
        }
        
        if (!$cart || $cart->items->count() === 0) {
            \Log::info("Payment form access - Cart empty or not found. Cart: " . ($cart ? "Found but empty" : "Not found"));
            return redirect()->route('cart.index')->with('error', 'Your cart is empty. Please add some events to your cart before proceeding to checkout.');
        }
        
        \Log::info("Payment form access - Cart found with " . $cart->items->count() . " items, proceeding to payment form");

        $cartItems = $cart->items()->with(['ticketType.event'])->get();
        
        // Calculate total
        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->ticketType->price;
        });

        return view('event-booking.payment-form-yf', compact('cartItems', 'total', 'user'));
    }

    /**
     * Process the payment
     */
    public function process(Request $request): RedirectResponse
    {
        \Log::info("=== PAYMENT PROCESSING STARTED ===");
        \Log::info("Request ID: " . uniqid());
        \Log::info("User ID: " . Auth::id());
        \Log::info("Request data: " . json_encode($request->all()));

        $request->validate([
            'payment_method' => 'required|in:stripe,tng_ewallet,bank_transfer',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:1000'
        ]);

        $user = Auth::user();
        $cart = $this->getOrCreateCart($user->id);
        \Log::info("Process: Using cart ID: {$cart->id} for user {$user->id}");
        \Log::info("Process: Cart items before payment: " . $cart->items->count());
        
        // Load cart items with relationships
        if ($cart) {
            $cart->load(['items.ticketType', 'items.event']);
        }
        
        if (!$cart || $cart->items->count() === 0) {
            \Log::warning("Empty cart for user: " . $user->id);
            return redirect()->route('cart.index')->with('error', 'Your cart is empty. Please add some events to your cart before proceeding to checkout.');
        }

        $cartItems = $cart->items()->with(['ticketType.event'])->get();
        \Log::info("Cart items count: " . $cartItems->count());
        
        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->ticketType->price;
        });

        DB::beginTransaction();
        
        try {
            // Note: Duplicate submission protection is handled by:
            // 1. Frontend button disabling
            // 2. Database transactions
            // 3. Order status validation in updateTicketAvailability
            
            // Create order for each event
            $orders = [];
            foreach ($cartItems->groupBy('ticketType.event_id') as $eventId => $items) {
                $event = $items->first()->ticketType->event;
                $eventTotal = $items->sum(function ($item) {
                    return $item->quantity * $item->ticketType->price;
                });

                $ticketDetails = $items->map(function ($item) {
                    return [
                        'ticket_type_id' => $item->ticket_type_id,
                        'ticket_type_name' => $item->ticketType->name,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->ticketType->price,
                        'total_price' => $item->quantity * $item->ticketType->price
                    ];
                })->toArray();

                $order = EventOrderYf::create([
                    'order_number' => EventOrderYf::generateOrderNumber(),
                    'user_id' => $user->id,
                    'event_id' => $eventId,
                    'total_amount' => $eventTotal,
                    'status' => 'pending',
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    'customer_phone' => $request->customer_phone,
                    'ticket_details' => $ticketDetails,
                    'notes' => $request->notes
                ]);

                $orders[] = $order;

                // Create payment record
                EventPaymentYf::create([
                    'order_id' => $order->id,
                    'payment_method' => $request->payment_method,
                    'amount' => $eventTotal,
                    'currency' => 'MYR',
                    'status' => 'pending'
                ]);
            }

            DB::commit();

            // Process payment using Strategy pattern (this will redirect to payment gateway)
            $paymentResult = $this->paymentService->processPayment($orders, $request);
            
            if (!$paymentResult->success) {
                throw new \Exception($paymentResult->message);
            }
            
            \Log::info("Payment gateway redirect initiated: " . $paymentResult->message);

            // Note: Orders are NOT marked as paid yet - they remain 'pending'
            // Payment completion will be handled by the PaymentGatewayController
            // after successful gateway processing

            // Redirect to payment gateway
            if ($paymentResult->redirectUrl) {
                return redirect($paymentResult->redirectUrl);
            }
            
            // Fallback if no redirect URL
            return redirect()->route('event-booking.payment-success-yf', ['order' => $orders[0]->order_number])
                ->with('error', 'Payment gateway not available. Please try again.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Event booking payment processing error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Payment processing failed. Please try again. Error: ' . $e->getMessage());
        }
    }


    /**
     * Update ticket availability after successful payment
     */
    private function updateTicketAvailability(EventOrderYf $order): void
    {
        \Log::info("Starting ticket availability update for order: {$order->order_number}");
        
        $order->refresh(); // Ensure we have the latest order data
        
        if ($order->status !== 'paid') {
            \Log::warning("Order {$order->order_number} is not paid yet - skipping ticket availability update");
            return;
        }
        
        // Check if this order has already been processed for ticket deduction
        if ($order->tickets_processed) {
            \Log::warning("Order {$order->order_number} tickets have already been processed - skipping ticket availability update");
            return;
        }
        
        // Use database transaction to ensure atomicity
        \DB::beginTransaction();
        
        try {
            // Double-check with a fresh query to prevent race conditions
            $freshOrder = \App\Models\EventOrderYf::where('id', $order->id)
                ->where('status', 'paid')
                ->where('tickets_processed', false)
                ->lockForUpdate()
                ->first();
                
            if (!$freshOrder) {
                \Log::warning("Order {$order->order_number} is not eligible for ticket processing (already processed or not paid)");
                \DB::rollBack();
                return;
            }
        
            foreach ($freshOrder->ticket_details as $detail) {
                $ticketType = TicketType::find($detail['ticket_type_id']);
                if ($ticketType) {
                    // Record before values for verification
                    $beforeAvailable = $ticketType->available_quantity;
                    $beforeSold = $ticketType->sold_quantity;
                    
                    \Log::info("Before update - Ticket type {$ticketType->id} ({$ticketType->name}): available={$beforeAvailable}, sold={$beforeSold}");
                    
                    // Use database-level atomic update to prevent race conditions
                    $affectedRows = \DB::table('ticket_types')
                        ->where('id', $ticketType->id)
                        ->where('available_quantity', '>=', $detail['quantity']) // Keep this check for safety
                        ->update([
                            'sold_quantity' => \DB::raw('sold_quantity + ' . $detail['quantity']),
                            'available_quantity' => \DB::raw('available_quantity - ' . $detail['quantity']),
                            'updated_at' => now()
                        ]);
                    
                    if ($affectedRows === 0) {
                        \Log::error("Failed to update ticket availability - insufficient tickets or race condition for ticket type {$ticketType->id}");
                        throw new \Exception("Insufficient tickets available for {$ticketType->name}");
                    }
                    
                    // Refresh the model to get updated values
                    $ticketType->refresh();
                    $afterAvailable = $ticketType->available_quantity;
                    $afterSold = $ticketType->sold_quantity;
                    
                    \Log::info("After update - Ticket type {$ticketType->id} ({$ticketType->name}): available={$afterAvailable}, sold={$afterSold}");
                    
                    // Verify the deduction was correct
                    $expectedAvailable = $beforeAvailable - $detail['quantity'];
                    $expectedSold = $beforeSold + $detail['quantity'];
                    
                    if ($afterAvailable != $expectedAvailable || $afterSold != $expectedSold) {
                        \Log::error("DEDUCTION VERIFICATION FAILED for ticket type {$ticketType->id}!");
                        \Log::error("Expected: available={$expectedAvailable}, sold={$expectedSold}");
                        \Log::error("Actual: available={$afterAvailable}, sold={$afterSold}");
                        throw new \Exception("Ticket deduction verification failed for {$ticketType->name}");
                    } else {
                        \Log::info("DEDUCTION VERIFICATION PASSED for ticket type {$ticketType->id}");
                    }
                } else {
                    \Log::error("Ticket type not found for ID: {$detail['ticket_type_id']}");
                    throw new \Exception("Ticket type not found");
                }
            }
            
            // Mark the order as processed for tickets
            $freshOrder->update(['tickets_processed' => true]);
            
            \DB::commit();
            \Log::info("Completed ticket availability update for order: {$freshOrder->order_number}");
            
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Failed to update ticket availability for order {$order->order_number}: " . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Show payment success page
     */
    public function success(Request $request): View
    {
        $orderNumber = $request->get('order');
        $order = EventOrderYf::where('order_number', $orderNumber)->with(['event', 'payment'])->first();
        
        if (!$order) {
            abort(404, 'Order not found');
        }

        return view('event-booking.payment-success-yf', compact('order'));
    }

    /**
     * Show bank transfer instructions
     */
    public function bankTransfer(Request $request): View
    {
        $orderNumber = $request->get('order');
        $order = EventOrderYf::where('order_number', $orderNumber)->with(['event', 'payment'])->first();
        
        if (!$order) {
            abort(404, 'Order not found');
        }

        return view('event-booking.bank-transfer-instructions-yf', compact('order'));
    }

    /**
     * Show receipt
     */
    public function receipt(Request $request): View
    {
        $orderNumber = $request->get('order');
        $order = EventOrderYf::where('order_number', $orderNumber)->with(['event', 'payment', 'user'])->first();
        
        if (!$order) {
            abort(404, 'Order not found');
        }

        return view('event-booking.payment-receipt-yf', compact('order'));
    }

    /**
     * Get or create cart for current user (same logic as CartController)
     */
    private function getOrCreateCart(int $userId): Cart
    {
        return Cart::findOrCreateForUser($userId);
    }
}