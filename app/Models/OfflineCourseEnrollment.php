<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfflineCourseEnrollment extends Model
{
    protected $fillable = [
        'user_id',
        'offline_course_id',
        'group_id',
        'enrollment_channel',
        'enrolled_at',
        'status',
        'progress',
        'attendance_count',
        'absence_count',
        'notes',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'payment_status',
        'invoice_id',
        'payment_method',
        'payment_notes',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'progress' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    /**
     * علاقة مع الطالب
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * علاقة مع الكورس الأوفلاين
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(OfflineCourse::class, 'offline_course_id');
    }

    /**
     * علاقة مع المجموعة
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(OfflineCourseGroup::class, 'group_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function installmentAgreements(): HasMany
    {
        return $this->hasMany(InstallmentAgreement::class, 'offline_course_enrollment_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isFullyPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isPartiallyPaid(): bool
    {
        return $this->payment_status === 'partial';
    }

    public function calculateRemaining(): float
    {
        return max(0, (float)$this->total_amount - (float)$this->paid_amount);
    }

    public function updatePaymentStatus(): void
    {
        $remaining = $this->calculateRemaining();
        $this->remaining_amount = $remaining;

        if ($remaining <= 0 && (float)$this->total_amount > 0) {
            $this->payment_status = 'paid';
        } elseif ((float)$this->paid_amount > 0) {
            $this->payment_status = 'partial';
        } else {
            $this->payment_status = 'unpaid';
        }
        $this->save();
    }
}
