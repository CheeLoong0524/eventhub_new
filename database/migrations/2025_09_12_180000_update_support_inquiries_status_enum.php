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
        // First, add 'pending' to the existing enum
        DB::statement("ALTER TABLE support_inquiries MODIFY COLUMN status ENUM('open', 'in_progress', 'resolved', 'closed', 'pending') DEFAULT 'open'");
        
        // Then update existing records to new status values
        DB::statement("UPDATE support_inquiries SET status = 'pending' WHERE status = 'open'");
        DB::statement("UPDATE support_inquiries SET status = 'pending' WHERE status = 'in_progress'");
        
        // Finally, remove the old enum values
        DB::statement("ALTER TABLE support_inquiries MODIFY COLUMN status ENUM('pending', 'resolved', 'closed') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the status enum to original values
        DB::statement("ALTER TABLE support_inquiries MODIFY COLUMN status ENUM('open', 'in_progress', 'resolved', 'closed') DEFAULT 'open'");
        
        // Revert existing records
        DB::statement("UPDATE support_inquiries SET status = 'open' WHERE status = 'pending'");
    }
};
