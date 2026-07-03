<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppContact extends Model
{
    protected $table = 'whatsapp_contacts';

    protected $fillable = [
        'phone_number',
        'name',
        'email',
        'company',
        'country_code',
        'language',
        'source',
        'sales_lead_id',
        'user_id',
        'assigned_to',
        'lifetime_value',
        'last_contacted_at',
        'meta',
    ];

    protected $casts = [
        'lifetime_value' => 'decimal:2',
        'last_contacted_at' => 'datetime',
        'meta' => 'array',
    ];

    public function salesLead(): BelongsTo
    {
        return $this->belongsTo(SalesLead::class, 'sales_lead_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(WhatsAppConversation::class, 'contact_id');
    }

    public function displayName(): string
    {
        return $this->name
            ?: $this->salesLead?->name
            ?: $this->user?->name
            ?: '+' . $this->phone_number;
    }
}
