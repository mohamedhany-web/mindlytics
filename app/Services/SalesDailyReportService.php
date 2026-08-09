<?php

namespace App\Services;

use App\Models\EmployeeAgreement;
use App\Models\EmployeeSalaryDeduction;
use App\Models\SalesActivity;
use App\Models\SalesDailyReport;
use App\Models\SalesDailyReportContact;
use App\Models\SalesLead;
use App\Models\User;
use App\Support\PenaltyWindow;
use App\Support\SalesDailyReportSettings;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class SalesDailyReportService
{
    /**
     * @return list<string>
     */
    public function requiredFieldKeys(): array
    {
        return [
            'messages_replied',
            'leads_qualified',
            'bookings_from_leads',
            'numbers_worked',
            'followups_done',
            'calls_made',
            'meetings_held',
            'calls_answered',
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<array<string, mixed>>  $contacts
     * @return array{missing: list<string>, contact_errors: list<string>}
     */
    public function validateCompleteness(array $data, array $contacts): array
    {
        $missing = [];
        foreach ($this->requiredFieldKeys() as $key) {
            if (! array_key_exists($key, $data) || $data[$key] === null || $data[$key] === '') {
                $missing[] = $key;
            }
        }

        $contactErrors = [];
        $calls = (int) ($data['calls_made'] ?? 0);
        $meetings = (int) ($data['meetings_held'] ?? 0);

        if ($calls > 0 || $meetings > 0) {
            $callContacts = collect($contacts)->where('interaction_type', SalesDailyReportContact::TYPE_CALL)->count();
            $meetingContacts = collect($contacts)->where('interaction_type', SalesDailyReportContact::TYPE_MEETING)->count();

            if ($callContacts < $calls) {
                $contactErrors[] = "يجب تسجيل {$calls} مكالمة على الأقل مع رقم العميل وحالته ومشاكله (مسجّل {$callContacts}).";
            }
            if ($meetingContacts < $meetings) {
                $contactErrors[] = "يجب تسجيل {$meetings} اجتماع على الأقل مع رقم العميل وحالته ومشاكله (مسجّل {$meetingContacts}).";
            }

            foreach ($contacts as $i => $c) {
                $row = $i + 1;
                if (empty(trim((string) ($c['contact_phone'] ?? '')))) {
                    $contactErrors[] = "صف التواصل #{$row}: رقم الهاتف مطلوب.";
                }
                if (empty(trim((string) ($c['client_status'] ?? '')))) {
                    $contactErrors[] = "صف التواصل #{$row}: حالة العميل مطلوبة.";
                }
                $problems = trim((string) ($c['client_problems'] ?? ''));
                if ($problems === '' || $this->isAutoPlaceholderProblems($problems)) {
                    $contactErrors[] = "صف التواصل #{$row}: مشاكل/احتياجات العميل — يجب كتابتها يدوياً (لا تُحسب تلقائياً).";
                }
            }
        }

        return ['missing' => $missing, 'contact_errors' => $contactErrors];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  list<array<string, mixed>>  $contacts
     */
    public function saveReport(User $user, Carbon $date, array $payload, array $contacts, bool $submit): SalesDailyReport
    {
        $report = SalesDailyReport::firstOrNew([
            'user_id' => $user->id,
            'report_date' => $date->toDateString(),
        ]);

        if ($report->exists && $report->isSubmitted()) {
            throw ValidationException::withMessages([
                'report_date' => 'تم تسليم تقرير هذا اليوم مسبقاً ولا يمكن تعديله.',
            ]);
        }

        $data = $this->normalizeNumericPayload($payload);
        $check = $this->validateCompleteness($data, $contacts);

        if ($submit) {
            if ($check['missing'] !== [] || $check['contact_errors'] !== []) {
                $messages = array_merge(
                    array_map(fn ($k) => $this->fieldLabel($k).' مطلوب.', $check['missing']),
                    $check['contact_errors']
                );
                throw ValidationException::withMessages(['form' => $messages]);
            }
        }

        $report->fill($data);
        $report->missing_fields = array_merge($check['missing'], $check['contact_errors'] !== [] ? ['contacts'] : []);
        $report->activity_notes = $payload['activity_notes'] ?? null;
        $report->productivity_notes = $payload['productivity_notes'] ?? null;

        if ($submit) {
            $report->status = SalesDailyReport::STATUS_SUBMITTED;
            $report->submitted_at = now();
            $report->missing_fields = null;
        } else {
            $report->status = SalesDailyReport::STATUS_DRAFT;
        }

        $report->user_id = $user->id;
        $report->report_date = $date->toDateString();
        $report->save();

        $this->syncContacts($report, $contacts, $user->id);

        if ($submit) {
            app(\App\Services\SalesTeamService::class)->syncMemberReportTeamId($user, $report);
        }

        return $report->fresh(['contacts.lead']);
    }

    /**
     * @param  list<array<string, mixed>>  $contacts
     */
    private function syncContacts(SalesDailyReport $report, array $contacts, int $userId): void
    {
        $report->contacts()->delete();

        foreach ($contacts as $c) {
            $phone = trim((string) ($c['contact_phone'] ?? ''));
            if ($phone === '' && empty($c['sales_lead_id'])) {
                continue;
            }

            $leadId = ! empty($c['sales_lead_id']) ? (int) $c['sales_lead_id'] : null;
            $lead = null;
            if ($leadId) {
                $lead = SalesLead::where('id', $leadId)->where('assigned_to', $userId)->first();
                if (! $lead) {
                    continue;
                }
            }

            if ($phone === '' && ! $lead) {
                continue;
            }

            $report->contacts()->create([
                'sales_lead_id' => $leadId,
                'contact_name' => $c['contact_name'] ?? ($lead?->name),
                'contact_phone' => $phone !== '' ? $phone : (string) ($lead?->phone ?? ''),
                'interaction_type' => in_array($c['interaction_type'] ?? '', [SalesDailyReportContact::TYPE_CALL, SalesDailyReportContact::TYPE_MEETING], true)
                    ? $c['interaction_type']
                    : SalesDailyReportContact::TYPE_CALL,
                'client_status' => (string) ($c['client_status'] ?? ''),
                'client_problems' => (string) ($c['client_problems'] ?? ''),
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, int|null>
     */
    private function normalizeNumericPayload(array $payload): array
    {
        $out = [];
        foreach ($this->requiredFieldKeys() as $key) {
            $val = $payload[$key] ?? null;
            $out[$key] = ($val === null || $val === '') ? null : max(0, (int) $val);
        }

        return $out;
    }

    public function fieldLabel(string $key): string
    {
        return match ($key) {
            'messages_replied' => 'ردود على الرسائل',
            'leads_qualified' => 'عملاء مؤهّلين',
            'bookings_from_leads' => 'حجوزات من Leads',
            'numbers_worked' => 'أرقام تم العمل عليها',
            'followups_done' => 'متابعات منفّذة',
            'calls_made' => 'مكالمات أُجريت',
            'meetings_held' => 'اجتماعات / ميتينج',
            'calls_answered' => 'مكالمات تم الرد عليها',
            default => $key,
        };
    }

    /**
     * هل يُحسب هذا اليوم يوم عمل للموظف (تقرير + خصم + KPI)؟
     */
    public function isWorkDay(Carbon $date, ?User $employee = null): bool
    {
        if ($employee && ! $employee->isEmployedOn($date)) {
            return false;
        }

        if (! SalesDailyReportSettings::all()['work_days_only']) {
            return true;
        }

        if ($employee) {
            return $employee->requiresDailyReportOn($date);
        }

        return ! $date->isWeekend();
    }

    public function todayReportFor(User $user): ?SalesDailyReport
    {
        return SalesDailyReport::forUser($user->id)
            ->whereDate('report_date', today())
            ->with('contacts.lead')
            ->first();
    }

    /**
     * أنشطة الموظف في يوم التقرير — للعرض القابل للتنقّل من لوحة الإدارة.
     *
     * @return \Illuminate\Support\Collection<int, SalesActivity>
     */
    public function activitiesForUserOnDate(User $user, Carbon $date): Collection
    {
        $start = $date->copy()->startOfDay();
        $end = $date->copy()->endOfDay();

        return SalesActivity::query()
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$start, $end])
            ->with(['lead' => fn ($q) => $q->withTrashed()->select('id', 'name', 'phone', 'stage', 'assigned_to')])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * يبني مسودة التقرير من نشاط الموظف (مكالمات، واتساب، متابعات، تغيير مراحل…).
     *
     * @return array{
     *     metrics: array<string, int>,
     *     contacts: list<array<string, mixed>>,
     *     activity_notes: string,
     *     productivity_notes: string,
     *     activity_count: int
     * }
     */
    public function buildFromActivities(User $user, Carbon $date): array
    {
        // نشاط موثّق فقط (مربوط بعميل) — نفس قاعدة SOS/KPI
        $activities = $this->activitiesForUserOnDate($user, $date)
            ->filter(fn (SalesActivity $a) => filled($a->sales_lead_id))
            ->sortBy('created_at')
            ->values();

        $sos = app(SalesDailyResultService::class)->metricsFor(
            (int) $user->id,
            $date->copy()->startOfDay(),
            $date->copy()->endOfDay()
        );

        $followUps = $activities->where('type', 'follow_up');
        $messages = $activities->whereIn('type', ['whatsapp', 'email']);

        $stageChanges = $activities->where('type', 'stage_change');
        $qualified = (int) ($sos['qualified_conversations_daily'] ?? 0);
        $bookings = (int) ($sos['paid_enrollments_daily'] ?? 0)
            + $stageChanges
                ->filter(fn (SalesActivity $a) => in_array($a->meta['to'] ?? null, ['offer_sent', 'proposal'], true))
                ->unique('sales_lead_id')
                ->count();

        $touchTypes = ['call', 'meeting', 'follow_up', 'whatsapp', 'email', 'note', 'other'];
        $touchedLeadIds = $activities
            ->whereIn('type', $touchTypes)
            ->pluck('sales_lead_id')
            ->filter()
            ->unique();

        $numbersWorked = SalesLead::query()
            ->whereIn('id', $touchedLeadIds)
            ->where('assigned_to', $user->id)
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->distinct()
            ->count('phone');

        if ($numbersWorked === 0) {
            $numbersWorked = $touchedLeadIds->count();
        }

        $metrics = [
            'messages_replied' => $messages->count(),
            'leads_qualified' => $qualified,
            'bookings_from_leads' => $bookings,
            'numbers_worked' => $numbersWorked,
            'followups_done' => $followUps->count(),
            // موحّد مع SOS
            'calls_made' => (int) ($sos['call_attempts_daily'] ?? 0),
            'meetings_held' => (int) ($sos['discovery_sessions_daily'] ?? 0),
            'calls_answered' => (int) ($sos['calls_answered_daily'] ?? 0),
        ];

        $contacts = $this->buildContactRowsFromActivities($activities);

        return [
            'metrics' => $metrics,
            'contacts' => $contacts,
            'activity_notes' => $this->buildActivityNotesTimeline($activities),
            'productivity_notes' => $this->buildProductivityNotes($metrics, $activities),
            'activity_count' => $activities->count(),
            'sos_metrics' => $sos,
        ];
    }

    /**
     * عملاء تواصل معهم الموظف في يوم معيّن — للاختيار في التقرير.
     *
     * @return list<array<string, mixed>>
     */
    public function leadsTouchedOnDate(User $user, Carbon $date): array
    {
        $start = $date->copy()->startOfDay();
        $end = $date->copy()->endOfDay();

        $activities = SalesActivity::query()
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$start, $end])
            ->with(['lead' => fn ($q) => $q->withTrashed()])
            ->orderByDesc('created_at')
            ->get();

        return $this->buildContactRowsFromActivities($activities);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, SalesActivity>  $activities
     * @return list<array<string, mixed>>
     */
    private function buildContactRowsFromActivities(Collection $activities): array
    {
        $contactActivityTypes = ['call', 'meeting', 'whatsapp', 'follow_up'];

        $grouped = $activities
            ->whereIn('type', $contactActivityTypes)
            ->filter(fn (SalesActivity $a) => $a->lead && trim((string) $a->lead->phone) !== '')
            ->groupBy('sales_lead_id');

        $contacts = [];
        foreach ($grouped as $leadActivities) {
            /** @var SalesActivity $activity */
            $activity = $leadActivities->sortByDesc('created_at')->first();
            $lead = $activity->lead;
            if (! $lead) {
                continue;
            }

            $contacts[] = [
                'sales_lead_id' => $lead->id,
                'contact_name' => $lead->name,
                'contact_phone' => $lead->phone,
                'interaction_type' => $activity->type === 'meeting'
                    ? SalesDailyReportContact::TYPE_MEETING
                    : SalesDailyReportContact::TYPE_CALL,
                'client_status' => $this->contactStatusText($activity, $lead),
                'client_problems' => $this->contactProblemsText($activity, $lead),
                'activity_type' => $activity->type,
                'activity_label' => SalesActivity::typeLabel($activity->type),
                'auto_filled' => true,
            ];
        }

        usort($contacts, fn ($a, $b) => strcmp($a['contact_name'], $b['contact_name']));

        return $contacts;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function suggestedContactsForReport(User $user, Carbon $date, ?SalesDailyReport $report): array
    {
        $fromActivity = $this->leadsTouchedOnDate($user, $date);

        if ($fromActivity !== []) {
            return $fromActivity;
        }

        if (! $report) {
            return [];
        }

        return $report->contacts->map(fn ($c) => [
            'sales_lead_id' => $c->sales_lead_id,
            'contact_name' => $c->contact_name,
            'contact_phone' => $c->contact_phone,
            'interaction_type' => $c->interaction_type,
            'client_status' => $c->client_status,
            'client_problems' => $c->client_problems,
            'activity_type' => $c->interaction_type,
            'activity_label' => $c->interactionTypeLabel(),
            'auto_filled' => true,
        ])->values()->all();
    }


    /**
     * يحدّث مسودة اليوم من النشاط المسجّل — لا يمس التقارير المسلّمة.
     */
    public function syncAutoDraft(User $user, Carbon $date): ?SalesDailyReport
    {
        if ($date->isFuture() || ! $this->isWorkDay($date, $user)) {
            return null;
        }

        $report = SalesDailyReport::forUser($user->id)
            ->whereDate('report_date', $date)
            ->first();

        if ($report?->isSubmitted()) {
            return $report;
        }

        $built = $this->buildFromActivities($user, $date);

        $report = SalesDailyReport::firstOrNew([
            'user_id' => $user->id,
            'report_date' => $date->toDateString(),
        ]);

        $report->fill($built['metrics']);
        $report->activity_notes = $built['activity_notes'] ?: null;
        $report->productivity_notes = $built['productivity_notes'] ?: null;
        $report->status = SalesDailyReport::STATUS_DRAFT;
        $report->user_id = $user->id;
        $report->report_date = $date->toDateString();

        $check = $this->validateCompleteness($built['metrics'], $built['contacts']);
        $report->missing_fields = array_merge(
            $check['missing'],
            $check['contact_errors'] !== [] ? ['contacts'] : []
        );

        $report->save();
        $this->syncContacts($report, $built['contacts'], $user->id);

        return $report->fresh(['contacts.lead']);
    }

    private function contactProblemsText(SalesActivity $activity, SalesLead $lead): string
    {
        $body = trim((string) ($activity->body ?? ''));
        if ($body !== '') {
            return $body;
        }

        $notes = trim((string) ($lead->notes ?? ''));
        if ($notes !== '') {
            return $notes;
        }

        $interest = trim((string) ($lead->interest ?? ''));
        if ($interest !== '') {
            return 'الاهتمام: '.$interest;
        }

        return '';
    }

    private function contactStatusText(SalesActivity $activity, SalesLead $lead): string
    {
        $parts = [
            'مرحلة: '.SalesLead::stageLabel($lead->stage),
            'مصدر: '.SalesLead::sourceLabel($lead->source ?? 'other'),
            'أولوية: '.SalesLead::priorityLabel($lead->priority ?? 'normal'),
            SalesActivity::typeLabel($activity->type),
        ];

        if ($activity->title) {
            $parts[] = $activity->title;
        }

        if ($lead->company) {
            $parts[] = 'شركة: '.$lead->company;
        }

        return implode(' | ', $parts);
    }

    private function isAutoPlaceholderProblems(string $text): bool
    {
        return str_contains($text, 'تم التسجيل تلقائياً من النشاط');
    }

    /**
     * مقارنة تقرير اليوم بأهداف KPI اليومية.
     *
     * @param  array<string, int|null>|SalesDailyReport  $report
     * @return array{
     *     status: string,
     *     status_label: string,
     *     overall_pct: float,
     *     lines: list<array{key: string, label: string, actual: int, target: int, pct: float, status: string}>
     * }
     */
    public function kpiComparisonForReport(User $user, array|SalesDailyReport $report, Carbon $date): array
    {
        // مصدر الحقيقة: CRM SOS (موثّق) — مش الأرقام اليدوية وحدها
        $sos = app(SalesDailyResultService::class)->comparisonFor($user, $date);

        return [
            'status' => $sos['status'],
            'status_label' => $sos['status_label'],
            'overall_pct' => $sos['overall_pct'],
            'lines' => collect($sos['lines'])->map(fn ($line) => [
                'key' => $line['key'],
                'label' => $line['label'],
                'actual' => $line['actual'],
                'target' => (int) $line['target'],
                'pct' => $line['pct'],
                'status' => $line['status'],
            ])->values()->all(),
            'source' => 'crm_sos',
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, SalesActivity>  $activities
     */
    private function buildActivityNotesTimeline(Collection $activities): string
    {
        if ($activities->isEmpty()) {
            return '';
        }

        $lines = $activities
            ->filter(fn (SalesActivity $a) => $a->type !== 'stage_change')
            ->map(function (SalesActivity $a) {
                $time = $a->created_at?->format('H:i') ?? '';
                $leadName = $a->lead?->name ?? '—';
                $label = SalesActivity::typeLabel($a->type);
                $extra = trim((string) ($a->body ?? ''));

                return '• '.$time.' — '.$label.': '.$leadName.($extra !== '' ? ' — '.$extra : '');
            })
            ->values();

        $stageLines = $activities
            ->where('type', 'stage_change')
            ->map(function (SalesActivity $a) {
                $time = $a->created_at?->format('H:i') ?? '';
                $leadName = $a->lead?->name ?? '—';

                return '• '.$time.' — تغيير مرحلة: '.$leadName.' — '.trim((string) ($a->body ?? ''));
            })
            ->values();

        return $lines->concat($stageLines)->implode("\n");
    }

    /**
     * @param  array<string, int>  $metrics
     * @param  \Illuminate\Support\Collection<int, SalesActivity>  $activities
     */
    private function buildProductivityNotes(array $metrics, Collection $activities): string
    {
        if ($activities->isEmpty()) {
            return '';
        }

        $parts = [
            'مكالمات: '.$metrics['calls_made'],
            'ردود: '.$metrics['calls_answered'],
            'متابعات: '.$metrics['followups_done'],
            'اجتماعات: '.$metrics['meetings_held'],
            'أرقام: '.$metrics['numbers_worked'],
            'رسائل: '.$metrics['messages_replied'],
        ];

        return 'ملخّص تلقائي — '.implode(' · ', $parts);
    }


    /**
     * نسبة أيام العمل التي سُلّم فيها التقرير (لـ KPI).
     */
    public function submissionRatePct(int $userId, Carbon $start, Carbon $end): ?float
    {
        $employee = User::find($userId);
        $workDays = 0;
        $submitted = 0;
        $cursor = $start->copy()->startOfDay();
        $endDay = $end->copy()->endOfDay();

        // الأيام التي لم تأتِ بعد لا تُحتسب ضمن نسبة التسليم.
        if ($endDay->gt(now())) {
            $endDay = now()->endOfDay();
        }

        while ($cursor->lte($endDay)) {
            if ($this->isWorkDay($cursor, $employee)) {
                $workDays++;
                $has = SalesDailyReport::forUser($userId)
                    ->whereDate('report_date', $cursor)
                    ->submitted()
                    ->exists();
                if ($has) {
                    $submitted++;
                }
            }
            $cursor->addDay();
        }

        if ($workDays === 0) {
            return null;
        }

        return round($submitted / $workDays * 100, 1);
    }

    /**
     * تطبيق خصم تلقائي ليوم واحد إن لم يُسلّم التقرير.
     */
    public function applyPenaltyForDate(User $employee, Carbon $date): ?EmployeeSalaryDeduction
    {
        if (! SalesDailyReportSettings::enabled() || ! SalesDailyReportSettings::penaltyEnabled()) {
            return null;
        }

        if (! $this->isWorkDay($date, $employee)) {
            return null;
        }

        if (! PenaltyWindow::isChargeable(PenaltyWindow::SALES_DAILY_REPORT, $employee, $date)) {
            return null;
        }

        $report = SalesDailyReport::forUser($employee->id)
            ->whereDate('report_date', $date)
            ->first();

        if ($report?->isSubmitted()) {
            return null;
        }

        if ($report?->penalty_waived_at) {
            return null;
        }

        if ($report?->auto_deduction_id) {
            return $report->autoDeduction;
        }

        $settings = SalesDailyReportSettings::all();
        $agreement = EmployeeAgreement::where('employee_id', $employee->id)->where('status', 'active')->first();

        $deduction = EmployeeSalaryDeduction::createWithAutoDeductionNumber([
            'employee_id' => $employee->id,
            'agreement_id' => $agreement?->id,
            'title' => (string) ($settings['penalty_title'] ?? 'غرامة التقرير اليومي'),
            'description' => (string) ($settings['penalty_description'] ?? ''),
            'amount' => SalesDailyReportSettings::penaltyAmount(),
            'type' => in_array($settings['penalty_type'] ?? 'penalty', ['tax', 'insurance', 'loan', 'penalty', 'other'], true)
                ? $settings['penalty_type']
                : 'penalty',
            'deduction_date' => $date->toDateString(),
            'status' => 'applied',
            'notes' => 'تقرير يومي مبيعات — تاريخ '.$date->format('Y-m-d'),
            'created_by' => null,
        ]);

        if (! $report) {
            $report = SalesDailyReport::create([
                'user_id' => $employee->id,
                'report_date' => $date->toDateString(),
                'status' => SalesDailyReport::STATUS_DRAFT,
                'auto_deduction_id' => $deduction->id,
            ]);
        } else {
            $report->update(['auto_deduction_id' => $deduction->id]);
        }

        app(SalesNotificationService::class)->notifyDailyReportPenalty(
            $employee,
            $deduction,
            $date->format('Y-m-d')
        );

        return $deduction;
    }

    /**
     * هل انتهى موعد تسليم التقرير لهذا اليوم (يُسمح بتطبيق الخصم)؟
     */
    public function isPenaltyDueForDate(Carbon $date): bool
    {
        if ($date->isFuture()) {
            return false;
        }

        if ($date->isToday()) {
            $time = (string) (SalesDailyReportSettings::all()['deadline_time'] ?? '23:59');
            [$h, $m] = array_pad(explode(':', $time), 2, '0');

            return now()->greaterThan($date->copy()->setTime((int) $h, (int) $m, 59));
        }

        return true;
    }

    /**
     * تطبيق الخصومات المستحقة لأيام عمل بدون تسليم (بدون انتظار Cron).
     *
     * @param  iterable<int, User>|null  $employees
     */
    public function applyDuePenaltiesInRange(Carbon $from, Carbon $to, ?iterable $employees = null): int
    {
        if (! SalesDailyReportSettings::enabled() || ! SalesDailyReportSettings::penaltyEnabled()) {
            return 0;
        }

        $this->ensureLinkedAutoDeductionsApplied();

        $employees = $employees ?? User::salesEmployees()->where('is_active', true)->get();
        $count = 0;
        $end = $to->copy()->startOfDay();

        foreach ($employees as $employee) {
            $cursor = PenaltyWindow::earliestChargeableDate(
                PenaltyWindow::SALES_DAILY_REPORT,
                $employee,
                $from
            );

            while ($cursor->lte($end)) {
                if ($this->isPenaltyDueForDate($cursor) && $this->applyPenaltyForDate($employee, $cursor)) {
                    $count++;
                }
                $cursor->addDay();
            }
        }

        return $count;
    }

    private function ensureLinkedAutoDeductionsApplied(): void
    {
        $ids = SalesDailyReport::query()
            ->whereNotNull('auto_deduction_id')
            ->pluck('auto_deduction_id');

        if ($ids->isEmpty()) {
            return;
        }

        EmployeeSalaryDeduction::query()
            ->whereIn('id', $ids)
            ->where('status', 'pending')
            ->update(['status' => 'applied']);
    }

    /**
     * @return Collection<int, SalesDailyReport>
     */
    public function reportsQuery(?int $userId, Carbon $from, Carbon $to, ?string $status = null): Collection
    {
        $q = SalesDailyReport::query()
            ->with(['user', 'contacts.lead', 'autoDeduction'])
            ->whereBetween('report_date', [$from->toDateString(), $to->toDateString()])
            ->orderByDesc('report_date');

        if ($userId) {
            $q->where('user_id', $userId);
        }

        if ($status && in_array($status, [SalesDailyReport::STATUS_DRAFT, SalesDailyReport::STATUS_SUBMITTED], true)) {
            $q->where('status', $status);
        }

        return $q->get();
    }
}
