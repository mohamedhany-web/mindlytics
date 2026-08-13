<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moderator_montage_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('moderator_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('montage_employee_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('requirements');
            $table->string('priority', 20)->default('medium');
            $table->timestamp('deadline_at');
            $table->string('status', 40)->default('pending');
            $table->foreignId('employee_task_id')->nullable()->constrained('employee_tasks')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['moderator_id', 'status']);
            $table->index(['montage_employee_id', 'status']);
            $table->index('deadline_at');
        });

        Schema::table('employee_tasks', function (Blueprint $table) {
            $table->foreignId('montage_request_id')
                ->nullable()
                ->after('marketing_event_id')
                ->constrained('moderator_montage_requests')
                ->nullOnDelete();
            $table->boolean('flexible_video_delivery')
                ->default(false)
                ->after('montage_request_id');
        });
    }

    public function down(): void
    {
        Schema::table('employee_tasks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('montage_request_id');
            $table->dropColumn('flexible_video_delivery');
        });

        Schema::dropIfExists('moderator_montage_requests');
    }
};
