<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScholarshipRegistration extends Model
{
    public const STATUS_REGISTERED = 'registered';
    public const STATUS_ACTIVATED = 'activated';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_DEACTIVATED = 'deactivated';

    protected $fillable = [
        'scholarship_program_id',
        'user_id',
        'status',
        'registered_at',
        'activated_at',
        'activated_by',
        'student_course_enrollment_id',
        'notes',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'activated_at' => 'datetime',
    ];

    public static function statusLabels(): array
    {
        return [
            self::STATUS_REGISTERED => 'مسجّل — بانتظار التفعيل',
            self::STATUS_ACTIVATED => 'مفعّل',
            self::STATUS_REJECTED => 'مرفوض',
            self::STATUS_DEACTIVATED => 'ملغى التفعيل',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(ScholarshipProgram::class, 'scholarship_program_id');
    }

    public function scholarshipProgram(): BelongsTo
    {
        return $this->program();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function student(): BelongsTo
    {
        return $this->user();
    }

    public function activatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'activated_by');
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(StudentCourseEnrollment::class, 'student_course_enrollment_id');
    }

    public function scopeActivated($query)
    {
        return $query->where('status', self::STATUS_ACTIVATED);
    }

    public function scopePendingActivation($query)
    {
        return $query->where('status', self::STATUS_REGISTERED);
    }
}
