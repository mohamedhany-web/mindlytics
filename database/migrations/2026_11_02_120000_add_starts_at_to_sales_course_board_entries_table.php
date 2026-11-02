<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * تاريخ بداية فعلي لكورسات لوحة المبيعات (زي الدبلومات)، عشان الصفحة الرئيسية ترتّب الدفعات بالأقرب.
 * عمود جديد nullable بس: مفيش تغيير على أي بيانات قديمة، و«موعد البداية» النصي يفضل زي ما هو.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sales_course_board_entries') && ! Schema::hasColumn('sales_course_board_entries', 'starts_at')) {
            Schema::table('sales_course_board_entries', function (Blueprint $table) {
                $table->date('starts_at')->nullable()->after('start_label');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sales_course_board_entries', 'starts_at')) {
            Schema::table('sales_course_board_entries', function (Blueprint $table) {
                $table->dropColumn('starts_at');
            });
        }
    }
};
