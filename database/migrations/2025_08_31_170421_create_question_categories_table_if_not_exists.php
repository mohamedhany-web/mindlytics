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
        if (!Schema::hasTable('question_categories')) {
            Schema::create('question_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
                $table->foreignId('academic_subject_id')->constrained()->onDelete('cascade');
                $table->foreignId('parent_id')->nullable()->constrained('question_categories')->onDelete('cascade');
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['academic_year_id', 'academic_subject_id']);
                $table->index(['parent_id', 'order']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('question_categories');
    }
};
