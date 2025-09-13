@extends('layouts.app')

@section('title', 'Check My Inquiries - EventHub')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- Header Section -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-primary mb-3">
                    <i class="fas fa-search me-3"></i>Check My Inquiries
                </h1>
                <p class="lead text-muted">View the status of your support inquiries and responses from our team</p>
            </div>

            <!-- Back to Support -->
            <div class="mb-4">
                <a href="{{ route('support.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Support
                </a>
            </div>

            <!-- Error Messages -->
            @if(isset($error))
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ $error }}
                </div>
            @endif

            <!-- Success Messages -->
            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif

            <!-- User Status and Search Form -->
            @if(isset($isGuest) && $isGuest)
            <!-- Guest User - Show Search Form -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-search me-2"></i>Search Your Inquiries
                    </h5>
                    <p class="text-muted mb-3">Please enter your email address to view your support inquiries.</p>
                    <form method="GET" action="{{ route('support.check') }}">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ request('email') }}" placeholder="your@email.com" required>
                                <small class="form-text text-muted">Enter your email to view all your inquiries</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="inquiry_id" class="form-label">Inquiry ID (Optional)</label>
                                <input type="text" class="form-control" id="inquiry_id" name="inquiry_id" 
                                       value="{{ request('inquiry_id') }}" placeholder="e.g., INQ-2024-001">
                                <small class="form-text text-muted">Leave empty to see all your inquiries</small>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>View My Inquiries
                        </button>
                    </form>
                </div>
            </div>
            @elseif(isset($email) && auth()->check())
            <!-- Authenticated User - Show User Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">
                                <i class="fas fa-user me-2"></i>Your Support Inquiries
                            </h5>
                            <p class="text-muted mb-0">Showing inquiries for: <strong>{{ $email }}</strong></p>
                        </div>
                        <div>
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle me-1"></i>Logged In
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @elseif(isset($email))
            <!-- Guest User with Email - Show Email Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-1">
                        <i class="fas fa-envelope me-2"></i>Support Inquiries
                    </h5>
                    <p class="text-muted mb-0">Showing inquiries for: <strong>{{ $email }}</strong></p>
                </div>
            </div>
            @endif

            <!-- Inquiry Results -->
            @if(isset($inquiries) && $inquiries->count() > 0)
                <!-- Legacy support for old format -->
                <div class="row">
                    @foreach($inquiries as $inquiry)
                    <div class="col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-{{ $inquiry->status === 'resolved' ? 'success' : ($inquiry->status === 'pending' ? 'warning' : 'primary') }} text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="fas fa-ticket-alt me-2"></i>{{ $inquiry->inquiry_id }}
                                    </h6>
                                    <span class="badge bg-light text-dark">
                                        {{ ucfirst($inquiry->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{ $inquiry->subject }}</h5>
                                <p class="card-text text-muted">
                                    {{ Str::limit($inquiry->message, 100) }}
                                </p>
                                <div class="row text-center mb-3">
                                    <div class="col-6">
                                        <small class="text-muted">Submitted</small>
                                        <div class="fw-bold">{{ $inquiry->created_at->format('M d, Y') }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Status</small>
                                        <div class="fw-bold text-{{ $inquiry->status === 'resolved' ? 'success' : ($inquiry->status === 'pending' ? 'warning' : 'primary') }}">
                                            {{ ucfirst($inquiry->status) }}
                                        </div>
                                    </div>
                                </div>
                                @if($inquiry->has_admin_reply)
                                    <div class="alert alert-info alert-sm mb-3">
                                        <i class="fas fa-reply me-2"></i>
                                        <strong>Admin Reply Available!</strong>
                                        <p class="mb-0 mt-1 small">Our team has responded to your inquiry.</p>
                                    </div>
                                @endif
                                
                                <div class="d-grid">
                                    <a href="{{ route('support.inquiry.show', $inquiry->inquiry_id) }}?email={{ $email }}" class="btn btn-outline-primary">
                                        <i class="fas fa-eye me-2"></i>View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <!-- No Inquiries -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-inbox text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="text-muted mb-3">No Inquiries Found</h4>
                    <p class="text-muted mb-4">You haven't submitted any support inquiries yet.</p>
                    <a href="{{ route('support.index') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Submit New Inquiry
                    </a>
                </div>
            @endif

            <!-- Quick Actions -->
            @if(isset($inquiries) && $inquiries->count() > 0)
            <div class="card border-0 shadow-sm mt-5">
                <div class="card-body text-center">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-lightbulb me-2 text-warning"></i>Quick Actions
                    </h5>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ route('support.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-envelope me-2"></i>Submit New Inquiry
                        </a>
                        <a href="{{ route('support.faq') }}" class="btn btn-outline-info">
                            <i class="fas fa-question-circle me-2"></i>View FAQ
                        </a>
                        <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#contactModal">
                            <i class="fas fa-headset me-2"></i>Contact Support
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Contact Modal (same as in index.blade.php) -->
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
                                   id="name" name="name" value="{{ old('name', $user->name ?? '') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
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
