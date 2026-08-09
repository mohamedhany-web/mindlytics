<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesDailyKpiPenalty extends Model
{
    protected $fillable = [
        'user_id',
        'work_date',
        'metric_key',
        'actual',
        'target',
        'pct',
        'deduction_id',
        'waived_at',
    ];

    protected function casts(): array
    {
        return [
            'work_date' => 'date',
            'actual' => 'decimal:2',
            'target' => 'decimal:2',
            'pct' => 'decimal:2',
            'waived_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deduction(): BelongsTo
    {
        return $this->belongsTo(EmployeeSalaryDeduction::class, 'deduction_id');
    }

    public function isWaived(): bool
    {
        return $this->waived_at !== null;
    }
}
