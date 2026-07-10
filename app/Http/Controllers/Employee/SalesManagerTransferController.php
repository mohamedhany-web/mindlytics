<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalesLead;
use App\Services\SalesTeamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SalesManagerTransferController extends Controller
{
    public function __construct(
        private SalesTeamService $teamService
    ) {
        $this->middleware('sales.manager');
    }

    public function index(Request $request)
    {
        $team = $this->teamService->managedTeamOrFail(Auth::user());
        $members = $team->members()->with('user:id,name')->get();
        $memberIds = $this->teamService->memberUserIds($team);

        $fromId = $request->filled('from_user_id') ? (int) $request->from_user_id : null;
        $toId = $request->filled('to_user_id') ? (int) $request->to_user_id : null;
        $fromRep = $fromId ? $members->firstWhere('user_id', $fromId)?->user : null;
        $toRep = $toId ? $members->firstWhere('user_id', $toId)?->user : null;

        $memberLeadCounts = SalesLead::query()
            ->whereIn('assigned_to', $memberIds)
            ->selectRaw('assigned_to, COUNT(*) as total')
            ->groupBy('assigned_to')
            ->pluck('total', 'assigned_to');

        $stats = null;
        if ($fromRep && in_array($fromId, $memberIds, true)) {
            $leadsBase = SalesLead::query()->where('assigned_to', $fromId);
            $byStage = (clone $leadsBase)
                ->selectRaw('stage, COUNT(*) as c')
                ->groupBy('stage')
                ->pluck('c', 'stage')
                ->toArray();

            $stats = [
                'leads_total' => (int) (clone $leadsBase)->count(),
                'leads_open' => (int) (clone $leadsBase)->openPipeline()->count(),
                'leads_by_stage' => $byStage,
                'overdue' => (int) (clone $leadsBase)->openPipeline()
                    ->whereNotNull('next_follow_up_at')
                    ->where('next_follow_up_at', '<', now())
                    ->count(),
            ];
        }

        return view('employee.sales-manager.transfer.index', compact(
            'team',
            'members',
            'fromId',
            'toId',
            'fromRep',
            'toRep',
            'stats',
            'memberLeadCounts',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $team = $this->teamService->managedTeamOrFail(Auth::user());
        $memberIds = $this->teamService->memberUserIds($team);

        $validated = $request->validate([
            'from_user_id' => ['required', 'integer', Rule::in($memberIds)],
            'to_user_id' => ['required', 'integer', Rule::in($memberIds), 'different:from_user_id'],
            'confirm' => ['required', 'accepted'],
        ], [
            'from_user_id.required' => 'اختر الموظف المصدر.',
            'to_user_id.required' => 'اختر الموظف المستلم.',
            'to_user_id.different' => 'يجب أن يكون المستلم مختلفاً عن المصدر.',
            'confirm.accepted' => 'يرجى تأكيد عملية التحويل.',
        ]);

        $fromId = (int) $validated['from_user_id'];
        $toId = (int) $validated['to_user_id'];

        $moved = SalesLead::query()
            ->where('assigned_to', $fromId)
            ->update(['assigned_to' => $toId]);

        return redirect()
            ->route('employee.sales-manager.transfer.index', ['from_user_id' => $toId])
            ->with('success', "تم تحويل {$moved} عميل محتمل بنجاح إلى الموظف المحدد.");
    }
}
