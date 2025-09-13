<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Activity;
use App\Builders\EventDirector;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Services\EventService;

class EventController extends Controller
{
    protected $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }
    // Show all events
    public function index()
    {
        $events = $this->eventService->getAllEvents();
        return view('events.index', compact('events'));
    }

    // Show form to create new event
    public function create()
    {
        $venues = \App\Models\Venue::all();
        return view('events.create', compact('venues'));
    }

    // Store new event with activities
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'venue_id'   => 'required|exists:venues,id',
            'start_time' => 'required|date',
            'end_time'   => 'required|date|after:start_time',
            'organizer'  => 'required|string|max:255',
            'status'     => 'required|in:draft,active,inactive',

            // Pricing and availability
            'booth_price'    => 'required|numeric|min:0',
            'booth_quantity' => 'required|integer|min:1',
            'ticket_price'   => 'nullable|numeric|min:0',
            'ticket_quantity'=> 'nullable|integer|min:0',

            // Event costs
            'venue_cost'     => 'nullable|numeric|min:0',
            'staff_cost'     => 'nullable|numeric|min:0',
            'equipment_cost' => 'nullable|numeric|min:0',
            'marketing_cost' => 'nullable|numeric|min:0',
            'other_costs'    => 'nullable|numeric|min:0',

            // Activities
            'activities'              => 'sometimes|array',
            'activities.*.name'       => 'required|string|max:255',
            'activities.*.description'=> 'nullable|string',
            'activities.*.start_time' => 'required|date|after_or_equal:start_time|before_or_equal:end_time',
            'activities.*.duration'   => 'required|integer|min:1',
            'activities.*.status'     => 'nullable|in:pending,in_progress,completed',
            'activities.*.venue_id'   => 'required|exists:venues,id',
        ]); // Hardcoded

        $event = Event::create($validated);

        // Save activities if provided
        if (!empty($validated['activities'])) {
            foreach ($validated['activities'] as $activityData) {
                $event->activities()->create($activityData);
            }
        }

        // Calculate and update financials
        $event->updateFinancials();

        return redirect()->route('events.index')->with('success', 'Event created successfully!');
    }

    // Show single event details
    public function show($id)
    {
        $event =  $this -> eventService -> getEventById($id);
        return view('events.show', compact('event'));
    }


    // Show edit form
    public function edit($id)
    {
        $event = Event::findOrFail($id);
        $venues = \App\Models\Venue::all();
        return view('events.edit', compact('event', 'venues'));
    }

    // Update event
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'venue_id'   => 'required|exists:venues,id',
            'start_time' => 'required|date',
            'end_time'   => 'required|date|after:start_time',
            'organizer'  => 'required|string|max:255',
            'status'     => 'required|in:draft,active,inactive',

            // Pricing and availability
            'booth_price'    => 'required|numeric|min:0',
            'booth_quantity' => 'required|integer|min:1',
            'ticket_price'   => 'nullable|numeric|min:0',
            'ticket_quantity'=> 'nullable|integer|min:0',

            // Event costs
            'venue_cost'     => 'nullable|numeric|min:0',
            'staff_cost'     => 'nullable|numeric|min:0',
            'equipment_cost' => 'nullable|numeric|min:0',
            'marketing_cost' => 'nullable|numeric|min:0',
            'other_costs'    => 'nullable|numeric|min:0',

            // Activities
            'activities'              => 'array',
            'activities.*.name'       => 'required|string|max:255',
            'activities.*.description'=> 'nullable|string',
            'activities.*.start_time' => 'required|date|after_or_equal:start_time|before_or_equal:end_time',
            'activities.*.duration'   => 'required|integer|min:1',
            'activities.*.status'     => 'nullable|in:pending,in_progress,completed',
            'activities.*.venue_id'   => 'required|exists:venues,id',
        ]);


        $event = Event::findOrFail($id);
        $event->update($validated);

        // Sync activities only if provided
        if ($request->has('activities')) {
            $activitiesPayload = $validated['activities'] ?? [];

            $existingIds = $event->activities->pluck('id')->toArray();
            $incomingIds = collect($activitiesPayload)->pluck('id')->filter()->toArray();

            // Delete removed activities
            $toDelete = array_diff($existingIds, $incomingIds);
            if (!empty($toDelete)) {
                Activity::destroy($toDelete);
            }

            // Update or create activities
            foreach ($activitiesPayload as $activityData) {
                if (!empty($activityData['id'])) {
                    $activity = Activity::find($activityData['id']);
                    if ($activity) {
                        $activity->update($activityData);
                    }
                } else {
                    $event->activities()->create($activityData);
                }
            }
        }

        // Calculate and update financials
        $event->updateFinancials();

        return redirect()->route('events.show', $event->id)->with('success', 'Event updated successfully!');
    }

    // Delete event
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return redirect()->route('events.index')->with('success', 'Event deleted successfully!');
    }

    // Update event status
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,active,inactive'
        ]);

        $event = Event::findOrFail($id);
        $event->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Event status updated successfully!');
    }
}
