<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\OfflineCourseBooking;
use App\Models\OfflineCourseEnrollment;
use App\Models\Order;
use App\Models\SalesActivity;
use App\Models\SalesLead;
use App\Models\StudentCourseEnrollment;
use App\Models\Transaction;
use App\Models\User;
use App\Services\SalesInsightsAnalyticsService;
use App\Services\SalesKpiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class SalesInsightsController extends Controller
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
        'sales_lead_won_confirmed',
    ];

    public function index(Request $request, SalesKpiService $kpi, SalesInsightsAnalyticsService $analytics)
    {
        $salesReps = User::salesEmployees()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        if ($salesReps->isEmpty()) {
            return view('admin.sales.insights.empty', [
                'salesReps' => $salesReps,
            ]);
        }

        $request->mergeIfMissing([
            'period' => 'week',
            'user_id' => (int) $salesReps->first()->id,
        ]);

        $validated = $request->validate([
            'period' => ['required', Rule::in(['day', 'week', 'month', 'custom'])],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'user_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
        ]);

        $repId = (int) ($validated['user_id'] ?? $salesReps->first()->id);
        $rep = User::query()->findOrFail($repId);
        abort_unless($rep->isSalesEmployee(), 422, 'المستخدم ليس موظف مبيعات.');

        [$start, $end] = $this->rangeFor($validated);

        $periodReport = $kpi->buildPeriodReport($rep, $start, $end);

        $leadsQuery = $this->leadsTouchedInPeriodQuery($rep->id, $start, $end);
        $actQuery = $this->activitiesInPeriodQuery($rep->id, $start, $end);
        $auditQuery = $this->auditInPeriodQuery($start, $end, $rep->id);

        $counts = [
            'leads' => (clone $leadsQuery)->count(),
            'activities' => (clone $actQuery)->count(),
            'audit' => (clone $auditQuery)->count(),
            'leads_created_by' => SalesLead::query()
                ->where('created_by', $rep->id)
                ->whereBetween('created_at', [$start, $end])
                ->count(),
            'wins_confirmed' => SalesLead::query()
                ->where('assigned_to', $rep->id)
                ->whereNotNull('won_confirmed_at')
                ->whereBetween('won_confirmed_at', [$start, $end])
                ->count(),
        ];

        $leadsSample = (clone $leadsQuery)->limit(35)->get();
        $activitiesSample = (clone $actQuery)->limit(60)->get();
        $auditSample = (clone $auditQuery)->limit(40)->get();

        $commission = $this->commissionSummary($rep->id, $start, $end);

        $courses = $this->coursesSummaryFromLeadEmails($rep->id, $start, $end);

        $decision = $this->decisionFromReport($periodReport, $counts, $commission, $courses);

        $teamDashboard = $analytics->buildTeamDashboard(6);
        $repCharts = $analytics->buildRepCharts($rep->id, $start, $end, $periodReport);

        $periodLabel = match ($validated['period']) {
            'day' => 'اليوم',
            'week' => 'الأسبوع الحالي',
            'month' => 'هذا الشهر',
            default => 'فترة مخصصة',
        };

        return view('admin.sales.insights.index', compact(
            'salesReps',
            'rep',
            'start',
            'end',
            'periodReport',
            'counts',
            'leadsSample',
            'activitiesSample',
            'auditSample',
            'commission',
            'courses',
            'decision',
            'periodLabel',
            'teamDashboard',
            'repCharts'
        ));
    }

    /**
     * @param  array<string,mixed>  $validated
     * @return array{0: Carbon, 1: Carbon}
     */
    private function rangeFor(array $validated): array
    {
        $period = (string) $validated['period'];

        if ($period === 'custom') {
            $start = Carbon::parse((string) ($validated['date_from'] ?? now()->toDateString()))->startOfDay();
            $end = Carbon::parse((string) ($validated['date_to'] ?? now()->toDateString()))->endOfDay();

            return [$start, $end];
        }

        return match ($period) {
            'day' => [now()->startOfDay(), now()->endOfDay()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            default => [now()->startOfWeek(), now()->endOfWeek()],
        };
    }

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
            ->with(['assignee:id,name', 'creator:id,name'])
            ->orderByDesc('updated_at');
    }

    private function activitiesInPeriodQuery(int $userId, Carbon $start, Carbon $end)
    {
        return SalesActivity::query()
            ->where('user_id', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->with(['lead:id,name,stage', 'user:id,name'])
            ->orderByDesc('created_at');
    }

    private function auditInPeriodQuery(Carbon $start, Carbon $end, int $userId)
    {
        return ActivityLog::query()
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('action', self::SALES_AUDIT_ACTIONS)
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->orWhereJsonContains('new_values->assigned_to', $userId)
                    ->orWhereJsonContains('old_values->assigned_to', $userId);
            })
            ->with('user:id,name')
            ->orderByDesc('id');
    }

    /**
     * @return array{txn_sum: float, confirmed_wins: int, expected_confirmed: float, commission_from_leads: float, mismatch: bool}
     */
    private function commissionSummary(int $userId, Carbon $start, Carbon $end): array
    {
        $confirmed = SalesLead::query()
            ->where('assigned_to', $userId)
            ->whereNotNull('won_confirmed_at')
            ->whereBetween('won_confirmed_at', [$start, $end]);

        $confirmedWins = (int) (clone $confirmed)->count();
        $expected = (float) (clone $confirmed)->sum('expected_value');
        $commissionFromLeads = (float) (clone $confirmed)->sum('commission_amount');

        $txnSum = (float) Transaction::query()
            ->where('user_id', $userId)
            ->where('type', 'credit')
            ->where('category', 'commission')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        return [
            'txn_sum' => $txnSum,
            'confirmed_wins' => $confirmedWins,
            'expected_confirmed' => $expected,
            'commission_from_leads' => $commissionFromLeads,
            'mismatch' => abs($commissionFromLeads - $txnSum) > 0.02,
        ];
    }

    /**
     * يربط Leads بالمستخدمين عبر الإيميل، ثم يلخص كورسات/حجوزات/تسجيلات.
     *
     * @return array<string,mixed>
     */
    private function coursesSummaryFromLeadEmails(int $assigneeId, Carbon $start, Carbon $end): array
    {
        $emails = $this->leadsTouchedInPeriodQuery($assigneeId, $start, $end)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->pluck('email')
            ->map(fn ($e) => strtolower(trim((string) $e)))
            ->unique()
            ->values()
            ->all();

        if ($emails === []) {
            return [
                'matched_users' => 0,
                'orders' => ['count' => 0, 'rows' => collect()],
                'online_enrollments' => ['count' => 0, 'rows' => collect()],
                'offline_bookings' => ['count' => 0, 'rows' => collect()],
                'offline_enrollments' => ['count' => 0, 'rows' => collect()],
                'note' => 'لا يمكن ربط الكورسات بدون بريد إلكتروني صحيح داخل الـ lead.',
            ];
        }

        $users = User::query()->whereIn('email', $emails)->get(['id', 'name', 'email']);
        $userIds = $users->pluck('id')->all();

        $orders = Order::query()
            ->whereIn('user_id', $userIds)
            ->whereBetween('created_at', [$start, $end])
            ->with(['user:id,name,email', 'course:id,title', 'learningPath:id,name'])
            ->latest('id');

        $onlineEnroll = StudentCourseEnrollment::query()
            ->whereIn('user_id', $userIds)
            ->whereBetween('enrolled_at', [$start, $end])
            ->with(['user:id,name,email', 'course:id,title'])
            ->latest('id');

        $offlineBookings = OfflineCourseBooking::query()
            ->whereIn('user_id', $userIds)
            ->whereBetween('created_at', [$start, $end])
            ->with(['user:id,name,email', 'course:id,title', 'requestedGroup:id,name', 'assignedGroup:id,name'])
            ->latest('id');

        $offlineEnrollments = OfflineCourseEnrollment::query()
            ->whereIn('user_id', $userIds)
            ->whereBetween('enrolled_at', [$start, $end])
            ->with(['student:id,name,email', 'course:id,title', 'group:id,name'])
            ->latest('id');

        return [
            'matched_users' => $users->count(),
            'users' => $users,
            'orders' => ['count' => (clone $orders)->count(), 'rows' => (clone $orders)->limit(10)->get()],
            'online_enrollments' => ['count' => (clone $onlineEnroll)->count(), 'rows' => (clone $onlineEnroll)->limit(10)->get()],
            'offline_bookings' => ['count' => (clone $offlineBookings)->count(), 'rows' => (clone $offlineBookings)->limit(10)->get()],
            'offline_enrollments' => ['count' => (clone $offlineEnrollments)->count(), 'rows' => (clone $offlineEnrollments)->limit(10)->get()],
            'note' => null,
        ];
    }

    /**
     * @param  array<string,mixed>  $periodReport
     * @param  array<string,mixed>  $counts
     * @param  array<string,mixed>  $commission
     * @param  array<string,mixed>  $courses
     * @return array<string,mixed>
     */
    private function decisionFromReport(array $periodReport, array $counts, array $commission, array $courses): array
    {
        $composite = (float) ($periodReport['composite'] ?? 0);
        $pillars = (array) ($periodReport['pillars'] ?? []);

        $status = match (true) {
            $composite >= 80 => 'excellent',
            $composite >= 65 => 'good',
            $composite >= 45 => 'warning',
            default => 'critical',
        };

        $statusLabel = match ($status) {
            'excellent' => 'ممتاز — ثابت على المسار',
            'good' => 'جيد — قابل للتحسين',
            'warning' => 'تحذير — يحتاج تدخل إداري',
            default => 'حرج — خطر على SLA والأهداف',
        };

        $recs = [];
        $overdue = (int) data_get($periodReport, 'metrics.overdue_followups', 0);
        $stale = (int) data_get($periodReport, 'metrics.stale_open_leads', 0);
        if ($overdue > 0) {
            $recs[] = "إلزام بخطة متابعة: يوجد {$overdue} متابعة متأخرة داخل الأنبوب.";
        }
        if ($stale > 0) {
            $recs[] = "تنشيط العملاء الراكدة: يوجد {$stale} عميل مفتوح بلا تواصل كافٍ.";
        }
        if ((int) ($counts['activities'] ?? 0) === 0) {
            $recs[] = 'لا توجد أنشطة CRM مسجّلة في الفترة — يُنصح بمراجعة التزام التسجيل وإيقاف التنبيه إن كان الموظف جديدًا.';
        }
        if (($commission['mismatch'] ?? false) === true) {
            $recs[] = 'مراجعة مالية: فرق بين كوميشن الـ leads وقيود المعاملات.';
        }
        if ((int) ($courses['matched_users'] ?? 0) === 0) {
            $recs[] = 'بيانات الكورسات غير مكتملة: لا يوجد ربط بين leads وحسابات الطلاب (تأكد من البريد داخل الـ lead).';
        }

        // أبرز عمود ضعيف
        $weak = collect($pillars)->sortBy(fn ($p) => (float) ($p['score'] ?? 999))->first();
        $weakLabel = is_array($weak) ? ($weak['label'] ?? null) : null;
        if ($weakLabel) {
            $recs[] = 'أضعف محور: ' . $weakLabel;
        }

        $recs = array_values(array_unique(array_filter($recs)));
        if ($recs === []) {
            $recs[] = 'لا توجد ملاحظات حرجة في هذه الفترة.';
        }

        return [
            'status' => $status,
            'status_label' => $statusLabel,
            'composite' => round($composite, 1),
            'recommendations' => $recs,
            'flags' => Arr::wrap($periodReport['alert_flags'] ?? []),
        ];
    }
}

