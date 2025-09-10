<div class="col-md-6 col-lg-4 mb-4">
    <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0">
                    @if($record->type === 'booking')
                        Booking #{{ $record->id }}
                    @else
                        Application #{{ $record->id }}
                    @endif
                </h6>
                <small class="text-muted">
                    @if($record->type === 'booking')
                        <i class="fas fa-calendar-check me-1"></i>Direct Booking
                    @else
                        <i class="fas fa-file-alt me-1"></i>Event Application
                    @endif
                </small>
            </div>
            <span class="badge bg-{{ $record->status_badge_color }}">{{ ucfirst(str_replace('_', ' ', $record->status)) }}</span>
        </div>
        <div class="card-body">
            <div class="mb-2">
                <strong>Event:</strong> {{ $record->event_name }}
            </div>
            <div class="mb-2">
                <strong>Booth:</strong> {{ $record->booth_number }} ({{ $record->booth_type }})
            </div>
            <div class="mb-2">
                <strong>Size:</strong> {{ $record->booth_size }} 
                @if($record->type === 'booking')
                    sq ft
                @else
                    {{ $record->booth_size !== 'N/A' ? 'sq ft' : '' }}
                @endif
            </div>
            <div class="mb-2">
                <strong>Amount:</strong> RM {{ number_format($record->amount, 2) }}
            </div>
            <div class="mb-2">
                <strong>Event Date:</strong> 
                {{ $record->event_start_date ? $record->event_start_date->format('M d, Y') : 'N/A' }}
            </div>
            @if($record->special_requirements)
                <div class="mb-2">
                    <strong>Special Requirements:</strong>
                    <small class="text-muted d-block">{{ Str::limit($record->special_requirements, 50) }}</small>
                </div>
            @endif
        </div>
        <div class="card-footer">
            <div class="d-flex gap-2">
                @if($record->type === 'booking')
                    <a href="{{ route('vendor.bookings.show', $record->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye me-1"></i>View
                    </a>
                @else
                    <a href="{{ route('vendor.applications.show', $record->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye me-1"></i>View
                    </a>
                @endif
                
                @if($record->can_be_paid)
                    @if($record->type === 'booking')
                        <a href="{{ route('vendor.bookings.payment', $record->id) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-credit-card me-1"></i>Pay
                        </a>
                    @else
                        <a href="{{ route('vendor.applications.payment', $record->id) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-credit-card me-1"></i>Pay
                        </a>
                    @endif
                @endif
                
                @if($record->can_be_cancelled)
                    <button type="button" class="btn btn-sm btn-outline-danger" 
                            data-bs-toggle="modal" data-bs-target="#cancelModal{{ $record->type }}{{ $record->id }}">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
