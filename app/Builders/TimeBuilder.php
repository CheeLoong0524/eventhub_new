<?php 

namespace App\Builders;

class TimeBuilder
{
    public function build(string $start, string $end): array
    {
        if ($end <= $start) {
            throw new \Exception("End time must be after start time.");
        }

        return [
            'start_time' => $start,
            'end_time'   => $end,
        ];
    }
}
