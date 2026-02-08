<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
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
