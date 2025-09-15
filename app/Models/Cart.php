<?php
// Author: Gooi Ye Fan

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    /**
     * Get the user who owns this cart
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the cart items
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get total items in cart
     */
    public function getTotalItems(): int
    {
        return $this->items()->sum('quantity');
    }

    /**
     * Get total price of cart
     */
    public function getTotalPrice(): float
    {
        return $this->items()->with('event')->get()->sum(function ($item) {
            return $item->quantity * $item->price;
        });
    }

    /**
     * Get formatted total price
     */
    public function getFormattedTotalPrice(): string
    {
        return 'RM ' . number_format($this->getTotalPrice(), 2);
    }

    /**
     * Check if cart is empty
     */
    public function isEmpty(): bool
    {
        return $this->items()->count() === 0;
    }

    /**
     * Check if cart is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at < now();
    }

    /**
     * Clear all items from cart
     */
    public function clear(): void
    {
        $this->items()->delete();
    }

    /**
     * Find or create cart for user
     */
    public static function findOrCreateForUser(int $userId): self
    {
        $cart = static::where('user_id', $userId)
                     ->where('expires_at', '>', now())
                     ->first();

        if (!$cart) {
            $cart = static::create([
                'user_id' => $userId,
                'expires_at' => now()->addHours(24) // Cart expires in 24 hours
            ]);
        }

        return $cart;
    }

    /**
     * Find or create cart for session
     */
    public static function findOrCreateForSession(string $sessionId): self
    {
        $cart = static::where('session_id', $sessionId)
                     ->where('expires_at', '>', now())
                     ->first();

        if (!$cart) {
            $cart = static::create([
                'session_id' => $sessionId,
                'expires_at' => now()->addHours(24) // Cart expires in 24 hours
            ]);
        }

        return $cart;
    }

    /**
     * Clear all carts for a user
     */
    public static function clearAllForUser(int $userId): int
    {
        $carts = static::where('user_id', $userId)->get();
        $totalItemsCleared = 0;
        
        foreach ($carts as $cart) {
            $itemCount = $cart->items()->count();
            if ($itemCount > 0) {
                $cart->clear();
                $totalItemsCleared += $itemCount;
            }
        }
        
        return $totalItemsCleared;
    }
}
