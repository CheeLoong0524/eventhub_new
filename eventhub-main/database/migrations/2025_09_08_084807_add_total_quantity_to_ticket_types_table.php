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
        Schema::table('ticket_types', function (Blueprint $table) {
            $table->integer('total_quantity')->default(0)->after('available_quantity');
        });
        
        // Populate total_quantity with current available_quantity + sold_quantity
        DB::statement('UPDATE ticket_types SET total_quantity = available_quantity + sold_quantity');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_types', function (Blueprint $table) {
            $table->dropColumn('total_quantity');
        });
    }
};