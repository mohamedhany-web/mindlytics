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
        Schema::table('user_achievements', function (Blueprint $table) {
            // إضافة عمود points_earned إذا لم يكن موجوداً
            if (!Schema::hasColumn('user_achievements', 'points_earned')) {
                $table->integer('points_earned')->default(0)->after('progress');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_achievements', function (Blueprint $table) {
            if (Schema::hasColumn('user_achievements', 'points_earned')) {
                $table->dropColumn('points_earned');
            }
        });
    }
};
