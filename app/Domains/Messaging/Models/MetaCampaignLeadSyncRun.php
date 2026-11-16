<?php

namespace App\Domains\Messaging\Models;

use App\Domains\Marketing\Models\AdvertisingCampaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetaCampaignLeadSyncRun extends Model
{
    public const STATUS_QUEUED = 'queued';

    public const STATUS_RUNNING = 'running';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'meta_campaign_id',
        'meta_campaign_name',
        'advertising_campaign_id',
        'requested_by',
        'status',
        'import_batch',
        'created_count',
        'updated_count',
        'skipped_count',
        'conversations_linked',
        'message',
        'diagnostics',
        'error',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'diagnostics' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'created_count' => 'integer',
        'updated_count' => 'integer',
        'skipped_count' => 'integer',
        'conversations_linked' => 'integer',
    ];

    public function advertisingCampaign(): BelongsTo
    {
        return $this->belongsTo(AdvertisingCampaign::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_QUEUED => 'في الانتظار',
            self::STATUS_RUNNING => 'جاري السحب',
            self::STATUS_COMPLETED => 'اكتمل',
            self::STATUS_FAILED => 'فشل',
            default => $this->status,
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_QUEUED => 'bg-slate-100 text-slate-700',
            self::STATUS_RUNNING => 'bg-amber-100 text-amber-800',
            self::STATUS_COMPLETED => 'bg-emerald-100 text-emerald-800',
            self::STATUS_FAILED => 'bg-rose-100 text-rose-800',
            default => 'bg-slate-100 text-slate-700',
        };
    }

    public function totalImported(): int
    {
        return (int) $this->created_count + (int) $this->updated_count + (int) $this->conversations_linked;
    }
}
