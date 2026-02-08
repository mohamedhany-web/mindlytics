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
        // تحديث جدول التسجيلات في الكورسات
        if (Schema::hasTable('student_course_enrollments')) {
            Schema::table('student_course_enrollments', function (Blueprint $table) {
                // إضافة حقول المدفوعات
                if (!Schema::hasColumn('student_course_enrollments', 'invoice_id')) {
                    if (Schema::hasTable('invoices')) {
                        $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('set null')->after('activated_by');
                    } else {
                        $table->unsignedBigInteger('invoice_id')->nullable()->after('activated_by');
                    }
                }
                if (!Schema::hasColumn('student_course_enrollments', 'payment_id')) {
                    if (Schema::hasTable('payments')) {
                        $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('set null')->after('invoice_id');
                    } else {
                        $table->unsignedBigInteger('payment_id')->nullable()->after('invoice_id');
                    }
                }
                if (!Schema::hasColumn('student_course_enrollments', 'coupon_id')) {
                    if (Schema::hasTable('coupons')) {
                        $table->foreignId('coupon_id')->nullable()->constrained('coupons')->onDelete('set null')->after('payment_id');
                    } else {
                        $table->unsignedBigInteger('coupon_id')->nullable()->after('payment_id');
                    }
                }
                if (!Schema::hasColumn('student_course_enrollments', 'original_price')) {
                    $table->decimal('original_price', 10, 2)->default(0)->after('coupon_id');
                }
                if (!Schema::hasColumn('student_course_enrollments', 'discount_amount')) {
                    $table->decimal('discount_amount', 10, 2)->default(0)->after('original_price');
                }
                if (!Schema::hasColumn('student_course_enrollments', 'final_price')) {
                    $table->decimal('final_price', 10, 2)->default(0)->after('discount_amount');
                }
                if (!Schema::hasColumn('student_course_enrollments', 'payment_method')) {
                    $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'online', 'wallet', 'subscription', 'free'])->nullable()->after('final_price');
                }
                if (!Schema::hasColumn('student_course_enrollments', 'enrollment_type')) {
                    $table->enum('enrollment_type', ['purchase', 'subscription', 'gift', 'trial', 'promotional'])->default('purchase')->after('payment_method');
                }
                if (!Schema::hasColumn('student_course_enrollments', 'expires_at')) {
                    $table->dateTime('expires_at')->nullable()->after('activated_at');
                }
                if (!Schema::hasColumn('student_course_enrollments', 'access_type')) {
                    $table->enum('access_type', ['lifetime', 'limited', 'subscription'])->default('lifetime')->after('expires_at');
                }
                if (!Schema::hasColumn('student_course_enrollments', 'referral_code')) {
                    $table->string('referral_code')->nullable()->after('access_type');
                }
            });
        }
        
        // تحديث جدول الكورسات - إضافة حقول للأكاديمية
        if (Schema::hasTable('advanced_courses')) {
            Schema::table('advanced_courses', function (Blueprint $table) {
                if (!Schema::hasColumn('advanced_courses', 'is_free')) {
                    $table->boolean('is_free')->default(false)->after('is_featured');
                }
                if (!Schema::hasColumn('advanced_courses', 'trial_days')) {
                    $table->integer('trial_days')->default(0)->after('is_free');
                }
                if (!Schema::hasColumn('advanced_courses', 'enrollment_count')) {
                    $table->integer('enrollment_count')->default(0)->after('trial_days');
                }
                if (!Schema::hasColumn('advanced_courses', 'completion_rate')) {
                    $table->decimal('completion_rate', 5, 2)->default(0)->after('enrollment_count');
                }
                if (!Schema::hasColumn('advanced_courses', 'average_rating')) {
                    $table->decimal('average_rating', 3, 2)->default(0)->after('completion_rate');
                }
                if (!Schema::hasColumn('advanced_courses', 'reviews_count')) {
                    $table->integer('reviews_count')->default(0)->after('average_rating');
                }
                if (!Schema::hasColumn('advanced_courses', 'instructor_id')) {
                    $table->foreignId('instructor_id')->nullable()->constrained('users')->onDelete('set null')->after('teacher_id');
                }
                if (!Schema::hasColumn('advanced_courses', 'certificate_required')) {
                    $table->boolean('certificate_required')->default(true)->after('reviews_count');
                }
                if (!Schema::hasColumn('advanced_courses', 'certificate_criteria')) {
                    $table->json('certificate_criteria')->nullable()->after('certificate_required');
                }
            });
        }
        
        // جدول التقييمات والمراجعات
        if (!Schema::hasTable('course_reviews')) {
            Schema::create('course_reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('advanced_courses')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->integer('rating')->default(5); // 1-5
                $table->text('review')->nullable();
                $table->boolean('is_verified_purchase')->default(false);
                $table->boolean('is_approved')->default(true);
                $table->boolean('is_featured')->default(false);
                $table->integer('helpful_count')->default(0);
                $table->timestamps();
                
                $table->unique(['course_id', 'user_id']);
                $table->index(['course_id', 'is_approved', 'rating']);
            });
        }
        
        // جدول تقييمات مفيدة
        if (!Schema::hasTable('review_helpful')) {
            Schema::create('review_helpful', function (Blueprint $table) {
                $table->id();
                $table->foreignId('review_id')->constrained('course_reviews')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->boolean('is_helpful')->default(true);
                $table->timestamps();
                
                $table->unique(['review_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_helpful');
        Schema::dropIfExists('course_reviews');
        
        if (Schema::hasTable('advanced_courses')) {
            Schema::table('advanced_courses', function (Blueprint $table) {
                $columns = [
                    'is_free', 'trial_days', 'enrollment_count', 'completion_rate',
                    'average_rating', 'reviews_count', 'instructor_id',
                    'certificate_required', 'certificate_criteria'
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('advanced_courses', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
        
        if (Schema::hasTable('student_course_enrollments')) {
            Schema::table('student_course_enrollments', function (Blueprint $table) {
                $columns = [
                    'invoice_id', 'payment_id', 'coupon_id', 'original_price',
                    'discount_amount', 'final_price', 'payment_method', 'enrollment_type',
                    'expires_at', 'access_type', 'referral_code'
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('student_course_enrollments', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
