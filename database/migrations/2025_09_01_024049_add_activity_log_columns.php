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
        Schema::table('activity_logs', function (Blueprint $table) {
            // إضافة حقول جديدة للمراقبة الشاملة
            if (!Schema::hasColumn('activity_logs', 'description')) {
                $table->text('description')->nullable()->after('action');
            }
            if (!Schema::hasColumn('activity_logs', 'url')) {
                $table->text('url')->nullable()->after('user_agent');
            }
            if (!Schema::hasColumn('activity_logs', 'method')) {
                $table->string('method', 10)->nullable()->after('url');
            }
            if (!Schema::hasColumn('activity_logs', 'response_code')) {
                $table->integer('response_code')->nullable()->after('method');
            }
            if (!Schema::hasColumn('activity_logs', 'duration')) {
                $table->integer('duration')->nullable()->comment('مدة الطلب بالميلي ثانية');
            }
            if (!Schema::hasColumn('activity_logs', 'session_id')) {
                $table->string('session_id')->nullable()->after('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $columnsToRemove = [];
            if (Schema::hasColumn('activity_logs', 'description')) $columnsToRemove[] = 'description';
            if (Schema::hasColumn('activity_logs', 'url')) $columnsToRemove[] = 'url';
            if (Schema::hasColumn('activity_logs', 'method')) $columnsToRemove[] = 'method';
            if (Schema::hasColumn('activity_logs', 'response_code')) $columnsToRemove[] = 'response_code';
            if (Schema::hasColumn('activity_logs', 'duration')) $columnsToRemove[] = 'duration';
            if (Schema::hasColumn('activity_logs', 'session_id')) $columnsToRemove[] = 'session_id';
            
            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }
};
