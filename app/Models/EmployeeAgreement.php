<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeAgreement extends Model
{
    protected $fillable = [
        'employee_id',
        'agreement_number',
        'title',
        'description',
        'salary',
        'start_date',
        'end_date',
        'status',
        'contract_terms',
        'agreement_terms',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'salary' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * علاقة مع الموظف
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * علاقة مع منشئ الاتفاقية
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * علاقة مع الخصومات
     */
    public function deductions(): HasMany
    {
        return $this->hasMany(EmployeeSalaryDeduction::class, 'agreement_id');
    }

    /**
     * علاقة مع المدفوعات
     */
    public function payments(): HasMany
    {
        return $this->hasMany(EmployeeSalaryPayment::class, 'agreement_id');
    }

    /**
     * هل تُحتسب رواتب على هذه الاتفاقية؟
     */
    public function isPayrollActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return [
            'draft' => 'مسودة',
            'active' => 'نشط',
            'suspended' => 'مجمّد',
            'terminated' => 'موقوف / مغادرة',
            'completed' => 'مكتمل',
        ];
    }
    public static function generateAgreementNumber(): string
    {
        $year = date('Y');
        $prefix = 'EMP-AGR-'.$year.'-';
        $prefixLength = strlen($prefix);

        $maxSeq = self::query()
            ->where('agreement_number', 'like', $prefix.'%')
            ->pluck('agreement_number')
            ->map(function (string $number) use ($prefixLength) {
                $suffix = substr($number, $prefixLength);

                return ctype_digit($suffix) ? (int) $suffix : 0;
            })
            ->max() ?? 0;

        for ($i = 1; $i <= 50; $i++) {
            $candidate = $prefix.str_pad((string) ($maxSeq + $i), 6, '0', STR_PAD_LEFT);
            if (! self::query()->where('agreement_number', $candidate)->exists()) {
                return $candidate;
            }
        }

        return $prefix.str_pad((string) now()->format('His'), 6, '0', STR_PAD_LEFT);
    }
}
