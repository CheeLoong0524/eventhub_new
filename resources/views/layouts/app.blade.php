<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'EventHub')</title>

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
        body { 
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar { 
            position: relative;
            z-index: 1030;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15); 
            width: 100%;
            order: -1;
            flex-shrink: 0;
        }
        .navbar-brand { font-weight: 600; font-size: 1.25rem; }
        .nav-link { font-weight: 500; transition: color 0.2s ease; color: rgba(255,255,255,0.9) !important; }
        .nav-link:hover { color: #fff !important; }
        .nav-link.active { color: #fff !important; text-decoration: underline; text-underline-offset: 4px; }
        .nav-icon { width: 1.1rem; text-align: center; }
        .avatar-initials { width: 32px; height: 32px; border-radius: 50%; background: #6c757d; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 600; }
        .dropdown-menu { border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 10px; }
        footer { margin-top: auto; box-shadow: 0 -2px 4px rgba(0,0,0,0.1); }
        main { 
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
    @unless(request()->routeIs('event-booking.payment-receipt-yf'))
        @include('layouts.partials.navbar')
    @endunless

    <!-- Main Content -->
    <main class="flex-grow-1 py-4">
        @if(session('success'))
            <div class="container">
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container">
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    @unless(request()->routeIs('event-booking.payment-receipt-yf'))
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
    @endunless

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Handle Firebase logout before form submission
        function handleLogout(event) {
            if (typeof firebase !== 'undefined' && firebase.auth) {
                firebase.auth().signOut().catch(() => {});
            }
            return true;
        }
        
        // Update cart count on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
        });
        
        function updateCartCount() {
            const cartCountElement = document.querySelector('.cart-count');
            if (cartCountElement) {
                fetch('{{ route("cart.summary") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    cartCountElement.textContent = data.total_items || 0;
                })
                .catch(error => {
                    console.error('Error updating cart count:', error);
                });
            }
        }
    </script>
    
    @yield('scripts')
</body>
</html> 