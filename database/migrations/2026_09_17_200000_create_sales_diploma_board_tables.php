<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_diploma_board_entries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('audience')->nullable();
            $table->string('instructor_name')->nullable();
            $table->string('start_label')->nullable();
            $table->date('starts_at')->nullable();
            $table->string('schedule_days')->nullable();
            $table->string('duration')->nullable();
            $table->string('hours')->nullable();
            $table->decimal('price_online', 10, 2)->nullable();
            $table->decimal('price_recorded', 10, 2)->nullable();
            $table->string('format')->nullable();
            $table->text('summary')->nullable();
            $table->longText('landing_details')->nullable();
            $table->json('highlights')->nullable();
            $table->json('booking_methods')->nullable();
            $table->json('free_lecture_links')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('landing_published')->default(false);
            $table->timestamps();

            $table->foreign('academic_year_id')
                ->references('id')
                ->on('academic_years')
                ->nullOnDelete();
        });

        Schema::create('sales_diploma_board_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_diploma_board_entry_id')
                ->constrained('sales_diploma_board_entries')
                ->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('referer', 2048)->nullable();
            $table->string('accept_language', 255)->nullable();
            $table->string('session_id', 120)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['sales_diploma_board_entry_id', 'created_at'], 'diploma_visits_entry_created_idx');
            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_diploma_board_visits');
        Schema::dropIfExists('sales_diploma_board_entries');
    }
};
