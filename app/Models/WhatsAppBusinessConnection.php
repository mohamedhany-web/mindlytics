<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppBusinessConnection extends Model
{
    public const STATUS_CONNECTED = 'connected';

    public const STATUS_DISCONNECTED = 'disconnected';

    protected $table = 'whatsapp_business_connections';

    protected $fillable = [
        'business_portfolio_id',
        'waba_id',
        'phone_number_id',
        'display_phone_number',
        'verified_display_name',
        'access_token',
        'status',
        'webhook_subscribed_at',
        'connected_at',
        'connected_by',
        'meta',
    ];

    protected $casts = [
        'access_token' => 'encrypted',
        'meta' => 'array',
        'webhook_subscribed_at' => 'datetime',
        'connected_at' => 'datetime',
    ];

    protected function castAttribute($key, $value)
    {
        if ($key === 'access_token' && $value !== null && $value !== '') {
            try {
                return parent::castAttribute($key, $value);
            } catch (\Throwable) {
                return (string) $value;
            }
        }

        return parent::castAttribute($key, $value);
    }

    public function connectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'connected_by');
    }

    public static function active(): ?self
    {
        try {
            $instance = new static;
            if (! \Illuminate\Support\Facades\Schema::hasTable($instance->getTable())) {
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
