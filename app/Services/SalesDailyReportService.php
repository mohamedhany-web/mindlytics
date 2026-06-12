<?php

namespace App\Services;

use App\Models\EmployeeAgreement;
use App\Models\EmployeeSalaryDeduction;
use App\Models\SalesDailyReport;
use App\Models\SalesDailyReportContact;
use App\Models\SalesLead;
use App\Models\User;
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
                if (empty(trim((string) ($c['client_problems'] ?? '')))) {
                    $contactErrors[] = "صف التواصل #{$row}: مشاكل/احتياجات العميل مطلوبة.";
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
     * نسبة أيام العمل التي سُلّم فيها التقرير (لـ KPI).
     */
    public function submissionRatePct(int $userId, Carbon $start, Carbon $end): ?float
    {
        $employee = User::find($userId);
        $workDays = 0;
        $submitted = 0;
        $cursor = $start->copy()->startOfDay();
        $endDay = $end->copy()->endOfDay();

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

        $report = SalesDailyReport::forUser($employee->id)
            ->whereDate('report_date', $date)
            ->first();

        if ($report?->isSubmitted()) {
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
            'status' => in_array($settings['penalty_status'] ?? 'pending', ['pending', 'applied', 'cancelled'], true)
                ? $settings['penalty_status']
                : 'pending',
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

        return $deduction;
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
