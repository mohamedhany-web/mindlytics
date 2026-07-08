<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePresenceViolation extends Model
{
    public const STATUS_OPEN = 'open';

    public const STATUS_CLOSED = 'closed';

    public const REASON_NO_HEARTBEAT = 'no_heartbeat';

    public const REASON_SESSION_EXPIRED = 'session_expired';

    public const REASON_BROWSER_CLOSED = 'browser_closed';

    protected $fillable = [
        'user_id',
        'work_date',
        'employee_attendance_record_id',
        'started_at',
        'ended_at',
        'duration_seconds',
        'reason',
        'status',
        'acknowledged_by',
        'acknowledged_at',
        'ip_address',
        'notes',
    ];

    protected $casts = [
        'work_date' => 'date',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'acknowledged_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendanceRecord(): BelongsTo
    {
        return $this->belongsTo(EmployeeAttendanceRecord::class, 'employee_attendance_record_id');
    }

    public function acknowledgedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('work_date', $date);
    }
}
