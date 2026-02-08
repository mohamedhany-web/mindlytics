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
        if (!Schema::hasTable('course_sections')) {
            Schema::create('course_sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('advanced_course_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('advanced_course_id')->references('id')->on('advanced_courses')->onDelete('cascade');
            $table->index(['advanced_course_id', 'order']);
            });
        }

        // جدول curriculum_items لربط العناصر (محاضرات، دروس، واجبات) بالأقسام
        if (!Schema::hasTable('curriculum_items')) {
            Schema::create('curriculum_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_section_id');
            $table->string('item_type');
            $table->unsignedBigInteger('item_id');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('course_section_id')->references('id')->on('course_sections')->onDelete('cascade');
            $table->index(['course_section_id', 'order']);
            $table->index(['item_type', 'item_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curriculum_items');
        Schema::dropIfExists('course_sections');
    }
};
