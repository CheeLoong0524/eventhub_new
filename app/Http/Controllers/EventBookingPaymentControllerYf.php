<?php
// Author: Gooi Ye Fan

namespace App\Http\Controllers;

use App\Models\EventOrderYf;
use App\Models\EventPaymentYf;
use App\Models\Event;
use App\Models\Cart;
use App\Models\CartItem;
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
            return redirect()->route('login')->with('error', 'Please login to proceed with payment.');
        }

        // Use the same cart lookup logic as CartController
        $cart = $this->getOrCreateCart($user->id);
        \Log::info("Payment form - Using cart ID: {$cart->id} for user {$user->id}");
        
        // Load cart items with relationships
        if ($cart) {
            $cart->load(['items.event']);
        }
        
        if (!$cart || $cart->items->count() === 0) {
            \Log::info("Payment form access - Cart empty or not found. Cart: " . ($cart ? "Found but empty" : "Not found"));
            return redirect()->route('cart.index')->with('error', 'Your cart is empty. Please add some events to your cart before proceeding to checkout.');
        }
        
        \Log::info("Payment form access - Cart found with " . $cart->items->count() . " items, proceeding to payment form");

        $cartItems = $cart->items()->with(['event'])->get();
        
        // Calculate total
        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
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
            $cart->load(['items.event']);
        }
        
        if (!$cart || $cart->items->count() === 0) {
            \Log::warning("Empty cart for user: " . $user->id);
            return redirect()->route('cart.index')->with('error', 'Your cart is empty. Please add some events to your cart before proceeding to checkout.');
        }

        $cartItems = $cart->items()->with(['event'])->get();
        \Log::info("Cart items count: " . $cartItems->count());
        
        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        DB::beginTransaction();
        
        try {
            // Create order for each event
            $orders = [];
            foreach ($cartItems->groupBy('event_id') as $eventId => $items) {
                $event = $items->first()->event;
                $eventTotal = $items->sum(function ($item) {
                    return $item->quantity * $item->price;
                });

                $ticketDetails = $items->map(function ($item) {
                    return [
                        'event_id' => $item->event_id,
                        'event_name' => $item->event->name,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->price,
                        'total_price' => $item->quantity * $item->price
                    ];
                })->toArray();

                $order = EventOrderYf::create([
                    'order_number' => EventOrderYf::generateOrderNumber(),
                    'user_id' => $user->id,
                    'event_id' => $eventId,
                    'total_amount' => $eventTotal,
                    'status' => 'paid',
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    'customer_phone' => $request->customer_phone,
                    'ticket_details' => $ticketDetails,
                    'notes' => $request->notes
                ]);

                $orders[] = $order;

                // Create payment record as completed
                EventPaymentYf::create([
                    'order_id' => $order->id,
                    'payment_method' => $request->payment_method,
                    'amount' => $eventTotal,
                    'currency' => 'MYR',
                    'status' => 'completed',
                    'transaction_id' => $request->payment_method . '_' . uniqid(),
                    'processed_at' => now()
                ]);
            }

            DB::commit();

            // Process payment using Strategy pattern (this will redirect to payment gateway)
            $paymentResult = $this->paymentService->processPayment($orders, $request);
            
            if (!$paymentResult->success) {
                throw new \Exception($paymentResult->message);
            }
            
            \Log::info("Payment gateway redirect initiated: " . $paymentResult->message);

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
     * Get receipt data as JSON (replaces PDF download)
     */
    public function getReceiptData(Request $request)
    {
        $orderNumber = $request->get('order');
        $order = EventOrderYf::where('order_number', $orderNumber)->with(['event', 'payment', 'user'])->first();
        
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $receiptData = $this->receiptService->generateReceiptData($order);
        return response()->json($receiptData);
    }

    /**
     * Get or create cart for current user (same logic as CartController)
     */
    private function getOrCreateCart(int $userId): Cart
    {
        return Cart::findOrCreateForUser($userId);
    }
}