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
        Schema::dropIfExists('vendor_booth_bookings');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the table if needed (for rollback)
        Schema::create('vendor_booth_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->unsignedBigInteger('event_id');
            $table->string('booth_number');
            $table->string('booth_type');
            $table->decimal('booth_size', 8, 2);
            $table->decimal('price', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'paid', 'cancelled', 'completed'])->default('pending');
            $table->timestamp('booking_date')->nullable();
            $table->timestamp('event_start_date')->nullable();
            $table->timestamp('event_end_date')->nullable();
            $table->text('special_requirements')->nullable();
            $table->json('equipment_included')->nullable();
            $table->json('additional_services')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'refunded'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->timestamps();
            $table->index(['vendor_id', 'status']);
            $table->index(['event_id', 'status']);
            $table->index('booking_date');
        });
    }
};
