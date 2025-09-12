<?php

namespace App\Services;

use App\Models\Event;

class EventService
{
    // Get all events with related data 
    public function getAllEvents()
    {
        return Event::with(['venue', 'activities.venue'])->get();
    }

     // Get a single event by ID with related data
    public function getEventById(int $id)
    {
        return Event::with(['venue', 'activities.venue'])->findOrFail($id);
    }
}