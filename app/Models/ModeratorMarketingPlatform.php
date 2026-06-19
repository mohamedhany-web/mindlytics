<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ModeratorMarketingPlatform extends Model
{
    protected $table = 'moderator_mkt_platforms';

    protected $fillable = [
        'plan_id',
        'platform_key',
        'custom_label',
        'profile_url',
        'strategy_notes',
        'cadence_notes',
        'color_hex',
        'sort_order',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(ModeratorMarketingPlan::class, 'plan_id');
    }

    public function calendarEvents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ModeratorMarketingCalendarEvent::class, 'platform_id');
    }

    public function employeeJobs(): BelongsToMany
    {
        return $this->belongsToMany(
            EmployeeJob::class,
            'moderator_mkt_platform_jobs',
            'platform_id',
            'employee_job_id'
        )->withTimestamps();
    }

    public function displayName(): string
    {
        if ($this->platform_key === 'other' && $this->custom_label) {
            return $this->custom_label;
        }

        return self::platformLabels()[$this->platform_key] ?? $this->platform_key;
    }

    public static function platformLabels(): array
    {
        return [
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'tiktok' => 'TikTok',
            'youtube' => 'YouTube',
            'twitter' => 'X / Twitter',
            'linkedin' => 'LinkedIn',
            'snapchat' => 'Snapchat',
            'telegram' => 'Telegram',
            'other' => 'أخرى',
        ];
    }
}
