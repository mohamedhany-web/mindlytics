<?php

namespace App\Domains\Messaging\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class WhatsAppConversationNote extends Model
{
    protected $table = 'whatsapp_conversation_notes';

    protected $fillable = [
        'conversation_id',
        'created_by',
        'body',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(WhatsAppConversation::class, 'conversation_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
