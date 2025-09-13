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
        Schema::create('support_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('inquiry_id')->unique(); // Unique inquiry ID (e.g., INQ-2025-001)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Nullable for guest users
            $table->string('name');
            $table->string('email');
            $table->string('subject');
            $table->text('message');
            $table->enum('category', ['general', 'technical', 'billing', 'event', 'other'])->default('general');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->text('admin_reply')->nullable(); // Admin's reply to the customer
            $table->timestamp('resolved_at')->nullable(); // When inquiry was resolved
            $table->foreignId('resolved_by')->nullable()->constrained('users'); // Admin who resolved it
            $table->text('admin_notes')->nullable(); // Internal notes from admin
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['status']);
            $table->index(['user_id', 'created_at']);
            $table->index('inquiry_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_inquiries');
    }
};
