<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningPathEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'academic_year_id',
        'enrolled_at',
        'activated_at',
        'activated_by',
        'status',
        'progress',
        'notes',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'activated_at' => 'datetime',
        'progress' => 'decimal:2',
    ];

    /**
     * علاقة مع الطالب
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * علاقة مع المستخدم (alias)
     */
    public function user()
    {
        return $this->student();
    }

    /**
     * علاقة مع المسار التعليمي
     */
    public function learningPath()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    /**
     * علاقة مع المستخدم الذي فعل التسجيل
     */
    public function activatedBy()
    {
        return $this->belongsTo(User::class, 'activated_by');
    }

    /**
     * تحديد ما إذا كان التسجيل نشط
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * تحديد ما إذا كان التسجيل مكتمل
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Scope للتسجيلات النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope للتسجيلات المعلقة
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
