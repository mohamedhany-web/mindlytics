<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesInterestType;
use App\Models\SalesLead;
use App\Models\User;
use App\Services\SalesLeadTransferService;
use App\Services\SalesSpecialtyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesDistributionController extends Controller
{
    public function index(Request $request, SalesSpecialtyService $specialtyService, SalesTeamService $teamService)
    {
        $types = $specialtyService->activeTypes();
        $filter = $request->query('filter', 'all'); // all|unassigned|mismatch
        $interestTypeId = $request->filled('interest_type_id') ? (int) $request->interest_type_id : null;

        $query = SalesLead::query()
            ->with(['assignee', 'creator', 'interestType', 'assignee.salesInterestTypes'])
            ->whereNotIn('stage', SalesLead::CLOSED_STAGES)
            ->orderByDesc('created_at');

        if ($interestTypeId) {
            $query->where('interest_type_id', $interestTypeId);
        }

        $leads = $query->limit(200)->get();

        if ($filter === 'unassigned') {
            $leads = $leads->filter(fn ($l) => ! $l->assigned_to);
        } elseif ($filter === 'mismatch') {
            $leads = $leads->filter(function ($l) use ($specialtyService) {
                if (! $l->assigned_to || ! $l->interest_type_id || ! $l->assignee) {
                    return (bool) $l->interest_type_id;
                }

                return ! $specialtyService->userHasSpecialty($l->assignee, (int) $l->interest_type_id);
            });
        }

        $specialistsByType = [];
        foreach ($types as $type) {
            $specialistsByType[$type->id] = $specialtyService->specialistsFor((int) $type->id);
        }

        $salesReps = User::salesEmployees()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.sales.distribution.index', compact(
            'types',
            'leads',
            'filter',
            'interestTypeId',
            'specialistsByType',
            'salesReps'
        ));
    }

    public function assign(Request $request, SalesLead $lead, SalesLeadTransferService $transferService)
    {
        $validated = $request->validate([
            'to_user_id' => 'required|integer|exists:users,id',
            'reason' => 'nullable|string|max:500',
        ]);

        $transferService->assign(
            $lead,
            (int) $validated['to_user_id'],
            Auth::user(),
            $validated['reason'] ?? 'توزيع حسب الاهتمام/التخصص',
            SalesLeadTransferService::SOURCE_DISTRIBUTION
        );

        return back()->with('success', 'تم تعيين العميل لـ '.$lead->fresh()->assignee?->name);
    }
}
