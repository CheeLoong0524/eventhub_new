@extends('layouts.admin')

@section('title', 'Inquiry Details - Admin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-envelope-open me-2 text-primary"></i>Inquiry Details
            </h1>
            <p class="text-muted mb-0">Inquiry ID: <code>{{ $inquiry->inquiry_id }}</code></p>
        </div>
        <a href="{{ route('admin.support.inquiries') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Back to Inquiries
        </a>
    </div>

    <div class="row">
        <!-- Inquiry Details -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Inquiry Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Customer Name:</strong>
                            <p class="mb-0">{{ $inquiry->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Email:</strong>
                            <p class="mb-0">{{ $inquiry->email }}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Status:</strong>
                            <p class="mb-0">
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'resolved' => 'success',
                                        'closed' => 'secondary'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$inquiry->status] ?? 'secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $inquiry->status)) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Subject:</strong>
                        <p class="mb-0">{{ $inquiry->subject }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>Message:</strong>
                        <div class="border rounded p-3 bg-light">
                            {{ $inquiry->message }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <strong>Submitted:</strong>
                            <p class="mb-0">{{ is_string($inquiry->created_at) ? \Carbon\Carbon::parse($inquiry->created_at)->format('M d, Y H:i:s') : $inquiry->created_at->format('M d, Y H:i:s') }}</p>
                        </div>
                        @if($inquiry->resolved_at ?? false)
                        <div class="col-md-6">
                            <strong>Resolved:</strong>
                            <p class="mb-0">{{ is_string($inquiry->resolved_at) ? \Carbon\Carbon::parse($inquiry->resolved_at)->format('M d, Y H:i:s') : $inquiry->resolved_at->format('M d, Y H:i:s') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Admin Reply Section -->
            @if($inquiry->admin_reply ?? false)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-reply me-2"></i>Admin Reply
                    </h6>
                </div>
                <div class="card-body">
                    <div class="border rounded p-3 bg-light">
                        {{ $inquiry->admin_reply ?? '' }}
                    </div>
                    @if($inquiry->resolver ?? false)
                    <small class="text-muted mt-2 d-block">
                        Replied by: {{ $inquiry->resolver->name }} on {{ is_string($inquiry->resolved_at) ? \Carbon\Carbon::parse($inquiry->resolved_at)->format('M d, Y H:i:s') : $inquiry->resolved_at->format('M d, Y H:i:s') }}
                    </small>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Admin Actions -->
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cogs me-2"></i>Admin Actions
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.support.inquiry.update', $inquiry->inquiry_id) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending" {{ $inquiry->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="resolved" {{ $inquiry->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $inquiry->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="admin_reply" class="form-label">Reply to Customer</label>
                            <textarea class="form-control" id="admin_reply" name="admin_reply" rows="4" 
                                      placeholder="Enter your reply to the customer...">{{ old('admin_reply', $inquiry->admin_reply ?? '') }}</textarea>
                        </div>


                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>Update Inquiry
                        </button>
                    </form>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user me-2"></i>Customer Information
                    </h6>
                </div>
                <div class="card-body">
                    @if($inquiry->user ?? false)
                        <p><strong>Account:</strong> Registered User</p>
                        <p><strong>Role:</strong> {{ ucfirst($inquiry->user->role ?? 'user') }}</p>
                        <p><strong>Member Since:</strong> {{ isset($inquiry->user->created_at) ? \Carbon\Carbon::parse($inquiry->user->created_at)->format('M d, Y') : 'N/A' }}</p>
                        @if($inquiry->user->vendor ?? false)
                            <p><strong>Vendor Status:</strong> 
                                <span class="badge bg-{{ $inquiry->user->vendor->status === 'approved' ? 'success' : 'warning' }}">
                                    {{ ucfirst($inquiry->user->vendor->status ?? 'pending') }}
                                </span>
                            </p>
                        @endif
                    @else
                        <p><strong>Account:</strong> Guest User</p>
                        <p class="text-muted">This inquiry was submitted by a non-registered user.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
