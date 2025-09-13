<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Observers\AdminInquiryObserver;
use App\Observers\CustomerInquiryObserver;

class SupportInquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'inquiry_id',
        'user_id',
        'name',
        'email',
        'subject',
        'message',
        'status',
        'admin_reply',
        'resolved_at',
        'resolved_by'
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    /**
     * Generate unique inquiry ID
     */
    public static function generateInquiryId()
    {
        $year = date('Y');
        $lastInquiry = static::where('inquiry_id', 'like', "INQ-{$year}-%")
            ->orderBy('inquiry_id', 'desc')
            ->first();
        
        if ($lastInquiry) {
            $lastNumber = (int) substr($lastInquiry->inquiry_id, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return 'INQ-' . $year . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Admin who resolved the inquiry
     */
    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Scope for pending inquiries
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for resolved inquiries
     */
    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    /**
     * The "booted" method of the model.
     * Register observers when the model is booted
     */
    protected static function booted()
    {
        static::observe(AdminInquiryObserver::class);
        static::observe(CustomerInquiryObserver::class);
    }
}
