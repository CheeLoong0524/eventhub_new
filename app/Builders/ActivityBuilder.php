<?php 

namespace App\Builders;

use App\Models\Activity;

class ActivityBuilder
{
    public function build(array $activitiesData): array
    {
        $activities = [];

        foreach ($activitiesData as $data) {
            $activities[] = new Activity($data);
        }

        return $activities;
    }
}
