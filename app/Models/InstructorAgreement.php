<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstructorAgreement extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_TERMINATED = 'terminated';
    public const STATUS_COMPLETED = 'completed';

    /** نوع الاتفاقية: بالجلسة | راتب شهري | باكورس كامل */
    public const BILLING_PER_SESSION = 'per_session';
    public const BILLING_MONTHLY = 'monthly';
    public const BILLING_FULL_COURSE = 'full_course';

    protected $fillable = [
        'instructor_id',
        'offline_course_id',
        'billing_type',
        'type',
        'rate',
        'agreement_number',
        'title',
        'description',
        'start_date',
        'end_date',
        'salary_per_session',
        'sessions_count',
        'total_amount',
        'monthly_amount',
        'months_count',
        'payment_status',
        'status',
        'terms',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'rate' => 'decimal:2',
        'salary_per_session' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'monthly_amount' => 'decimal:2',
    ];

    /**
     * علاقة مع المدرب
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * علاقة مع الكورس الأوفلاين
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(OfflineCourse::class, 'offline_course_id');
    }

    /**
     * علاقة مع الكورس الأوفلاين (اسم بديل للتوافق)
     */
    public function offlineCourse(): BelongsTo
    {
        return $this->belongsTo(OfflineCourse::class, 'offline_course_id');
    }

    /**
     * علاقة مع مدفوعات الاتفاقية
     */
    public function payments(): HasMany
    {
        return $this->hasMany(AgreementPayment::class, 'agreement_id');
    }

    /**
     * علاقة مع المدفوعات المكتملة (المدفوعة) فقط
     */
    public function paidPayments(): HasMany
    {
        return $this->hasMany(AgreementPayment::class, 'agreement_id')
            ->where('status', AgreementPayment::STATUS_PAID);
    }

    /**
     * علاقة مع المدفوعات الموافق عليها (لم تُدفع بعد)
     */
    public function approvedPayments(): HasMany
    {
        return $this->hasMany(AgreementPayment::class, 'agreement_id')
            ->where('status', AgreementPayment::STATUS_APPROVED);
    }

    /**
     * علاقة مع من أنشأ الاتفاقية
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * إنشاء رقم اتفاقية تلقائي
     */
    public static function generateAgreementNumber(): string
    {
        return 'AGR-' . date('Y') . '-' . str_pad(self::count() + 1, 6, '0', STR_PAD_LEFT);
    }

    /**
     * تسمية نوع الاتفاقية للعرض
     */
    public static function billingTypeLabels(): array
    {
        return [
            self::BILLING_PER_SESSION => 'بالجلسة',
            self::BILLING_MONTHLY => 'راتب شهري',
            self::BILLING_FULL_COURSE => 'باكورس كامل',
        ];
    }

    public function getBillingTypeLabelAttribute(): string
    {
        return self::billingTypeLabels()[$this->billing_type] ?? $this->billing_type;
    }
}
