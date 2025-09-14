<?php

// Author  : Choong Yoong Sheng (Vendor module)

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('company_name');
            $table->text('company_description')->nullable();
            $table->string('business_registration_number');
            $table->string('contact_person');
            $table->string('contact_phone');
            $table->string('contact_email');
            $table->text('business_address');
            $table->enum('service_type', ['food', 'equipment', 'decoration', 'entertainment', 'logistics', 'other']);
            $table->text('service_description');
            $table->json('service_categories')->nullable();
            $table->string('website')->nullable();
            $table->json('social_media')->nullable();
            $table->json('documents')->nullable();
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_applications');
    }
};


