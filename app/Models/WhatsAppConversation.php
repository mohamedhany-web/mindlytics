<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppConversation extends Model
{
    protected $table = 'whatsapp_conversations';

    protected $fillable = [
        'phone_number',
        'contact_name',
        'user_id',
        'last_message_at',
        'last_message_preview',
        'last_message_direction',
        'unread_count',
        'meta',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'meta' => 'array',
        'unread_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(WhatsAppConversationMessage::class, 'conversation_id');
    }

    public function displayName(): string
    {
        if ($this->contact_name) {
            return $this->contact_name;
        }

        if ($this->user?->name) {
            return $this->user->name;
        }

        return '+' . $this->phone_number;
    }

    public function formattedPhone(): string
    {
        return '+' . $this->phone_number;
    }
}
