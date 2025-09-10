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
        // Drop the existing unique constraint to allow multiple applications per vendor-event pair
        // We'll handle uniqueness in the application logic instead
        try {
            Schema::table('vendor_event_applications', function (Blueprint $table) {
                $table->dropUnique(['vendor_id', 'event_id']);
            });
        } catch (\Exception $e) {
            // If the unique constraint doesn't exist, try dropping it by name
            try {
                DB::statement('ALTER TABLE vendor_event_applications DROP INDEX vendor_event_applications_vendor_id_event_id_unique');
            } catch (\Exception $e2) {
                // If that doesn't work either, the constraint might not exist
                // Continue with the migration
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the original unique constraint
        Schema::table('vendor_event_applications', function (Blueprint $table) {
            $table->unique(['vendor_id', 'event_id']);
        });
    }
};
