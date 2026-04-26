<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ربط موارد الكورس بمحاضراته (Many-to-Many)
     */
    public function up(): void
    {
        Schema::create('offline_lecture_resource', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offline_lecture_id')->constrained('offline_lectures')->onDelete('cascade');
            $table->foreignId('offline_course_resource_id')->constrained('offline_course_resources')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['offline_lecture_id', 'offline_course_resource_id'], 'olr_unique');
            $table->index(['offline_course_resource_id', 'offline_lecture_id'], 'olr_resource_lecture_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offline_lecture_resource');
    }
};

