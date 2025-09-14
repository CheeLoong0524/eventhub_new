<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'venue_id', 
        'start_time', 
        'end_time',
        'organizer',
        'status',
        // Pricing and availability
        'booth_price', 'booth_quantity', 'booth_sold',
        'ticket_price', 'ticket_quantity', 'ticket_sold',
        // Costs
        'venue_cost', 'staff_cost', 'equipment_cost', 'marketing_cost', 'other_costs',
        // Revenue tracking
        'total_revenue', 'total_costs', 'net_profit'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        // Pricing casts
        'booth_price' => 'decimal:2',
        'ticket_price' => 'decimal:2',
        'venue_cost' => 'decimal:2',
        'staff_cost' => 'decimal:2',
        'equipment_cost' => 'decimal:2',
        'marketing_cost' => 'decimal:2',
        'other_costs' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'total_costs' => 'decimal:2',
        'net_profit' => 'decimal:2',
    ];

    /**
     * RELATIONSHIPS ------------------------------------------------- 
     */
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function vendorApplications()
    {
        return $this->hasMany(VendorEventApplication::class);
    }

    // Get the cart items for this event 
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * METHODS ------------------------------------------------- 
     */
    /**
     * Check if event is upcoming (excluding today)
     */
    public function isUpcoming(): bool
    {
        if (!$this->start_time) {
            return false; // If no start_time, consider it not upcoming
        }
        return $this->start_time->toDateString() > now()->toDateString();
    }

    /**
     * Check if event is past (including today)
     */
    public function isPast(): bool
    {
        if (!$this->start_time) {
            return false; // If no start_time, consider it not past
        }
        return $this->start_time->toDateString() <= now()->toDateString();
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
        return $query->where('status', 'active');
    }

    /**
     * Scope for upcoming events (excluding today)
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now()->endOfDay());
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
        return $query->whereHas('venue', function($q) use ($venue) {
            $q->where('name', 'like', "%{$venue}%");
        });
    }

    /**
     * Scope for events by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_time', [$startDate, $endDate]);
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
        return $this->start_time->format('M d, Y \a\t g:i A');
    }

    public function isAcceptingApplications()
    {
        return $this->status === 'active';
    }

    public function hasAvailableSlots()
    {
        return $this->booth_quantity > $this->booth_sold;
    }

    public function getAvailableBoothsAttribute()
    {
        return $this->booth_quantity - $this->booth_sold;
    }

    public function getAvailableTicketsAttribute()
    {
        return $this->ticket_quantity;
    }

    public function calculateTotalCosts()
    {
        return ($this->venue_cost ?? 0) + 
               ($this->staff_cost ?? 0) + 
               ($this->equipment_cost ?? 0) + 
               ($this->marketing_cost ?? 0) + 
               ($this->other_costs ?? 0);
    }

    public function calculateBoothRevenue()
    {
        // Calculate actual revenue from paid applications (including tax and service charge)
        return VendorEventApplication::where('event_id', $this->id)
            ->where('status', 'paid')
            ->sum('approved_price');
    }

    public function calculateTicketRevenue()
    {
        return $this->ticket_sold * ($this->ticket_price ?? 0);
    }

    public function calculateTotalRevenue()
    {
        return $this->calculateBoothRevenue() + $this->calculateTicketRevenue();
    }

    public function calculateNetProfit()
    {
        return $this->calculateTotalRevenue() - $this->calculateTotalCosts();
    }

    public function updateFinancials()
    {
        $this->total_costs = $this->calculateTotalCosts();
        $this->total_revenue = $this->calculateTotalRevenue();
        $this->net_profit = $this->calculateNetProfit();
        $this->save();
    }
}
