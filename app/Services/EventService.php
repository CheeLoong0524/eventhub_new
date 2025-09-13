<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\DB;

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


    
    // Update ticket quantity by deducting sold tickets
    // Used by ticketing module to update ticket availability
    // input: $eventId, $quantityToDeduct 
    // output: array
    // throws \Exception

    public function updateTicketQuantity(int $eventId, int $quantityToDeduct): array
    {
        return DB::transaction(function () use ($eventId, $quantityToDeduct) {
            // Lock the event row for update to prevent race conditions
            $event = Event::where('id', $eventId)->lockForUpdate()->first();
            
            if (!$event) {
                throw new \Exception('Event not found');
            }

            // Check if we have enough tickets to deduct
            if ($event->ticket_sold + $quantityToDeduct > $event->ticket_quantity) {
                throw new \Exception('Insufficient tickets available. Available: ' . $event->available_tickets);
            }
            
            // Deduct from available tickets (add to sold tickets)
            $event->ticket_sold += $quantityToDeduct;
            $event->ticket_quantity -= $quantityToDeduct;
            $event->save();
            
            // Update financials
            $event->updateFinancials();
            
            return [
                'success' => true,
                'message' => 'Ticket quantity updated successfully',
                'data' => [
                    'event_id' => $event->id,
                    'ticket_quantity' => $event->ticket_quantity,
                    'ticket_sold' => $event->ticket_sold,
                    'available_tickets' => $event->available_tickets
                ]
            ];
        });
    }


    
    // Update vendor/booth quantity by deducting sold booths
    // Used by vendor module to update booth availability 
    // input: $eventId, $quantityToDeduct
    // output: array
    // throws \Exception

    public function updateVendorQuantity(int $eventId, int $quantityToDeduct): array
    {
        return DB::transaction(function () use ($eventId, $quantityToDeduct) {
            // Lock the event row for update to prevent race conditions
            $event = Event::where('id', $eventId)->lockForUpdate()->first();
            
            if (!$event) {
                throw new \Exception('Event not found');
            }

            // Check if we have enough booths to deduct
            if ($event->booth_sold + $quantityToDeduct > $event->booth_quantity) {
                throw new \Exception('Insufficient booths available. Available: ' . $event->available_booths);
            }
            
            // Deduct from available booths (add to sold booths)
            $event->booth_sold += $quantityToDeduct;
            $event->booth_quantity -= $quantityToDeduct;
            $event->save();
            
            // Update financials
            $event->updateFinancials();
            
            return [
                'success' => true,
                'message' => 'Vendor quantity updated successfully',
                'data' => [
                    'event_id' => $event->id,
                    'booth_quantity' => $event->booth_quantity,
                    'booth_sold' => $event->booth_sold,
                    'available_booths' => $event->available_booths
                ]
            ];
        });
    }
}