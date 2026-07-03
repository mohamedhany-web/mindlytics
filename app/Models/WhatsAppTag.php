<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WhatsAppTag extends Model
{
    protected $table = 'whatsapp_tags';

    protected $fillable = [
        'name',
        'slug',
        'color',
    ];

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(WhatsAppConversation::class, 'whatsapp_conversation_tag', 'tag_id', 'conversation_id')
            ->withPivot('tagged_by', 'created_at');
    }
}
