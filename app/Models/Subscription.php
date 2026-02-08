<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_type',
        'plan_name',
        'price',
        'start_date',
        'end_date',
        'status',
        'auto_renew',
        'billing_cycle',
        'invoice_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'auto_renew' => 'boolean',
    ];

    public static function typeLabels(): array
    {
        return [
            'monthly' => 'شهري',
            'quarterly' => 'ربع سنوي',
            'yearly' => 'سنوي',
            'lifetime' => 'مدى الحياة',
            'trial' => 'تجريبي',
            'custom' => 'مخصص',
        ];
    }

    public static function billingCycleLabels(): array
    {
        return [
            'monthly' => 'كل شهر',
            'quarterly' => 'كل 3 أشهر',
            'yearly' => 'سنوياً',
            'biannual' => 'كل 6 أشهر',
            'weekly' => 'أسبوعي',
        ];
    }

    public static function typeLabel(?string $type): string
    {
        if (!$type) {
            return 'غير محدد';
        }

        return static::typeLabels()[strtolower($type)] ?? $type;
    }

    public static function billingCycleLabel(?string $cycle): string
    {
        if (!$cycle) {
            return 'غير محدد';
        }

        return static::billingCycleLabels()[strtolower($cycle)] ?? $cycle;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Invoice::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function isActive()
    {
        return $this->status === 'active' &&
               (!$this->end_date || $this->end_date >= now());
    }

    public function isExpired()
    {
        return $this->end_date && $this->end_date < now();
    }
}
