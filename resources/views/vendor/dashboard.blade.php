@extends('layouts.vendor')

@section('title', 'Vendor Dashboard - EventHub')

@section('styles')
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 2rem;
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
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="vendor-pattern" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23vendor-pattern)"/></svg>');
        opacity: 0.3;
    }
    
    .dashboard-header > * {
        position: relative;
        z-index: 2;
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
        margin-bottom: 2rem;
    }
    
    .quick-actions-card .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: none;
        padding: 1.5rem;
    }
    
    .action-btn {
        display: block;
        padding: 1rem;
        text-decoration: none;
        border-radius: 12px;
        transition: all 0.3s ease;
        text-align: center;
        font-weight: 600;
        border: none;
        width: 100%;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    
    .action-btn.btn-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
    }
    
    .action-btn.btn-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        color: white;
    }
    
    .action-btn.btn-success {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        color: white;
    }
    
    .action-btn.btn-warning {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        color: #212529;
    }
    
    @media (max-width: 768px) {
        .dashboard-header {
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .stats-card .stats-number {
            font-size: 2rem;
        }
        
        .action-btn {
            margin-bottom: 0.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="dashboard-header text-center">
        <div class="mb-3">
            <span class="badge bg-light text-dark px-3 py-2 fs-6 fw-semibold">
                <i class="fas fa-store me-2"></i>VENDOR DASHBOARD
            </span>
        </div>
        <h1 class="display-5 fw-bold mb-3">
            <i class="fas fa-store me-3"></i>Welcome back, {{ $vendor->business_name }}!
        </h1>
        <p class="lead mb-0 opacity-90">
            <i class="fas fa-store me-2"></i>{{ ucfirst(str_replace('_', ' ', $vendor->business_type)) }} • 
            <span class="badge bg-{{ $vendor->status === 'approved' ? 'success' : ($vendor->status === 'pending' ? 'warning' : 'danger') }}">
                {{ ucfirst($vendor->status) }}
            </span>
        </p>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <a href="{{ route('vendor.applications') }}" class="text-decoration-none">
                <div class="card stats-card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stats-number">{{ $vendor->eventApplications()->where('status', '!=', 'paid')->count() }}</div>
                                <div class="stats-label">My Applications</div>
                            </div>
                            <div class="stats-icon">
                                <i class="fas fa-file-alt fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <a href="{{ route('vendor.bookings') }}" class="text-decoration-none">
                <div class="card stats-card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stats-number">{{ $vendor->eventApplications()->where('status', 'paid')->count() }}</div>
                                <div class="stats-label">Confirmed Bookings</div>
                            </div>
                            <div class="stats-icon">
                                <i class="fas fa-calendar-check fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <a href="{{ route('vendor.events') }}" class="text-decoration-none">
                <div class="card stats-card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stats-number">{{ \App\Models\Event::where('status', 'active')->count() }}</div>
                                <div class="stats-label">Available Events</div>
                            </div>
                            <div class="stats-icon">
                                <i class="fas fa-calendar fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card stats-card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-number">{{ $vendor->eventApplications()->where('status', 'approved')->count() }}</div>
                            <div class="stats-label">Approved Applications</div>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-check-circle fa-2x"></i>
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
                        <i class="fas fa-bolt me-2 text-warning"></i>Vendor Quick Actions
                    </h5>
                    <p class="text-muted mb-0 small">Access frequently used vendor functions to manage your business</p>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('vendor.events') }}" class="action-btn btn-primary">
                                <i class="fas fa-search me-2"></i>Browse Events
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('vendor.applications') }}" class="action-btn btn-info">
                                <i class="fas fa-file-alt me-2"></i>My Applications
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('vendor.bookings') }}" class="action-btn btn-success">
                                <i class="fas fa-calendar-check me-2"></i>My Bookings
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('profile.show') }}" class="action-btn btn-warning">
                                <i class="fas fa-user-cog me-2"></i>Profile Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommended Events & Status -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-star me-2"></i>Recommended Events
                    </h5>
                </div>
                <div class="card-body">
                    @if($recommendedEvents->count() > 0)
                        <div class="row g-3">
                            @foreach($recommendedEvents as $event)
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0">{{ $event->name }}</h6>
                                                <span class="badge bg-success status-badge">
                                                    <i class="fas fa-check-circle me-1"></i>Available
                                                </span>
                                            </div>
                                            
                                            <p class="text-muted small mb-2">
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ $event->start_time ? $event->start_time->format('M d, Y') : 'TBD' }}
                                            </p>
                                            
                                            @if($event->venue)
                                                <p class="text-muted small mb-2">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    {{ $event->venue->name }}
                                                </p>
                                            @endif
                                            
                                            @if($event->description)
                                                <p class="text-muted small mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                    {{ Str::limit($event->description, 100) }}
                                                </p>
                                            @endif
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $event->created_at->diffForHumans() }}
                                                </small>
                                                <a href="{{ route('vendor.events.show', $event->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i>View
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="{{ route('vendor.events') }}" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>View All Events
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Recommended Events</h5>
                            <p class="text-muted">You've applied to all available events or there are no active events at the moment.</p>
                            <a href="{{ route('vendor.events') }}" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Browse All Events
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Account Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Application Status:</strong>
                        <span class="badge bg-{{ $vendor->status === 'approved' ? 'success' : ($vendor->status === 'pending' ? 'warning' : 'danger') }} ms-2">
                            {{ ucfirst($vendor->status) }}
                        </span>
                    </div>
                    
                    @if($vendor->status === 'pending')
                        <div class="alert alert-warning">
                            <i class="fas fa-clock me-2"></i>
                            Your application is under review. We'll notify you once it's processed.
                        </div>
                    @elseif($vendor->status === 'approved')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            Congratulations! Your vendor account is approved and active.
                        </div>
                    @elseif($vendor->status === 'rejected')
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle me-2"></i>
                            Your application was rejected. Please contact support for more information.
                        </div>
                    @endif
                    
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            Member since: {{ $vendor->created_at->format('M d, Y') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.btn-lg {
    padding: 12px 24px;
    font-weight: 600;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.btn-lg:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.card {
    border-radius: 15px;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px 15px 0 0 !important;
    border-bottom: 1px solid #dee2e6;
}

.alert {
    border-radius: 10px;
    border: none;
}

@media (max-width: 768px) {
    .btn-lg {
        padding: 10px 20px;
        font-size: 0.9rem;
    }
    
    .d-flex.gap-3 {
        flex-direction: column;
    }
    
    .d-flex.gap-3 .btn {
        width: 100%;
    }
}
</style>
@endsection


