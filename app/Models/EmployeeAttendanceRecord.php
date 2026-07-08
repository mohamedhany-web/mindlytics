<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAttendanceRecord extends Model
{
    protected $fillable = [
        'user_id',
        'work_schedule_id',
        'work_date',
        'scheduled_start',
        'scheduled_end',
        'required_minutes',
        'clock_in_at',
        'clock_out_at',
        'worked_minutes',
        'status',
        'is_late',
        'clock_in_ip',
        'clock_out_ip',
        'metadata',
        'created_by',
        'late_deduction_id',
        'absence_deduction_id',
        'incomplete_deduction_id',
        'presence_deduction_id',
    ];

    protected $casts = [
        'work_date' => 'date',
        'scheduled_start' => 'datetime',
        'scheduled_end' => 'datetime',
        'clock_in_at' => 'datetime',
        'clock_out_at' => 'datetime',
        'worked_minutes' => 'integer',
        'required_minutes' => 'integer',
        'is_late' => 'boolean',
        'metadata' => 'array',
    ];

    public static function statusLabels(): array
    {
        return [
            'pending' => 'في انتظار الحضور',
            'active' => 'جاري العمل',
            'completed' => 'مكتمل',
            'absent' => 'غائب',
            'late' => 'متأخر',
            'incomplete' => 'غير مكتمل',
            'on_leave' => 'إجازة',
            'off_day' => 'راحة أسبوعية',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workSchedule(): BelongsTo
    {
        return $this->belongsTo(WorkSchedule::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lateDeduction(): BelongsTo
    {
        return $this->belongsTo(EmployeeSalaryDeduction::class, 'late_deduction_id');
    }

    public function absenceDeduction(): BelongsTo
    {
        return $this->belongsTo(EmployeeSalaryDeduction::class, 'absence_deduction_id');
    }

    public function incompleteDeduction(): BelongsTo
    {
        return $this->belongsTo(EmployeeSalaryDeduction::class, 'incomplete_deduction_id');
    }

    public function presenceDeduction(): BelongsTo
    {
        return $this->belongsTo(EmployeeSalaryDeduction::class, 'presence_deduction_id');
    }

    public function totalDeductionAmount(): float
    {
        return (float) ($this->lateDeduction?->amount ?? 0)
            + (float) ($this->absenceDeduction?->amount ?? 0)
            + (float) ($this->incompleteDeduction?->amount ?? 0)
            + (float) ($this->presenceDeduction?->amount ?? 0);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->clock_in_at && ! $this->clock_out_at;
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed' && $this->clock_out_at !== null;
    }
}
