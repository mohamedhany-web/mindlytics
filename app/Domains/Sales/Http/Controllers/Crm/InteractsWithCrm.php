<?php

namespace App\Domains\Sales\Http\Controllers\Crm;

use App\Domains\Sales\Support\CrmNavigation;
use App\Domains\Sales\Support\CrmScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/** مشترك بين controllers شاشات CRM: النطاق حسب الدور وإطار الصفحة. */
trait InteractsWithCrm
{
    private function scope(Request $request): CrmScope
    {
        return CrmScope::for(Auth::user(), str_starts_with((string) $request->route()?->getName(), 'admin.'));
    }

    private function view(CrmScope $scope, string $module, string $view, array $data): View
    {
        return view('sales::crm.'.$view, $data + [
            'crm' => $scope,
            'crmModule' => $module,
            'crmTabs' => CrmNavigation::tabs($scope),
            'crmQuickCreate' => CrmNavigation::quickCreate($scope),
        ]);
    }

    /** رجوع لصفحة داخل الموقع لو اتبعتت في redirect_to، وإلا للمسار الافتراضي. */
    private function backTo(Request $request, string $fallback, string $message): RedirectResponse
    {
        $to = (string) $request->input('redirect_to', '');

        return redirect()->to($to !== '' && str_starts_with($to, url('/')) ? $to : $fallback)->with('success', $message);
    }
}
