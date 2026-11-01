<?php

namespace App\Domains\Sales\Http\Controllers\Crm;

use App\Domains\Sales\Support\CrmNavigation;
use App\Domains\Sales\Support\CrmPermissions;
use App\Domains\Sales\Support\CrmScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/** مشترك بين controllers شاشات CRM: النطاق حسب الدور وإطار الصفحة. */
trait InteractsWithCrm
{
    /** النطاق حسب الدور، مع منع الدخول لوحدة قفلتها الإدارة (CrmPermissions). */
    private function scope(Request $request): CrmScope
    {
        $name = (string) $request->route()?->getName();
        $scope = CrmScope::for(Auth::user(), str_starts_with($name, 'admin.'));

        $module = explode('.', (string) preg_replace('/^(admin|employee)\.crm\./', '', $name))[0];
        abort_if(isset(CrmPermissions::MODULES[$module]) && ! $scope->can($module.'.access'), 403, 'الوحدة دي مقفولة لدورك من إعدادات CRM.');

        return $scope;
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
