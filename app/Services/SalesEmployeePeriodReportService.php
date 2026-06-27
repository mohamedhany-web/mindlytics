<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\SalesActivity;
use App\Models\SalesDailyReport;
use App\Models\SalesLead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SalesEmployeePeriodReportService
{
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

  public function __construct(
    private SalesKpiService $kpi,
    private SalesDailyReportService $dailyReports,
  ) {}

  /**
   * @return array<string, mixed>
   */
  public function build(User $rep, Carbon $start, Carbon $end, string $leadScope = 'touched'): array
  {
    $start = $start->copy()->startOfDay();
    $end = $end->copy()->endOfDay();

    $effectiveStart = $start->copy();
    if ($rep->created_at && $rep->created_at->gt($effectiveStart)) {
      $effectiveStart = $rep->created_at->copy()->startOfDay();
    }

    $periodReport = $this->kpi->buildPeriodReport($rep, $start, $end);
    $leads = $this->leadsScopeQuery((int) $rep->id, $start, $end, $leadScope)
      ->with(['assignee:id,name', 'creator:id,name'])
      ->get();

    $activities = SalesActivity::query()
      ->where('user_id', $rep->id)
      ->whereBetween('created_at', [$start, $end])
      ->with(['lead:id,name,phone,stage'])
      ->orderByDesc('created_at')
      ->get();

    $loginDays = ActivityLog::query()
      ->where('user_id', $rep->id)
      ->where('action', 'login')
      ->whereBetween('created_at', [$start, $end])
      ->selectRaw('DATE(created_at) as d')
      ->distinct()
      ->pluck('d')
      ->map(fn ($d) => (string) $d)
      ->flip();

    $activityDays = $activities
      ->groupBy(fn ($a) => $a->created_at?->format('Y-m-d') ?? '')
      ->filter(fn ($_, $d) => $d !== '');

    $dailyReportRows = SalesDailyReport::forUser($rep->id)
      ->whereBetween('report_date', [$start->toDateString(), $end->toDateString()])
      ->get()
      ->keyBy(fn ($r) => $r->report_date->format('Y-m-d'));

    $leadsCreatedByDay = SalesLead::query()
      ->where('created_by', $rep->id)
      ->whereBetween('created_at', [$start, $end])
      ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
      ->groupBy('d')
      ->pluck('c', 'd');

    $leadsFromAdminByDay = SalesLead::query()
      ->where('assigned_to', $rep->id)
      ->where(function ($q) use ($rep) {
        $q->whereNull('created_by')->orWhere('created_by', '!=', $rep->id);
      })
      ->whereBetween('created_at', [$start, $end])
      ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
      ->groupBy('d')
      ->pluck('c', 'd');

    $dailyRows = [];
    $absentWorkDays = [];
    $inactiveWorkDays = [];
    $workDaysCount = 0;
    $loginWorkDays = 0;
    $activeWorkDays = 0;
    $reportsSubmitted = 0;
    $reportsMissing = 0;

    $cursor = $start->copy()->startOfDay();
    while ($cursor->lte($end)) {
      $dateKey = $cursor->format('Y-m-d');
      $isWorkDay = $this->dailyReports->isWorkDay($cursor, $rep);
      $loggedIn = $loginDays->has($dateKey);
      $dayActivities = $activityDays->get($dateKey, collect());
      $hasCrm = $dayActivities->isNotEmpty();

      $typeCounts = [
        'call' => $dayActivities->where('type', 'call')->count(),
        'meeting' => $dayActivities->where('type', 'meeting')->count(),
        'follow_up' => $dayActivities->where('type', 'follow_up')->count(),
        'whatsapp' => $dayActivities->where('type', 'whatsapp')->count(),
        'other' => $dayActivities->whereNotIn('type', ['call', 'meeting', 'follow_up', 'whatsapp'])->count(),
      ];

      $leadsCreated = (int) ($leadsCreatedByDay[$dateKey] ?? 0);
      $leadsFromAdmin = (int) ($leadsFromAdminByDay[$dateKey] ?? 0);

      $dailyReport = $dailyReportRows->get($dateKey);
      $reportStatus = 'off';
      if ($isWorkDay) {
        if ($dailyReport?->status === SalesDailyReport::STATUS_SUBMITTED) {
          $reportStatus = 'submitted';
          $reportsSubmitted++;
        } elseif ($dailyReport) {
          $reportStatus = 'draft';
        } else {
          $reportStatus = 'missing';
          if ($cursor->lt(now()->startOfDay())) {
            $reportsMissing++;
          }
        }
      }

      $dayStatus = $this->dayStatusLabel($isWorkDay, $loggedIn, $hasCrm, $reportStatus);

      if ($isWorkDay) {
        $workDaysCount++;
        if ($loggedIn) {
          $loginWorkDays++;
        } else {
          $absentWorkDays[] = $dateKey;
        }
        if ($hasCrm) {
          $activeWorkDays++;
        } elseif ($isWorkDay && $loggedIn) {
          $inactiveWorkDays[] = $dateKey;
        }
      }

      $dailyRows[] = [
        'date' => $dateKey,
        'day_name' => $cursor->locale('ar')->isoFormat('dddd'),
        'is_work_day' => $isWorkDay,
        'logged_in' => $loggedIn,
        'has_crm_activity' => $hasCrm,
        'activities_total' => $dayActivities->count(),
        'calls' => $typeCounts['call'],
        'meetings' => $typeCounts['meeting'],
        'followups' => $typeCounts['follow_up'],
        'whatsapp' => $typeCounts['whatsapp'],
        'other_activities' => $typeCounts['other'],
        'leads_created' => $leadsCreated,
        'leads_from_admin' => $leadsFromAdmin,
        'daily_report_status' => $reportStatus,
        'daily_report_label' => $this->dailyReportLabel($reportStatus),
        'status_label' => $dayStatus['label'],
        'status_tone' => $dayStatus['tone'],
      ];

      $cursor->addDay();
    }

    $metrics = $periodReport['metrics'] ?? [];

    return [
      'rep' => $rep,
      'start' => $start,
      'end' => $end,
      'effective_start' => $effectiveStart,
      'lead_scope' => $leadScope,
      'lead_scope_label' => $this->leadScopeLabel($leadScope),
      'period_report' => $periodReport,
      'summary' => [
        'period_days' => max(1, (int) $start->diffInDays($end) + 1),
        'work_days' => $workDaysCount,
        'days_with_login' => $loginWorkDays,
        'days_without_login' => count($absentWorkDays),
        'days_with_crm' => $activeWorkDays,
        'days_without_crm' => max(0, $workDaysCount - $activeWorkDays),
        'daily_reports_submitted' => $reportsSubmitted,
        'daily_reports_missing' => $reportsMissing,
        'leads_in_scope' => $leads->count(),
        'leads_created_by_rep' => (int) ($metrics['new_leads'] ?? SalesLead::query()
          ->where('created_by', $rep->id)
          ->whereBetween('created_at', [$start, $end])
          ->count()),
        'leads_from_admin' => SalesLead::query()
          ->where('assigned_to', $rep->id)
          ->where(function ($q) use ($rep) {
            $q->whereNull('created_by')->orWhere('created_by', '!=', $rep->id);
          })
          ->whereBetween('created_at', [$start, $end])
          ->count(),
        'total_activities' => $activities->count(),
        'calls' => (int) ($metrics['calls'] ?? 0),
        'meetings' => (int) ($metrics['meetings'] ?? 0),
        'followups' => (int) ($metrics['followups'] ?? 0),
        'won_deals' => (int) ($metrics['won_closed'] ?? 0),
        'revenue' => (float) ($metrics['revenue_closed'] ?? 0),
        'composite_score' => $periodReport['composite'] ?? null,
        'joined_at' => $rep->created_at,
      ],
      'daily_rows' => $dailyRows,
      'absent_work_days' => $absentWorkDays,
      'inactive_work_days' => $inactiveWorkDays,
      'leads' => $leads,
      'activities' => $activities,
      'activity_breakdown' => $this->activityBreakdown($activities),
      'generated_at' => now(),
      'exported_by' => auth()->user()?->name,
    ];
  }

  /**
   * @return array{label: string, tone: string}
   */
  private function dayStatusLabel(bool $isWorkDay, bool $loggedIn, bool $hasCrm, string $reportStatus): array
  {
    if (! $isWorkDay) {
      return ['label' => 'عطلة / لا يُحسب', 'tone' => 'slate'];
    }
    if (! $loggedIn && ! $hasCrm) {
      return ['label' => 'لم يدخل النظام', 'tone' => 'rose'];
    }
    if ($loggedIn && ! $hasCrm) {
      return ['label' => 'دخل بدون نشاط مبيعات', 'tone' => 'amber'];
    }

    return ['label' => 'يوم نشط', 'tone' => 'emerald'];
  }

  private function dailyReportLabel(string $status): string
  {
    return match ($status) {
      'submitted' => 'مُسلَّم',
      'draft' => 'مسودة',
      'missing' => 'غير مُسلَّم',
      default => '—',
    };
  }

  private function leadScopeLabel(string $scope): string
  {
    return match ($scope) {
      'new' => 'Leads سجّلها الموظف بنفسه',
      'transferred_from_admin' => 'Leads محوّلة من الإدارة',
      default => 'كل Leads ذات صلة بالفترة',
    };
  }

  /**
   * @param  Collection<int, SalesActivity>  $activities
   * @return list<array{type: string, label: string, count: int}>
   */
  private function activityBreakdown(Collection $activities): array
  {
    $counts = $activities->groupBy('type')->map->count();

    return $counts->map(function ($count, $type) {
      return [
        'type' => (string) $type,
        'label' => SalesActivity::typeLabel((string) $type),
        'count' => (int) $count,
      ];
    })->sortByDesc('count')->values()->all();
  }

  /**
   * @return \Illuminate\Database\Eloquent\Builder<SalesLead>
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

      default => SalesLead::query()
        ->forAssignee($userId)
        ->where(function ($q) use ($start, $end, $userId) {
          $q->whereBetween('created_at', [$start, $end])
            ->orWhereBetween('closed_at', [$start, $end])
            ->orWhereBetween('updated_at', [$start, $end])
            ->orWhereHas('activities', function ($aq) use ($start, $end, $userId) {
              $aq->where('user_id', $userId)->whereBetween('created_at', [$start, $end]);
            });
        })
        ->orderByDesc('updated_at'),
    };
  }
}
