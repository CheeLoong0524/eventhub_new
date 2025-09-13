@extends('layouts.admin')

@section('title', 'Customer Support Dashboard - Admin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-headset me-2 text-primary"></i>Customer Support Dashboard
            </h1>
            <p class="text-muted mb-0">Manage customer inquiries and FAQ content</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.support.inquiries') }}" class="btn btn-outline-primary">
                <i class="fas fa-list me-2"></i>View All Inquiries
            </a>
            <a href="{{ route('admin.support.faqs') }}" class="btn btn-primary">
                <i class="fas fa-question-circle me-2"></i>Manage FAQs
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Inquiries
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_inquiries'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Inquiries
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_inquiries'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Resolved
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['resolved_inquiries'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Inquiries -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>Recent Inquiries
                    </h6>
                    <a href="{{ route('admin.support.inquiries') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentInquiries->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Subject</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentInquiries as $inquiry)
                                    <tr>
                                        <td>
                                            <code class="text-primary">{{ $inquiry->inquiry_id }}</code>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $inquiry->name }}</strong>
                                                @if($inquiry->user)
                                                    <br><small class="text-muted">{{ $inquiry->user->email }}</small>
                                                @else
                                                    <br><small class="text-muted">{{ $inquiry->email }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 200px;" title="{{ $inquiry->subject }}">
                                                {{ $inquiry->subject }}
                                            </div>
                                        </td>
                                        <td>
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
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $inquiry->created_at->format('M d, Y') }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.support.inquiry.show', $inquiry->inquiry_id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">No inquiries found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- FAQ Statistics & Quick Actions -->
        <div class="col-lg-4">
            <!-- FAQ Statistics -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-question-circle me-2"></i>FAQ Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h4 text-primary">{{ $faqStats['total_faqs'] }}</div>
                            <small class="text-muted">Total FAQs</small>
                        </div>
                        <div class="col-6">
                            <div class="h4 text-success">{{ $faqStats['active_faqs'] }}</div>
                            <small class="text-muted">Active FAQs</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6 class="mb-3">FAQs by Category</h6>
                    @foreach($faqStats['faqs_by_category'] as $category => $count)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            @php
                                $color = match($category) {
                                    'general' => 'secondary',
                                    'technical' => 'warning',
                                    'billing' => 'primary',
                                    'event' => 'dark',
                                    'customer' => 'info',
                                    default => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $color }}">
                                {{ ucfirst($category) }}
                            </span>
                            <span class="fw-bold">{{ $count }}</span>
                        </div>
                    @endforeach
                    
                    <div class="mt-3">
                        <a href="{{ route('admin.support.faqs') }}" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-cog me-2"></i>Manage FAQs
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }
    .border-left-danger {
        border-left: 0.25rem solid #e74a3b !important;
    }
</style>
@endsection
