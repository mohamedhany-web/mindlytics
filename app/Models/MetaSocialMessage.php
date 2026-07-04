<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetaSocialMessage extends Model
{
    public const DIRECTION_INBOUND = 'inbound';

    public const DIRECTION_OUTBOUND = 'outbound';

    protected $fillable = [
        'meta_social_conversation_id',
        'meta_message_id',
        'direction',
        'message_type',
        'body',
        'attachment_url',
        'sent_by_user_id',
        'meta',
        'sent_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'sent_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(MetaSocialConversation::class, 'meta_social_conversation_id');
    }

    public function sentBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by_user_id');
    }

    public function displayBody(): string
    {
        if ($this->body) {
            return $this->body;
        }

        return match ($this->message_type) {
            'image' => '[صورة]',
            'audio' => '[صوت]',
            'video' => '[فيديو]',
            'file' => '[ملف]',
            'sticker' => '[ملصق]',
            default => '[رسالة]',
        };
    }
}
