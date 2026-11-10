<?php

namespace App\Domains\Sales\Services;

use App\Domains\Sales\Models\CrmFeatureAccess;
use App\Domains\Sales\Support\CrmFeatureCatalog;
use App\Domains\Sales\Support\CrmScope;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class CrmAccessService
{
    private static ?bool $tableReady = null;

    private static function tableReady(): bool
    {
        return self::$tableReady ??= \Illuminate\Support\Facades\Schema::hasTable('crm_feature_access');
    }

    /** @return array<string, bool> feature_key => allowed */
    public function effectiveMap(User $user, ?CrmScope $scope = null): array
    {
        $scope ??= CrmScope::for($user, $user->isAdmin() || $user->isSuperAdmin() || ($user->role ?? '') === 'admin');
        $role = $scope->role;
        // لو جدول الاستثناءات لسه ما اتعملش (migration ما اشتغلش) الصلاحيات الافتراضية للدور بتشتغل بدل ما الصفحة تقع
        $overrides = self::tableReady()
            ? CrmFeatureAccess::query()->where('user_id', $user->id)->pluck('allowed', 'feature_key')
            : collect();

        $map = [];
        foreach (CrmFeatureCatalog::all() as $feature) {
            $key = $feature['key'];
            if ($overrides->has($key)) {
                $map[$key] = (bool) $overrides[$key];
            } else {
                $map[$key] = CrmFeatureCatalog::defaultAllowed($key, $role);
            }
        }

        return $map;
    }

    public function allows(User $user, string $featureKey, ?CrmScope $scope = null): bool
    {
        $map = $this->effectiveMap($user, $scope);

        return $map[$featureKey] ?? true;
    }

    public function allowsTab(User $user, string $tabKey, ?CrmScope $scope = null): bool
    {
        $feature = CrmFeatureCatalog::tabFeatureMap()[$tabKey] ?? null;
        if ($feature === null) {
            return true;
        }

        return $this->allows($user, $feature, $scope);
    }

    public function allowsRouteHint(User $user, string $routeName, ?CrmScope $scope = null): bool
    {
        // أطول مفتاح بيطابق هو اللي بيحكم (meta-social.agents قبل meta-social، daily-reports قبل reports)
        $match = null;
        foreach (CrmFeatureCatalog::routeFeatureHints() as $needle => $featureKey) {
            if (str_contains($routeName, $needle) && ($match === null || strlen($needle) > strlen($match[0]))) {
                $match = [$needle, $featureKey];
            }
        }

        return $match === null || $this->allows($user, $match[1], $scope);
    }

    /**
     * يحفظ الحالة المطلوبة: يخزّن فقط ما يختلف عن افتراضي الدور.
     *
     * @param  array<string, bool>  $desired  feature_key => allowed
     */
    public function sync(User $user, array $desired, ?CrmScope $scope = null): void
    {
        $scope ??= CrmScope::for($user, $user->isAdmin() || $user->isSuperAdmin() || ($user->role ?? '') === 'admin');
        $role = $scope->role;
        $catalog = CrmFeatureCatalog::keyed();

        DB::transaction(function () use ($user, $desired, $role, $catalog) {
            CrmFeatureAccess::query()->where('user_id', $user->id)->delete();

            foreach ($catalog as $key => $meta) {
                if (! array_key_exists($key, $desired)) {
                    continue;
                }
                $allowed = (bool) $desired[$key];
                $baseline = CrmFeatureCatalog::defaultAllowed($key, $role);
                if ($allowed === $baseline) {
                    continue;
                }
                CrmFeatureAccess::query()->create([
                    'user_id' => $user->id,
                    'feature_key' => $key,
                    'allowed' => $allowed,
                ]);
            }
        });
    }

    /** موظفو المبيعات + المديرون (+ أدمن المنصة إن لزم العرض). */
    public function manageableUsers(): Collection
    {
        return User::query()
            ->with(['employeeJob:id,code,name'])
            ->where(function ($q) {
                $q->whereHas('employeeJob', function ($j) {
                    $j->whereIn('code', ['sales', 'sales_manager', 'business_developer']);
                })->orWhereIn('role', ['admin', 'super_admin']);
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role', 'employee_job_id']);
    }
}
