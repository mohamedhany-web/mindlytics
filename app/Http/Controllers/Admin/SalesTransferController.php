<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SalesActivity;
use App\Models\SalesLead;
use App\Models\SalesLeadGroup;
use App\Models\User;
use App\Services\SalesAuditService;
use App\Services\SalesNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SalesTransferController extends Controller
{
    private const SALES_AUDIT_ACTIONS = SalesAuditController::SALES_ACTIONS;

    public function index(Request $request)
    {
        $salesReps = User::salesEmployees()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $fromId = $request->filled('from_user_id') ? (int) $request->query('from_user_id') : null;
        $scope = $request->query('scope', 'all') === 'group' ? 'group' : 'all';
        $groupId = $request->filled('group_id') ? (int) $request->query('group_id') : null;
        $fromRep = $fromId ? $salesReps->firstWhere('id', $fromId) : null;

        $groups = collect();
        $selectedGroup = null;
        $stats = null;

        if ($fromRep) {
            $groups = $this->groupsForRep($fromId);
            if ($scope === 'group' && $groupId) {
                $selectedGroup = $groups->firstWhere('id', $groupId);
                if (! $selectedGroup) {
                    $scope = 'all';
                    $groupId = null;
                }
            }

            $stats = $this->buildStats($fromId, $scope, $selectedGroup?->id);
        }

        return view('admin.sales.transfer.index', [
            'salesReps' => $salesReps,
            'fromId' => $fromId,
            'fromRep' => $fromRep,
            'stats' => $stats,
            'groups' => $groups,
            'scope' => $scope,
            'groupId' => $groupId,
            'selectedGroup' => $selectedGroup,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_user_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'to_user_id' => ['required', 'integer', Rule::exists('users', 'id'), 'different:from_user_id'],
            'scope' => ['required', Rule::in(['all', 'group'])],
            'group_id' => ['nullable', 'integer', Rule::exists('sales_lead_groups', 'id')],
            'confirm' => ['accepted'],
        ], [
            'from_user_id.required' => 'اختر موظف المصدر.',
            'to_user_id.required' => 'اختر موظف الوجهة.',
            'to_user_id.different' => 'يجب أن يكون الموظف الوجهة مختلفاً عن المصدر.',
            'scope.required' => 'اختر نطاق التحويل.',
            'scope.in' => 'نطاق التحويل غير صالح.',
            'group_id.exists' => 'المجموعة المحددة غير موجودة.',
            'confirm.accepted' => 'يجب تأكيد عملية التحويل عبر المربع أسفل التنبيه.',
        ]);

        $fromId = (int) $validated['from_user_id'];
        $toId = (int) $validated['to_user_id'];
        $scope = (string) $validated['scope'];
        $groupId = isset($validated['group_id']) ? (int) $validated['group_id'] : null;

        if ($scope === 'group' && ! $groupId) {
            throw ValidationException::withMessages([
                'group_id' => 'اختر المجموعة المراد تحويل بياناتها.',
            ]);
        }

        if (! User::salesEmployees()->where('is_active', true)->whereKey($fromId)->exists()) {
            return back()->withInput()->with('error', 'يرجى اختيار موظف مبيعات (من) فعّال.');
        }
        if (! User::salesEmployees()->where('is_active', true)->whereKey($toId)->exists()) {
            return back()->withInput()->with('error', 'يرجى اختيار موظف مبيعات (إلى) فعّال.');
        }

        $group = null;
        if ($scope === 'group') {
            $group = SalesLeadGroup::query()->find($groupId);
            if (! $group || ! $this->groupsForRep($fromId)->contains('id', $groupId)) {
                return back()->withInput()->with('error', 'المجموعة المحددة غير مرتبطة بموظف المصدر أو لا تحتوي على بياناته.');
            }
        }

        $fromRep = User::salesEmployees()->whereKey($fromId)->first();
        $toRep = User::salesEmployees()->whereKey($toId)->first();

        $summary = DB::transaction(function () use ($fromId, $toId, $scope, $group) {
            return $scope === 'group'
                ? $this->transferGroupData($fromId, $toId, $group)
                : $this->transferAllData($fromId, $toId);
        });

        if ($fromRep && $toRep) {
            try {
                app(SalesNotificationService::class)->notifyDataTransferred($fromRep, $toRep, $summary);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $successMsg = $scope === 'group' && $group
            ? 'تم تحويل بيانات المجموعة «'.$group->name.'» بنجاح.'
            : 'تم تحويل كل بيانات الموظف بنجاح.';

        return redirect()
            ->route('admin.sales.transfer.index', array_filter([
                'from_user_id' => $fromId,
                'scope' => $scope,
                'group_id' => $group?->id,
            ]))
            ->with('success', $successMsg)
            ->with('transfer_summary', $summary);
    }

    /**
     * @return Collection<int, SalesLeadGroup>
     */
    private function groupsForRep(int $fromId): Collection
    {
        $groupIdsFromLeads = SalesLead::query()
            ->withTrashed()
            ->where('assigned_to', $fromId)
            ->whereNotNull('sales_lead_group_id')
            ->distinct()
            ->pluck('sales_lead_group_id');

        return SalesLeadGroup::query()
            ->where(function ($q) use ($fromId, $groupIdsFromLeads) {
                $q->forAssignee($fromId);
                if ($groupIdsFromLeads->isNotEmpty()) {
                    $q->orWhereIn('id', $groupIdsFromLeads);
                }
            })
            ->withCount([
                'leads as leads_for_rep_count' => fn ($q) => $q->withTrashed()->where('assigned_to', $fromId),
            ])
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'assigned_to']);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildStats(int $fromId, string $scope, ?int $groupId): array
    {
        $leadsBase = SalesLead::query()->withTrashed()->where('assigned_to', $fromId);
        if ($scope === 'group' && $groupId) {
            $leadsBase->where('sales_lead_group_id', $groupId);
        }

        $leadsTotal = (int) (clone $leadsBase)->count();
        $leadsByStage = (clone $leadsBase)
            ->selectRaw('stage, COUNT(*) as c')
            ->groupBy('stage')
            ->pluck('c', 'stage')
            ->toArray();

        $leadIds = (clone $leadsBase)->pluck('id');

        if ($scope === 'group' && $groupId) {
            $activitiesTotal = $leadIds->isEmpty()
                ? 0
                : (int) SalesActivity::query()
                    ->where('user_id', $fromId)
                    ->whereIn('sales_lead_id', $leadIds)
                    ->count();

            return [
                'scope' => 'group',
                'group_id' => $groupId,
                'leads_total' => $leadsTotal,
                'leads_by_stage' => $leadsByStage,
                'activities_total' => $activitiesTotal,
                'audit_total' => 0,
                'won_confirmed_total' => (int) (clone $leadsBase)->where('won_confirmed_by', $fromId)->count(),
                'created_by_total' => (int) (clone $leadsBase)->where('created_by', $fromId)->count(),
                'kpi_targets_total' => 0,
                'ungrouped_leads' => 0,
            ];
        }

        $activitiesTotal = (int) SalesActivity::query()->where('user_id', $fromId)->count();
        $auditTotal = (int) ActivityLog::query()->whereIn('action', self::SALES_AUDIT_ACTIONS)->where('user_id', $fromId)->count();
        $wonConfirmed = (int) SalesLead::query()->withTrashed()->where('won_confirmed_by', $fromId)->count();
        $createdBy = (int) SalesLead::query()->withTrashed()->where('created_by', $fromId)->count();
        $ungrouped = (int) SalesLead::query()->withTrashed()->where('assigned_to', $fromId)->whereNull('sales_lead_group_id')->count();

        $kpiTargetsTotal = 0;
        if (Schema::hasTable('sales_kpi_targets')) {
            $kpiTargetsTotal = (int) DB::table('sales_kpi_targets')->where('user_id', $fromId)->count();
        }

        return [
            'scope' => 'all',
            'group_id' => null,
            'leads_total' => $leadsTotal,
            'leads_by_stage' => $leadsByStage,
            'activities_total' => $activitiesTotal,
            'audit_total' => $auditTotal,
            'won_confirmed_total' => $wonConfirmed,
            'created_by_total' => $createdBy,
            'kpi_targets_total' => $kpiTargetsTotal,
            'ungrouped_leads' => $ungrouped,
        ];
    }

    /**
     * @return array<string, int>
     */
    private function transferAllData(int $fromId, int $toId): array
    {
        $moved = $this->emptySummary();

        $moved['leads_assigned'] = (int) SalesLead::query()
            ->withTrashed()
            ->where('assigned_to', $fromId)
            ->update(['assigned_to' => $toId]);

        $moved['leads_created_by'] = (int) SalesLead::query()
            ->withTrashed()
            ->where('created_by', $fromId)
            ->update(['created_by' => $toId]);

        $moved['leads_won_confirmed_by'] = (int) SalesLead::query()
            ->withTrashed()
            ->where('won_confirmed_by', $fromId)
            ->update(['won_confirmed_by' => $toId]);

        $moved['activities'] = (int) SalesActivity::query()
            ->where('user_id', $fromId)
            ->update(['user_id' => $toId]);

        $moved['audit_logs'] = (int) ActivityLog::query()
            ->whereIn('action', self::SALES_AUDIT_ACTIONS)
            ->where('user_id', $fromId)
            ->update(['user_id' => $toId]);

        $this->transferKpiTargets($fromId, $toId, $moved);

        SalesAuditService::log(
            'sales_data_transferred',
            null,
            ['from_user_id' => $fromId, 'scope' => 'all'],
            ['to_user_id' => $toId],
            'تحويل كل بيانات المبيعات من موظف #'.$fromId.' إلى موظف #'.$toId
        );

        return $moved;
    }

    /**
     * @return array<string, int>
     */
    private function transferGroupData(int $fromId, int $toId, SalesLeadGroup $group): array
    {
        $moved = $this->emptySummary();
        $moved['scope_group_id'] = (int) $group->id;

        $leadIds = SalesLead::query()
            ->withTrashed()
            ->where('assigned_to', $fromId)
            ->where('sales_lead_group_id', $group->id)
            ->pluck('id');

        if ($leadIds->isNotEmpty()) {
            $moved['leads_assigned'] = (int) SalesLead::query()
                ->withTrashed()
                ->whereIn('id', $leadIds)
                ->update(['assigned_to' => $toId]);

            $moved['leads_created_by'] = (int) SalesLead::query()
                ->withTrashed()
                ->whereIn('id', $leadIds)
                ->where('created_by', $fromId)
                ->update(['created_by' => $toId]);

            $moved['leads_won_confirmed_by'] = (int) SalesLead::query()
                ->withTrashed()
                ->whereIn('id', $leadIds)
                ->where('won_confirmed_by', $fromId)
                ->update(['won_confirmed_by' => $toId]);

            $moved['activities'] = (int) SalesActivity::query()
                ->where('user_id', $fromId)
                ->whereIn('sales_lead_id', $leadIds)
                ->update(['user_id' => $toId]);
        }

        $this->syncGroupMembershipAfterTransfer($group, $fromId, $toId);

        SalesAuditService::log(
            'sales_data_transferred',
            null,
            ['from_user_id' => $fromId, 'scope' => 'group', 'group_id' => $group->id],
            ['to_user_id' => $toId],
            'تحويل بيانات مجموعة المبيعات «'.$group->name.'» من موظف #'.$fromId.' إلى موظف #'.$toId
        );

        return $moved;
    }

    private function syncGroupMembershipAfterTransfer(SalesLeadGroup $group, int $fromId, int $toId): void
    {
        $memberIds = $group->memberIds()->map(fn ($id) => (int) $id)->values();

        if (! $memberIds->contains($toId)) {
            $memberIds->push($toId);
        }

        $fromStillHasLeads = SalesLead::query()
            ->withTrashed()
            ->where('assigned_to', $fromId)
            ->where('sales_lead_group_id', $group->id)
            ->exists();

        if (! $fromStillHasLeads) {
            $memberIds = $memberIds->reject(fn ($id) => $id === $fromId)->values();
        }

        if ($memberIds->isEmpty()) {
            $memberIds = collect([$toId]);
        }

        $group->syncMembers($memberIds->all());
    }

    /**
     * @param  array<string, int>  $moved
     */
    private function transferKpiTargets(int $fromId, int $toId, array &$moved): void
    {
        if (! Schema::hasTable('sales_kpi_targets')) {
            return;
        }

        $fromRows = DB::table('sales_kpi_targets')
            ->where('user_id', $fromId)
            ->select(['id', 'year_month'])
            ->get();

        foreach ($fromRows as $row) {
            $exists = DB::table('sales_kpi_targets')
                ->where('user_id', $toId)
                ->where('year_month', $row->year_month)
                ->exists();

            if ($exists) {
                $moved['kpi_targets_conflicts']++;
                continue;
            }

            $ok = DB::table('sales_kpi_targets')
                ->where('id', $row->id)
                ->update(['user_id' => $toId]);

            if ($ok) {
                $moved['kpi_targets_moved']++;
            }
        }
    }

    /**
     * @return array<string, int>
     */
    private function emptySummary(): array
    {
        return [
            'leads_assigned' => 0,
            'leads_created_by' => 0,
            'leads_won_confirmed_by' => 0,
            'activities' => 0,
            'audit_logs' => 0,
            'kpi_targets_moved' => 0,
            'kpi_targets_conflicts' => 0,
        ];
    }
}
