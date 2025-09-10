@extends('layouts.app')

@section('title', 'Vendor Dashboard - EventHub')

@section('styles')
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="vendor-pattern" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23vendor-pattern)"/></svg>');
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
        background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
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
    
    .activity-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 1rem;
    }
    
    .activity-card .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: none;
        padding: 1.5rem;
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
            <i class="fas fa-store me-3"></i>Vendor Dashboard
        </h1>
        <p class="lead mb-0 opacity-90">Manage your vendor activities and grow your business</p>
    </div>

<!-- Welcome Message -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card welcome-card">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="card-title fw-bold mb-2">
                            <i class="fas fa-handshake me-2"></i>Welcome back, {{ $user->name }}!
                        </h5>
                        <p class="card-text mb-0 opacity-90">
                            As a vendor, you can apply for events, manage your booth bookings, and connect with event organizers. 
                            Start exploring opportunities to grow your business!
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <i class="fas fa-store fa-4x opacity-75"></i>
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
                        <div class="stats-number">0</div>
                        <div class="stats-label">Applied Events</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-calendar-check fa-2x"></i>
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
                        <div class="stats-number">0</div>
                        <div class="stats-label">Approved Events</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-check-circle fa-2x"></i>
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
                        <div class="stats-number">0</div>
                        <div class="stats-label">Pending Events</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-clock fa-2x"></i>
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
                        <div class="stats-number">$0</div>
                        <div class="stats-label">Total Revenue</div>
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
                <p class="text-muted mb-0 small">Access frequently used vendor functions</p>
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
                            <i class="fas fa-plus me-2"></i>Apply for Event
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="#" class="action-btn btn-info">
                            <i class="fas fa-list me-2"></i>My Applications
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="#" class="action-btn btn-warning">
                            <i class="fas fa-chart-line me-2"></i>Analytics
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-12">
        <div class="card activity-card">
            <div class="card-header">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-history me-2 text-info"></i>Recent Activity
                </h5>
                <p class="text-muted mb-0 small">Track your latest vendor activities and applications</p>
            </div>
            <div class="card-body">
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h6 class="mb-2">No Recent Activity</h6>
                    <p class="mb-2">Start by browsing available events and applying for them!</p>
                    <a href="#" class="btn btn-primary btn-sm">
                        <i class="fas fa-search me-1"></i>Browse Events
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection 