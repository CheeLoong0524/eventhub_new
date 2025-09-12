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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->date('date');
            $table->time('time');
            $table->string('venue');
            $table->string('location');
            $table->string('category');
            $table->string('image_url')->nullable();
            $table->enum('status', ['draft', 'published', 'cancelled'])->default('published');
            $table->integer('max_attendees')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->boolean('is_featured')->default(false);
            $table->integer('popularity_score')->default(0);
            $table->timestamps();

            $table->index(['date', 'status']);
            $table->index(['category', 'status']);
            $table->index(['is_featured', 'status']);
            $table->index('popularity_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

