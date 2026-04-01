<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_task_deliverables', function (Blueprint $table) {
            if (! Schema::hasColumn('employee_task_deliverables', 'file_disk')) {
                $table->string('file_disk', 32)->nullable()->after('file_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_task_deliverables', function (Blueprint $table) {
            if (Schema::hasColumn('employee_task_deliverables', 'file_disk')) {
                $table->dropColumn('file_disk');
            }
        });
    }
};
