<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesTeamConversation extends Model
{
    public const TYPE_DIRECT = 'direct';

    public const TYPE_TEAM = 'team';

    protected $fillable = [
        'sales_team_id',
        'type',
        'title',
        'created_by',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(SalesTeam::class, 'sales_team_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'sales_team_conversation_participants', 'conversation_id', 'user_id')
            ->withPivot(['last_read_at'])
            ->withTimestamps();
    }

    public function participantRows(): HasMany
    {
        return $this->hasMany(SalesTeamConversationParticipant::class, 'conversation_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SalesTeamMessage::class, 'conversation_id');
    }

    public function isTeamChannel(): bool
    {
        return $this->type === self::TYPE_TEAM;
    }

    public function isDirect(): bool
    {
        return $this->type === self::TYPE_DIRECT;
    }
}
