<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Workshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'location',
        'whatsapp_group_link',
        'slug',
        'description',
        'mode',
        'starts_at',
        'ends_at',
        'max_seats',
        'seats_online',
        'seats_offline',
        'is_active',
        'created_by',
        'welcome_meta_template_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Workshop $workshop) {
            if (empty($workshop->slug)) {
                $workshop->slug = Str::slug($workshop->title) . '-' . Str::random(6);
            }
        });
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(WorkshopRegistration::class);
    }

    public function welcomeMetaTemplate(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(WhatsAppMetaTemplate::class, 'welcome_meta_template_id');
    }

    public function getRemainingSeatsAttribute(): ?int
    {
        if (!$this->max_seats) {
            return null;
        }

        $count = $this->registrations()->count();

        return max($this->max_seats - $count, 0);
    }

    public function getRemainingOnlineSeatsAttribute(): ?int
    {
        if (!$this->seats_online) {
            return null;
        }

        $count = $this->registrations()
            ->where('attendance_mode', 'online')
            ->count();

        return max($this->seats_online - $count, 0);
    }

    public function getRemainingOfflineSeatsAttribute(): ?int
    {
        if (!$this->seats_offline) {
            return null;
        }

        $count = $this->registrations()
            ->where('attendance_mode', 'offline')
            ->count();

        return max($this->seats_offline - $count, 0);
    }

    public function publicWhatsappGroupUrl(): ?string
    {
        $link = trim((string) ($this->whatsapp_group_link ?? ''));

        return $link !== '' ? $link : null;
    }
}

