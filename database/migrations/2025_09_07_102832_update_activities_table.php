<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            // Add duration if not exists
            if (!Schema::hasColumn('activities', 'duration')) {
                $table->integer('duration')->nullable()->after('start_time');
            }

            // Add status if not exists
            if (!Schema::hasColumn('activities', 'status')) {
                $table->enum('status', ['pending', 'in_progress', 'completed'])
                      ->default('pending')
                      ->after('duration');
            }

            // Add venue_id foreign key if not exists
            if (!Schema::hasColumn('activities', 'venue_id')) {
                $table->unsignedBigInteger('venue_id')->nullable()->after('status');

                $table->foreign('venue_id')
                      ->references('id')
                      ->on('venues')
                      ->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            if (Schema::hasColumn('activities', 'venue_id')) {
                $table->dropForeign(['venue_id']);
                $table->dropColumn('venue_id');
            }

            $table->dropColumn(['duration', 'status']);
        });
    }
};
