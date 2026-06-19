<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class EmployeeSalaryAddition extends Model
{
    protected $fillable = [
        'employee_id',
        'agreement_id',
        'addition_number',
        'title',
        'description',
        'amount',
        'type',
        'addition_date',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'addition_date' => 'date',
    ];

    public static function typeLabels(): array
    {
        return [
            'bonus' => 'مكافأة',
            'overtime' => 'ساعات إضافية',
            'allowance' => 'بدل',
            'incentive' => 'حافز',
            'other' => 'أخرى',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function agreement(): BelongsTo
    {
        return $this->belongsTo(EmployeeAgreement::class, 'agreement_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function createWithAutoNumber(array $attributes): self
    {
        return DB::transaction(function () use ($attributes) {
            $attributes['addition_number'] = self::allocateNextNumber();

            return self::create($attributes);
        });
    }

    private static function allocateNextNumber(): string
    {
        $year = date('Y');
        $prefix = 'ADD-'.$year.'-';

        self::query()
            ->where('addition_number', 'like', $prefix.'%')
            ->orderByDesc('addition_number')
            ->lockForUpdate()
            ->first();

        $max = self::query()
            ->where('addition_number', 'like', $prefix.'%')
            ->pluck('addition_number')
            ->map(fn (string $n) => preg_match('/^ADD-\d{4}-(\d{6})$/', $n, $m) ? (int) $m[1] : 0)
            ->max();

        return $prefix.str_pad((string) ((int) ($max ?? 0) + 1), 6, '0', STR_PAD_LEFT);
    }
}
