<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'date',
        'time',
        'venue',
        'location',
        'category',
        'image_url',
        'status',
        'max_attendees',
        'created_by',
        'is_featured',
        'popularity_score'
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
        'is_featured' => 'boolean',
        'popularity_score' => 'integer'
    ];

    /**
     * Get the ticket types for this event
     */
    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class);
    }

    /**
     * Get the cart items for this event
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the user who created this event
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if event is upcoming
     */
    public function isUpcoming(): bool
    {
        return $this->date >= now()->toDateString();
    }

    /**
     * Check if event is past
     */
    public function isPast(): bool
    {
        return $this->date < now()->toDateString();
    }

    /**
     * Get total available tickets
     */
    public function getTotalAvailableTickets(): int
    {
        // Use the loaded relationship if available, otherwise query fresh
        if ($this->relationLoaded('ticketTypes')) {
            return $this->ticketTypes->where('is_active', true)->sum(function($ticketType) {
                return $ticketType->getRemainingQuantity();
            });
        }
        
        return $this->ticketTypes()->where('is_active', true)->get()->sum(function($ticketType) {
            return $ticketType->getRemainingQuantity();
        });
    }

    /**
     * Get total sold tickets
     */
    public function getTotalSoldTickets(): int
    {
        return $this->ticketTypes()->sum('sold_quantity');
    }

    /**
     * Check if event has available tickets
     */
    public function hasAvailableTickets(): bool
    {
        return $this->getTotalAvailableTickets() > 0;
    }

    /**
     * Scope for published events
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for upcoming events
     */
    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString());
    }

    /**
     * Scope for featured events
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for events by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for events by venue
     */
    public function scopeByVenue($query, $venue)
    {
        return $query->where('venue', 'like', "%{$venue}%");
    }

    /**
     * Scope for events by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope for popular events (by popularity score)
     */
    public function scopePopular($query)
    {
        return $query->orderBy('popularity_score', 'desc');
    }

    /**
     * Get formatted date and time
     */
    public function getFormattedDateTime(): string
    {
        return $this->date->format('M d, Y') . ' at ' . $this->time->format('g:i A');
    }
}

