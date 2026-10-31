<?php

namespace App\Domains\Sales\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmAccount extends Model
{
    public const TYPES = [
        'customer' => 'عميل',
        'prospect' => 'محتمل',
        'partner' => 'شريك',
        'other' => 'أخرى',
    ];

    protected $fillable = [
        'name', 'phone', 'email', 'website', 'industry', 'account_type',
        'billing_address', 'description', 'assigned_to', 'created_by',
    ];

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to')->withDefault(['name' => '—']);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault(['name' => '—']);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(CrmContact::class);
    }

    public function cases(): HasMany
    {
        return $this->hasMany(CrmCase::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(SalesLead::class, 'crm_account_id');
    }

    public static function typeLabel(?string $type): string
    {
        return self::TYPES[$type] ?? ($type ?: '—');
    }
}
