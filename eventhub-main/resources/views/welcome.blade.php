@extends('layouts.app')

@section('title', 'Welcome to EventHub')

@section('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }
    
    .hero-content {
        position: relative;
        z-index: 2;
    }
    
    .text-gradient {
        background: linear-gradient(45deg, #fff, #f8f9fa);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .hero-buttons .btn {
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .hero-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }
    
    .stats-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 1px solid #dee2e6;
    }
    
    .stat-item {
        padding: 1rem;
    }
    
    .stat-item h4 {
        color: #667eea;
        font-size: 2.5rem;
    }
    
    .feature-card {
        background: white;
        transition: all 0.3s ease;
        border: 1px solid #f8f9fa;
    }
    
    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border-color: #667eea;
    }
    
    .feature-icon {
        transition: transform 0.3s ease;
    }
    
    .feature-card:hover .feature-icon {
        transform: scale(1.1);
    }
    
    .cta-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        overflow: hidden;
    }
    
    .cta-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>');
        opacity: 0.3;
    }
    
    .cta-section > * {
        position: relative;
        z-index: 2;
    }
    
    .cta-section .btn {
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .cta-section .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }
    
    @media (max-width: 768px) {
        .hero-section {
            padding: 3rem 0;
        }
        
        .hero-buttons .btn {
            margin-bottom: 1rem;
        }
        
        .cta-section {
            padding: 3rem 2rem !important;
        }
    }
            </style>
@endsection

@section('content')
<div class="hero-section text-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="hero-content">
                    <h1 class="display-3 fw-bold mb-4 text-gradient">
                        <i class="fas fa-calendar-alt me-3"></i>
                        Welcome to EventHub
                    </h1>
                    
                    <p class="lead mb-5 fs-5 text-muted">
                        Your premier platform for discovering, creating, and managing amazing events.
                        Connect with vendors, customers, and event enthusiasts in one place.
                    </p>
                    
                    <div class="hero-buttons mb-5">
                        <a href="{{ route('auth.firebase') }}" class="btn btn-primary btn-lg me-3 px-4 py-3">
                            <i class="fas fa-rocket me-2"></i>
                            Get Started Today
                        </a>
                        
                        <a href="#features" class="btn btn-outline-primary btn-lg px-4 py-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Learn More
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center mb-5">
        <div class="col-lg-10">
            <div class="stats-section text-center p-4 bg-light rounded-3">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-item">
                            <i class="fas fa-users fa-2x text-primary mb-2"></i>
                            <h4 class="fw-bold">1000+</h4>
                            <p class="text-muted mb-0">Active Users</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-item">
                            <i class="fas fa-calendar-check fa-2x text-success mb-2"></i>
                            <h4 class="fw-bold">500+</h4>
                            <p class="text-muted mb-0">Events Created</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-item">
                            <i class="fas fa-star fa-2x text-warning mb-2"></i>
                            <h4 class="fw-bold">4.9/5</h4>
                            <p class="text-muted mb-0">User Rating</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Features Section -->
    <div id="features" class="py-5">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">Why Choose EventHub?</h2>
                <p class="lead text-muted">Discover what makes us the preferred choice for event management</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="feature-card h-100 text-center p-4 rounded-3 shadow-sm border-0">
                    <div class="feature-icon mb-4">
                        <i class="fas fa-users fa-3x text-primary"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Community Driven</h5>
                    <p class="text-muted mb-0">
                        Connect with event enthusiasts, vendors, and customers in a vibrant community.
                        Build lasting relationships and grow your network.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="feature-card h-100 text-center p-4 rounded-3 shadow-sm border-0">
                    <div class="feature-icon mb-4">
                        <i class="fas fa-shield-alt fa-3x text-success"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Secure & Reliable</h5>
                    <p class="text-muted mb-0">
                        Built with Firebase Authentication for enterprise-grade security and reliability.
                        Your data is protected with industry-standard encryption.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="feature-card h-100 text-center p-4 rounded-3 shadow-sm border-0">
                    <div class="feature-icon mb-4">
                        <i class="fas fa-mobile-alt fa-3x text-info"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Mobile First</h5>
                    <p class="text-muted mb-0">
                        Responsive design that works perfectly on all devices and screen sizes.
                        Access EventHub anywhere, anytime with our mobile-optimized platform.
                    </p>
                </div>
            </div>
        </div>
        </div>

    <!-- Call to Action -->
    <div class="py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="cta-section bg-gradient text-white rounded-4 p-5 shadow-lg">
                    <h3 class="fw-bold mb-3">Ready to Get Started?</h3>
                    <p class="lead mb-4 opacity-90">
                        Join EventHub today and discover a world of amazing events!
                        Start creating, managing, and participating in events that matter.
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                        <a href="{{ route('auth.firebase') }}" class="btn btn-light btn-lg px-4 py-3 fw-bold">
                            <i class="fas fa-user-plus me-2"></i>
                            Create Your Account
                        </a>
                        <a href="{{ route('auth.firebase') }}" class="btn btn-outline-light btn-lg px-4 py-3">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Sign In
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
