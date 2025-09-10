@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Events</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $event->name }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-primary"><i class="fas fa-calendar me-2"></i>{{ $event->name }}</h1>
                <p class="text-muted mb-0">Event details and activities</p>
            </div>
            <div>
                <a href="{{ route('events.edit', $event->id) }}" class="btn btn-warning me-2"><i class="fas fa-edit me-1"></i>Edit</a>
                <a href="{{ route('events.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Event Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-uppercase text-muted small mb-1">Organizer</h6>
                            <div class="d-flex align-items-center">
                                <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="fas fa-user-tie text-white"></i>
                                </div>
                                <span>{{ $event->organizer }}</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-uppercase text-muted small mb-1">Venue</h6>
                            <div class="d-flex align-items-center">
                                <div class="bg-info rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="fas fa-map-marker-alt text-white"></i>
                                </div>
                                <div>
                                    <div><strong>{{ $event->venue->name }}</strong></div>
                                    <small class="text-muted">Capacity: {{ $event->venue->capacity }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-uppercase text-muted small mb-1">Start Time</h6>
                            <div class="d-flex align-items-center">
                                <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="fas fa-clock text-white"></i>
                                </div>
                                <span>{{ \Carbon\Carbon::parse($event->start_time)->format('M d, Y \a\t g:i A') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-uppercase text-muted small mb-1">End Time</h6>
                            <div class="d-flex align-items-center">
                                <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="fas fa-clock text-white"></i>
                                </div>
                                <span>{{ \Carbon\Carbon::parse($event->end_time)->format('M d, Y \a\t g:i A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-tasks me-2"></i>Activities</h5>
                    <a href="{{ route('activities.create', ['event_id' => $event->id]) }}" class="btn btn-light btn-sm"><i class="fas fa-plus me-1"></i>Add Activity</a>
                </div>
                <div class="card-body">
                    @if($event->activities->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No activities yet</h5>
                            <p class="text-muted">Start planning by adding activities</p>
                            <a href="{{ route('activities.create', ['event_id' => $event->id]) }}" class="btn btn-success"><i class="fas fa-plus me-1"></i>Add Activity</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Time</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                        <th>Venue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($event->activities as $activity)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold text-primary">{{ $activity->name }}</div>
                                                <div class="text-muted small">{{ $activity->description }}</div>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($activity->start_time)->format('M d, Y g:i A') }}</td>
                                            <td>{{ $activity->duration }} min</td>
                                            <td>
                                                <span class="badge bg-{{ $activity->status === 'completed' ? 'success' : ($activity->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($activity->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $activity->venue->name ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white"><h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Stats</h6></div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <h4 class="text-primary mb-0">{{ $event->activities->count() }}</h4>
                            <small class="text-muted">Activities</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h4 class="text-success mb-0">{{ $event->activities->where('status', 'completed')->count() }}</h4>
                            <small class="text-muted">Completed</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-warning mb-0">{{ $event->activities->where('status', 'in_progress')->count() }}</h4>
                            <small class="text-muted">In Progress</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-secondary mb-0">{{ $event->activities->where('status', 'pending')->count() }}</h4>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white"><h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h6></div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('events.edit', $event->id) }}" class="btn btn-outline-warning"><i class="fas fa-edit me-1"></i>Edit Event</a>
                        <a href="{{ route('activities.create', ['event_id' => $event->id]) }}" class="btn btn-outline-success"><i class="fas fa-plus me-1"></i>Add Activity</a>
                        <form action="{{ route('events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash me-1"></i>Delete Event</button>
                        </form>
                        <a href="{{ route('events.index') }}" class="btn btn-outline-secondary"><i class="fas fa-list me-1"></i>All Events</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
