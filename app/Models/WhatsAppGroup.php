<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppGroup extends Model
{
    protected $table = 'whatsapp_groups';

    public const STATUS_DRAFT = 'draft';

    public const STATUS_CREATING = 'creating';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_FAILED = 'failed';

    public const STATUS_LEFT = 'left';

    protected $fillable = [
        'sales_lead_group_id',
        'created_by',
        'assigned_to',
        'subject',
        'description',
        'wa_group_jid',
        'invite_link',
        'announce_only',
        'restrict_info',
        'status',
        'bridge_error',
        'settings',
        'last_synced_at',
    ];

    protected $casts = [
        'announce_only' => 'boolean',
        'restrict_info' => 'boolean',
        'settings' => 'array',
        'last_synced_at' => 'datetime',
    ];

    public function salesLeadGroup(): BelongsTo
    {
        return $this->belongsTo(SalesLeadGroup::class, 'sales_lead_group_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(WhatsAppGroupParticipant::class, 'whatsapp_group_id');
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE && $this->wa_group_jid;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_CREATING => 'جاري الإنشاء',
            self::STATUS_ACTIVE => 'نشطة',
            self::STATUS_FAILED => 'فشل',
            self::STATUS_LEFT => 'تم الخروج',
            default => 'مسودة',
        };
    }

    public function scopeVisibleTo($query, User $user, bool $isAdmin = false)
    {
        if ($isAdmin) {
            return $query;
        }

        $userId = (int) $user->id;

        return $query->where(function ($q) use ($userId) {
            $q->where('created_by', $userId)
                ->orWhere('assigned_to', $userId);
        });
    }
}
