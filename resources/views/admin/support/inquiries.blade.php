@extends('layouts.admin')

@section('title', 'Support Inquiries - Admin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-envelope me-2 text-primary"></i>Support Inquiries
            </h1>
            <p class="text-muted mb-0">Manage all customer support inquiries</p>
        </div>
        <a href="{{ route('admin.support.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    <!-- Inquiries Table -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>All Inquiries
            </h6>
        </div>
        <div class="card-body">
            @if($inquiries->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inquiries as $inquiry)
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
                                    <small class="text-muted">{{ $inquiry->created_at->format('M d, Y H:i') }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('admin.support.inquiry.show', $inquiry->inquiry_id) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $inquiries->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-4x text-gray-300 mb-3"></i>
                    <h5 class="text-muted">No inquiries found</h5>
                    <p class="text-muted">Customer inquiries will appear here when they are submitted.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
