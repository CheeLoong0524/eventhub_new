<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FirebaseAuthController extends Controller
{
    /**
     * Handle Firebase authentication callback
     */
    public function callback(Request $request)
    {
        try {
            Log::info('Firebase authentication callback received', $request->all());
            
            // Extract user info from the request
            $email = $request->input('email');
            $name = $request->input('name');
            $uid = $request->input('uid');
            $role = $request->input('role'); // Role might not be sent for existing users
            $authType = $request->input('auth_type', 'oauth'); // 'oauth' or 'firebase_email'
            
            Log::info('Received user data', [
                'email' => $email,
                'name' => $name,
                'uid' => $uid,
                'role' => $role,
                'has_role' => $request->has('role'),
                'auth_type' => $authType
            ]);
            
            // Find existing user by Firebase UID first (primary lookup)
            $user = User::findByFirebaseUid($uid);
            
            if ($user) {
                Log::info('Found existing Firebase user', ['user_id' => $user->id, 'firebase_uid' => $uid, 'current_role' => $user->role]);
                
                // Update existing user profile data, but NEVER role or auth_method
                $user->update([
                    'name' => $name,
                    'email' => $email, // Update email if changed in Firebase
                ]);
                Log::info('Updated existing Firebase user profile', ['user_id' => $user->id, 'kept_role' => $user->role, 'kept_auth_method' => $user->auth_method]);
            } else {
                // Check if user exists by email (migration case)
                $user = User::where('email', $email)->first();
                
                if ($user) {
                    Log::info('Found existing user by email, linking Firebase UID', ['user_id' => $user->id, 'email' => $email]);
                    
                    // Link existing user with Firebase UID
                    $user->update([
                        'firebase_uid' => $uid,
                        'name' => $name,
                        'auth_method' => $authType,
                    ]);
                    Log::info('Linked existing user with Firebase', ['user_id' => $user->id, 'firebase_uid' => $uid]);
                } else {
                    Log::info('Creating new Firebase user', ['email' => $email, 'role' => $role, 'auth_type' => $authType]);
                    
                    // Create new user using factory
                    if (!$role) {
                        Log::error('No role provided for new user');
                        return response()->json(['error' => 'Role is required for new users'], 400);
                    }
                    
                    $user = \App\Factories\UserFactory::createFirebaseUser([
                        'uid' => $uid,
                        'name' => $name,
                        'email' => $email,
                        'role' => $role,
                        'auth_type' => $authType,
                    ]);
                    Log::info('Created new Firebase user via factory', ['user_id' => $user->id, 'firebase_uid' => $uid, 'role' => $role, 'auth_method' => $authType]);
                }
            }

            // Log in the user
            Auth::login($user);
            Log::info('User logged in successfully', ['user_id' => $user->id]);

            // Get redirect URL based on user role
            $redirectUrl = $this->getRedirectUrl($user);
            Log::info('Redirect URL determined', ['redirect_url' => $redirectUrl]);

            return response()->json([
                'success' => true,
                'user' => $user,
                'redirect_url' => $redirectUrl,
                'message' => 'Authentication successful'
            ]);

        } catch (\Exception $e) {
            Log::error('Authentication failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Authentication failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get redirect URL based on user role
     */
    private function getRedirectUrl(User $user): string
    {
        try {
            Log::info('Determining redirect URL for user', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'is_admin' => $user->isAdmin(),
                'is_vendor' => $user->isVendor(),
                'is_customer' => $user->isCustomer()
            ]);
            
            if ($user->isAdmin()) {
                $url = route('admin.dashboard');
                Log::info('Admin user, redirecting to', ['url' => $url]);
                return $url;
            } elseif ($user->isVendor()) {
                $url = route('vendor.dashboard');
                Log::info('Vendor user, redirecting to', ['url' => $url]);
                return $url;
            } else {
                $url = route('customer.dashboard');
                Log::info('Customer user, redirecting to', ['url' => $url]);
                return $url;
            }
        } catch (\Exception $e) {
            Log::error('Error generating redirect URL', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'error' => $e->getMessage()
            ]);
            // Fallback to home page
            return route('home');
        }
    }

    /**
     * Logout user from Laravel session
     */
    public function logout(Request $request)
    {
        Log::info('User logout requested');
        
        // Logout from Laravel
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Always redirect to home page after logout
        return redirect()->route('home')->with('success', 'Logged out successfully');
    }

    /**
     * Get current authenticated user
     */
    public function user(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        return response()->json([
            'user' => $user,
            'role' => $user->role
        ]);
    }

    /**
     * Check if user exists by email (no auth required)
     */
    public function checkUserExists(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email'
            ]);

            $email = $request->input('email');
            $user = User::where('email', $email)->first();

            if ($user) {
                Log::info('User exists check', ['email' => $email, 'exists' => true, 'role' => $user->role]);
                return response()->json([
                    'exists' => true,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role
                    ]
                ]);
            } else {
                Log::info('User exists check', ['email' => $email, 'exists' => false]);
                return response()->json([
                    'exists' => false
                ]);
            }
        } catch (\Exception $e) {
            Log::error('User exists check failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Check failed'], 500);
        }
    }


}
