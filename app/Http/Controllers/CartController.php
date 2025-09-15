<?php
// Author: Gooi Ye Fan

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display the cart
     */
    public function index(): View
    {
        $cart = $this->getOrCreateCart();
        $cart->load(['items.event']);

        // Debug logging
        \Log::info("Cart index - Cart ID: {$cart->id}, Items count: {$cart->items->count()}, Total items: {$cart->getTotalItems()}");
        
        // Additional debugging - check if cart has items
        if ($cart->items->count() > 0) {
            \Log::info("Cart has items - showing cart");
            foreach ($cart->items as $item) {
                \Log::info("Cart item: Event ID: {$item->event_id}, Quantity: {$item->quantity}");
            }
        } else {
            \Log::info("Cart is empty - showing empty cart message");
        }

        // If cart has items, clear any error messages that might be lingering
        if ($cart->items->count() > 0) {
            session()->forget('error');
        }

        return view('cart.index', compact('cart'));
    }

    /**
     * Add item to cart
     */
    public function addItem(Request $request): JsonResponse
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'quantity' => 'required|integer|min:1|max:10'
        ]);

        $event = Event::findOrFail($request->event_id);

        // Check if event has tickets available
        if (!$event->ticket_price || $event->ticket_quantity <= 0) {
            return response()->json(['error' => 'This event does not have tickets available'], 400);
        }

        // Check if event is still upcoming
        if ($event->isPast()) {
            return response()->json(['error' => 'This event has already passed'], 400);
        }

        // Check if quantity is available
        if ($event->available_tickets < $request->quantity) {
            return response()->json(['error' => 'Requested quantity is not available'], 400);
        }

        $cart = $this->getOrCreateCart();

        // Check if item already exists in cart
        $existingItem = $cart->items()
                            ->where('event_id', $event->id)
                            ->first();

        if ($existingItem) {
            // Update existing item
            $newQuantity = $existingItem->quantity + $request->quantity;
            
            if ($event->available_tickets < $newQuantity) {
                return response()->json(['error' => 'Total quantity exceeds available tickets'], 400);
            }

            $existingItem->update([
                'quantity' => $newQuantity,
                'price' => $event->ticket_price
            ]);
        } else {
            // Create new item
            CartItem::create([
                'cart_id' => $cart->id,
                'event_id' => $event->id,
                'quantity' => $request->quantity,
                'price' => $event->ticket_price
            ]);
        }

        $cart->load(['items.event']);

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart successfully',
            'cart' => [
                'total_items' => $cart->getTotalItems(),
                'total_price' => $cart->getFormattedTotalPrice()
            ]
        ]);
    }

    /**
     * Update item quantity in cart
     */
    public function updateItem(Request $request, CartItem $cartItem): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10'
        ]);

        // Verify cart item belongs to user's cart
        $cart = $this->getOrCreateCart();
        if ($cartItem->cart_id !== $cart->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if event is still available
        if ($cartItem->event->isPast()) {
            return response()->json(['error' => 'This event has already passed'], 400);
        }

        // Check if event has enough available tickets
        if ($cartItem->event->available_tickets < $request->quantity) {
            return response()->json(['error' => 'Requested quantity is not available'], 400);
        }

        $cartItem->update([
            'quantity' => $request->quantity,
            'price' => $cartItem->event->ticket_price
        ]);

        $cart->load(['items.event']);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'cart' => [
                'total_items' => $cart->getTotalItems(),
                'total_price' => $cart->getFormattedTotalPrice()
            ]
        ]);
    }

    /**
     * Remove item from cart
     */
    public function removeItem(CartItem $cartItem): JsonResponse
    {
        // Verify cart item belongs to user's cart
        $cart = $this->getOrCreateCart();
        if ($cartItem->cart_id !== $cart->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $cartItem->delete();

        $cart->load(['items.event']);

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart successfully',
            'cart' => [
                'total_items' => $cart->getTotalItems(),
                'total_price' => $cart->getFormattedTotalPrice()
            ]
        ]);
    }

    /**
     * Clear all items from cart
     */
    public function clear(): JsonResponse
    {
        $cart = $this->getOrCreateCart();
        $cart->clear();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully'
        ]);
    }

    /**
     * Get cart summary for API
     */
    public function summary(): JsonResponse
    {
        $cart = $this->getOrCreateCart();
        $cart->load(['items.event']);

        return response()->json([
            'total_items' => $cart->getTotalItems(),
            'total_price' => $cart->getFormattedTotalPrice(),
            'items' => $cart->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'event_name' => $item->event->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total_price' => $item->getFormattedTotalPrice(),
                    'is_valid' => $item->isValid()
                ];
            })
        ]);
    }

    /**
     * Get or create cart for current user/session
     */
    private function getOrCreateCart(): Cart
    {
        if (Auth::check()) {
            $cart = Cart::findOrCreateForUser(Auth::id());
            // Force refresh the cart data to ensure we have the latest state
            $cart->load(['items.event']);
            return $cart;
        } else {
            $cart = Cart::findOrCreateForSession(session()->getId());
            // Force refresh the cart data to ensure we have the latest state
            $cart->load(['items.event']);
            return $cart;
        }
    }
}
