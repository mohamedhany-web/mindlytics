<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppConversationMessage extends Model
{
    protected $table = 'whatsapp_conversation_messages';

    public const DIRECTION_INBOUND = 'inbound';

    public const DIRECTION_OUTBOUND = 'outbound';

    protected $fillable = [
        'conversation_id',
        'direction',
        'body',
        'message_type',
        'whatsapp_message_id',
        'status',
        'sent_by_user_id',
        'template_name',
        'template_params',
        'payload',
        'error_message',
        'sent_at',
        'delivered_at',
        'read_at',
    ];

    protected $casts = [
        'template_params' => 'array',
        'payload' => 'array',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(WhatsAppConversation::class, 'conversation_id');
    }

    public function sentBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by_user_id');
    }

    public function isInbound(): bool
    {
        return $this->direction === self::DIRECTION_INBOUND;
    }

    public function displayBody(): string
    {
        if ($this->body) {
            return $this->body;
        }

        if ($this->template_name) {
            return '[قالب: ' . $this->template_name . ']';
        }

        return match ($this->message_type) {
            'image' => '[صورة]',
            'audio' => '[رسالة صوتية]',
            'video' => '[فيديو]',
            'document' => '[مستند]',
            'sticker' => '[ملصق]',
            'location' => '[موقع]',
            default => '[' . $this->message_type . ']',
        };
    }
}
