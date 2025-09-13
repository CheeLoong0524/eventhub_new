@extends('layouts.admin')

@section('title', 'Edit Event - EventHub')
@section('page-title', 'Edit Event')
@section('page-description', 'Update the details for "' . $event->name . '"')

@section('content')
<div class="container-fluid">
    <!-- Navigation Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Events</a></li>
            <li class="breadcrumb-item"><a href="{{ route('events.show', $event->id) }}">{{ $event->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Event</li>
        </ol>
    </nav>


    <!-- Form Section -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Event Information
                    </h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6>Please correct the following errors:</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('events.update', $event->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-tag me-1"></i>Event Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="name" 
                                       id="name"
                                       class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $event->name) }}"
                                       placeholder="Enter event name"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="venue_id" class="form-label">
                                    <i class="fas fa-map-marker-alt me-1"></i>Venue <span class="text-danger">*</span>
                                </label>
                                <select name="venue_id" 
                                        id="venue_id"
                                        class="form-select @error('venue_id') is-invalid @enderror" 
                                        required>
                                    <option value="">Select a venue</option>
                                    @foreach($venues as $venue)
                                        <option value="{{ $venue->id }}" 
                                                {{ old('venue_id', $event->venue_id) == $venue->id ? 'selected' : '' }}>
                                            {{ $venue->name }} (Capacity: {{ $venue->capacity }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('venue_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="organizer" class="form-label">
                                    <i class="fas fa-user-tie me-1"></i>Organizer <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="organizer" 
                                       id="organizer"
                                       class="form-control @error('organizer') is-invalid @enderror" 
                                       value="{{ old('organizer', $event->organizer) }}"
                                       placeholder="Enter organizer name"
                                       required>
                                @error('organizer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">
                                    <i class="fas fa-toggle-on me-1"></i>Status <span class="text-danger">*</span>
                                </label>
                                <select name="status" 
                                        id="status"
                                        class="form-select @error('status') is-invalid @enderror" 
                                        required>
                                    <option value="draft" {{ old('status', $event->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="active" {{ old('status', $event->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $event->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Event status - Active events accept bookings and applications.</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_time" class="form-label">
                                    <i class="fas fa-clock me-1"></i>Start Time <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local" 
                                       name="start_time" 
                                       id="start_time"
                                       class="form-control @error('start_time') is-invalid @enderror" 
                                       value="{{ old('start_time', $event->start_time ? $event->start_time->format('Y-m-d\TH:i') : '') }}"
                                       required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="end_time" class="form-label">
                                    <i class="fas fa-clock me-1"></i>End Time <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local" 
                                       name="end_time" 
                                       id="end_time"
                                       class="form-control @error('end_time') is-invalid @enderror" 
                                       value="{{ old('end_time', $event->end_time ? $event->end_time->format('Y-m-d\TH:i') : '') }}"
                                       required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Pricing & Availability -->
                        <hr class="my-4">
                        <h5 class="mb-3"><i class="fas fa-dollar-sign me-2"></i>Pricing & Availability</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="booth_price" class="form-label">
                                    <i class="fas fa-store me-1"></i>Booth Price (RM) <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       name="booth_price" 
                                       id="booth_price"
                                       class="form-control @error('booth_price') is-invalid @enderror" 
                                       value="{{ old('booth_price', $event->booth_price) }}"
                                       step="0.01" min="0" placeholder="0.00" required>
                                @error('booth_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Price per booth for vendors</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="booth_quantity" class="form-label">
                                    <i class="fas fa-hashtag me-1"></i>Booth Quantity <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       name="booth_quantity" 
                                       id="booth_quantity"
                                       class="form-control @error('booth_quantity') is-invalid @enderror" 
                                       value="{{ old('booth_quantity', $event->booth_quantity) }}"
                                       min="1" placeholder="0" required>
                                @error('booth_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Total number of booths available</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ticket_price" class="form-label">
                                    <i class="fas fa-ticket-alt me-1"></i>Ticket Price (RM)
                                </label>
                                <input type="number" 
                                       name="ticket_price" 
                                       id="ticket_price"
                                       class="form-control @error('ticket_price') is-invalid @enderror" 
                                       value="{{ old('ticket_price', $event->ticket_price) }}"
                                       step="0.01" min="0" placeholder="0.00">
                                @error('ticket_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Price per ticket for customers (optional)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ticket_quantity" class="form-label">
                                    <i class="fas fa-hashtag me-1"></i>Ticket Quantity
                                </label>
                                <input type="number" 
                                       name="ticket_quantity" 
                                       id="ticket_quantity"
                                       class="form-control @error('ticket_quantity') is-invalid @enderror" 
                                       value="{{ old('ticket_quantity', $event->ticket_quantity) }}"
                                       min="0" placeholder="0">
                                @error('ticket_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Total number of tickets available (optional)</small>
                            </div>
                        </div>

                        <!-- Event Costs -->
                        <hr class="my-4">
                        <h5 class="mb-3"><i class="fas fa-calculator me-2"></i>Event Costs (for Profit/Loss Calculation)</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="venue_cost" class="form-label">
                                    <i class="fas fa-building me-1"></i>Venue Cost (RM)
                                </label>
                                <input type="number" 
                                       name="venue_cost" 
                                       id="venue_cost"
                                       class="form-control @error('venue_cost') is-invalid @enderror" 
                                       value="{{ old('venue_cost', $event->venue_cost) }}"
                                       step="0.01" min="0" placeholder="0.00">
                                @error('venue_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Cost of venue rental</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="staff_cost" class="form-label">
                                    <i class="fas fa-users me-1"></i>Staff Cost (RM)
                                </label>
                                <input type="number" 
                                       name="staff_cost" 
                                       id="staff_cost"
                                       class="form-control @error('staff_cost') is-invalid @enderror" 
                                       value="{{ old('staff_cost', $event->staff_cost) }}"
                                       step="0.01" min="0" placeholder="0.00">
                                @error('staff_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Staff and personnel costs</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="equipment_cost" class="form-label">
                                    <i class="fas fa-tools me-1"></i>Equipment Cost (RM)
                                </label>
                                <input type="number" 
                                       name="equipment_cost" 
                                       id="equipment_cost"
                                       class="form-control @error('equipment_cost') is-invalid @enderror" 
                                       value="{{ old('equipment_cost', $event->equipment_cost) }}"
                                       step="0.01" min="0" placeholder="0.00">
                                @error('equipment_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Equipment rental and setup costs</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="marketing_cost" class="form-label">
                                    <i class="fas fa-bullhorn me-1"></i>Marketing Cost (RM)
                                </label>
                                <input type="number" 
                                       name="marketing_cost" 
                                       id="marketing_cost"
                                       class="form-control @error('marketing_cost') is-invalid @enderror" 
                                       value="{{ old('marketing_cost', $event->marketing_cost) }}"
                                       step="0.01" min="0" placeholder="0.00">
                                @error('marketing_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Marketing and promotion costs</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="other_costs" class="form-label">
                                    <i class="fas fa-plus-circle me-1"></i>Other Costs (RM)
                                </label>
                                <input type="number" 
                                       name="other_costs" 
                                       id="other_costs"
                                       class="form-control @error('other_costs') is-invalid @enderror" 
                                       value="{{ old('other_costs', $event->other_costs) }}"
                                       step="0.01" min="0" placeholder="0.00">
                                @error('other_costs')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Other miscellaneous costs</small>
                            </div>
                        </div>

                        <!-- Activities Editor -->
                        <hr class="my-4">
                        <h5 class="mb-3">
                            <i class="fas fa-tasks me-2"></i>Activities
                            <span class="badge bg-primary ms-2">{{ $event->activities->count() }}</span>
                        </h5>

                        <div id="activities-wrapper">
                            @php $idx = 0; @endphp
                            @foreach($event->activities as $activity)
                                <div class="card mb-4 activity-item border border-primary rounded-3 shadow-sm" style="border-width: 10px;" data-index="{{ $idx }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0 text-primary">
                                                <i class="fas fa-stream me-2"></i>Activity #{{ $idx + 1 }}
                                            </h6>
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-activity" data-activity-id="{{ $activity->id }}">
                                                <i class="fas fa-trash me-1"></i>Delete
                                            </button>
                                        </div>

                                        <input type="hidden" name="activities[{{ $idx }}][id]" value="{{ $activity->id }}">

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                                <input type="text" name="activities[{{ $idx }}][name]" class="form-control" value="{{ $activity->name }}" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Venue <span class="text-danger">*</span></label>
                                                <select name="activities[{{ $idx }}][venue_id]" class="form-select" required>
                                                    @foreach($venues as $venue)
                                                        <option value="{{ $venue->id }}" {{ $activity->venue_id == $venue->id ? 'selected' : '' }}>
                                                            {{ $venue->name }} (Cap: {{ $venue->capacity }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Start Time <span class="text-danger">*</span></label>
                                                <input type="datetime-local" name="activities[{{ $idx }}][start_time]" class="form-control" value="{{ \Carbon\Carbon::parse($activity->start_time)->format('Y-m-d\TH:i') }}" required>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">Duration (min) <span class="text-danger">*</span></label>
                                                <input type="number" name="activities[{{ $idx }}][duration]" class="form-control" min="1" value="{{ $activity->duration }}" required>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">Status</label>
                                                <select name="activities[{{ $idx }}][status]" class="form-select">
                                                    <option value="pending" {{ $activity->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="in_progress" {{ $activity->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                    <option value="completed" {{ $activity->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label">Description</label>
                                            <textarea name="activities[{{ $idx }}][description]" class="form-control" rows="2">{{ $activity->description }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                @php $idx++; @endphp
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-outline-success" id="add-activity" onclick="addActivity()">
                                <i class="fas fa-plus me-1"></i>Add Activity
                            </button>
                            <div>
                                <a href="{{ route('events.show', $event->id) }}" class="btn btn-outline-info me-2">
                                    <i class="fas fa-eye me-1"></i>View Event
                                </a>
                                <a href="{{ route('events.index') }}" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save me-1"></i>Update Event
                                </button>
                            </div>
                        </div>

                        <!-- Activity Template (hidden) -->
                        <template id="activity-template">
                            <div class="card mb-4 activity-item border border-primary rounded-3 shadow-sm" style="border-width: 10px;" data-index="__INDEX__">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 text-primary">
                                            <i class="fas fa-stream me-2"></i>Activity #__HUMAN_INDEX__
                                        </h6>
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-activity" data-activity-id="">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </button>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Name <span class="text-danger">*</span></label>
                                            <input type="text" name="activities[__INDEX__][name]" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Venue <span class="text-danger">*</span></label>
                                            <select name="activities[__INDEX__][venue_id]" class="form-select" required>
                                                @foreach($venues as $venue)
                                                    <option value="{{ $venue->id }}">{{ $venue->name }} (Cap: {{ $venue->capacity }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Start Time <span class="text-danger">*</span></label>
                                            <input type="datetime-local" name="activities[__INDEX__][start_time]" class="form-control" required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Duration (min) <span class="text-danger">*</span></label>
                                            <input type="number" name="activities[__INDEX__][duration]" class="form-control" min="1" required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="activities[__INDEX__][status]" class="form-select">
                                                <option value="pending" selected>Pending</option>
                                                <option value="in_progress">In Progress</option>
                                                <option value="completed">Completed</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-0">
                                        <label class="form-label">Description</label>
                                        <textarea name="activities[__INDEX__][description]" class="form-control" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div class="d-flex justify-content-between mt-4">
                            <div></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Event Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Created:</strong><br>
                        <small class="text-muted">{{ $event->created_at->format('M d, Y \a\t g:i A') }}</small>
                    </div>
                    <div class="mb-3">
                        <strong>Last Updated:</strong><br>
                        <small class="text-muted">{{ $event->updated_at->format('M d, Y \a\t g:i A') }}</small>
                    </div>
                    <div class="mb-3">
                        <strong>Current Venue:</strong><br>
                        <small class="text-muted">{{ $event->venue->name }}</small>
                    </div>
                    <div class="mb-0">
                        <strong>Activities:</strong><br>
                        <span class="badge bg-primary">{{ $event->activities->count() }} activities</span>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-header bg-success text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('events.show', $event->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>View Event
                        </a>
                        <a href="{{ route('events.create', ['event_id' => $event->id]) }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-plus me-1"></i>Create Event
                        </a>
                        <a href="{{ route('events.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-list me-1"></i>All Events
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const activitiesContainer = document.getElementById('activities-wrapper');
    const addButton = document.getElementById('add-activity');

    if (!activitiesContainer || !addButton) {
        console.error('Missing required elements');
        return;
    }

    let activityCount = activitiesContainer.querySelectorAll('.activity-item').length;

    function addActivity() {
        // Create new activity using the template
        const template = document.getElementById('activity-template');
        if (!template) {
            console.error('Activity template not found');
            return;
        }
        
        const newActivity = template.content.cloneNode(true);
        const activityElement = newActivity.querySelector('.activity-item');
        
        // Update index and form names
        activityElement.setAttribute('data-index', activityCount);
        activityElement.querySelector('h6').innerHTML = `<i class="fas fa-stream me-2"></i>Activity #${activityCount + 1}`;
        
        // Update form field names
        activityElement.querySelectorAll('input, select, textarea').forEach(field => {
            if (field.name) {
                field.name = field.name.replace('__INDEX__', activityCount);
            }
        });
        
        activitiesContainer.appendChild(activityElement);
        bindActivityEvents(activityElement);
        activityCount++;
    }

    function bindActivityEvents(activityElement) {
        const removeBtn = activityElement.querySelector('.remove-activity');
        
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                activityElement.remove();
                reindexActivities();
            });
        }
    }

    function reindexActivities() {
        const activities = activitiesContainer.querySelectorAll('.activity-item');
        
        activities.forEach((activity, index) => {
            activity.setAttribute('data-index', index);
            activity.querySelector('h6').innerHTML = `<i class="fas fa-stream me-2"></i>Activity #${index + 1}`;
            
            activity.querySelectorAll('input, select, textarea').forEach(field => {
                if (field.name) {
                    field.name = field.name.replace(/activities\[\d+\]/, `activities[${index}]`);
                }
            });
        });
        
        activityCount = activities.length;
    }

    // Event listeners
    addButton.addEventListener('click', function(e) {
        e.preventDefault();
        addActivity();
    });

    // Bind events to existing activities
    activitiesContainer.querySelectorAll('.activity-item').forEach(activity => {
        bindActivityEvents(activity);
    });

    // Initial reindex
    reindexActivities();
});
</script>
@endsection