<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeWorkUnlock extends Model
{
    protected $fillable = [
        'user_id',
        'unlocked_by',
        'work_date',
        'starts_at',
        'expires_at',
        'reason',
        'duration_label',
        'revoked_at',
        'revoked_by',
        'revoke_reason',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'work_date' => 'date',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'revoked_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function unlockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'unlocked_by');
    }

    public function revokedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    public function isActive(?Carbon $now = null): bool
    {
        $now = $now ?? now();

        if ($this->revoked_at) {
            return false;
        }

        return $this->starts_at->lte($now) && $this->expires_at->gt($now);
    }

    public function scopeActive($query, ?Carbon $now = null)
    {
        $now = $now ?? now();

        return $query
            ->whereNull('revoked_at')
            ->where('starts_at', '<=', $now)
            ->where('expires_at', '>', $now);
    }

    /** @return array<string, string> */
    public static function durationOptions(): array
    {
        return [
            '2h' => 'ساعتان',
            '4h' => '4 ساعات',
            '8h' => '8 ساعات',
            'end_of_day' => 'حتى نهاية اليوم',
        ];
    }
}
