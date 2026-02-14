<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdvancedCourse extends Model
{
    use HasFactory;

    /**
     * حذف السجلات المرتبطة بالكورس باستعلامات SQL مباشرة (بدون تحميل نماذج أو أحداث).
     * الترتيب حسب تبعيات المفاتيح الأجنبية: الأبناء قبل الآباء.
     * يُستدعى من الكونترولر قبل حذف سجل الكورس.
     */
    public static function deleteRelatedRecords(int $courseId): void
    {
        $steps = [
            ['table' => 'lesson_progress', 'column' => 'course_lesson_id', 'parent' => 'course_lessons', 'parent_column' => 'advanced_course_id'],
            ['table' => 'curriculum_items', 'column' => 'course_section_id', 'parent' => 'course_sections', 'parent_column' => 'advanced_course_id'],
            ['table' => 'attendance_records', 'column' => 'lecture_id', 'parent' => 'lectures', 'parent_column' => 'course_id'],
            ['table' => 'teams_attendance_files', 'column' => 'lecture_id', 'parent' => 'lectures', 'parent_column' => 'course_id'],
            ['table' => 'lectures', 'column' => 'course_id', 'direct' => true],
            ['table' => 'course_lessons', 'column' => 'advanced_course_id', 'direct' => true],
            ['table' => 'assignment_submissions', 'column' => 'assignment_id', 'parent' => 'assignments', 'parent_column' => 'advanced_course_id'],
            ['table' => 'assignments', 'column' => 'advanced_course_id', 'direct' => true],
            ['table' => 'review_helpful', 'column' => 'review_id', 'parent' => 'course_reviews', 'parent_column' => 'course_id'],
            ['table' => 'course_reviews', 'column' => 'course_id', 'direct' => true],
            ['table' => 'student_course_enrollments', 'column' => 'advanced_course_id', 'direct' => true],
            ['table' => 'installment_agreements', 'column' => 'advanced_course_id', 'direct' => true],
            ['table' => 'installment_plans', 'column' => 'advanced_course_id', 'direct' => true],
            ['table' => 'course_sections', 'column' => 'advanced_course_id', 'direct' => true],
            ['table' => 'learning_pattern_attempts', 'column' => 'learning_pattern_id', 'parent' => 'learning_patterns', 'parent_column' => 'advanced_course_id'],
            ['table' => 'learning_patterns', 'column' => 'advanced_course_id', 'direct' => true],
            ['table' => 'exam_anti_cheat_logs', 'column' => 'exam_id', 'parent' => 'exams', 'parent_column' => 'advanced_course_id'],
            ['table' => 'exam_tab_switch_logs', 'column' => 'exam_id', 'parent' => 'exams', 'parent_column' => 'advanced_course_id'],
            ['table' => 'exam_activity_logs', 'column' => 'exam_id', 'parent' => 'exams', 'parent_column' => 'advanced_course_id'],
            ['table' => 'exam_attempts', 'column' => 'exam_id', 'parent' => 'exams', 'parent_column' => 'advanced_course_id'],
            ['table' => 'exam_questions', 'column' => 'exam_id', 'parent' => 'exams', 'parent_column' => 'advanced_course_id'],
            ['table' => 'exams', 'column' => 'advanced_course_id', 'direct' => true],
            ['table' => 'package_course', 'column' => 'course_id', 'direct' => true],
            ['table' => 'attendance_statistics', 'column' => 'course_id', 'direct' => true],
            ['table' => 'academic_year_courses', 'column' => 'advanced_course_id', 'direct' => true],
            ['table' => 'calendar_events', 'column' => 'advanced_course_id', 'direct' => true],
        ];

        foreach ($steps as $def) {
            try {
                if (! Schema::hasTable($def['table'])) {
                    continue;
                }
                if (! empty($def['direct'])) {
                    DB::table($def['table'])->where($def['column'], $courseId)->delete();
                } else {
                    if (! Schema::hasTable($def['parent'])) {
                        continue;
                    }
                    $parentIds = DB::table($def['parent'])->where($def['parent_column'], $courseId)->pluck('id');
                    if ($parentIds->isNotEmpty()) {
                        DB::table($def['table'])->whereIn($def['column'], $parentIds)->delete();
                    }
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    protected $fillable = [
        'instructor_id',
        'title',
        'academic_year_id',
        'academic_subject_id',
        'programming_language',
        'framework',
        'category',
        'description',
        'video_url',
        'objectives',
        'level',
        'duration_hours',
        'duration_minutes',
        'price',
        'thumbnail',
        'requirements',
        'prerequisites',
        'what_you_learn',
        'skills',
        'language',
        'students_count',
        'rating',
        'reviews_count',
        'is_active',
        'is_featured',
        'is_free',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_free' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'price' => 'decimal:2',
        'rating' => 'decimal:2',
        'skills' => 'array',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function academicSubject()
    {
        return $this->belongsTo(AcademicSubject::class);
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    // للحفاظ على التوافق مع الكود القديم
    public function teacher()
    {
        return $this->instructor();
    }

    public function lessons()
    {
        return $this->hasMany(CourseLesson::class);
    }

    public function lectures()
    {
        return $this->hasMany(Lecture::class, 'course_id');
    }

    public function activations()
    {
        return $this->hasMany(CourseActivation::class);
    }

    public function exams()
    {
        return $this->hasMany(AdvancedExam::class, 'advanced_course_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function learningPatterns()
    {
        return $this->hasMany(LearningPattern::class, 'advanced_course_id');
    }

    public function sections()
    {
        return $this->hasMany(CourseSection::class, 'advanced_course_id')->orderBy('order');
    }

    public function activeSections()
    {
        return $this->hasMany(CourseSection::class, 'advanced_course_id')
            ->where('is_active', true)
            ->orderBy('order');
    }

    public function installmentPlans()
    {
        return $this->hasMany(InstallmentPlan::class, 'advanced_course_id');
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_course', 'course_id', 'package_id')
            ->withPivot('order')
            ->orderBy('package_course.order')
            ->withTimestamps();
    }

    public function installmentAgreements()
    {
        return $this->hasMany(InstallmentAgreement::class, 'advanced_course_id');
    }

    public function enrollments()
    {
        return $this->hasMany(StudentCourseEnrollment::class, 'advanced_course_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * علاقة مع الطلاب المسجلين
     */
    public function enrolledStudents()
    {
        return $this->belongsToMany(User::class, 'student_course_enrollments', 'advanced_course_id', 'user_id')
                    ->withPivot(['status', 'progress', 'enrolled_at', 'activated_at']);
    }

    /**
     * علاقة مع الطلاب النشطين فقط
     */
    public function activeStudents()
    {
        return $this->belongsToMany(User::class, 'student_course_enrollments', 'advanced_course_id', 'user_id')
                    ->wherePivot('status', 'active')
                    ->withPivot(['status', 'progress', 'enrolled_at', 'activated_at']);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function getTotalLessonsAttribute()
    {
        return $this->lessons()->count();
    }

    public function getActivatedStudentsCountAttribute()
    {
        return $this->activations()->where('is_active', true)->count();
    }

    public function isActivatedForUser($userId)
    {
        return $this->activations()
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();
    }

    public function getProgressForUser($userId)
    {
        $totalLessons = $this->lessons()->count();
        if ($totalLessons === 0) return 0;

        $completedLessons = LessonProgress::where('user_id', $userId)
            ->whereIn('course_lesson_id', $this->lessons()->pluck('id'))
            ->where('is_completed', true)
            ->count();

        return round(($completedLessons / $totalLessons) * 100, 2);
    }

    public function getLevelBadgeAttribute()
    {
        $badges = [
            'beginner' => ['text' => 'مبتدئ', 'color' => 'green'],
            'intermediate' => ['text' => 'متوسط', 'color' => 'yellow'],
            'advanced' => ['text' => 'متقدم', 'color' => 'red'],
        ];

        return $badges[$this->level] ?? $badges['beginner'];
    }
}