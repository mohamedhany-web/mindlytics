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
        Schema::table('coupons', function (Blueprint $table) {
            // إضافة عمود title إذا لم يكن موجوداً
            if (!Schema::hasColumn('coupons', 'title')) {
                $table->string('title')->nullable()->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            if (Schema::hasColumn('coupons', 'title')) {
                $table->dropColumn('title');
            }
        });
    }
};
