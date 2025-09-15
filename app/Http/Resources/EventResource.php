<?php
// Author: Lee Chee Loong

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'name' => $this->name,
            'start_time' => $this->start_time?->format('Y-m-d H:i:s'),
            'end_time' => $this->end_time?->format('Y-m-d H:i:s'),
            'organizer' => $this->organizer,
            'status' => $this->status,
            
            // Ticket information
            'ticket_price' => $this->ticket_price,
            'ticket_quantity' => $this->ticket_quantity,
            'ticket_sold' => $this->ticket_sold,
            'available_tickets' => $this->available_tickets,
            
            // Booth information
            'booth_price' => $this->booth_price,
            'booth_quantity' => $this->booth_quantity,
            'booth_sold' => $this->booth_sold,
            'available_booths' => $this->available_booths,
            
            
            // Relationships
            'venue' => new VenueResource($this->whenLoaded('venue')),
            'activities' => ActivityResource::collection($this->whenLoaded('activities')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
