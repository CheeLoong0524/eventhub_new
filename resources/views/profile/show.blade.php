@extends('layouts.app')

@section('title', 'Profile - EventHub')

@section('styles')
<style>
    /* Password Validation Styles */
    .password-strength .progress {
        background-color: #e9ecef;
        border-radius: 10px;
    }
    
    .password-strength .progress-bar {
        transition: all 0.3s ease;
        border-radius: 10px;
    }
    
    .password-strength .progress-bar.weak {
        background-color: #dc3545;
        width: 25% !important;
    }
    
    .password-strength .progress-bar.medium {
        background-color: #ffc107;
        width: 50% !important;
    }
    
    .password-strength .progress-bar.strong {
        background-color: #28a745;
        width: 75% !important;
    }
    
    .password-strength .progress-bar.very-strong {
        background-color: #20c997;
        width: 100% !important;
    }
    
    .password-requirements ul li {
        font-size: 0.85rem;
        margin-bottom: 0.25rem;
    }
    
    .password-requirements ul li.valid {
        color: #28a745;
    }
    
    .password-requirements ul li.valid i {
        color: #28a745;
    }
    
    .password-match, .password-mismatch {
        font-size: 0.85rem;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-user me-2"></i>Profile Information
                </h4>
                <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit me-1"></i>Edit Profile
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Full Name</label>
                            <p class="form-control-plaintext">{{ $user->name }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email Address</label>
                            <p class="form-control-plaintext">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Account Type</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'vendor' ? 'warning' : 'info') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Account Status</label>
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
                            <label class="form-label fw-bold">Phone Number</label>
                            <p class="form-control-plaintext">{{ $user->phone ?: 'Not provided' }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Member Since</label>
                            <p class="form-control-plaintext">{{ $user->created_at->format('F j, Y') }}</p>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Address</label>
                    <p class="form-control-plaintext">{{ $user->address ?: 'Not provided' }}</p>
                </div>

                <hr>

                <!-- Password Update Section (only for Firebase email/password users) -->
                @if(Auth::user() && Auth::user()->canChangePassword())
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-key me-2"></i>Update Password
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="password-update-form" onsubmit="handlePasswordUpdate(event)">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="current-password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="current-password" name="currentPassword" 
                                               placeholder="Enter current password" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="new-password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new-password" name="newPassword" 
                                               placeholder="Enter new password" required oninput="validateProfilePassword(this.value)">
                                        <div class="password-strength mt-2">
                                            <div class="progress" style="height: 5px;">
                                                <div class="progress-bar" id="profile-password-strength-bar" role="progressbar" style="width: 0%"></div>
                                            </div>
                                            <small class="text-muted mt-1 d-block">Password strength: <span id="profile-password-strength-text">Weak</span></small>
                                        </div>
                                        <div class="password-requirements mt-2" id="profile-password-requirements">
                                            <small class="text-muted">Requirements:</small>
                                            <ul class="list-unstyled mt-1 mb-0">
                                                <li id="profile-req-length"><span class="req-icon text-danger me-1">❌</span>At least 8 characters</li>
                                                <li id="profile-req-uppercase"><span class="req-icon text-danger me-1">❌</span>Contains uppercase letter</li>
                                                <li id="profile-req-lowercase"><span class="req-icon text-danger me-1">❌</span>Contains lowercase letter</li>
                                                <li id="profile-req-number"><span class="req-icon text-danger me-1">❌</span>Contains number</li>
                                                <li id="profile-req-special"><span class="req-icon text-danger me-1">❌</span>Contains special character</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="confirm-password" class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" id="confirm-password" name="confirmPassword" 
                                               placeholder="Confirm new password" required oninput="validateProfilePasswordConfirmation()">
                                        <div class="password-match mt-2" id="profile-password-match" style="display: none;">
                                            <small class="text-success"><span class="me-1">✅</span>Passwords match</small>
                                        </div>
                                        <div class="password-mismatch mt-2" id="profile-password-mismatch" style="display: none;">
                                            <small class="text-danger"><span class="me-1">❌</span>Passwords do not match</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-key me-2"></i>Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                <div class="d-flex justify-content-between">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Firebase SDK -->
<script type="module">
    // Import Firebase modules
    import { initializeApp } from 'https://www.gstatic.com/firebasejs/12.2.1/firebase-app.js';
    import { getAuth, updatePassword, reauthenticateWithCredential, EmailAuthProvider } from 'https://www.gstatic.com/firebasejs/12.2.1/firebase-auth.js';

    // Firebase configuration
    const firebaseConfig = {
        apiKey: "AIzaSyDXQoekC8zki3ECJxxr3ubfhRk5tCZBCps",
        authDomain: "eventhubi1.firebaseapp.com",
        projectId: "eventhubi1",
        storageBucket: "eventhubi1.firebasestorage.app",
        messagingSenderId: "620234482550",
        appId: "1:620234482550:web:6814aee8f31a093aca1057",
        measurementId: "G-36E52RL50J"
    };

    // Initialize Firebase
    const app = initializeApp(firebaseConfig);
    const auth = getAuth(app);

    // Check if user is authenticated with Firebase
    auth.onAuthStateChanged(function(user) {
        if (user) {
            // Enable password update form if user is authenticated
            const passwordForm = document.getElementById('password-update-form');
            if (passwordForm) {
                passwordForm.style.display = 'block';
            }
        } else {
            // Hide password update form if no user
            const passwordForm = document.getElementById('password-update-form');
            if (passwordForm) {
                passwordForm.style.display = 'none';
            }
        }
    });



    // Message display function
    function showMessage(message, type = 'error') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
        
        // Use simple text symbols instead of Font Awesome icons to avoid display issues
        const icon = type === 'success' ? '✓' : '⚠';
        alertDiv.innerHTML = `
            <span class="me-2 fw-bold">${icon}</span>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Insert at the top of the form
        const form = document.getElementById('password-update-form');
        form.parentNode.insertBefore(alertDiv, form);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    // Password Validation Functions for Profile
    window.validateProfilePassword = function(password) {
        const requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /\d/.test(password),
            special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
        };

        // Update requirement indicators
        Object.keys(requirements).forEach(req => {
            const element = document.getElementById(`profile-req-${req}`);
            if (element) {
                const iconSpan = element.querySelector('.req-icon');
                if (iconSpan) {
                    if (requirements[req]) {
                        element.classList.add('valid');
                        iconSpan.textContent = '✅';
                        iconSpan.className = 'req-icon text-success me-1';
                    } else {
                        element.classList.remove('valid');
                        iconSpan.textContent = '❌';
                        iconSpan.className = 'req-icon text-danger me-1';
                    }
                }
            }
        });

        // Calculate password strength
        const validCount = Object.values(requirements).filter(Boolean).length;
        const strengthBar = document.getElementById('profile-password-strength-bar');
        const strengthText = document.getElementById('profile-password-strength-text');

        if (validCount <= 2) {
            strengthBar.className = 'progress-bar weak';
            strengthText.textContent = 'Weak';
            strengthText.className = 'text-danger';
        } else if (validCount <= 3) {
            strengthBar.className = 'progress-bar medium';
            strengthText.textContent = 'Medium';
            strengthText.className = 'text-warning';
        } else if (validCount <= 4) {
            strengthBar.className = 'progress-bar strong';
            strengthText.textContent = 'Strong';
            strengthText.className = 'text-success';
        } else {
            strengthBar.className = 'progress-bar very-strong';
            strengthText.textContent = 'Very Strong';
            strengthText.className = 'text-success';
        }

        // Store password validation state
        window.profilePasswordValid = validCount === 5;
        
        // Validate password confirmation if it exists
        if (document.getElementById('confirm-password').value) {
            validateProfilePasswordConfirmation();
        }
    };

    window.validateProfilePasswordConfirmation = function() {
        const password = document.getElementById('new-password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        const matchDiv = document.getElementById('profile-password-match');
        const mismatchDiv = document.getElementById('profile-password-mismatch');

        if (confirmPassword === '') {
            matchDiv.style.display = 'none';
            mismatchDiv.style.display = 'none';
            return;
        }

        if (password === confirmPassword) {
            matchDiv.style.display = 'block';
            mismatchDiv.style.display = 'none';
            window.profilePasswordsMatch = true;
        } else {
            matchDiv.style.display = 'none';
            mismatchDiv.style.display = 'block';
            window.profilePasswordsMatch = false;
        }
    };

    // Enhanced password update with validation
    window.handlePasswordUpdate = async function(event) {
        event.preventDefault();

        // Check password validation
        if (!window.profilePasswordValid) {
            showMessage('Please ensure your new password meets all requirements.');
            return;
        }
        
        // Check password confirmation
        if (!window.profilePasswordsMatch) {
            showMessage('New passwords do not match. Please try again.');
            return;
        }

        const form = event.target;
        const currentPassword = form.querySelector('#current-password').value;
        const newPassword = form.querySelector('#new-password').value;
        const confirmPassword = form.querySelector('#confirm-password').value;

        if (newPassword !== confirmPassword) {
            showMessage('New passwords do not match. Please try again.');
            return;
        }

        if (newPassword.length < 8) {
            showMessage('New password must be at least 8 characters long.');
            return;
        }

        try {
            showMessage('Updating password...', 'success');

            // Get current user
            const user = auth.currentUser;
            if (!user) {
                showMessage('No user found. Please sign in again.');
                return;
            }

            // Re-authenticate user to verify current password
            const credential = EmailAuthProvider.credential(user.email, currentPassword);
            await reauthenticateWithCredential(user, credential);

            // Update password in Firebase
            await updatePassword(user, newPassword);

            showMessage('Password updated successfully!', 'success');

            // Clear form fields
            form.reset();

            // Reset validation indicators
            document.getElementById('profile-password-strength-bar').className = 'progress-bar';
            document.getElementById('profile-password-strength-text').textContent = 'Weak';
            document.getElementById('profile-password-strength-text').className = 'text-danger';
            document.getElementById('profile-password-match').style.display = 'none';
            document.getElementById('profile-password-mismatch').style.display = 'none';

        } catch (error) {
            let errorMessage = 'Error updating password: ';

            switch (error.code) {
                case 'auth/requires-recent-login':
                    errorMessage += 'This operation requires a recent login. Please sign in again.';
                    break;
                case 'auth/wrong-password':
                    errorMessage += 'Incorrect current password. Please try again.';
                    break;
                case 'auth/invalid-credential':
                    errorMessage += 'Invalid credential. Please sign in again.';
                    break;
                default:
                    errorMessage += error.message;
            }

            showMessage(errorMessage);
        }
    };
</script>
@endsection 