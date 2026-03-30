<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('offline_course_sections')) {
            Schema::create('offline_course_sections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('offline_course_id')->constrained('offline_courses')->cascadeOnDelete();
                $table->foreignId('parent_id')->nullable()->constrained('offline_course_sections')->cascadeOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                $table->unsignedInteger('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->index(['offline_course_id', 'parent_id', 'order']);
            });
        }

        if (! Schema::hasTable('offline_curriculum_notes')) {
            Schema::create('offline_curriculum_notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('offline_course_id')->constrained('offline_courses')->cascadeOnDelete();
                $table->string('title');
                $table->text('body')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('offline_curriculum_items')) {
            Schema::create('offline_curriculum_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('offline_course_section_id')->constrained('offline_course_sections')->cascadeOnDelete();
                $table->string('item_type');
                $table->unsignedBigInteger('item_id');
                $table->unsignedInteger('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->index(['offline_course_section_id', 'order']);
                $table->index(['item_type', 'item_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('offline_curriculum_items');
        Schema::dropIfExists('offline_curriculum_notes');
        Schema::dropIfExists('offline_course_sections');
    }
};
