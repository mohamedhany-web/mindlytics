<?php

namespace App\Domains\Accounting\Models;

use App\Models\Concerns\QueriesByBranch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Branch;
use App\Models\User;

class Wallet extends Model
{
    use HasFactory, QueriesByBranch;

    protected static function booted(): void
    {
        static::creating(function (Wallet $wallet): void {
            if ($wallet->branch_id !== null && $wallet->branch_id !== '') {
                return;
            }
            if ($wallet->user_id) {
                $bid = User::query()->whereKey($wallet->user_id)->value('branch_id');
                if ($bid !== null) {
                    $wallet->branch_id = $bid;

                    return;
                }
            }
            $default = Branch::defaultAssignableId();
            if ($default !== null) {
                $wallet->branch_id = $default;
            }
        });
    }

    protected $fillable = [
        'branch_id',
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

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * محافظ المنصة للتحصيل فقط (أنشئت من الإدارة، بدون ربط بحساب طالب/شخصي).
     */
    public function scopeAcademyWallets(Builder $query): Builder
    {
        return $query->whereNull('user_id');
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
        return $this->recordMovement('deposit', $amount, false, $notes, [
            'payment_id' => $paymentId,
            'transaction_id' => $transactionId,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * سحب مبلغ
     */
    public function withdraw($amount, $notes = null)
    {
        return $this->recordMovement('withdrawal', $amount, true, $notes, [
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * استرداد مبلغ (سحب من المحفظة مع تسجيل نوع refund)
     */
    public function refund($amount, $paymentId = null, $transactionId = null, $notes = null, $createdBy = null)
    {
        return $this->recordMovement('refund', $amount, true, $notes, [
            'payment_id' => $paymentId,
            'transaction_id' => $transactionId,
            'created_by' => $createdBy ?? auth()->id(),
        ]);
    }

    /**
     * تغيير الرصيد وتسجيل الحركة معاً أو لا شيء، على صف المحفظة مقفولاً (docs/ACCOUNTING_AUDIT.md → M1):
     * - فحص الرصيد يتم على القيمة الحالية في قاعدة البيانات لا على نسخة قديمة في الذاكرة، فسحبان متزامنان لا يجعلان الرصيد سالباً.
     * - لو فشل تسجيل الحركة يُلغى تغيير الرصيد أيضاً (كان الرصيد يتغير بدون حركة).
     *
     * @param  array<string, mixed>  $optional  أعمدة تُكتب فقط إن كانت موجودة في جدول الحركات
     */
    private function recordMovement(string $type, $amount, bool $outgoing, $notes, array $optional): WalletTransaction
    {
        return DB::transaction(function () use ($type, $amount, $outgoing, $notes, $optional) {
            $locked = static::query()->withoutGlobalScopes()->whereKey($this->getKey())->lockForUpdate()->firstOrFail();

            $balanceBefore = (float) $locked->balance;
            if ($outgoing && $balanceBefore < (float) $amount) {
                throw new \Exception('رصيد المحفظة غير كافي');
            }

            $outgoing ? $locked->decrement('balance', $amount) : $locked->increment('balance', $amount);
            $balanceAfter = (float) $locked->fresh()->balance;
            $this->setRawAttributes(array_merge($this->getAttributes(), ['balance' => $locked->balance]), true);

            $table = (new WalletTransaction)->getTable();

            $data = [
                'wallet_id' => $this->id,
                'type' => $type,
                'amount' => $amount,
                'balance_after' => $balanceAfter,
            ];

            if (Schema::hasColumn($table, 'balance_before')) {
                $data['balance_before'] = $balanceBefore;
            }
            if (Schema::hasColumn($table, 'description')) {
                $data['description'] = $notes ?? '';
            } elseif (Schema::hasColumn($table, 'notes')) {
                $data['notes'] = $notes;
            }
            foreach ($optional as $column => $value) {
                if (Schema::hasColumn($table, $column)) {
                    $data[$column] = $value;
                }
            }

            return WalletTransaction::create($data);
        });
    }

    /**
     * الحصول على نوع المحفظة بالعربية
     */
    public function getTypeNameAttribute()
    {
        return static::typeLabel($this->type);
    }
}
