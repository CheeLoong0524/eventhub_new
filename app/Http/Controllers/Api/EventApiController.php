<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Resources\ActivityResource;
use App\Http\Resources\VenueResource;
use App\Models\Event;
use App\Models\Activity;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EventApiController extends Controller
{
    /**
     * Get all events with optional filtering
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Event::with(['venue', 'activities']);
            
            // Filter by status if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            // Filter by date range if provided
            if ($request->has('start_date')) {
                $query->where('start_time', '>=', $request->start_date);
            }
            
            if ($request->has('end_date')) {
                $query->where('start_time', '<=', $request->end_date);
            }
            
            $events = $query->paginate($request->get('per_page', 15));
            
            return response()->json([
                'success' => true,
                'message' => 'Events retrieved successfully',
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
                'message' => 'Failed to retrieve events',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific event by ID
     */
    public function show(Event $event): JsonResponse
    {
        try {
            $event->load(['venue', 'activities']);
            
            return response()->json([
                'success' => true,
                'message' => 'Event retrieved successfully',
                'data' => new EventResource($event)
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve event',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
}
