@extends('layouts.app')

@section('title', 'Sign In - EventHub')

@section('styles')
<style>
    .firebase-auth-container {
        max-width: 600px;
        margin: 3rem auto;
        padding: 0 1rem;
    }
    
    .auth-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .auth-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 2rem 1.5rem;
        text-align: center;
    }
    
    .auth-card .card-header h4 {
        font-weight: 600;
        margin: 0;
        font-size: 1.75rem;
    }
    
    .auth-tabs {
        display: flex;
        margin: 0;
        border-bottom: 1px solid #e9ecef;
        background-color: #f8f9fa;
    }
    
    .auth-tab {
        flex: 1;
        padding: 1.25rem 1rem;
        text-align: center;
        cursor: pointer;
        border: none;
        background: transparent;
        color: #6c757d;
        font-weight: 500;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .auth-tab.active {
        color: #667eea;
        background-color: white;
        border-bottom: 3px solid #667eea;
    }
    
    .auth-tab:hover:not(.active) {
        background-color: #e9ecef;
        color: #495057;
    }
    
    .auth-content {
        display: none;
        padding: 2rem 1.5rem;
    }
    
    .auth-content.active {
        display: block;
        animation: fadeIn 0.5s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .firebase-ui-container {
        min-height: 350px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        border-radius: 12px;
        margin: 1rem 0;
    }
    
    .loading-spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #667eea;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
        border-radius: 50%;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .error-message {
        color: #dc3545;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        text-align: center;
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.1);
    }

    .success-message {
        color: #155724;
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        text-align: center;
        box-shadow: 0 2px 8px rgba(25, 135, 84, 0.1);
    }
    
    .role-selection-modal .modal-content {
        border: none;
        border-radius: 16px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    }
    
    .role-selection-modal .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 16px 16px 0 0;
    }
    
    .role-option {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
    }
    
    .role-option:hover {
        border-color: #667eea;
        background-color: #f8f9fa;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
    }
    
    .role-option.selected {
        border-color: #667eea;
        background-color: #f0f4ff;
    }
    
    .role-option i {
        font-size: 2.5rem;
        color: #667eea;
        margin-bottom: 1rem;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 8px;
        padding: 0.75rem 2rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .btn-outline-primary {
        border-color: #667eea;
        color: #667eea;
        border-radius: 8px;
        padding: 0.75rem 2rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-outline-primary:hover {
        background-color: #667eea;
        border-color: #667eea;
        transform: translateY(-1px);
    }
    
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
    
    @media (max-width: 768px) {
        .firebase-auth-container {
            margin: 1rem auto;
        }
        
        .auth-card .card-header {
            padding: 1.5rem 1rem;
        }
        
        .auth-content {
            padding: 1.5rem 1rem;
        }
        
        .auth-tab {
            padding: 1rem 0.5rem;
            font-size: 0.9rem;
        }
    }
</style>
@endsection

@section('content')
<div class="firebase-auth-container">
    <div class="card auth-card">
        <div class="card-header">
            <h4>
                <i class="fas fa-calendar-alt me-2"></i>Welcome to EventHub
            </h4>
            <p class="mb-0 mt-2 opacity-75">Sign in or create your account to get started</p>
        </div>
        
        <div class="card-body p-0">
            <!-- Error/Success Messages -->
            <div id="message-container"></div>
            
            <!-- Sign Out Button (shown when user is signed in) -->
            <div id="signout-section" style="display: none;" class="text-center mb-3">
                <p class="text-muted">You are currently signed in</p>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="signOut()">
                    <i class="fas fa-sign-out-alt me-2"></i>Sign Out
                </button>
            </div>

            <!-- Auth Tabs -->
            <div class="auth-tabs">
                <div class="auth-tab active" data-tab="signin">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </div>
                <div class="auth-tab" data-tab="signup">
                    <i class="fas fa-user-plus me-2"></i>Sign Up
                </div>
            </div>
            
            <!-- Sign In Content -->
            <div class="auth-content active" id="signin-content">
                <div class="row">
                    <div class="col-md-6">
                        <div class="text-center mb-4">
                            <h5 class="mb-3">Sign In with Google</h5>
                            <button type="button" class="btn btn-primary btn-lg w-100" onclick="signInWithGoogle()">
                                <i class="fab fa-google me-2"></i>Continue with Google
                            </button>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="text-center mb-3">
                            <h5 class="mb-3">Sign In with Email</h5>
                        </div>
                        <form id="signin-form" onsubmit="handleEmailSignIn(event)">
                            <div class="mb-3">
                                <input type="email" class="form-control" id="signin-email" name="email" 
                                       placeholder="Email Address" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control" id="signin-password" name="password" 
                                       placeholder="Password" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In
                            </button>
                        </form>
                        
                        <!-- Forgot Password Link -->
                        <div class="text-center mt-3">
                            <a href="#" onclick="handleForgotPassword()" class="text-decoration-none text-muted">
                                <small><i class="fas fa-question-circle me-1"></i>Forgot your password?</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sign Up Content -->
            <div class="auth-content" id="signup-content">
                <div class="row">
                    <div class="col-md-6">
                        <div class="text-center mb-4">
                            <h5 class="mb-3">Sign Up with Google</h5>
                            <button type="button" class="btn btn-primary btn-lg w-100" onclick="signInWithGoogle()">
                                <i class="fab fa-google me-2"></i>Continue with Google
                            </button>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="text-center mb-3">
                            <h5 class="mb-3">Sign Up with Email</h5>
                        </div>
                        <form id="signup-form" onsubmit="handleEmailSignUp(event)">
                            <div class="mb-3">
                                <input type="text" class="form-control" id="signup-name" name="name" 
                                       placeholder="Full Name" required>
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control" id="signup-email" name="email" 
                                       placeholder="Email Address" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control" id="signup-password" name="password" 
                                       placeholder="Password" required oninput="validatePassword(this.value)">
                                <div class="password-strength mt-2">
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar" id="password-strength-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Password strength: <span id="password-strength-text">Weak</span></small>
                                </div>
                                <div class="password-requirements mt-2" id="password-requirements">
                                    <small class="text-muted">Requirements:</small>
                                    <ul class="list-unstyled mt-1 mb-0">
                                        <li id="req-length"><span class="req-icon text-danger me-1">❌</span>At least 8 characters</li>
                                        <li id="req-uppercase"><span class="req-icon text-danger me-1">❌</span>Contains uppercase letter</li>
                                        <li id="req-lowercase"><span class="req-icon text-danger me-1">❌</span>Contains lowercase letter</li>
                                        <li id="req-number"><span class="req-icon text-danger me-1">❌</span>Contains number</li>
                                        <li id="req-special"><span class="req-icon text-danger me-1">❌</span>Contains special character</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control" id="signup-confirm-password" name="confirm-password" 
                                       placeholder="Confirm Password" required oninput="validatePasswordConfirmation()">
                                <div class="password-match mt-2" id="password-match" style="display: none;">
                                    <small class="text-success"><span class="me-1">✅</span>Passwords match</small>
                                </div>
                                <div class="password-mismatch mt-2" id="password-mismatch" style="display: none;">
                                    <small class="text-danger"><span class="me-1">❌</span>Passwords do not match</small>
                                </div>
                            </div>
                            <div class="mb-3">
                                <select class="form-control" id="signup-role" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="customer">Customer</option>
                                    <option value="vendor">Vendor</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-user-plus me-2"></i>Create Account
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Firebase SDK -->
<script type="module">
  // Import the functions you need from the SDKs you need
  import { initializeApp } from "https://www.gstatic.com/firebasejs/12.2.1/firebase-app.js";
  import { getAuth, signInWithPopup, GoogleAuthProvider, updateProfile } from "https://www.gstatic.com/firebasejs/12.2.1/firebase-auth.js";
  import { getAnalytics } from "https://www.gstatic.com/firebasejs/12.2.1/firebase-analytics.js";

  // Your web app's Firebase configuration
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
  
  // Initialize Analytics only in production
  let analytics;
  if (window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
    analytics = getAnalytics(app);
  }

  // Show message function
  function showMessage(message, type = 'error') {
    const container = document.getElementById('message-container');
    container.innerHTML = `<div class="${type === 'error' ? 'error-message' : 'success-message'}">${message}</div>`;
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
      container.innerHTML = '';
    }, 5000);
  }

  // Initialize Firebase UI when page loads
  document.addEventListener('DOMContentLoaded', function() {
      // Clear any leftover new user role
      if (window.newUserRole) {
          window.newUserRole = null;
      }
      
      // Set up tab switching functionality
      document.querySelectorAll('.auth-tab').forEach(tab => {
          tab.addEventListener('click', function() {
              const targetTab = this.getAttribute('data-tab');
              
              // Remove active class from all tabs and content
              document.querySelectorAll('.auth-tab').forEach(t => {
                  t.classList.remove('active');
              });
              document.querySelectorAll('.auth-content').forEach(c => {
                  c.classList.remove('active');
              });
              
              // Add active class to clicked tab and corresponding content
              this.classList.add('active');
              
              const targetContent = document.getElementById(targetTab + '-content');
              if (targetContent) {
                  targetContent.classList.add('active');
              } else {
                  console.error('❌ Target content not found:', targetTab + '-content');
              }
          });
      });
      
      // Sign out any previous user to start fresh
      auth.signOut().then(() => {
      }).catch((error) => {
      });
      
      // Development mode logging
      if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
          console.log('🔥 Firebase running in development mode');
          console.log('📍 Make sure localhost is added to Firebase authorized domains');
      }
  });

  // Firebase Auth State Observer
  auth.onAuthStateChanged(function(user) {
      const signoutSection = document.getElementById('signout-section');
      
      if (user) {
          // User is signed in
          signoutSection.style.display = 'block';
          
          // Only proceed if this is a fresh sign-in (not auto-login)
          if (window.freshSignIn) {
              showMessage(`Welcome back, ${user.displayName || user.email}!`, 'success');
              
              // Get the ID token
              user.getIdToken().then(function(idToken) {
                  // Send token to Laravel backend
                  authenticateWithLaravel(idToken);
              }).catch(function(error) {
                  console.error('Error getting ID token:', error);
                  showMessage('Error getting authentication token: ' + error.message);
              });
          } else {
          }
      } else {
          // User is signed out
          signoutSection.style.display = 'none';
          window.freshSignIn = false; // Reset flag
      }
  });

  // Google Sign In
  window.signInWithGoogle = function() {
      const provider = new GoogleAuthProvider();
      signInWithPopup(auth, provider)
          .then((result) => {
              
              // Check if this is a new user by looking at creation time
              const isNewUser = result.user.metadata.creationTime === result.user.metadata.lastSignInTime;
              
              // Always check our database to be sure
              checkUserInDatabase(result.user);
          })
          .catch((error) => {
              let errorMessage = 'Error signing in with Google: ';
              
              switch(error.code) {
                  case 'auth/unauthorized-domain':
                      errorMessage += 'This domain is not authorized. Please add localhost to Firebase authorized domains.';
                      break;
                  case 'auth/popup-closed-by-user':
                      errorMessage += 'Sign-in popup was closed. Please try again.';
                      break;
                  case 'auth/popup-blocked':
                      errorMessage += 'Pop-up was blocked by browser. Please allow pop-ups for this site.';
                      break;
                  case 'auth/cancelled-popup-request':
                      errorMessage += 'Sign-in was cancelled. Please try again.';
                      break;
                  default:
                      errorMessage += error.message;
              }
              
              showMessage(errorMessage);
          });
  };

  // Check if user exists in our database
  function checkUserInDatabase(firebaseUser) {
      
      fetch('/auth/check-user', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
              email: firebaseUser.email
          })
      })
      .then(response => response.json())
      .then(data => {
          
          if (data.exists && data.user) {
              // User exists in database, proceed with authentication
              window.freshSignIn = true;
              showMessage('Successfully signed in with Google!', 'success');
              
              // Authenticate with Laravel backend
              setTimeout(() => {
                  authenticateWithLaravel();
              }, 1000);
          } else {
              // User doesn't exist in database, show role selection
              showRoleSelectionModal(firebaseUser);
          }
      })
      .catch(error => {
          // If we can't check the database, assume new user and show role selection
          showRoleSelectionModal(firebaseUser);
      });
  }

  // Show role selection modal for Google sign-in users
  function showRoleSelectionModal(user) {
      
      const modalHtml = `
          <div class="modal fade" id="roleModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
              <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header">
                          <h5 class="modal-title">Welcome to EventHub!</h5>
                      </div>
                      <div class="modal-body">
                          <p>Hi <strong>${user.displayName || user.email}</strong>! Please select your account type:</p>
                          <div class="mb-3">
                              <label for="googleRole" class="form-label">Account Type <span class="text-danger">*</span></label>
                              <select class="form-select" id="googleRole" required>
                                  <option value="">Select account type</option>
                                  <option value="customer">Customer - I want to attend events</option>
                                  <option value="vendor">Vendor - I want to create and manage events</option>
                              </select>
                              <div class="form-text">This selection cannot be changed later</div>
                          </div>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-primary" onclick="confirmGoogleRole()" id="confirmRoleBtn" disabled>
                              Continue
                          </button>
                      </div>
                  </div>
              </div>
          </div>
      `;
      
      // Remove existing modal if any
      const existingModal = document.getElementById('roleModal');
      if (existingModal) {
          existingModal.remove();
      }
      
      // Add modal to page
      document.body.insertAdjacentHTML('beforeend', modalHtml);
      
      // Wait for DOM to be ready, then set up event listeners
      setTimeout(() => {
          const roleSelect = document.getElementById('googleRole');
          const confirmBtn = document.getElementById('confirmRoleBtn');
          
          if (roleSelect && confirmBtn) {
              // Enable/disable continue button based on selection
              roleSelect.addEventListener('change', function() {
                  confirmBtn.disabled = !this.value;
              });
              
              // Show modal
              const modal = new bootstrap.Modal(document.getElementById('roleModal'));
              modal.show();
          } else {
              console.error('Modal elements not found');
          }
      }, 100);
      
      // Store user for later use
      window.googleUser = user;
  }

  // Confirm role selection for Google sign-in
  window.confirmGoogleRole = function() {
      const role = document.getElementById('googleRole').value;
      
      if (!role) {
          showMessage('Please select an account type');
          return;
      }
      
      // Store the role for this new user
      window.newUserRole = role;
      window.freshSignIn = true;
      
      // Hide the modal
      const modal = bootstrap.Modal.getInstance(document.getElementById('roleModal'));
      modal.hide();
      
      showMessage('Account created successfully!', 'success');
      
      setTimeout(() => {
          authenticateWithLaravel();
      }, 1000);
  };

  // Authenticate with Laravel backend
  function authenticateWithLaravel(idToken = null) {
      const user = auth.currentUser;
      if (!user) {
          showMessage('No user found. Please try signing in again.');
          return;
      }
      
      if (!idToken) {
          user.getIdToken().then(function(token) {
              authenticateWithLaravel(token);
          }).catch(function(error) {
              console.error('Error getting ID token:', error);
              showMessage('Error getting authentication token: ' + error.message);
          });
          return;
      }
      
      const userData = {
          idToken: idToken,
          email: user.email,
          name: user.displayName || user.email,
          uid: user.uid
      };
      
      // Add role for new users (either from email/password signup or Google signup)
      if (window.newUserRole) {
          userData.role = window.newUserRole;
      } else if (user.role && user.role !== 'customer') {
          userData.role = user.role;
      }

      // Add auth_type based on authentication method
      if (user.providerData.some(provider => provider.providerId === 'password')) {
          userData.auth_type = 'firebase_email';
      } else {
          userData.auth_type = 'oauth';
      }
      
      // Send authentication data to Laravel
      fetch('/auth/firebase/callback', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify(userData)
      })
      .then(response => {
          return response.json();
      })
      .then(data => {
          
          if (data.success) {
              showMessage('Authentication successful! Redirecting...', 'success');
              
              // Clear the new user role after successful authentication
              if (window.newUserRole) {
                  window.newUserRole = null;
              }
              
              setTimeout(() => {
                  window.location.href = data.redirect_url;
              }, 1500);
          } else {
              console.error('Authentication failed:', data.error);
              showMessage('Authentication failed: ' + data.error);
          }
      })
      .catch(error => {
          console.error('Network error:', error);
          showMessage('Error connecting to server. Please try again.');
      });
  }

  // Sign out function
  window.signOut = function() {
      auth.signOut().then(() => {
          showMessage('Signed out successfully!', 'success');
          window.location.reload();
      }).catch((error) => {
          console.error('Error signing out:', error);
          showMessage('Error signing out: ' + error.message);
      });
  };

  // Email/Password Sign Up
  window.handleEmailSignUp = async function(event) {
      event.preventDefault();
      
      const form = event.target;
      const email = form.querySelector('#signup-email').value;
      const password = form.querySelector('#signup-password').value;
      const name = form.querySelector('#signup-name').value;
      const role = form.querySelector('#signup-role').value;
      
      if (!role) {
          showMessage('Please select a role for your account.');
          return;
      }
      
      try {
          showMessage('Creating your account...', 'success');
          
          // Create user in Firebase
          const { createUserWithEmailAndPassword } = await import("https://www.gstatic.com/firebasejs/12.2.1/firebase-auth.js");
          const userCredential = await createUserWithEmailAndPassword(auth, email, password);
          const user = userCredential.user;
          
          // Update profile with name
          await updateProfile(user, { displayName: name });
          
          // Store role for Laravel authentication
          window.newUserRole = role;
          
          // Set fresh sign-in flag
          window.freshSignIn = true;
          
          showMessage('Account created successfully! Authenticating...', 'success');
          
          // Authenticate with Laravel
          setTimeout(() => {
              authenticateWithLaravel();
          }, 1000);
          
      } catch (error) {
          let errorMessage = 'Error creating account: ';
          
          switch (error.code) {
              case 'auth/email-already-in-use':
                  errorMessage += 'This email is already registered. Please sign in instead.';
                  break;
              case 'auth/invalid-email':
                  errorMessage += 'Please enter a valid email address.';
                  break;
              case 'auth/weak-password':
                  errorMessage += 'Password should be at least 6 characters long.';
                  break;
              default:
                  errorMessage += error.message;
          }
          
          showMessage(errorMessage);
      }
  };

  // Email/Password Sign In
  window.handleEmailSignIn = async function(event) {
      event.preventDefault();
      
      const form = event.target;
      const email = form.querySelector('#signin-email').value;
      const password = form.querySelector('#signin-password').value;
      
      try {
          showMessage('Signing you in...', 'success');
          
          // Sign in with Firebase
          const { signInWithEmailAndPassword } = await import("https://www.gstatic.com/firebasejs/12.2.1/firebase-auth.js");
          const userCredential = await signInWithEmailAndPassword(auth, email, password);
          const user = userCredential.user;
          
          // Set fresh sign-in flag
          window.freshSignIn = true;
          
          showMessage('Sign in successful! Authenticating...', 'success');
          
          // Authenticate with Laravel
          setTimeout(() => {
              authenticateWithLaravel();
          }, 1000);
          
      } catch (error) {
          let errorMessage = 'Error signing in: ';
          
          switch (error.code) {
              case 'auth/user-not-found':
                  errorMessage += 'No account found with this email. Please sign up first.';
                  break;
              case 'auth/wrong-password':
                  errorMessage += 'Incorrect password. Please try again.';
                  break;
              case 'auth/invalid-email':
                  errorMessage += 'Please enter a valid email address.';
                  break;
              case 'auth/user-disabled':
                  errorMessage += 'This account has been disabled. Please contact support.';
                  break;
              default:
                  errorMessage += error.message;
          }
          
          showMessage(errorMessage);
      }
  };

  // Forgot Password
  window.handleForgotPassword = async function() {
      const email = document.getElementById('signin-email').value;
      
      if (!email) {
          showMessage('Please enter your email address first.');
          return;
      }

      try {
          showMessage('Sending password reset email...', 'success');
          
          // Send password reset email via Firebase
          const { sendPasswordResetEmail } = await import("https://www.gstatic.com/firebasejs/12.2.1/firebase-auth.js");
          await sendPasswordResetEmail(auth, email);
          
          showMessage('Password reset email sent! Please check your inbox.', 'success');
          
      } catch (error) {
          let errorMessage = 'Error sending password reset email: ';
          
          switch (error.code) {
              case 'auth/user-not-found':
                  errorMessage += 'No account found with this email address.';
                  break;
              case 'auth/invalid-email':
                  errorMessage += 'Please enter a valid email address.';
                  break;
              default:
                  errorMessage += error.message;
          }
          
          showMessage(errorMessage);
      }
  };

  // Password Validation Functions
  window.validatePassword = function(password) {
      const requirements = {
          length: password.length >= 8,
          uppercase: /[A-Z]/.test(password),
          lowercase: /[a-z]/.test(password),
          number: /\d/.test(password),
          special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
      };

              // Update requirement indicators
        Object.keys(requirements).forEach(req => {
            const element = document.getElementById(`req-${req}`);
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
      const strengthBar = document.getElementById('password-strength-bar');
      const strengthText = document.getElementById('password-strength-text');

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
          strengthText.textContent = 'text-success';
      } else {
          strengthBar.className = 'progress-bar very-strong';
          strengthText.textContent = 'Very Strong';
          strengthText.className = 'text-success';
      }

      // Store password validation state
      window.passwordValid = validCount === 5;
      
      // Validate password confirmation if it exists
      if (document.getElementById('signup-confirm-password').value) {
          validatePasswordConfirmation();
      }
  };

  window.validatePasswordConfirmation = function() {
      const password = document.getElementById('signup-password').value;
      const confirmPassword = document.getElementById('signup-confirm-password').value;
      const matchDiv = document.getElementById('password-match');
      const mismatchDiv = document.getElementById('password-mismatch');

      if (confirmPassword === '') {
          matchDiv.style.display = 'none';
          mismatchDiv.style.display = 'none';
          return;
      }

      if (password === confirmPassword) {
          matchDiv.style.display = 'block';
          mismatchDiv.style.display = 'none';
          window.passwordsMatch = true;
      } else {
          matchDiv.style.display = 'none';
          mismatchDiv.style.display = 'block';
          window.passwordsMatch = false;
      }
  };

  // Enhanced form submission with validation
  window.handleEmailSignUp = async function(event) {
      event.preventDefault();
      
      // Check password validation
      if (!window.passwordValid) {
          showMessage('Please ensure your password meets all requirements.');
          return;
      }
      
      // Check password confirmation
      if (!window.passwordsMatch) {
          showMessage('Passwords do not match. Please try again.');
          return;
      }

      // Continue with existing signup logic
      const form = event.target;
      const name = form.querySelector('#signup-name').value;
      const email = form.querySelector('#signup-email').value;
      const password = form.querySelector('#signup-password').value;
      const role = form.querySelector('#signup-role').value;

      if (!name || !email || !password || !role) {
          showMessage('Please fill in all required fields.');
          return;
      }

      try {
          showMessage('Creating your account...', 'success');
          
          // Create user with Firebase
          const { createUserWithEmailAndPassword } = await import("https://www.gstatic.com/firebasejs/12.2.1/firebase-auth.js");
          const userCredential = await createUserWithEmailAndPassword(auth, email, password);
          const user = userCredential.user;
          
          // Update profile with name
          await updateProfile(user, { displayName: name });
          
          // Store role for Laravel authentication
          window.newUserRole = role;
          window.newUserPassword = password;
          
          showMessage('Account created successfully! Authenticating...', 'success');
          
          // Authenticate with Laravel
          setTimeout(() => {
              authenticateWithLaravel();
          }, 1000);
          
      } catch (error) {
          let errorMessage = 'Error creating account: ';
          
          switch (error.code) {
              case 'auth/email-already-in-use':
                  errorMessage += 'This email is already registered. Please sign in instead.';
                  break;
              case 'auth/invalid-email':
                  errorMessage += 'Please enter a valid email address.';
                  break;
              case 'auth/weak-password':
                  errorMessage += 'Password should be at least 8 characters long.';
                  break;
              default:
                  errorMessage += error.message;
          }
          
          showMessage(errorMessage);
      }
  };
</script>
@endsection
