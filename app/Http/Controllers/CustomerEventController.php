<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class CustomerEventController extends Controller
{
    /**
     * Display events for customers only
     */
    public function index(Request $request): View
    {
        try {
            // Auto-detect: if request has 'use_api' query param, consume externally
            $useApi = $request->query('use_api', false);
            
            if ($useApi) {
                // External API consumption (simulate another module)
                $response = \Illuminate\Support\Facades\Http::timeout(10)->get(url('/api/v1/events'));
                
                if ($response->failed()) {
                    throw new \Exception('Failed to fetch events from API');
                }
                
                $apiData = $response->json();
                $events = collect($apiData['data'])->paginate(12);
            } else {
                // Internal service consumption
                $events = Event::with(['venue', 'activities'])
                              ->where('status', 'active')
                              ->where(function($query) {
                                  $query->where('start_date', '>=', now()->toDateString())
                                        ->orWhereNull('start_date');
                              })
                              ->orderBy('start_date', 'asc')
                              ->orderBy('start_time', 'asc')
                              ->paginate(12);
            }

            return view('customer.events.index', compact('events'));
            
        } catch (\Exception $e) {
            // Fallback to internal consumption if API fails
            $events = Event::with(['venue', 'activities'])
                          ->where('status', 'active')
                          ->where(function($query) {
                              $query->where('start_date', '>=', now()->toDateString())
                                    ->orWhereNull('start_date');
                          })
                          ->orderBy('start_date', 'asc')
                          ->orderBy('start_time', 'asc')
                          ->paginate(12);

            return view('customer.events.index', compact('events'));
        }
    }

    /**
     * Display event details for customers
     */
    public function show(Request $request, $id): View
    {
        try {
            // Auto-detect: if request has 'use_api' query param, consume externally
            $useApi = $request->query('use_api', false);
            
            if ($useApi) {
                // External API consumption (simulate another module)
                $response = \Illuminate\Support\Facades\Http::timeout(10)->get(url("/api/v1/events/{$id}"));
                
                if ($response->failed()) {
                    throw new \Exception('Failed to fetch event from API');
                }
                
                $apiData = $response->json();
                $eventData = $apiData['data'];
                
                // Convert API data back to Event model for compatibility
                $event = new Event($eventData);
                $event->exists = true;
                $event->id = $eventData['id'];
            } else {
                // Internal service consumption
                $event = Event::with(['venue', 'activities.venue'])->findOrFail($id);
            }

            // Check if event is available for customers
            if ($event->status !== 'active' || $event->isPast()) {
                abort(404, 'Event not found or no longer available');
            }

            // Load ticket information directly
            $ticketInfo = null;
            if ($event->ticket_price && $event->ticket_quantity > 0) {
                $ticketInfo = [
                    'ticket_price' => $event->ticket_price,
                    'ticket_quantity' => $event->ticket_quantity,
                    'ticket_sold' => $event->ticket_sold,
                    'available_tickets' => $event->available_tickets,
                ];
            }

            return view('customer.events.show', compact('event', 'ticketInfo'));
            
        } catch (\Exception $e) {
            // Fallback to internal consumption if API fails
            $event = Event::with(['venue', 'activities.venue'])->findOrFail($id);
            
            if ($event->status !== 'active' || $event->isPast()) {
                abort(404, 'Event not found or no longer available');
            }

            // Load ticket information for fallback
            $ticketInfo = null;
            if ($event->ticket_price && $event->ticket_quantity > 0) {
                $ticketInfo = [
                    'ticket_price' => $event->ticket_price,
                    'ticket_quantity' => $event->ticket_quantity,
                    'ticket_sold' => $event->ticket_sold,
                    'available_tickets' => $event->available_tickets,
                ];
            }

            return view('customer.events.show', compact('event', 'ticketInfo'));
        }
    }

    /**
     * Get events for API (AJAX requests)
     */
    public function apiIndex(Request $request): JsonResponse
    {
        try {
            // Auto-detect: if request has 'use_api' query param, consume externally
            $useApi = $request->query('use_api', false);
            
            if ($useApi) {
                // External API consumption (simulate another module)
                $response = \Illuminate\Support\Facades\Http::timeout(10)->get(url('/api/v1/events'));
                
                if ($response->failed()) {
                    throw new \Exception('Failed to fetch events from API');
                }
                
                $apiData = $response->json();
                return response()->json([
                    'success' => true,
                    'events' => $apiData['data'],
                    'pagination' => $apiData['pagination'] ?? []
                ]);
            } else {
                // Internal service consumption
                $events = Event::with(['venue', 'activities'])
                              ->where('status', 'active')
                              ->where(function($query) {
                                  $query->where('start_date', '>=', now()->toDateString())
                                        ->orWhereNull('start_date');
                              })
                              ->orderBy('start_date', 'asc')
                              ->orderBy('start_time', 'asc')
                              ->paginate(12);

                return response()->json([
                    'success' => true,
                    'events' => $events->items(),
                    'pagination' => [
                        'current_page' => $events->currentPage(),
                        'last_page' => $events->lastPage(),
                        'per_page' => $events->perPage(),
                        'total' => $events->total(),
                    ]
                ]);
            }
            
        } catch (\Exception $e) {
            // Fallback to internal consumption if API fails
            $events = Event::with(['venue', 'activities'])
                          ->where('status', 'active')
                          ->where(function($query) {
                              $query->where('start_date', '>=', now()->toDateString())
                                    ->orWhereNull('start_date');
                          })
                          ->orderBy('start_date', 'asc')
                          ->orderBy('start_time', 'asc')
                          ->paginate(12);

            return response()->json([
                'success' => true,
                'events' => $events->items(),
                'pagination' => [
                    'current_page' => $events->currentPage(),
                    'last_page' => $events->lastPage(),
                    'per_page' => $events->perPage(),
                    'total' => $events->total(),
                ]
            ]);
        }
    }

    /**
     * Get event details for API
     */
    public function apiShow(Event $event): JsonResponse
    {
        $event->load(['venue', 'activities']);

        if ($event->status !== 'active' || $event->isPast()) {
            return response()->json([
                'success' => false,
                'error' => 'Event not found or no longer available'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'event' => $event
        ]);
    }

    /**
     * Get featured events for customers
     */
    public function featured(): JsonResponse
    {
        $events = Event::with(['venue', 'activities'])
                      ->where('status', 'active')
                      ->where(function($query) {
                          $query->where('start_date', '>=', now()->toDateString())
                                ->orWhereNull('start_date');
                      })
                      ->where('is_featured', true)
                      ->orderBy('start_date', 'asc')
                      ->limit(6)
                      ->get();

        return response()->json([
            'success' => true,
            'events' => $events
        ]);
    }

    /**
     * Get upcoming events for customers
     */
    public function upcoming(): JsonResponse
    {
        $events = Event::with(['venue', 'activities'])
                      ->where('status', 'active')
                      ->where(function($query) {
                          $query->where('start_date', '>=', now()->toDateString())
                                ->orWhereNull('start_date');
                      })
                      ->orderBy('start_date', 'asc')
                      ->orderBy('start_time', 'asc')
                      ->limit(8)
                      ->get();

        return response()->json([
            'success' => true,
            'events' => $events
        ]);
    }
}
