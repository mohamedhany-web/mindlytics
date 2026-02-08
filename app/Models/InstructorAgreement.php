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

    protected $fillable = [
        'instructor_id',
        'offline_course_id',
        'agreement_number',
        'title',
        'description',
        'start_date',
        'end_date',
        'salary_per_session',
        'sessions_count',
        'total_amount',
        'payment_status',
        'status',
        'terms',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'salary_per_session' => 'decimal:2',
        'total_amount' => 'decimal:2',
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
}
