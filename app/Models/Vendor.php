<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'business_type',
        'business_description',
        'business_phone',
        'business_email',
        'years_in_business',
        'business_size',
        'annual_revenue',
        'event_experience',
        'product_category',
        'target_audience',
        'marketing_strategy',
        'contact_person',
        'contact_email',
        'contact_phone',
        'website',
        'status',
        'rejection_reason',
        'approved_at',
        'approved_by',
        'rating',
        'total_events',
        'is_verified',
    ];

    protected $casts = [
        'service_categories' => 'array',
        'social_media' => 'array',
        'documents' => 'array',
        'approved_at' => 'datetime',
        'rating' => 'decimal:2',
        'is_verified' => 'boolean',
    ];

    public function setServiceCategoriesAttribute($value): void
    {
        if (is_string($value)) {
            $items = array_map(static function ($part) {
                return trim((string) $part);
            }, explode(',', $value));

            $items = array_values(array_filter($items, static function ($v) {
                return $v !== '';
            }));

            $this->attributes['service_categories'] = json_encode($items);
            return;
        }

        if (is_array($value)) {
            $normalized = array_map(static function ($v) {
                if (is_string($v)) {
                    $v = trim($v);
                }
                return $v;
            }, $value);

            $normalized = array_values(array_filter($normalized, static function ($v) {
                return !is_null($v) && $v !== '';
            }));

            $this->attributes['service_categories'] = json_encode($normalized);
            return;
        }

        $this->attributes['service_categories'] = json_encode([]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }


    public function applications(): HasMany
    {
        return $this->hasMany(VendorApplication::class, 'user_id', 'user_id');
    }

    // Notifications relation removed

    public function eventApplications(): HasMany
    {
        return $this->hasMany(VendorEventApplication::class);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
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

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'suspended' => 'secondary',
            default => 'secondary',
        };
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByServiceType($query, string $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeWithMinRating($query, float $rating)
    {
        return $query->where('rating', '>=', $rating);
    }
}


