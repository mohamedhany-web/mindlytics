<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SalesActivity;
use App\Models\SalesLead;
use App\Models\User;
use App\Services\SalesEmployeeDailyLeadsExcelExportService;
use App\Services\SalesEmployeePeriodReportService;
use App\Services\SalesEmployeeReportPdfService;
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

    public function index(Request $request, SalesKpiService $kpi, SalesEmployeePeriodReportService $employeeReportService)
    {
        $salesReps = User::salesEmployees()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        $request->mergeIfMissing([
            'date_from' => now()->startOfMonth()->toDateString(),
            'date_to' => now()->toDateString(),
            'lead_scope' => 'touched',
        ]);

        $dateFrom = (string) $request->get('date_from');
        $dateTo = (string) $request->get('date_to');
        $userId = $request->get('user_id');
        $leadScope = (string) $request->get('lead_scope', 'touched');

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
        $employeeReport = null;

        try {
            $validated = $request->validate([
                'date_from' => ['required', 'date'],
                'date_to' => ['required', 'date', 'after_or_equal:date_from'],
                'user_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
                'lead_scope' => ['nullable', 'string', Rule::in(['touched', 'new', 'transferred_from_admin'])],
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
                    $leadsQuery = $this->leadsScopeQuery((int) $selectedRep->id, $start, $end, (string) ($validated['lead_scope'] ?? 'touched'));
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
                        'leads_transferred_from_admin' => SalesLead::query()
                            ->where('assigned_to', $selectedRep->id)
                            ->where(function ($q) use ($selectedRep) {
                                $q->whereNull('created_by')->orWhere('created_by', '!=', $selectedRep->id);
                            })
                            ->whereBetween('created_at', [$start, $end])
                            ->count(),
                    ];

                    $leadsSample = (clone $leadsQuery)->limit(30)->get();
                    $activitiesSample = (clone $actQuery)->limit(40)->get();
                    $auditSample = (clone $auditQuery)->limit(25)->get();

                    $employeeReport = $employeeReportService->build(
                        $selectedRep,
                        $start,
                        $end,
                        (string) ($validated['lead_scope'] ?? 'touched')
                    );
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
            'leadScope',
            'error',
            'start',
            'end',
            'selectedRep',
            'periodReport',
            'repSummaries',
            'leadsSample',
            'activitiesSample',
            'auditSample',
            'counts',
            'employeeReport'
        ));
    }

    public function pdfExport(Request $request, SalesEmployeePeriodReportService $employeeReportService, SalesEmployeeReportPdfService $pdf)
    {
        $validated = $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'lead_scope' => ['nullable', 'string', Rule::in(['touched', 'new', 'transferred_from_admin'])],
        ], [
            'user_id.required' => 'اختر موظف مبيعات لتحميل التقرير PDF.',
        ]);

        $rep = User::query()->findOrFail($validated['user_id']);
        if (! $rep->isSalesEmployee()) {
            abort(422, 'المستخدم ليس موظف مبيعات.');
        }

        $start = Carbon::parse($validated['date_from'])->startOfDay();
        $end = Carbon::parse($validated['date_to'])->endOfDay();

        $report = $employeeReportService->build(
            $rep,
            $start,
            $end,
            (string) ($validated['lead_scope'] ?? 'touched')
        );

        return $pdf->download($report);
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
     * تقرير يومي موجّه للإدارة: ملخص يومي + بيانات Leads كاملة حسب فلتر.
     */
    public function dailyExport(Request $request, SalesEmployeeDailyLeadsExcelExportService $excel): StreamedResponse
    {
        $validated = $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'lead_scope' => ['nullable', 'string', Rule::in(['touched', 'new', 'transferred_from_admin'])],
        ], [
            'user_id.required' => 'اختر موظف مبيعات أولاً لاستخراج التقرير اليومي.',
            'user_id.exists' => 'الموظف المحدد غير موجود.',
            'date_from.required' => 'حدد تاريخ البداية.',
            'date_to.required' => 'حدد تاريخ النهاية.',
            'date_to.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية.',
        ]);

        $start = Carbon::parse($validated['date_from'])->startOfDay();
        $end = Carbon::parse($validated['date_to'])->endOfDay();

        $rep = User::query()->findOrFail($validated['user_id']);
        if (! $rep->isSalesEmployee()) {
            abort(422, 'المستخدم ليس موظف مبيعات.');
        }

        $scope = (string) ($validated['lead_scope'] ?? 'touched');
        $scopeLabel = match ($scope) {
            'new' => 'Leads مسجلة جديداً بواسطة الموظف',
            'transferred_from_admin' => 'Leads محوّلة من الإدارة (مسندة للموظف)',
            default => 'كل Leads ذات صلة بالفترة (Touched)',
        };

        $leads = $this->leadsScopeQuery((int) $rep->id, $start, $end, $scope)
            ->with(['assignee:id,name', 'creator:id,name'])
            ->get();

        $activities = $this->activitiesInPeriodQuery((int) $rep->id, $start, $end)
            ->with(['lead:id,name,assigned_to', 'user:id,name'])
            ->get();

        $dailyRows = [];
        $days = Carbon::parse($start)->startOfDay()->daysUntil(Carbon::parse($end)->endOfDay())->toArray();
        foreach ($days as $d) {
            $dayStart = $d->copy()->startOfDay();
            $dayEnd = $d->copy()->endOfDay();

            $leadsCount = $leads->filter(fn ($l) => $l->created_at && $l->created_at->betweenIncluded($dayStart, $dayEnd))->count();
            $actCount = $activities->filter(fn ($a) => $a->created_at && $a->created_at->betweenIncluded($dayStart, $dayEnd))->count();

            $createdByRep = SalesLead::query()
                ->where('created_by', $rep->id)
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->count();

            $transferred = SalesLead::query()
                ->where('assigned_to', $rep->id)
                ->where(function ($q) use ($rep) {
                    $q->whereNull('created_by')->orWhere('created_by', '!=', $rep->id);
                })
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->count();

            $dailyRows[] = [
                'date' => $d->format('Y-m-d'),
                'leads' => $leadsCount,
                'activities' => $actCount,
                'leads_created_by_rep' => $createdByRep,
                'leads_transferred_from_admin' => $transferred,
            ];
        }

        $exportedBy = 'تصدير من الإدارة — '.(auth()->user()->name ?? '').' — '.now()->format('Y-m-d H:i');
        $payload = [
            'rep_name' => (string) ($rep->name ?? ''),
            'date_from' => $start->format('Y-m-d'),
            'date_to' => $end->format('Y-m-d'),
            'lead_scope_label' => $scopeLabel,
            'context' => $exportedBy,
            'daily_rows' => $dailyRows,
            'leads' => $leads,
            'activities' => $activities,
        ];

        $spreadsheet = $excel->buildSpreadsheet($payload);
        $filename = 'تقرير-يومي-مبيعات-'.now()->format('Y-m-d').'-موظف-'.$rep->id.'.xlsx';

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
     * فلترة Leads لتقارير أعمال الموظف.
     *
     * - touched: أي Lead “ذو صلة” بالفترة (إنشاء/تحديث/إغلاق/نشاط)
     * - new: Leads أنشأها الموظف داخل الفترة
     * - transferred_from_admin: Leads مسندة للموظف ولكن لم ينشئها هو (محولة/قادمة من الإدارة أو مصدر آخر)
     *
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\SalesLead>
     */
    private function leadsScopeQuery(int $userId, Carbon $start, Carbon $end, string $scope)
    {
        return match ($scope) {
            'new' => SalesLead::query()
                ->forAssignee($userId)
                ->where('created_by', $userId)
                ->whereBetween('created_at', [$start, $end])
                ->orderByDesc('created_at'),

            'transferred_from_admin' => SalesLead::query()
                ->forAssignee($userId)
                ->where(function ($q) use ($userId) {
                    $q->whereNull('created_by')->orWhere('created_by', '!=', $userId);
                })
                ->whereBetween('created_at', [$start, $end])
                ->orderByDesc('created_at'),

            default => $this->leadsTouchedInPeriodQuery($userId, $start, $end),
        };
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
