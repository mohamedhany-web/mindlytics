<?php

namespace App\Services\MetaSocial;

use App\Models\MetaSocialConversation;
use App\Models\SalesLead;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class MetaSocialLeadCenterService
{
    public function __construct(
        private MetaSocialCrmService $crm,
    ) {}

    public function ready(): bool
    {
        return Schema::hasTable('meta_social_conversations');
    }

    /**
     * @return array<string, int>
     */
    public function stats(?int $pageId = null): array
    {
        $base = $this->baseQuery($pageId);

        return [
            'all' => (clone $base)->count(),
            'new' => (clone $base)->whereNull('sales_lead_id')->count(),
            'in_crm' => (clone $base)->whereNotNull('sales_lead_id')->count(),
            'has_phone' => (clone $base)->whereNotNull('phone')->where('phone', 'not like', 'meta_%')->count(),
            'unassigned' => (clone $base)->whereNull('assigned_to')->count(),
            'unread' => (clone $base)->where('unread_count', '>', 0)->count(),
            'open' => (clone $base)->where('status', MetaSocialConversation::STATUS_OPEN)->count(),
            'closed' => (clone $base)->where('status', MetaSocialConversation::STATUS_CLOSED)->count(),
            'messenger' => (clone $base)->where('platform', MetaSocialConversation::PLATFORM_MESSENGER)->count(),
            'instagram' => (clone $base)->where('platform', MetaSocialConversation::PLATFORM_INSTAGRAM)->count(),
        ];
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
        $q = $this->baseQuery($pageId > 0 ? $pageId : null)
            ->with([
                'page:id,page_name',
                'assignee:id,name',
                'salesLead:id,name,phone,email,stage,priority,assigned_to,source,updated_at,last_contacted_at',
            ])
            ->orderByDesc('last_message_at')
            ->orderByDesc('id');

        $tab = (string) ($filters['tab'] ?? 'all');
        match ($tab) {
            'new' => $q->whereNull('sales_lead_id'),
            'in_crm' => $q->whereNotNull('sales_lead_id'),
            'has_phone' => $q->whereNotNull('phone')->where('phone', 'not like', 'meta_%'),
            'unassigned' => $q->whereNull('assigned_to'),
            'unread' => $q->where('unread_count', '>', 0),
            'open' => $q->where('status', MetaSocialConversation::STATUS_OPEN),
            'closed' => $q->where('status', MetaSocialConversation::STATUS_CLOSED),
            'messenger' => $q->where('platform', MetaSocialConversation::PLATFORM_MESSENGER),
            'instagram' => $q->where('platform', MetaSocialConversation::PLATFORM_INSTAGRAM),
            default => null,
        };

        if (! empty($filters['assigned_to'])) {
            if ($filters['assigned_to'] === 'unassigned') {
                $q->whereNull('assigned_to');
            } elseif (is_numeric($filters['assigned_to'])) {
                $q->where('assigned_to', (int) $filters['assigned_to']);
            }
        }

        if (! empty($filters['stage']) && Schema::hasTable('sales_leads')) {
            $stage = (string) $filters['stage'];
            $q->whereHas('salesLead', fn ($lq) => $lq->where('stage', $stage));
        }

        $search = trim((string) ($filters['q'] ?? ''));
        if ($search !== '') {
            $q->where(function ($inner) use ($search) {
                $inner->where('participant_name', 'like', '%'.$search.'%')
                    ->orWhere('participant_username', 'like', '%'.$search.'%')
                    ->orWhere('participant_id', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('last_message_preview', 'like', '%'.$search.'%')
                    ->orWhereHas('salesLead', function ($lq) use ($search) {
                        $lq->where('name', 'like', '%'.$search.'%')
                            ->orWhere('phone', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%');
                    });
            });
        }

        return $q;
    }

    /**
     * @return Collection<int, MetaSocialConversation>
     */
    public function listLeads(array $filters, int $limit = 300): Collection
    {
        return $this->filteredQuery($filters)->limit($limit)->get();
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

        return [
            'id' => $c->id,
            'display_name' => $c->displayName(),
            'platform' => $c->platform,
            'platform_label' => $c->platformLabel(),
            'page_name' => $c->page?->page_name,
            'profile_pic' => $c->participant_profile_pic,
            'phone' => $phone ?: $lead?->phone,
            'email' => $c->email ?: $lead?->email,
            'preview' => $c->last_message_preview,
            'unread' => (int) $c->unread_count,
            'status' => $c->status,
            'assigned_to' => $c->assigned_to,
            'assignee_name' => $c->assignee?->name,
            'last_at' => $c->last_message_at?->format('Y-m-d H:i'),
            'last_human' => $c->last_message_at?->diffForHumans(),
            'last_time' => $c->last_message_at?->format('H:i') ?? '',
            'in_crm' => (bool) $c->sales_lead_id,
            'sales_lead_id' => $c->sales_lead_id,
            'sales_lead_name' => $lead?->name,
            'stage' => $lead?->stage,
            'stage_label' => $lead ? SalesLead::stageLabel((string) $lead->stage) : 'لم يُنشأ بعد',
            'priority' => $lead?->priority,
            'inbox_url' => route('admin.meta-social.inbox.index', ['conversation' => $c->id]),
            'crm_url' => $lead ? route('admin.sales.leads.show', $lead) : null,
            'is_real_phone' => (bool) ($phone ?: ($lead?->phone && ! str_starts_with((string) $lead->phone, 'meta_'))),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function serializeDetail(MetaSocialConversation $c): array
    {
        $row = $this->serializeRow($c);
        if ($this->crm->crmReady()) {
            $row['crm'] = $this->crm->serializeCrm($c);
        }
        $row['notes'] = $c->notes;
        $row['participant_id'] = $c->participant_id;
        $row['participant_username'] = $c->participant_username;

        return $row;
    }
}
