<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class EmployeeSalaryDeduction extends Model
{
    protected $fillable = [
        'employee_id',
        'agreement_id',
        'deduction_number',
        'title',
        'description',
        'amount',
        'type',
        'deduction_date',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'deduction_date' => 'date',
    ];

    /**
     * علاقة مع الموظف
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * علاقة مع الاتفاقية
     */
    public function agreement(): BelongsTo
    {
        return $this->belongsTo(EmployeeAgreement::class, 'agreement_id');
    }

    /**
     * علاقة مع منشئ الخصم
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * إنشاء سجل خصم مع رقم تلقائي داخل معاملة واحدة (قفل + أقصى تسلسل للسنة).
     *
     * @param  array<string, mixed>  $attributes
     */
    public static function createWithAutoDeductionNumber(array $attributes): self
    {
        return DB::transaction(function () use ($attributes) {
            $attributes['deduction_number'] = self::allocateNextDeductionNumber();

            return self::create($attributes);
        });
    }

    /**
     * تخصيص الرقم التالي للسنة الحالية — يُستدعى فقط من داخل DB::transaction.
     */
    private static function allocateNextDeductionNumber(): string
    {
        $year = date('Y');
        $prefix = 'DED-' . $year . '-';

        self::query()
            ->where('deduction_number', 'like', $prefix.'%')
            ->orderByDesc('deduction_number')
            ->lockForUpdate()
            ->first();

        $maxSuffix = self::maxNumericSuffixForPrefix($prefix);

        return $prefix.str_pad((string) ($maxSuffix + 1), 6, '0', STR_PAD_LEFT);
    }

    /**
     * أقصى لاحقة رقمية لبادئة السنة (لا يعتمد على العدد لتفادي التكرار بعد الحذف).
     */
    private static function maxNumericSuffixForPrefix(string $prefix): int
    {
        $max = self::query()
            ->where('deduction_number', 'like', $prefix.'%')
            ->pluck('deduction_number')
            ->map(function (string $number): int {
                return preg_match('/^DED-\d{4}-(\d{6})$/', $number, $m)
                    ? (int) $m[1]
                    : 0;
            })
            ->max();

        return (int) ($max ?? 0);
    }
}
