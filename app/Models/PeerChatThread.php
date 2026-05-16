<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeerChatThread extends Model
{
    protected $fillable = [
        'user_low_id',
        'user_high_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(PeerChatMessage::class, 'thread_id');
    }

    public function userLow(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_low_id');
    }

    public function userHigh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_high_id');
    }

    public static function findOrCreateForUsers(int $userIdA, int $userIdB): self
    {
        $low = min($userIdA, $userIdB);
        $high = max($userIdA, $userIdB);

        return static::firstOrCreate(
            [
                'user_low_id' => $low,
                'user_high_id' => $high,
            ],
            [
                'last_message_at' => null,
            ]
        );
    }

    public function otherUserId(int $viewerId): int
    {
        return (int) $this->user_low_id === $viewerId
            ? (int) $this->user_high_id
            : (int) $this->user_low_id;
    }
}
