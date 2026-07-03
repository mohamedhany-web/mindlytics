<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestmentInquiry extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_UNDER_REVIEW = 'under_review';

    public const STATUS_MEETING = 'meeting_scheduled';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_WITHDRAWN = 'withdrawn';

    protected $fillable = [
        'investment_plan_id',
        'full_name',
        'email',
        'phone',
        'country_code',
        'company_name',
        'investor_type',
        'proposed_amount',
        'currency',
        'experience_notes',
        'message',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
        'ip_address',
        'meta',
    ];

    protected $casts = [
        'proposed_amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'meta' => 'array',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(InvestmentPlan::class, 'investment_plan_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function investorTypeLabel(): string
    {
        return self::investorTypeLabels()[$this->investor_type] ?? $this->investor_type;
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING => 'جديد',
            self::STATUS_UNDER_REVIEW => 'قيد المراجعة',
            self::STATUS_MEETING => 'اجتماع مجدول',
            self::STATUS_APPROVED => 'مقبول',
            self::STATUS_REJECTED => 'مرفوض',
            self::STATUS_WITHDRAWN => 'منسحب',
        ];
    }

    public static function investorTypeLabels(): array
    {
        return [
            'individual' => 'فرد',
            'company' => 'شركة',
            'fund' => 'صندوق / مؤسسة',
        ];
    }
}
