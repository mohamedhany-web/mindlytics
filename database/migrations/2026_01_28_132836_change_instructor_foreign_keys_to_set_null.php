<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // تغيير offline_courses.instructor_id من cascade إلى set null
        if (Schema::hasTable('offline_courses') && Schema::hasColumn('offline_courses', 'instructor_id')) {
            try {
                // البحث عن اسم الـ constraint
                $constraints = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'offline_courses' 
                    AND COLUMN_NAME = 'instructor_id' 
                    AND REFERENCED_TABLE_NAME = 'users'
                ");
                
                if (count($constraints) > 0) {
                    $constraintName = $constraints[0]->CONSTRAINT_NAME;
                    // حذف foreign key القديم
                    DB::statement("ALTER TABLE offline_courses DROP FOREIGN KEY `{$constraintName}`");
                }
            } catch (\Exception $e) {
                // تجاهل الخطأ إذا لم يكن موجوداً
            }
            
            // جعل العمود nullable
            DB::statement('ALTER TABLE offline_courses MODIFY instructor_id BIGINT UNSIGNED NULL');
            
            // إضافة foreign key جديد مع set null
            DB::statement('ALTER TABLE offline_courses ADD CONSTRAINT offline_courses_instructor_id_foreign 
                          FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE');
        }

        // تغيير lectures.instructor_id من cascade إلى set null
        if (Schema::hasTable('lectures') && Schema::hasColumn('lectures', 'instructor_id')) {
            try {
                // البحث عن اسم الـ constraint
                $constraints = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'lectures' 
                    AND COLUMN_NAME = 'instructor_id' 
                    AND REFERENCED_TABLE_NAME = 'users'
                ");
                
                if (count($constraints) > 0) {
                    $constraintName = $constraints[0]->CONSTRAINT_NAME;
                    // حذف foreign key القديم
                    DB::statement("ALTER TABLE lectures DROP FOREIGN KEY `{$constraintName}`");
                }
            } catch (\Exception $e) {
                // تجاهل الخطأ إذا لم يكن موجوداً
            }
            
            // جعل العمود nullable
            DB::statement('ALTER TABLE lectures MODIFY instructor_id BIGINT UNSIGNED NULL');
            
            // إضافة foreign key جديد مع set null
            DB::statement('ALTER TABLE lectures ADD CONSTRAINT lectures_instructor_id_foreign 
                          FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE');
        }

        // تغيير academic_year_instructors.instructor_id من cascade إلى set null
        // ملاحظة: هذا جدول pivot، عند حذف المدرب سنحذف السجل من الجدول بدلاً من set null
        if (Schema::hasTable('academic_year_instructors') && Schema::hasColumn('academic_year_instructors', 'instructor_id')) {
            try {
                // البحث عن اسم الـ constraint
                $constraints = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'academic_year_instructors' 
                    AND COLUMN_NAME = 'instructor_id' 
                    AND REFERENCED_TABLE_NAME = 'users'
                ");
                
                if (count($constraints) > 0) {
                    $constraintName = $constraints[0]->CONSTRAINT_NAME;
                    // حذف foreign key القديم
                    DB::statement("ALTER TABLE academic_year_instructors DROP FOREIGN KEY `{$constraintName}`");
                }
            } catch (\Exception $e) {
                // تجاهل الخطأ إذا لم يكن موجوداً
            }
            
            // جعل العمود nullable
            DB::statement('ALTER TABLE academic_year_instructors MODIFY instructor_id BIGINT UNSIGNED NULL');
            
            // إضافة foreign key جديد مع set null
            DB::statement('ALTER TABLE academic_year_instructors ADD CONSTRAINT academic_year_instructors_instructor_id_foreign 
                          FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE');
        }

        // تغيير offline_course_groups.instructor_id من cascade إلى set null
        if (Schema::hasTable('offline_course_groups') && Schema::hasColumn('offline_course_groups', 'instructor_id')) {
            try {
                // البحث عن اسم الـ constraint
                $constraints = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'offline_course_groups' 
                    AND COLUMN_NAME = 'instructor_id' 
                    AND REFERENCED_TABLE_NAME = 'users'
                ");
                
                if (count($constraints) > 0) {
                    $constraintName = $constraints[0]->CONSTRAINT_NAME;
                    // حذف foreign key القديم
                    DB::statement("ALTER TABLE offline_course_groups DROP FOREIGN KEY `{$constraintName}`");
                }
            } catch (\Exception $e) {
                // تجاهل الخطأ إذا لم يكن موجوداً
            }
            
            // جعل العمود nullable
            DB::statement('ALTER TABLE offline_course_groups MODIFY instructor_id BIGINT UNSIGNED NULL');
            
            // إضافة foreign key جديد مع set null
            DB::statement('ALTER TABLE offline_course_groups ADD CONSTRAINT offline_course_groups_instructor_id_foreign 
                          FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE');
        }

        // التأكد من أن advanced_courses.instructor_id يستخدم set null (يجب أن يكون موجوداً بالفعل)
        if (Schema::hasTable('advanced_courses') && Schema::hasColumn('advanced_courses', 'instructor_id')) {
            // التحقق من وجود foreign key
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'advanced_courses' 
                AND COLUMN_NAME = 'instructor_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            if (count($foreignKeys) > 0) {
                $constraintName = $foreignKeys[0]->CONSTRAINT_NAME;
                
                // التحقق من نوع onDelete
                $constraintInfo = DB::select("
                    SELECT DELETE_RULE 
                    FROM information_schema.REFERENTIAL_CONSTRAINTS 
                    WHERE CONSTRAINT_SCHEMA = DATABASE() 
                    AND CONSTRAINT_NAME = ?
                ", [$constraintName]);
                
                if (count($constraintInfo) > 0 && $constraintInfo[0]->DELETE_RULE !== 'SET NULL') {
                    Schema::table('advanced_courses', function (Blueprint $table) {
                        $table->dropForeign(['instructor_id']);
                    });
                    
                    Schema::table('advanced_courses', function (Blueprint $table) {
                        $table->foreign('instructor_id')
                              ->references('id')
                              ->on('users')
                              ->onDelete('set null')
                              ->onUpdate('cascade');
                    });
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إعادة العلاقات إلى cascade (إذا لزم الأمر)
        // ملاحظة: هذا قد يكون خطيراً على البيانات الموجودة
    }
};
