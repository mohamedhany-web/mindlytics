<?php

namespace App\Domains\Sales\Services;

use App\Domains\Messaging\Models\MetaSocialAgentLink;
use App\Domains\Messaging\Models\MetaSocialMessage;
use App\Domains\Messaging\Models\WhatsAppConversationMessage;
use App\Domains\Sales\Models\SalesActivity;
use App\Domains\Sales\Models\SalesDailyReport;
use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Support\PhoneKey;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * مطابقة التقرير اليومي المسلَّم مع النشاط الفعلي (CRM + واتساب + ميتا).
 */
class SalesReportActualsService
{
    public function __construct(
        private SalesDailyReportService $dailyReports,
    ) {}

    /**
     * @return array{
     *   metrics: array<string, int>,
     *   leads: list<array<string, mixed>>,
     *   people_contacted: int,
     *   meta_linked: bool,
     *   meta_link_label: string|null
     * }
     */
    public function actualsForUserDay(User $user, Carbon $date): array
    {
        $date = $date->copy()->startOfDay();
        $built = $this->dailyReports->buildFromActivities($user, $date);
        $metrics = $built['metrics'];

        $leads = $this->leadsContactedOnDate($user, $date);

        $meta = $this->metaLinkStatus($user);

        return [
            'metrics' => [
                'messages_replied' => (int) ($metrics['messages_replied'] ?? 0),
                'whatsapp_replies' => (int) ($metrics['whatsapp_replies'] ?? 0),
                'messenger_replies' => (int) ($metrics['messenger_replies'] ?? 0),
                'leads_qualified' => (int) ($metrics['leads_qualified'] ?? 0),
                'bookings_from_leads' => (int) ($metrics['bookings_from_leads'] ?? 0),
                'numbers_worked' => (int) ($metrics['numbers_worked'] ?? 0),
                'followups_done' => (int) ($metrics['followups_done'] ?? 0),
                'calls_made' => (int) ($metrics['calls_made'] ?? 0),
                'meetings_held' => (int) ($metrics['meetings_held'] ?? 0),
                'calls_answered' => (int) ($metrics['calls_answered'] ?? 0),
            ],
            'leads' => $leads,
            'people_contacted' => count($leads),
            'meta_linked' => $meta['linked'],
            'meta_link_label' => $meta['label'],
        ];
    }

    /**
     * @return array{
     *   report_id: int,
     *   user_id: int,
     *   report_date: string,
     *   submitted: array<string, int>,
     *   actual: array<string, int>,
     *   deltas: array<string, int>,
     *   accuracy_pct: float,
     *   flags: array{inflated: bool, under_reported: bool, contacts_mismatch: bool},
     *   contacts: array{
     *     matched: list<array<string, mixed>>,
     *     report_only: list<array<string, mixed>>,
     *     actual_only: list<array<string, mixed>>
     *   },
     *   actual_leads: list<array<string, mixed>>,
     *   people_contacted: int,
     *   meta_linked: bool,
     *   meta_link_label: string|null
     * }
     */
    public function compareReportToActuals(SalesDailyReport $report): array
    {
        $report->loadMissing(['user', 'contacts.lead']);
        $user = $report->user;
        if (! $user instanceof User) {
            throw new \InvalidArgumentException('التقرير اليومي غير مرتبط بموظف.');
        }

        $date = Carbon::parse($report->report_date)->startOfDay();
        $actuals = $this->actualsForUserDay($user, $date);

        $submitted = [
            'messages_replied' => (int) ($report->messages_replied ?? 0),
            'leads_qualified' => (int) ($report->leads_qualified ?? 0),
            'bookings_from_leads' => (int) ($report->bookings_from_leads ?? 0),
            'numbers_worked' => (int) ($report->numbers_worked ?? 0),
            'followups_done' => (int) ($report->followups_done ?? 0),
            'calls_made' => (int) ($report->calls_made ?? 0),
            'meetings_held' => (int) ($report->meetings_held ?? 0),
            'calls_answered' => (int) ($report->calls_answered ?? 0),
        ];

        $actualMetrics = $actuals['metrics'];
        $compareKeys = [
            'calls_made',
            'meetings_held',
            'followups_done',
            'messages_replied',
            'numbers_worked',
        ];

        $deltas = [];
        foreach ($compareKeys as $key) {
            $deltas[$key] = ($submitted[$key] ?? 0) - ($actualMetrics[$key] ?? 0);
        }

        $accuracyPct = $this->dayAccuracyPct($submitted, $actualMetrics, $compareKeys);

        $contacts = $this->diffContacts($report, $actuals['leads']);

        $inflated = ($deltas['calls_made'] > 2)
            || ($deltas['meetings_held'] > 1)
            || ($deltas['followups_done'] > 2)
            || ($deltas['messages_replied'] > 5)
            || ($deltas['numbers_worked'] > 3);

        $underReported = ($deltas['calls_made'] < -2)
            || ($deltas['meetings_held'] < -1)
            || ($deltas['followups_done'] < -2)
            || ($deltas['messages_replied'] < -5)
            || ($deltas['numbers_worked'] < -3);

        return [
            'report_id' => (int) $report->id,
            'user_id' => (int) $user->id,
            'report_date' => $date->toDateString(),
            'submitted' => $submitted,
            'actual' => $actualMetrics,
            'deltas' => $deltas,
            'accuracy_pct' => $accuracyPct,
            'flags' => [
                'inflated' => $inflated,
                'under_reported' => $underReported,
                'contacts_mismatch' => $contacts['report_only'] !== [] || $contacts['actual_only'] !== [],
            ],
            'contacts' => $contacts,
            'actual_leads' => $actuals['leads'],
            'people_contacted' => $actuals['people_contacted'],
            'meta_linked' => $actuals['meta_linked'],
            'meta_link_label' => $actuals['meta_link_label'],
        ];
    }

