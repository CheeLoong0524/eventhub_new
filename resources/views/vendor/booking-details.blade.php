@extends('layouts.app')

@section('title', 'Booking Details')

@section('content')
<div class="container">
    <h3 class="mb-4">Booking Details</h3>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Booking ID:</strong> {{ $booking->id }}</p>
                    <p class="mb-1"><strong>Status:</strong> <span class="badge bg-{{ $booking->status_badge_color }}">{{ ucfirst($booking->status) }}</span></p>
                    <p class="mb-1"><strong>Payment Status:</strong> <span class="badge bg-{{ $booking->payment_status_badge_color }}">{{ ucfirst($booking->payment_status) }}</span></p>
                    <p class="mb-1"><strong>Booth Number:</strong> {{ $booking->booth_number }}</p>
                    <p class="mb-1"><strong>Booth Type:</strong> {{ $booking->booth_type }}</p>
                    <p class="mb-1"><strong>Booth Size:</strong> {{ $booking->booth_size }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Price (MYR):</strong> {{ number_format($booking->price, 2) }}</p>
                    <p class="mb-1"><strong>Total Amount (MYR):</strong> {{ number_format($booking->total_amount, 2) }}</p>
                    <p class="mb-1"><strong>Deposit (MYR):</strong> {{ number_format($booking->deposit_amount, 2) }}</p>
                    <p class="mb-1"><strong>Booking Date:</strong> {{ optional($booking->booking_date)->format('Y-m-d H:i') }}</p>
                    <p class="mb-1"><strong>Event Start:</strong> {{ optional($booking->event_start_date)->format('Y-m-d H:i') }}</p>
                    <p class="mb-1"><strong>Event End:</strong> {{ optional($booking->event_end_date)->format('Y-m-d H:i') }}</p>
                </div>
            </div>
            @if($booking->special_requirements)
                <hr>
                <p class="mb-0"><strong>Special Requirements:</strong><br>{{ $booking->special_requirements }}</p>
            @endif
        </div>
    </div>

    <div class="d-flex gap-2">
        @if(!$booking->isCancelled())
            <form method="POST" action="{{ route('vendor.bookings.cancel', $booking->id) }}">
                @csrf
                <input type="hidden" name="cancellation_reason" value="Vendor requested cancellation">
                <button type="submit" class="btn btn-danger">Cancel Booking</button>
            </form>
        @endif

        <a href="{{ route('vendor.bookings') }}" class="btn btn-secondary">Back to My Bookings</a>
        <a href="{{ route('vendor.bookings.payment', $booking->id) }}" class="btn btn-primary">Go to Payment</a>
    </div>
</div>
@endsection
