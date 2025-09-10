<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@eventhub.com',
            'password' => Hash::make('admin123456'),
            'role' => 'admin',
            'auth_method' => 'laravel', // Admin uses Laravel authentication
            'phone' => '+60123456789',
            'address' => 'TARUMT Main Campus, Kuala Lumpur',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create sample users for testing
        User::create([
            'name' => 'John Vendor',
            'email' => 'vendor@example.com',
            'password' => Hash::make('vendor123456'),
            'role' => 'vendor',
            'auth_method' => 'laravel', // Test vendor uses Laravel authentication
            'phone' => '+60123456788',
            'address' => '123 Vendor Street, KL',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Jane Customer',
            'email' => 'customer@example.com',
            'password' => Hash::make('customer123456'),
            'role' => 'customer',
            'auth_method' => 'laravel', // Test customer uses Laravel authentication
            'phone' => '+60123456787',
            'address' => '456 Customer Avenue, KL',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Admin and sample users created successfully!');
        $this->command->info('Admin credentials: admin@eventhub.com / admin123456');
        $this->command->info('Vendor credentials: vendor@example.com / vendor123456');
        $this->command->info('Customer credentials: customer@example.com / customer123456');
    }
} 