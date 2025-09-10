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
        $venue = $this->venueBuilder->build($data['venue_id']);
        $time  = $this->timeBuilder->build($data['start_time'], $data['end_time']);
        $organizer = $this->organizerBuilder->build($data['organizer']);
        $activities = $this->activityBuilder->build($data['activities'] ?? []);

        $event = new Event([
            'name'       => $data['name'],
            'venue_id'   => $venue->id,
            'start_time' => $time['start_time'],
            'end_time'   => $time['end_time'],
            'organizer'  => $organizer,
        ]);

        $event->setRelation('activities', collect($activities));

        return $event;
    }
}
