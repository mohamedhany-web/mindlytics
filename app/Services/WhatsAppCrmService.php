<?php

namespace App\Services;

use App\Models\SalesActivity;
use App\Models\SalesLead;
use App\Models\User;
use App\Models\WhatsAppContact;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppConversationEvent;
use App\Models\WhatsAppConversationNote;
use App\Models\WhatsAppTag;
use Illuminate\Support\Facades\Schema;

class WhatsAppCrmService
{
    public function __construct(
        private WhatsAppAssignmentService $assignment,
    ) {}

    public function crmTablesReady(): bool
    {
        return Schema::hasTable('whatsapp_contacts')
            && Schema::hasColumn('whatsapp_conversations', 'assigned_to');
    }

    public function ensureContactForConversation(WhatsAppConversation $conversation): ?WhatsAppContact
    {
        if (! $this->crmTablesReady()) {
            return null;
        }

        $contact = WhatsAppContact::query()->firstOrCreate(
            ['phone_number' => $conversation->phone_number],
            ['name' => $conversation->contact_name]
        );

        $lead = $this->findLeadByPhone($conversation->phone_number);
        $updates = [];

        if ($lead && ! $contact->sales_lead_id) {
            $updates['sales_lead_id'] = $lead->id;
        }

        if ($conversation->contact_name && ! $contact->name) {
            $updates['name'] = $conversation->contact_name;
        }

        if ($lead?->name && ! $contact->name) {
            $updates['name'] = $lead->name;
        }

        if ($lead?->email && ! $contact->email) {
            $updates['email'] = $lead->email;
        }

        if ($lead?->company && ! $contact->company) {
            $updates['company'] = $lead->company;
        }

        if ($lead?->source && ! $contact->source) {
            $updates['source'] = $lead->source;
        }

        if ($lead?->expected_value && ! $contact->lifetime_value) {
            $updates['lifetime_value'] = $lead->expected_value;
        }

        if ($conversation->user_id && ! $contact->user_id) {
            $updates['user_id'] = $conversation->user_id;
        }

        if ($updates !== []) {
            $contact->update($updates);
        }

        $conversationUpdates = ['contact_id' => $contact->id];

        if ($lead && ! $conversation->sales_lead_id) {
            $conversationUpdates['sales_lead_id'] = $lead->id;
            $this->logEvent($conversation, WhatsAppConversationEvent::TYPE_LEAD_LINKED, 'ربط بعميل مبيعات', $lead->name, [
                'sales_lead_id' => $lead->id,
            ]);
        }

        if ($lead?->assigned_to && ! $conversation->assigned_to) {
            $conversationUpdates['assigned_to'] = $lead->assigned_to;
            $contact->update(['assigned_to' => $lead->assigned_to]);
        }

        if (! $conversation->department) {
            $conversationUpdates['department'] = 'sales';
        }

        if (! $conversation->status) {
            $conversationUpdates['status'] = WhatsAppConversation::STATUS_OPEN;
        }

        $conversation->update($conversationUpdates);

        if (! $conversation->assigned_to) {
            $this->assignment->autoAssign($conversation->fresh());
        }

        return $contact->fresh();
    }

