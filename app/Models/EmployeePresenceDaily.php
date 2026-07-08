<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePresenceDaily extends Model
{
    protected $table = 'employee_presence_daily';

    protected $fillable = [
        'user_id',
        'work_date',
        'employee_attendance_record_id',
        'first_seen_at',
        'last_seen_at',
        'heartbeat_count',
        'online_seconds',
        'away_seconds',
        'offline_seconds',
        'violation_count',
    ];

    protected $casts = [
        'work_date' => 'date',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendanceRecord(): BelongsTo
    {
        return $this->belongsTo(EmployeeAttendanceRecord::class, 'employee_attendance_record_id');
    }
}
