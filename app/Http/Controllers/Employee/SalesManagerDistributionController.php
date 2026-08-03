<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalesLead;
use App\Services\SalesLeadTransferService;
use App\Services\SalesSpecialtyService;
use App\Services\SalesTeamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesManagerDistributionController extends Controller
{
    public function index(Request $request, SalesTeamService $teamService, SalesSpecialtyService $specialtyService)
    {
        $team = $teamService->managedTeamOrFail(Auth::user());
        $memberIds = $teamService->memberUserIds($team);
        $types = $specialtyService->activeTypes();
        $filter = $request->query('filter', 'all');
        $interestTypeId = $request->filled('interest_type_id') ? (int) $request->interest_type_id : null;

        $query = SalesLead::query()
            ->with(['assignee', 'creator', 'interestType', 'assignee.salesInterestTypes'])
            ->whereIn('assigned_to', $memberIds)
            ->whereNotIn('stage', SalesLead::CLOSED_STAGES)
            ->orderByDesc('created_at');

        if ($interestTypeId) {
            $query->where('interest_type_id', $interestTypeId);
        }

        $leads = $query->limit(200)->get();

        if ($filter === 'mismatch') {
            $leads = $leads->filter(function ($l) use ($specialtyService) {
                if (! $l->assigned_to || ! $l->interest_type_id || ! $l->assignee) {
                    return (bool) $l->interest_type_id;
                }

                return ! $specialtyService->userHasSpecialty($l->assignee, (int) $l->interest_type_id);
            });
        }

        $specialistsByType = [];
        foreach ($types as $type) {
            $specialistsByType[$type->id] = $specialtyService->specialistsFor((int) $type->id, $memberIds);
        }

        $salesReps = $team->members()->with('user:id,name')->get()->pluck('user')->filter()->sortBy('name')->values();

        return view('employee.sales-manager.distribution.index', compact(
            'types',
            'leads',
            'filter',
            'interestTypeId',
            'specialistsByType',
            'salesReps',
            'team'
        ));
    }

    public function assign(Request $request, SalesLead $lead, SalesTeamService $teamService, SalesLeadTransferService $transferService)
    {
        $manager = Auth::user();
        $team = $teamService->managedTeamOrFail($manager);
        $memberIds = $teamService->memberUserIds($team);

        if (! in_array((int) $lead->assigned_to, $memberIds, true)) {
            abort(403);
        }

        $validated = $request->validate([
            'to_user_id' => 'required|integer|exists:users,id',
            'reason' => 'nullable|string|max:500',
        ]);

        $toId = (int) $validated['to_user_id'];
        if (! in_array($toId, $memberIds, true)) {
            return back()->with('error', 'يجب التحويل لعضو في فريقك.');
        }

        $transferService->assign(
            $lead,
            $toId,
            $manager,
            $validated['reason'] ?? 'توزيع المدير حسب التخصص',
            SalesLeadTransferService::SOURCE_DISTRIBUTION,
            (int) $team->id
        );

        return back()->with('success', 'تم تحويل العميل بنجاح.');
    }
}
