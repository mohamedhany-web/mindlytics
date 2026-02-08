<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'subject_id',
        'created_by',
        'instructor_id',
        'difficulty',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * العلاقة مع المادة
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * العلاقة مع منشئ بنك الأسئلة
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * العلاقة مع المدرب
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * العلاقة مع الأسئلة
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * الحصول على الأسئلة النشطة
     */
    public function activeQuestions()
    {
        return $this->questions()->where('is_active', true);
    }

    /**
     * إحصائيات بنك الأسئلة
     */
    public function getStatsAttribute()
    {
        return [
            'total_questions' => $this->questions()->count(),
            'active_questions' => $this->activeQuestions()->count(),
            'easy_questions' => $this->questions()->where('difficulty', 'easy')->count(),
            'medium_questions' => $this->questions()->where('difficulty', 'medium')->count(),
            'hard_questions' => $this->questions()->where('difficulty', 'hard')->count(),
        ];
    }
}