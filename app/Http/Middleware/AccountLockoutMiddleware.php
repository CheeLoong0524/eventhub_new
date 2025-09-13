<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AccountLockoutMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $email = $request->input('email');
        $ip = $request->ip();
        
        if ($email) {
            // Check for account lockout
            $lockoutKey = "account_lockout_{$email}";
            $ipLockoutKey = "ip_lockout_{$ip}";
            
            // Check if account is locked
            if (Cache::has($lockoutKey)) {
                $lockoutTime = Cache::get($lockoutKey);
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
            if (Cache::has($ipLockoutKey)) {
                $lockoutTime = Cache::get($ipLockoutKey);
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
        }
        
        return $next($request);
    }
    
    /**
     * Record failed login attempt
     */
    public static function recordFailedAttempt(string $email, string $ip): void
    {
        $maxAttempts = 5; // Maximum failed attempts before lockout
        $lockoutDuration = 300; // 5 minutes lockout
        
        // Track failed attempts for email
        $emailKey = "failed_attempts_{$email}";
        $emailAttempts = Cache::get($emailKey, 0) + 1;
        
        if ($emailAttempts >= $maxAttempts) {
            // Lock account
            Cache::put("account_lockout_{$email}", time() + $lockoutDuration, $lockoutDuration);
            Cache::forget($emailKey);
            
            Log::warning('Account locked due to failed attempts', [
                'email' => $email,
                'attempts' => $emailAttempts,
                'ip' => $ip
            ]);
        } else {
            Cache::put($emailKey, $emailAttempts, $lockoutDuration);
        }
        
        // Track failed attempts for IP
        $ipKey = "failed_attempts_ip_{$ip}";
        $ipAttempts = Cache::get($ipKey, 0) + 1;
        
        if ($ipAttempts >= $maxAttempts) {
            // Lock IP
            Cache::put("ip_lockout_{$ip}", time() + $lockoutDuration, $lockoutDuration);
            Cache::forget($ipKey);
            
            Log::warning('IP locked due to failed attempts', [
                'email' => $email,
                'attempts' => $ipAttempts,
                'ip' => $ip
            ]);
        } else {
            Cache::put($ipKey, $ipAttempts, $lockoutDuration);
        }
    }
    
    /**
     * Clear failed attempts on successful login
     */
    public static function clearFailedAttempts(string $email, string $ip): void
    {
        Cache::forget("failed_attempts_{$email}");
        Cache::forget("failed_attempts_ip_{$ip}");
        Cache::forget("account_lockout_{$email}");
        Cache::forget("ip_lockout_{$ip}");
    }
}
