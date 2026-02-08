<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * السماح بمرفقات متعددة (ملفات متعددة) للمورد
     */
    public function up(): void
    {
        Schema::table('offline_course_resources', function (Blueprint $table) {
            $table->json('attachments')->nullable()->after('file_name')->comment('مرفقات متعددة [{"path":"","name":""}]');
        });
    }

    public function down(): void
    {
        Schema::table('offline_course_resources', function (Blueprint $table) {
            $table->dropColumn('attachments');
        });
    }
};
