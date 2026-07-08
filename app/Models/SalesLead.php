<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesLead extends Model
{
    use SoftDeletes;

    public const STAGES = [
        'new' => 'جديد',
        'contacted' => 'تم التواصل',
        'qualified' => 'مؤهل',
        'proposal' => 'عرض سعر',
        'won' => 'مكتمل / فوز',
        'lost' => 'خسارة',
    ];

    public const SOURCES = [
        'website' => 'الموقع',
        'referral' => 'إحالة',
        'call' => 'مكالمة واردة',
        'social' => 'وسائل التواصل',
        'whatsapp' => 'واتساب',
        'event' => 'فعالية',
        'other' => 'أخرى',
    ];

    public const PRIORITIES = [
        'low' => 'منخفض',
        'normal' => 'عادي',
        'high' => 'مرتفع',
        'urgent' => 'عاجل',
    ];

    public const LOSS_REASONS = [
        'price_high' => 'السعر مرتفع',
        'no_budget' => 'لا يوجد ميزانية',
        'competitor' => 'اختار منافس',
        'not_decision_maker' => 'ليس صاحب قرار',
        'timing' => 'التوقيت غير مناسب',
        'no_follow_up' => 'عدم الاستجابة للمتابعة',
        'wrong_fit' => 'غير مناسب للاحتياج',
        'other' => 'أخرى',
    ];

    /** بعد كم يوم بلا تواصل نعتبر العميل «يحتاج متابعة» في التقارير */
    public const STALE_CONTACT_DAYS = 10;

    protected $fillable = [
        'assigned_to',
        'created_by',
        'category_id',
        'sales_lead_group_id',
        'import_batch',
        'name',
        'phone',
        'email',
        'company',
        'source',
        'stage',
        'priority',
        'interest',
        'expected_value',
        'notes',
        'next_follow_up_at',
        'last_contacted_at',
        'closed_at',
        'won_confirmed_at',
        'won_confirmed_by',
        'commission_amount',
        'commission_transaction_id',
        'commission_notes',
        'lost_reason',
        'csat_rating',
        'csat_comment',
        'csat_recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'expected_value' => 'decimal:2',
            'next_follow_up_at' => 'datetime',
            'last_contacted_at' => 'datetime',
            'closed_at' => 'datetime',
            'won_confirmed_at' => 'datetime',
            'commission_amount' => 'decimal:2',
            'csat_recorded_at' => 'datetime',
        ];
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SalesLeadCategory::class, 'category_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(SalesLeadGroup::class, 'sales_lead_group_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(SalesActivity::class)->orderByDesc('created_at');
    }

    public function scopeForAssignee($query, int $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeForVisibleSalesUser($query, User $user)
    {
        if ($user->isSalesManager()) {
            $ids = app(\App\Services\SalesTeamService::class)->visibleAssigneeIds($user);
            if ($ids === []) {
                return $query->whereRaw('1 = 0');
            }

            return $query->whereIn('assigned_to', $ids);
        }

        return $query->forAssignee((int) $user->id);
    }

    public function scopeOpenPipeline($query)
    {
        return $query->whereNotIn('stage', ['won', 'lost']);
    }

    public function scopePendingWinApproval($query)
    {
        return $query->where('stage', 'won')->whereNull('won_confirmed_at');
    }

    public function isPendingWinApproval(): bool
    {
        return $this->stage === 'won' && $this->won_confirmed_at === null;
    }

    public function isWinConfirmed(): bool
    {
        return $this->stage === 'won' && $this->won_confirmed_at !== null;
    }

    public static function stageLabel(string $stage): string
    {
        return self::STAGES[$stage] ?? $stage;
    }

    public static function sourceLabel(string $source): string
    {
        return self::SOURCES[$source] ?? $source;
    }

    public static function priorityLabel(?string $priority): string
    {
        if ($priority === null || $priority === '') {
            return self::PRIORITIES['normal'];
        }

        return self::PRIORITIES[$priority] ?? $priority;
    }

    public function isOpen(): bool
    {
        return ! in_array($this->stage, ['won', 'lost'], true);
    }

    public function isFollowUpOverdue(): bool
    {
        if (! $this->isOpen() || ! $this->next_follow_up_at) {
            return false;
        }

        return $this->next_follow_up_at->isPast();
    }

    public function isStaleContact(?int $days = null): bool
    {
        $days ??= self::STALE_CONTACT_DAYS;
        if (! $this->isOpen()) {
            return false;
        }

        $ref = $this->last_contacted_at ?? $this->created_at;

        return $ref->copy()->addDays($days)->isPast();
    }

    public static function shouldTouchLastContact(string $activityType): bool
    {
        return $activityType !== 'stage_change';
    }

    public function touchLastContactFromActivity(string $activityType): void
    {
        if (! self::shouldTouchLastContact($activityType)) {
            return;
        }

        $this->newQuery()->whereKey($this->id)->update(['last_contacted_at' => now()]);
        $this->last_contacted_at = now();
    }

    public function scopeOrderByPriorityDesc($query)
    {
        return $query->orderByRaw("FIELD(priority, 'urgent','high','normal','low')");
    }
}
