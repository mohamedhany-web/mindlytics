<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_task_deliverables', function (Blueprint $table) {
            if (! Schema::hasColumn('employee_task_deliverables', 'duration_before_minutes')) {
                $table->unsignedInteger('duration_before_minutes')->nullable()->after('duration_before');
            }
            if (! Schema::hasColumn('employee_task_deliverables', 'duration_after_minutes')) {
                $table->unsignedInteger('duration_after_minutes')->nullable()->after('duration_after');
            }
            if (! Schema::hasColumn('employee_task_deliverables', 'link_url_hash')) {
                $table->string('link_url_hash', 64)->nullable()->after('link_url');
            }
        });

        Schema::table('employee_task_deliverables', function (Blueprint $table) {
            if (Schema::hasColumn('employee_task_deliverables', 'link_url_hash')) {
                $table->index(['task_id', 'link_url_hash'], 'etd_task_link_hash_idx');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_task_deliverables', function (Blueprint $table) {
            try {
                $table->dropIndex('etd_task_link_hash_idx');
            } catch (\Throwable) {
                //
            }
        });

        Schema::table('employee_task_deliverables', function (Blueprint $table) {
            foreach (['duration_before_minutes', 'duration_after_minutes', 'link_url_hash'] as $col) {
                if (Schema::hasColumn('employee_task_deliverables', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
