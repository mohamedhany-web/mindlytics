<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learn_discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('advanced_courses')->cascadeOnDelete();
            $table->string('context_type', 32); // lecture|assignment|exam|pattern
            $table->unsignedBigInteger('context_id');
            $table->string('kind', 20); // discussion|qa
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('learn_discussions')->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();

            $table->index(['course_id', 'context_type', 'context_id', 'kind'], 'learn_disc_ctx_idx');
            $table->index(['parent_id']);
            $table->index(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learn_discussions');
    }
};
