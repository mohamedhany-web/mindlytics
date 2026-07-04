<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MetaSocialPage extends Model
{
    protected $fillable = [
        'page_id',
        'page_name',
        'page_username',
        'page_access_token',
        'picture_url',
        'category',
        'instagram_business_id',
        'instagram_username',
        'instagram_profile_picture',
        'is_active',
        'webhook_subscribed_at',
        'last_synced_at',
        'connected_by',
        'meta',
    ];

    protected $casts = [
        'page_access_token' => 'encrypted',
        'is_active' => 'boolean',
        'webhook_subscribed_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'meta' => 'array',
    ];

    public function connectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'connected_by');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(MetaSocialConversation::class);
    }

    public function hasInstagram(): bool
    {
        return ! empty($this->instagram_business_id);
    }

    public function platformLabel(): string
    {
        $platforms = ['Facebook Page'];
        if ($this->hasInstagram()) {
            $platforms[] = 'Instagram';
        }

        return implode(' + ', $platforms);
    }
}
