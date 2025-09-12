<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{

    /**
     * Display a listing of events
     */
    public function index(Request $request): View
    {
        $events = Event::with(['ticketTypes', 'creator'])
                      ->where('status', 'published')
                      ->upcoming()
                      ->orderBy('date', 'asc')
                      ->orderBy('time', 'asc')
                      ->paginate(12);

        return view('events.index', compact('events'));
    }

    /**
     * Display the specified event
     */
    public function show(Event $event): View
    {
        $event->load(['ticketTypes' => function ($query) {
            $query->where('is_active', true);
        }, 'creator']);

        // Check if event is available
        if ($event->status !== 'published' || $event->isPast()) {
            abort(404, 'Event not found or no longer available');
        }

        return view('events.show', compact('event'));
    }

    /**
     * Get events for API (AJAX requests)
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $events = Event::with(['ticketTypes', 'creator'])
                      ->where('status', 'published')
                      ->upcoming()
                      ->orderBy('date', 'asc')
                      ->orderBy('time', 'asc')
                      ->paginate(12);

        return response()->json([
            'events' => $events->items(),
            'pagination' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
            ]
        ]);
    }

    /**
     * Get event details for API
     */
    public function apiShow(Event $event): JsonResponse
    {
        $event->load(['ticketTypes' => function ($query) {
            $query->where('is_active', true);
        }, 'creator']);

        if ($event->status !== 'published' || $event->isPast()) {
            return response()->json(['error' => 'Event not found'], 404);
        }

        return response()->json([
            'event' => $event,
            'ticket_types' => $event->ticketTypes->map(function ($ticketType) {
                return [
                    'id' => $ticketType->id,
                    'name' => $ticketType->name,
                    'description' => $ticketType->description,
                    'price' => $ticketType->price,
                    'formatted_price' => $ticketType->getFormattedPrice(),
                    'available_quantity' => $ticketType->getRemainingQuantity(),
                    'max_per_order' => $ticketType->max_per_order,
                    'is_available' => $ticketType->isAvailableForSale(),
                ];
            })
        ]);
    }

    /**
     * Get featured events
     */
    public function featured(): JsonResponse
    {
        $events = Event::with(['ticketTypes', 'creator'])
                      ->where('status', 'published')
                      ->upcoming()
                      ->featured()
                      ->orderBy('popularity_score', 'desc')
                      ->limit(6)
                      ->get();

        return response()->json($events);
    }

    /**
     * Get upcoming events
     */
    public function upcoming(): JsonResponse
    {
        $events = Event::with(['ticketTypes', 'creator'])
                      ->where('status', 'published')
                      ->upcoming()
                      ->orderBy('date', 'asc')
                      ->orderBy('time', 'asc')
                      ->limit(8)
                      ->get();

        return response()->json($events);
    }
}




