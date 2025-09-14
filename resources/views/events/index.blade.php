@extends('layouts.admin')

@section('title', 'Events - EventHub')
@section('page-title', 'All Events')
@section('page-description', 'Manage and view all your events')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-primary">
                        <i class="fas fa-calendar-alt me-2"></i>All Events
                    </h1>
                    <p class="text-muted">Manage and view all your events</p>
                </div>
                <a href="{{ route('events.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Create Event
                </a>
            </div>
        </div>
    </div>


    
    <!-- Quick Status: Total events, Upcoming events, Total Activities, Total Venue --> 
    @if($events->count() > 0)
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $events->count() }}</h4>
                            <small>Total Events</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $events->where('start_time', '>', now())->count() }}</h4>
                            <small>Upcoming Events</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $events->sum(function($event) { return $event->activities->count(); }) }}</h4>
                            <small>Total Activities</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tasks fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $events->unique('venue_id')->count() }}</h4>
                            <small>Total Venues</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-map-marker-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Events Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Events List
                        <span class="badge bg-primary ms-2">{{ $events->count() }} events</span>
                    </h5>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($events->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">
                                    <i class="fas fa-tag me-1"></i>Event Name
                                </th>
                                <th class="border-0">
                                    <i class="fas fa-map-marker-alt me-1"></i>Venue
                                </th>
                                <th class="border-0">
                                    <i class="fas fa-user-tie me-1"></i>Organizer
                                </th>
                                <th class="border-0">
                                    <i class="fas fa-toggle-on me-1"></i>Status
                                </th>
                                <th class="border-0">
                                    <i class="fas fa-clock me-1"></i>Date & Time
                                </th>
                                <th class="border-0">
                                    <i class="fas fa-tasks me-1"></i>Activities
                                </th>
                                <th class="border-0 text-center">
                                    <i class="fas fa-cogs me-1"></i>Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-calendar text-white"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-primary">{{ $event->name }}</h6>
                                            <small class="text-muted">
                                                Created {{ $event->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $event->venue->name }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-users me-1"></i>Capacity: {{ $event->venue->capacity }}
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-info rounded-circle d-flex align-items-center justify-content-center me-2" 
                                             style="width: 32px; height: 32px;">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                        <span>{{ $event->organizer }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $event->status === 'active' ? 'success' : ($event->status === 'draft' ? 'warning' : 'secondary') }} fs-6">
                                        <i class="fas fa-{{ $event->status === 'active' ? 'check-circle' : ($event->status === 'draft' ? 'edit' : 'pause-circle') }} me-1"></i>
                                        {{ ucfirst($event->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ \Carbon\Carbon::parse($event->start_time)->format('M d, Y') }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }} - 
                                            {{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }}
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        <i class="fas fa-tasks me-1"></i>{{ $event->activities->count() }} activities
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <form action="{{ route('events.show', $event->id) }}" 
                                              method="GET" 
                                              class="d-inline">
                                            <button type="submit" 
                                                    class="btn btn-outline-info btn-sm" 
                                                    title="View Event">
                                                <i class="fas fa-eye me-1"></i>View
                                            </button>
                                        </form>
                                        <form action="{{ route('events.edit', $event->id) }}" 
                                              method="GET" 
                                              class="d-inline">
                                            <button type="submit" 
                                                    class="btn btn-outline-warning btn-sm" 
                                                    title="Edit Event">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </button>
                                        </form>
                                        <form action="{{ route('events.destroy', $event->id) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this event? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-outline-danger btn-sm" 
                                                    title="Delete Event">
                                                <i class="fas fa-trash me-1"></i>Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-calendar-times fa-4x text-muted"></i>
                    </div>
                    <h4 class="text-muted">No Events Found</h4>
                    <p class="text-muted mb-4">You haven't created any events yet. Start by creating your first event!</p>
                    <a href="{{ route('events.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Create Your First Event
                    </a>
                </div>
            @endif
        </div>
    </div>


    @endif
</div>
@endsection
