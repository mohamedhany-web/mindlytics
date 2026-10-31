<?php

namespace App\Domains\Sales\Http\Controllers\Crm\Concerns;

use App\Domains\Sales\Support\CrmNavigation;
use App\Domains\Sales\Support\CrmScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

trait InteractsWithCrm
{
    protected function scope(Request $request): CrmScope
    {
        return CrmScope::for(Auth::user(), str_starts_with((string) $request->route()?->getName(), 'admin.'));
    }

    protected function crmView(CrmScope $scope, string $module, string $view, array $data = []): View
    {
        return view('sales::crm.'.$view, $data + [
            'crm' => $scope,
            'crmModule' => $module,
            'crmTabs' => CrmNavigation::tabs($scope),
            'crmQuickCreate' => CrmNavigation::quickCreate($scope),
        ]);
    }

    protected function applyAssigneeScope($query, CrmScope $scope, string $column = 'assigned_to')
    {
        if ($scope->assigneeIds !== null) {
            $query->whereIn($column, $scope->assigneeIds === [] ? [0] : $scope->assigneeIds);
        }

        return $query;
    }

    protected function canAssignTo(CrmScope $scope, ?int $userId): bool
    {
        if ($userId === null) {
            return $scope->isAdmin();
        }

        return $scope->assigneeIds === null || in_array($userId, $scope->assigneeIds, true);
    }
}
