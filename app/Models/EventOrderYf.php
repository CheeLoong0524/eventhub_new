<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EventOrderYf extends Model
{
    protected $table = 'event_orders_yf';

    protected $fillable = [
        'order_number',
        'user_id',
        'event_id',
        'total_amount',
        'status',
        'customer_name',
        'customer_email',
        'customer_phone',
        'ticket_details',
        'notes',
        'tickets_processed'
    ];

    protected $casts = [
        'ticket_details' => 'array',
        'total_amount' => 'decimal:2',
        'tickets_processed' => 'boolean'
    ];

    /**
     * Generate a unique order number
     */
    public static function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . strtoupper(uniqid());
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Get the user who placed the order
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the event for this order
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the payment for this order
     */
    public function payment(): HasOne
    {
        return $this->hasOne(EventPaymentYf::class, 'order_id');
    }

    /**
     * Check if order is paid
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if order is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Mark order as paid
     */
    public function markAsPaid(): void
    {
        $this->update(['status' => 'paid']);
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'RM ' . number_format($this->total_amount, 2);
    }
}

