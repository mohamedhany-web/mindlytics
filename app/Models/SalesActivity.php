<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesActivity extends Model
{
    public const TYPES = [
        'note' => 'ملاحظة',
        'call' => 'مكالمة',
        'meeting' => 'اجتماع / ديمو',
        'follow_up' => 'متابعة',
        'whatsapp' => 'واتساب',
        'email' => 'بريد',
        'stage_change' => 'تغيير مرحلة',
        'other' => 'أخرى',
    ];

    protected $fillable = [
        'sales_lead_id',
        'user_id',
        'type',
        'title',
        'body',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(SalesLead::class, 'sales_lead_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function typeLabel(string $type): string
    {
        return self::TYPES[$type] ?? $type;
    }
}
