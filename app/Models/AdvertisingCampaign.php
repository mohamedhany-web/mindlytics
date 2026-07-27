<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdvertisingCampaign extends Model
{
    protected $fillable = [
        'name',
        'platform',
        'description',
        'cost',
        'currency',
        'start_date',
        'end_date',
        'is_active',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * منصات الحملات الإعلانية المتاحة.
     *
     * @return array<string, string>
     */
    public static function platforms(): array
    {
        return [
            'facebook' => 'فيسبوك',
            'instagram' => 'إنستجرام',
            'tiktok' => 'تيك توك',
            'google' => 'جوجل',
            'youtube' => 'يوتيوب',
            'whatsapp' => 'واتساب',
            'snapchat' => 'سناب شات',
            'linkedin' => 'لينكدإن',
            'other' => 'أخرى',
        ];
    }

    public function platformLabel(): string
    {
        return self::platforms()[$this->platform] ?? ($this->platform ?: '—');
    }

    public function salesEmployees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'advertising_campaign_sales_user')
            ->withTimestamps();
    }

    public function dailyReports(): HasMany
    {
        return $this->hasMany(CampaignDailyReport::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
