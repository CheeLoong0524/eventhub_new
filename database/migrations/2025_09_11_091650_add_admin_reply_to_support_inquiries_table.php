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
        Schema::table('support_inquiries', function (Blueprint $table) {
            $table->text('admin_reply')->nullable()->after('status'); // Admin's reply to the customer
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_inquiries', function (Blueprint $table) {
            $table->dropColumn('admin_reply');
        });
    }
};
