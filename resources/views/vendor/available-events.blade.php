@extends('layouts.vendor')

@section('title', 'Available Events - EventHub')
@section('page-title', 'Available Events')
@section('page-description', 'Browse and apply to available events')

@push('styles')
<style>
/* 整體頁面樣式 */
body {
    background-color: #f8f9fa;
}

/* 頁面標題區域 */
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 3rem 0;
    margin-bottom: 3rem;
    border-radius: 0 0 30px 30px;
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.page-header .container {
    position: relative;
    z-index: 1;
}

.page-header h1 {
    font-weight: 800;
    margin-bottom: 0.5rem;
    font-size: 2.5rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.page-header p {
    opacity: 0.95;
    margin-bottom: 0;
    font-size: 1.1rem;
    font-weight: 300;
}

/* 事件卡片樣式 */
.event-card {
    transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
    border-radius: 20px;
    overflow: hidden;
    border: none;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    background: white;
    position: relative;
}

.event-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.event-card:hover::before {
    opacity: 1;
}

.event-card:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

/* 卡片頭部圖片 */
.event-card .card-img-top {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    transition: all 0.4s ease;
    height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.event-card .card-img-top::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: shimmer 3s infinite;
}

@keyframes shimmer {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.event-card:hover .card-img-top {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 50%, #f093fb 100%);
}

/* 可用性徽章 */
.availability-badge {
    font-size: 0.75em;
    padding: 0.5em 1em;
    border-radius: 25px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

/* 事件元信息 */
.event-meta {
    margin: 1rem 0;
}

.event-meta .d-flex {
    margin-bottom: 0.5rem;
    padding: 0.25rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.event-meta .d-flex:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.event-meta i {
    width: 20px;
    text-align: center;
    font-size: 1.1em;
}

.event-meta .text-muted {
    font-weight: 500;
    color: #6c757d !important;
}

/* 價格卡片 */
.pricing-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 15px;
    padding: 1rem;
    border: 1px solid #e9ecef;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    position: relative;
    overflow: hidden;
}

.pricing-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(180deg, #667eea, #764ba2);
}

.pricing-card h6 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

/* 統計行 */
.stats-row {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 15px;
    padding: 0.75rem;
    margin: 0.75rem 0;
    border: 1px solid #e9ecef;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.stats-row .fw-bold {
    font-size: 1.2rem;
    font-weight: 700;
}

/* 按鈕樣式 */
.event-card .btn {
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    border-radius: 30px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.85rem;
    padding: 0.75rem 1.5rem;
}

.event-card .btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.event-card .btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

.event-card .btn-outline-info {
    border: 2px solid #17a2b8;
    color: #17a2b8;
    background: transparent;
}

.event-card .btn-outline-info:hover {
    background: #17a2b8;
    border-color: #17a2b8;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(23, 162, 184, 0.3);
}

/* 申請狀態提示 */
.alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    border: 1px solid #bee5eb;
    border-radius: 10px;
    font-weight: 500;
}

/* 空狀態 */
.text-center.py-5 {
    padding: 4rem 0 !important;
}

.text-center.py-5 i {
    color: #dee2e6;
    margin-bottom: 1.5rem;
}

/* 分頁樣式 */
.pagination {
    justify-content: center;
    margin-top: 3rem;
}

.pagination .page-link {
    border-radius: 10px;
    margin: 0 2px;
    border: 1px solid #dee2e6;
    color: #667eea;
    font-weight: 500;
    transition: all 0.3s ease;
}

.pagination .page-link:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: #667eea;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

/* 響應式設計 */
@media (max-width: 768px) {
    .page-header h1 {
        font-size: 2rem;
    }
    
    .event-card:hover {
        transform: translateY(-8px) scale(1.01);
    }
    
    .pricing-card {
        padding: 1rem;
    }
    
    .stats-row {
        padding: 0.75rem;
    }
}

/* 加載動畫 */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.event-card {
    animation: fadeInUp 0.6s ease-out;
}

.event-card:nth-child(1) { animation-delay: 0.1s; }
.event-card:nth-child(2) { animation-delay: 0.2s; }
.event-card:nth-child(3) { animation-delay: 0.3s; }
.event-card:nth-child(4) { animation-delay: 0.4s; }
.event-card:nth-child(5) { animation-delay: 0.5s; }
.event-card:nth-child(6) { animation-delay: 0.6s; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
<div class="container">
            <div class="row">
                <div class="col-12 text-center">
        <h1>Available Events</h1>
                    <p>Discover events where you can showcase your business</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Events Grid -->
    <div class="container">

    @if($events->count() > 0)
        <div class="row" id="events-container">
            @foreach($events as $event)
                @php
                    // Get all applications for this event (excluding cancelled applications)
                    $existingApplications = collect();
                    if (isset($vendor) && $vendor) {
                        $existingApplications = $vendor->eventApplications()
                            ->where('event_id', $event->id)
                            ->where('status', '!=', 'cancelled')
                            ->get();
                    }
                    $hasApplications = $existingApplications->count() > 0;
                    
                    // Removed calculations - not needed
                @endphp
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100 event-card" data-event-id="{{ $event->id }}">
                        <!-- Event Image Header -->
                        <div class="card-img-top text-white d-flex align-items-center justify-content-center position-relative">
                            <div class="text-center">
                                <i class="fas fa-calendar-alt fa-3x mb-2"></i>
                                <h6 class="mb-0">Event</h6>
                            </div>
                            <!-- Status Badge -->
                            <div class="position-absolute top-0 end-0 m-3">
                                @if($event->hasAvailableSlots())
                                    <span class="badge bg-success availability-badge">
                                        <i class="fas fa-check-circle me-1"></i>Available
                                    </span>
                                @else
                                    <span class="badge bg-warning availability-badge">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Fully Booked
                                </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column p-3">
                            <div class="flex-grow-1">
                                <!-- Event Title -->
                                <h5 class="card-title fw-bold text-dark mb-3" style="font-size: 1.1rem; line-height: 1.3; min-height: 2.2rem;">
                                    {{ Str::limit($event->name, 45) }}
                                </h5>
                                
                                <!-- Event Meta Information -->
                                <div class="event-meta">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar text-primary me-3"></i>
                                        <div>
                                            <div class="fw-semibold text-dark small">
                                                @if($event->start_time)
                                                    {{ $event->start_time->format('M d, Y') }}
                                @if($event->end_time && $event->start_time->format('M d') !== $event->end_time->format('M d'))
                                    - {{ $event->end_time->format('M d, Y') }}
                                                    @endif
                                                @else
                                                    Date TBA
                                @endif
                            </div>
                                            @if($event->start_time)
                                            <div class="text-muted small">
                                                {{ $event->start_time->format('g:i A') }}
                                @if($event->end_time)
                                                    - {{ $event->end_time->format('g:i A') }}
                                @endif
                            </div>
                                @endif
                            </div>
                            </div>
                            
                                    @if($event->venue)
                                    <div class="d-flex align-items-center mt-2">
                                        <i class="fas fa-map-marker-alt text-warning me-3"></i>
                                        <div>
                                            <div class="fw-semibold text-dark small">{{ Str::limit($event->venue->name, 35) }}</div>
                                            @if($event->venue->location)
                                            <div class="text-muted small">{{ Str::limit($event->venue->location, 40) }}</div>
                                            @endif
                                        </div>
                                </div>
                            @endif
                            
                                    @if($event->organizer)
                                    <div class="d-flex align-items-center mt-2">
                                        <i class="fas fa-user-tie text-info me-3"></i>
                                        <div>
                                            <div class="fw-semibold text-dark small">Organizer</div>
                                            <div class="text-muted small">{{ Str::limit($event->organizer, 30) }}</div>
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                <!-- Pricing Information -->
                                <div class="pricing-card mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-primary mb-0">RM {{ number_format($event->booth_price ?? 0, 2) }}</h6>
                                            <small class="text-muted">Per booth</small>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted">
                                                <i class="fas fa-hashtag me-1"></i>
                                                {{ $event->available_booths }}/{{ $event->booth_quantity }} booths
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Event Stats -->
                                <div class="stats-row">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="small text-muted">Total Capacity</div>
                                            <div class="fw-bold text-info">{{ $event->booth_quantity }}</div>
                            </div>
                                <div class="col-6">
                                            <div class="small text-muted">Available</div>
                                            <div class="fw-bold text-success">{{ $event->available_booths }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Application Status -->
                                @if($hasApplications)
                                <div class="alert alert-info py-2 px-3 mb-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <small>You have {{ $existingApplications->count() }} application(s) for this event</small>
                                </div>
                                @endif
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="mt-auto pt-2">
                                <div class="d-grid gap-2">
                                    @if($event->status === 'active' && $event->hasAvailableSlots())
                                        <a href="{{ route('vendor.events.apply', $event->id) }}" class="btn btn-primary">
                                            <i class="fas fa-paper-plane me-2"></i>Apply Now
                                        </a>
                                    @elseif($event->status === 'active' && !$event->hasAvailableSlots())
                                        <button class="btn btn-warning" disabled>
                                            <i class="fas fa-exclamation-triangle me-2"></i>Fully Booked
                                        </button>
                                @else
                                        <button class="btn btn-secondary" disabled>
                                            <i class="fas fa-times me-2"></i>Not Available
                                    </button>
                                @endif
                                
                                    <a href="{{ route('vendor.events.show', $event->id) }}" class="btn btn-outline-info">
                                        <i class="fas fa-info-circle me-2"></i>View Details
                                </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
            <h4 class="text-muted">No Available Events</h4>
            <p class="text-muted">There are currently no events available for application.</p>
        </div>
    @endif

        <!-- Pagination -->
        @if($events->hasPages())
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $events->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Add smooth scrolling for pagination
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll to top when pagination is clicked
    const paginationLinks = document.querySelectorAll('.pagination a');
    paginationLinks.forEach(link => {
        link.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
    
    // Add loading state for apply buttons
    const applyButtons = document.querySelectorAll('a[href*="apply"]');
    applyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Loading...';
            this.classList.add('disabled');
            
            // Re-enable after 3 seconds if still on page
            setTimeout(() => {
                this.innerHTML = originalText;
                this.classList.remove('disabled');
            }, 3000);
        });
    });
});
</script>
@endpush
