<?php

namespace App\Services;

use App\Models\Event;
use App\Http\Controllers\Api\TicketApiController;
use App\Http\Requests\Api\UpdateTicketQuantityRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class TicketService
{
    protected $ticketApiController;

    public function __construct(TicketApiController $ticketApiController)
    {
        $this->ticketApiController = $ticketApiController;
    }

    /**
     * Get ticket information for an event
     * Can be consumed internally or externally via API based on use_api query parameter
     */
    public function getTicketInfo(Event $event, bool $useApi = false)
    {
        try {
            // Auto-detect: if request has 'use_api' query param, consume externally
            $useApi = request()->query('use_api', $useApi);
            
            if ($useApi) {
                // External API consumption (simulate another module)
                $response = Http::timeout(10)->get(url("/api/v1/ticketing/events/{$event->id}/tickets"));
                
                if ($response->failed()) {
                    throw new \Exception('Failed to fetch ticket info from API');
                }
                
                $apiData = $response->json();
                return $apiData['data'];
            } else {
                // Internal service consumption
                $response = $this->ticketApiController->getTicketInfo($event);
                $data = $response->getData(true);
                return $data['data'];
            }
            
        } catch (\Exception $e) {
            // Fallback to internal consumption if API fails
            $response = $this->ticketApiController->getTicketInfo($event);
            $data = $response->getData(true);
            return $data['data'];
        }
    }

    /**
     * Get all events with ticket information
     * Can be consumed internally or externally via API based on use_api query parameter
     */
    public function getAllEventsWithTickets(bool $useApi = false)
    {
        try {
            // Auto-detect: if request has 'use_api' query param, consume externally
            $useApi = request()->query('use_api', $useApi);
            
            if ($useApi) {
                // External API consumption (simulate another module)
                $response = Http::timeout(10)->get(url('/api/v1/ticketing/events'));
                
                if ($response->failed()) {
                    throw new \Exception('Failed to fetch events with tickets from API');
                }
                
                $apiData = $response->json();
                return $apiData['data'];
            } else {
                // Internal service consumption
                $response = $this->ticketApiController->getAllEventsWithTickets(request());
                $data = $response->getData(true);
                return $data['data'];
            }
            
        } catch (\Exception $e) {
            // Fallback to internal consumption if API fails
            $response = $this->ticketApiController->getAllEventsWithTickets(request());
            $data = $response->getData(true);
            return $data['data'];
        }
    }

    /**
     * Update ticket quantity
     * Can be consumed internally or externally via API based on use_api query parameter
     */
    public function updateTicketQuantity(Event $event, array $data, bool $useApi = false)
    {
        try {
            // Auto-detect: if request has 'use_api' query param, consume externally
            $useApi = request()->query('use_api', $useApi);
            
            if ($useApi) {
                // External API consumption (simulate another module)
                $response = Http::timeout(10)->patch(url("/api/v1/ticketing/events/{$event->id}/tickets/quantity"), $data);
                
                if ($response->failed()) {
                    throw new \Exception('Failed to update ticket quantity via API');
                }
                
                return $response->json();
            } else {
                // Internal service consumption - directly implement the logic
                return $this->updateTicketQuantityInternal($event, $data);
            }
            
        } catch (\Exception $e) {
            // Fallback to internal consumption if API fails
            return $this->updateTicketQuantityInternal($event, $data);
        }
    }

    /**
     * Internal method to update ticket quantity directly
     */
    private function updateTicketQuantityInternal(Event $event, array $data): array
    {
        $quantity = $data['quantity'];
        $operation = $data['operation'];

        return \DB::transaction(function () use ($event, $quantity, $operation) {
            // Lock the event row for update to prevent race conditions
            $event = \App\Models\Event::where('id', $event->id)->lockForUpdate()->first();
            
            if (!$event) {
                throw new \Exception('Event not found');
            }

            if ($operation === 'subtract') {
                // Check if we have enough tickets to subtract
                if ($event->ticket_sold + $quantity > $event->ticket_quantity) {
                    throw new \Exception('Not enough tickets available');
                }
                
                $event->ticket_sold += $quantity;
            } else {
                // Add operation - check if we're not going below 0
                if ($event->ticket_sold - $quantity < 0) {
                    throw new \Exception('Cannot reduce sold tickets below zero');
                }
                
                $event->ticket_sold -= $quantity;
            }
            
            $event->save();
            
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
}

