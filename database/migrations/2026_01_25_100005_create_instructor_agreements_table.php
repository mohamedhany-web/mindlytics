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
        // التحقق من وجود الجدول أولاً (قد يكون موجوداً من migration سابقة)
        if (Schema::hasTable('instructor_agreements')) {
            // إضافة الأعمدة الجديدة إذا لم تكن موجودة
            Schema::table('instructor_agreements', function (Blueprint $table) {
                if (!Schema::hasColumn('instructor_agreements', 'offline_course_id')) {
                    $table->foreignId('offline_course_id')->nullable()->after('instructor_id')->constrained('offline_courses')->onDelete('set null');
                }
                if (!Schema::hasColumn('instructor_agreements', 'salary_per_session')) {
                    $table->decimal('salary_per_session', 10, 2)->default(0)->after('end_date')->comment('الراتب لكل جلسة');
                }
                if (!Schema::hasColumn('instructor_agreements', 'sessions_count')) {
                    $table->integer('sessions_count')->default(0)->after('salary_per_session')->comment('عدد الجلسات المتفق عليها');
                }
                if (!Schema::hasColumn('instructor_agreements', 'total_amount')) {
                    $table->decimal('total_amount', 10, 2)->default(0)->after('sessions_count')->comment('المبلغ الإجمالي');
                }
                if (!Schema::hasColumn('instructor_agreements', 'payment_status')) {
                    $table->enum('payment_status', ['pending', 'partial', 'paid', 'overdue'])->default('pending')->after('total_amount');
                }
            });
            
            // محاولة إضافة الفهارس (قد تفشل إذا كانت موجودة)
            try {
                Schema::table('instructor_agreements', function (Blueprint $table) {
                    $table->index(['offline_course_id', 'status'], 'instructor_agreements_offline_course_id_status_index');
                });
            } catch (\Exception $e) {
                // الفهرس موجود بالفعل
            }
            
            try {
                Schema::table('instructor_agreements', function (Blueprint $table) {
                    $table->index('payment_status', 'instructor_agreements_payment_status_index');
                });
            } catch (\Exception $e) {
                // الفهرس موجود بالفعل
            }
            
            return;
        }

        Schema::create('instructor_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('offline_course_id')->nullable()->constrained('offline_courses')->onDelete('set null');
            $table->string('agreement_number')->unique()->comment('رقم الاتفاقية');
            $table->string('title')->comment('عنوان الاتفاقية');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('salary_per_session', 10, 2)->default(0)->comment('الراتب لكل جلسة');
            $table->integer('sessions_count')->default(0)->comment('عدد الجلسات المتفق عليها');
            $table->decimal('total_amount', 10, 2)->default(0)->comment('المبلغ الإجمالي');
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'overdue'])->default('pending');
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('draft');
            $table->text('terms')->nullable()->comment('شروط الاتفاقية');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['instructor_id', 'status']);
            $table->index(['offline_course_id', 'status']);
            $table->index('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructor_agreements');
    }
};
