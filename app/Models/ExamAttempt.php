<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'user_id',
        'started_at',
        'submitted_at',
        'time_taken',
        'score',
        'percentage',
        'status',
        'answers',
        'ip_address',
        'user_agent',
        'tab_switches',
        'suspicious_activities',
        'auto_submitted',
        'reviewed_by',
        'reviewed_at',
        'feedback',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'answers' => 'array',
        'suspicious_activities' => 'array',
        'auto_submitted' => 'boolean',
        'score' => 'decimal:2',
        'percentage' => 'decimal:2',
        'time_taken' => 'integer', // بالثواني
        'tab_switches' => 'integer',
    ];

    /**
     * علاقة مع الامتحان
     */
    public function exam()
    {
        // استخدام AdvancedExam لأنه يستخدم نفس الجدول
        return $this->belongsTo(\App\Models\AdvancedExam::class, 'exam_id');
    }

    /**
     * علاقة مع الطالب
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * علاقة مع المراجع
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * scope للمحاولات المكتملة
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * scope للمحاولات الجارية
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * التحقق من انتهاء الوقت
     */
    public function isTimeExpired()
    {
        if (!$this->started_at || $this->status !== 'in_progress') {
            return false;
        }

        $timeLimit = $this->exam->duration_minutes * 60; // تحويل لثواني
        $elapsed = now()->diffInSeconds($this->started_at);

        return $elapsed >= $timeLimit;
    }

    /**
     * الحصول على الوقت المتبقي بالثواني
     */
    public function getRemainingTimeAttribute()
    {
        if (!$this->started_at || $this->status !== 'in_progress') {
            return 0;
        }

        $timeLimit = $this->exam->duration_minutes * 60;
        $elapsed = now()->diffInSeconds($this->started_at);

        return max(0, $timeLimit - $elapsed);
    }

    /**
     * الحصول على الوقت المستغرق منسق
     */
    public function getFormattedTimeAttribute()
    {
        if (!$this->time_taken) {
            return 'غير محدد';
        }

        $hours = floor($this->time_taken / 3600);
        $minutes = floor(($this->time_taken % 3600) / 60);
        $seconds = $this->time_taken % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * الحصول على حالة النتيجة
     */
    public function getResultStatusAttribute()
    {
        if ($this->status !== 'completed') {
            return 'غير مكتمل';
        }

        if ($this->score >= $this->exam->passing_marks) {
            return 'ناجح';
        }

        return 'راسب';
    }

    /**
     * الحصول على لون حالة النتيجة
     */
    public function getResultColorAttribute()
    {
        if ($this->status !== 'completed') {
            return 'gray';
        }

        if ($this->score >= $this->exam->passing_marks) {
            return 'green';
        }

        return 'red';
    }

    /**
     * حساب النتيجة
     */
    public function calculateScore()
    {
        $totalScore = 0;
        $totalMarks = 0;

        foreach ($this->exam->examQuestions as $examQuestion) {
            $question = $examQuestion->question;
            $userAnswer = $this->answers[$question->id] ?? null;
            $totalMarks += $examQuestion->marks;

            if ($question->isCorrectAnswer($userAnswer)) {
                $totalScore += $examQuestion->marks;
            }
        }

        $this->update([
            'score' => $totalScore,
            'percentage' => $totalMarks > 0 ? ($totalScore / $totalMarks) * 100 : 0,
        ]);

        return $totalScore;
    }

    /**
     * تسجيل نشاط مشبوه
     */
    public function logSuspiciousActivity($activity, $details = null)
    {
        $activities = $this->suspicious_activities ?? [];
        $activities[] = [
            'activity' => $activity,
            'details' => $details,
            'timestamp' => now()->toISOString(),
            'ip' => request()->ip(),
        ];

        $this->update(['suspicious_activities' => $activities]);
    }

    /**
     * زيادة عداد تبديل التبويبات
     */
    public function incrementTabSwitches()
    {
        $this->increment('tab_switches');
        $this->logSuspiciousActivity('tab_switch', 'تم تبديل التبويب أو النافذة');
    }

    /**
     * التحقق من وجود أنشطة مشبوهة
     */
    public function hasSuspiciousActivities()
    {
        return !empty($this->suspicious_activities) || $this->tab_switches > 0;
    }
}