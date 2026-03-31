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
        'online_slug',
        'online_booking_enabled',
        'max_students_online',
        'current_students_online',
    ];

    protected $casts = [
        'class_time' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'session_duration_hours' => 'decimal:1',
        'is_active' => 'boolean',
        'public_booking_enabled' => 'boolean',
        'online_booking_enabled' => 'boolean',
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
    public function pendingBookings(string $channel = 'offline'): HasMany
    {
        return $this->hasMany(OfflineCourseBooking::class, 'requested_group_id')
            ->where('booking_channel', $channel)
            ->where('status', OfflineCourseBooking::STATUS_PENDING);
    }

    public function pendingBookingsCount(string $channel = 'offline'): int
    {
        return (int) $this->pendingBookings($channel)->count();
    }

    /**
     * مقاعد متاحة مع احتساب الطلبات المعلقة على هذه المجموعة.
     */
    public function effectiveAvailableSeats(string $channel = 'offline'): int
    {
        return max(0, $this->availableSeats($channel) - $this->pendingBookingsCount($channel));
    }

    public function canAcceptPublicBooking(string $channel = 'offline'): bool
    {
        $enabled = $channel === 'online'
            ? (bool) $this->online_booking_enabled
            : (bool) $this->public_booking_enabled;
        $slug = $channel === 'online'
            ? $this->online_slug
            : $this->public_slug;

        if (! $enabled || ! $this->is_active || $this->status !== 'active') {
            return false;
        }

        return $this->effectiveAvailableSeats($channel) > 0 && filled($slug);
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

    public static function generateUniqueOnlineSlug(string $fromName, ?int $exceptId = null): string
    {
        $base = Str::slug($fromName) ?: 'group-online';
        $slug = $base;
        $n = 0;

        while (true) {
            $q = static::query()->where('online_slug', $slug);
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

    public function availableSeats(string $channel = 'offline'): int
    {
        if ($channel === 'online') {
            return max(0, (int) $this->max_students_online - (int) $this->current_students_online);
        }

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
    public function canEnroll(string $channel = 'offline'): bool
    {
        $available = $this->availableSeats($channel);

        return $this->is_active 
            && $this->status === 'active' 
            && $available > 0;
    }
}
