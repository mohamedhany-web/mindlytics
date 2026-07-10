<?php

namespace App\Services;

use App\Models\SalesActivity;
use App\Models\SalesLead;
use App\Models\SalesLeadCategory;
use App\Models\User;
use App\Models\WhatsAppContact;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppConversationEvent;
use App\Models\WhatsAppConversationMessage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WhatsAppQueueService
{
    public function __construct(
        private WhatsAppCrmService $crm,
        private SalesNotificationService $notifications,
    ) {}

    public function queueEnabled(): bool
    {
        return (bool) config('whatsapp.inbox_queue.enabled', true)
            && config('whatsapp.assignment.strategy') === 'manual_queue';
    }

    public function isInQueue(WhatsAppConversation $conversation): bool
    {
        if (! $this->queueEnabled()) {
            return false;
        }

        if ($this->assigneeId($conversation->assigned_to) !== null) {
            return false;
        }

        if (! in_array($conversation->status, [null, '', WhatsAppConversation::STATUS_OPEN, WhatsAppConversation::STATUS_PENDING], true)) {
            return false;
        }

        $department = $conversation->department ?? 'sales';
        if ($department !== 'sales') {
            return false;
        }

        $conversation->loadMissing('salesLead');

        if ($conversation->sales_lead_id && $this->assigneeId($conversation->salesLead?->assigned_to) !== null) {
            return false;
        }

        // رقم مربوط بعميل مسند في CRM حتى لو المحادثة غير مربوطة بعد
        if ($this->phoneHasAssignedLead($conversation->phone_number)) {
            return false;
        }

        return true;
    }

    public function pendingCount(): int
    {
        if (! $this->queueEnabled()) {
            return 0;
        }

        return $this->pendingQuery()->count();
    }

    /** @return Builder<WhatsAppConversation> */
    public function pendingQuery(): Builder
    {
        return WhatsAppConversation::query()
            ->inSalesQueue()
            ->whereNotExists(function ($sub) {
                $sub->selectRaw('1')
                    ->from('sales_leads')
                    ->whereNotNull('sales_leads.assigned_to')
                    ->where('sales_leads.assigned_to', '!=', 0)
                    ->whereRaw(
                        "REPLACE(REPLACE(REPLACE(COALESCE(sales_leads.phone,''),'+',''),' ',''),'-','') LIKE CONCAT('%', RIGHT(REPLACE(REPLACE(REPLACE(COALESCE(whatsapp_conversations.phone_number,''),'+',''),' ',''),'-',''), 10))"
                    )
                    ->whereRaw('CHAR_LENGTH(REPLACE(REPLACE(REPLACE(COALESCE(whatsapp_conversations.phone_number,\'\'),\'+\',\'\'),\' \',\'\'),\'-\',\'\')) >= 8');
            })
            ->orderByDesc('last_message_at');
    }

    private function assigneeId(mixed $value): ?int
    {
        $id = (int) ($value ?? 0);

        return $id > 0 ? $id : null;
    }

    private function phoneHasAssignedLead(?string $phone): bool
    {
        $digits = preg_replace('/[^0-9]/', '', (string) $phone);
        if ($digits === '' || strlen($digits) < 8) {
            return false;
        }

        $suffix = substr($digits, -10);

        return SalesLead::query()
            ->whereNotNull('assigned_to')
            ->where('assigned_to', '!=', 0)
            ->where(function ($q) use ($digits, $suffix) {
                $q->where('phone', 'like', '%'.$digits)
                    ->orWhere('phone', 'like', '%'.$suffix)
                    ->orWhere('phone', 'like', '%'.ltrim($digits, '0'));
            })
            ->exists();
    }

    public function handleAfterInbound(WhatsAppConversation $conversation, WhatsAppConversationMessage $message): void
    {
        if (! config('whatsapp.notifications.inbound_messages', true)) {
            return;
        }

        $conversation = $conversation->fresh();

        if ($this->isInQueue($conversation)) {
            if (config('whatsapp.notifications.queue_requests', true)) {
                $this->notifications->notifyWhatsAppQueueRequest($conversation, $message);
            }

            return;
        }

        if ($conversation->assigned_to) {
            $assignee = User::query()->find($conversation->assigned_to);
            if ($assignee?->isSalesStaff()) {
                $this->notifications->notifyWhatsAppInboundMessage($assignee, $conversation, $message);
            }
        }
    }

    public function claim(WhatsAppConversation $conversation, User $user): SalesLead
    {
        if (! $user->isSalesStaff()) {
            abort(403, 'هذه العملية لموظفي المبيعات فقط.');
        }

        return DB::transaction(function () use ($conversation, $user) {
            /** @var WhatsAppConversation $locked */
            $locked = WhatsAppConversation::query()
                ->whereKey($conversation->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->assigned_to) {
                if ((int) $locked->assigned_to === (int) $user->id && $locked->sales_lead_id) {
                    return SalesLead::query()->findOrFail($locked->sales_lead_id);
                }

                throw ValidationException::withMessages([
                    'conversation' => 'تم قبول هذا الطلب من موظف آخر.',
                ]);
            }

            if (! $this->isInQueue($locked)) {
                throw ValidationException::withMessages([
                    'conversation' => 'هذه المحادثة غير متاحة في قائمة الانتظار.',
                ]);
            }

            $lead = $locked->sales_lead_id
                ? SalesLead::query()->lockForUpdate()->findOrFail($locked->sales_lead_id)
                : $this->createLeadFromConversation($locked, $user);

            if ((int) $lead->assigned_to !== (int) $user->id) {
                $lead->update(['assigned_to' => $user->id]);
            }

            $this->linkLead($locked, $lead);
            $this->crm->assign($locked, (int) $user->id, (int) $user->id);

            if ($locked->status === WhatsAppConversation::STATUS_PENDING) {
                $locked->update(['status' => WhatsAppConversation::STATUS_OPEN]);
            }

            $this->crm->logEvent(
                $locked->fresh(),
                WhatsAppConversationEvent::TYPE_ASSIGNED,
                'قبول طلب واتساب',
                $user->name.' قبل المحادثة',
                ['lead_id' => $lead->id, 'claimed' => true],
                (int) $user->id
            );

            SalesActivity::create([
                'sales_lead_id' => $lead->id,
                'user_id' => $user->id,
                'type' => 'whatsapp',
                'title' => 'قبول محادثة واتساب',
                'body' => 'تم قبول طلب واتساب من قائمة الانتظار',
                'meta' => ['conversation_id' => $locked->id],
            ]);

            $this->notifications->notifyLeadAssigned($lead->fresh(['assignee', 'category']));

            return $lead->fresh();
        });
    }

    private function createLeadFromConversation(WhatsAppConversation $conversation, User $user): SalesLead
    {
        $existing = $this->crm->findLeadByPhone($conversation->phone_number);
        if ($existing) {
            if (! $existing->assigned_to) {
                $existing->update(['assigned_to' => $user->id]);
            }

            return $existing->fresh();
        }

        return SalesLead::create([
            'name' => $conversation->displayName(),
            'phone' => '+'.ltrim($conversation->phone_number, '+'),
            'source' => 'whatsapp',
            'stage' => 'new',
            'priority' => 'normal',
            'assigned_to' => $user->id,
            'created_by' => $user->id,
            'category_id' => SalesLeadCategory::defaultGeneralId(),
            'last_contacted_at' => $conversation->last_message_at ?? now(),
            'notes' => 'أُنشئ تلقائياً من طابور واتساب.',
        ]);
    }

    private function linkLead(WhatsAppConversation $conversation, SalesLead $lead): void
    {
        $conversation->update(['sales_lead_id' => $lead->id]);

        if ($conversation->contact_id) {
            WhatsAppContact::query()
                ->where('id', $conversation->contact_id)
                ->update([
                    'sales_lead_id' => $lead->id,
                    'assigned_to' => $lead->assigned_to,
                ]);
        }

        $this->crm->logEvent(
            $conversation,
            WhatsAppConversationEvent::TYPE_LEAD_LINKED,
            'ربط بعميل مبيعات',
            $lead->name,
            ['sales_lead_id' => $lead->id]
        );
    }

    public function inboxUrlFor(User $user, WhatsAppConversation $conversation): string
    {
        return $this->inboxIndexUrlFor($user, ['conversation' => $conversation->id]);
    }

    public function inboxIndexUrlFor(User $user, array $params = []): string
    {
        if ($user->isSalesManager()) {
            return route('employee.sales-manager.whatsapp.inbox.index', $params);
        }

        return route('employee.sales.whatsapp.inbox.index', $params);
    }
}
