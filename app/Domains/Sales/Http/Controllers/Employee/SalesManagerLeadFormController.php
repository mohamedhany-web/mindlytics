<?php

namespace App\Domains\Sales\Http\Controllers\Employee;

use App\Domains\Sales\Http\Controllers\Admin\SalesLeadController as AdminSalesLeadController;
use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Services\SalesTeamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * إضافة وتعديل عميل من فورم الـ CRM لمدير المبيعات (نفس منطق الإدارة):
 * الموظف المختار لازم يكون في فريق المدير، والعميل اللي بيتعدّل لازم يكون في الفريق، والرجوع على صفحة الـ CRM بتاعة المدير.
 */
class SalesManagerLeadFormController extends AdminSalesLeadController
{
    public function __construct(private SalesTeamService $teamService) {}

    public function store(Request $request)
    {
        $this->assertTeamAssignee($request);

        return $this->toCrm(parent::store($request));
    }

    public function update(Request $request, SalesLead $lead)
    {
        abort_unless($this->teamService->canAccessLead(Auth::user(), $lead), 403);
        $this->assertTeamAssignee($request);

        $response = parent::update($request, $lead);

        return $response instanceof RedirectResponse ? redirect()->route('employee.crm.leads.show', $lead->id) : $response;
    }

    private function assertTeamAssignee(Request $request): void
    {
        $request->merge(['assigned_to' => (int) ($request->input('assigned_to') ?: Auth::id())]);
        if (! in_array((int) $request->input('assigned_to'), array_map('intval', $this->teamService->visibleAssigneeIds(Auth::user())), true)) {
            throw ValidationException::withMessages(['assigned_to' => ['اختار موظف من فريقك.']]);
        }
    }

    /** نفس الرسالة اللي سجّلتها الإدارة، بس الرجوع على صفحة العميل في الـ CRM بتاعة المدير. */
    private function toCrm($response)
    {
        if ($response instanceof RedirectResponse && preg_match('#/crm/leads/(\d+)#', $response->getTargetUrl(), $m)) {
            return redirect()->route('employee.crm.leads.show', (int) $m[1]);
        }

        return $response;
    }
}
