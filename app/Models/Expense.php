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
        'funding_source',
        'wallet_id',
        'offline_location_id',
        'place_monthly_settlement_id',
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

    public function offlineLocation()
    {
        return $this->belongsTo(OfflineLocation::class, 'offline_location_id');
    }

    public function placeMonthlySettlement()
    {
        return $this->belongsTo(PlaceMonthlySettlement::class, 'place_monthly_settlement_id');
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

    public static function fundingSourceLabels(): array
    {
        return \App\Support\AccountingAnalytics::fundingSourceLabels();
    }

    public static function fundingSourceLabel(?string $source): string
    {
        return \App\Support\AccountingAnalytics::fundingSourceLabel($source);
    }

    public function scopeFromRevenue($query)
    {
        return $query->where('funding_source', \App\Support\AccountingAnalytics::FUNDING_REVENUE);
    }

    public function scopeOutOfPocket($query)
    {
        return $query->where('funding_source', \App\Support\AccountingAnalytics::FUNDING_OUT_OF_POCKET);
    }

    public function getFundingSourceLabelAttribute(): string
    {
        return static::fundingSourceLabel($this->funding_source);
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
