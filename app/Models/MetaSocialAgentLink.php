<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetaSocialAgentLink extends Model
{
    protected $fillable = [
        'meta_user_id',
        'meta_user_name',
        'meta_user_email',
        'user_id',
        'tasks',
        'page_ids',
        'source',
        'last_synced_at',
    ];

    protected $casts = [
        'tasks' => 'array',
        'page_ids' => 'array',
        'last_synced_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isLinked(): bool
    {
        return (int) $this->user_id > 0;
    }
}
