<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JourneyShareEvent extends Model
{
    protected $fillable = [
        'user_id',
        'shareable_type',
        'shareable_id',
        'channel',
        'card_type',
        'ip_address',
        'user_agent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
