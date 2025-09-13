<nav class="navbar navbar-expand-lg navbar-dark py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <a class="navbar-brand fs-4" href="{{ route('home') }}">
            <i class="fas fa-calendar-alt me-2"></i>
            <span>EventHub</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}"><i class="fas fa-home me-2"></i>Home</a>
                </li>
                @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('support.index') }}"><i class="fas fa-headset me-2"></i>Customer Support</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                    </li>
                    @if(Auth::user()->role === 'customer')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('customer.events.index') }}"><i class="fas fa-calendar me-2"></i>Events</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('cart.index') }}">
                                <i class="fas fa-shopping-cart me-2"></i>Cart
                                <span class="badge bg-warning text-dark cart-count ms-1">0</span>
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('profile.show') }}"><i class="fas fa-user-circle me-2"></i>Profile</a>
                    </li>
                @endauth
            </ul>
            
            <ul class="navbar-nav">
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-2"></i>{{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.show') }}">
                                <i class="fas fa-user-circle me-2"></i>Profile
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('auth.logout') }}" method="POST" class="d-inline" onsubmit="return handleLogout(event)">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('auth.firebase') }}">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-warning" href="{{ route('admin.login') }}">
                            <i class="fas fa-shield-alt me-2"></i>Admin
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
