<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OfflineCourse extends Model
{
    protected $fillable = [
        'title',
        'description',
        'instructor_id',
        'location_id',
        'location',
        'start_date',
        'end_date',
        'duration_hours',
        'sessions_count',
        'price',
        'max_students',
        'current_students',
        'status',
        'is_active',
        'public_booking_enabled',
        'student_online_portal_enabled',
        'online_only',
        'booking_opens_at',
        'booking_closes_at',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'public_booking_enabled' => 'boolean',
        'student_online_portal_enabled' => 'boolean',
        'online_only' => 'boolean',
        'booking_opens_at' => 'datetime',
        'booking_closes_at' => 'datetime',
    ];

    /**
     * علاقة مع المدرب
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * علاقة مع المكان
     */
    public function locationModel(): BelongsTo
    {
        return $this->belongsTo(OfflineLocation::class, 'location_id');
    }

    /**
     * علاقة مع المجموعات
     */
    public function groups(): HasMany
    {
        return $this->hasMany(OfflineCourseGroup::class, 'offline_course_id');
    }

    /**
     * علاقة مع التسجيلات
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(OfflineCourseEnrollment::class, 'offline_course_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(OfflineCourseBooking::class, 'offline_course_id');
    }

    /**
     * علاقة مع الطلاب (من خلال التسجيلات)
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'offline_course_enrollments', 'offline_course_id', 'user_id')
                    ->withPivot(['status', 'progress', 'enrolled_at', 'group_id'])
                    ->withTimestamps();
    }

    /**
     * علاقة مع الأنشطة
     */
    public function activities(): HasMany
    {
        return $this->hasMany(OfflineActivity::class, 'offline_course_id');
    }

    /**
     * علاقة مع اتفاقيات المدربين
     */
    public function instructorAgreements(): HasMany
    {
        return $this->hasMany(InstructorAgreement::class, 'offline_course_id');
    }

    /**
     * علاقة مع الحضور
     */
    public function attendance(): HasMany
    {
        return $this->hasMany(OfflineAttendance::class, 'offline_course_id');
    }

    /**
     * علاقة مع موارد الكورس (أوفلاين)
     */
    public function resources(): HasMany
    {
        return $this->hasMany(OfflineCourseResource::class, 'offline_course_id');
    }

    /**
     * علاقة مع محاضرات الكورس (أوفلاين)
     */
    public function offlineLectures(): HasMany
    {
        return $this->hasMany(OfflineLecture::class, 'offline_course_id');
    }

    /**
     * أقسام المنهج / التوصيف (يُبنى من المدرب)
     */
    public function offlineCourseSections(): HasMany
    {
        return $this->hasMany(OfflineCourseSection::class, 'offline_course_id');
    }

    public function offlineCurriculumNotes(): HasMany
    {
        return $this->hasMany(OfflineCurriculumNote::class, 'offline_course_id');
    }

    /**
     * علاقة مع امتحانات الأكاديمية المرتبطة بالكورس الأوفلاين
     */
    public function exams(): HasMany
    {
        return $this->hasMany(AdvancedExam::class, 'offline_course_id');
    }

    /**
     * Scope للكورسات النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    /**
     * Scope للكورسات حسب المدرب
     */
    public function scopeForInstructor($query, $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    /**
     * التحقق من إمكانية التسجيل
     */
    public function canEnroll(): bool
    {
        return $this->is_active 
            && $this->status === 'active' 
            && $this->current_students < $this->max_students;
    }

    /**
     * تاريخ إغلاق الحجز الفعلي: إن وُجدت «نهاية الحجز» بوقت 00:00:00 نعتبرها نهاية ذلك اليوم (لأن حقول datetime-local غالباً ترسل اليوم بدون وقت).
     */
    public function bookingClosesAtEffective(): ?Carbon
    {
        if (! $this->booking_closes_at) {
            return null;
        }

        $c = $this->booking_closes_at;
        if ($c->format('H:i:s') === '00:00:00') {
            return $c->copy()->endOfDay();
        }

        return $c;
    }

    /**
     * الجدول الزمني للحجز (بدون اشتراط تفعيل «الحجز العام» على الكورس).
     * يُستخدم لصفحة رابط المجموعة؛ كتالوج الطلاب يشترط أيضاً public_booking_enabled.
     */
    public function isOfflineBookingScheduleOpen(): bool
    {
        if (! $this->is_active || $this->status !== 'active') {
            return false;
        }

        $now = now();
        if ($this->booking_opens_at && $now->lt($this->booking_opens_at)) {
            return false;
        }

        $closes = $this->bookingClosesAtEffective();
        if ($closes && $now->gt($closes)) {
            return false;
        }

        return true;
    }

    /**
     * نافذة الحجز عند مشاركة رابط مباشر للطالب: «الحجز العام» مفعّل + الجدول الزمني.
     */
    public function isPublicBookingWindowOpen(): bool
    {
        return $this->public_booking_enabled && $this->isOfflineBookingScheduleOpen();
    }

    public function scopeWithOpenPublicBooking($query)
    {
        $now = now();

        return $query
            ->where('public_booking_enabled', true)
            ->where('is_active', true)
            ->where('status', 'active')
            ->where(function ($q) use ($now) {
                $q->whereNull('booking_opens_at')->orWhere('booking_opens_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('booking_closes_at')
                    ->orWhere(function ($q2) use ($now) {
                        $q2->whereRaw('(HOUR(booking_closes_at) = 0 AND MINUTE(booking_closes_at) = 0 AND SECOND(booking_closes_at) = 0)')
                            ->whereRaw('CONCAT(DATE(booking_closes_at), " 23:59:59") >= ?', [$now->format('Y-m-d H:i:s')]);
                    })
                    ->orWhere(function ($q2) use ($now) {
                        $q2->whereRaw('NOT (HOUR(booking_closes_at) = 0 AND MINUTE(booking_closes_at) = 0 AND SECOND(booking_closes_at) = 0)')
                            ->where('booking_closes_at', '>=', $now);
                    });
            });
    }

    /**
     * زيادة عدد الطلاب
     */
    public function incrementStudents(): void
    {
        $this->increment('current_students');
    }

    /**
     * تقليل عدد الطلاب
     */
    public function decrementStudents(): void
    {
        $this->decrement('current_students');
    }
}
