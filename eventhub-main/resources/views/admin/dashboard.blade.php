@extends('layouts.app')

@section('title', 'Admin Dashboard - EventHub')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
            <div class="position-sticky pt-3">
                <div class="text-center mb-4">
                    <i class="fas fa-shield-alt text-white" style="font-size: 2rem;"></i>
                    <h5 class="text-white mt-2">Admin Panel</h5>
                </div>
                
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active text-white" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('admin.users.index') }}">
                            <i class="fas fa-users me-2"></i>
                            User Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('home') }}">
                            <i class="fas fa-home me-2"></i>
                            Back to Main Site
                        </a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="nav-link text-white border-0 bg-transparent">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <span class="btn btn-outline-secondary">
                            <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                        </span>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Welcome Card -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-users me-2"></i>Total Users
                            </h5>
                            <p class="card-text display-6">{{ \App\Models\User::count() }}</p>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light">
                                <i class="fas fa-eye me-1"></i>View Users
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-user-check me-2"></i>Active Users
                            </h5>
                            <p class="card-text display-6">{{ \App\Models\User::where('is_active', true)->count() }}</p>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light">
                                <i class="fas fa-eye me-1"></i>Manage Users
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-bolt me-2"></i>Quick Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary w-100 mb-2">
                                        <i class="fas fa-users me-2"></i>Manage Users
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('home') }}" class="btn btn-outline-secondary w-100 mb-2">
                                        <i class="fas fa-home me-2"></i>View Main Site
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger w-100 mb-2">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
.sidebar {
    min-height: 100vh;
    box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
}

.sidebar .nav-link {
    font-weight: 500;
    color: #333;
    padding: 0.75rem 1rem;
    border-radius: 0.375rem;
    margin: 0.25rem 0;
    transition: all 0.3s ease;
}

.sidebar .nav-link:hover,
.sidebar .nav-link.active {
    background-color: rgba(255, 255, 255, 0.1);
    color: #fff;
}

.sidebar .nav-link button {
    width: 100%;
    text-align: left;
    padding: 0.75rem 1rem;
}

.sidebar .nav-link button:hover {
    background-color: rgba(255, 255, 255, 0.1);
}
</style>
@endsection
