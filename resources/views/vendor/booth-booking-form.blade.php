@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Book Booth - {{ $event->name }}</h3>

    <form method="POST" action="{{ route('vendor.events.book.submit', $event->id) }}">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Booth Number</label>
                <input type="text" name="booth_number" value="{{ old('booth_number') }}" class="form-control @error('booth_number') is-invalid @enderror" required>
                @error('booth_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Booth Type</label>
                <input type="text" name="booth_type" value="{{ old('booth_type') }}" class="form-control @error('booth_type') is-invalid @enderror" required>
                @error('booth_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Booth Size (sqm)</label>
                <input type="number" step="0.01" name="booth_size" value="{{ old('booth_size') }}" class="form-control @error('booth_size') is-invalid @enderror" required>
                @error('booth_size')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Price (MYR)</label>
                <input type="number" step="0.01" name="price" value="{{ old('price') }}" class="form-control @error('price') is-invalid @enderror" required>
                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Event Dates</label>
                <div class="d-flex gap-2">
                    <input type="datetime-local" name="event_start_date" value="{{ old('event_start_date') }}" class="form-control @error('event_start_date') is-invalid @enderror" required>
                    <input type="datetime-local" name="event_end_date" value="{{ old('event_end_date') }}" class="form-control @error('event_end_date') is-invalid @enderror" required>
                </div>
                @error('event_start_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                @error('event_end_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Special Requirements</label>
            <textarea name="special_requirements" class="form-control @error('special_requirements') is-invalid @enderror" rows="3">{{ old('special_requirements') }}</textarea>
            @error('special_requirements')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-primary">Submit Booking</button>
        <a href="{{ route('vendor.events') }}" class="btn btn-secondary">Back to Events</a>
    </form>
</div>
@endsection
