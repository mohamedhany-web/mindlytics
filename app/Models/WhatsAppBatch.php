<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppBatch extends Model
{
    protected $fillable = [
        'title',
        'source_type',
        'source_id',
        'message_template',
        'status',
        'total_count',
        'sent_count',
        'failed_count',
        'created_by',
        'started_at',
        'completed_at',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(WhatsAppBatchItem::class, 'batch_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pendingCount(): int
    {
        return max(0, $this->total_count - $this->sent_count - $this->failed_count);
    }

    public function progressPercent(): int
    {
        if ($this->total_count <= 0) {
            return 0;
        }

        return (int) round((($this->sent_count + $this->failed_count) / $this->total_count) * 100);
    }

    public function isFinished(): bool
    {
        return in_array($this->status, ['completed', 'cancelled'], true);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'في الانتظار',
            'processing' => 'جاري الإرسال',
            'completed' => 'اكتمل',
            'cancelled' => 'ملغى',
            default => $this->status,
        };
    }
}
