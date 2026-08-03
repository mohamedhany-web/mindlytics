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
        // Backward compatible: single to_user_id → to_user_ids[]
        if (! $request->filled('to_user_ids') && $request->filled('to_user_id')) {
            $request->merge(['to_user_ids' => [(int) $request->input('to_user_id')]]);
        }

        $validated = $request->validate([
            'from_user_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'to_user_ids' => ['required', 'array', 'min:1'],
            'to_user_ids.*' => ['integer', Rule::exists('users', 'id'), 'different:from_user_id'],
            'scope' => ['required', Rule::in(['all', 'group'])],
            'group_id' => ['nullable', 'integer', Rule::exists('sales_lead_groups', 'id')],
            'confirm' => ['accepted'],
        ], [
            'from_user_id.required' => 'اختر موظف المصدر.',
            'to_user_ids.required' => 'اختر موظفاً واحداً على الأقل كوجهة.',
            'to_user_ids.min' => 'اختر موظفاً واحداً على الأقل كوجهة.',
            'to_user_ids.*.different' => 'لا يمكن أن يكون الموظف المصدر ضمن الوجهات.',
            'scope.required' => 'اختر نطاق التحويل.',
            'scope.in' => 'نطاق التحويل غير صالح.',
            'group_id.exists' => 'المجموعة المحددة غير موجودة.',
            'confirm.accepted' => 'يجب تأكيد عملية التحويل عبر المربع أسفل التنبيه.',
        ]);

        $fromId = (int) $validated['from_user_id'];
        $toIds = array_values(array_unique(array_map('intval', $validated['to_user_ids'])));
        $toIds = array_values(array_filter($toIds, fn (int $id) => $id !== $fromId));
        $scope = (string) $validated['scope'];
        $groupId = isset($validated['group_id']) ? (int) $validated['group_id'] : null;

        if ($toIds === []) {
            throw ValidationException::withMessages([
                'to_user_ids' => 'اختر موظفاً واحداً على الأقل مختلفاً عن المصدر.',
            ]);
        }

        if ($scope === 'group' && ! $groupId) {
            throw ValidationException::withMessages([
                'group_id' => 'اختر المجموعة المراد تحويل بياناتها.',
            ]);
        }

        $activeSales = User::salesEmployees()->where('is_active', true);

        if (! (clone $activeSales)->whereKey($fromId)->exists()) {
            return back()->withInput()->with('error', 'يرجى اختيار موظف مبيعات (من) فعّال.');
        }

        $toReps = (clone $activeSales)->whereIn('id', $toIds)->orderBy('name')->get(['id', 'name']);
        if ($toReps->count() !== count($toIds)) {
            return back()->withInput()->with('error', 'بعض موظفي الوجهة غير فعّالين أو ليسوا موظفي مبيعات.');
        }

        // Preserve selected order as submitted
        $toReps = collect($toIds)->map(fn (int $id) => $toReps->firstWhere('id', $id))->filter();

        $group = null;
        if ($scope === 'group') {
            $group = SalesLeadGroup::query()->find($groupId);
            if (! $group || ! $this->groupsForRep($fromId)->contains('id', $groupId)) {
                return back()->withInput()->with('error', 'المجموعة المحددة غير مرتبطة بموظف المصدر أو لا تحتوي على بياناته.');
            }
        }

        $fromRep = User::salesEmployees()->whereKey($fromId)->first();

        $summary = DB::transaction(function () use ($fromId, $toIds, $scope, $group) {
            return $scope === 'group'
                ? $this->transferGroupData($fromId, $toIds, $group)
                : $this->transferAllData($fromId, $toIds);
        });

        if ($fromRep && $toReps->isNotEmpty()) {
            try {
                app(SalesNotificationService::class)->notifyDataTransferredMulti($fromRep, $toReps, $summary);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $destNames = $toReps->pluck('name')->implode('، ');
        $successMsg = $scope === 'group' && $group
            ? 'تم توزيع بيانات المجموعة «'.$group->name.'» على: '.$destNames
            : 'تم توزيع بيانات الموظف على: '.$destNames;

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
     * @param  list<int>  $toIds
     * @return array<string, mixed>
     */
    private function transferAllData(int $fromId, array $toIds): array
    {
        $moved = $this->emptySummary($toIds);

        $leadIds = SalesLead::query()
            ->withTrashed()
            ->where('assigned_to', $fromId)
            ->orderBy('id')
            ->pluck('id');

        $assignment = $this->roundRobinAssign($leadIds->all(), $toIds);
        $moved = $this->applyLeadAssignments($assignment, $fromId, $moved);

        // Activities not yet moved with leads (orphans / null lead)
        $remainingActivityIds = SalesActivity::query()
            ->where('user_id', $fromId)
            ->orderBy('id')
            ->pluck('id')
            ->all();

        if ($remainingActivityIds !== []) {
            $actAssign = $this->roundRobinAssign($remainingActivityIds, $toIds);
            foreach ($actAssign as $toId => $ids) {
                if ($ids === []) {
                    continue;
                }
                $n = (int) SalesActivity::query()->whereIn('id', $ids)->update(['user_id' => $toId]);
                $moved['activities'] += $n;
                $moved['per_rep'][$toId]['activities'] = ($moved['per_rep'][$toId]['activities'] ?? 0) + $n;
            }
        }

        // created_by / won_confirmed_by still pointing at source (leads not in assigned_to set)
        $createdIds = SalesLead::query()->withTrashed()->where('created_by', $fromId)->orderBy('id')->pluck('id')->all();
        if ($createdIds !== []) {
            $createdAssign = $this->roundRobinAssign($createdIds, $toIds);
            foreach ($createdAssign as $toId => $ids) {
                if ($ids === []) {
                    continue;
                }
                $n = (int) SalesLead::query()->withTrashed()->whereIn('id', $ids)->update(['created_by' => $toId]);
                $moved['leads_created_by'] += $n;
            }
        }

        $wonIds = SalesLead::query()->withTrashed()->where('won_confirmed_by', $fromId)->orderBy('id')->pluck('id')->all();
        if ($wonIds !== []) {
            $wonAssign = $this->roundRobinAssign($wonIds, $toIds);
            foreach ($wonAssign as $toId => $ids) {
                if ($ids === []) {
                    continue;
                }
                $n = (int) SalesLead::query()->withTrashed()->whereIn('id', $ids)->update(['won_confirmed_by' => $toId]);
                $moved['leads_won_confirmed_by'] += $n;
            }
        }

        // Audit + KPI go to the first destination (cannot fairly split monthly KPI rows)
        $primaryTo = $toIds[0];
        $moved['audit_logs'] = (int) ActivityLog::query()
            ->whereIn('action', self::SALES_AUDIT_ACTIONS)
            ->where('user_id', $fromId)
            ->update(['user_id' => $primaryTo]);

        $this->transferKpiTargets($fromId, $primaryTo, $moved);

        SalesAuditService::log(
            'sales_data_transferred',
            null,
            ['from_user_id' => $fromId, 'scope' => 'all'],
            ['to_user_ids' => $toIds, 'per_rep' => $moved['per_rep']],
            'توزيع كل بيانات المبيعات من موظف #'.$fromId.' على موظفين: '.implode(',', $toIds)
        );

        return $moved;
    }

    /**
     * @param  list<int>  $toIds
     * @return array<string, mixed>
     */
    private function transferGroupData(int $fromId, array $toIds, SalesLeadGroup $group): array
    {
        $moved = $this->emptySummary($toIds);
        $moved['scope_group_id'] = (int) $group->id;

        $leadIds = SalesLead::query()
            ->withTrashed()
            ->where('assigned_to', $fromId)
            ->where('sales_lead_group_id', $group->id)
            ->orderBy('id')
            ->pluck('id');

        $assignment = $this->roundRobinAssign($leadIds->all(), $toIds);
        $moved = $this->applyLeadAssignments($assignment, $fromId, $moved);

        $this->syncGroupMembershipAfterTransfer($group, $fromId, $toIds);

        SalesAuditService::log(
            'sales_data_transferred',
            null,
            ['from_user_id' => $fromId, 'scope' => 'group', 'group_id' => $group->id],
            ['to_user_ids' => $toIds, 'per_rep' => $moved['per_rep']],
            'توزيع بيانات مجموعة «'.$group->name.'» من موظف #'.$fromId.' على: '.implode(',', $toIds)
        );

        return $moved;
    }

    /**
     * Apply per-lead assignee map and move matching CRM activities.
     *
     * @param  array<int, list<int>>  $assignment  toUserId => leadIds
     * @param  array<string, mixed>  $moved
     * @return array<string, mixed>
     */
    private function applyLeadAssignments(array $assignment, int $fromId, array $moved): array
    {
        $transferService = app(\App\Services\SalesLeadTransferService::class);
        $actor = auth()->user();

        foreach ($assignment as $toId => $ids) {
            if ($ids === []) {
                continue;
            }

            $leads = SalesLead::query()->withTrashed()->whereIn('id', $ids)->get();
            $n = 0;
            foreach ($leads as $lead) {
                if ((int) $lead->assigned_to === (int) $toId) {
                    continue;
                }
                try {
                    $transferService->assign(
                        $lead,
                        (int) $toId,
                        $actor,
                        'توزيع جماعي من الإدارة',
                        \App\Services\SalesLeadTransferService::SOURCE_ADMIN_BULK
                    );
                    $n++;
                } catch (\Throwable $e) {
                    // fallback: direct update if transfer validation fails
                    $lead->update(['assigned_to' => $toId]);
                    $n++;
                }
            }

            $moved['leads_assigned'] += $n;
            $moved['per_rep'][$toId]['leads'] = ($moved['per_rep'][$toId]['leads'] ?? 0) + $n;

            $created = (int) SalesLead::query()
                ->withTrashed()
                ->whereIn('id', $ids)
                ->where('created_by', $fromId)
                ->update(['created_by' => $toId]);
            $moved['leads_created_by'] += $created;

            $won = (int) SalesLead::query()
                ->withTrashed()
                ->whereIn('id', $ids)
                ->where('won_confirmed_by', $fromId)
                ->update(['won_confirmed_by' => $toId]);
            $moved['leads_won_confirmed_by'] += $won;

            $acts = (int) SalesActivity::query()
                ->where('user_id', $fromId)
                ->whereIn('sales_lead_id', $ids)
                ->update(['user_id' => $toId]);
            $moved['activities'] += $acts;
            $moved['per_rep'][$toId]['activities'] = ($moved['per_rep'][$toId]['activities'] ?? 0) + $acts;
        }

        return $moved;
    }

    /**
     * @param  list<int>  $itemIds
     * @param  list<int>  $toIds
     * @return array<int, list<int>>
     */
    private function roundRobinAssign(array $itemIds, array $toIds): array
    {
        $buckets = [];
        foreach ($toIds as $id) {
            $buckets[$id] = [];
        }

        $count = count($toIds);
        if ($count === 0) {
            return $buckets;
        }

        foreach (array_values($itemIds) as $i => $itemId) {
            $toId = $toIds[$i % $count];
            $buckets[$toId][] = (int) $itemId;
        }

        return $buckets;
    }

    /**
     * @param  list<int>  $toIds
     */
    private function syncGroupMembershipAfterTransfer(SalesLeadGroup $group, int $fromId, array $toIds): void
    {
        $memberIds = $group->memberIds()->map(fn ($id) => (int) $id)->values();

        foreach ($toIds as $toId) {
            if (! $memberIds->contains($toId)) {
                $memberIds->push($toId);
            }
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
            $memberIds = collect($toIds);
        }

        $group->syncMembers($memberIds->unique()->values()->all());
    }

    /**
     * @param  array<string, mixed>  $moved
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
     * @param  list<int>  $toIds
     * @return array<string, mixed>
     */
    private function emptySummary(array $toIds = []): array
    {
        $perRep = [];
        foreach ($toIds as $id) {
            $perRep[$id] = ['leads' => 0, 'activities' => 0];
        }

        return [
            'leads_assigned' => 0,
            'leads_created_by' => 0,
            'leads_won_confirmed_by' => 0,
            'activities' => 0,
            'audit_logs' => 0,
            'kpi_targets_moved' => 0,
            'kpi_targets_conflicts' => 0,
            'to_user_ids' => $toIds,
            'per_rep' => $perRep,
        ];
    }
}
