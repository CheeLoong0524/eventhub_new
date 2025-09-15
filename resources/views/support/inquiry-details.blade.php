@extends('layouts.app')

@section('title', 'Inquiry Details - EventHub')

@section('content')
<!-- Author: Yap Jia Wei -->

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="display-6 fw-bold text-primary mb-2">
                        <i class="fas fa-ticket-alt me-3"></i>{{ $inquiry->inquiry_id }}
                    </h1>
                    <p class="text-muted mb-0">{{ $inquiry->subject }}</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-{{ $inquiry->status === 'resolved' ? 'success' : ($inquiry->status === 'pending' ? 'warning' : 'primary') }} fs-6 px-3 py-2">
                        <i class="fas fa-{{ $inquiry->status === 'resolved' ? 'check-circle' : ($inquiry->status === 'pending' ? 'clock' : 'hourglass-half') }} me-2"></i>
                        {{ ucfirst($inquiry->status) }}
                    </span>
                </div>
            </div>

            <!-- Back Button -->
            <div class="mb-4">
                <a href="{{ route('support.check') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to My Inquiries
                </a>
            </div>

            <!-- Inquiry Details Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2 text-primary"></i>Inquiry Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Inquiry ID:</strong> {{ $inquiry->inquiry_id }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Submitted:</strong> {{ $inquiry->created_at->format('M d, Y \a\t g:i A') }}
                        </div>
                        <div class="col-md-6">
                            <strong>Last Updated:</strong> {{ $inquiry->updated_at->format('M d, Y \a\t g:i A') }}
                        </div>
                    </div>
                    @if($inquiry->resolved_at)
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Resolved:</strong> {{ $inquiry->resolved_at->format('M d, Y \a\t g:i A') }}
                        </div>
                        @if($inquiry->resolver)
                        <div class="col-md-6">
                            <strong>Resolved By:</strong> {{ $inquiry->resolver->name }}
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Customer Question -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>Your Question
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Subject:</strong> {{ $inquiry->subject }}
                    </div>
                    <div>
                        <strong>Message:</strong>
                        <div class="mt-2 p-3 bg-light rounded">
                            {{ $inquiry->message }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Reply -->
            @if($inquiry->admin_reply)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-reply me-2"></i>Admin Response
                    </h5>
                </div>
                <div class="card-body">
                    <div class="p-3 bg-light rounded">
                        {{ $inquiry->admin_reply }}
                    </div>
                    @if($inquiry->resolved_at)
                    <div class="mt-3 text-muted">
                        <small>
                            <i class="fas fa-clock me-1"></i>
                            Replied on {{ $inquiry->resolved_at->format('M d, Y \a\t g:i A') }}
                            @if($inquiry->resolver)
                                by {{ $inquiry->resolver->name }}
                            @endif
                        </small>
                    </div>
                    @endif
                </div>
            </div>
            @else
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-hourglass-half me-2"></i>Waiting for Response
                    </h5>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-clock text-warning mb-3" style="font-size: 3rem;"></i>
                    <h5 class="text-muted">Your inquiry is being reviewed</h5>
                    <p class="text-muted mb-0">
                        Our support team will respond to your inquiry within 24 hours. 
                        You will receive an email notification when we reply.
                    </p>
                </div>
            </div>
            @endif

            <!-- Status Timeline -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2 text-primary"></i>Status Timeline
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Submitted -->
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Inquiry Submitted</h6>
                                <p class="text-muted mb-0">{{ $inquiry->created_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>

                        @if($inquiry->status === 'pending')
                        <!-- Pending -->
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Pending Review</h6>
                                <p class="text-muted mb-0">Your inquiry is being reviewed by our support team</p>
                            </div>
                        </div>
                        @endif

                        @if($inquiry->status === 'resolved' && $inquiry->admin_reply)
                        <!-- Resolved -->
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Resolved</h6>
                                <p class="text-muted mb-0">
                                    {{ $inquiry->resolved_at ? $inquiry->resolved_at->format('M d, Y \a\t g:i A') : 'Response provided' }}
                                    @if($inquiry->resolver)
                                        by {{ $inquiry->resolver->name }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-lightbulb me-2 text-warning"></i>Need More Help?
                    </h5>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ route('support.check') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list me-2"></i>View All Inquiries
                        </a>
                        <a href="{{ route('support.index') }}" class="btn btn-outline-success">
                            <i class="fas fa-envelope me-2"></i>Submit New Inquiry
                        </a>
                        <a href="{{ route('support.faq') }}" class="btn btn-outline-info">
                            <i class="fas fa-question-circle me-2"></i>View FAQ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #007bff;
}
</style>
@endsection
