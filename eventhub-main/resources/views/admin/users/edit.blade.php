@extends('layouts.app')

@section('title', 'Edit User - EventHub')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-user-edit me-2"></i>Edit User
            </h2>
            <div>
                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-info me-2">
                    <i class="fas fa-eye me-2"></i>View User
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Users
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
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="vendor" {{ old('role', $user->role) == 'vendor' ? 'selected' : '' }}>Vendor</option>
                                    <option value="customer" {{ old('role', $user->role) == 'customer' ? 'selected' : '' }}>Customer</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active User
                                    </label>
                                </div>
                                <div class="form-text">Uncheck to deactivate this user account.</div>
                            </div>
                        </div>
                    </div>

                    @if($errors->has('general'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ $errors->first('general') }}
                        </div>
                    @endif

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-info">
                            <i class="fas fa-eye me-2"></i>View User
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">User Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Authentication Method:</label>
                    <p class="form-control-plaintext">
                        <span class="badge bg-{{ $user->auth_method === 'laravel' ? 'primary' : ($user->auth_method === 'firebase_email' ? 'info' : 'success') }}">
                            {{ ucfirst(str_replace('_', ' ', $user->auth_method)) }}
                        </span>
                    </p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Joined:</label>
                    <p class="form-control-plaintext">{{ $user->created_at->format('M j, Y') }}</p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Last Updated:</label>
                    <p class="form-control-plaintext">{{ $user->updated_at->format('M j, Y') }}</p>
                </div>

                @if($user->auth_method !== 'laravel')
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Note:</strong> This user uses {{ ucfirst(str_replace('_', ' ', $user->auth_method)) }} authentication. 
                        Password changes should be made through their authentication provider.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
