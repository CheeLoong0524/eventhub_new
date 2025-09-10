<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Vendor - EventHub')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @yield('styles')
    <style>
        body { min-height: 100vh; display: flex; flex-direction: column; }
        .content-container { flex: 1; }
        .page-header { background: #fff; border-bottom: 1px solid #eee; }
        .page-header .container { padding-top: 1rem; padding-bottom: 1rem; }
        .breadcrumb { margin-bottom: 0; }
        .navbar .navbar-brand i { line-height: 1; }
        
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        
        .timeline-marker {
            position: absolute;
            left: -30px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 0 0 3px #e9ecef;
        }
        
        .timeline-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: -24px;
            top: 17px;
            width: 2px;
            height: calc(100% + 3px);
            background: #e9ecef;
        }
        
        .timeline-content {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 3px solid #007bff;
        }
        
        .vendor-content {
            padding: 2rem;
        }
        
        @media (max-width: 768px) {
            .vendor-content {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
        <a class="navbar-brand text-white fw-semibold d-flex align-items-center fs-4" href="{{ route('vendor.dashboard') }}">
            <i class="fas fa-calendar-alt me-2"></i>
            <span>EventHub</span>
            <span class="badge bg-warning text-dark ms-2 px-2 py-1 fs-6">VENDOR</span>
        </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#vendorTopNav" aria-controls="vendorTopNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="vendorTopNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item"><a class="nav-link text-white {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}" href="{{ route('vendor.dashboard') }}"><i class="fas fa-tachometer-alt"></i><span class="ms-2">Dashboard</span></a></li>
                    <li class="nav-item"><a class="nav-link text-white {{ request()->routeIs('vendor.events') ? 'active' : '' }}" href="{{ route('vendor.events') }}"><i class="fas fa-calendar"></i><span class="ms-2">Events</span></a></li>
                    <li class="nav-item"><a class="nav-link text-white {{ request()->routeIs('vendor.applications*') ? 'active' : '' }}" href="{{ route('vendor.applications') }}"><i class="fas fa-file-alt"></i><span class="ms-2">Applications</span></a></li>
                    <li class="nav-item"><a class="nav-link text-white {{ request()->routeIs('vendor.bookings*') ? 'active' : '' }}" href="{{ route('vendor.bookings') }}"><i class="fas fa-calendar-check"></i><span class="ms-2">Bookings</span></a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user me-2"></i>{{ Auth::user()->name ?? 'Account' }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="fas fa-user-circle me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('auth.logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="content-container">
        <div class="vendor-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="text-white py-4 mt-auto" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center gap-3">
                <h6 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>EventHub</h6>
                <div class="text-end">
                    <p class="mb-0">&copy; 2025 EventHub. All rights reserved.</p>
                    <div class="mt-2">
                        <a href="#" class="text-white-50 me-3"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-white-50 me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white-50 me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white-50"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
