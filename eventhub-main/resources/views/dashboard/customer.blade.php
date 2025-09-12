@extends('layouts.app')

@section('title', 'Customer Dashboard - EventHub')

@section('styles')
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
        color: white;
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        margin-left: 1rem;
        margin-right: 1rem;
        position: relative;
        overflow: hidden;
    }
    
    .dashboard-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="customer-pattern" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23customer-pattern)"/></svg>');
        opacity: 0.3;
    }
    
    .dashboard-header > * {
        position: relative;
        z-index: 2;
    }
    
    .welcome-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        overflow: hidden;
        background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);
        color: white;
    }
    
    .stats-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        overflow: hidden;
        margin-bottom: 1rem;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    }
    
    .stats-card .card-body {
        padding: 1.5rem;
    }
    
    .stats-card .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
    }
    
    .stats-card .stats-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .stats-card .stats-label {
        font-size: 0.9rem;
        opacity: 0.9;
        font-weight: 500;
    }
    
    .quick-actions-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 1rem;
    }
    
    .quick-actions-card .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: none;
        padding: 1.5rem;
    }
    
    .action-btn {
        border-radius: 12px;
        padding: 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        text-decoration: none;
        display: block;
        text-align: center;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        text-decoration: none;
    }
    
    .upcoming-events-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 1rem;
    }
    
    .upcoming-events-card .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: none;
        padding: 1.5rem;
    }
    
    /* Timeline Styles */
    .timeline {
        position: relative;
        padding-left: 2rem;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 0.75rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }
    
    .timeline-marker {
        position: absolute;
        left: -2rem;
        top: 0.5rem;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 0 0 3px #e9ecef;
    }
    
    .timeline-content {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-left: 3px solid #28a745;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    
    @media (max-width: 768px) {
        .dashboard-header {
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            margin-left: 0.5rem;
            margin-right: 0.5rem;
        }
        
        .stats-card .stats-number {
            font-size: 2rem;
        }
        
        .action-btn {
            margin-bottom: 0.5rem;
        }
        
        .container-fluid {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="dashboard-header text-center">
        <h1 class="display-5 fw-bold mb-3">
            <i class="fas fa-user me-3"></i>Customer Dashboard
        </h1>
        <p class="lead mb-0 opacity-90">Discover amazing events and manage your bookings</p>
    </div>

<!-- Welcome Message -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card welcome-card">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="card-title fw-bold mb-2">
                            <i class="fas fa-heart me-2"></i>Welcome back, {{ $user->name }}!
                        </h5>
                        <p class="card-text mb-0 opacity-90">
                            Discover amazing events, book tickets, and enjoy unforgettable experiences with EventHub. 
                            Start exploring and create memories that last a lifetime!
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <i class="fas fa-user fa-4x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card stats-card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-number">{{ $totalBookings }}</div>
                        <div class="stats-label">Booked Events</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-ticket-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card stats-card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-number">{{ $attendedEvents ?? 0 }}</div>
                        <div class="stats-label">Attended Events</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card stats-card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-number">{{ $upcomingBookings }}</div>
                        <div class="stats-label">Upcoming Events</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-calendar fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card stats-card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-number">RM {{ number_format($totalSpent ?? 0, 2) }}</div>
                        <div class="stats-label">Total Spent</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-dollar-sign fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card quick-actions-card">
            <div class="card-header">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-bolt me-2 text-warning"></i>Quick Actions
                </h5>
                <p class="text-muted mb-0 small">Access frequently used customer functions</p>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <a href="#" class="action-btn btn-primary">
                            <i class="fas fa-search me-2"></i>Browse Events
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="#" class="action-btn btn-success">
                            <i class="fas fa-ticket-alt me-2"></i>My Tickets
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="#" class="action-btn btn-info">
                            <i class="fas fa-history me-2"></i>Booking History
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="#" class="action-btn btn-warning">
                            <i class="fas fa-star me-2"></i>Favorites
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Featured Events -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card upcoming-events-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-star me-2 text-warning"></i>Featured Events
                    </h5>
                    <p class="text-muted mb-0 small">Discover exciting upcoming events</p>
                </div>
                <a href="#" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-external-link-alt me-1"></i>View All Events
                </a>
            </div>
            <div class="card-body">
                <div class="empty-state">
                    <i class="fas fa-calendar"></i>
                    <h6 class="mb-2">No Featured Events</h6>
                    <p class="mb-2">Check back soon for exciting upcoming events!</p>
                    <a href="#" class="btn btn-primary btn-sm">
                        <i class="fas fa-search me-1"></i>Browse Events
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-12">
        <div class="card upcoming-events-card">
            <div class="card-header">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-history me-2 text-info"></i>Recent Activity
                </h5>
                <p class="text-muted mb-0 small">Track your latest bookings and activities</p>
            </div>
            <div class="card-body">
                @if($recentBookings->count() > 0)
                    <div class="timeline">
                        @foreach($recentBookings as $booking)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $booking->event->name }}</h6>
                                        <p class="text-muted small mb-1">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ $booking->event->getFormattedDateTime() }}
                                        </p>
                                        <p class="text-muted small mb-1">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            {{ $booking->event->venue }}
                                        </p>
                                        <p class="text-muted small mb-0">
                                            <i class="fas fa-ticket-alt me-1"></i>
                                            Order #{{ $booking->order_number }} - {{ $booking->formatted_total }}
                                        </p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-success">Paid</span>
                                        <br>
                                        <small class="text-muted">{{ $booking->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    @if($recentBookings->count() >= 5)
                    <div class="text-center mt-3">
                        <a href="#" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-history me-1"></i>View All Bookings
                        </a>
                    </div>
                    @endif
                @else
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h6 class="mb-2">No Recent Activity</h6>
                        <p class="mb-2">Start by browsing and booking your first event!</p>
                        <a href="{{ route('events.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-search me-1"></i>Browse Events
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>
@endsection 