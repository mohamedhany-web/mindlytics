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
        // التحقق من وجود الجدول أولاً
        if (!Schema::hasTable('advanced_courses')) {
            return;
        }

        $driver = DB::getDriverName();
        
        if ($driver === 'sqlite') {
            // طريقة SQLite: إنشاء جدول جديد ونسخ البيانات
            $this->sqliteMigration();
        } else {
            // طريقة MySQL/MariaDB: استخدام ALTER TABLE مباشرة
            Schema::table('advanced_courses', function (Blueprint $table) {
                // إزالة الحقول القديمة إذا كانت موجودة
                if (Schema::hasColumn('advanced_courses', 'academic_year_id')) {
                    $table->dropForeign(['academic_year_id']);
                    $table->dropColumn('academic_year_id');
                }
                
                if (Schema::hasColumn('advanced_courses', 'academic_subject_id')) {
                    $table->dropForeign(['academic_subject_id']);
                    $table->dropColumn('academic_subject_id');
                }
                
                // إعادة تسمية teacher_id إلى instructor_id إذا كان موجوداً
                if (Schema::hasColumn('advanced_courses', 'teacher_id') && !Schema::hasColumn('advanced_courses', 'instructor_id')) {
                    $table->renameColumn('teacher_id', 'instructor_id');
                }
            });

            Schema::table('advanced_courses', function (Blueprint $table) {
                // إضافة حقول جديدة
                $this->addNewColumns($table);
            });

            // إضافة الفهارس
            Schema::table('advanced_courses', function (Blueprint $table) {
                $table->index('programming_language');
                $table->index('category');
                $table->index('instructor_id');
            });
        }
    }

    /**
     * Migration خاصة بـ SQLite
     */
    private function sqliteMigration(): void
    {
        // إنشاء نسخة احتياطية
        DB::statement('CREATE TABLE IF NOT EXISTS advanced_courses_backup AS SELECT * FROM advanced_courses');
        
        // إنشاء جدول جديد بالبنية المحدثة
        Schema::dropIfExists('advanced_courses_new');
        Schema::create('advanced_courses_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->string('programming_language')->nullable();
            $table->string('framework')->nullable();
            $table->string('category')->nullable();
            $table->string('language')->default('ar');
            $table->text('description')->nullable();
            $table->text('objectives')->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->integer('duration_hours')->default(0);
            $table->integer('duration_minutes')->default(0);
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('students_count')->default(0);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('reviews_count')->default(0);
            $table->string('thumbnail')->nullable();
            $table->text('requirements')->nullable();
            $table->text('prerequisites')->nullable();
            $table->text('what_you_learn')->nullable();
            $table->json('skills')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            
            $table->index('programming_language');
            $table->index('category');
            $table->index('instructor_id');
            $table->index(['is_active', 'is_featured']);
        });

        // نسخ البيانات من الجدول القديم
        $baseColumns = [
            'id', 'title', 'description', 'objectives', 'level', 
            'duration_hours', 'price', 'thumbnail', 'requirements', 
            'what_you_learn', 'starts_at', 'ends_at', 'is_active', 
            'is_featured', 'created_at', 'updated_at'
        ];

        // إعداد أعمدة SELECT
        $selectColumns = implode(', ', $baseColumns);
        
        // إضافة instructor_id
        if (Schema::hasColumn('advanced_courses', 'teacher_id')) {
            $selectColumns .= ', teacher_id as instructor_id';
        } elseif (Schema::hasColumn('advanced_courses', 'instructor_id')) {
            $selectColumns .= ', instructor_id';
        } else {
            $selectColumns .= ', NULL as instructor_id';
        }

        // أعمدة INSERT
        $insertColumns = 'id, title, description, objectives, level, duration_hours, price, thumbnail, requirements, what_you_learn, starts_at, ends_at, is_active, is_featured, created_at, updated_at, instructor_id, programming_language, framework, category, language, duration_minutes, students_count, rating, reviews_count, prerequisites, skills';
        
        DB::statement("
            INSERT INTO advanced_courses_new 
            ($insertColumns)
            SELECT 
                $selectColumns,
                NULL as programming_language,
                NULL as framework,
                NULL as category,
                'ar' as language,
                0 as duration_minutes,
                0 as students_count,
                0.00 as rating,
                0 as reviews_count,
                NULL as prerequisites,
                NULL as skills
            FROM advanced_courses
        ");

        // حذف الجدول القديم وإعادة تسمية الجديد
        Schema::dropIfExists('advanced_courses');
        Schema::rename('advanced_courses_new', 'advanced_courses');
        Schema::dropIfExists('advanced_courses_backup');
    }

    /**
     * إضافة الأعمدة الجديدة
     */
    private function addNewColumns(Blueprint $table): void
    {
        if (!Schema::hasColumn('advanced_courses', 'programming_language')) {
            $table->string('programming_language')->nullable()->after('title');
        }
        if (!Schema::hasColumn('advanced_courses', 'framework')) {
            $table->string('framework')->nullable()->after('programming_language');
        }
        if (!Schema::hasColumn('advanced_courses', 'category')) {
            $table->string('category')->nullable()->after('framework');
        }
        if (!Schema::hasColumn('advanced_courses', 'language')) {
            $table->string('language')->default('ar')->after('category');
        }
        if (!Schema::hasColumn('advanced_courses', 'skills')) {
            $table->json('skills')->nullable()->after('what_you_learn');
        }
        if (!Schema::hasColumn('advanced_courses', 'prerequisites')) {
            $table->text('prerequisites')->nullable()->after('requirements');
        }
        if (!Schema::hasColumn('advanced_courses', 'duration_minutes')) {
            $table->integer('duration_minutes')->default(0)->after('duration_hours');
        }
        if (!Schema::hasColumn('advanced_courses', 'students_count')) {
            $table->integer('students_count')->default(0)->after('price');
        }
        if (!Schema::hasColumn('advanced_courses', 'rating')) {
            $table->decimal('rating', 3, 2)->default(0)->after('students_count');
        }
        if (!Schema::hasColumn('advanced_courses', 'reviews_count')) {
            $table->integer('reviews_count')->default(0)->after('rating');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // في حالة التراجع، يمكن إعادة الحقول القديمة
        // لكن سنترك هذا فارغاً لتجنب التعقيدات
    }
};
