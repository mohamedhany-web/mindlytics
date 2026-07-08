<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesTeam extends Model
{
    protected $fillable = [
        'name',
        'description',
        'manager_id',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members(): HasMany
    {
        return $this->hasMany(SalesTeamMember::class);
    }

    public function memberUsers(): HasMany
    {
        return $this->hasMany(SalesTeamMember::class)->where('role', 'member');
    }

    public function dailyReports(): HasMany
    {
        return $this->hasMany(SalesTeamDailyReport::class);
    }

    public function leadTransfers(): HasMany
    {
        return $this->hasMany(SalesLeadTransfer::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** @return list<int> */
    public function memberUserIds(): array
    {
        return $this->members()
            ->where('role', 'member')
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    /** @return list<int> */
    public function allStaffUserIds(): array
    {
        $ids = $this->memberUserIds();
        $ids[] = (int) $this->manager_id;

        return array_values(array_unique($ids));
    }
}
