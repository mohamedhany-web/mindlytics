<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'account_number',
        'bank_name',
        'account_holder',
        'notes',
        'is_active',
        'balance',
        'pending_balance',
        'currency',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'pending_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * خريطة أنواع المحافظ
     */
    public static function typeLabels(): array
    {
        return [
            'vodafone_cash' => 'فودافون كاش',
            'instapay' => 'إنستا باي',
            'bank_transfer' => 'تحويل بنكي',
            'cash' => 'كاش',
            'other' => 'أخرى',
        ];
    }

    /**
     * الحصول على تسمية نوع محدد
     */
    public static function typeLabel(?string $type): string
    {
        if ($type === null || $type === '') {
            return 'غير محدد';
        }

        return static::typeLabels()[$type] ?? $type;
    }

    /**
     * العلاقة مع المعاملات
     */
    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * العلاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * العلاقة مع التقارير
     */
    public function reports()
    {
        return $this->hasMany(WalletReport::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * إيداع مبلغ
     */
    public function deposit($amount, $paymentId = null, $transactionId = null, $notes = null)
    {
        $balanceBefore = $this->balance;
        $this->increment('balance', $amount);
        
        return WalletTransaction::create([
            'wallet_id' => $this->id,
            'payment_id' => $paymentId,
            'transaction_id' => $transactionId,
            'type' => 'deposit',
            'amount' => $amount,
            'balance_after' => $this->balance,
            'notes' => $notes,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * سحب مبلغ
     */
    public function withdraw($amount, $notes = null)
    {
        if ($this->balance < $amount) {
            throw new \Exception('رصيد المحفظة غير كافي');
        }

        $balanceBefore = $this->balance;
        $this->decrement('balance', $amount);
        
        return WalletTransaction::create([
            'wallet_id' => $this->id,
            'type' => 'withdrawal',
            'amount' => $amount,
            'balance_after' => $this->balance,
            'notes' => $notes,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * الحصول على نوع المحفظة بالعربية
     */
    public function getTypeNameAttribute()
    {
        return static::typeLabel($this->type);
    }
}
