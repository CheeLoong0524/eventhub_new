<?php
// Author: Gooi Ye Fan

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Resources\TicketResource;
use App\Http\Requests\Api\UpdateTicketQuantityRequest;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TicketApiController extends Controller
{
    /**
     * Get ticket information for a specific event
     */
    public function getTicketInfo(Event $event): JsonResponse
    {
        try {
            $event->load(['venue']);
            
            return response()->json([
                'success' => true,
                'message' => 'Ticket information retrieved successfully',
                'data' => new TicketResource($event)
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve ticket information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update ticket quantity (add or subtract sold tickets)
     */
    public function updateTicketQuantity(UpdateTicketQuantityRequest $request, Event $event): JsonResponse
    {
        try {
            $validated = $request->validated();
            $quantity = $validated['quantity'];
            $operation = $validated['operation'];

            return DB::transaction(function () use ($event, $quantity, $operation) {
                // Lock the event row for update to prevent race conditions
                $event = Event::where('id', $event->id)->lockForUpdate()->first();
                
                if (!$event) {
                    throw new \Exception('Event not found');
                }

                if ($operation === 'subtract') {
                    // Check if we have enough tickets to subtract
                    if ($quantity > $event->ticket_quantity) {
                        throw new \Exception('Insufficient tickets available. Available: ' . $event->ticket_quantity);
                    }
                    
                    // Subtract from available tickets (decrease ticket_quantity)
                    $event->ticket_quantity -= $quantity;
                    $event->ticket_sold += $quantity;
                    
                } else { // add operation
                    // Check if we're not going below zero
                    if ($event->ticket_sold - $quantity < 0) {
                        throw new \Exception('Cannot reduce sold tickets below zero');
                    }
                    
                    // Add back to available tickets (increase ticket_quantity, decrease sold)
                    $event->ticket_quantity += $quantity;
                    $event->ticket_sold -= $quantity;
                }

                $event->save();
                
                // Update financials
                $event->updateFinancials();

                return response()->json([
                    'success' => true,
                    'message' => 'Ticket quantity updated successfully',
                    'data' => [
                        'event_id' => $event->id,
                        'ticket_price' => $event->ticket_price,
                        'ticket_quantity' => $event->ticket_quantity,
                        'ticket_sold' => $event->ticket_sold,
                        'available_tickets' => $event->available_tickets,
                        'operation' => $operation,
                        'quantity_changed' => $quantity,
                    ]
                ], 200);
            });
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ticket quantity',
                'error' => $e->getMessage()
            ], 422);
        }
    }

}
