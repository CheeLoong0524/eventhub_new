<?php

// Author  : Choong Yoong Sheng (Vendor module)


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_event_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('booth_size')->nullable();
            $table->integer('booth_quantity')->default(1);
            $table->enum('service_type', ['food', 'equipment', 'decoration', 'entertainment', 'logistics', 'other']);
            $table->text('service_description');
            $table->json('service_categories')->nullable();
            $table->decimal('requested_price', 10, 2)->nullable();
            $table->decimal('approved_price', 10, 2)->nullable();
            $table->text('special_requirements')->nullable();
            $table->json('equipment_needed')->nullable();
            $table->json('additional_services')->nullable();
            $table->enum('status', ['pending', 'under_review', 'approved', 'paid', 'rejected', 'cancelled'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->index(['vendor_id', 'status']);
            $table->index(['event_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->unique(['vendor_id', 'event_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_event_applications');
    }
};


