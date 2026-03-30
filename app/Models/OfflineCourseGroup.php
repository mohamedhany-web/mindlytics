<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\OfflineGroupSession;
use Illuminate\Support\Str;

class OfflineCourseGroup extends Model
{
    protected $fillable = [
        'offline_course_id',
        'instructor_id',
        'name',
        'description',
        'max_students',
        'current_students',
        'location',
        'class_time',
        'start_date',
        'end_date',
        'session_duration_hours',
        'location_id',
        'status',
        'is_active',
        'public_slug',
        'public_booking_enabled',
    ];

    protected $casts = [
        'class_time' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'session_duration_hours' => 'decimal:1',
        'is_active' => 'boolean',
        'public_booking_enabled' => 'boolean',
    ];

    /**
     * علاقة مع الكورس الأوفلاين
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(OfflineCourse::class, 'offline_course_id');
    }

    /**
     * علاقة مع المدرب
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * علاقة مع التسجيلات
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(OfflineCourseEnrollment::class, 'group_id');
    }

    /**
     * طلبات حجز معلقة لهذه المجموعة (صفحة الحجز العامة بالرابط).
     */
    public function pendingBookings(): HasMany
    {
        return $this->hasMany(OfflineCourseBooking::class, 'requested_group_id')
            ->where('status', OfflineCourseBooking::STATUS_PENDING);
    }

    public function pendingBookingsCount(): int
    {
        return (int) $this->pendingBookings()->count();
    }

    /**
     * مقاعد متاحة مع احتساب الطلبات المعلقة على هذه المجموعة.
     */
    public function effectiveAvailableSeats(): int
    {
        return max(0, $this->availableSeats() - $this->pendingBookingsCount());
    }

    public function canAcceptPublicBooking(): bool
    {
        if (! $this->public_booking_enabled || ! $this->is_active || $this->status !== 'active') {
            return false;
        }

        return $this->effectiveAvailableSeats() > 0 && filled($this->public_slug);
    }

    public static function generateUniquePublicSlug(string $fromName, ?int $exceptId = null): string
    {
        $base = Str::slug($fromName) ?: 'group';
        $slug = $base;
        $n = 0;

        while (true) {
            $q = static::query()->where('public_slug', $slug);
            if ($exceptId !== null) {
                $q->where('id', '!=', $exceptId);
            }
            if (! $q->exists()) {
                return $slug;
            }
            $n++;
            $slug = $base . '-' . $n;
        }
    }

    /**
     * علاقة مع الأنشطة
     */
    public function activities(): HasMany
    {
        return $this->hasMany(OfflineActivity::class, 'group_id');
    }

    /**
     * علاقة مع الجلسات
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(OfflineGroupSession::class, 'group_id');
    }

    /**
     * علاقة مع المكان
     */
    public function locationModel(): BelongsTo
    {
        return $this->belongsTo(OfflineLocation::class, 'location_id');
    }

    public function upcomingSessions()
    {
        return $this->sessions()->upcoming()->ordered();
    }

    public function availableSeats(): int
    {
        return max(0, $this->max_students - $this->current_students);
    }

    /**
     * علاقة مع الحضور
     */
    public function attendance(): HasMany
    {
        return $this->hasMany(OfflineAttendance::class, 'group_id');
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
}
