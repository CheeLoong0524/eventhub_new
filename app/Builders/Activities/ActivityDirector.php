<?php

namespace App\Builders\Activities;

use App\Models\Activity;

class ActivityDirector
{
    protected ConcreteActivityBuilder $builder;

    public function __construct(ConcreteActivityBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Build an Activity object from validated request data
     */
    public function buildActivity(array $data): Activity
    {
        return $this->builder
            ->setEvent($data['event_id'])
            ->setName($data['name'])
            ->setDescription($data['description'] ?? null)
            ->setStartTime($data['start_time'])
            ->setDuration($data['duration'])
            ->setStatus($data['status'] ?? 'pending')
            ->setVenue($data['venue_id'])
            ->getResult();
    }
}
