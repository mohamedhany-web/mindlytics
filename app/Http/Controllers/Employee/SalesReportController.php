<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SalesActivity;
use App\Models\SalesLead;
use App\Services\SalesKpiService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
    /** @see \App\Http\Controllers\Admin\SalesAuditController */
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
        $request->mergeIfMissing([
            'date_from' => now()->startOfMonth()->toDateString(),
            'date_to' => now()->toDateString(),
        ]);

        $dateFrom = (string) $request->get('date_from');
        $dateTo = (string) $request->get('date_to');

        $validated = null;
        $error = null;
        $start = null;
        $end = null;
        $periodReport = null;
        $leadsSample = collect();
        $activitiesSample = collect();
        $auditSample = collect();
        $counts = [];

        try {
            $validated = $request->validate([
                'date_from' => ['required', 'date'],
                'date_to' => ['required', 'date', 'after_or_equal:date_from'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $error = $e->validator->errors()->first();
        }

        $user = auth()->user();

        if ($validated) {
            $start = Carbon::parse($validated['date_from'])->startOfDay();
            $end = Carbon::parse($validated['date_to'])->endOfDay();

            $periodReport = $kpi->buildPeriodReport($user, $start, $end);
            $leadsQuery = $this->leadsTouchedInPeriodQuery((int) $user->id, $start, $end);
            $actQuery = $this->activitiesInPeriodQuery((int) $user->id, $start, $end);
            $auditQuery = $this->auditInPeriodQuery($start, $end, (int) $user->id);

            $counts = [
                'leads' => (clone $leadsQuery)->count(),
                'activities' => (clone $actQuery)->count(),
                'audit' => (clone $auditQuery)->count(),
                'leads_created_by_me' => SalesLead::query()
                    ->where('created_by', $user->id)
                    ->whereBetween('created_at', [$start, $end])
                    ->count(),
                'leads_assigned_to_me_created_by_admin' => SalesLead::query()
                    ->where('assigned_to', $user->id)
                    ->where('created_by', '!=', $user->id)
                    ->whereBetween('created_at', [$start, $end])
                    ->count(),
            ];

            $leadsSample = (clone $leadsQuery)->limit(30)->get();
            $activitiesSample = (clone $actQuery)->limit(40)->get();
            $auditSample = (clone $auditQuery)->limit(25)->get();
        }

        return view('employee.sales.reports.index', compact(
            'dateFrom',
            'dateTo',
            'error',
            'start',
            'end',
            'periodReport',
            'leadsSample',
            'activitiesSample',
            'auditSample',
            'counts'
        ));
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
    private function auditInPeriodQuery(Carbon $start, Carbon $end, int $userId)
    {
        return ActivityLog::query()
            ->whereIn('action', self::SALES_AUDIT_ACTIONS)
            ->whereBetween('created_at', [$start, $end])
            ->where('user_id', $userId)
            ->orderByDesc('created_at');
    }
}

