<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إضافة فهارس لتحسين الأداء وقابلية التوسع
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // فهرس للبحث السريع بالدور والحالة
            if (!$this->indexExists('users', 'users_role_is_active_index')) {
                $table->index(['role', 'is_active'], 'users_role_is_active_index');
            }
            
            // فهرس للبحث بالبريد الإلكتروني (إذا لم يكن موجوداً)
            if (!$this->indexExists('users', 'users_email_index')) {
                $table->index('email', 'users_email_index');
            }
            
            // فهرس للبحث بتاريخ الإنشاء (للإحصائيات)
            if (!$this->indexExists('users', 'users_created_at_index')) {
                $table->index('created_at', 'users_created_at_index');
            }
        });

        Schema::table('student_course_enrollments', function (Blueprint $table) {
            // فهرس مركب للبحث السريع بالطالب والحالة
            if (!$this->indexExists('student_course_enrollments', 'enrollments_user_status_index')) {
                $table->index(['user_id', 'status'], 'enrollments_user_status_index');
            }
            
            // فهرس للبحث بالكورس والحالة
            if (!$this->indexExists('student_course_enrollments', 'enrollments_course_status_index')) {
                $table->index(['advanced_course_id', 'status'], 'enrollments_course_status_index');
            }
            
            // فهرس للبحث بتاريخ التسجيل (للإحصائيات)
            if (!$this->indexExists('student_course_enrollments', 'enrollments_enrolled_at_index')) {
                $table->index('enrolled_at', 'enrollments_enrolled_at_index');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            // فهرس للبحث بالحالة وتاريخ الدفع
            if (!$this->indexExists('payments', 'payments_status_paid_at_index')) {
                $table->index(['status', 'paid_at'], 'payments_status_paid_at_index');
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            // فهرس للبحث بالحالة
            if (!$this->indexExists('invoices', 'invoices_status_index')) {
                $table->index('status', 'invoices_status_index');
            }
        });

        Schema::table('advanced_courses', function (Blueprint $table) {
            // فهرس للبحث بالحالة النشطة
            if (!$this->indexExists('advanced_courses', 'courses_is_active_index')) {
                $table->index('is_active', 'courses_is_active_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_is_active_index');
            $table->dropIndex('users_email_index');
            $table->dropIndex('users_created_at_index');
        });

        Schema::table('student_course_enrollments', function (Blueprint $table) {
            $table->dropIndex('enrollments_user_status_index');
            $table->dropIndex('enrollments_course_status_index');
            $table->dropIndex('enrollments_enrolled_at_index');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_status_paid_at_index');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('invoices_status_index');
        });

        Schema::table('advanced_courses', function (Blueprint $table) {
            $table->dropIndex('courses_is_active_index');
        });
    }

    /**
     * التحقق من وجود الفهرس
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $databaseName = $connection->getDatabaseName();
        
        $result = $connection->select(
            "SELECT COUNT(*) as count FROM information_schema.statistics 
             WHERE table_schema = ? AND table_name = ? AND index_name = ?",
            [$databaseName, $table, $indexName]
        );
        
        return $result[0]->count > 0;
    }
};
