<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorEventApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'event_id',
        'booth_size',
        'booth_quantity',
        'service_type',
        'service_description',
        'service_categories',
        'requested_price',
        'approved_price',
        'base_amount',
        'tax_amount',
        'service_charge_amount',
        'final_amount',
        'special_requirements',
        'equipment_needed',
        'additional_services',
        'status',
        'admin_notes',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'approved_at',
        'rejected_at',
        'paid_at',
    ];

    protected $casts = [
        'service_categories' => 'array',
        'equipment_needed' => 'array',
        'additional_services' => 'array',
        'requested_price' => 'decimal:2',
        'approved_price' => 'decimal:2',
        'base_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'service_charge_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

	public function isUnderReview(): bool
	{
		return $this->status === 'pending';
	}


    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'paid' => 'primary',
            'rejected' => 'danger',
            'cancelled' => 'secondary',
            default => 'secondary',
        };
    }

    public function getStatusIconAttribute(): string
    {
        return match($this->status) {
            'pending' => 'fas fa-clock',
            'approved' => 'fas fa-check-circle',
            'paid' => 'fas fa-credit-card',
            'rejected' => 'fas fa-times-circle',
            'cancelled' => 'fas fa-ban',
            default => 'fas fa-question-circle',
        };
    }

    public function getServiceTypeLabelAttribute(): string
    {
        return match($this->service_type) {
            'food' => 'Food & Beverage',
            'equipment' => 'Equipment Rental',
            'decoration' => 'Decoration & Design',
            'entertainment' => 'Entertainment',
            'logistics' => 'Logistics & Transportation',
            'other' => 'Other Services',
            default => ucfirst($this->service_type),
        };
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }


    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByVendor($query, int $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeByEvent($query, int $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function canBeCancelled(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeEdited(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeRejected(): bool
    {
        return $this->status === 'pending';
    }
}


