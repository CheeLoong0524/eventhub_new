<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>@yield('title', 'Admin - EventHub')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    @yield('styles')
    <style>
        /* Navigation Styles */
        .navbar.navbar-dark .navbar-nav .nav-link {
            color: rgba(255,255,255,0.9);
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            margin-right: 0.25rem;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        .navbar.navbar-dark .navbar-nav .nav-link:hover {
            color: #fff;
            background-color: rgba(255,255,255,0.12);
            transform: translateY(-1px);
        }
        .navbar.navbar-dark .navbar-nav .nav-link.active {
            color: #fff;
            background-color: rgba(255,255,255,0.22);
            font-weight: 500;
        }
        
        /* Responsive Navigation */
        @media (max-width: 991.98px) {
            .navbar-nav .nav-link {
                padding: 0.75rem 1rem;
                border-bottom: 1px solid rgba(255,255,255,0.1);
            }
            .navbar-nav .nav-item:last-child .nav-link {
                border-bottom: none;
            }
            .navbar-nav .dropdown-menu {
                position: static !important;
                transform: none !important;
                box-shadow: none;
                background-color: rgba(255,255,255,0.1);
                border: none;
                margin-left: 1rem;
            }
            .navbar-nav .dropdown-item {
                color: rgba(255,255,255,0.8);
                padding: 0.5rem 1rem;
            }
            .navbar-nav .dropdown-item:hover {
                background-color: rgba(255,255,255,0.1);
                color: #fff;
            }
        }
        
        /* Mobile Brand */
        @media (max-width: 575.98px) {
            .navbar-brand span:first-of-type {
                display: none !important;
            }
        }
        
        /* Desktop Navigation Spacing */
        @media (min-width: 992px) {
            .navbar-nav .nav-item {
                margin-right: 0.25rem;
            }
            .navbar-nav .nav-link {
                padding: 0.5rem 0.75rem;
            }
        }
        
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: #f8f9fa;
        }
        
         .main-content {
             margin-left: 0;
             transition: all 0.3s ease;
         }
         
         .main-content.expanded {
             margin-left: 0;
         }
        
        .admin-sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            margin: 0.25rem 0.5rem;
            transition: all 0.3s ease;
        }
        
        .admin-sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        
        .admin-sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.2);
            font-weight: 600;
        }
        
        .admin-sidebar .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
        }
        
        .admin-header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1rem 2rem;
            margin-bottom: 2rem;
        }
        
        .admin-content {
            padding: 2rem;
        }
        
        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 1rem;
        }
        
        .sidebar-brand h4 {
            color: white;
            margin: 0;
            font-weight: 600;
        }
        
        .sidebar-brand small {
            color: rgba(255,255,255,0.7);
        }
        
        .nav-section {
            margin-bottom: 2rem;
        }
        
        .nav-section-title {
            color: rgba(255,255,255,0.6);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 1.5rem;
            margin-bottom: 0.5rem;
        }
        /* Make inner nav scrollable within viewport */
        .admin-sidebar nav.nav.flex-column {
            max-height: calc(100vh - 140px);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .main-content {
            flex: 1;
            background-color: #f8f9fa;
        }
        
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .btn {
            border-radius: 0.5rem;
        }
        
        .table {
            background: white;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        
        .badge {
            border-radius: 0.5rem;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .admin-sidebar {
                min-height: auto;
            }
            
            .admin-content {
                padding: 1rem;
            }
            
            .admin-header {
                padding: 1rem;
            }
            
            /* Mobile Navigation Improvements */
            .navbar-collapse {
                background-color: rgba(0,0,0,0.1);
                border-radius: 0.5rem;
                margin-top: 0.5rem;
                padding: 0.5rem;
            }
            
            .navbar-nav .nav-link {
                font-size: 0.9rem;
            }
            
            .navbar-brand {
                font-size: 1.1rem;
            }
        }
        
        /* Extra Small Devices */
        @media (max-width: 575.98px) {
            .navbar {
                padding: 0.5rem 0;
            }
            
            .navbar-brand {
                font-size: 1rem;
            }
            
            .badge {
                font-size: 0.6rem;
                padding: 0.25rem 0.5rem;
            }
        }
        
        /* Large Desktop Optimization */
        @media (min-width: 1200px) {
            .container-fluid {
                max-width: 1400px;
            }
            
            .navbar-nav .nav-item {
                margin-right: 0.5rem;
            }
        }
        
        /* Pagination Styles */
        .pagination {
            margin: 0;
            gap: 0.25rem;
        }
        .pagination .page-link {
            color: #6c757d;
            border: 1px solid #dee2e6;
            padding: 0.5rem 0.75rem;
            margin: 0;
            border-radius: 0.375rem;
            transition: all 0.3s ease;
            min-width: 2.5rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .pagination .page-link:hover {
            color: #495057;
            background-color: #e9ecef;
            border-color: #adb5bd;
            transform: translateY(-1px);
        }
        .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
            font-weight: 600;
        }
        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #fff;
            border-color: #dee2e6;
            cursor: not-allowed;
        }
        .pagination .page-item.disabled .page-link:hover {
            transform: none;
        }
        
        /* Pagination Info */
        .pagination-info {
            font-size: 0.875rem;
            color: #6c757d;
        }
        
        /* Responsive Pagination */
        @media (max-width: 576px) {
            .pagination .page-link {
                padding: 0.375rem 0.5rem;
                min-width: 2rem;
                font-size: 0.875rem;
            }
        }
    </style>
</head>
<body>
    <!-- Top Navbar (replaces sidebar) -->
    <nav class="navbar navbar-expand-lg navbar-dark py-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container-fluid px-3">
            <!-- Brand -->
            <a class="navbar-brand text-white fw-semibold d-flex align-items-center fs-4" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-calendar-alt me-2"></i>
                <span class="d-none d-sm-inline">EventHub</span>
                <span class="badge bg-danger text-white ms-2 px-2 py-1 fs-6">ADMIN</span>
            </a>
            
            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminTopNav" aria-controls="adminTopNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navigation Menu -->
            <div class="collapse navbar-collapse" id="adminTopNav">
                <!-- Main Navigation - Responsive -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <!-- Home -->
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="fas fa-home"></i>
                            <span class="ms-1 d-none d-lg-inline">Home</span>
                        </a>
                    </li>
                    
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="ms-1 d-none d-lg-inline">Dashboard</span>
                        </a>
                    </li>
                    
                    <!-- Users -->
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                            <i class="fas fa-users"></i>
                            <span class="ms-1 d-none d-lg-inline">Users</span>
                        </a>
                    </li>

                    <!-- Vendors Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white {{ request()->routeIs('admin.vendor.*') ? 'active' : '' }}" href="#" id="vendorsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-store"></i>
                            <span class="ms-1 d-none d-lg-inline">Vendors</span>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="vendorsDropdown">
                            <li><a class="dropdown-item {{ request()->routeIs('admin.vendor.applications*') ? 'active' : '' }}" href="{{ route('admin.vendor.applications') }}"><i class="fas fa-file-signature me-2"></i>Applications</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('admin.vendor.vendors*') ? 'active' : '' }}" href="{{ route('admin.vendor.vendors') }}"><i class="fas fa-check-circle me-2"></i>Approved Vendors</a></li>
                        </ul>
                    </li>

                    <!-- Events Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white {{ request()->routeIs('events.*') || request()->routeIs('admin.event-applications*') ? 'active' : '' }}" href="#" id="eventsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-calendar"></i>
                            <span class="ms-1 d-none d-lg-inline">Events</span>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="eventsDropdown">
                            <li><a class="dropdown-item {{ request()->routeIs('events.index') ? 'active' : '' }}" href="{{ route('events.index') }}"><i class="fas fa-list me-2"></i>All Events</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('events.create') ? 'active' : '' }}" href="{{ route('events.create') }}"><i class="fas fa-calendar-plus me-2"></i>Create Event</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item {{ request()->routeIs('admin.event-applications*') ? 'active' : '' }}" href="{{ route('admin.event-applications.index') }}"><i class="fas fa-file-alt me-2"></i>Booth Applications</a></li>
                        </ul>
                    </li>

                    <!-- Reports -->
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.reports*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                            <i class="fas fa-chart-line"></i>
                            <span class="ms-1 d-none d-lg-inline">Reports</span>
                        </a>
                    </li>

                    <!-- Support -->
                    <li class="nav-item dropdown">
                        <a class="nav-link text-white dropdown-toggle {{ request()->routeIs('admin.support*') ? 'active' : '' }}" 
                           href="#" 
                           id="supportDropdown" 
                           role="button" 
                           data-bs-toggle="dropdown" 
                           aria-expanded="false">
                            <i class="fas fa-headset"></i>
                            <span class="ms-1 d-none d-lg-inline">Support</span>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="supportDropdown">
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('admin.support.index') ? 'active' : '' }}" 
                                   href="{{ route('admin.support.index') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('admin.support.inquiries*') ? 'active' : '' }}" 
                                   href="{{ route('admin.support.inquiries') }}">
                                    <i class="fas fa-list me-2"></i>Inquiries
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('admin.support.check.customer*') ? 'active' : '' }}" 
                                   href="{{ route('admin.support.check.customer.view') }}">
                                    <i class="fas fa-user-check me-2"></i>Check Customer
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('admin.support.faqs*') ? 'active' : '' }}" 
                                   href="{{ route('admin.support.faqs') }}">
                                    <i class="fas fa-question-circle me-2"></i>FAQs
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
                
                <!-- Right Side Navigation -->
                <ul class="navbar-nav">
                    <!-- Notifications -->
                    <li class="nav-item me-2">
                        @php
                            $notifications = \App\Models\Notification::where('user_id', auth()->id())
                                ->orderBy('created_at', 'desc')
                                ->limit(10)
                                ->get();
                            $unreadCount = \App\Models\Notification::where('user_id', auth()->id())
                                ->where('status', 'unread')
                                ->count();
                        @endphp
                        <x-notifications :notifications="$notifications" :unreadCount="$unreadCount" />
                    </li>
                    
                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-2"></i>
                            <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="fas fa-user-circle me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
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

    <div class="d-flex">
        <!-- Main Content -->
        <div class="main-content flex-grow-1" id="mainContent">
            <!-- Page Content -->
            <div class="admin-content">
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
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
     <script>
         function toggleSidebar() {
             const sidebar = document.getElementById('adminSidebar');
             const mainContent = document.getElementById('mainContent');
             const toggleIcon = document.getElementById('sidebarToggleIcon');
             
             // Force toggle with direct style manipulation
             if (sidebar.classList.contains('expanded')) {
                 sidebar.classList.remove('expanded');
                 mainContent.classList.remove('expanded');
                 sidebar.style.width = '70px';
                 mainContent.style.marginLeft = '70px';
                 toggleIcon.className = 'fas fa-bars';
                 localStorage.setItem('sidebarExpanded', 'false');
             } else {
                 sidebar.classList.add('expanded');
                 mainContent.classList.add('expanded');
                 sidebar.style.width = '280px';
                 mainContent.style.marginLeft = '280px';
                 toggleIcon.className = 'fas fa-chevron-left';
                 localStorage.setItem('sidebarExpanded', 'true');
             }
         }
         
         // Load saved state on page load
         document.addEventListener('DOMContentLoaded', function() {
             const isExpanded = localStorage.getItem('sidebarExpanded') === 'true';
             const sidebar = document.getElementById('adminSidebar');
             const mainContent = document.getElementById('mainContent');
             const toggleIcon = document.getElementById('sidebarToggleIcon');
             
             if (isExpanded) {
                 sidebar.classList.add('expanded');
                 mainContent.classList.add('expanded');
                 sidebar.style.width = '280px';
                 mainContent.style.marginLeft = '280px';
                 toggleIcon.className = 'fas fa-chevron-left';
             } else {
                 sidebar.style.width = '70px';
                 mainContent.style.marginLeft = '70px';
                 toggleIcon.className = 'fas fa-bars';
             }
         });
     </script>
    
    @yield('scripts')
</body>
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
</html>
