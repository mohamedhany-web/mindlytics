<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SalesActivity;
use App\Models\SalesLead;
use App\Models\User;
use App\Services\SalesAuditService;
use App\Services\SalesNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
        $fromRep = $fromId ? $salesReps->firstWhere('id', $fromId) : null;

        $stats = null;
        if ($fromRep) {
            $leadsBase = SalesLead::query()->withTrashed()->where('assigned_to', $fromId);
            $leadsTotal = (int) (clone $leadsBase)->count();
            $leadsByStage = (clone $leadsBase)
                ->selectRaw('stage, COUNT(*) as c')
                ->groupBy('stage')
                ->pluck('c', 'stage')
                ->toArray();

            $activitiesTotal = (int) SalesActivity::query()->where('user_id', $fromId)->count();
            $auditTotal = (int) ActivityLog::query()->whereIn('action', self::SALES_AUDIT_ACTIONS)->where('user_id', $fromId)->count();
            $wonConfirmed = (int) SalesLead::query()->withTrashed()->where('won_confirmed_by', $fromId)->count();
            $createdBy = (int) SalesLead::query()->withTrashed()->where('created_by', $fromId)->count();

            $kpiTargetsTotal = 0;
            if (DB::getSchemaBuilder()->hasTable('sales_kpi_targets')) {
                $kpiTargetsTotal = (int) DB::table('sales_kpi_targets')->where('user_id', $fromId)->count();
            }

            $stats = [
                'leads_total' => $leadsTotal,
                'leads_by_stage' => $leadsByStage,
                'activities_total' => $activitiesTotal,
                'audit_total' => $auditTotal,
                'won_confirmed_total' => $wonConfirmed,
                'created_by_total' => $createdBy,
                'kpi_targets_total' => $kpiTargetsTotal,
            ];
        }

        return view('admin.sales.transfer.index', [
            'salesReps' => $salesReps,
            'fromId' => $fromId,
            'fromRep' => $fromRep,
            'stats' => $stats,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_user_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'to_user_id' => ['required', 'integer', Rule::exists('users', 'id'), 'different:from_user_id'],
            'confirm' => ['required', 'accepted'],
        ], [
            'confirm.accepted' => 'يرجى تأكيد عملية التحويل.',
        ]);

        $fromId = (int) $validated['from_user_id'];
        $toId = (int) $validated['to_user_id'];

        if (! User::salesEmployees()->where('is_active', true)->whereKey($fromId)->exists()) {
            return back()->withInput()->with('error', 'يرجى اختيار موظف مبيعات (من) فعّال.');
        }
        if (! User::salesEmployees()->where('is_active', true)->whereKey($toId)->exists()) {
            return back()->withInput()->with('error', 'يرجى اختيار موظف مبيعات (إلى) فعّال.');
        }

        $fromRep = User::salesEmployees()->whereKey($fromId)->first();
        $toRep = User::salesEmployees()->whereKey($toId)->first();

        $summary = DB::transaction(function () use ($fromId, $toId) {
            $moved = [
                'leads_assigned' => 0,
                'leads_created_by' => 0,
                'leads_won_confirmed_by' => 0,
                'activities' => 0,
                'audit_logs' => 0,
                'kpi_targets_moved' => 0,
                'kpi_targets_conflicts' => 0,
            ];

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

            if (DB::getSchemaBuilder()->hasTable('sales_kpi_targets')) {
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

            SalesAuditService::log(
                'sales_data_transferred',
                null,
                ['from_user_id' => $fromId],
                ['to_user_id' => $toId],
                'تحويل بيانات المبيعات من موظف #' . $fromId . ' إلى موظف #' . $toId
            );

            return $moved;
        });

        if ($fromRep && $toRep) {
            try {
                app(SalesNotificationService::class)->notifyDataTransferred($fromRep, $toRep, $summary);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()
            ->route('admin.sales.transfer.index', ['from_user_id' => $fromId])
            ->with('success', 'تم تحويل البيانات بنجاح.')
            ->with('transfer_summary', $summary);
    }
}

