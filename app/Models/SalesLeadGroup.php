<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class SalesLeadGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
        'assigned_to',
        'created_by',
        'is_admin_managed',
    ];

    protected function casts(): array
    {
        return [
            'is_admin_managed' => 'boolean',
        ];
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'sales_lead_group_members', 'sales_lead_group_id', 'user_id')
            ->withTimestamps();
    }

    public function leads(): HasMany
    {
        return $this->hasMany(SalesLead::class, 'sales_lead_group_id');
    }

    public function scopeForAssignee(Builder $query, int $userId): Builder
    {
        if (! Schema::hasTable('sales_lead_group_members')) {
            return $query->where('assigned_to', $userId);
        }

        return $query->where(function (Builder $q) use ($userId) {
            $q->where('assigned_to', $userId)
                ->orWhereHas('members', fn (Builder $mq) => $mq->where('users.id', $userId));
        });
    }

    public function userHasAccess(int $userId): bool
    {
        if ((int) $this->assigned_to === $userId) {
            return true;
        }

        if (! Schema::hasTable('sales_lead_group_members')) {
            return false;
        }

        if ($this->relationLoaded('members')) {
            return $this->members->contains('id', $userId);
        }

        return $this->members()->whereKey($userId)->exists();
    }

    /**
     * @param  list<int>  $userIds
     */
    public function syncMembers(array $userIds): void
    {
        $ids = collect($userIds)->map(fn ($id) => (int) $id)->filter()->unique()->values();

        if (Schema::hasTable('sales_lead_group_members')) {
            $this->members()->sync($ids->all());
        }

        if ($ids->isEmpty()) {
            if ($this->assigned_to !== null) {
                $this->update(['assigned_to' => null]);
            }

            return;
        }

        if (! $ids->contains((int) $this->assigned_to)) {
            $this->update(['assigned_to' => $ids->first()]);
        }
    }

    /**
     * أضف أعضاء للمجموعة دون حذف الموجودين.
     *
     * @param  list<int>  $userIds
     */
    public function ensureMembers(array $userIds): void
    {
        $incoming = collect($userIds)->map(fn ($id) => (int) $id)->filter()->unique();
        if ($incoming->isEmpty()) {
            return;
        }

        $merged = $this->memberIds()->merge($incoming)->unique()->values()->all();
        $this->syncMembers($merged);
        $this->unsetRelation('members');
    }

    /**
     * @param  list<int>  $userIds
     */
    public function includesAllMembers(array $userIds): bool
    {
        $required = collect($userIds)->map(fn ($id) => (int) $id)->unique()->filter();

        if ($required->isEmpty()) {
            return true;
        }

        if (! Schema::hasTable('sales_lead_group_members')) {
            return $required->count() === 1 && (int) $this->assigned_to === (int) $required->first();
        }

        $memberIds = $this->relationLoaded('members')
            ? $this->members->pluck('id')
            : $this->members()->pluck('users.id');

        return $required->diff($memberIds)->isEmpty();
    }

    /**
     * @return Collection<int, int>
     */
    public function memberIds(): Collection
    {
        if ($this->relationLoaded('members') && Schema::hasTable('sales_lead_group_members')) {
            $ids = $this->members->pluck('id')->values();

            if ($ids->isNotEmpty()) {
                return $ids;
            }
        }

        if (Schema::hasTable('sales_lead_group_members')) {
            $ids = $this->members()->pluck('users.id');

            if ($ids->isNotEmpty()) {
                return $ids->values();
            }
        }

        return $this->assigned_to ? collect([(int) $this->assigned_to]) : collect();
    }
}
