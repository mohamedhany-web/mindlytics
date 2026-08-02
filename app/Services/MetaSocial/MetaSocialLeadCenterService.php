<?php

namespace App\Services\MetaSocial;

use App\Models\MetaSocialConversation;
use App\Models\SalesLead;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MetaSocialLeadCenterService
{
    /** خريطة مراحل Business Suite ↔ مراحل CRM الأكاديمية */
    public const STAGE_TO_CRM = [
        'intake' => 'new_lead',
        'new_lead' => 'new_lead',
        'first_contact' => 'first_contact',
        'qualified' => 'qualification',
        'follow_up' => 'follow_up_scheduled',
        'offer_sent' => 'offer_sent',
        'converted' => 'enrollment_completed',
        'not_qualified' => 'dormant',
        'lost' => 'lost',
    ];

    public const CRM_TO_STAGE = [
        'new_lead' => 'intake',
        'first_contact' => 'first_contact',
        'no_answer' => 'follow_up',
        'connected' => 'first_contact',
        'qualification' => 'qualified',
        'interested' => 'qualified',
        'objection' => 'follow_up',
        'follow_up_scheduled' => 'follow_up',
        'offer_sent' => 'offer_sent',
        'payment_pending' => 'offer_sent',
        'payment_received' => 'converted',
        'enrollment_completed' => 'converted',
        'upsell' => 'converted',
        'dormant' => 'not_qualified',
        'lost' => 'lost',
    ];

    public function __construct(
        private MetaSocialCrmService $crm,
    ) {}

    public function ready(): bool
    {
        return Schema::hasTable('meta_social_conversations');
    }

    public function crmColumnsReady(): bool
    {
        return Schema::hasColumn('meta_social_conversations', 'sales_lead_id')
            && Schema::hasColumn('meta_social_conversations', 'phone');
    }

    public function leadCenterColumnsReady(): bool
    {
        return Schema::hasColumn('meta_social_conversations', 'labels')
            && Schema::hasColumn('meta_social_conversations', 'priority')
            && Schema::hasColumn('meta_social_conversations', 'reminder_at')
            && Schema::hasColumn('meta_social_conversations', 'lead_stage');
    }

    /**
     * @return array<string, int>
     */
    public function stats(?int $pageId = null): array
    {
        $base = $this->baseQuery($pageId);
        $stats = [
            'all' => (clone $base)->count(),
            'new' => 0,
            'in_crm' => 0,
            'has_phone' => 0,
            'unassigned' => (clone $base)->whereNull('assigned_to')->count(),
            'unread' => (clone $base)->where('unread_count', '>', 0)->count(),
            'open' => (clone $base)->where('status', MetaSocialConversation::STATUS_OPEN)->count(),
            'closed' => (clone $base)->where('status', MetaSocialConversation::STATUS_CLOSED)->count(),
            'messenger' => (clone $base)->where('platform', MetaSocialConversation::PLATFORM_MESSENGER)->count(),
            'instagram' => (clone $base)->where('platform', MetaSocialConversation::PLATFORM_INSTAGRAM)->count(),
            'reminder_due' => 0,
            'high_priority' => 0,
        ];

        if ($this->crmColumnsReady()) {
            $stats['new'] = (clone $base)->whereNull('sales_lead_id')->count();
            $stats['in_crm'] = (clone $base)->whereNotNull('sales_lead_id')->count();
            $stats['has_phone'] = (clone $base)->whereNotNull('phone')->where('phone', 'not like', 'meta_%')->count();
        }

        if ($this->leadCenterColumnsReady()) {
            $stats['reminder_due'] = (clone $base)->whereNotNull('reminder_at')->where('reminder_at', '<=', now())->count();
            $stats['high_priority'] = (clone $base)->whereIn('priority', ['high', 'urgent'])->count();
        }

        return $stats;
    }

    public function baseQuery(?int $pageId = null): Builder
    {
        $q = MetaSocialConversation::query();
        if ($pageId && $pageId > 0) {
            $q->where('meta_social_page_id', $pageId);
        }

        return $q;
    }

    /**
     * @return Builder<MetaSocialConversation>
     */
    public function filteredQuery(array $filters): Builder
    {
        $pageId = (int) ($filters['page'] ?? 0);
        $with = [
            'page:id,page_name',
            'assignee:id,name',
        ];
        if ($this->crmColumnsReady() && Schema::hasTable('sales_leads')) {
            $with[] = 'salesLead:id,name,phone,email,stage,priority,assigned_to,source,updated_at,last_contacted_at,next_follow_up_at,notes';
        }
        $q = $this->baseQuery($pageId > 0 ? $pageId : null)->with($with);

        $sort = (string) ($filters['sort'] ?? 'recent');
        match ($sort) {
            'oldest' => $q->orderBy('last_message_at')->orderBy('id'),
            'name' => $q->orderBy('participant_name')->orderByDesc('id'),
            'priority' => $this->leadCenterColumnsReady()
                ? $q->orderByRaw("FIELD(COALESCE(priority, 'normal'), 'urgent','high','normal','low')")->orderByDesc('last_message_at')
                : $q->orderByDesc('last_message_at'),
            'reminder' => $this->leadCenterColumnsReady()
                ? $q->orderByRaw('reminder_at is null')->orderBy('reminder_at')->orderByDesc('id')
                : $q->orderByDesc('last_message_at'),
            default => $q->orderByDesc('last_message_at')->orderByDesc('id'),
        };

        $tab = (string) ($filters['tab'] ?? 'all');
        $crmReady = $this->crmColumnsReady();
        $lcReady = $this->leadCenterColumnsReady();
        match ($tab) {
            'new' => $crmReady ? $q->whereNull('sales_lead_id') : $q->whereRaw('1=0'),
            'in_crm' => $crmReady ? $q->whereNotNull('sales_lead_id') : $q->whereRaw('1=0'),
            'has_phone' => $crmReady
                ? $q->whereNotNull('phone')->where('phone', 'not like', 'meta_%')
                : $q->whereRaw('1=0'),
            'unassigned' => $q->whereNull('assigned_to'),
            'unread' => $q->where('unread_count', '>', 0),
            'open' => $q->where('status', MetaSocialConversation::STATUS_OPEN),
            'closed' => $q->where('status', MetaSocialConversation::STATUS_CLOSED),
            'messenger' => $q->where('platform', MetaSocialConversation::PLATFORM_MESSENGER),
            'instagram' => $q->where('platform', MetaSocialConversation::PLATFORM_INSTAGRAM),
            'reminder_due' => $lcReady
                ? $q->whereNotNull('reminder_at')->where('reminder_at', '<=', now())
                : $q->whereRaw('1=0'),
            'high_priority' => $lcReady
                ? $q->whereIn('priority', ['high', 'urgent'])
                : $q->whereRaw('1=0'),
            default => null,
        };

        if (! empty($filters['assigned_to'])) {
            if ($filters['assigned_to'] === 'unassigned') {
                $q->whereNull('assigned_to');
            } elseif (is_numeric($filters['assigned_to'])) {
                $q->where('assigned_to', (int) $filters['assigned_to']);
            }
        }

        if (! empty($filters['stage'])) {
            $stage = (string) $filters['stage'];
            $q->where(function ($inner) use ($stage) {
                $applied = false;
                if (Schema::hasColumn('meta_social_conversations', 'lead_stage')) {
                    $inner->where('lead_stage', $stage);
                    $applied = true;
                }
                if ($this->crmColumnsReady() && Schema::hasTable('sales_leads')) {
                    $crmStage = self::STAGE_TO_CRM[$stage] ?? $stage;
                    $method = $applied ? 'orWhereHas' : 'whereHas';
                    $inner->{$method}('salesLead', fn ($lq) => $lq->where('stage', $crmStage));
                    $applied = true;
                }
                if (! $applied) {
                    $inner->whereRaw('1=0');
                }
            });
        }

        if (! empty($filters['label']) && $this->leadCenterColumnsReady()) {
            $label = (string) $filters['label'];
            $q->whereJsonContains('labels', $label);
        }

        if (! empty($filters['priority']) && $this->leadCenterColumnsReady()) {
            $q->where('priority', (string) $filters['priority']);
        }

        if (! empty($filters['from'])) {
            $q->whereDate('last_message_at', '>=', $filters['from']);
        }
        if (! empty($filters['to'])) {
            $q->whereDate('last_message_at', '<=', $filters['to']);
        }

        $search = trim((string) ($filters['q'] ?? ''));
        if ($search !== '') {
            $q->where(function ($inner) use ($search) {
                $inner->where('participant_name', 'like', '%'.$search.'%')
                    ->orWhere('participant_username', 'like', '%'.$search.'%')
                    ->orWhere('participant_id', 'like', '%'.$search.'%')
                    ->orWhere('last_message_preview', 'like', '%'.$search.'%');
                if (Schema::hasColumn('meta_social_conversations', 'phone')) {
                    $inner->orWhere('phone', 'like', '%'.$search.'%');
                }
                if (Schema::hasColumn('meta_social_conversations', 'email')) {
                    $inner->orWhere('email', 'like', '%'.$search.'%');
                }
                if (Schema::hasColumn('meta_social_conversations', 'notes')) {
                    $inner->orWhere('notes', 'like', '%'.$search.'%');
                }
                if ($this->crmColumnsReady() && Schema::hasTable('sales_leads')) {
                    $inner->orWhereHas('salesLead', function ($lq) use ($search) {
                        $lq->where('name', 'like', '%'.$search.'%')
                            ->orWhere('phone', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%');
                    });
                }
            });
        }

        return $q;
    }

    /**
     * @return Collection<int, MetaSocialConversation>
     */
    public function listLeads(array $filters, int $limit = 400): Collection
    {
        return $this->filteredQuery($filters)->limit($limit)->get();
    }

    public function resolveStage(MetaSocialConversation $c): string
    {
        if ($c->lead_stage) {
            return (string) $c->lead_stage;
        }
        if ($c->salesLead?->stage) {
            return self::CRM_TO_STAGE[$c->salesLead->stage] ?? 'intake';
        }

        return 'intake';
    }

    public function stageLabel(string $stage): string
    {
        return MetaSocialConversation::LEAD_STAGES[$stage] ?? $stage;
    }

    /**
     * @return array<string, mixed>
     */
    public function serializeRow(MetaSocialConversation $c): array
    {
        $lead = $c->salesLead;
        $phone = $c->phone;
        if ($phone && str_starts_with($phone, 'meta_')) {
            $phone = null;
        }
        $realPhone = $phone ?: (($lead?->phone && ! str_starts_with((string) $lead->phone, 'meta_')) ? $lead->phone : null);
        $stage = $this->resolveStage($c);
        $priority = $c->priority ?: ($lead?->priority ?: 'normal');
        $labels = is_array($c->labels) ? array_values($c->labels) : [];

        return [
            'id' => $c->id,
            'display_name' => $c->displayName(),
            'platform' => $c->platform,
            'platform_label' => $c->platformLabel(),
            'page_name' => $c->page?->page_name,
            'profile_pic' => $c->participant_profile_pic,
            'username' => $c->participant_username,
            'participant_id' => $c->participant_id,
            'phone' => $realPhone,
            'email' => $c->email ?: $lead?->email,
            'preview' => $c->last_message_preview,
            'unread' => (int) $c->unread_count,
            'status' => $c->status,
            'is_done' => $c->status === MetaSocialConversation::STATUS_CLOSED,
            'assigned_to' => $c->assigned_to,
            'assignee_name' => $c->assignee?->name,
            'created_at' => $c->created_at?->format('Y-m-d H:i'),
            'created_human' => $c->created_at?->diffForHumans(),
            'last_at' => $c->last_message_at?->format('Y-m-d H:i'),
            'last_human' => $c->last_message_at?->diffForHumans(),
            'last_time' => $c->last_message_at?->format('H:i') ?? '',
            'in_crm' => (bool) $c->sales_lead_id,
            'sales_lead_id' => $c->sales_lead_id,
            'sales_lead_name' => $lead?->name,
            'stage' => $stage,
            'stage_label' => $this->stageLabel($stage),
            'crm_stage' => $lead?->stage,
            'crm_stage_label' => $lead ? SalesLead::stageLabel((string) $lead->stage) : null,
            'priority' => $priority,
            'priority_label' => MetaSocialConversation::PRIORITIES[$priority] ?? $priority,
            'labels' => $labels,
            'reminder_at' => $c->reminder_at?->format('Y-m-d\TH:i') ?? ($lead?->next_follow_up_at?->format('Y-m-d\TH:i')),
            'reminder_human' => $c->reminder_at?->diffForHumans() ?? $lead?->next_follow_up_at?->diffForHumans(),
            'reminder_due' => $c->reminder_at ? $c->reminder_at->isPast() : (bool) ($lead?->next_follow_up_at?->isPast()),
            'source' => 'Organic messaging',
            'channel' => $c->platformLabel(),
            'inbox_url' => route('admin.meta-social.inbox.index', ['conversation' => $c->id]),
            'crm_url' => $lead ? route('admin.sales.leads.show', $lead) : null,
            'is_real_phone' => (bool) $realPhone,
            'can_request_phone' => $c->platform === MetaSocialConversation::PLATFORM_MESSENGER,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function serializeDetail(MetaSocialConversation $c): array
    {
        $row = $this->serializeRow($c);
        try {
            if ($this->crm->crmReady()) {
                $row['crm'] = $this->crm->serializeCrm($c);
            }
        } catch (\Throwable) {
            $row['crm'] = null;
        }
        $row['notes'] = Schema::hasColumn('meta_social_conversations', 'notes')
            ? ($c->notes ?: $c->salesLead?->notes)
            : ($c->salesLead?->notes);
        $row['message_count'] = 0;
        $row['recent_messages'] = [];
        try {
            if (Schema::hasTable('meta_social_messages')) {
                $row['message_count'] = $c->messages()->count();
                $row['recent_messages'] = $c->messages()
                    ->with('sentBy:id,name')
                    ->orderByDesc('id')
                    ->limit(12)
                    ->get()
                    ->sortBy('id')
                    ->values()
                    ->map(fn ($m) => [
                        'id' => $m->id,
                        'body' => $m->displayBody(),
                        'direction' => $m->direction,
                        'author' => $m->sentBy?->name,
                        'sent_at_human' => $m->sent_at?->format('Y-m-d H:i') ?? $m->created_at?->format('Y-m-d H:i'),
                    ])
                    ->all();
            }
        } catch (\Throwable) {
            // تجاهل فشل تحميل الرسائل حتى لا تسقط الصفحة
        }

        return $row;
    }

    /**
     * يضمن وجود SalesLead مربوط (زي Intake في Business Suite).
     */
    public function ensureCrmLead(MetaSocialConversation $conversation, ?int $assigneeId = null, ?int $actorId = null): SalesLead
    {
        if ($conversation->sales_lead_id) {
            $existing = SalesLead::query()->find($conversation->sales_lead_id);
            if ($existing) {
                return $existing;
            }
        }

        $assigneeId = $assigneeId ?: $conversation->assigned_to ?: $actorId ?: auth()->id();
        if (! $assigneeId) {
            $first = $this->crm->eligibleAgents()[0] ?? null;
            $assigneeId = $first?->id;
        }
        if (! $assigneeId) {
            throw ValidationException::withMessages([
                'assigned_to' => 'عيّن موظف مبيعات أولاً لإنشاء Lead في CRM.',
            ]);
        }

        return $this->crm->createLeadFromConversation(
            $conversation,
            (int) $assigneeId,
            $actorId ?: auth()->id(),
            $conversation->phone,
            $conversation->email,
            $conversation->displayName(),
        );
    }

    public function updateStage(MetaSocialConversation $conversation, string $stage, ?int $actorId = null): MetaSocialConversation
    {
        if (! array_key_exists($stage, MetaSocialConversation::LEAD_STAGES)) {
            throw ValidationException::withMessages(['stage' => 'مرحلة غير صالحة']);
        }

        $updates = [];
        if (Schema::hasColumn('meta_social_conversations', 'lead_stage')) {
            $updates['lead_stage'] = $stage;
        }
        if (in_array($stage, ['converted', 'lost', 'not_qualified'], true)) {
            $updates['status'] = MetaSocialConversation::STATUS_CLOSED;
        } elseif ($conversation->status === MetaSocialConversation::STATUS_CLOSED && in_array($stage, ['intake', 'new_lead', 'first_contact', 'qualified', 'follow_up', 'offer_sent'], true)) {
            $updates['status'] = MetaSocialConversation::STATUS_OPEN;
        }
        if ($updates !== []) {
            $conversation->update($updates);
        }

        if ($this->crm->crmReady()) {
            $lead = $this->ensureCrmLead($conversation->fresh(), null, $actorId);
            $crmStage = self::STAGE_TO_CRM[$stage] ?? 'new_lead';
            if (array_key_exists($crmStage, SalesLead::STAGES)) {
                $lead->update([
                    'stage' => $crmStage,
                    'stage_entered_at' => now(),
                ]);
            }
        }

        MetaSocialContactCaptureService::bumpInboxVersion();

        return $conversation->fresh(['page', 'assignee', 'salesLead']);
    }

    public function updatePriority(MetaSocialConversation $conversation, string $priority): MetaSocialConversation
    {
        if (! array_key_exists($priority, MetaSocialConversation::PRIORITIES)) {
            throw ValidationException::withMessages(['priority' => 'أولوية غير صالحة']);
        }
        if (Schema::hasColumn('meta_social_conversations', 'priority')) {
            $conversation->update(['priority' => $priority]);
        }
        if ($conversation->sales_lead_id && array_key_exists($priority, SalesLead::PRIORITIES)) {
            SalesLead::query()->where('id', $conversation->sales_lead_id)->update(['priority' => $priority]);
        }
        MetaSocialContactCaptureService::bumpInboxVersion();

        return $conversation->fresh(['page', 'assignee', 'salesLead']);
    }

    public function updateReminder(MetaSocialConversation $conversation, ?string $reminderAt): MetaSocialConversation
    {
        $value = $reminderAt ? \Carbon\Carbon::parse($reminderAt) : null;
        if (Schema::hasColumn('meta_social_conversations', 'reminder_at')) {
            $conversation->update(['reminder_at' => $value]);
        }
        if ($conversation->sales_lead_id) {
            SalesLead::query()->where('id', $conversation->sales_lead_id)->update([
                'next_follow_up_at' => $value,
            ]);
        }
        MetaSocialContactCaptureService::bumpInboxVersion();

        return $conversation->fresh(['page', 'assignee', 'salesLead']);
    }

    /**
     * @param  list<string>  $labels
     */
    public function updateLabels(MetaSocialConversation $conversation, array $labels): MetaSocialConversation
    {
        $clean = collect($labels)
            ->map(fn ($l) => trim((string) $l))
            ->filter()
            ->unique()
            ->take(12)
            ->values()
            ->all();

        if (Schema::hasColumn('meta_social_conversations', 'labels')) {
            $conversation->update(['labels' => $clean]);
        }
        MetaSocialContactCaptureService::bumpInboxVersion();

        return $conversation->fresh(['page', 'assignee', 'salesLead']);
    }

    public function markDone(MetaSocialConversation $conversation, bool $done = true): MetaSocialConversation
    {
        $conversation->update([
            'status' => $done ? MetaSocialConversation::STATUS_CLOSED : MetaSocialConversation::STATUS_OPEN,
        ]);
        if ($done && Schema::hasColumn('meta_social_conversations', 'lead_stage') && ! in_array($conversation->lead_stage, ['converted', 'lost', 'not_qualified'], true)) {
            // Done بدون تغيير مرحلة التحويل
        }
        MetaSocialContactCaptureService::bumpInboxVersion();

        return $conversation->fresh(['page', 'assignee', 'salesLead']);
    }

    public function unassign(MetaSocialConversation $conversation): MetaSocialConversation
    {
        $conversation->update(['assigned_to' => null]);
        if ($conversation->sales_lead_id) {
            SalesLead::query()->where('id', $conversation->sales_lead_id)->update(['assigned_to' => null]);
        }
        MetaSocialContactCaptureService::bumpInboxVersion();

        return $conversation->fresh(['page', 'assignee', 'salesLead']);
    }

    /**
     * @param  list<int>  $ids
     * @return array{updated: int}
     */
    public function bulkAction(array $ids, string $action, array $payload = [], ?int $actorId = null): array
    {
        $conversations = MetaSocialConversation::query()->whereIn('id', $ids)->get();
        $updated = 0;
        foreach ($conversations as $c) {
            match ($action) {
                'done' => $this->markDone($c, true),
                'reopen' => $this->markDone($c, false),
                'assign' => isset($payload['assigned_to'])
                    ? $this->crm->assign($c, (int) $payload['assigned_to'], $actorId)
                    : null,
                'unassign' => $this->unassign($c),
                'stage' => isset($payload['stage']) ? $this->updateStage($c, (string) $payload['stage'], $actorId) : null,
                'priority' => isset($payload['priority']) ? $this->updatePriority($c, (string) $payload['priority']) : null,
                'create_crm' => $this->ensureCrmLead($c, $payload['assigned_to'] ?? null, $actorId),
                default => null,
            };
            $updated++;
        }
        MetaSocialContactCaptureService::bumpInboxVersion();

        return ['updated' => $updated];
    }

    public function exportCsv(array $filters): StreamedResponse
    {
        $rows = $this->listLeads($filters, 2000);

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, [
                'ID', 'Name', 'Platform', 'Page', 'Phone', 'Email', 'Stage', 'Priority',
                'Labels', 'Assignee', 'Status', 'Unread', 'In CRM', 'CRM Lead ID',
                'Last message', 'Reminder', 'Created at', 'Last message at',
            ]);
            foreach ($rows as $c) {
                $r = $this->serializeRow($c);
                fputcsv($out, [
                    $r['id'],
                    $r['display_name'],
                    $r['platform_label'],
                    $r['page_name'],
                    $r['phone'],
                    $r['email'],
                    $r['stage_label'],
                    $r['priority_label'],
                    implode(' | ', $r['labels']),
                    $r['assignee_name'],
                    $r['is_done'] ? 'Done' : 'Open',
                    $r['unread'],
                    $r['in_crm'] ? 'Yes' : 'No',
                    $r['sales_lead_id'],
                    $r['preview'],
                    $r['reminder_at'],
                    $r['created_at'],
                    $r['last_at'],
                ]);
            }
            fclose($out);
        }, 'meta-lead-center-'.now()->format('Ymd-His').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @return array<string, list<array<string, mixed>>>
     */
    public function pipelineGroups(array $filters): array
    {
        $filters = array_merge($filters, ['tab' => $filters['tab'] ?? 'all']);
        unset($filters['stage']);
        $rows = $this->listLeads($filters, 500)->map(fn ($c) => $this->serializeRow($c));
        $groups = [];
        foreach (array_keys(MetaSocialConversation::LEAD_STAGES) as $stage) {
            $groups[$stage] = [];
        }
        foreach ($rows as $row) {
            $stage = $row['stage'] ?? 'intake';
            if (! isset($groups[$stage])) {
                $groups[$stage] = [];
            }
            $groups[$stage][] = $row;
        }

        return $groups;
    }
}
