<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use App\Builders\Activities\ActivityDirector;
use App\Builders\Activities\ConcreteActivityBuilder;

class ActivityController extends Controller
{
    public function create(Request $request)
    {
        $eventId = $request->query('event_id');
        $event = \App\Models\Event::findOrFail($eventId);
        $venues = \App\Models\Venue::all();
        return view('activities.create', compact('event', 'venues'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id'   => 'required|exists:events,id',
            'name'       => 'required|string|max:255',
            'description'=> 'nullable|string',
            'start_time' => 'required|date',
            'duration'   => 'required|integer',
            'status'     => 'nullable|in:pending,in_progress,completed',
            'venue_id'   => 'required|exists:venues,id',
        ]);
        


        $director = new ActivityDirector();
        $builder = new ConcreteActivityBuilder();

        $activity = $director->buildActivity($builder, $validated);
        $activity->save();

        return redirect()->back()->with('success', 'Activity created successfully!');
    }
}

