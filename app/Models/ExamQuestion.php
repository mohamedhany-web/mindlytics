<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'question_id',
        'order',
        'marks',
        'time_limit',
        'is_required',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'marks' => 'decimal:2',
        'time_limit' => 'integer',
    ];

    /**
     * علاقة مع الامتحان
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * علاقة مع السؤال
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * scope للأسئلة مرتبة
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * scope للأسئلة المطلوبة
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }
}