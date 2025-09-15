@extends('layouts.admin')

@section('title', 'Create Event - EventHub')
@section('page-title', 'Create Event')
@section('page-description', 'Fill out the form to create a new event')

@section('content')
<!-- Author: Lee Chee Loong -->

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-primary"><i class="fas fa-calendar-plus me-2"></i>Create Event</h1>
                <p class="text-muted mb-0">Fill out the form below to create a new event</p>
            </div>
            <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Events
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">

                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Event Information</h5>
                </div>

                <div class="card-body">
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6 class="mb-2">Please correct the following errors:</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('events.store') }}" method="POST">
                        @csrf

                        <h6 class="text-uppercase text-muted small mb-3">Details</h6>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label"><i class="fas fa-tag me-1"></i>Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Event name" required>
                                <small class="text-muted">A short, descriptive name your attendees will recognize.</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Venue <span class="text-danger">*</span></label>
                                <select name="venue_id" class="form-select" required>
                                    <option value="">Select a venue</option>
                                    @foreach($venues as $venue)
                                        <option value="{{ $venue->id }}">{{ $venue->name }} (Cap: {{ $venue->capacity }})</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Choose where your event will take place.</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-user-tie me-1"></i>Organizer <span class="text-danger">*</span></label>
                                <input type="text" name="organizer" class="form-control" placeholder="Organizer name" required>
                                <small class="text-muted">The host or organization responsible for the event.</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-toggle-on me-1"></i>Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="draft">Draft</option>
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <small class="text-muted">Event status - Active events accept bookings and applications.</small>
                            </div>
                        </div>

                        <h6 class="text-uppercase text-muted small mb-3 mt-2">Schedule</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-clock me-1"></i>Start Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_time" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-clock me-1"></i>End Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="end_time" class="form-control" required>
                            </div>
                        </div>

                        <!-- Pricing & Availability -->
                        <hr class="my-4">
                        <h5 class="mb-3"><i class="fas fa-dollar-sign me-2"></i>Pricing & Availability</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-store me-1"></i>Booth Price (RM) <span class="text-danger">*</span></label>
                                <input type="number" name="booth_price" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                                <small class="text-muted">Price per booth for vendors</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-hashtag me-1"></i>Booth Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="booth_quantity" class="form-control" min="1" placeholder="0" required>
                                <small class="text-muted">Total number of booths available</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-ticket-alt me-1"></i>Ticket Price (RM)</label>
                                <input type="number" name="ticket_price" class="form-control" step="0.01" min="0" placeholder="0.00">
                                <small class="text-muted">Price per ticket for customers (optional)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-hashtag me-1"></i>Ticket Quantity</label>
                                <input type="number" name="ticket_quantity" class="form-control" min="0" placeholder="0">
                                <small class="text-muted">Total number of tickets available (optional)</small>
                            </div>
                        </div>

                        <!-- Event Costs -->
                        <hr class="my-4">
                        <h5 class="mb-3"><i class="fas fa-calculator me-2"></i>Event Costs (for Profit/Loss Calculation)</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-building me-1"></i>Venue Cost (RM)</label>
                                <input type="number" name="venue_cost" class="form-control" step="0.01" min="0" placeholder="0.00">
                                <small class="text-muted">Cost of venue rental</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-users me-1"></i>Staff Cost (RM)</label>
                                <input type="number" name="staff_cost" class="form-control" step="0.01" min="0" placeholder="0.00">
                                <small class="text-muted">Staff and personnel costs</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-tools me-1"></i>Equipment Cost (RM)</label>
                                <input type="number" name="equipment_cost" class="form-control" step="0.01" min="0" placeholder="0.00">
                                <small class="text-muted">Equipment rental and setup costs</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-bullhorn me-1"></i>Marketing Cost (RM)</label>
                                <input type="number" name="marketing_cost" class="form-control" step="0.01" min="0" placeholder="0.00">
                                <small class="text-muted">Marketing and promotion costs</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-plus-circle me-1"></i>Other Costs (RM)</label>
                                <input type="number" name="other_costs" class="form-control" step="0.01" min="0" placeholder="0.00">
                                <small class="text-muted">Other miscellaneous costs</small>
                            </div>
                        </div>

                        <!-- Activity -->
                        <hr class="my-4">
                        <h5 class="mb-1"><i class="fas fa-tasks me-2"></i>Activities</h5>
                        <p class="text-muted small mb-3">Add activities that will happen during this event. You can add more or remove them as needed.</p>

                        <div id="activities" class="mb-3">
                            <div class="activity mb-4 card border border-primary rounded-3 shadow-sm" style="border-width: 10px;">
                                <div class="card-body">
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 text-primary">
                                            <i class="fas fa-stream me-2"></i>Activity #1
                                        </h6>
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-activity">
                                            <i class="fas fa-trash me-1"></i>Remove
                                        </button>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Name <span class="text-danger">*</span></label>
                                            <input type="text" name="activities[0][name]" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Venue <span class="text-danger">*</span></label>
                                            <select name="activities[0][venue_id]" class="form-select" required>
                                                @foreach($venues as $venue)
                                                    <option value="{{ $venue->id }}">{{ $venue->name }} (Cap: {{ $venue->capacity }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Start Time <span class="text-danger">*</span></label>
                                            <input type="datetime-local" name="activities[0][start_time]" class="form-control" required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                                            <input type="number" name="activities[0][duration]" class="form-control" min="1" required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="activities[0][status]" class="form-select">
                                                <option value="pending">Pending</option>
                                                <option value="in_progress">In Progress</option>
                                                <option value="completed">Completed</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-0">
                                        <label class="form-label">Description</label>
                                        <textarea name="activities[0][description]" class="form-control" rows="2"></textarea>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" id="add-activity" class="btn btn-outline-success">
                                <i class="fas fa-plus me-1"></i>Add Activity
                            </button>
                            <div>
                                <a href="{{ route('events.index') }}" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Create Event
                                </button>
                            </div>
                        </div>

                        <!-- Activity Template (for adding more activities) --> 
                        <template id="activity-create-template">
                            <div class="activity mb-4 card border border-primary rounded-3 shadow-sm" style="border-width: 10px;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 text-primary">
                                            <i class="fas fa-stream me-2"></i>Activity #__HUMAN_INDEX__
                                        </h6>
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-activity">
                                            <i class="fas fa-trash me-1"></i>Remove
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
                                            <label class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                                            <input type="number" name="activities[__INDEX__][duration]" class="form-control" min="1" required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="activities[__INDEX__][status]" class="form-select">
                                                <option value="pending">Pending</option>
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
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0"><i class="fas fa-lightbulb me-2"></i>Tips</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0 list-unstyled">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Ensure end time is after start time</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Provide clear activity names</li>
                        <li class="mb-0"><i class="fas fa-check-circle text-success me-2"></i>Pick venues with enough capacity</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    const list = document.getElementById('activities');
    const addBtn = document.getElementById('add-activity');
    const template = document.getElementById('activity-create-template').innerHTML;

    // Compute initial count from existing .activity blocks
    let activityIndex = list.querySelectorAll('.activity').length;

    function addActivity() {
        const humanIndex = activityIndex + 1;
        // replace placeholders with the numeric index and human-friendly number
        const html = template
            .replace(/__INDEX__/g, activityIndex)
            .replace(/__HUMAN_INDEX__/g, humanIndex);

        const wrapper = document.createElement('div');
        wrapper.innerHTML = html.trim();
        const node = wrapper.firstElementChild;
        list.appendChild(node);

        activityIndex++;
    }

    function reindexActivities() {
        const cards = Array.from(list.querySelectorAll('.activity'));
        cards.forEach((card, idx) => {
            const human = idx + 1;

            // Update header (preserve icon if present)
            const h6 = card.querySelector('h6');
            if (h6) {
                const icon = h6.querySelector('i');
                const iconHtml = icon ? icon.outerHTML + ' ' : '';
                h6.innerHTML = iconHtml + 'Activity #' + human;
            }

            // Update all name attributes inside this card to use the new index
            const namedElements = card.querySelectorAll('[name]');
            namedElements.forEach(el => {
                // Replace any existing activities[n] prefix with the new index
                el.name = el.name.replace(/activities\[\d+\]/, `activities[${idx}]`);
            });
        });

        // Reset the next index to current count (so new one will be appended at the end)
        activityIndex = cards.length;
    }

    // Add button handler
    addBtn.addEventListener('click', function () {
        addActivity();
    });

    // Delegated remove handler (works if clicking <button> or <i> inside it)
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-activity');
        if (btn) {
            const card = btn.closest('.activity');
            if (card) {
                card.remove();
                // reindex after removal so indexes become contiguous and next index resets if none left
                reindexActivities();
            }
        }
    });

    // Ensure initial blocks (if any) are properly indexed on page load
    reindexActivities();
})();
</script>
@endsection