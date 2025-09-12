@extends('layouts.app')

@section('title', 'Admin Dashboard - EventHub')

@section('styles')
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="admin-pattern" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23admin-pattern)"/></svg>');
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
    
    .recent-users-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 1rem;
    }
    
    .recent-users-card .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: none;
        padding: 1.5rem;
    }
    
    .table {
        margin-bottom: 0;
    }
    
    .table th {
        border: none;
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
    }
    
    .table td {
        border: none;
        vertical-align: middle;
        padding: 1rem 0.75rem;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
        transition: background-color 0.3s ease;
    }
    
    .badge {
        padding: 0.5rem 0.75rem;
        border-radius: 20px;
        font-weight: 500;
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
            <i class="fas fa-tachometer-alt me-3"></i>Admin Dashboard
        </h1>
        <p class="lead mb-0 opacity-90">Manage your EventHub platform with powerful tools and insights</p>
    </div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card stats-card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-number">{{ $totalUsers }}</div>
                        <div class="stats-label">Total Users</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-users fa-2x"></i>
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
                        <div class="stats-number">{{ $totalCustomers }}</div>
                        <div class="stats-label">Customers</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-user fa-2x"></i>
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
                        <div class="stats-number">{{ $totalVendors }}</div>
                        <div class="stats-label">Vendors</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-store fa-2x"></i>
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
                        <div class="stats-label">Events</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-calendar fa-2x"></i>
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
                <p class="text-muted mb-0 small">Access frequently used administrative functions</p>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('admin.users.create') }}" class="action-btn btn-primary">
                            <i class="fas fa-user-plus me-2"></i>Add User
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('admin.users.index') }}" class="action-btn btn-info">
                            <i class="fas fa-users me-2"></i>Manage Users
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="#" class="action-btn btn-success">
                            <i class="fas fa-calendar-plus me-2"></i>Create Event
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="#" class="action-btn btn-warning">
                            <i class="fas fa-chart-bar me-2"></i>Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Users -->
<div class="row">
    <div class="col-12">
        <div class="card recent-users-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-clock me-2 text-info"></i>Recent Users
                    </h5>
                    <p class="text-muted mb-0 small">Latest registered users on the platform</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-external-link-alt me-1"></i>View All
                </a>
            </div>
            <div class="card-body">
                @if($recentUsers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentUsers as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-3">
                                                    <i class="fas fa-user-circle fa-2x text-muted"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $user->name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $user->email }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'vendor' ? 'warning' : 'info') }}">
                                                <i class="fas fa-{{ $user->role === 'admin' ? 'shield-alt' : ($user->role === 'vendor' ? 'store' : 'user') }} me-1"></i>
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                                <i class="fas fa-{{ $user->is_active ? 'check-circle' : 'times-circle' }} me-1"></i>
                                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ $user->created_at->format('M j, Y') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No users found.</p>
                        <small class="text-muted">Users will appear here once they register.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>
@endsection 