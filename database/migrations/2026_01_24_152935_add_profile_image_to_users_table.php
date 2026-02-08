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
        Schema::table('users', function (Blueprint $table) {
            // إضافة عمود profile_image إذا لم يكن موجوداً
            if (!Schema::hasColumn('users', 'profile_image')) {
                $table->string('profile_image')->nullable()->after('avatar');
            }
        });
        
        // نسخ البيانات من avatar إلى profile_image إذا كانت موجودة
        \DB::statement('UPDATE users SET profile_image = avatar WHERE avatar IS NOT NULL AND (profile_image IS NULL OR profile_image = "")');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'profile_image')) {
                $table->dropColumn('profile_image');
            }
        });
    }
};
