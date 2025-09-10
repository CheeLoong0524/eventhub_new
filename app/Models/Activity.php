<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [     //$fillable - allows mass assignment when saving
        'event_id',
        'name',
        'description',
        'start_time',
        'duration',
        'status',
        'venue_id',
    ];

    protected $casts = [
        'start_time' => 'datetime',
    ];

    // define the BelongTo relationship (Each event belongs to one event)
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // define the BelongTo relationship (Each venue belongs to one venue)
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }
}
