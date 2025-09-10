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
        Schema::table('vendor_event_applications', function (Blueprint $table) {
            // Payment amounts
            $table->decimal('base_amount', 10, 2)->nullable()->comment('Base booth price before tax and service charge');
            $table->decimal('tax_amount', 10, 2)->nullable()->comment('Tax amount (6%)');
            $table->decimal('service_charge_amount', 10, 2)->nullable()->comment('Service charge amount (RM 10)');
            $table->decimal('final_amount', 10, 2)->nullable()->comment('Final amount paid including tax and service charge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_event_applications', function (Blueprint $table) {
            $table->dropColumn([
                'base_amount', 'tax_amount', 'service_charge_amount', 'final_amount'
            ]);
        });
    }
};
