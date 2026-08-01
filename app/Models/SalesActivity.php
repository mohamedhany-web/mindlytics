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

    public const OUTCOMES = [
        'interested' => 'مهتم (Interested)',
        'follow_up' => 'متابعة (Follow Up)',
        'not_interested' => 'غير مهتم (Not Interested)',
        'no_answer' => 'لم يرد (No Answer)',
        'wrong_number' => 'رقم خطأ (Wrong Number)',
        'closed_won' => 'إغلاق ناجح (Closed Won)',
        'closed_lost' => 'إغلاق خسارة (Closed Lost)',
    ];

    /** نتائج تعني أن المكالمة تم الرد عليها */
    public const ANSWERED_OUTCOMES = [
        'interested',
        'follow_up',
        'not_interested',
        'closed_won',
        'closed_lost',
    ];

    /** نتائج تُحسب محادثة مؤهلة */
    public const QUALIFIED_OUTCOMES = [
        'interested',
        'follow_up',
        'closed_won',
    ];

    protected $fillable = [
        'sales_lead_id',
        'user_id',
        'type',
        'outcome',
        'duration_seconds',
        'recording_url',
        'title',
        'body',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'duration_seconds' => 'integer',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(SalesLead::class, 'sales_lead_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault(['name' => '—']);
    }

    public static function typeLabel(string $type): string
    {
        return self::TYPES[$type] ?? $type;
    }

    public static function outcomeLabel(?string $outcome): string
    {
        if ($outcome === null || $outcome === '') {
            return '—';
        }

        return self::OUTCOMES[$outcome] ?? $outcome;
    }

    public function isAnsweredCall(): bool
    {
        return $this->type === 'call' && in_array((string) $this->outcome, self::ANSWERED_OUTCOMES, true);
    }

    public function isQualifiedConversation(): bool
    {
        return $this->type === 'call' && in_array((string) $this->outcome, self::QUALIFIED_OUTCOMES, true);
    }
}
