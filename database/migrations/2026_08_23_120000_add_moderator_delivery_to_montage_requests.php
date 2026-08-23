<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('moderator_montage_requests', function (Blueprint $table) {
            $table->foreignId('moderator_delivery_task_id')
                ->nullable()
                ->after('employee_task_id')
                ->constrained('employee_tasks')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('moderator_montage_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('moderator_delivery_task_id');
        });
    }
};
