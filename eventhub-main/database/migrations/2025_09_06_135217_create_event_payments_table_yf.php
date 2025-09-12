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
        Schema::create('event_payments_yf', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('event_orders_yf')->onDelete('cascade');
            $table->string('payment_method'); // stripe, paypal, bank_transfer, etc.
            $table->string('payment_gateway_transaction_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('MYR');
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled', 'refunded'])->default('pending');
            $table->json('gateway_response')->nullable(); // Store gateway response data
            $table->timestamp('processed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_payments_yf');
    }
};
