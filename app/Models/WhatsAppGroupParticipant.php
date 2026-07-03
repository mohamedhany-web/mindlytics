<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppGroupParticipant extends Model
{
    protected $table = 'whatsapp_group_participants';

    public const STATUS_PENDING = 'pending';

    public const STATUS_ADDED = 'added';

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
            self::STATUS_ADDED => 'مضاف',
            self::STATUS_FAILED => 'فشل',
            self::STATUS_REMOVED => 'محذوف',
            default => 'قيد الانتظار',
        };
    }
}
