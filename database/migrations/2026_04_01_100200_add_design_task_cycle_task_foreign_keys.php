<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('design_task_cycles', function (Blueprint $table) {
            if (Schema::hasTable('employee_tasks')) {
                $table->foreign('designer_task_id')
                    ->references('id')
                    ->on('employee_tasks')
                    ->nullOnDelete();
                $table->foreign('moderator_delivery_task_id')
                    ->references('id')
                    ->on('employee_tasks')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('design_task_cycles', function (Blueprint $table) {
            $table->dropForeign(['designer_task_id']);
            $table->dropForeign(['moderator_delivery_task_id']);
        });
    }
};
