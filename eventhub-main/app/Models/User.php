<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'firebase_uid',
        'auth_method',
        'role',
        'phone',
        'address',
        'is_active',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a vendor
     */
    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    /**
     * Check if user is a customer
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }



    /**
     * Check if user uses Firebase email/password authentication
     */
    public function usesFirebaseEmail(): bool
    {
        return $this->auth_method === 'firebase_email';
    }

    /**
     * Check if user uses OAuth (Google, etc.)
     */
    public function usesOAuth(): bool
    {
        return $this->auth_method === 'oauth';
    }

    /**
     * Check if user uses Laravel authentication
     */
    public function usesLaravelAuth(): bool
    {
        return $this->auth_method === 'laravel';
    }

    /**
     * Check if user can change password (only Firebase email users)
     */
    public function canChangePassword(): bool
    {
        return $this->usesFirebaseEmail();
    }

    /**
     * Check if user is Firebase-managed (not Laravel auth)
     */
    public function isFirebaseManaged(): bool
    {
        return $this->auth_method !== 'laravel';
    }

    /**
     * Find user by Firebase UID
     */
    public static function findByFirebaseUid(string $uid): ?User
    {
        return static::where('firebase_uid', $uid)->first();
    }

    /**
     * Scope for Firebase-managed users
     */
    public function scopeFirebaseManaged($query)
    {
        return $query->where('auth_method', '!=', 'laravel');
    }

    /**
     * Scope for Laravel-managed users (admin only)
     */
    public function scopeLaravelManaged($query)
    {
        return $query->where('auth_method', 'laravel');
    }

    /**
     * Get the primary identifier for the user
     * Firebase users use firebase_uid, Laravel users use id
     */
    public function getPrimaryIdentifier(): string
    {
        return $this->isFirebaseManaged() ? $this->firebase_uid : (string) $this->id;
    }
}
