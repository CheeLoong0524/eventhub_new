<?php

namespace App\Builders\Activities;

use App\Models\Activity;

class ConcreteActivityBuilder
{
    protected Activity $activity;

    public function __construct()
    {
        $this->activity = new Activity();
    }

    public function setEvent(int $eventId): self
    {
        $this->activity->event_id = $eventId;
        return $this;
    }

    public function setName(string $name): self
    {
        $this->activity->name = $name;
        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->activity->description = $description;
        return $this;
    }

    public function setStartTime(string $startTime): self
    {
        $this->activity->start_time = $startTime;
        return $this;
    }

    public function setDuration(int $duration): self
    {
        $this->activity->duration = $duration;
        return $this;
    }

    public function setStatus(string $status = 'pending'): self
    {
        $this->activity->status = $status;
        return $this;
    }

    public function setVenue(int $venueId): self
    {
        $this->activity->venue_id = $venueId;
        return $this;
    }

    public function getResult(): Activity
    {
        return $this->activity;
    }
}
