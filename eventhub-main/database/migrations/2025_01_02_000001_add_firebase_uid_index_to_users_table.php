<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add index on firebase_uid for faster lookups
            $table->index('firebase_uid');
            
            // Add index on auth_method for filtering
            $table->index('auth_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['firebase_uid']);
            $table->dropIndex(['auth_method']);
        });
    }
};
