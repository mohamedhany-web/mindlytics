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
        if (!Schema::hasTable('academic_year_instructors')) {
            Schema::create('academic_year_instructors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade')->comment('ID المسار التعليمي');
                $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade')->comment('ID المدرب');
                $table->json('assigned_courses')->nullable()->comment('الكورسات المخصصة لهذا المدرب في المسار');
                $table->text('notes')->nullable()->comment('ملاحظات');
                $table->timestamps();
                
                $table->unique(['academic_year_id', 'instructor_id'], 'unique_year_instructor');
                $table->index('academic_year_id');
                $table->index('instructor_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_year_instructors');
    }
};
