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
            // Booth pricing and availability
            $table->decimal('booth_price', 10, 2)->nullable()->comment('Price per booth');
            $table->integer('booth_quantity')->default(0)->comment('Total number of booths available');
            $table->integer('booth_sold')->default(0)->comment('Number of booths sold');
            
            // Ticket pricing and availability
            $table->decimal('ticket_price', 10, 2)->nullable()->comment('Price per ticket');
            $table->integer('ticket_quantity')->default(0)->comment('Total number of tickets available');
            $table->integer('ticket_sold')->default(0)->comment('Number of tickets sold');
            
            // Event costs for profit/loss calculation
            $table->decimal('venue_cost', 10, 2)->nullable()->comment('Cost of venue rental');
            $table->decimal('staff_cost', 10, 2)->nullable()->comment('Staff costs');
            $table->decimal('equipment_cost', 10, 2)->nullable()->comment('Equipment costs');
            $table->decimal('marketing_cost', 10, 2)->nullable()->comment('Marketing costs');
            $table->decimal('other_costs', 10, 2)->nullable()->comment('Other miscellaneous costs');
            
            // Event revenue tracking
            $table->decimal('total_revenue', 10, 2)->default(0)->comment('Total revenue from booths and tickets');
            $table->decimal('total_costs', 10, 2)->default(0)->comment('Total costs for the event');
            $table->decimal('net_profit', 10, 2)->default(0)->comment('Net profit (revenue - costs)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'booth_price', 'booth_quantity', 'booth_sold',
                'ticket_price', 'ticket_quantity', 'ticket_sold',
                'venue_cost', 'staff_cost', 'equipment_cost', 'marketing_cost', 'other_costs',
                'total_revenue', 'total_costs', 'net_profit'
            ]);
        });
    }
};
