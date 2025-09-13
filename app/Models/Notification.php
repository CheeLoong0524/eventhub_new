<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'inquiry_id',
        'message',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the inquiry that this notification is about
     */
    public function inquiry()
    {
        return $this->belongsTo(SupportInquiry::class, 'inquiry_id', 'inquiry_id');
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('status', 'unread');
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('status', 'read');
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update(['status' => 'read']);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread()
    {
        $this->update(['status' => 'unread']);
    }
}