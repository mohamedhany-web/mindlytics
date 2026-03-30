<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_path_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->string('status', 32)->nullable()->default('pending');
            $table->boolean('is_verified_purchase')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('helpful_count')->default(0);
            $table->timestamps();

            $table->unique(['academic_year_id', 'user_id']);
            $table->index(['academic_year_id', 'is_approved']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_path_reviews');
    }
};

