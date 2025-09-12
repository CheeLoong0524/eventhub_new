@extends('layouts.app')

@section('title', 'User Management - EventHub')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>
                    <i class="fas fa-users me-2"></i>User Management
                </h2>
                <p class="text-muted mb-0">Manage all users in the system</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i>Add Admin User
            </a>
        </div>
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Note:</strong> This page shows all users. Regular users (vendors/customers) register through the main authentication system and use Firebase authentication. 
            Only admin users are created through this panel.
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.users.search') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="query" class="form-label">Search</label>
                        <input type="text" class="form-control" id="query" name="query" 
                               value="{{ $query ?? '' }}" placeholder="Search by name or email">
                    </div>
                    <div class="col-md-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role">
                            <option value="">All Roles</option>
                            <option value="admin" {{ ($role ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="vendor" {{ ($role ?? '') == 'vendor' ? 'selected' : '' }}>Vendor</option>
                            <option value="customer" {{ ($role ?? '') == 'customer' ? 'selected' : '' }}>Customer</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Search
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if($users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'vendor' ? 'warning' : 'info') }} text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                                {{ $user->name }}
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'vendor' ? 'warning' : 'info') }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ $user->created_at->format('M j, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-{{ $user->is_active ? 'warning' : 'success' }}"
                                                            onclick="return confirm('Are you sure you want to {{ $user->is_active ? 'deactivate' : 'activate' }} this user?')">
                                                        <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                                                    </button>
                                                </form>
                                                @if($user->id !== auth()->id())
                                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $users->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No users found.</p>
                        @if(($query ?? '') || ($role ?? ''))
                            <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
                                <i class="fas fa-times me-2"></i>Clear Filters
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 