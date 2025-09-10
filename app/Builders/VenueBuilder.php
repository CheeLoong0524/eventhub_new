<?php 

namespace App\Builders;

use App\Models\Venue;

class VenueBuilder
{
    public function build(int $venueId): Venue
    {
        return Venue::findOrFail($venueId);
    }
}
