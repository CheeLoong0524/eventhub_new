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
        'start_date',
        'end_date',
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
        'start_date' => 'datetime',
        'end_date' => 'datetime',
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
        return $this->ticket_quantity - $this->ticket_sold;
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
            ->sum('final_amount');
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
