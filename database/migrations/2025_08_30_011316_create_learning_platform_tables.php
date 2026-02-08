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
        // إضافة حقول جديدة لجدول المستخدمين
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'phone')) {
                    $table->string('phone')->unique()->after('email');
                }
                if (!Schema::hasColumn('users', 'role')) {
                    $table->enum('role', ['admin', 'teacher', 'student', 'parent'])->default('student')->after('phone');
                }
                if (!Schema::hasColumn('users', 'avatar')) {
                    $table->string('avatar')->nullable()->after('role');
                }
                if (!Schema::hasColumn('users', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('avatar');
                }
                if (!Schema::hasColumn('users', 'bio')) {
                    $table->text('bio')->nullable()->after('is_active');
                }
                if (Schema::hasColumn('users', 'email_verified_at')) {
                    $table->dropColumn('email_verified_at');
                }
            });

            // Skip column modifications for SQLite
            if (\Illuminate\Support\Facades\DB::connection()->getDriverName() !== 'sqlite') {
                Schema::table('users', function (Blueprint $table) {
                    if (Schema::hasColumn('users', 'email')) {
                        $table->string('email')->nullable()->change();
                    }
                });
            }
        }

        // جدول المدارس
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // جدول الصفوف الدراسية
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // جدول المواد الدراسية
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color')->default('#3B82F6');
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // جدول الكورسات
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->string('thumbnail')->nullable();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('classroom_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->integer('duration_minutes')->nullable();
            $table->boolean('is_free')->default(false);
            $table->decimal('price', 10, 2)->nullable();
            $table->timestamps();
        });

        // جدول الدروس
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->string('video_url')->nullable();
            $table->string('thumbnail')->nullable();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->integer('duration_minutes')->nullable();
            $table->boolean('is_free')->default(false);
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();
        });

        // جدول تسجيل الطلاب في الكورسات
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->integer('progress_percentage')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['student_id', 'course_id']);
        });

        // جدول تسجيل الطلاب في الصفوف
        Schema::create('classroom_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('classroom_id')->constrained()->onDelete('cascade');
            $table->timestamp('enrolled_at')->useCurrent();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['student_id', 'classroom_id']);
        });

        // جدول ربط أولياء الأمور بالطلاب
        Schema::create('parent_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->enum('relation', ['father', 'mother', 'guardian'])->default('father');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            
            $table->unique(['parent_id', 'student_id']);
        });

        // جدول الواجبات
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('due_date')->nullable();
            $table->integer('max_score')->default(100);
            $table->boolean('allow_late_submission')->default(false);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamps();
        });

        // جدول تسليم الواجبات
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->text('content')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->integer('score')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['submitted', 'graded', 'returned'])->default('submitted');
            $table->timestamps();
            
            $table->unique(['assignment_id', 'student_id']);
        });

        // جدول الاختبارات
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->integer('duration_minutes')->default(60);
            $table->integer('max_attempts')->default(1);
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('show_results_immediately')->default(false);
            $table->timestamp('available_from')->nullable();
            $table->timestamp('available_until')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamps();
        });

        // جدول أسئلة الاختبارات
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->text('question');
            $table->enum('type', ['multiple_choice', 'true_false', 'short_answer', 'essay']);
            $table->json('options')->nullable(); // للخيارات المتعددة
            $table->text('correct_answer');
            $table->integer('points')->default(1);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // جدول محاولات الاختبارات
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->json('answers')->nullable();
            $table->integer('score')->nullable();
            $table->integer('total_points')->nullable();
            $table->enum('status', ['in_progress', 'completed', 'timeout'])->default('in_progress');
            $table->timestamps();
        });

        // جدول الإشعارات
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['info', 'success', 'warning', 'error'])->default('info');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();
        });

        // جدول الملفات
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('original_name');
            $table->string('path');
            $table->string('mime_type');
            $table->bigInteger('size');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->morphs('fileable'); // للربط مع نماذج مختلفة
            $table->timestamps();
        });

        // جدول إعدادات النظام
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->string('type')->default('text'); // text, number, boolean, json
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('files');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('parent_students');
        Schema::dropIfExists('classroom_students');
        Schema::dropIfExists('course_enrollments');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('classrooms');
        Schema::dropIfExists('schools');
        
        // إزالة الحقول المضافة من جدول المستخدمين
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $columnsToRemove = [];
                if (Schema::hasColumn('users', 'phone')) $columnsToRemove[] = 'phone';
                if (Schema::hasColumn('users', 'role')) $columnsToRemove[] = 'role';
                if (Schema::hasColumn('users', 'avatar')) $columnsToRemove[] = 'avatar';
                if (Schema::hasColumn('users', 'is_active')) $columnsToRemove[] = 'is_active';
                if (Schema::hasColumn('users', 'bio')) $columnsToRemove[] = 'bio';
                
                if (count($columnsToRemove) > 0) {
                    $table->dropColumn($columnsToRemove);
                }
                
                if (!Schema::hasColumn('users', 'email_verified_at')) {
                    $table->timestamp('email_verified_at')->nullable();
                }
            });

            // Skip column modifications for SQLite
            if (\Illuminate\Support\Facades\DB::connection()->getDriverName() !== 'sqlite') {
                Schema::table('users', function (Blueprint $table) {
                    if (Schema::hasColumn('users', 'email')) {
                        $table->string('email')->unique()->change();
                    }
                });
            }
        }
    }
};
