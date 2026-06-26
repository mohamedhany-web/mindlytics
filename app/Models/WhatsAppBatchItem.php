<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppBatchItem extends Model
{
    protected $table = 'whatsapp_batch_items';

    protected $fillable = [
        'batch_id',
        'recipient_name',
        'phone',
        'message',
        'message_type',
        'status',
        'error_message',
        'whatsapp_message_id',
        'workshop_registration_id',
        'sales_lead_id',
        'user_id',
        'sort_order',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(WhatsAppBatch::class, 'batch_id');
    }

    public function whatsappMessage(): BelongsTo
    {
        return $this->belongsTo(WhatsAppMessage::class, 'whatsapp_message_id');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'في الانتظار',
            'processing' => 'جاري الإرسال',
            'sent' => 'تم الإرسال',
            'failed' => 'فشل',
            'cancelled' => 'موقوف',
            default => $this->status,
        };
    }
}
