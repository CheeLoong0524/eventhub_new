<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Show admin login form 
     */
    public function showLoginForm()
    {
        // Redirect if already logged in as admin
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.auth.login');
    }

    /**
     * Handle admin login authentication through Laravel and local mysql database
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $email = $credentials['email'];
        $ip = $request->ip();
        
        // Check for account lockout before processing
        $lockoutKey = "account_lockout_{$email}";
        $ipLockoutKey = "ip_lockout_{$ip}";
        
        // Check if account is locked
        if (cache()->has($lockoutKey)) {
            $lockoutTime = cache()->get($lockoutKey);
            $remainingTime = $lockoutTime - time();
            
            if ($remainingTime > 0) {
                Log::warning('Account lockout attempt', [
                    'email' => $email,
                    'ip' => $ip,
                    'remaining_time' => $remainingTime
                ]);
                
                return redirect()->route('admin.login')
                    ->withErrors([
                        'email' => "Account temporarily locked. Please try again in {$remainingTime} seconds."
                    ]);
            }
        }
        
        // Check for IP lockout
        if (cache()->has($ipLockoutKey)) {
            $lockoutTime = cache()->get($ipLockoutKey);
            $remainingTime = $lockoutTime - time();
            
            if ($remainingTime > 0) {
                Log::warning('IP lockout attempt', [
                    'email' => $email,
                    'ip' => $ip,
                    'remaining_time' => $remainingTime
                ]);
                
                return redirect()->route('admin.login')
                    ->withErrors([
                        'email' => "Too many failed attempts from this IP. Please try again in {$remainingTime} seconds."
                    ]);
            }
        }
        
        // Check if user exists and is admin
        $user = User::where('email', $credentials['email'])->first();
        
        if (!$user || !$user->isAdmin() || !$user->is_active) {
            throw ValidationException::withMessages([
                'email' => 'Invalid credentials or account access denied.',
            ]);
        }

        // Attempt to authenticate
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Update last login information (Practice #52)
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);
            
            // Clear any failed attempts (Practice #41)
            cache()->forget("failed_attempts_{$email}");
            cache()->forget("failed_attempts_ip_{$ip}");
            cache()->forget("account_lockout_{$email}");
            cache()->forget("ip_lockout_{$ip}");
            
            Log::info('Admin login successful', [
                'user_id' => $user->id,
                'email' => $email,
                'ip' => $request->ip(),
                'last_login' => $user->last_login_at
            ]);
            
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }

        // Record failed attempt (Practice #41)
        $maxAttempts = 5; // Maximum failed attempts before lockout
        $lockoutDuration = 300; // 5 minutes lockout
        
        // Track failed attempts for email
        $emailKey = "failed_attempts_{$email}";
        $emailAttempts = cache()->get($emailKey, 0) + 1;
        
        if ($emailAttempts >= $maxAttempts) {
            // Lock account
            cache()->put("account_lockout_{$email}", time() + $lockoutDuration, $lockoutDuration);
            cache()->forget($emailKey);
            
            Log::warning('Account locked due to failed attempts', [
                'email' => $email,
                'attempts' => $emailAttempts,
                'ip' => $ip
            ]);
        } else {
            cache()->put($emailKey, $emailAttempts, $lockoutDuration);
        }
        
        // Track failed attempts for IP
        $ipKey = "failed_attempts_ip_{$ip}";
        $ipAttempts = cache()->get($ipKey, 0) + 1;
        
        if ($ipAttempts >= $maxAttempts) {
            // Lock IP
            cache()->put("ip_lockout_{$ip}", time() + $lockoutDuration, $lockoutDuration);
            cache()->forget($ipKey);
            
            Log::warning('IP locked due to failed attempts', [
                'email' => $email,
                'attempts' => $ipAttempts,
                'ip' => $ip
            ]);
        } else {
            cache()->put($ipKey, $ipAttempts, $lockoutDuration);
        }
        
        Log::warning('Admin login failed', [
            'email' => $email,
            'ip' => $request->ip()
        ]);

        throw ValidationException::withMessages([
            'email' => 'Invalid credentials or account access denied.',
        ]);
    }

    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        
        return redirect()->route('admin.login')
            ->with('success', 'You have been successfully logged out.');
    }

    /**
     * Show admin dashboard (redirect if not admin)
     */
    public function dashboard()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect()->route('admin.login');
        }
        
        return view('admin.dashboard');
    }
}
