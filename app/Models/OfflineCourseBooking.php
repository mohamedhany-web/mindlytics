<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfflineCourseBooking extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'offline_course_id',
        'requested_group_id',
        'wallet_id',
        'payment_method',
        'payment_proof',
        'student_notes',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
        'assigned_group_id',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(OfflineCourse::class, 'offline_course_id');
    }

    public function requestedGroup(): BelongsTo
    {
        return $this->belongsTo(OfflineCourseGroup::class, 'requested_group_id');
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'wallet_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function assignedGroup(): BelongsTo
    {
        return $this->belongsTo(OfflineCourseGroup::class, 'assigned_group_id');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
