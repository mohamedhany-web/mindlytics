<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     * إنشاء رقم خصم تلقائي
     */
    public static function generateDeductionNumber(): string
    {
        return 'DED-' . date('Y') . '-' . str_pad(self::count() + 1, 6, '0', STR_PAD_LEFT);
    }
}
