<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Resources\BoothResource;
use App\Http\Requests\Api\UpdateBoothQuantityRequest;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class VendorApiController_cl extends Controller
{
    /**
     * Get event information for vendor display
     */
    public function getEventInfo(Event $event): JsonResponse
    {
        try {
            $event->load(['venue', 'activities']);
            
            return response()->json([
                'success' => true,
                'message' => 'Event information retrieved successfully',
                'data' => new EventResource($event)
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve event information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get booth information for a specific event
     */
    public function getBoothInfo(Event $event): JsonResponse
    {
        try {
            $event->load(['venue']);
            
            return response()->json([
                'success' => true,
                'message' => 'Booth information retrieved successfully',
                'data' => new BoothResource($event)
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve booth information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update booth quantity (add or subtract sold booths)
     */
    public function updateBoothQuantity(UpdateBoothQuantityRequest $request, Event $event): JsonResponse
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
                    // Check if we have enough booths to subtract
                    if ($event->booth_sold + $quantity > $event->booth_quantity) {
                        throw new \Exception('Insufficient booths available. Available: ' . $event->available_booths);
                    }
                    
                    // Subtract sold booths (increase available booths)
                    $event->booth_sold += $quantity;
                    
                } else { // add operation
                    // Check if we're not going below zero
                    if ($event->booth_sold - $quantity < 0) {
                        throw new \Exception('Cannot reduce sold booths below zero');
                    }
                    
                    // Add back to available booths (decrease sold booths)
                    $event->booth_sold -= $quantity;
                }

                $event->save();
                
                // Update financials
                $event->updateFinancials();

                return response()->json([
                    'success' => true,
                    'message' => 'Booth quantity updated successfully',
                    'data' => [
                        'event_id' => $event->id,
                        'booth_quantity' => $event->booth_quantity,
                        'booth_sold' => $event->booth_sold,
                        'available_booths' => $event->available_booths,
                        'operation' => $operation,
                        'quantity_changed' => $quantity,
                    ]
                ], 200);
            });
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update booth quantity',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get all events with booth information
     */
    public function getAllEventsWithBooths(Request $request): JsonResponse
    {
        try {
            $query = Event::with(['venue'])
                ->whereNotNull('booth_price')
                ->where('booth_quantity', '>', 0);
            
            // Filter by status if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            $events = $query->paginate($request->get('per_page', 15));
            
            return response()->json([
                'success' => true,
                'message' => 'Events with booth information retrieved successfully',
                'data' => EventResource::collection($events),
                'pagination' => [
                    'current_page' => $events->currentPage(),
                    'last_page' => $events->lastPage(),
                    'per_page' => $events->perPage(),
                    'total' => $events->total(),
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve events with booth information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get events accepting vendor applications
     */
    public function getEventsAcceptingApplications(Request $request): JsonResponse
    {
        try {
            $query = Event::with(['venue'])
                ->where('status', 'active')
                ->whereNotNull('booth_price')
                ->where('booth_quantity', '>', 0)
                ->whereRaw('booth_quantity > booth_sold'); // Has available booths
            
            $events = $query->paginate($request->get('per_page', 15));
            
            return response()->json([
                'success' => true,
                'message' => 'Events accepting applications retrieved successfully',
                'data' => EventResource::collection($events),
                'pagination' => [
                    'current_page' => $events->currentPage(),
                    'last_page' => $events->lastPage(),
                    'per_page' => $events->perPage(),
                    'total' => $events->total(),
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve events accepting applications',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
