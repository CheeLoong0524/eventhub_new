@extends('layouts.app')

@section('title', 'Customer Support - EventHub')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Header Section -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-primary mb-3">
                    <i class="fas fa-headset me-3"></i>Customer Support
                </h1>
                <p class="lead text-muted">{{ $userSupportData['welcome_message'] }}</p>
                @if($user)
                    <div class="badge bg-{{ $userSupportData['support_priority'] === 'high' ? 'success' : 'primary' }} fs-6">
                        <i class="fas fa-{{ $userSupportData['support_priority'] === 'high' ? 'star' : 'user' }} me-2"></i>
                        {{ ucfirst($userSupportData['user_type']) }} Support
                        @if($userSupportData['support_priority'] === 'high')
                            <span class="ms-2">(Priority Support)</span>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Quick Help Cards -->
            <div class="row mb-5">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="fas fa-question-circle text-primary fs-4"></i>
                            </div>
                            <h5 class="card-title">FAQ</h5>
                            <p class="card-text text-muted">Find answers to frequently asked questions</p>
                            <a href="{{ route('support.faq') }}" class="btn btn-outline-primary">View FAQ</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="fas fa-envelope text-success fs-4"></i>
                            </div>
                            <h5 class="card-title">Contact Us</h5>
                            <p class="card-text text-muted">Send us a message and we'll get back to you</p>
                            <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#contactModal">Contact Us</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="fas fa-search text-warning fs-4"></i>
                            </div>
                            <h5 class="card-title">Check Inquiry</h5>
                            <p class="card-text text-muted">View status of your submitted inquiries</p>
                            <a href="{{ route('support.check') }}" class="btn btn-outline-warning">Check Status</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personalized Support Section -->
            @if($user)
            <div class="row mb-5">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title mb-4">
                                <i class="fas fa-user-cog me-2 text-primary"></i>Your Personal Support Dashboard
                            </h4>
                            
                            <div class="row">
                                <!-- Account Status -->
                                <div class="col-md-6 mb-4">
                                    <h6 class="text-muted mb-3">Account Status</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-{{ $userSupportData['account_status']['is_active'] ? 'check-circle text-success' : 'times-circle text-danger' }} me-2"></i>
                                            Account: {{ $userSupportData['account_status']['is_active'] ? 'Active' : 'Inactive' }}
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-{{ $userSupportData['account_status']['email_verified'] ? 'check-circle text-success' : 'exclamation-triangle text-warning' }} me-2"></i>
                                            Email: {{ $userSupportData['account_status']['email_verified'] ? 'Verified' : 'Not Verified' }}
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-shield-alt me-2 text-info"></i>
                                            Auth Method: {{ ucfirst($userSupportData['account_status']['auth_method']) }}
                                        </li>
                                    </ul>
                                </div>
                                
                                <!-- Recent Activities -->
                                <div class="col-md-6 mb-4">
                                    <h6 class="text-muted mb-3">Recent Activities</h6>
                                    @if(count($userSupportData['recent_activities']) > 0)
                                        <ul class="list-unstyled">
                                            @foreach($userSupportData['recent_activities'] as $activity)
                                                <li class="mb-2">
                                                    <i class="fas fa-{{ $activity['type'] === 'vendor_application' ? 'store' : 'user-plus' }} me-2 text-primary"></i>
                                                    {{ $activity['message'] }}
                                                    <small class="text-muted d-block">{{ $activity['date']->format('M d, Y') }}</small>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted">No recent activities</p>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Role-specific Information -->
                            @if(isset($userSupportData['admin_specific']))
                                <div class="mt-4 p-3 bg-light rounded">
                                    <h6 class="text-muted mb-3">Admin Statistics</h6>
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="fw-bold text-primary">{{ $userSupportData['admin_specific']['total_users'] }}</div>
                                            <small class="text-muted">Total Users</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="fw-bold text-warning">{{ $userSupportData['admin_specific']['pending_vendors'] }}</div>
                                            <small class="text-muted">Pending Vendors</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="fw-bold text-success">{{ $userSupportData['admin_specific']['active_events'] }}</div>
                                            <small class="text-muted">Active Events</small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            @if(isset($userSupportData['vendor_specific']))
                                <div class="mt-4 p-3 bg-light rounded">
                                    <h6 class="text-muted mb-3">Vendor Information</h6>
                                    @if($userSupportData['vendor_specific']['has_vendor_profile'])
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="fw-bold text-{{ $userSupportData['vendor_specific']['vendor_status'] === 'approved' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($userSupportData['vendor_specific']['vendor_status']) }}
                                                </div>
                                                <small class="text-muted">Status</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="fw-bold text-primary">{{ $userSupportData['vendor_specific']['total_applications'] }}</div>
                                                <small class="text-muted">Total Applications</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="fw-bold text-success">{{ $userSupportData['vendor_specific']['approved_applications'] }}</div>
                                                <small class="text-muted">Approved</small>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-muted mb-0">Complete your vendor application to access vendor features.</p>
                                    @endif
                                </div>
                            @endif
                            
                            @if(isset($userSupportData['customer_specific']))
                                <div class="mt-4 p-3 bg-light rounded">
                                    <h6 class="text-muted mb-3">Customer Information</h6>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="fw-bold text-primary">{{ $userSupportData['customer_specific']['member_since'] }}</div>
                                            <small class="text-muted">Member Since</small>
                                        </div>
                                        <div class="col-6">
                                            <div class="fw-bold text-info">{{ $userSupportData['customer_specific']['account_type'] }}</div>
                                            <small class="text-muted">Account Type</small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Personalized Tips -->
            @if(count($userSupportData['personalized_tips']) > 0)
            <div class="card border-0 shadow-sm mb-5">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <i class="fas fa-lightbulb me-2 text-warning"></i>Personalized Tips for You
                    </h4>
                    <ul class="list-unstyled">
                        @foreach($userSupportData['personalized_tips'] as $tip)
                            <li class="mb-2">
                                <i class="fas fa-arrow-right me-2 text-primary"></i>{{ $tip }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <!-- Support Hours -->
            <div class="card border-0 shadow-sm mb-5">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <i class="fas fa-clock me-2 text-primary"></i>Support Information
                    </h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Monday - Friday:</strong> 9:00 AM - 6:00 PM EST</p>
                            <p class="mb-2"><strong>Saturday:</strong> 10:00 AM - 4:00 PM EST</p>
                            <p class="mb-0"><strong>Sunday:</strong> Closed</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Email Response:</strong> Within 24 hours</p>
                            <p class="mb-2"><strong>Inquiry Tracking:</strong> Real-time status updates</p>
                            <p class="mb-0"><strong>Emergency Support:</strong> Available for critical issues</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Popular Topics -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <i class="fas fa-star me-2 text-warning"></i>Popular Support Topics
                    </h4>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <a href="{{ route('support.faq') }}#event-creation" class="text-decoration-none">
                                        <i class="fas fa-chevron-right me-2 text-primary"></i>How to create an event
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="{{ route('support.faq') }}#vendor-application" class="text-decoration-none">
                                        <i class="fas fa-chevron-right me-2 text-primary"></i>Vendor application process
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="{{ route('support.faq') }}#payment-issues" class="text-decoration-none">
                                        <i class="fas fa-chevron-right me-2 text-primary"></i>Payment and billing issues
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <a href="{{ route('support.faq') }}#account-management" class="text-decoration-none">
                                        <i class="fas fa-chevron-right me-2 text-primary"></i>Account management
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="{{ route('support.faq') }}#technical-support" class="text-decoration-none">
                                        <i class="fas fa-chevron-right me-2 text-primary"></i>Technical support
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="{{ route('support.faq') }}#cancellation-policy" class="text-decoration-none">
                                        <i class="fas fa-chevron-right me-2 text-primary"></i>Cancellation policy
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Modal -->
<div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contactModalLabel">
                    <i class="fas fa-envelope me-2"></i>Contact Support
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('support.contact') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject *</label>
                        <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                               id="subject" name="subject" value="{{ old('subject') }}" required>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message *</label>
                        <textarea class="form-control @error('message') is-invalid @enderror" 
                                  id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-populate form if user is logged in
    document.addEventListener('DOMContentLoaded', function() {
        @auth
            // Form is already pre-populated with user data
        @endauth
    });
</script>
@endsection
