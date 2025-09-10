@extends('layouts.vendor')

@section('title', 'Apply for Event - EventHub')
@section('page-title', 'Apply for Event')
@section('page-description', 'Submit your application to participate in this event')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Apply for Event: {{ $event->name }}</h4>
                </div>
                <div class="card-body">
                    <!-- Event Information -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Event Details</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Event Name:</strong> {{ $event->name }}</p>
                                <p class="mb-1"><strong>Event Type:</strong> General Event</p>
                                <p class="mb-1"><strong>Start Date:</strong> {{ $event->start_time->format('M d, Y') }}</p>
                                <p class="mb-1"><strong>End Date:</strong> {{ $event->end_time->format('M d, Y') }}</p>
                                <p class="mb-1"><strong>Start Time:</strong> {{ $event->start_time->format('H:i') }}</p>
                                <p class="mb-1"><strong>End Time:</strong> {{ $event->end_time->format('H:i') }}</p>
                                <p class="mb-1"><strong>Duration:</strong> {{ $event->start_time->diffForHumans($event->end_time, true) }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Location:</strong> {{ $event->venue->name ?? 'TBD' }}</p>
                                <p class="mb-1"><strong>Venue Type:</strong> {{ $event->venue->type ?? 'N/A' }}</p>
                                <p class="mb-1"><strong>Venue Capacity:</strong> {{ $event->venue->capacity ?? 'N/A' }} people</p>
                                <p class="mb-1"><strong>Organizer:</strong> {{ $event->organizer ?? 'N/A' }}</p>
                                <p class="mb-1"><strong>Event Status:</strong> <span class="badge bg-{{ $event->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($event->status) }}</span></p>
                                @if($event->venue && $event->venue->address)
                                    <p class="mb-1"><strong>Address:</strong> {{ $event->venue->address }}</p>
                                @endif
                            </div>
                        </div>
                        @if($event->description)
                            <hr class="my-3">
                            <div class="row">
                                <div class="col-12">
                                    <p class="mb-1"><strong>Event Description:</strong></p>
                                    <p class="text-muted mb-0">{{ $event->description }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Pricing Information -->
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-dollar-sign me-2"></i>Pricing & Availability</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="pricing-card bg-light p-3 rounded">
                                    <h5 class="text-primary mb-2">RM {{ number_format($event->booth_price ?? 0, 2) }}</h5>
                                    <p class="text-muted mb-0">Per booth rental</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="availability-info">
                                    <p class="mb-1"><strong>Available Booths:</strong> 
                                        <span class="text-success">{{ $event->available_booths }}/{{ $event->booth_quantity }}</span>
                                    </p>
                                    <p class="mb-1"><strong>Booths Sold:</strong> 
                                        <span class="text-info">{{ $event->booth_sold }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('vendor.events.apply', $event->id) }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Booth Size <span class="text-danger">*</span></label>
                                    <select name="booth_size" id="booth_size" class="form-select @error('booth_size') is-invalid @enderror" required>
                                        <option value="">Select booth size</option>
                                        <option value="10x10" {{ old('booth_size') == '10x10' ? 'selected' : '' }} data-multiplier="1">10x10 ft</option>
                                        <option value="20x20" {{ old('booth_size') == '20x20' ? 'selected' : '' }} data-multiplier="2">20x20 ft</option>
                                        <option value="30x30" {{ old('booth_size') == '30x30' ? 'selected' : '' }} data-multiplier="3">30x30 ft</option>
                                    </select>
                                    @error('booth_size')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Booth Quantity <span class="text-danger">*</span></label>
                                    <input type="number" name="booth_quantity" class="form-control @error('booth_quantity') is-invalid @enderror" 
                                           value="{{ old('booth_quantity', 1) }}" min="1" max="{{ $event->available_booths }}" required>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Maximum {{ $event->available_booths }} booths available
                                    </small>
                                    @error('booth_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Service Type <span class="text-danger">*</span></label>
                                    <select name="service_type" class="form-select @error('service_type') is-invalid @enderror" required>
                                        <option value="">Select service type</option>
                                        <option value="food" {{ old('service_type') == 'food' ? 'selected' : '' }}>Food & Beverage</option>
                                        <option value="equipment" {{ old('service_type') == 'equipment' ? 'selected' : '' }}>Equipment Rental</option>
                                        <option value="decoration" {{ old('service_type') == 'decoration' ? 'selected' : '' }}>Decoration & Design</option>
                                        <option value="entertainment" {{ old('service_type') == 'entertainment' ? 'selected' : '' }}>Entertainment</option>
                                        <option value="logistics" {{ old('service_type') == 'logistics' ? 'selected' : '' }}>Logistics & Transportation</option>
                                        <option value="other" {{ old('service_type') == 'other' ? 'selected' : '' }}>Other Services</option>
                                    </select>
                                    @error('service_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Calculated Price (RM)</label>
                                    <input type="number" name="requested_price" id="calculated_price" class="form-control" 
                                           value="{{ old('requested_price', $event->booth_price) }}" step="0.01" min="0" readonly>
                                    <small class="text-muted">Price calculated based on booth size and base price (RM {{ number_format($event->booth_price, 2) }})</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Price Breakdown</label>
                                    <div class="price-breakdown bg-light p-3 rounded">
                                        <div class="d-flex justify-content-between">
                                            <span>Base Price (10x10):</span>
                                            <span>RM {{ number_format($event->booth_price, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Size Multiplier:</span>
                                            <span id="multiplier_text">1x</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Unit Price:</span>
                                            <span id="unit_price">RM {{ number_format($event->booth_price, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Quantity:</span>
                                            <span id="quantity_text">1</span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Total Price:</span>
                                            <span id="total_price">RM {{ number_format($event->booth_price, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Service Description <span class="text-danger">*</span></label>
                            <textarea name="service_description" class="form-control @error('service_description') is-invalid @enderror" 
                                      rows="4" required placeholder="Describe the services you will provide at this event...">{{ old('service_description') }}</textarea>
                            @error('service_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Submit Application
                            </button>
                            <a href="{{ route('vendor.events') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Events
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const boothSizeSelect = document.getElementById('booth_size');
    const boothQuantityInput = document.querySelector('input[name="booth_quantity"]');
    const calculatedPriceInput = document.getElementById('calculated_price');
    const multiplierText = document.getElementById('multiplier_text');
    const unitPriceSpan = document.getElementById('unit_price');
    const quantityText = document.getElementById('quantity_text');
    const totalPriceSpan = document.getElementById('total_price');
    
    const basePrice = {{ $event->booth_price }};
    
    function updatePrice() {
        const selectedOption = boothSizeSelect.options[boothSizeSelect.selectedIndex];
        const multiplier = selectedOption ? parseFloat(selectedOption.dataset.multiplier) : 1;
        const quantity = parseInt(boothQuantityInput.value) || 1;
        const unitPrice = basePrice * multiplier;
        const totalPrice = unitPrice * quantity;
        
        calculatedPriceInput.value = totalPrice.toFixed(2);
        multiplierText.textContent = multiplier + 'x';
        unitPriceSpan.textContent = 'RM ' + unitPrice.toFixed(2);
        quantityText.textContent = quantity;
        totalPriceSpan.textContent = 'RM ' + totalPrice.toFixed(2);
    }
    
    boothSizeSelect.addEventListener('change', updatePrice);
    boothQuantityInput.addEventListener('input', updatePrice);
    
    // Initialize price on page load
    updatePrice();
});
</script>
@endsection

@section('styles')
<style>
.alert {
    border-left: 4px solid #0d6efd;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.form-label {
    font-weight: 600;
    color: #495057;
}

.btn {
    border-radius: 0.375rem;
}
</style>
@endsection
