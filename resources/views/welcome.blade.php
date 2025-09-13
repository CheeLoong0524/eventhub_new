@extends('layouts.app')

@section('title', 'EventHub - Your Premier Event Management Platform')

@section('styles')
<style>
    :root {
        --primary-color: #2563eb;
        --primary-dark: #1d4ed8;
        --secondary-color: #7c3aed;
        --accent-color: #f59e0b;
        --success-color: #10b981;
        --text-dark: #1f2937;
        --text-muted: #6b7280;
        --bg-light: #f8fafc;
        --border-light: #e5e7eb;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .hero-section {
        background: linear-gradient(135deg, #1e293b 0%, #334155 25%, #475569 50%, #64748b 75%, #94a3b8 100%);
        color: white;
        position: relative;
        overflow: hidden;
        padding: 6rem 0;
        min-height: 100vh;
        display: flex;
        align-items: center;
    }
    
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 20% 80%, rgba(37, 99, 235, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(124, 58, 237, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 40% 40%, rgba(245, 158, 11, 0.05) 0%, transparent 50%);
        opacity: 0.8;
    }
    
    .hero-content {
        position: relative;
        z-index: 2;
    }
    
    .hero-title {
        font-size: clamp(2.5rem, 5vw, 4rem);
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .hero-subtitle {
        font-size: clamp(1.1rem, 2.5vw, 1.4rem);
        line-height: 1.6;
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 2.5rem;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .hero-buttons .btn {
        border-radius: 12px;
        font-weight: 600;
        font-size: 1.1rem;
        padding: 0.875rem 2rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        position: relative;
        overflow: hidden;
    }
    
    .hero-buttons .btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    
    .hero-buttons .btn:hover::before {
        left: 100%;
    }
    
    .hero-buttons .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        box-shadow: 0 4px 14px 0 rgba(37, 99, 235, 0.3);
    }
    
    .btn-outline-primary {
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: white;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
    }
    
    .btn-outline-primary:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.5);
        color: white;
    }
    
    .stats-section {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid var(--border-light);
        border-radius: 20px;
        box-shadow: var(--shadow-lg);
        position: relative;
        overflow: hidden;
    }
    
    .stats-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color), var(--accent-color));
    }
    
    .stat-item {
        padding: 2rem 1rem;
        text-align: center;
        transition: transform 0.3s ease;
    }
    
    .stat-item:hover {
        transform: translateY(-5px);
    }
    
    .stat-item h4 {
        color: var(--primary-color);
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.5rem;
    }
    
    .stat-item p {
        color: var(--text-muted);
        font-weight: 600;
        font-size: 1.1rem;
        margin: 0;
    }
    
    .stat-item i {
        color: var(--primary-color);
        margin-bottom: 1rem;
        opacity: 0.8;
    }
    
    .feature-card {
        background: #fff;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid var(--border-light);
        border-radius: 16px;
        position: relative;
        overflow: hidden;
    }
    
    .feature-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }
    
    .feature-card:hover::before {
        transform: scaleX(1);
    }
    
    .feature-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-xl);
        border-color: var(--primary-color);
    }
    
    .feature-icon {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    
    .feature-card:hover .feature-icon {
        transform: scale(1.1) rotate(5deg);
    }
    
    .feature-card h5 {
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 1rem;
    }
    
    .feature-card p {
        color: var(--text-muted);
        line-height: 1.7;
        margin: 0;
    }
    
    .testimonial-card {
        background: #fff;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-light);
        transition: all 0.3s ease;
        position: relative;
    }
    
    .testimonial-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }
    
    .testimonial-card::before {
        content: '"';
        position: absolute;
        top: -10px;
        left: 20px;
        font-size: 4rem;
        color: var(--primary-color);
        opacity: 0.2;
        font-family: serif;
    }
    
    .testimonial-text {
        font-style: italic;
        color: var(--text-muted);
        line-height: 1.7;
        margin-bottom: 1.5rem;
    }
    
    .testimonial-author {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .author-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.2rem;
    }
    
    .author-info h6 {
        margin: 0;
        color: var(--text-dark);
        font-weight: 600;
    }
    
    .author-info p {
        margin: 0;
        color: var(--text-muted);
        font-size: 0.9rem;
    }
    
    .cta-section {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        position: relative;
        overflow: hidden;
        border-radius: 24px;
    }
    
    .cta-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 30% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
            radial-gradient(circle at 70% 80%, rgba(255,255,255,0.05) 0%, transparent 50%);
    }
    
    .cta-section > * {
        position: relative;
        z-index: 2;
    }
    
    .cta-title {
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 800;
        margin-bottom: 1rem;
    }
    
    .cta-subtitle {
        font-size: 1.2rem;
        opacity: 0.9;
        margin-bottom: 2rem;
    }
    
    .cta-section .btn {
        border-radius: 12px;
        font-weight: 600;
        font-size: 1.1rem;
        padding: 0.875rem 2rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .cta-section .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }
    
    .btn-light {
        background: rgba(255, 255, 255, 0.95);
        color: var(--primary-color);
        border: none;
        font-weight: 700;
    }
    
    .btn-light:hover {
        background: white;
        color: var(--primary-color);
    }
    
    .btn-outline-light {
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: white;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
    }
    
    .btn-outline-light:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.5);
        color: white;
    }
    
    .section-title {
        font-size: clamp(2rem, 4vw, 2.5rem);
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 1rem;
    }
    
    .section-subtitle {
        font-size: 1.2rem;
        color: var(--text-muted);
        margin-bottom: 3rem;
    }
    
    .fade-in {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.6s ease;
    }
    
    .fade-in.visible {
        opacity: 1;
        transform: translateY(0);
    }
    
    @media (max-width: 768px) {
        .hero-section {
            padding: 4rem 0;
            min-height: 80vh;
        }
        
        .hero-buttons .btn {
            width: 100%;
            margin-bottom: 1rem;
        }
        
        .stat-item {
            padding: 1.5rem 1rem;
        }
        
        .stat-item h4 {
            font-size: 2rem;
        }
        
        .feature-card {
            margin-bottom: 2rem;
        }
        
        .testimonial-card {
            margin-bottom: 2rem;
        }
    }
    
    @media (max-width: 576px) {
        .hero-section {
            padding: 3rem 0;
        }
        
        .stat-item h4 {
            font-size: 1.75rem;
        }
        
        .cta-section {
            padding: 2rem 1.5rem !important;
        }
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<div class="hero-section text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="hero-content fade-in">
                    <h1 class="hero-title">
                        <i class="fas fa-calendar-alt me-3"></i>
                        Welcome to EventHub
                    </h1>
                    
                    <p class="hero-subtitle">
                        Your premier platform for discovering, creating, and managing amazing events.
                        Connect with vendors, customers, and event enthusiasts in one place.
                    </p>
                    
                    <div class="hero-buttons">
                        <a href="{{ route('auth.firebase') }}" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-rocket me-2"></i>
                            Get Started Today
                        </a>
                        
                        <a href="#features" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-info-circle me-2"></i>
                            Learn More
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Section -->
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="stats-section text-center p-5 fade-in">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-item">
                            <i class="fas fa-users fa-3x mb-3"></i>
                            <h4>2,500+</h4>
                            <p>Active Users</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-item">
                            <i class="fas fa-calendar-check fa-3x mb-3"></i>
                            <h4>1,200+</h4>
                            <p>Events Created</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-item">
                            <i class="fas fa-star fa-3x mb-3"></i>
                            <h4>4.9/5</h4>
                            <p>User Rating</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="container py-5">
    <div class="row">
        <div class="col-12 text-center mb-5 fade-in">
            <h2 class="section-title">Why Choose EventHub?</h2>
            <p class="section-subtitle">Discover what makes us the preferred choice for event management</p>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-lg-4 col-md-6 fade-in">
            <div class="feature-card h-100 text-center p-5">
                <div class="feature-icon mb-4">
                    <i class="fas fa-users fa-3x text-primary"></i>
                </div>
                <h5 class="mb-3">Community Driven</h5>
                <p>
                    Connect with event enthusiasts, vendors, and customers in a vibrant community.
                    Build lasting relationships and grow your network.
                </p>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 fade-in">
            <div class="feature-card h-100 text-center p-5">
                <div class="feature-icon mb-4">
                    <i class="fas fa-shield-alt fa-3x text-success"></i>
                </div>
                <h5 class="mb-3">Secure & Reliable</h5>
                <p>
                    Built with Firebase Authentication for enterprise-grade security and reliability.
                    Your data is protected with industry-standard encryption.
                </p>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 fade-in">
            <div class="feature-card h-100 text-center p-5">
                <div class="feature-icon mb-4">
                    <i class="fas fa-mobile-alt fa-3x text-info"></i>
                </div>
                <h5 class="mb-3">Mobile First</h5>
                <p>
                    Responsive design that works perfectly on all devices and screen sizes.
                    Access EventHub anywhere, anytime with our mobile-optimized platform.
                </p>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 fade-in">
            <div class="feature-card h-100 text-center p-5">
                <div class="feature-icon mb-4">
                    <i class="fas fa-chart-line fa-3x text-warning"></i>
                </div>
                <h5 class="mb-3">Analytics & Insights</h5>
                <p>
                    Track your event performance with comprehensive analytics and insights.
                    Make data-driven decisions to improve your events.
                </p>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 fade-in">
            <div class="feature-card h-100 text-center p-5">
                <div class="feature-icon mb-4">
                    <i class="fas fa-headset fa-3x text-danger"></i>
                </div>
                <h5 class="mb-3">24/7 Support</h5>
                <p>
                    Get help when you need it with our dedicated support team.
                    We're here to ensure your events run smoothly.
                </p>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 fade-in">
            <div class="feature-card h-100 text-center p-5">
                <div class="feature-icon mb-4">
                    <i class="fas fa-bolt fa-3x text-secondary"></i>
                </div>
                <h5 class="mb-3">Lightning Fast</h5>
                <p>
                    Experience blazing-fast performance with our optimized platform.
                    Create and manage events in seconds, not minutes.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Testimonials Section -->
<div class="container py-5">
    <div class="row">
        <div class="col-12 text-center mb-5 fade-in">
            <h2 class="section-title">What Our Users Say</h2>
            <p class="section-subtitle">Hear from our satisfied customers</p>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-lg-4 col-md-6 fade-in">
            <div class="testimonial-card">
                <p class="testimonial-text">
                    EventHub has revolutionized how we manage our corporate events. The platform is intuitive, 
                    secure, and has everything we need in one place.
                </p>
                <div class="testimonial-author">
                    <div class="author-avatar">SM</div>
                    <div class="author-info">
                        <h6>Sarah Mitchell</h6>
                        <p>Event Manager, TechCorp</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 fade-in">
            <div class="testimonial-card">
                <p class="testimonial-text">
                    As a vendor, EventHub has helped me reach more customers and grow my business. 
                    The booking system is seamless and the support is outstanding.
                </p>
                <div class="testimonial-author">
                    <div class="author-avatar">DJ</div>
                    <div class="author-info">
                        <h6>David Johnson</h6>
                        <p>Catering Services Owner</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 fade-in">
            <div class="testimonial-card">
                <p class="testimonial-text">
                    The mobile experience is fantastic! I can manage my events on the go and everything 
                    syncs perfectly across all my devices.
                </p>
                <div class="testimonial-author">
                    <div class="author-avatar">AL</div>
                    <div class="author-info">
                        <h6>Alex Lee</h6>
                        <p>Wedding Planner</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Call to Action -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 text-center">
            <div class="cta-section text-white p-5 fade-in">
                <h3 class="cta-title">Ready to Get Started?</h3>
                <p class="cta-subtitle">
                    Join EventHub today and discover a world of amazing events!
                    Start creating, managing, and participating in events that matter.
                </p>
                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                    <a href="{{ route('auth.firebase') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-user-plus me-2"></i>
                        Create Your Account
                    </a>
                    <a href="{{ route('auth.firebase') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Sign In
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for fade-in animations -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.fade-in').forEach(el => {
        observer.observe(el);
    });
});
</script>
@endsection
