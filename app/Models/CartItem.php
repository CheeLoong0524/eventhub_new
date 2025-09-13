<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'event_id',
        'quantity',
        'price'
    ];

    protected $casts = [
        'price' => 'decimal:2'
    ];

    /**
     * Get the cart this item belongs to
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the event this item is for
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }


    /**
     * Get total price for this item
     */
    public function getTotalPrice(): float
    {
        return $this->quantity * $this->price;
    }

    /**
     * Get formatted total price
     */
    public function getFormattedTotalPrice(): string
    {
        return 'RM ' . number_format($this->getTotalPrice(), 2);
    }

    /**
     * Check if this item is still valid
     */
    public function isValid(): bool
    {
        // Check if event is still upcoming
        if ($this->event->isPast()) {
            return false;
        }

        // Check if event has available tickets
        if ($this->event->available_tickets < $this->quantity) {
            return false;
        }

        return true;
    }

    /**
     * Update quantity and price
     */
    public function updateQuantity(int $quantity): bool
    {
        // Check if event has enough available tickets
        if ($this->event->available_tickets < $quantity) {
            return false;
        }

        $this->update([
            'quantity' => $quantity,
            'price' => $this->event->ticket_price
        ]);

        return true;
    }
}
