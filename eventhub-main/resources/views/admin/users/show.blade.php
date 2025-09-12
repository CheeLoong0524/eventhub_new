@extends('layouts.app')

@section('title', 'User Details - EventHub')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-user me-2"></i>User Details
            </h2>
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
