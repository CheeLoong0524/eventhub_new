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
        Schema::table('events', function (Blueprint $table) {
            // Remove start_date and end_date columns
            $table->dropColumn(['start_date', 'end_date']);
            
            // Revert start_time and end_time back to datetime
            $table->dateTime('start_time')->change();
            $table->dateTime('end_time')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Add back start_date and end_date columns
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            
            // Revert back to time type
            $table->time('start_time')->change();
            $table->time('end_time')->change();
        });
    }
};
