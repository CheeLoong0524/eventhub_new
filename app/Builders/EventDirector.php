<?php 

namespace App\Builders;

use App\Models\Event;

class EventDirector
{
    protected VenueBuilder $venueBuilder;
    protected TimeBuilder $timeBuilder;
    protected OrganizerBuilder $organizerBuilder;
    protected ActivityBuilder $activityBuilder;

    public function __construct()
    {
        $this->venueBuilder = new VenueBuilder();
        $this->timeBuilder = new TimeBuilder();
        $this->organizerBuilder = new OrganizerBuilder();
        $this->activityBuilder = new ActivityBuilder();
    }

    public function buildEvent(array $data): Event
    {
        // Step 1: Build venue using VenueBuilder
        $venue = $this->venueBuilder->build($data['venue_id']);
        
        // Step 2: Build time data using TimeBuilder
        $time = $this->timeBuilder->build($data['start_time'], $data['end_time']);
        
        // Step 3: Build organizer using OrganizerBuilder
        $organizer = $this->organizerBuilder->build($data['organizer']);
        
        // Step 4: Build activities using ActivityBuilder
        $activities = $this->activityBuilder->build($data['activities'] ?? []);

        // Step 5: Assemble the final Event object
        $event = new Event([
            'name'       => $data['name'],
            'venue_id'   => $venue->id,
            'start_time' => $time['start_time'],
            'end_time'   => $time['end_time'],
            'organizer'  => $organizer,
        ]);

        // Step 6: Set relationships
        $event->setRelation('activities', collect($activities));

        return $event;
    }
}
