<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SalesActivity;
use App\Models\SalesLead;
use App\Models\User;
use App\Services\SalesFullReportExcelExportService;
use App\Services\SalesKpiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalesReportController extends Controller
{
    /** @see SalesAuditController */
    private const SALES_AUDIT_ACTIONS = [
        'sales_lead_created',
        'sales_lead_viewed',
        'sales_lead_updated',
        'sales_lead_deleted',
        'sales_activity_created',
        'sales_lead_created_admin',
        'sales_lead_viewed_admin',
        'sales_lead_updated_admin',
        'sales_lead_deleted_admin',
        'sales_lead_reassigned',
        'sales_activity_created_admin',
    ];

    public function index(Request $request, SalesKpiService $kpi)
    {
        $salesReps = User::salesEmployees()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        $request->mergeIfMissing([
            'date_from' => now()->startOfMonth()->toDateString(),
            'date_to' => now()->toDateString(),
        ]);

        $dateFrom = (string) $request->get('date_from');
        $dateTo = (string) $request->get('date_to');
        $userId = $request->get('user_id');

        $validated = null;
        $error = null;
        $start = null;
        $end = null;
        $selectedRep = null;
        $periodReport = null;
        $repSummaries = collect();
        $leadsSample = collect();
        $activitiesSample = collect();
        $auditSample = collect();
        $counts = [];

        try {
            $validated = $request->validate([
                'date_from' => ['required', 'date'],
                'date_to' => ['required', 'date', 'after_or_equal:date_from'],
                'user_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $error = $e->validator->errors()->first();
        }

        if ($validated) {
            $start = Carbon::parse($validated['date_from'])->startOfDay();
            $end = Carbon::parse($validated['date_to'])->endOfDay();

            if (! empty($validated['user_id'])) {
                $selectedRep = User::query()->find($validated['user_id']);
                if (! $selectedRep || ! $selectedRep->isSalesEmployee()) {
                    $error = 'المستخدم المحدد ليس موظف مبيعات.';
                } else {
                    $periodReport = $kpi->buildPeriodReport($selectedRep, $start, $end);
                    $leadsQuery = $this->leadsTouchedInPeriodQuery((int) $selectedRep->id, $start, $end);
                    $actQuery = $this->activitiesInPeriodQuery((int) $selectedRep->id, $start, $end);
                    $auditQuery = $this->auditInPeriodQuery($start, $end, (int) $selectedRep->id);

                    $counts = [
                        'leads' => (clone $leadsQuery)->count(),
                        'activities' => (clone $actQuery)->count(),
                        'audit' => (clone $auditQuery)->count(),
                        'leads_created_by' => SalesLead::query()
                            ->where('created_by', $selectedRep->id)
                            ->whereBetween('created_at', [$start, $end])
                            ->count(),
                    ];

                    $leadsSample = (clone $leadsQuery)->limit(30)->get();
                    $activitiesSample = (clone $actQuery)->limit(40)->get();
                    $auditSample = (clone $auditQuery)->limit(25)->get();
                }
            } else {
                foreach ($salesReps as $rep) {
                    $repSummaries->push([
                        'user' => $rep,
                        'report' => $kpi->buildPeriodReport($rep, $start, $end),
                    ]);
                }

                $repIds = $salesReps->pluck('id')->all();
                $leadsQuery = $this->leadsTeamTouchedQuery($repIds, $start, $end);
                $actQuery = SalesActivity::query()
                    ->whereIn('user_id', $repIds)
                    ->whereBetween('created_at', [$start, $end])
                    ->with(['lead:id,name', 'user:id,name']);
                $auditQuery = $this->auditInPeriodQuery($start, $end, null);

                $counts = [
                    'leads' => $repIds === [] ? 0 : (clone $leadsQuery)->count(),
                    'activities' => $repIds === [] ? 0 : (clone $actQuery)->count(),
                    'audit' => (clone $auditQuery)->count(),
                ];

                $leadsSample = $repIds === [] ? collect() : (clone $leadsQuery)->limit(25)->get();
                $activitiesSample = $repIds === [] ? collect() : (clone $actQuery)->limit(30)->get();
                $auditSample = (clone $auditQuery)->limit(20)->get();
            }
        }

        return view('admin.sales.reports.index', compact(
            'salesReps',
            'dateFrom',
            'dateTo',
            'userId',
            'error',
            'start',
            'end',
            'selectedRep',
            'periodReport',
            'repSummaries',
            'leadsSample',
            'activitiesSample',
            'auditSample',
            'counts'
        ));
    }

    public function export(Request $request, SalesKpiService $kpi, SalesFullReportExcelExportService $excel): StreamedResponse
    {
        $validated = $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
            'user_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
        ]);

        $start = Carbon::parse($validated['date_from'])->startOfDay();
        $end = Carbon::parse($validated['date_to'])->endOfDay();

        $salesReps = User::salesEmployees()->where('is_active', true)->orderBy('name')->get();
        $repIds = $salesReps->pluck('id')->all();

        $exportedBy = 'تصدير من الإدارة — '.(auth()->user()->name ?? '').' — '.now()->format('Y-m-d H:i');

        if (! empty($validated['user_id'])) {
            $rep = User::query()->findOrFail($validated['user_id']);
            if (! $rep->isSalesEmployee()) {
                abort(422, 'المستخدم ليس موظف مبيعات.');
            }

            $periodReport = $kpi->buildPeriodReport($rep, $start, $end);
            $leads = $this->leadsTouchedInPeriodQuery((int) $rep->id, $start, $end)->with(['assignee:id,name', 'creator:id,name'])->get();
            $activities = $this->activitiesInPeriodQuery((int) $rep->id, $start, $end)->get();
            $audit = $this->auditInPeriodQuery($start, $end, (int) $rep->id)->with('user:id,name')->get();
            $createdByCount = SalesLead::query()
                ->where('created_by', $rep->id)
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $payload = [
                'mode' => 'single',
                'rep' => $rep,
                'start' => $start,
                'end' => $end,
                'period_report' => $periodReport,
                'rep_summaries' => [],
                'leads' => $leads,
                'activities' => $activities,
                'audit_logs' => $audit,
                'created_by_leads_count' => $createdByCount,
                'context' => $exportedBy,
            ];
        } else {
            $summaries = [];
            foreach ($salesReps as $r) {
                $summaries[] = [
                    'user' => $r,
                    'report' => $kpi->buildPeriodReport($r, $start, $end),
                ];
            }

            $leads = $repIds === []
                ? collect()
                : $this->leadsTeamTouchedQuery($repIds, $start, $end)->with(['assignee:id,name', 'creator:id,name'])->get();

            $activities = $repIds === []
                ? collect()
                : SalesActivity::query()
                    ->whereIn('user_id', $repIds)
                    ->whereBetween('created_at', [$start, $end])
                    ->with(['lead:id,name', 'user:id,name'])
                    ->orderByDesc('created_at')
                    ->get();

            $audit = $this->auditInPeriodQuery($start, $end, null)->with('user:id,name')->get();

            $payload = [
                'mode' => 'all',
                'rep' => null,
                'start' => $start,
                'end' => $end,
                'period_report' => null,
                'rep_summaries' => $summaries,
                'leads' => $leads,
                'activities' => $activities,
                'audit_logs' => $audit,
                'created_by_leads_count' => null,
                'context' => $exportedBy,
            ];
        }

        $spreadsheet = $excel->buildSpreadsheet($payload);
        $suffix = ($payload['mode'] === 'single' && $payload['rep'])
            ? '-موظف-'.$payload['rep']->id
            : '-الفريق';
        $filename = 'تقرير-مبيعات-شامل-'.now()->format('Y-m-d').$suffix.'.xlsx';

        return response()->streamDownload(function () use ($excel, $spreadsheet) {
            $excel->writeToOutput($spreadsheet);
            $spreadsheet->disconnectWorksheets();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\SalesLead>
     */
    private function leadsTouchedInPeriodQuery(int $userId, Carbon $start, Carbon $end)
    {
        return SalesLead::query()
            ->forAssignee($userId)
            ->where(function ($q) use ($start, $end, $userId) {
                $q->whereBetween('created_at', [$start, $end])
                    ->orWhereBetween('closed_at', [$start, $end])
                    ->orWhereBetween('updated_at', [$start, $end])
                    ->orWhereHas('activities', function ($aq) use ($start, $end, $userId) {
                        $aq->where('user_id', $userId)->whereBetween('created_at', [$start, $end]);
                    });
            })
            ->orderByDesc('updated_at');
    }

    /**
     * @param  list<int>  $repIds
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\SalesLead>
     */
    private function leadsTeamTouchedQuery(array $repIds, Carbon $start, Carbon $end)
    {
        return SalesLead::query()
            ->whereIn('assigned_to', $repIds)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end])
                    ->orWhereBetween('closed_at', [$start, $end])
                    ->orWhereBetween('updated_at', [$start, $end])
                    ->orWhereHas('activities', fn ($aq) => $aq->whereBetween('created_at', [$start, $end]));
            })
            ->orderByDesc('updated_at');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\SalesActivity>
     */
    private function activitiesInPeriodQuery(int $userId, Carbon $start, Carbon $end)
    {
        return SalesActivity::query()
            ->where('user_id', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->with(['lead:id,name,assigned_to'])
            ->orderByDesc('created_at');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\ActivityLog>
     */
    private function auditInPeriodQuery(Carbon $start, Carbon $end, ?int $userId)
    {
        $q = ActivityLog::query()
            ->whereIn('action', self::SALES_AUDIT_ACTIONS)
            ->whereBetween('created_at', [$start, $end]);

        if ($userId !== null) {
            $q->where('user_id', $userId);
        }

        return $q->orderByDesc('created_at');
    }
}
