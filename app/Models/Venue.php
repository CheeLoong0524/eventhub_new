<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'capacity',
    ];

    // A venue can host many events
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    // A venue can also host many activities
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
}
