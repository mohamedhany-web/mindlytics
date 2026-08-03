<?php

namespace App\Services;

use App\Models\SalesInterestType;
use App\Models\SalesUserSpecialty;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class SalesSpecialtyService
{
    public function ready(): bool
    {
        return Schema::hasTable('sales_interest_types')
            && Schema::hasTable('sales_user_specialties');
    }

    /** @return Collection<int, SalesInterestType> */
    public function activeTypes(): Collection
    {
        if (! $this->ready()) {
            return collect();
        }

        return SalesInterestType::query()->active()->ordered()->get();
    }

    /** @return Collection<int, SalesInterestType> */
    public function interestTypesFor(User $user): Collection
    {
        if (! $this->ready()) {
            return collect();
        }

        return $user->salesInterestTypes()->active()->ordered()->get();
    }

    /**
     * @param  list<int>|null  $limitUserIds
     * @return Collection<int, User>
     */
    public function specialistsFor(int $interestTypeId, ?array $limitUserIds = null): Collection
    {
        if (! $this->ready()) {
            return collect();
        }

        $query = User::salesEmployees()
            ->where('is_active', true)
            ->whereHas('salesInterestTypes', fn ($q) => $q->where('sales_interest_types.id', $interestTypeId))
            ->orderBy('name');

        if ($limitUserIds !== null) {
            $query->whereIn('id', $limitUserIds);
        }

        return $query->get(['id', 'name', 'email']);
    }

    /**
     * @param  list<int>  $interestTypeIds
     */
    public function syncForUser(User $user, array $interestTypeIds): void
    {
        if (! $this->ready()) {
            return;
        }

        $ids = array_values(array_unique(array_filter(array_map('intval', $interestTypeIds))));
        $valid = SalesInterestType::query()->whereIn('id', $ids)->pluck('id')->all();

        SalesUserSpecialty::query()->where('user_id', $user->id)->delete();
        foreach ($valid as $typeId) {
            SalesUserSpecialty::query()->create([
                'user_id' => $user->id,
                'interest_type_id' => $typeId,
            ]);
        }
    }

    public function userHasSpecialty(User $user, ?int $interestTypeId): bool
    {
        if (! $interestTypeId || ! $this->ready()) {
            return false;
        }

        return SalesUserSpecialty::query()
            ->where('user_id', $user->id)
            ->where('interest_type_id', $interestTypeId)
            ->exists();
    }
}
