<form action="{{ route('activities.store') }}" method="POST">
    @csrf
    <input type="hidden" name="event_id" value="{{ $event->id }}">

    <label>Name:</label>
    <input type="text" name="name" required>

    <label>Description:</label>
    <textarea name="description"></textarea>

    <label>Start Time:</label>
    <input type="datetime-local" name="start_time" required>

    <label>Duration (minutes):</label>
    <input type="number" name="duration" min="1" required>

    <label>Status:</label>
    <select name="status">
        <option value="pending" selected>Pending</option>
        <option value="in_progress">In Progress</option>
        <option value="completed">Completed</option>
    </select>

    <label>Venue:</label>
    <select name="venue_id" required>
        @foreach($venues as $venue)
            <option value="{{ $venue->id }}">{{ $venue->name }} (Cap: {{ $venue->capacity }})</option>
        @endforeach
    </select>

    <button type="submit">Save Activity</button>
</form>
