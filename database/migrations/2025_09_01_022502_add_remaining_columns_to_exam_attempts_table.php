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
        Schema::table('exam_attempts', function (Blueprint $table) {
            // إضافة الحقول المفقودة المتبقية
            if (!Schema::hasColumn('exam_attempts', 'auto_submitted')) {
                $table->boolean('auto_submitted')->default(false)->after('suspicious_activities');
            }
            if (!Schema::hasColumn('exam_attempts', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null')->after('auto_submitted');
            }
            if (!Schema::hasColumn('exam_attempts', 'reviewed_at')) {
                $table->datetime('reviewed_at')->nullable()->after('reviewed_by');
            }
            if (!Schema::hasColumn('exam_attempts', 'feedback')) {
                $table->text('feedback')->nullable()->after('reviewed_at');
            }
            
            // تغيير time_spent إلى time_taken إذا كان موجوداً
            if (Schema::hasColumn('exam_attempts', 'time_spent') && !Schema::hasColumn('exam_attempts', 'time_taken')) {
                $table->renameColumn('time_spent', 'time_taken');
            }
            // أو إضافة time_taken إذا لم يكن موجوداً
            elseif (!Schema::hasColumn('exam_attempts', 'time_taken')) {
                $table->integer('time_taken')->nullable()->after('submitted_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            $columnsToRemove = [];
            if (Schema::hasColumn('exam_attempts', 'auto_submitted')) $columnsToRemove[] = 'auto_submitted';
            if (Schema::hasColumn('exam_attempts', 'reviewed_by')) {
                $table->dropForeign(['reviewed_by']);
                $columnsToRemove[] = 'reviewed_by';
            }
            if (Schema::hasColumn('exam_attempts', 'reviewed_at')) $columnsToRemove[] = 'reviewed_at';
            if (Schema::hasColumn('exam_attempts', 'feedback')) $columnsToRemove[] = 'feedback';
            
            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
            
            // إعادة تسمية time_taken إلى time_spent
            if (Schema::hasColumn('exam_attempts', 'time_taken')) {
                $table->renameColumn('time_taken', 'time_spent');
            }
        });
    }
};
