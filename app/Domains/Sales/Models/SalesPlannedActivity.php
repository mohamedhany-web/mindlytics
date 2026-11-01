<?php

namespace App\Domains\Sales\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * نشاط مخطط (اللي جاي): مكالمة أو اجتماع أو مهمة على عميل أو طالب.
 * لما يتنفّذ بيتسجّل في sales_activities (اللي حصل) عشان KPIs والتقرير اليومي يحسبوه زي أي نشاط.
 */
class SalesPlannedActivity extends Model
{
    public const KINDS = [
        'call' => 'مكالمة',
        'meeting' => 'اجتماع / ديمو',
        'task' => 'مهمة',
    ];

    public const KIND_ICONS = [
        'call' => 'fa-phone',
        'meeting' => 'fa-users',
        'task' => 'fa-check-square',
    ];

    public const STATUSES = [
        'planned' => 'مخطط',
        'done' => 'اتنفّذ',
        'missed' => 'ما اتنفّذش',
        'cancelled' => 'اتلغى',
    ];

    public const PRIORITIES = [
        'low' => 'منخفضة',
        'normal' => 'عادية',
        'high' => 'عالية',
    ];

    protected $fillable = [
        'kind',
        'title',
        'description',
        'sales_lead_id',
        'student_user_id',
        'assigned_to',
        'created_by',
        'starts_at',
        'duration_minutes',
        'priority',
        'status',
        'outcome',
        'result_notes',
        'completed_at',
        'completed_by',
        'sales_activity_id',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'completed_at' => 'datetime',
            'duration_minutes' => 'integer',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(SalesLead::class, 'sales_lead_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function scopePlanned(Builder $query): Builder
    {
        return $query->where('status', 'planned');
    }

    public function isPlanned(): bool
    {
        return $this->status === 'planned';
    }

    public function isOverdue(): bool
    {
        return $this->isPlanned() && $this->starts_at !== null && $this->starts_at->isPast();
    }

    public function endsAt(): ?\Illuminate\Support\Carbon
    {
        return $this->starts_at?->copy()->addMinutes($this->duration_minutes ?: 30);
    }

    public static function kindLabel(?string $kind): string
    {
        return self::KINDS[$kind] ?? (string) $kind;
    }

    public static function statusLabel(?string $status): string
    {
        return self::STATUSES[$status] ?? (string) $status;
    }
}
