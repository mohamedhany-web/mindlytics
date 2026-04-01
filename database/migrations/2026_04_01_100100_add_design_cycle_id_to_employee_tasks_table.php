<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_tasks', function (Blueprint $table) {
            if (! Schema::hasColumn('employee_tasks', 'design_cycle_id')) {
                $table->foreignId('design_cycle_id')
                    ->nullable()
                    ->after('notes')
                    ->constrained('design_task_cycles')
                    ->nullOnDelete();
                $table->index('design_cycle_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('employee_tasks', 'design_cycle_id')) {
                $table->dropForeign(['design_cycle_id']);
                $table->dropColumn('design_cycle_id');
            }
        });
    }
};
