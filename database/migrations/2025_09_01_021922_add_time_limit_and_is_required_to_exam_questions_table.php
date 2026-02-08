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
        Schema::table('exam_questions', function (Blueprint $table) {
            // إضافة الحقول المفقودة
            $table->integer('time_limit')->nullable()->after('marks');
            $table->boolean('is_required')->default(false)->after('time_limit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_questions', function (Blueprint $table) {
            $table->dropColumn(['time_limit', 'is_required']);
        });
    }
};
