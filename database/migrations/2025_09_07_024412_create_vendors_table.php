<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('company_name');
            $table->text('company_description')->nullable();
            $table->string('business_registration_number')->unique();
            $table->string('contact_person');
            $table->string('contact_phone');
            $table->string('contact_email');
            $table->text('business_address');
            $table->enum('service_type', ['food', 'equipment', 'decoration', 'entertainment', 'logistics', 'other']);
            $table->text('service_description');
            $table->json('service_categories')->nullable();
            $table->string('website')->nullable();
            $table->json('social_media')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->json('documents')->nullable();
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('total_events')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            $table->index(['status', 'service_type']);
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};


