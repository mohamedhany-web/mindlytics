<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesManagerDailyReview extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_REVIEWED = 'reviewed';

    public const STATUS_APPROVED = 'approved';

    public const STATUSES = [
        self::STATUS_DRAFT => 'مسودة',
        self::STATUS_REVIEWED => 'تمت المراجعة',
        self::STATUS_APPROVED => 'معتمد',
    ];

    protected $fillable = [
        'sales_team_id',
        'employee_id',
        'manager_id',
        'work_date',
        'status',
        'verified_score',
        'score_snapshot',
        'recommendation',
        'proposed_deduction_amount',
        'manager_notes',
        'reviewed_at',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'work_date' => 'date',
            'verified_score' => 'decimal:1',
            'score_snapshot' => 'array',
            'proposed_deduction_amount' => 'decimal:2',
            'reviewed_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(SalesTeam::class, 'sales_team_id');
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function recommendationLabel(): string
    {
        $map = config('sales_manager_scorecard.recommendations', []);

        return $map[$this->recommendation] ?? $this->recommendation;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }
}
