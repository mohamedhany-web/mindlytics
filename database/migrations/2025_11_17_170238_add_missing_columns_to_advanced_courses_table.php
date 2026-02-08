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
        if (!Schema::hasTable('advanced_courses')) {
            return;
        }

        Schema::table('advanced_courses', function (Blueprint $table) {
            // إضافة is_free
            if (!Schema::hasColumn('advanced_courses', 'is_free')) {
                $table->boolean('is_free')->default(false)->after('is_featured');
            }

            // إضافة category
            if (!Schema::hasColumn('advanced_courses', 'category')) {
                $table->string('category')->nullable()->after('title');
            }

            // إضافة programming_language
            if (!Schema::hasColumn('advanced_courses', 'programming_language')) {
                $table->string('programming_language')->nullable()->after('category');
            }

            // إضافة framework
            if (!Schema::hasColumn('advanced_courses', 'framework')) {
                $table->string('framework')->nullable()->after('programming_language');
            }

            // إضافة duration_minutes
            if (!Schema::hasColumn('advanced_courses', 'duration_minutes')) {
                $table->integer('duration_minutes')->default(0)->after('duration_hours');
            }

            // إضافة rating
            if (!Schema::hasColumn('advanced_courses', 'rating')) {
                $table->decimal('rating', 3, 2)->default(0)->after('price');
            }

            // إضافة skills
            if (!Schema::hasColumn('advanced_courses', 'skills')) {
                $table->json('skills')->nullable()->after('what_you_learn');
            }

            // إضافة instructor_id إذا لم يكن موجوداً
            if (!Schema::hasColumn('advanced_courses', 'instructor_id')) {
                if (Schema::hasColumn('advanced_courses', 'teacher_id')) {
                    // إذا كان teacher_id موجوداً، نضيف instructor_id كعمود منفصل
                    $table->foreignId('instructor_id')->nullable()->constrained('users')->onDelete('set null')->after('teacher_id');
                } else {
                    $table->foreignId('instructor_id')->nullable()->constrained('users')->onDelete('set null');
                }
            }

            // إضافة students_count
            if (!Schema::hasColumn('advanced_courses', 'students_count')) {
                $table->integer('students_count')->default(0)->after('rating');
            }

            // إضافة reviews_count
            if (!Schema::hasColumn('advanced_courses', 'reviews_count')) {
                $table->integer('reviews_count')->default(0)->after('students_count');
            }

            // إضافة language
            if (!Schema::hasColumn('advanced_courses', 'language')) {
                $table->string('language')->default('ar')->after('framework');
            }

            // إضافة prerequisites
            if (!Schema::hasColumn('advanced_courses', 'prerequisites')) {
                $table->text('prerequisites')->nullable()->after('requirements');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advanced_courses', function (Blueprint $table) {
            // يمكن إزالة الأعمدة هنا إذا لزم الأمر
            // لكن سنتركها للاحتفاظ بالبيانات
        });
    }
};
