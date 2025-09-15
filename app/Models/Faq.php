<?php
/** Author: Yap Jia Wei **/

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'category',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope for active FAQs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for multiple categories
     */
    public function scopeByCategories($query, array $categories)
    {
        return $query->whereIn('category', $categories);
    }

    /**
     * Scope for ordered FAQs
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at');
    }

    /**
     * Get category label
     */
    public function getCategoryLabelAttribute()
    {
        return match($this->category) {
            'general' => 'General',
            'technical' => 'Technical',
            'billing' => 'Billing',
            'event' => 'Event',
            'customer' => 'Customer',
            default => ucfirst($this->category),
        };
    }

    /**
     * Get category badge color
     */
    public function getCategoryBadgeColorAttribute()
    {
        return match($this->category) {
            'general' => 'secondary',
            'technical' => 'warning',
            'billing' => 'primary',
            'event' => 'dark',
            'customer' => 'info',
            default => 'secondary',
        };
    }
}
