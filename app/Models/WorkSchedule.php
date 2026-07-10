<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkSchedule extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'required_hours',
        'work_days',
        'grace_minutes',
        'early_access_minutes',
        'is_active',
        'description',
    ];

    protected $casts = [
        'required_hours' => 'decimal:2',
        'work_days' => 'array',
        'grace_minutes' => 'integer',
        'early_access_minutes' => 'integer',
        'is_active' => 'boolean',
    ];

    public static function defaultWorkDays(): array
    {
        return [0, 1, 2, 3, 4];
    }

    public function employees(): HasMany
    {
        return $this->hasMany(User::class, 'work_schedule_id');
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(EmployeeAttendanceRecord::class);
    }

    public function workDaysLabel(): string
    {
        return 'من ملف الموظف (يوم الإجازة الأسبوعية)';
    }

    public function timeRangeLabel(): string
    {
        $start = is_string($this->start_time) ? substr($this->start_time, 0, 5) : $this->start_time?->format('H:i');
        $end = is_string($this->end_time) ? substr($this->end_time, 0, 5) : $this->end_time?->format('H:i');

        return ($start ?? '—') . ' — ' . ($end ?? '—');
    }
}
