<?php
// Author: Lee Chee Loong

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'event_id' => $this->id,
            'event_name' => $this->name,
            'ticket_price' => $this->ticket_price,
            'ticket_quantity' => $this->ticket_quantity,
            'ticket_sold' => $this->ticket_sold,
            'available_tickets' => $this->available_tickets,
            'event_status' => $this->status,
            
            // Venue information
            'venue' => $this->whenLoaded('venue', function () {
                return [
                    'id' => $this->venue->id,
                    'name' => $this->venue->name,
                    'location' => $this->venue->location,
                    'capacity' => $this->venue->capacity,
                ];
            }),
            
            // Event dates
            'event_dates' => [
                'start_time' => $this->start_time?->format('H:i:s'),
                'end_time' => $this->end_time?->format('H:i:s'),
            ],
            
            // Financial information
            'financials' => [
                'total_revenue' => $this->total_revenue,
                'total_costs' => $this->total_costs,
                'net_profit' => $this->net_profit,
            ],
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
