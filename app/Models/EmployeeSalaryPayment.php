<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSalaryPayment extends Model
{
    protected $fillable = [
        'employee_id',
        'agreement_id',
        'payment_number',
        'base_salary',
        'total_deductions',
        'total_additions',
        'net_salary',
        'period_month',
        'period_year',
        'payment_date',
        'paid_at',
        'status',
        'notes',
        'transfer_receipt_path',
        'wallet_id',
        'expense_id',
        'created_by',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_additions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'payment_date' => 'date',
        'paid_at' => 'date',
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
     * علاقة مع منشئ الدفعة
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    /**
     * إنشاء رقم دفعة تلقائي
     */
    public static function generatePaymentNumber(): string
    {
        return 'PAY-' . date('Y') . '-' . str_pad(self::count() + 1, 6, '0', STR_PAD_LEFT);
    }
}
