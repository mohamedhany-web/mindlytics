<?php

namespace App\Services\MetaSocial;

use App\Models\MetaSocialConversation;
use App\Models\SalesActivity;
use App\Models\SalesLead;
use App\Models\SalesLeadCategory;
use App\Models\User;
use App\Services\WhatsAppAssignmentService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class MetaSocialCrmService
{
    public function __construct(
        private MetaSocialGraphService $graph,
        private WhatsAppAssignmentService $assignment,
    ) {}

    public function crmReady(): bool
    {
        return Schema::hasTable('meta_social_conversations')
            && Schema::hasColumn('meta_social_conversations', 'sales_lead_id');
    }

    /**
     * @return list<User>
     */
    public function eligibleAgents(): array
    {
        return $this->assignment->eligibleSalesStaff();
    }

    public function enrichParticipantProfile(MetaSocialConversation $conversation): MetaSocialConversation
    {
        $conversation->loadMissing('page');
        $page = $conversation->page;
        if (! $page || ! $page->page_access_token || ! $conversation->participant_id) {
            return $conversation;
        }

        try {
            // Meta لا ترجع phone/email هنا حتى مع Business verification
            $fields = $conversation->platform === MetaSocialConversation::PLATFORM_INSTAGRAM
                ? 'name,username,profile_pic'
                : 'name,first_name,last_name,profile_pic';

            $response = Http::timeout(20)->get(
                "{$this->graph->graphUrl()}/{$conversation->participant_id}",
                [
                    'fields' => $fields,
                    'access_token' => $page->page_access_token,
                ]
            );

            if (! $response->successful()) {
                app(MetaSocialContactCaptureService::class)->scanConversationHistory($conversation);

                return $conversation->fresh(['page', 'assignee', 'salesLead']);
            }

            $data = $response->json() ?? [];
            $updates = [];
            $name = (string) ($data['name'] ?? '');
            if ($name === '' && (! empty($data['first_name']) || ! empty($data['last_name']))) {
                $name = trim(($data['first_name'] ?? '').' '.($data['last_name'] ?? ''));
            }
            if ($name !== '' && $name !== $conversation->participant_name) {
                $updates['participant_name'] = $name;
            }
            if (! empty($data['username'])) {
                $updates['participant_username'] = (string) $data['username'];
            }
            if (! empty($data['profile_pic'])) {
                $updates['participant_profile_pic'] = (string) $data['profile_pic'];
            }

            if ($updates !== []) {
                $conversation->update($updates);
            }
        } catch (\Throwable) {
            // ignore Graph profile failures
        }

        app(MetaSocialContactCaptureService::class)->scanConversationHistory($conversation);

        return $conversation->fresh(['page', 'assignee', 'salesLead']);
    }

    /**
     * @param  array{name?:?string,phone?:?string,email?:?string,notes?:?string,status?:?string}  $data
     */
    public function updateContactDetails(MetaSocialConversation $conversation, array $data): MetaSocialConversation
    {
        $updates = [];
        foreach (['phone', 'email', 'notes', 'status'] as $field) {
            if (array_key_exists($field, $data)) {
                $updates[$field] = $data[$field] !== '' ? $data[$field] : null;
            }
        }
        if (array_key_exists('name', $data) && filled($data['name'])) {
            $updates['participant_name'] = (string) $data['name'];
        }

        if ($updates !== []) {
            $conversation->update($updates);
        }

        return $conversation->fresh(['page', 'assignee', 'salesLead']);
    }

    public function assign(MetaSocialConversation $conversation, int $userId, ?int $performedBy = null): MetaSocialConversation
    {
        $assignee = User::query()->findOrFail($userId);
        $conversation->update(['assigned_to' => $assignee->id]);

        if ($conversation->sales_lead_id) {
            SalesLead::query()
                ->where('id', $conversation->sales_lead_id)
                ->update(['assigned_to' => $assignee->id]);
        }

        if ($conversation->sales_lead_id) {
            SalesActivity::query()->create([
                'sales_lead_id' => $conversation->sales_lead_id,
                'user_id' => $performedBy ?? auth()->id(),
                'type' => 'note',
                'title' => 'تعيين محادثة سوشيال',
                'body' => 'تم تعيين محادثة '.$conversation->platformLabel().' إلى '.$assignee->name,
                'meta' => [
                    'meta_social_conversation_id' => $conversation->id,
                    'assigned_to' => $assignee->id,
                ],
            ]);
        }

        return $conversation->fresh(['page', 'assignee', 'salesLead']);
    }

    public function createLeadFromConversation(
        MetaSocialConversation $conversation,
        ?int $assigneeId = null,
        ?int $createdBy = null,
        ?string $phone = null,
        ?string $email = null,
        ?string $name = null,
    ): SalesLead {
        if ($conversation->sales_lead_id) {
            $existing = SalesLead::query()->find($conversation->sales_lead_id);
            if ($existing) {
                return $existing;
            }
        }

        $phone = trim((string) ($phone ?: $conversation->phone));
        if ($phone === '') {
            // Meta غالباً لا يعطي رقم هاتف — نستخدم معرّف فريد قابل للبحث لاحقاً
            $phone = 'meta_'.$conversation->platform.'_'.$conversation->participant_id;
        }

        $byPhone = $this->findLeadByPhone($phone);
        if ($byPhone) {
            $this->linkLead($conversation, $byPhone);
            if ($assigneeId && ! $byPhone->assigned_to) {
                $byPhone->update(['assigned_to' => $assigneeId]);
            }

            return $byPhone->fresh();
        }

        $assigneeId = $assigneeId ?: $conversation->assigned_to ?: auth()->id();
        if (! $assigneeId) {
            throw ValidationException::withMessages([
                'assigned_to' => 'اختر موظف مبيعات قبل إنشاء العميل.',
            ]);
        }

        $lead = SalesLead::query()->create([
            'name' => $name ?: $conversation->displayName(),
            'phone' => $phone,
            'email' => $email ?: $conversation->email,
            'source' => 'social',
            'stage' => 'new_lead',
            'priority' => 'normal',
            'assigned_to' => $assigneeId,
            'created_by' => $createdBy ?: auth()->id(),
            'category_id' => SalesLeadCategory::defaultGeneralId(),
            'last_contacted_at' => $conversation->last_message_at ?? now(),
            'notes' => trim(
                'أُنشئ من Inbox السوشيال ('.$conversation->platformLabel().')'
                ."\nصفحة: ".($conversation->page?->page_name ?? '—')
                ."\nمعرّف المستخدم: ".$conversation->participant_id
                .($conversation->notes ? "\n".$conversation->notes : '')
            ),
        ]);

        $this->linkLead($conversation, $lead);

        if (! $conversation->assigned_to) {
            $conversation->update(['assigned_to' => $assigneeId]);
        }

        SalesActivity::query()->create([
            'sales_lead_id' => $lead->id,
            'user_id' => $createdBy ?: auth()->id(),
            'type' => 'note',
            'title' => 'إنشاء عميل من السوشيال',
            'body' => 'محادثة '.$conversation->platformLabel().' #'.$conversation->id,
            'meta' => ['meta_social_conversation_id' => $conversation->id],
        ]);

        return $lead->fresh();
    }

    public function linkLead(MetaSocialConversation $conversation, SalesLead $lead): MetaSocialConversation
    {
        $updates = ['sales_lead_id' => $lead->id];
        if ($lead->assigned_to && ! $conversation->assigned_to) {
            $updates['assigned_to'] = $lead->assigned_to;
        }
        if ($lead->phone && ! $conversation->phone) {
            $updates['phone'] = $lead->phone;
        }
        if ($lead->email && ! $conversation->email) {
            $updates['email'] = $lead->email;
        }
        if ($lead->name && (! $conversation->participant_name || $conversation->participant_name === $conversation->participant_id)) {
            $updates['participant_name'] = $lead->name;
        }

        $conversation->update($updates);

        return $conversation->fresh(['page', 'assignee', 'salesLead']);
    }

    public function findLeadByPhone(string $phone): ?SalesLead
    {
        $digits = preg_replace('/[^0-9A-Za-z_]/', '', $phone);
        if ($digits === '') {
            return null;
        }

        return SalesLead::query()
            ->where(function ($q) use ($phone, $digits) {
                $q->where('phone', $phone)
                    ->orWhere('phone', 'like', '%'.$digits.'%');
            })
            ->orderByDesc('updated_at')
            ->first();
    }

    public function logOutboundToSalesLead(MetaSocialConversation $conversation, string $preview, ?int $userId = null): void
    {
        if (! $conversation->sales_lead_id) {
            return;
        }

        SalesActivity::query()->create([
            'sales_lead_id' => $conversation->sales_lead_id,
            'user_id' => $userId ?? auth()->id(),
            'type' => 'note',
            'title' => 'رد من Inbox السوشيال',
            'body' => mb_substr($preview, 0, 500),
            'meta' => [
                'meta_social_conversation_id' => $conversation->id,
                'platform' => $conversation->platform,
            ],
        ]);

        SalesLead::query()
            ->where('id', $conversation->sales_lead_id)
            ->update(['last_contacted_at' => now()]);
    }

    /**
     * @return array<string, mixed>
     */
    public function serializeCrm(MetaSocialConversation $conversation): array
    {
        $conversation->loadMissing(['assignee:id,name', 'salesLead:id,name,phone,email,stage,assigned_to', 'page:id,page_name']);

        $lead = $conversation->salesLead;

        return [
            'conversation_id' => $conversation->id,
            'display_name' => $conversation->displayName(),
            'participant_id' => $conversation->participant_id,
            'participant_username' => $conversation->participant_username,
            'participant_profile_pic' => $conversation->participant_profile_pic,
            'platform' => $conversation->platform,
            'platform_label' => $conversation->platformLabel(),
            'page_name' => $conversation->page?->page_name,
            'phone' => $conversation->phone,
            'email' => $conversation->email,
            'notes' => $conversation->notes,
            'status' => $conversation->status,
            'assigned_to' => $conversation->assigned_to,
            'assignee_name' => $conversation->assignee?->name,
            'sales_lead_id' => $conversation->sales_lead_id,
            'sales_lead_name' => $lead?->name,
            'sales_lead_stage' => $lead?->stage,
            'sales_lead_stage_label' => $lead ? SalesLead::stageLabel((string) $lead->stage) : null,
            'sales_lead_url' => $lead ? route('admin.sales.leads.show', $lead) : null,
            'last_message_at' => $conversation->last_message_at?->toIso8601String(),
            'unread_count' => (int) $conversation->unread_count,
        ];
    }
}
