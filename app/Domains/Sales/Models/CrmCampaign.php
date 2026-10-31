<?php

namespace App\Domains\Sales\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmCampaign extends Model
{
    public const STATUSES = [
        'planning' => 'تخطيط',
        'active' => 'نشطة',
        'complete' => 'مكتملة',
        'cancelled' => 'ملغاة',
    ];

    public const TYPES = [
        'meta' => 'Meta Ads',
        'whatsapp' => 'واتساب',
        'email' => 'بريد',
        'sms' => 'SMS',
        'other' => 'أخرى',
    ];

    protected $fillable = [
        'name', 'status', 'campaign_type', 'start_date', 'end_date', 'budget',
        'leads_count', 'description', 'assigned_to', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'budget' => 'decimal:2',
            'leads_count' => 'integer',
        ];
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to')->withDefault(['name' => '—']);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(SalesLead::class, 'crm_campaign_id');
    }

    public static function statusLabel(?string $status): string
    {
        return self::STATUSES[$status] ?? ($status ?: '—');
    }

    public static function typeLabel(?string $type): string
    {
        return self::TYPES[$type] ?? ($type ?: '—');
    }
}
