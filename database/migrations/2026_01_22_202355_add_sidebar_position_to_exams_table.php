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
        if (!Schema::hasTable('exams')) {
            return;
        }

        Schema::table('exams', function (Blueprint $table) {
            if (!Schema::hasColumn('exams', 'sidebar_position')) {
                $table->integer('sidebar_position')->nullable()->after('is_active')->comment('موضع الاختبار في السايدبار (1-10)');
            }
            if (!Schema::hasColumn('exams', 'show_in_sidebar')) {
                $table->boolean('show_in_sidebar')->default(true)->after('sidebar_position')->comment('إظهار الاختبار في السايدبار');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('exams')) {
            return;
        }

        Schema::table('exams', function (Blueprint $table) {
            if (Schema::hasColumn('exams', 'sidebar_position')) {
                $table->dropColumn('sidebar_position');
            }
            if (Schema::hasColumn('exams', 'show_in_sidebar')) {
                $table->dropColumn('show_in_sidebar');
            }
        });
    }
};
