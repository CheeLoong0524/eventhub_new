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
        Schema::create('ticket_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('available_quantity');
            $table->integer('sold_quantity')->default(0);
            $table->integer('max_per_order')->nullable();
            $table->boolean('is_active')->default(true);
            $table->datetime('sale_start_date')->nullable();
            $table->datetime('sale_end_date')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'is_active']);
            $table->index(['is_active', 'sale_start_date', 'sale_end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_types');
    }
};

