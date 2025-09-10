<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            // Drop old columns that are no longer needed
            $table->dropColumn([
                'company_name',
                'company_description', 
                'business_registration_number',
                'contact_person',
                'contact_phone',
                'contact_email',
                'business_address',
                'service_type',
                'service_description',
                'service_categories',
                'website',
                'social_media',
                'documents'
            ]);
            
            // Add new columns for the updated application form
            $table->string('business_name')->after('user_id');
            $table->string('business_type')->after('business_name');
            $table->text('business_description')->after('business_type');
            $table->string('business_phone')->after('business_description');
            $table->string('business_email')->after('business_phone');
            $table->string('years_in_business')->after('business_email');
            $table->string('business_size')->after('years_in_business');
            $table->string('annual_revenue')->after('business_size');
            $table->string('event_experience')->after('annual_revenue');
            $table->string('product_category')->after('event_experience');
            $table->string('target_audience')->after('product_category');
            $table->text('marketing_strategy')->after('target_audience');
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn([
                'business_name',
                'business_type',
                'business_description',
                'business_phone',
                'business_email',
                'years_in_business',
                'business_size',
                'annual_revenue',
                'event_experience',
                'product_category',
                'target_audience',
                'marketing_strategy'
            ]);
            
            // Restore old columns
            $table->string('company_name')->after('user_id');
            $table->text('company_description')->nullable()->after('company_name');
            $table->string('business_registration_number')->unique()->after('company_description');
            $table->string('contact_person')->after('business_registration_number');
            $table->string('contact_phone')->after('contact_person');
            $table->string('contact_email')->after('contact_phone');
            $table->text('business_address')->after('contact_email');
            $table->enum('service_type', ['food', 'equipment', 'decoration', 'entertainment', 'logistics', 'other'])->after('business_address');
            $table->text('service_description')->after('service_type');
            $table->json('service_categories')->nullable()->after('service_description');
            $table->string('website')->nullable()->after('service_categories');
            $table->json('social_media')->nullable()->after('website');
            $table->json('documents')->nullable()->after('social_media');
        });
    }
};
