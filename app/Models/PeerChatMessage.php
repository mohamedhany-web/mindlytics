<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeerChatMessage extends Model
{
    protected $fillable = [
        'thread_id',
        'sender_id',
        'reply_to_id',
        'kind',
        'body',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(PeerChatThread::class, 'thread_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(PeerChatMessage::class, 'reply_to_id');
    }
}
