<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_course_board_entries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('audience')->nullable();
            $table->string('instructor_name')->nullable();
            $table->string('start_label')->nullable();
            $table->string('schedule_days')->nullable();
            $table->string('duration')->nullable();
            $table->string('hours')->nullable();
            $table->decimal('price_online', 10, 2)->nullable();
            $table->decimal('price_recorded', 10, 2)->nullable();
            $table->string('format')->nullable();
            $table->text('summary')->nullable();
            $table->longText('landing_details')->nullable();
            $table->json('highlights')->nullable();
            $table->unsignedBigInteger('advanced_course_id')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('landing_published')->default(false);
            $table->timestamps();

            $table->foreign('advanced_course_id')
                ->references('id')
                ->on('advanced_courses')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_course_board_entries');
    }
};
