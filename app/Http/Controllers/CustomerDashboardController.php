<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\EventOrderYf;
use App\Models\EventPaymentYf;
use Carbon\Carbon;

class CustomerDashboardController extends Controller
{
    /**
     * Show customer dashboard with statistics and recent activity
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's orders with payments
        $userOrders = EventOrderYf::where('user_id', $user->id)
            ->with(['event', 'payment'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics
        $bookedEvents = $userOrders->count();
        
        // Filter events by start date properly - check both start_date and start_time
        $attendedEvents = $userOrders->filter(function($order) {
            $event = $order->event;
            $eventDate = $event->start_date ? $event->start_date->toDateString() : 
                        ($event->start_time ? $event->start_time->toDateString() : null);
            
            return $eventDate && $eventDate < now()->toDateString();
        })->count();
        
        $upcomingEvents = $userOrders->filter(function($order) {
            $event = $order->event;
            $eventDate = $event->start_date ? $event->start_date->toDateString() : 
                        ($event->start_time ? $event->start_time->toDateString() : null);
            
            return !$eventDate || $eventDate >= now()->toDateString();
        })->count();
        
        // Debug logging
        \Log::info('Dashboard Statistics', [
            'user_id' => $user->id,
            'total_orders' => $userOrders->count(),
            'attended_events' => $attendedEvents,
            'upcoming_events' => $upcomingEvents,
            'today' => now()->toDateString(),
            'events_with_dates' => $userOrders->map(function($order) {
                $event = $order->event;
                $eventDate = $event->start_date ? $event->start_date->toDateString() : 
                            ($event->start_time ? $event->start_time->toDateString() : null);
                return [
                    'event_name' => $event->name,
                    'start_date' => $event->start_date ? $event->start_date->toDateString() : null,
                    'start_time' => $event->start_time ? $event->start_time->toDateString() : null,
                    'calculated_date' => $eventDate,
                    'is_past' => $eventDate ? $eventDate < now()->toDateString() : false
                ];
            })->toArray()
        ]);
        
        $totalSpent = $userOrders->sum('total_amount');

        // Get upcoming events list (next 3) - events that haven't passed yet
        $upcomingEventsList = $userOrders
            ->filter(function($order) {
                $event = $order->event;
                $eventDate = $event->start_date ? $event->start_date->toDateString() : 
                            ($event->start_time ? $event->start_time->toDateString() : null);
                
                return !$eventDate || $eventDate >= now()->toDateString();
            })
            ->sortBy(function($order) {
                $event = $order->event;
                return $event->start_date ? $event->start_date : 
                       ($event->start_time ? $event->start_time : now()->addYear());
            })
            ->take(3);

        // Get recent activity (last 5 orders)
        $recentActivity = $userOrders->take(5);

        // Get featured events (active events not booked by user)
        $bookedEventIds = $userOrders->pluck('event_id')->toArray();
        $featuredEvents = Event::where('status', 'active')
            ->whereNotIn('id', $bookedEventIds)
            ->where('start_time', '>=', now())
            ->with(['venue'])
            ->orderBy('start_time', 'asc')
            ->limit(3)
            ->get();

        return view('dashboard.customer', compact(
            'user',
            'bookedEvents',
            'attendedEvents', 
            'upcomingEvents',
            'totalSpent',
            'upcomingEventsList',
            'recentActivity',
            'featuredEvents'
        ));
    }
}