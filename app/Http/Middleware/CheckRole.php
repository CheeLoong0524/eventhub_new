<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('auth.firebase');
        }

        // Check if user has the required role
        if (!Auth::user()->hasRole($role)) {
            return redirect()->route('auth.firebase');
        }

        // Check if user is active
        if (!Auth::user()->is_active) {
            return redirect()->route('auth.firebase');
        }

        return $next($request);
    }
} 