<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_number',
        'title',
        'description',
        'category',
        'amount',
        'currency',
        'expense_date',
        'payment_method',
        'wallet_id',
        'reference_number',
        'attachment',
        'status',
        'approved_by',
        'approved_at',
        'transaction_id',
        'invoice_id',
        'notes',
        'metadata',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'approved_at' => 'datetime',
        'metadata' => 'array',
    ];

    // العلاقات
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Methods
    public static function categoryLabels(): array
    {
        return [
            'operational' => 'تشغيلي',
            'marketing' => 'تسويق',
            'salaries' => 'رواتب',
            'utilities' => 'مرافق',
            'equipment' => 'معدات',
            'maintenance' => 'صيانة',
            'other' => 'أخرى',
        ];
    }

    public static function categoryLabel(?string $category): string
    {
        if ($category === null || $category === '') {
            return 'غير محدد';
        }

        return static::categoryLabels()[$category] ?? $category;
    }

    public function getCategoryLabelAttribute()
    {
        return static::categoryLabel($this->category);
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'في الانتظار',
            'approved' => 'موافق عليه',
            'rejected' => 'مرفوض',
        ];

        return $statuses[$this->status] ?? 'غير محدد';
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
        ];

        return $colors[$this->status] ?? 'gray';
    }
}
