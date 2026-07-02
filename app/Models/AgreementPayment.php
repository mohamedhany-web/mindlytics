<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgreementPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'agreement_id',
        'instructor_id',
        'payment_number',
        'type',
        'amount',
        'status',
        'description',
        'related_course_id',
        'related_lecture_id',
        'student_course_enrollment_id',
        'hours_count',
        'payment_date',
        'paid_at',
        'transfer_receipt_path',
        'payment_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'hours_count' => 'integer',
        'payment_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public const TYPE_COURSE_COMPLETION = 'course_completion';
    public const TYPE_HOURLY_TEACHING = 'hourly_teaching';
    public const TYPE_MONTHLY_SALARY = 'monthly_salary';
    public const TYPE_BONUS = 'bonus';
    public const TYPE_OTHER = 'other';
    /** نسبة من تفعيل الطالب للكورس (اتفاقية نسبة من الكورس) */
    public const TYPE_COURSE_ACTIVATION = 'course_activation';

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_PAID = 'paid';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    protected static function booted(): void
    {
        static::creating(function (AgreementPayment $payment) {
            if (empty($payment->payment_number)) {
                $payment->payment_number = self::generatePaymentNumber();
            }
        });
    }

    public static function generatePaymentNumber(): string
    {
        $prefix = 'AGP-' . date('Y') . '-';
        $lastNumber = static::query()
            ->where('payment_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('payment_number');

        $next = 1;
        if (is_string($lastNumber) && preg_match('/-(\d+)$/', $lastNumber, $matches)) {
            $next = ((int) $matches[1]) + 1;
        }

        return $prefix . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    public function agreement(): BelongsTo
    {
        return $this->belongsTo(InstructorAgreement::class, 'agreement_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(AdvancedCourse::class, 'related_course_id');
    }

    public function lecture(): BelongsTo
    {
        return $this->belongsTo(Lecture::class, 'related_lecture_id');
    }

    /**
     * تفعيل تسجيل الطالب المرتبط بهذا الدفع (نسبة من الكورس)
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(StudentCourseEnrollment::class, 'student_course_enrollment_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            self::TYPE_COURSE_COMPLETION => 'إتمام كورس',
            self::TYPE_HOURLY_TEACHING => 'تدريس بالساعة',
            self::TYPE_MONTHLY_SALARY => 'راتب شهري',
            self::TYPE_BONUS => 'مكافأة',
            self::TYPE_OTHER => 'أخرى',
            self::TYPE_COURSE_ACTIVATION => 'نسبة من تفعيل الطالب',
            default => 'غير محدد',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'قيد المراجعة',
            self::STATUS_APPROVED => 'موافق عليه',
            self::STATUS_PAID => 'مدفوع',
            self::STATUS_REJECTED => 'مرفوض',
            self::STATUS_CANCELLED => 'ملغي',
            default => 'غير محدد',
        };
    }

    public function scopeForInstructor($query, $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopeVisibleToInstructor($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('student_course_enrollment_id')
                ->orWhereHas('enrollment', fn ($enrollment) => $enrollment->visibleToInstructor());
        });
    }
}
