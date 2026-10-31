<?php

namespace App\Domains\Sales\Http\Controllers\Crm;

use App\Domains\Sales\Http\Controllers\Crm\Concerns\InteractsWithCrm;
use App\Domains\Sales\Services\CrmAccessService;
use App\Domains\Sales\Support\CrmFeatureCatalog;
use App\Domains\Sales\Support\CrmScope;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrmAccessController extends Controller
{
    use InteractsWithCrm;

    public function __construct(private CrmAccessService $access) {}

    public function index(Request $request): View
    {
        $scope = $this->scope($request);
        abort_unless($scope->isAdmin(), 403);
        abort_unless($this->access->allows($scope->user, 'ops.access_matrix', $scope), 403);

        $users = $this->access->manageableUsers()->map(function (User $user) {
            $userScope = CrmScope::for(
                $user,
                $user->isAdmin() || $user->isSuperAdmin() || in_array((string) $user->role, ['admin', 'super_admin'], true)
            );
            $map = $this->access->effectiveMap($user, $userScope);
            $enabled = count(array_filter($map));
            $total = count($map);

            return [
                'user' => $user,
                'crm_role' => $userScope->role,
                'role_label' => match ($userScope->role) {
                    'admin' => 'أدمن CRM',
                    'manager' => 'مدير مبيعات',
                    default => 'مندوب',
                },
                'enabled' => $enabled,
                'total' => $total,
                'job' => $user->employeeJob?->name ?? ($user->role ?: '—'),
            ];
        });

        return $this->crmView($scope, 'admin', 'access.index', compact('users'));
    }

    public function edit(Request $request, User $user): View
    {
        $scope = $this->scope($request);
        abort_unless($scope->isAdmin(), 403);
        abort_unless($this->access->allows($scope->user, 'ops.access_matrix', $scope), 403);

        $targetScope = CrmScope::for(
            $user,
            $user->isAdmin() || $user->isSuperAdmin() || in_array((string) $user->role, ['admin', 'super_admin'], true)
        );
        $effective = $this->access->effectiveMap($user, $targetScope);
        $groups = CrmFeatureCatalog::groups();
        $features = CrmFeatureCatalog::all();
        $baselines = [];
        foreach ($features as $f) {
            $baselines[$f['key']] = CrmFeatureCatalog::defaultAllowed($f['key'], $targetScope->role);
        }

        return $this->crmView($scope, 'admin', 'access.edit', [
            'target' => $user,
            'targetScope' => $targetScope,
            'effective' => $effective,
            'baselines' => $baselines,
            'groups' => $groups,
            'features' => $features,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $scope = $this->scope($request);
        abort_unless($scope->isAdmin(), 403);
        abort_unless($this->access->allows($scope->user, 'ops.access_matrix', $scope), 403);

        $keys = array_column(CrmFeatureCatalog::all(), 'key');
        $posted = $request->input('features', []);
        $desired = [];
        foreach ($keys as $key) {
            $desired[$key] = array_key_exists($key, $posted) && (string) $posted[$key] === '1';
        }

        $targetScope = CrmScope::for(
            $user,
            $user->isAdmin() || $user->isSuperAdmin() || in_array((string) $user->role, ['admin', 'super_admin'], true)
        );
        $this->access->sync($user, $desired, $targetScope);

        return redirect()
            ->route($scope->route('access.edit'), $user)
            ->with('success', 'تم تحديث صلاحيات CRM لـ '.$user->name);
    }
}
