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
        Schema::table('offline_courses', function (Blueprint $table) {
            // إضافة location_id
            if (!Schema::hasColumn('offline_courses', 'location_id')) {
                $table->foreignId('location_id')->nullable()->after('instructor_id')->constrained('offline_locations')->onDelete('set null');
            }
            
            // إزالة الحقول غير المطلوبة
            if (Schema::hasColumn('offline_courses', 'academic_year_id')) {
                $table->dropForeign(['academic_year_id']);
                $table->dropColumn('academic_year_id');
            }
            
            if (Schema::hasColumn('offline_courses', 'academic_subject_id')) {
                $table->dropForeign(['academic_subject_id']);
                $table->dropColumn('academic_subject_id');
            }
            
            if (Schema::hasColumn('offline_courses', 'class_time')) {
                $table->dropColumn('class_time');
            }
            
            // إزالة location القديم إذا كان موجوداً (سنستخدم location_id بدلاً منه)
            // لكن سنحتفظ به مؤقتاً للتوافق
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offline_courses', function (Blueprint $table) {
            if (Schema::hasColumn('offline_courses', 'location_id')) {
                $table->dropForeign(['location_id']);
                $table->dropColumn('location_id');
            }
            
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->onDelete('set null');
            $table->foreignId('academic_subject_id')->nullable()->constrained('academic_subjects')->onDelete('set null');
            $table->time('class_time')->nullable()->comment('وقت الحصة');
        });
    }
};
