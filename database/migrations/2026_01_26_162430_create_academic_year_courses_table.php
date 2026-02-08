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
        if (!Schema::hasTable('academic_year_courses')) {
            Schema::create('academic_year_courses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade')->comment('ID المسار التعليمي');
                $table->foreignId('advanced_course_id')->constrained('advanced_courses')->onDelete('cascade')->comment('ID الكورس');
                $table->integer('order')->default(0)->comment('ترتيب الكورس في المسار');
                $table->boolean('is_required')->default(true)->comment('هل الكورس إجباري');
                $table->timestamps();
                
                $table->unique(['academic_year_id', 'advanced_course_id'], 'unique_year_course');
                $table->index('academic_year_id');
                $table->index('advanced_course_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_year_courses');
    }
};
