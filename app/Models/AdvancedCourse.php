<?php

namespace App\Models;

use App\Models\Concerns\AssignsDefaultBranchOnCreate;
use App\Models\Concerns\QueriesByBranch;
use App\Models\Concerns\VisibleOnCurrentHostScope;
use App\Support\BranchContext;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdvancedCourse extends Model
{
    use AssignsDefaultBranchOnCreate;
    use HasFactory;
    use QueriesByBranch;
    use VisibleOnCurrentHostScope;

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
        'branch_id',
        'instructor_id',
        'title',
        'title_en',
        'academic_year_id',
        'academic_subject_id',
        'programming_language',
        'framework',
        'category',
        'description',
        'hook',
        'description_en',
        'hook_en',
        'video_url',
        'objectives',
        'level',
        'duration_hours',
        'duration_minutes',
        'price',
        'discount_amount',
        'thumbnail',
        'requirements',
        'prerequisites',
        'what_you_learn',
        'suitable_for',
        'instructor_info',
        'available_until_info',
        'follow_up_info',
        'age_suitability',
        'mind_map_steps',
        'mind_map_published',
        'mind_map_timetable',
        'skills',
        'language',
        'students_count',
        'rating',
        'reviews_count',
        'is_active',
        'is_featured',
        'is_free',
        'is_scholarship_only',
        'admin_unlock_all_videos',
        'scholarship_program_id',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_free' => 'boolean',
        'is_scholarship_only' => 'boolean',
        'admin_unlock_all_videos' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'rating' => 'decimal:2',
        'skills' => 'array',
        'mind_map_steps' => 'array',
        'mind_map_published' => 'boolean',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

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
        return $this->hasMany(Assignment::class, 'advanced_course_id');
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

    public function scopePublicCatalog($query)
    {
        return $query->where(function ($q) {
            $q->where('is_scholarship_only', false)->orWhereNull('is_scholarship_only');
        });
    }

    public function scholarshipProgram(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ScholarshipProgram::class, 'scholarship_program_id');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function isVisibleOnCurrentHost(): bool
    {
        $branch = app(BranchContext::class)->branch;
        if (! $branch) {
            return true;
        }

        return (int) $this->branch_id === (int) $branch->id;
    }

    /**
     * نص مُعرَّض حسب لغة الواجهة: عند en يُستخدم الحقل *_en إن وُجد، وإلا النص العربي.
     *
     * @param  string  $field  اسم الحقل الأساسي بدون اللاحقة، مثل title أو description
     */
    public function localized(string $field): string
    {
        $base = $this->getAttribute($field);
        if (app()->getLocale() !== 'en') {
            return is_string($base) ? $base : (string) ($base ?? '');
        }
        $enKey = $field . '_en';
        $en = $this->getAttribute($enKey);
        if (is_string($en) && $en !== '') {
            return $en;
        }

        return is_string($base) ? $base : (string) ($base ?? '');
    }

    public function getTotalLessonsAttribute()
    {
        return $this->lessons()->count();
    }

    /**
     * عدد المحاضرات المرتبطة بالكورس (يُفضَّل استخدام withCount('lectures') في الاستعلام).
     */
    public function getTotalLecturesAttribute(): int
    {
        if (array_key_exists('lectures_count', $this->attributes)) {
            return (int) $this->attributes['lectures_count'];
        }

        return (int) $this->lectures()->count();
    }

    /**
     * مدة العرض من حقول تعديل الكورس (ساعات + دقائق).
     */
    public function getDisplayDurationLabelAttribute(): string
    {
        $hours = (int) round((float) ($this->duration_hours ?? 0));
        $minutes = (int) ($this->duration_minutes ?? 0);
        if ($minutes > 59) {
            $minutes = $minutes % 60;
        }

        $locale = app()->getLocale();
        $hourWord = $locale === 'ar' ? 'ساعة' : 'hour'.($hours === 1 ? '' : 's');
        $minuteWord = $locale === 'ar' ? 'دقيقة' : 'min';

        if ($hours <= 0 && $minutes <= 0) {
            return $locale === 'ar' ? '0 ساعة' : '0 hours';
        }
        if ($hours > 0 && $minutes > 0) {
            return $locale === 'ar'
                ? "{$hours} ساعة و {$minutes} دقيقة"
                : "{$hours} {$hourWord} {$minutes} {$minuteWord}";
        }
        if ($minutes > 0) {
            return "{$minutes} {$minuteWord}";
        }

        return "{$hours} {$hourWord}";
    }

    /**
     * رقم الساعات للبطاقات (من تعديل الكورس فقط).
     */
    public function getDisplayDurationHoursAttribute(): int
    {
        return (int) round((float) ($this->duration_hours ?? 0));
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

    public function originalPrice(): float
    {
        return max(0, (float) ($this->price ?? 0));
    }

    public function courseDiscountAmount(): float
    {
        if ($this->is_free ?? false) {
            return 0;
        }

        return max(0, min($this->originalPrice(), (float) ($this->discount_amount ?? 0)));
    }

    public function effectivePrice(): float
    {
        if ($this->is_free ?? false) {
            return 0;
        }

        return max(0, round($this->originalPrice() - $this->courseDiscountAmount(), 2));
    }

    public function hasCourseDiscount(): bool
    {
        return $this->courseDiscountAmount() > 0 && $this->effectivePrice() < $this->originalPrice();
    }

    /**
     * @return array{original_amount: float, discount_amount: float, amount: float}
     */
    public function paymentBreakdown(): array
    {
        return [
            'original_amount' => $this->originalPrice(),
            'discount_amount' => $this->courseDiscountAmount(),
            'amount' => $this->effectivePrice(),
        ];
    }
}