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
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'advanced_course_id')) {
            try {
                // البحث عن اسم الـ foreign key constraint إن وجد
                $constraints = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'orders' 
                    AND COLUMN_NAME = 'advanced_course_id' 
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ");
                
                // حذف foreign key constraint إن وجد
                if (count($constraints) > 0) {
                    $constraintName = $constraints[0]->CONSTRAINT_NAME;
                    DB::statement("ALTER TABLE orders DROP FOREIGN KEY `{$constraintName}`");
                }
            } catch (\Exception $e) {
                // تجاهل الخطأ إذا لم يكن موجوداً
                \Log::info('No foreign key constraint found for advanced_course_id in orders table');
            }
            
            // جعل العمود nullable
            DB::statement('ALTER TABLE orders MODIFY advanced_course_id BIGINT UNSIGNED NULL');
            
            // إعادة إضافة foreign key constraint مع nullable
            try {
                DB::statement('ALTER TABLE orders ADD CONSTRAINT orders_advanced_course_id_foreign 
                              FOREIGN KEY (advanced_course_id) REFERENCES advanced_courses(id) ON DELETE SET NULL ON UPDATE CASCADE');
            } catch (\Exception $e) {
                // إذا فشل، قد يكون الـ constraint موجوداً بالفعل
                \Log::info('Foreign key constraint may already exist: ' . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'advanced_course_id')) {
            try {
                // البحث عن اسم الـ foreign key constraint
                $constraints = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'orders' 
                    AND COLUMN_NAME = 'advanced_course_id' 
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ");
                
                // حذف foreign key constraint
                if (count($constraints) > 0) {
                    $constraintName = $constraints[0]->CONSTRAINT_NAME;
                    DB::statement("ALTER TABLE orders DROP FOREIGN KEY `{$constraintName}`");
                }
            } catch (\Exception $e) {
                // تجاهل الخطأ
            }
            
            // جعل العمود NOT NULL مرة أخرى
            DB::statement('ALTER TABLE orders MODIFY advanced_course_id BIGINT UNSIGNED NOT NULL');
            
            // إعادة إضافة foreign key constraint
            try {
                DB::statement('ALTER TABLE orders ADD CONSTRAINT orders_advanced_course_id_foreign 
                              FOREIGN KEY (advanced_course_id) REFERENCES advanced_courses(id) ON DELETE CASCADE ON UPDATE CASCADE');
            } catch (\Exception $e) {
                // تجاهل الخطأ
            }
        }
    }
};
