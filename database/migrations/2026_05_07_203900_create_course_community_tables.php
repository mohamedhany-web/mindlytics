<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_community_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('advanced_courses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->boolean('is_pinned')->default(false);
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();

            $table->index(['course_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('course_community_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('course_community_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();

            $table->index(['post_id', 'created_at']);
        });

        Schema::create('course_community_reactions', function (Blueprint $table) {
            $table->id();
            $table->morphs('reactable'); // reactable_type, reactable_id
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type', 24)->default('like'); // future-proof
            $table->timestamps();

            $table->unique(['reactable_type', 'reactable_id', 'user_id', 'type'], 'cc_reactions_unique');
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_community_reactions');
        Schema::dropIfExists('course_community_comments');
        Schema::dropIfExists('course_community_posts');
    }
};

