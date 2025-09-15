<?php
// Author: Lee Chee Loong

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event_id' => $this->event_id,
            'name' => $this->name,
            'description' => $this->description,
            'start_time' => $this->start_time?->format('Y-m-d H:i:s'),
            'duration' => $this->duration,
            'status' => $this->status,
            'venue_id' => $this->venue_id,
            
            // Relationships
            'venue' => new VenueResource($this->whenLoaded('venue')),
            'event' => new EventResource($this->whenLoaded('event')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
