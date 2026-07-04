<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MetaSocialConnection extends Model
{
    public const STATUS_CONNECTED = 'connected';

    public const STATUS_DISCONNECTED = 'disconnected';

    protected $fillable = [
        'meta_user_id',
        'meta_user_name',
        'user_access_token',
        'token_expires_at',
        'status',
        'connected_by',
        'connected_at',
        'meta',
    ];

    protected $casts = [
        'user_access_token' => 'encrypted',
        'token_expires_at' => 'datetime',
        'connected_at' => 'datetime',
        'meta' => 'array',
    ];

    public function connectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'connected_by');
    }

    public static function active(): ?self
    {
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('meta_social_connections')) {
                return null;
            }

            return self::query()
                ->where('status', self::STATUS_CONNECTED)
                ->latest('connected_at')
                ->first();
        } catch (\Throwable) {
            return null;
        }
    }
}
