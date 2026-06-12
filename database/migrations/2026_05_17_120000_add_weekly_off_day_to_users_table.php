<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'weekly_off_day')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedTinyInteger('weekly_off_day')->nullable()->after('hire_date')
                    ->comment('يوم الإجازة الأسبوعية: 0=أحد … 6=سبت (Carbon dayOfWeek)');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'weekly_off_day')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('weekly_off_day');
            });
        }
    }
};
