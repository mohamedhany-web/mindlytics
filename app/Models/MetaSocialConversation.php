<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MetaSocialConversation extends Model
{
    public const PLATFORM_MESSENGER = 'messenger';

    public const PLATFORM_INSTAGRAM = 'instagram';

    public const STATUS_OPEN = 'open';

    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'meta_social_page_id',
        'platform',
        'participant_id',
        'participant_name',
        'participant_username',
        'participant_profile_pic',
        'thread_id',
        'last_message_at',
        'last_message_preview',
        'unread_count',
        'status',
        'assigned_to',
        'meta',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'unread_count' => 'integer',
        'meta' => 'array',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(MetaSocialPage::class, 'meta_social_page_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(MetaSocialMessage::class)->orderBy('sent_at')->orderBy('id');
    }

    public function platformLabel(): string
    {
        return match ($this->platform) {
            self::PLATFORM_INSTAGRAM => 'Instagram',
            default => 'Messenger',
        };
    }

    public function displayName(): string
    {
        return $this->participant_name
            ?: $this->participant_username
            ?: $this->participant_id;
    }
}