    public function findLeadByPhone(string $phone): ?SalesLead
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);
        if ($digits === '') {
            return null;
        }

        return SalesLead::query()
            ->where(function ($q) use ($digits) {
                $q->where('phone', 'like', '%' . $digits)
                    ->orWhere('phone', 'like', '%' . ltrim($digits, '0'));
            })
            ->orderByDesc('updated_at')
            ->first();
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public function logEvent(
        WhatsAppConversation $conversation,
        string $type,
        ?string $title = null,
        ?string $description = null,
        array $meta = [],
        ?int $performedBy = null
    ): ?WhatsAppConversationEvent {
        if (! Schema::hasTable('whatsapp_conversation_events')) {
            return null;
        }

        return WhatsAppConversationEvent::create([
            'conversation_id' => $conversation->id,
            'contact_id' => $conversation->contact_id,
            'type' => $type,
            'title' => $title,
            'description' => $description,
            'meta' => $meta ?: null,
            'performed_by' => $performedBy ?? auth()->id(),
        ]);
    }

    public function updateStatus(WhatsAppConversation $conversation, string $status, ?int $userId = null): WhatsAppConversation
    {
        $old = $conversation->status;
        $updates = ['status' => $status];

        if (in_array($status, [WhatsAppConversation::STATUS_CLOSED, WhatsAppConversation::STATUS_RESOLVED], true)) {
            $updates['closed_at'] = now();
        } elseif ($old !== $status) {
            $updates['closed_at'] = null;
        }

        $conversation->update($updates);

        $this->logEvent(
            $conversation,
            WhatsAppConversationEvent::TYPE_STATUS_CHANGED,
            'تغيير الحالة',
            WhatsAppConversation::statusLabel($old) . ' → ' . WhatsAppConversation::statusLabel($status),
            ['from' => $old, 'to' => $status],
            $userId
        );

        return $conversation->fresh();
    }

    public function transfer(
        WhatsAppConversation $conversation,
        int $toUserId,
        ?string $reason = null,
        ?int $fromUserId = null
    ): WhatsAppConversation {
        $fromUserId ??= $conversation->assigned_to;
        $toUser = User::query()->find($toUserId);
        $fromUser = $fromUserId ? User::query()->find($fromUserId) : null;

        $conversation->update(['assigned_to' => $toUserId]);

        if ($conversation->contact_id) {
            WhatsAppContact::query()
                ->where('id', $conversation->contact_id)
                ->update(['assigned_to' => $toUserId]);
        }

        if ($conversation->sales_lead_id) {
            SalesLead::query()
                ->where('id', $conversation->sales_lead_id)
                ->update(['assigned_to' => $toUserId]);
        }

        $this->logEvent(
            $conversation,
            WhatsAppConversationEvent::TYPE_TRANSFERRED,
            'نقل المحادثة',
            trim(($fromUser?->name ?? 'غير معيّن') . ' → ' . ($toUser?->name ?? '—') . ($reason ? ' — ' . $reason : '')),
            [
                'from_user_id' => $fromUserId,
                'to_user_id' => $toUserId,
                'reason' => $reason,
            ],
            auth()->id()
        );

        return $conversation->fresh(['assignee', 'tags', 'contact', 'salesLead']);
    }

    public function assign(WhatsAppConversation $conversation, int $userId, ?int $performedBy = null): WhatsAppConversation
    {
        $assignee = User::query()->find($userId);
        $conversation->update(['assigned_to' => $userId]);

        if ($conversation->contact_id) {
            WhatsAppContact::query()->where('id', $conversation->contact_id)->update(['assigned_to' => $userId]);
        }

        $this->logEvent(
            $conversation,
            WhatsAppConversationEvent::TYPE_ASSIGNED,
            'تعيين محادثة',
            $assignee?->name,
            ['assigned_to' => $userId],
            $performedBy
        );

        return $conversation->fresh(['assignee', 'tags', 'contact', 'salesLead']);
    }

    public function addNote(WhatsAppConversation $conversation, string $body, ?int $userId = null): WhatsAppConversationNote
    {
        $note = WhatsAppConversationNote::create([
            'conversation_id' => $conversation->id,
            'created_by' => $userId ?? auth()->id(),
            'body' => $body,
        ]);

        $this->logEvent(
            $conversation,
            WhatsAppConversationEvent::TYPE_NOTE_ADDED,
            'ملاحظة داخلية',
            mb_substr($body, 0, 200),
            ['note_id' => $note->id],
            $userId
        );

        return $note->load('author:id,name');
    }

    public function syncTag(WhatsAppConversation $conversation, int $tagId, bool $attach, ?int $userId = null): void
    {
        $tag = WhatsAppTag::query()->findOrFail($tagId);

        if ($attach) {
            $conversation->tags()->syncWithoutDetaching([
                $tagId => ['tagged_by' => $userId ?? auth()->id()],
            ]);

            $this->logEvent(
                $conversation,
                WhatsAppConversationEvent::TYPE_TAG_ADDED,
                'وسم',
                $tag->name,
                ['tag_id' => $tagId],
                $userId
            );
        } else {
            $conversation->tags()->detach($tagId);

            $this->logEvent(
                $conversation,
                WhatsAppConversationEvent::TYPE_TAG_REMOVED,
                'إزالة وسم',
                $tag->name,
                ['tag_id' => $tagId],
                $userId
            );
        }
    }

    public function touchContactActivity(WhatsAppConversation $conversation): void
    {
        if (! $conversation->contact_id) {
            return;
        }

        WhatsAppContact::query()
            ->where('id', $conversation->contact_id)
            ->update(['last_contacted_at' => now()]);
    }

    public function logOutboundToSalesLead(WhatsAppConversation $conversation, string $preview, ?int $userId = null): void
    {
        if (! $conversation->sales_lead_id) {
            return;
        }

        SalesActivity::create([
            'sales_lead_id' => $conversation->sales_lead_id,
            'user_id' => $userId ?? auth()->id(),
            'type' => 'whatsapp',
            'title' => 'رد واتساب من المحادثات',
            'body' => mb_substr($preview, 0, 500),
            'meta' => ['conversation_id' => $conversation->id],
        ]);

        SalesLead::query()
            ->where('id', $conversation->sales_lead_id)
            ->update(['last_contacted_at' => now()]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function timeline(WhatsAppConversation $conversation, int $limit = 50): array
    {
        $events = $conversation->events()
            ->with('performer:id,name')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return $events->map(fn (WhatsAppConversationEvent $e) => [
            'id' => $e->id,
            'type' => $e->type,
            'title' => $e->title,
            'description' => $e->description,
            'performed_by' => $e->performer?->name,
            'created_at' => $e->created_at?->format('Y-m-d H:i'),
            'created_at_human' => $e->created_at?->diffForHumans(),
            'meta' => $e->meta,
        ])->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function serializeCrm(WhatsAppConversation $conversation): array
    {
        $conversation->loadMissing(['assignee:id,name', 'tags', 'contact', 'salesLead']);

        return [
            'status' => $conversation->status,
            'status_label' => WhatsAppConversation::statusLabel($conversation->status),
            'department' => $conversation->department,
            'department_label' => WhatsAppConversation::departmentLabel($conversation->department),
            'priority' => $conversation->priority,
            'assigned_to' => $conversation->assigned_to,
            'assignee_name' => $conversation->assignee?->name,
            'sales_lead_id' => $conversation->sales_lead_id,
            'sales_lead_name' => $conversation->salesLead?->name,
            'sales_lead_stage' => $conversation->salesLead?->stage,
            'sales_lead_url' => $conversation->sales_lead_id
                ? route('admin.sales.leads.show', $conversation->sales_lead_id)
                : null,
            'tags' => $conversation->tags->map(fn (WhatsAppTag $t) => [
                'id' => $t->id,
                'name' => $t->name,
                'slug' => $t->slug,
                'color' => $t->color,
            ])->values()->all(),
            'contact' => $conversation->contact ? [
                'id' => $conversation->contact->id,
                'name' => $conversation->contact->displayName(),
                'email' => $conversation->contact->email,
                'company' => $conversation->contact->company,
                'source' => $conversation->contact->source,
                'lifetime_value' => $conversation->contact->lifetime_value,
                'last_contacted_at' => $conversation->contact->last_contacted_at?->diffForHumans(),
            ] : null,
        ];
    }
}
