<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvancedExam extends Model
{
    protected $table = 'exams';
    
    protected $fillable = [
        'advanced_course_id',
        'offline_course_id',
        'course_lesson_id',
        'title',
        'description',
        'instructions',
        'total_marks',
        'passing_marks',
        'duration_minutes',
        'attempts_allowed',
        'randomize_questions',
        'randomize_options',
        'show_results_immediately',
        'show_correct_answers',
        'show_explanations',
        'allow_review',
        'start_time',
        'end_time',
        'start_date',
        'end_date',
        'is_active',
        'is_published',
        'sidebar_position',
        'show_in_sidebar',
        'created_by',
    ];

    protected $casts = [
        'randomize_questions' => 'boolean',
        'randomize_options' => 'boolean',
        'show_results_immediately' => 'boolean',
        'show_correct_answers' => 'boolean',
        'show_explanations' => 'boolean',
        'allow_review' => 'boolean',
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'show_in_sidebar' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'sidebar_position' => 'integer',
    ];

    /**
     * علاقة مع الكورس الأونلاين
     */
    public function advancedCourse()
    {
        return $this->belongsTo(AdvancedCourse::class, 'advanced_course_id');
    }

    /**
     * علاقة مع الكورس الأوفلاين
     */
    public function offlineCourse()
    {
        return $this->belongsTo(OfflineCourse::class, 'offline_course_id');
    }

    /**
     * علاقة مع الكورس (alias للتوافق - أونلاين فقط)
     */
    public function course()
    {
        return $this->advancedCourse();
    }

    /**
     * هل الامتحان خاص بكورس أوفلاين
     */
    public function isOfflineExam(): bool
    {
        return !empty($this->offline_course_id);
    }

    /**
     * علاقة مع الدرس
     */
    public function lesson()
    {
        return $this->belongsTo(CourseLesson::class, 'course_lesson_id');
    }

    /**
     * علاقة مع الأسئلة
     */
    public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_questions', 'exam_id', 'question_id')
                    ->withPivot(['order', 'marks'])
                    ->orderBy('exam_questions.order');
    }

    /**
     * علاقة مع محاولات الامتحان
     */
    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class, 'exam_id');
    }

    /**
     * علاقة مع عناصر المنهج
     */
    public function curriculumItems()
    {
        return $this->morphMany(CurriculumItem::class, 'item');
    }
}
