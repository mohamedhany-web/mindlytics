<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesLead extends Model
{
    use SoftDeletes;

    /** Academy sales journey */
    public const STAGES = [
        'new_lead' => 'New Lead',
        'first_contact' => 'First Contact',
        'no_answer' => 'No Answer',
        'connected' => 'Connected',
        'qualification' => 'Qualification',
        'interested' => 'Interested',
        'objection' => 'Objection',
        'follow_up_scheduled' => 'Follow-up Scheduled',
        'offer_sent' => 'Offer Sent',
        'payment_pending' => 'Payment Pending',
        'payment_received' => 'Payment Received',
        'enrollment_completed' => 'Enrollment Completed',
        'upsell' => 'Upsell',
        'dormant' => 'Dormant Lead',
        'lost' => 'Lost',
    ];

    /** مراحل مغلقة خارج الأنبوب النشط */
    public const CLOSED_STAGES = [
        'lost',
        'dormant',
    ];

    /** مرحلة الفوز الرسمية (عمولة / موافقة) */
    public const WON_STAGE = 'enrollment_completed';

    /** مراحل تُحسب نجاحاً في التقارير */
    public const WON_LIKE_STAGES = [
        'enrollment_completed',
        'upsell',
    ];

    /** مراحل تُحسب تسجيلاً مدفوعاً لـ SOS */
    public const PAID_STAGES = [
        'payment_received',
        'enrollment_completed',
        'upsell',
    ];

    public const LEGACY_STAGE_MAP = [
        'new' => 'new_lead',
        'contacted' => 'connected',
        'qualified' => 'qualification',
        'proposal' => 'offer_sent',
        'won' => 'enrollment_completed',
    ];

    public const CONNECTED_DISPOSITIONS = [
        'interested' => 'مهتم',
        'busy' => 'مشغول',
        'callback' => 'يريد معاودة الاتصال',
        'info_only' => 'يسأل فقط',
        'wrong_number' => 'رقم خطأ',
    ];

    public const PROFILE_TYPES = [
        'student' => 'طالب',
        'graduate' => 'خريج',
        'employee' => 'موظف',
        'other' => 'أخرى',
    ];

    /** فئات عمرية للتأهيل (بدل رقم سن واحد) */
    public const AGE_RANGES = [
        'under_18' => 'أقل من 18',
        '18_24' => '18 – 24',
        '25_30' => '25 – 30',
        '31_35' => '31 – 35',
        '36_40' => '36 – 40',
        '41_50' => '41 – 50',
        'over_50' => 'أكثر من 50',
    ];

    public static function ageRangeMidpoint(string $range): ?int
    {
        return match ($range) {
            'under_18' => 16,
            '18_24' => 21,
            '25_30' => 27,
            '31_35' => 33,
            '36_40' => 38,
            '41_50' => 45,
            'over_50' => 55,
            default => null,
        };
    }

    public static function ageRangeLabel(?string $range): string
    {
        if (! $range) {
            return '—';
        }

        return self::AGE_RANGES[$range] ?? $range;
    }

    public const INTEREST_PCTS = [40, 60, 80, 100];

    public const OBJECTION_REASONS = [
        'price' => 'السعر',
        'timing' => 'الوقت',
        'thinking' => 'يفكر',
        'family' => 'يستشير أهله',
        'competitor' => 'يقارن مع منافس',
        'installment' => 'يريد التقسيط',
        'other_course' => 'يريد كورس آخر',
        'trust' => 'لا يثق',
        'no_need' => 'لا يحتاج حالياً',
        'postponed' => 'أجل القرار',
        'other' => 'أخرى',
    ];

    public const FOLLOW_UP_CHANNELS = [
        'call' => 'اتصال (Call)',
        'whatsapp' => 'واتساب',
        'meeting' => 'اجتماع',
    ];

    public const PAYMENT_METHODS = [
        'vodafone_cash' => 'فودافون كاش',
        'instapay' => 'إنستا باي',
        'bank_transfer' => 'تحويل بنكي',
        'card' => 'بطاقة',
        'cash' => 'نقدي',
        'other' => 'أخرى',
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
        'price' => 'السعر',
        'competitor' => 'منافس',
        'postponed' => 'أجل القرار',
        'no_time' => 'لا يملك وقتاً',
        'trust' => 'لا يثق',
        'no_need' => 'لا يحتاج حالياً',
        'price_high' => 'السعر مرتفع',
        'no_budget' => 'لا يوجد ميزانية',
        'not_decision_maker' => 'ليس صاحب قرار',
        'timing' => 'التوقيت غير مناسب',
        'no_follow_up' => 'عدم الاستجابة للمتابعة',
        'wrong_fit' => 'غير مناسب للاحتياج',
        'wrong_number' => 'رقم خطأ',
        'other' => 'أخرى',
    ];

    /** بعد كم يوم بلا تواصل نعتبر العميل «يحتاج متابعة» في التقارير */
    public const STALE_CONTACT_DAYS = 10;

    public const COURSE_TYPES = SalesCourseCommissionAgreement::COURSE_TYPES;

    protected $attributes = [
        'stage' => 'new_lead',
    ];

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
        'stage_entered_at',
        'priority',
        'interest',
        'interest_type_id',
        'course_type',
        'advanced_course_id',
        'offline_course_id',
        'course_id',
        'expected_value',
        'notes',
        'next_follow_up_at',
        'follow_up_channel',
        'last_contacted_at',
        'contact_attempts',
        'last_attempt_at',
        'next_attempt_due_at',
        'connected_disposition',
        'profile_type',
        'age',
        'age_range',
        'field_domain',
        'experience_level',
        'course_motivation',
        'start_preference',
        'can_pay',
        'interest_pct',
        'objection_reason',
        'objection_notes',
        'offer_sent_at',
        'offer_price',
        'offer_discount',
        'offer_installment_plan',
        'offer_notes',
        'payment_method',
        'payment_amount',
        'payment_due_at',
        'payment_txn_ref',
        'paid_at',
        'closed_at',
        'won_confirmed_at',
        'won_confirmed_by',
        'commission_amount',
        'commission_transaction_id',
        'commission_notes',
        'commission_settled_at',
        'commission_settled_by',
        'commission_settlement_id',
        'lost_reason',
        'csat_rating',
        'csat_comment',
        'csat_recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'expected_value' => 'decimal:2',
            'offer_price' => 'decimal:2',
            'payment_amount' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'next_follow_up_at' => 'datetime',
            'last_contacted_at' => 'datetime',
            'last_attempt_at' => 'datetime',
            'next_attempt_due_at' => 'datetime',
            'offer_sent_at' => 'datetime',
            'payment_due_at' => 'datetime',
            'paid_at' => 'datetime',
            'closed_at' => 'datetime',
            'won_confirmed_at' => 'datetime',
            'commission_settled_at' => 'datetime',
            'stage_entered_at' => 'datetime',
            'csat_recorded_at' => 'datetime',
            'can_pay' => 'boolean',
            'contact_attempts' => 'integer',
            'interest_pct' => 'integer',
            'age' => 'integer',
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

    public function interestType(): BelongsTo
    {
        return $this->belongsTo(SalesInterestType::class, 'interest_type_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(SalesLeadGroup::class, 'sales_lead_group_id');
    }

    public function advancedCourse(): BelongsTo
    {
        return $this->belongsTo(AdvancedCourse::class, 'advanced_course_id');
    }

    public function offlineCourse(): BelongsTo
    {
        return $this->belongsTo(OfflineCourse::class, 'offline_course_id');
    }

    public function legacyCourse(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function linkedCourseId(): ?int
    {
        return match ($this->course_type) {
            'advanced' => $this->advanced_course_id ? (int) $this->advanced_course_id : null,
            'online', 'offline' => $this->offline_course_id ? (int) $this->offline_course_id : null,
            'legacy' => $this->course_id ? (int) $this->course_id : null,
            default => null,
        };
    }

    public function linkedCourseTitle(): ?string
    {
        $title = match ($this->course_type) {
            'advanced' => $this->advancedCourse?->title,
            'online', 'offline' => $this->offlineCourse?->title,
            'legacy' => $this->legacyCourse?->title,
            default => null,
        };

        return $title !== null && $title !== '' ? (string) $title : null;
    }

    public function linkedCourseTypeLabel(): string
    {
        return self::COURSE_TYPES[$this->course_type] ?? '—';
    }

    public function applyCourseSelection(?string $type, ?int $courseId): void
    {
        $this->course_type = $type;
        $this->advanced_course_id = $type === 'advanced' ? $courseId : null;
        $this->offline_course_id = in_array($type, ['online', 'offline'], true) ? $courseId : null;
        $this->course_id = $type === 'legacy' ? $courseId : null;
    }

    public function activities(): HasMany
    {
        return $this->hasMany(SalesActivity::class)->orderByDesc('created_at');
    }

    public function metaSocialConversations(): HasMany
    {
        return $this->hasMany(MetaSocialConversation::class, 'sales_lead_id');
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(SalesLeadTransfer::class)->orderByDesc('created_at');
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
        return $query->whereNotIn('stage', self::CLOSED_STAGES);
    }

    public function scopeWonLike($query)
    {
        return $query->whereIn('stage', self::WON_LIKE_STAGES);
    }

    public function scopeEnrollmentWon($query)
    {
        return $query->where('stage', self::WON_STAGE);
    }

    public function scopePendingWinApproval($query)
    {
        return $query->where('stage', self::WON_STAGE)->whereNull('won_confirmed_at');
    }

    public function isPendingWinApproval(): bool
    {
        return $this->stage === self::WON_STAGE && $this->won_confirmed_at === null;
    }

    public function isWinConfirmed(): bool
    {
        return $this->stage === self::WON_STAGE && $this->won_confirmed_at !== null;
    }

    public function isCommissionSettled(): bool
    {
        return $this->commission_settled_at !== null;
    }

    public function scopeCommissionUnsettled($query)
    {
        return $query->whereNotNull('won_confirmed_at')->whereNull('commission_settled_at');
    }

    public function commissionSettlement(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SalesCommissionSettlement::class, 'commission_settlement_id');
    }

    public static function normalizeStage(?string $stage): string
    {
        if ($stage === null || $stage === '') {
            return 'new_lead';
        }

        return self::LEGACY_STAGE_MAP[$stage] ?? $stage;
    }

    public static function stageLabel(string $stage): string
    {
        $stage = self::normalizeStage($stage);

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
        return ! in_array($this->stage, self::CLOSED_STAGES, true);
    }

    public function isWonLike(): bool
    {
        return in_array($this->stage, self::WON_LIKE_STAGES, true);
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

    public function journeyOrder(): array
    {
        return array_keys(self::STAGES);
    }

    public function journeyIndex(): int
    {
        $keys = $this->journeyOrder();
        $idx = array_search($this->stage, $keys, true);

        return $idx === false ? 0 : (int) $idx;
    }
}
