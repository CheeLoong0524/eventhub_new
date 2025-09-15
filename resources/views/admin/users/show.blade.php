@extends('layouts.admin')
/** Author: Tan Chim Yang 
 * RSW2S3G4
 * 23WMR14610 
 * **/
@section('title', 'User Details - EventHub')
@section('page-title', 'User Details')
@section('page-description', 'View and manage user information')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-end mb-4">
        <div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Back to Users
            </a>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Edit User
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">User Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Name:</label>
                            <p class="form-control-plaintext">{{ $user->name }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email:</label>
                            <p class="form-control-plaintext">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Role:</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'vendor' ? 'warning' : 'info') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status:</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Phone:</label>
                            <p class="form-control-plaintext">{{ $user->phone ?? 'Not provided' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Authentication Method:</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-{{ $user->auth_method === 'laravel' ? 'primary' : ($user->auth_method === 'firebase_email' ? 'info' : 'success') }}">
                                    {{ ucfirst(str_replace('_', ' ', $user->auth_method)) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                @if($user->address)
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Address:</label>
                            <p class="form-control-plaintext">{{ $user->address }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Joined:</label>
                            <p class="form-control-plaintext">{{ $user->created_at->format('M j, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Last Updated:</label>
                            <p class="form-control-plaintext">{{ $user->updated_at->format('M j, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Support Information Card -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-headset me-2"></i>Support Information
                </h5>
                <div class="d-flex align-items-center">
                    <span class="badge bg-{{ $dataSource === 'external' ? 'success' : 'info' }} me-2">
                        <i class="fas fa-{{ $dataSource === 'external' ? 'cloud' : 'database' }} me-1"></i>
                        {{ $dataSource === 'external' ? 'External API' : 'Internal Service' }}
                    </span>
                    @if($dataSource === 'internal')
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>Auto-fallback
                        </small>
                    @endif
                </div>
            </div>
            <div class="card-body">
                @if($supportError)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ $supportError }}
                    </div>
                @elseif($supportData)
                    <!-- Support Statistics -->
                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <div class="border-end">
                                <h4 class="text-primary mb-0">{{ $supportData['stats']['total_inquiries'] ?? 0 }}</h4>
                                <small class="text-muted">Total</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-end">
                                <h4 class="text-warning mb-0">{{ $supportData['stats']['pending_inquiries'] ?? 0 }}</h4>
                                <small class="text-muted">Pending</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <h4 class="text-success mb-0">{{ $supportData['stats']['resolved_inquiries'] ?? 0 }}</h4>
                            <small class="text-muted">Resolved</small>
                        </div>
                    </div>

                    <!-- Recent Inquiries -->
                    @if(isset($supportData['inquiries']) && count($supportData['inquiries']) > 0)
                        <h6 class="mb-2">Recent Inquiries:</h6>
                        <div class="list-group list-group-flush">
                            @foreach(array_slice($supportData['inquiries'], 0, 3) as $inquiry)
                                <div class="list-group-item px-0 py-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 text-truncate" style="max-width: 200px;">
                                                {{ $inquiry['subject'] ?? 'No Subject' }}
                                            </h6>
                                            <small class="text-muted">
                                                {{ isset($inquiry['created_at']) ? \Carbon\Carbon::parse($inquiry['created_at'])->format('M j, Y') : 'Unknown Date' }}
                                            </small>
                                        </div>
                                        <span class="badge bg-{{ $inquiry['status'] === 'pending' ? 'warning' : ($inquiry['status'] === 'resolved' ? 'success' : 'secondary') }}">
                                            {{ ucfirst($inquiry['status'] ?? 'Unknown') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @if(count($supportData['inquiries']) > 3)
                            <div class="text-center mt-2">
                                <small class="text-muted">
                                    +{{ count($supportData['inquiries']) - 3 }} more inquiries
                                </small>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p class="mb-0">No support inquiries found</p>
                        </div>
                    @endif

                    <!-- API Information -->
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-muted">
                            <i class="fas fa-{{ $dataSource === 'external' ? 'cloud' : 'database' }} me-1"></i>
                            Data source: {{ $dataSource === 'external' ? 'External API' : 'Internal Service' }}
                            @if($dataSource === 'internal')
                                <span class="text-warning">
                                    <i class="fas fa-arrow-down me-1"></i>Auto-fallback
                                </span>
                            @endif
                        </small>
                    </div>
                @else
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                        <p class="mb-0">Loading support data...</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit User
                    </a>
                    
                    @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-grid">
                            @csrf
                            <button type="submit" class="btn btn-{{ $user->is_active ? 'warning' : 'success' }}">
                                <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }} me-2"></i>
                                {{ $user->is_active ? 'Deactivate' : 'Activate' }} User
                            </button>
                        </form>
                        
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-grid">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                <i class="fas fa-trash me-2"></i>Delete User
                            </button>
                        </form>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            You cannot modify your own account from this page.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
