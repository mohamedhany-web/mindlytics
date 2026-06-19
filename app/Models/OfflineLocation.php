<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfflineLocation extends Model
{
    protected $fillable = [
        'name',
        'address',
        'city',
        'phone',
        'description',
        'capacity',
        'facilities',
        'is_active',
        'hourly_rate',
        'default_wallet_id',
        'manager_user_id',
        'vendor_contact_name',
        'vendor_tax_id',
        'vendor_bank_details',
    ];

    protected $casts = [
        'facilities' => 'array',
        'is_active' => 'boolean',
        'hourly_rate' => 'decimal:2',
    ];

    /**
     * علاقة مع الكورسات الأوفلاين
     */
    public function courses(): HasMany
    {
        return $this->hasMany(OfflineCourse::class, 'location_id');
    }

    /**
     * Scope للأماكن النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function defaultWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'default_wallet_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function usageLogs(): HasMany
    {
        return $this->hasMany(PlaceUsageLog::class, 'offline_location_id');
    }

    public function dailyExpenses(): HasMany
    {
        return $this->hasMany(PlaceDailyExpense::class, 'offline_location_id');
    }

    public function monthlySettlements(): HasMany
    {
        return $this->hasMany(PlaceMonthlySettlement::class, 'offline_location_id');
    }

    public function placeInvoices(): HasMany
    {
        return $this->hasMany(PlaceInvoice::class, 'offline_location_id');
    }
}
