<?php

namespace App\Services;

use App\Models\User;
use App\Factories\UserFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class UserService
{
    /**
     * Generate XML for all users (Internal Service)
     * IFA: User Information Service
     */
    public function generateUsersXml()
    {
        $cacheKey = 'users_xml_' . md5('all_users');
        
        return Cache::remember($cacheKey, 60, function () {
            $users = User::with(['vendor'])->get();
            
            $xml = new \SimpleXMLElement('<users/>');
            
            foreach ($users as $user) {
                $userXml = $xml->addChild('user');
                $userXml->addChild('user_id', $user->id);
                $userXml->addChild('name', htmlspecialchars($user->name));
                $userXml->addChild('email', htmlspecialchars($user->email));
                $userXml->addChild('role', $user->role);
                $userXml->addChild('auth_method', $user->auth_method);
                $userXml->addChild('is_active', $user->is_active ? '1' : '0');
                $userXml->addChild('phone', htmlspecialchars($user->phone ?? ''));
                $userXml->addChild('address', htmlspecialchars($user->address ?? ''));
                $userXml->addChild('created_at', $user->created_at->toISOString());
                $userXml->addChild('last_login_at', $user->last_login_at ? $user->last_login_at->toISOString() : '');
                
                // Add vendor info if exists
                if ($user->vendor) {
                    $vendorXml = $userXml->addChild('vendor');
                    $vendorXml->addChild('vendor_id', $user->vendor->id);
                    $vendorXml->addChild('business_name', htmlspecialchars($user->vendor->business_name));
                    $vendorXml->addChild('status', $user->vendor->status);
                }
            }
            
            return $xml;
        });
    }

    /**
     * Generate XML for single user (Internal Service)
     * IFA: User Detail Service
     */
    public function generateUserXml($userId)
    {
        $cacheKey = 'user_xml_' . $userId;
        
        return Cache::remember($cacheKey, 30, function () use ($userId) {
            $user = User::with(['vendor'])->find($userId);
            
            if (!$user) {
                $xml = new \SimpleXMLElement('<error/>');
                $xml->addChild('message', 'User not found');
                $xml->addChild('user_id', $userId);
                return $xml;
            }
            
            $xml = new \SimpleXMLElement('<user/>');
            $xml->addChild('user_id', $user->id);
            $xml->addChild('name', htmlspecialchars($user->name));
            $xml->addChild('email', htmlspecialchars($user->email));
            $xml->addChild('role', $user->role);
            $xml->addChild('auth_method', $user->auth_method);
            $xml->addChild('is_active', $user->is_active ? '1' : '0');
            $xml->addChild('phone', htmlspecialchars($user->phone ?? ''));
            $xml->addChild('address', htmlspecialchars($user->address ?? ''));
            $xml->addChild('created_at', $user->created_at->toISOString());
            $xml->addChild('last_login_at', $user->last_login_at ? $user->last_login_at->toISOString() : '');
            
            // Add vendor info if exists
            if ($user->vendor) {
                $vendorXml = $xml->addChild('vendor');
                $vendorXml->addChild('vendor_id', $user->vendor->id);
                $vendorXml->addChild('business_name', htmlspecialchars($user->vendor->business_name));
                $vendorXml->addChild('status', $user->vendor->status);
            }
            
            return $xml;
        });
    }

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
        if ($userId) {
            Cache::forget('user_xml_' . $userId);
        } else {
            Cache::forget('users_xml_' . md5('all_users'));
        }
        
        Cache::forget('user_auth_stats');
    }
}
