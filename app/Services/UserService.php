<?php

namespace App\Services;

use App\Models\User;
use App\Factories\UserFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class UserService
{

    /**
     * Get user authentication statistics (Internal Service)
     * IFA: User Analytics Service
     */
    public function getUserAuthStats()
    {
        $cacheKey = 'user_auth_stats';
        
        return Cache::remember($cacheKey, 300, function () {
            $totalUsers = User::count();
            $activeUsers = User::where('is_active', true)->count();
            $inactiveUsers = User::where('is_active', false)->count();
            
            $roleStats = User::selectRaw('role, COUNT(*) as count')
                ->groupBy('role')
                ->pluck('count', 'role')
                ->toArray();
            
            $authMethodStats = User::selectRaw('auth_method, COUNT(*) as count')
                ->groupBy('auth_method')
                ->pluck('count', 'auth_method')
                ->toArray();
            
            $recentLogins = User::whereNotNull('last_login_at')
                ->where('last_login_at', '>=', now()->subDays(7))
                ->count();
            
            return [
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'inactive_users' => $inactiveUsers,
                'role_distribution' => $roleStats,
                'auth_method_distribution' => $authMethodStats,
                'recent_logins_7_days' => $recentLogins,
                'generated_at' => now()->toISOString()
            ];
        });
    }

    /**
     * Validate user data for creation (Internal Service)
     * IFA: User Validation Service
     */
    public function validateUserData(array $data, string $authMethod = 'laravel')
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,vendor,customer',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500'
        ];

        if ($authMethod === 'laravel') {
            $rules['password'] = 'required|string|min:8';
        } else {
            $rules['firebase_uid'] = 'required|string|unique:users,firebase_uid';
            $rules['auth_method'] = 'required|in:firebase_email,oauth';
        }

        return $rules;
    }

    /**
     * Clear user-related cache (Internal Service)
     */
    public function clearUserCache($userId = null)
    {
        // Clear user authentication statistics cache
        Cache::forget('user_auth_stats');
        
        // Add other cache keys as needed for JSON-based APIs
        if ($userId) {
            Cache::forget('user_data_' . $userId);
        } else {
            Cache::forget('users_data_all');
        }
    }
}
