<?php

namespace App\Domains\Sales\Http\Controllers\Crm;

use App\Domains\Sales\Http\Controllers\Crm\Concerns\InteractsWithCrm;
use App\Domains\Sales\Models\CrmSavedFilter;
use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Services\SalesLeadMergeService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** أدوات قايمة العملاء: الفلاتر المحفوظة، ودمج العملاء المكررين (docs/SALES_CRM.md). */
class CrmLeadToolsController extends Controller
{
    use InteractsWithCrm;

    public function __construct(private SalesLeadMergeService $merger) {}

    public function storeFilter(Request $request): RedirectResponse
    {
        $scope = $this->scope($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'module' => ['required', Rule::in(array_keys(CrmSavedFilter::MODULES))],
        ], ['name.required' => 'اكتب اسم للفلتر.']);
        $params = CrmSavedFilter::cleanParams($data['module'], (array) $request->input('params', []));
        if ($params === []) {
            return back()->with('error', 'اختار فلتر الأول وبعدين احفظه.');
        }

        CrmSavedFilter::query()->updateOrCreate(
            ['user_id' => $scope->user->id, 'module' => $data['module'], 'name' => trim($data['name'])],
            ['params' => $params]
        );

        return redirect()->route($scope->route('leads.index'), $params)->with('success', 'اتحفظ الفلتر «'.trim($data['name']).'».');
    }

    public function destroyFilter(Request $request, int $filter): RedirectResponse
    {
        $scope = $this->scope($request);
        CrmSavedFilter::query()->where('user_id', $scope->user->id)->whereKey($filter)->delete();

        return back()->with('success', 'اتمسح الفلتر.');
    }

    public function mergeForm(Request $request, int $lead): View
    {
        $scope = $this->scope($request);
        $lead = $this->visibleLead($scope, $lead);
        $other = $request->integer('with') ? $this->visibleLead($scope, $request->integer('with')) : null;

        return $this->crmView($scope, 'leads', 'leads.merge', [
            'lead' => $lead,
            'other' => $other,
            'search' => trim((string) $request->query('q', '')),
            'candidates' => $other ? collect() : $this->merger->candidates($lead, $scope, $request->query('q')),
            // سبب الرفض بيعتمد على مين اللي هيفضل: this = العميل ده يفضل، other = التاني يفضل
            'block' => $other ? ['this' => $this->merger->blockReason($lead, $other, $scope), 'other' => $this->merger->blockReason($other, $lead, $scope)] : null,
            'counts' => $other ? [$lead->id => $this->merger->linkedCounts($lead), $other->id => $this->merger->linkedCounts($other)] : [],
        ]);
    }

    public function merge(Request $request, int $lead): RedirectResponse
    {
        $scope = $this->scope($request);
        $lead = $this->visibleLead($scope, $lead);
        $data = $request->validate(['other_id' => ['required', 'integer'], 'keep' => ['required', Rule::in(['this', 'other'])]]);
        $other = $this->visibleLead($scope, (int) $data['other_id']);
        [$keep, $dupe] = $data['keep'] === 'this' ? [$lead, $other] : [$other, $lead];

        $kept = $this->merger->merge($keep, $dupe, $scope, $scope->user);

        return redirect()->route($scope->route('leads.show'), $kept->id)->with('success', 'اتدمج «'.$dupe->name.'» في «'.$kept->name.'»، وكل سجلاته اتنقلت.');
    }

    private function visibleLead($scope, int $id): SalesLead
    {
        $lead = SalesLead::query()->with('assignee:id,name')->find($id);
        abort_unless($lead && $scope->canSee($lead), 404);

        return $lead;
    }
}
