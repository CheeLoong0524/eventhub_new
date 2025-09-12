<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketType extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'description',
        'price',
        'available_quantity',
        'total_quantity',  // Add this line
        'sold_quantity',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'sale_start_date' => 'datetime',
        'sale_end_date' => 'datetime'
    ];

    /**
     * Get the event this ticket type belongs to
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get cart items for this ticket type
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get remaining quantity
     */
    public function getRemainingQuantity(): int
    {
        return max(0, $this->available_quantity);
    }

    /**
     * Check if ticket type is available for sale
     */
    public function isAvailableForSale(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->getRemainingQuantity() <= 0) {
            return false;
        }

        $now = now();
        if ($this->sale_start_date && $now < $this->sale_start_date) {
            return false;
        }

        if ($this->sale_end_date && $now > $this->sale_end_date) {
            return false;
        }

        return true;
    }

    /**
     * Check if quantity can be ordered
     */
    public function canOrderQuantity(int $quantity): bool
    {
        if (!$this->isAvailableForSale()) {
            return false;
        }

        if ($quantity > $this->getRemainingQuantity()) {
            return false;
        }

        if ($this->max_per_order && $quantity > $this->max_per_order) {
            return false;
        }

        return true;
    }

    /**
     * Get formatted price
     */
    public function getFormattedPrice(): string
    {
        return 'RM ' . number_format($this->price, 2);
    }

    /**
     * Scope for active ticket types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for available ticket types
     */
    public function scopeAvailable($query)
    {
        return $query->where('available_quantity', '>', 0)
                    ->where('is_active', true);
    }
}
