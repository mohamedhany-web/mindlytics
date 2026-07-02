<?php

namespace App\Models;

use App\Models\Concerns\AssignsDefaultBranchOnCreate;
use App\Models\Concerns\QueriesByBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentCourseEnrollment extends Model
{
    use AssignsDefaultBranchOnCreate, QueriesByBranch;

    protected $fillable = [
        'branch_id',
        'user_id',
        'advanced_course_id',
        'enrolled_at',
        'activated_at',
        'activated_by',
        'status',
        'progress',
        'notes',
        'invoice_id',
        'payment_id',
        'payment_method',
        'final_price',
        'original_price',
        'discount_amount',
        'coupon_id',
        'hide_from_instructor',
        'scholarship_registration_id',
        'enrollment_type',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'activated_at' => 'datetime',
        'progress' => 'decimal:2',
        'hide_from_instructor' => 'boolean',
    ];

    /**
     * علاقة مع الطالب
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * علاقة مع المستخدم (alias للتوافق)
     */
    public function user(): BelongsTo
    {
        return $this->student();
    }

    /**
     * علاقة مع الكورس
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(AdvancedCourse::class, 'advanced_course_id');
    }

    /**
     * علاقة مع المستخدم الذي فعل التسجيل
     */
    public function activatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'activated_by');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function installmentAgreements()
    {
        return $this->hasMany(InstallmentAgreement::class, 'student_course_enrollment_id');
    }

    /**
     * تحديد ما إذا كان التسجيل نشط
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isHiddenFromInstructor(): bool
    {
        return (bool) ($this->hide_from_instructor ?? false);
    }

    public function scopeVisibleToInstructor($query)
    {
        return $query->where(function ($q) {
            $q->where('hide_from_instructor', false)->orWhereNull('hide_from_instructor');
        });
    }

    public function scholarshipRegistration(): BelongsTo
    {
        return $this->belongsTo(ScholarshipRegistration::class, 'scholarship_registration_id');
    }

    public function isScholarshipEnrollment(): bool
    {
        return ($this->enrollment_type ?? '') === 'scholarship'
            || $this->scholarship_registration_id !== null;
    }

    public function scopeNonScholarship($query)
    {
        return $query->where(function ($q) {
            $q->where(function ($inner) {
                $inner->whereNull('enrollment_type')
                    ->orWhere('enrollment_type', '!=', 'scholarship');
            })->whereNull('scholarship_registration_id');
        });
    }

    public function scopeScholarshipOnly($query)
    {
        return $query->where(function ($q) {
            $q->where('enrollment_type', 'scholarship')
                ->orWhereNotNull('scholarship_registration_id');
        });
    }

    /**
     * تحديد ما إذا كان التسجيل مكتمل
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * الحصول على لون حالة التسجيل
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'active' => 'green',
            'completed' => 'blue',
            'suspended' => 'red',
            default => 'gray'
        };
    }

    /**
     * الحصول على نص حالة التسجيل
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'في الانتظار',
            'active' => 'نشط',
            'completed' => 'مكتمل',
            'suspended' => 'معلق',
            default => 'غير معروف'
        };
    }
}
