<?php

namespace App\Factories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserFactory
{
    /**
     * Create a Firebase authenticated user , only for all non-admin users
     */
    public static function createFirebaseUser(array $data): User
    {
        // Validate required fields for Firebase users
        $requiredFields = ['uid', 'name', 'email', 'role', 'auth_type'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new \InvalidArgumentException("Field '{$field}' is required for Firebase user");
            }
        }

        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email format");
        }

        // Validate role either vendor or customer
        $validRoles = ['vendor', 'customer']; // Admin users are created via Laravel
        if (!in_array($data['role'], $validRoles)) {
            throw new \InvalidArgumentException("Invalid role for Firebase user. Must be one of: " . implode(', ', $validRoles));
        }

        // Validate auth type either firebase_email or oauth
        $validAuthTypes = ['firebase_email', 'oauth'];
        if (!in_array($data['auth_type'], $validAuthTypes)) {
            throw new \InvalidArgumentException("Invalid auth type. Must be one of: " . implode(', ', $validAuthTypes));
        }

        // Check if Firebase UID already exists
        if (User::where('firebase_uid', $data['uid'])->exists()) {
            throw new \InvalidArgumentException("Firebase UID already exists");
        }

        // Check if email already exists in database
        if (User::where('email', $data['email'])->exists()) {
            throw new \InvalidArgumentException("Email already exists");
        }

        // Create Firebase user with all the given data
        return User::create([
            'firebase_uid' => $data['uid'],
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'auth_method' => $data['auth_type'],
            'email_verified_at' => now(),
            'is_active' => true,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);
    }

    /**
     * Create an admin user (through Laravel authentication only)
     * This method is ONLY for creating admin users through the admin panel by root admin
     */
    public static function createAdminUser(array $data): User
    {
        // Validate required fields for admin users
        $requiredFields = ['name', 'email', 'password'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new \InvalidArgumentException("Field '{$field}' is required for admin user");
            }
        }

        // Validate email format similar with above
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email format");
        }

        // Check if email already exists in database similar with above
        if (User::where('email', $data['email'])->exists()) {
            throw new \InvalidArgumentException("Email already exists");
        }

        // Force admin role for this method
        $data['role'] = 'admin';

        // Create admin user with Laravel authentication
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'admin', // Always admin for this method
            'auth_method' => 'laravel',
            'is_active' => true,
            'email_verified_at' => now(),
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);
    }

    /**
     * Update existing Firebase user (for profile updates)
     */
    public static function updateFirebaseUser(User $user, array $data): User
    {
        // Only allow updating profile data, not authentication data
        $allowedFields = ['name', 'phone', 'address'];
        $updateData = array_intersect_key($data, array_flip($allowedFields));
        
        if (empty($updateData)) {
            throw new \InvalidArgumentException("No valid fields to update");
        }

        $user->update($updateData);
        return $user->fresh();
    }

    /**
     * Find user by Firebase UID
     */
    public static function findByFirebaseUid(string $uid): ?User
    {
        return User::where('firebase_uid', $uid)->first();
    }

    /**
     * Find user by email (for backward compatibility)
     */
    public static function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Check if user is Firebase-managed
     */
    public static function isFirebaseManaged(User $user): bool
    {
        return $user->auth_method !== 'laravel';
    }

    /**
     * Legacy method for backward compatibility (Admin UserController)
     */
    public static function createUserWithValidation(array $data, string $role = 'customer'): User
    {
        if ($role === 'admin') {
            return self::createAdminUser($data);
        }
        
        throw new \InvalidArgumentException("Use createFirebaseUser() for non-admin users");
    }
} 