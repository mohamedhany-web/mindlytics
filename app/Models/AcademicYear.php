<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'video_url',
        'thumbnail',
        'price',
        'icon',
        'color',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function subjects()
    {
        return $this->hasMany(AcademicSubject::class);
    }

    public function academicSubjects()
    {
        return $this->hasMany(AcademicSubject::class);
    }

    // تم إزالة العلاقة المباشرة مع advanced_courses لأن academic_year_id تم إزالته
    // يمكن الوصول للكورسات من خلال المواد الدراسية: $year->academicSubjects->flatMap->advancedCourses
    public function getAdvancedCoursesAttribute()
    {
        if (!$this->relationLoaded('academicSubjects')) {
            $this->load('academicSubjects.advancedCourses');
        }
        return $this->academicSubjects->flatMap->advancedCourses;
    }

    // Alias للتوافق مع الكود القديم
    // يعيد query builder للكورسات المرتبطة بالمواد الدراسية لهذه السنة
    // ملاحظة: إذا كان academic_subject_id غير موجود، سيعيد query فارغ
    public function courses()
    {
        // التحقق من وجود العمود أولاً
        if (!Schema::hasColumn('advanced_courses', 'academic_subject_id')) {
            // إذا لم يكن موجوداً، نعيد query فارغ
            return AdvancedCourse::where('id', '<', 0);
    }

        $subjectIds = $this->academicSubjects()->pluck('academic_subjects.id')->toArray();
        if (empty($subjectIds)) {
            return AdvancedCourse::where('id', '<', 0);
        }
        return AdvancedCourse::whereIn('academic_subject_id', $subjectIds);
    }

    public function questionCategories()
    {
        return $this->hasMany(QuestionCategory::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function getActiveSubjectsCountAttribute()
    {
        return $this->subjects()->active()->count();
    }

    public function getActiveCoursesCountAttribute()
    {
        return $this->academicSubjects->sum(function($subject) {
            return $subject->advancedCourses()->active()->count();
        });
    }

    /**
     * علاقة مع الكورسات المرتبطة مباشرة بالمسار (many-to-many)
     */
    public function linkedCourses()
    {
        return $this->belongsToMany(AdvancedCourse::class, 'academic_year_courses', 'academic_year_id', 'advanced_course_id')
            ->withPivot('order', 'is_required')
            ->orderBy('academic_year_courses.order')
            ->withTimestamps()
            ->select('advanced_courses.*'); // تحديد الأعمدة بشكل صريح لتجنب ambiguous column
    }

    /**
     * علاقة مع المدربين في المسار
     */
    public function instructors()
    {
        return $this->belongsToMany(User::class, 'academic_year_instructors', 'academic_year_id', 'instructor_id')
            ->withPivot('assigned_courses', 'notes')
            ->withTimestamps();
    }

    /**
     * علاقة مع تسجيلات الطلاب في المسار
     */
    public function enrollments()
    {
        return $this->hasMany(LearningPathEnrollment::class, 'academic_year_id');
    }

    /**
     * علاقة مع الطلاب المسجلين في المسار
     */
    public function enrolledStudents()
    {
        return $this->belongsToMany(User::class, 'learning_path_enrollments', 'academic_year_id', 'user_id')
            ->withPivot(['status', 'progress', 'enrolled_at', 'activated_at'])
            ->withTimestamps();
    }

    /**
     * مشاريع البورتفوليو المرتبطة بهذا المسار
     */
    public function portfolioProjects()
    {
        return $this->hasMany(PortfolioProject::class, 'academic_year_id');
    }
}