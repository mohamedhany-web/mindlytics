<?php

namespace App\Services;

use App\Models\SalesLead;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class SalesHierarchyService
{
    public function ready(): bool
    {
        return Schema::hasColumn('users', 'sales_reports_to_id');
    }

    /** @return Collection<int, User> */
    public function salesStaff(): Collection
    {
        $employees = User::salesEmployees()->where('is_active', true)->with('salesInterestTypes')->orderBy('name')->get();
        $managers = User::salesManagers()->where('is_active', true)->with('salesInterestTypes')->orderBy('name')->get();

        return $employees->merge($managers)->unique('id')->values();
    }

    /**
     * بناء شجرة الهيكل من sales_reports_to_id.
     *
     * @return list<array{user: User, children: list}>
     */
    public function buildTree(?User $root = null): array
    {
        $staff = $this->salesStaff();
        if ($staff->isEmpty()) {
            return [];
        }

        $byId = $staff->keyBy('id');
        $childrenMap = [];
        foreach ($staff as $user) {
            $parentId = $user->sales_reports_to_id ? (int) $user->sales_reports_to_id : null;
            if ($parentId && ! $byId->has($parentId)) {
                $parentId = null;
            }
            $childrenMap[$parentId ?? 0][] = $user;
        }

        $build = function (?int $parentKey) use (&$build, $childrenMap): array {
            $nodes = [];
            foreach ($childrenMap[$parentKey ?? 0] ?? [] as $user) {
                $nodes[] = [
                    'user' => $user,
                    'children' => $build((int) $user->id),
                ];
            }

            return $nodes;
        };

        if ($root) {
            return [[
                'user' => $root,
                'children' => $build((int) $root->id),
            ]];
        }

        return $build(null);
    }

    /**
     * نطاق المرؤوسين (مباشر + غير مباشر) لمدير.
     *
     * @return list<int>
     */
    public function descendantIds(User $manager): array
    {
        if (! $this->ready()) {
            return [];
        }

        $staff = $this->salesStaff();
        $byParent = [];
        foreach ($staff as $user) {
            $pid = $user->sales_reports_to_id ? (int) $user->sales_reports_to_id : 0;
            $byParent[$pid][] = (int) $user->id;
        }

        $ids = [];
        $queue = $byParent[(int) $manager->id] ?? [];
        while ($queue !== []) {
            $id = array_shift($queue);
            if (in_array($id, $ids, true)) {
                continue;
            }
            $ids[] = $id;
            foreach ($byParent[$id] ?? [] as $child) {
                $queue[] = $child;
            }
        }

        return $ids;
    }

    public function setReportsTo(User $user, ?int $managerId): void
    {
        if (! $this->ready()) {
            throw ValidationException::withMessages(['sales_reports_to_id' => 'شغّل migrate أولاً.']);
        }

        if ($managerId === null) {
            $user->forceFill(['sales_reports_to_id' => null])->save();

            return;
        }

        if ((int) $managerId === (int) $user->id) {
            throw ValidationException::withMessages(['sales_reports_to_id' => 'لا يمكن أن يكون المدير المباشر هو نفس الشخص.']);
        }

        $manager = User::query()->find($managerId);
        if (! $manager) {
            throw ValidationException::withMessages(['sales_reports_to_id' => 'المدير المختار غير موجود.']);
        }

        // Prevent cycles: walk up from manager
        $seen = [(int) $user->id];
        $cursor = $manager;
        while ($cursor) {
            if (in_array((int) $cursor->id, $seen, true)) {
                throw ValidationException::withMessages(['sales_reports_to_id' => 'هذا الربط يسبب حلقة في الهيكل.']);
            }
            $seen[] = (int) $cursor->id;
            $cursor = $cursor->sales_reports_to_id
                ? User::query()->find($cursor->sales_reports_to_id)
                : null;
        }

        $user->forceFill(['sales_reports_to_id' => $managerId])->save();
    }

    public function openLeadsCount(int $userId): int
    {
        return (int) SalesLead::query()
            ->where('assigned_to', $userId)
            ->whereNotIn('stage', array_merge(SalesLead::CLOSED_STAGES, [SalesLead::WON_STAGE]))
            ->count();
    }
}
