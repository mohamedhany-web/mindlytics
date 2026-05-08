<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_community_post_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('course_community_posts')->cascadeOnDelete();
            $table->string('path', 512);
            $table->string('disk', 32)->default('r2');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['post_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_community_post_images');
    }
};