    /**
     * ملخص فترة لموظف واحد أو فريق.
     *
     * @param  Collection<int, User>|list<User>  $users
     * @return array{
     *   from: string,
     *   to: string,
     *   rows: list<array<string, mixed>>,
     *   summary: array<string, mixed>
     * }
     */
    public function periodBoard(iterable $users, Carbon $from, Carbon $to): array
    {
        $from = $from->copy()->startOfDay();
        $to = $to->copy()->endOfDay();

        $userIds = collect($users)->map(fn (User $u) => (int) $u->id)->filter()->unique()->values();
        $usersById = collect($users)->keyBy(fn (User $u) => (int) $u->id);

        $reports = SalesDailyReport::query()
            ->with(['user:id,name', 'contacts'])
            ->whereIn('user_id', $userIds)
            ->submitted()
            ->whereDate('report_date', '>=', $from->toDateString())
            ->whereDate('report_date', '<=', $to->toDateString())
            ->orderByDesc('report_date')
            ->orderBy('user_id')
            ->get();

        $rows = [];
        $accuracySum = 0.0;
        $accuracyN = 0;
        $inflatedDays = 0;
        $mismatchDays = 0;

        foreach ($reports as $report) {
            $diff = $this->compareReportToActuals($report);
            $rows[] = [
                'report_id' => $diff['report_id'],
                'user_id' => $diff['user_id'],
                'user_name' => $usersById->get($diff['user_id'])?->name ?? ($report->user->name ?? '—'),
                'report_date' => $diff['report_date'],
                'accuracy_pct' => $diff['accuracy_pct'],
                'flags' => $diff['flags'],
                'submitted' => $diff['submitted'],
                'actual' => $diff['actual'],
                'deltas' => $diff['deltas'],
                'matched_contacts' => count($diff['contacts']['matched']),
                'report_only_contacts' => count($diff['contacts']['report_only']),
                'actual_only_contacts' => count($diff['contacts']['actual_only']),
                'people_contacted' => $diff['people_contacted'],
                'meta_linked' => $diff['meta_linked'],
            ];
            $accuracySum += $diff['accuracy_pct'];
            $accuracyN++;
            if ($diff['flags']['inflated']) {
                $inflatedDays++;
            }
            if ($diff['flags']['contacts_mismatch']) {
                $mismatchDays++;
            }
        }

        return [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'rows' => $rows,
            'summary' => [
                'submitted_reports' => count($rows),
                'accuracy_pct' => $accuracyN > 0 ? round($accuracySum / $accuracyN, 1) : null,
                'inflated_days' => $inflatedDays,
                'mismatch_days' => $mismatchDays,
            ],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function leadsContactedOnDate(User $user, Carbon $date): array
    {
        $start = $date->copy()->startOfDay();
        $end = $date->copy()->endOfDay();
        $uid = (int) $user->id;

        /** @var array<int, array<string, mixed>> $byLead */
        $byLead = [];

        $activities = SalesActivity::query()
            ->where('user_id', $uid)
            ->whereNotNull('sales_lead_id')
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('type', ['call', 'meeting', 'whatsapp', 'messenger', 'follow_up', 'email'])
            ->with(['lead' => fn ($q) => $q->withTrashed()])
            ->orderBy('created_at')
            ->get();

        foreach ($activities as $activity) {
            $lead = $activity->lead;
            if (! $lead) {
                continue;
            }
            $id = (int) $lead->id;
            if (! isset($byLead[$id])) {
                $byLead[$id] = $this->emptyLeadRow($lead);
            }
            $channel = $this->channelFromActivityType((string) $activity->type);
            $byLead[$id]['channels'][$channel] = true;
            $byLead[$id]['activity_count']++;
            $ts = optional($activity->created_at)?->timezone(config('app.timezone'))?->format('Y-m-d H:i');
            if ($ts) {
                $byLead[$id]['first_at'] = $byLead[$id]['first_at'] ?: $ts;
                $byLead[$id]['last_at'] = $ts;
            }
        }

        $this->mergeOutboundMessageLeads($byLead, $uid, $start, $end);

        $rows = array_values(array_map(function (array $row) {
            $row['channels'] = array_keys($row['channels']);
            $row['channels_label'] = implode(' · ', array_map(
                fn ($c) => match ($c) {
                    'call' => 'اتصال',
                    'meeting' => 'اجتماع',
                    'whatsapp' => 'واتساب',
                    'messenger' => 'ماسنجر',
                    'follow_up' => 'متابعة',
                    'email' => 'بريد',
                    default => $c,
                },
                $row['channels']
            ));

            return $row;
        }, $byLead));

        usort($rows, fn ($a, $b) => strcmp((string) $a['name'], (string) $b['name']));

        return $rows;
    }

    /**
     * @return array{linked: bool, label: string|null}
     */
    public function metaLinkStatus(User $user): array
    {
        if (! Schema::hasTable('meta_social_agent_links')) {
            return ['linked' => false, 'label' => null];
        }

        $link = MetaSocialAgentLink::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->first();

        if (! $link) {
            return ['linked' => false, 'label' => null];
        }

        $label = trim((string) ($link->meta_user_name ?: $link->meta_user_id));

        return [
            'linked' => true,
            'label' => $label !== '' ? $label : (string) $link->meta_user_id,
        ];
    }

    /**
     * @param  array<string, int>  $submitted
     * @param  array<string, int>  $actual
     * @param  list<string>  $keys
     */
    private function dayAccuracyPct(array $submitted, array $actual, array $keys): float
    {
        $weighted = 0.0;
        $weightTotal = 0;
        foreach ($keys as $key) {
            $claimed = (int) ($submitted[$key] ?? 0);
            $verified = (int) ($actual[$key] ?? 0);
            $w = max($claimed, $verified);
            if ($w === 0) {
                continue;
            }
            $pairScore = max(0.0, 100.0 - (abs($claimed - $verified) / $w * 100.0));
            $weighted += $pairScore * $w;
            $weightTotal += $w;
        }

        return $weightTotal > 0 ? round($weighted / $weightTotal, 1) : 100.0;
    }

    /**
     * @param  list<array<string, mixed>>  $actualLeads
     * @return array{
     *   matched: list<array<string, mixed>>,
     *   report_only: list<array<string, mixed>>,
     *   actual_only: list<array<string, mixed>>
     * }
     */
    private function diffContacts(SalesDailyReport $report, array $actualLeads): array
    {
        $actualByLead = [];
        $actualByPhone = [];
        foreach ($actualLeads as $lead) {
            $lid = (int) ($lead['sales_lead_id'] ?? 0);
            if ($lid > 0) {
                $actualByLead[$lid] = $lead;
            }
            $phoneKey = PhoneKey::of((string) ($lead['phone'] ?? ''));
            if ($phoneKey) {
                $actualByPhone[$phoneKey] = $lead;
            }
        }

        $matched = [];
        $reportOnly = [];
        $matchedLeadIds = [];
        $matchedPhones = [];

        foreach ($report->contacts as $contact) {
            $lid = (int) ($contact->sales_lead_id ?? 0);
            $phoneKey = PhoneKey::of((string) ($contact->contact_phone ?? ''));
            $hit = null;
            if ($lid > 0 && isset($actualByLead[$lid])) {
                $hit = $actualByLead[$lid];
            } elseif ($phoneKey && isset($actualByPhone[$phoneKey])) {
                $hit = $actualByPhone[$phoneKey];
            }

            $row = [
                'sales_lead_id' => $lid ?: ($hit['sales_lead_id'] ?? null),
                'name' => $contact->contact_name ?: ($hit['name'] ?? '—'),
                'phone' => $contact->contact_phone ?: ($hit['phone'] ?? '—'),
                'interaction_type' => $contact->interaction_type,
                'channels_label' => $hit['channels_label'] ?? $contact->interactionTypeLabel(),
            ];

            if ($hit) {
                $matched[] = $row;
                if (! empty($hit['sales_lead_id'])) {
                    $matchedLeadIds[(int) $hit['sales_lead_id']] = true;
                }
                $p = PhoneKey::of((string) ($hit['phone'] ?? ''));
                if ($p) {
                    $matchedPhones[$p] = true;
                }
            } else {
                $reportOnly[] = $row;
            }
        }

        $actualOnly = [];
        foreach ($actualLeads as $lead) {
            $lid = (int) ($lead['sales_lead_id'] ?? 0);
            $p = PhoneKey::of((string) ($lead['phone'] ?? ''));
            if (($lid > 0 && isset($matchedLeadIds[$lid])) || ($p && isset($matchedPhones[$p]))) {
                continue;
            }
            $actualOnly[] = [
                'sales_lead_id' => $lid ?: null,
                'name' => $lead['name'] ?? '—',
                'phone' => $lead['phone'] ?? '—',
                'channels_label' => $lead['channels_label'] ?? '—',
                'last_at' => $lead['last_at'] ?? null,
            ];
        }

        return [
            'matched' => $matched,
            'report_only' => $reportOnly,
            'actual_only' => $actualOnly,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function emptyLeadRow(SalesLead $lead): array
    {
        return [
            'sales_lead_id' => (int) $lead->id,
            'name' => $lead->name,
            'phone' => $lead->phone,
            'channels' => [],
            'activity_count' => 0,
            'first_at' => null,
            'last_at' => null,
        ];
    }

    private function channelFromActivityType(string $type): string
    {
        return match ($type) {
            'whatsapp' => 'whatsapp',
            'messenger' => 'messenger',
            'meeting' => 'meeting',
            'follow_up' => 'follow_up',
            'email' => 'email',
            default => 'call',
        };
    }

    /**
     * @param  array<int, array<string, mixed>>  $byLead
     */
    private function mergeOutboundMessageLeads(array &$byLead, int $userId, Carbon $start, Carbon $end): void
    {
        if (Schema::hasTable('whatsapp_conversation_messages')
            && Schema::hasColumn('whatsapp_conversation_messages', 'sent_by_user_id')
            && Schema::hasTable('whatsapp_conversations')
            && Schema::hasColumn('whatsapp_conversations', 'sales_lead_id')) {
            $rows = WhatsAppConversationMessage::query()
                ->where('sent_by_user_id', $userId)
                ->where('direction', 'outbound')
                ->whereBetween('created_at', [$start, $end])
                ->whereHas('conversation', fn ($q) => $q->whereNotNull('sales_lead_id'))
                ->with(['conversation.salesLead' => fn ($q) => $q->withTrashed()])
                ->orderBy('created_at')
                ->get();

            foreach ($rows as $msg) {
                $lead = $msg->conversation?->salesLead;
                if (! $lead) {
                    continue;
                }
                $id = (int) $lead->id;
                if (! isset($byLead[$id])) {
                    $byLead[$id] = $this->emptyLeadRow($lead);
                }
                $byLead[$id]['channels']['whatsapp'] = true;
                $byLead[$id]['activity_count']++;
                $ts = optional($msg->created_at)?->timezone(config('app.timezone'))?->format('Y-m-d H:i');
                if ($ts) {
                    $byLead[$id]['first_at'] = $byLead[$id]['first_at'] ?: $ts;
                    $byLead[$id]['last_at'] = $ts;
                }
            }
        }

        if (Schema::hasTable('meta_social_messages')
            && Schema::hasColumn('meta_social_messages', 'sent_by_user_id')
            && Schema::hasTable('meta_social_conversations')
            && Schema::hasColumn('meta_social_conversations', 'sales_lead_id')) {
            $rows = MetaSocialMessage::query()
                ->where('sent_by_user_id', $userId)
                ->where('direction', 'outbound')
                ->whereBetween('created_at', [$start, $end])
                ->whereHas('conversation', fn ($q) => $q->whereNotNull('sales_lead_id'))
                ->with(['conversation.salesLead' => fn ($q) => $q->withTrashed()])
                ->orderBy('created_at')
                ->get();

            foreach ($rows as $msg) {
                $lead = $msg->conversation?->salesLead;
                if (! $lead) {
                    continue;
                }
                $id = (int) $lead->id;
                if (! isset($byLead[$id])) {
                    $byLead[$id] = $this->emptyLeadRow($lead);
                }
                $byLead[$id]['channels']['messenger'] = true;
                $byLead[$id]['activity_count']++;
                $ts = optional($msg->created_at)?->timezone(config('app.timezone'))?->format('Y-m-d H:i');
                if ($ts) {
                    $byLead[$id]['first_at'] = $byLead[$id]['first_at'] ?: $ts;
                    $byLead[$id]['last_at'] = $ts;
                }
            }
        }
    }
}
