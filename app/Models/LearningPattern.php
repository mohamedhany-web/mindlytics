<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningPattern extends Model
{
    use HasFactory;

    protected $fillable = [
        'advanced_course_id',
        'instructor_id',
        'type',
        'title',
        'description',
        'instructions',
        'pattern_data',
        'points',
        'time_limit_minutes',
        'difficulty_level',
        'is_required',
        'allow_multiple_attempts',
        'max_attempts',
        'order',
        'is_active',
        'total_attempts',
        'total_completions',
    ];

    protected $casts = [
        'pattern_data' => 'array',
        'is_required' => 'boolean',
        'allow_multiple_attempts' => 'boolean',
        'is_active' => 'boolean',
        'time_limit_minutes' => 'integer',
        'difficulty_level' => 'integer',
        'points' => 'integer',
        'max_attempts' => 'integer',
        'order' => 'integer',
        'total_attempts' => 'integer',
        'total_completions' => 'integer',
    ];

    // أنواع الأنماط المتاحة
    public static function getAvailableTypes()
    {
        return [
            'code_challenge' => [
                'name' => 'تحدي برمجي',
                'icon' => 'fas fa-code',
                'description' => 'تحدي برمجي يتطلب كتابة كود لحل مشكلة',
            ],
            'interactive_quiz' => [
                'name' => 'اختبار تفاعلي',
                'icon' => 'fas fa-question-circle',
                'description' => 'اختبار بأسئلة متعددة الخيارات أو صحيح/خطأ',
            ],
            'code_playground' => [
                'name' => 'محرر كود مباشر',
                'icon' => 'fas fa-terminal',
                'description' => 'محرر كود مباشر لتجربة الكود',
            ],
            'debugging_exercise' => [
                'name' => 'تمرين تصحيح الأخطاء',
                'icon' => 'fas fa-bug',
                'description' => 'تمرين لإيجاد وإصلاح الأخطاء في الكود',
            ],
            'project_based' => [
                'name' => 'مشروع عملي',
                'icon' => 'fas fa-project-diagram',
                'description' => 'مشروع عملي لبناء تطبيق كامل',
            ],
            'code_snippet' => [
                'name' => 'مثال كود تفاعلي',
                'icon' => 'fas fa-file-code',
                'description' => 'مثال كود مع شرح تفاعلي',
            ],
            'pair_programming' => [
                'name' => 'برمجة زوجية',
                'icon' => 'fas fa-users',
                'description' => 'تمرين برمجة زوجية مع زميل',
            ],
            'code_review' => [
                'name' => 'مراجعة الكود',
                'icon' => 'fas fa-eye',
                'description' => 'مراجعة وتحسين كود موجود',
            ],
            'algorithm_practice' => [
                'name' => 'تمرين خوارزميات',
                'icon' => 'fas fa-brain',
                'description' => 'تمرين على الخوارزميات وهياكل البيانات',
            ],
            'live_coding' => [
                'name' => 'جلسة برمجة مباشرة',
                'icon' => 'fas fa-video',
                'description' => 'جلسة برمجة مباشرة مع المدرب',
            ],
            'gamification' => [
                'name' => 'نظام النقاط والشارات',
                'icon' => 'fas fa-trophy',
                'description' => 'كسب نقاط وشارات عند إكمال المهام',
            ],
            'flashcards' => [
                'name' => 'بطاقات تعليمية',
                'icon' => 'fas fa-clone',
                'description' => 'بطاقات تعليمية للمفاهيم البرمجية',
            ],
        ];
    }

    public function course()
    {
        return $this->belongsTo(AdvancedCourse::class, 'advanced_course_id');
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function attempts()
    {
        return $this->hasMany(LearningPatternAttempt::class);
    }

    public function curriculumItems()
    {
        return $this->morphMany(CurriculumItem::class, 'item');
    }

    public function getUserAttempt($userId)
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->latest()
            ->first();
    }

    public function getUserBestAttempt($userId)
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->orderBy('score', 'desc')
            ->first();
    }

    public function isCompletedByUser($userId)
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->exists();
    }

    public function canAttempt($userId)
    {
        if (!$this->allow_multiple_attempts) {
            return !$this->isCompletedByUser($userId);
        }

        if ($this->max_attempts) {
            $attemptsCount = $this->attempts()
                ->where('user_id', $userId)
                ->count();
            return $attemptsCount < $this->max_attempts;
        }

        return true;
    }

    public function getTypeInfo()
    {
        $types = self::getAvailableTypes();
        return $types[$this->type] ?? null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
