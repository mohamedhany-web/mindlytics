<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * محافظ المنصة (فودافون كاش، تحويل بنكي، إلخ) لا ترتبط بمستخدم — جعل user_id قابلًا للقيمة الفارغة.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            Schema::table('wallets', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
            DB::statement('ALTER TABLE wallets MODIFY user_id BIGINT UNSIGNED NULL');
            Schema::table('wallets', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        } else {
            Schema::table('wallets', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            Schema::table('wallets', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
            DB::statement('ALTER TABLE wallets MODIFY user_id BIGINT UNSIGNED NOT NULL');
            Schema::table('wallets', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        } else {
            Schema::table('wallets', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable(false)->change();
            });
        }
    }
};
