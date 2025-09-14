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
            // Remove rarely used fields
            $table->dropColumn([
                'equipment_needed',        // 設備需求 - 很少使用
                'additional_services',     // 額外服務 - 很少使用
                'base_amount',             // 基礎金額 - 可以簡化
                'tax_amount',              // 稅額 - 可以簡化
                'service_charge_amount',   // 服務費 - 可以簡化
                'final_amount',            // 最終金額 - 可以簡化
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_event_applications', function (Blueprint $table) {
            // Restore the removed columns
            $table->json('equipment_needed')->nullable();
            $table->json('additional_services')->nullable();
            $table->decimal('base_amount', 10, 2)->nullable()->comment('Base booth price before tax and service charge');
            $table->decimal('tax_amount', 10, 2)->nullable()->comment('Tax amount (6%)');
            $table->decimal('service_charge_amount', 10, 2)->nullable()->comment('Service charge amount (RM 10)');
            $table->decimal('final_amount', 10, 2)->nullable()->comment('Final amount paid including tax and service charge');
        });
    }
};
