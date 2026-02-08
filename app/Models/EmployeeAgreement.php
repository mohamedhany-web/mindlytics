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
     * إنشاء رقم اتفاقية تلقائي
     */
    public static function generateAgreementNumber(): string
    {
        return 'EMP-AGR-' . date('Y') . '-' . str_pad(self::count() + 1, 6, '0', STR_PAD_LEFT);
    }
}
