<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppGroupParticipant extends Model
{
    protected $table = 'whatsapp_group_participants';

    public const STATUS_PENDING = 'pending';

    public const STATUS_INVITED = 'invited';

    public const STATUS_JOINED = 'joined';

    public const STATUS_ADDED = 'joined';

    public const STATUS_FAILED = 'failed';

    public const STATUS_REMOVED = 'removed';

    protected $fillable = [
        'whatsapp_group_id',
        'sales_lead_id',
        'phone',
        'display_name',
        'wa_participant_jid',
        'status',
        'error_message',
        'invited_at',
        'joined_at',
    ];

    protected $casts = [
        'invited_at' => 'datetime',
        'joined_at' => 'datetime',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(WhatsAppGroup::class, 'whatsapp_group_id');
    }

    public function salesLead(): BelongsTo
    {
        return $this->belongsTo(SalesLead::class, 'sales_lead_id');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_INVITED => 'دعوة مُرسلة',
            self::STATUS_JOINED, self::STATUS_ADDED => 'انضم',
            self::STATUS_FAILED => 'فشل',
            self::STATUS_REMOVED => 'محذوف',
            default => 'قيد الانتظار',
        };
    }
}
