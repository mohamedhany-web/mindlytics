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
        // التحقق من وجود الجدول أولاً
        if (!Schema::hasTable('certificates')) {
            return;
        }

        Schema::table('certificates', function (Blueprint $table) {
            // إضافة unique constraint للسيريال
            try {
                $table->unique('serial_number', 'certificates_serial_number_unique');
            } catch (\Exception $e) {
                // الـ unique constraint موجود بالفعل
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropUnique('certificates_serial_number_unique');
        });
    }
};
