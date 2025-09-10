<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the status enum to include 'paid'
        DB::statement("ALTER TABLE vendor_event_applications MODIFY COLUMN status ENUM('pending', 'under_review', 'approved', 'paid', 'rejected', 'cancelled') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the status enum to exclude 'paid'
        DB::statement("ALTER TABLE vendor_event_applications MODIFY COLUMN status ENUM('pending', 'under_review', 'approved', 'rejected', 'cancelled') DEFAULT 'pending'");
    }
};
