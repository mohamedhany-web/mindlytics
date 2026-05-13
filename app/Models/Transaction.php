<?php

namespace App\Models;

use App\Models\Concerns\QueriesByBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, QueriesByBranch;

    protected static function booted(): void
    {
        static::creating(function (Transaction $transaction): void {
            if ($transaction->branch_id !== null && $transaction->branch_id !== '') {
                return;
            }
            if ($transaction->user_id) {
                $bid = User::query()->whereKey($transaction->user_id)->value('branch_id');
                if ($bid !== null) {
                    $transaction->branch_id = $bid;

                    return;
                }
            }
            if ($transaction->payment_id) {
                $bid = Payment::query()->whereKey($transaction->payment_id)->value('branch_id');
                if ($bid !== null) {
                    $transaction->branch_id = $bid;

                    return;
                }
            }
            if ($transaction->invoice_id) {
                $bid = Invoice::query()->whereKey($transaction->invoice_id)->value('branch_id');
                if ($bid !== null) {
                    $transaction->branch_id = $bid;

                    return;
                }
            }
            $default = Branch::defaultAssignableId();
            if ($default !== null) {
                $transaction->branch_id = $default;
            }
        });
    }

    protected $fillable = [
        'branch_id',
        'transaction_number',
        'user_id',
        'payment_id',
        'invoice_id',
        'expense_id',
        'subscription_id',
        'type',
        'category',
        'amount',
        'currency',
        'description',
        'status',
        'metadata',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
