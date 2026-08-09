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
        private SalesTeamService $teams,
    ) {}

    public function queueEnabled(): bool
    {
        return (bool) config('whatsapp.inbox_queue.enabled', true)
            && config('whatsapp.assignment.strategy') === 'manual_queue';
    }

    public function hasCustomerReply(WhatsAppConversation $conversation): bool
    {
        if ($conversation->last_message_direction === WhatsAppConversationMessage::DIRECTION_INBOUND) {
            return true;
        }

        return $conversation->messages()
            ->where('direction', WhatsAppConversationMessage::DIRECTION_INBOUND)
            ->exists();
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

        if (! $this->hasCustomerReply($conversation)) {
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

    /**
     * مدير المبيعات يوزّع الطلب على موظف مبيعات من فريقه.
     */
    public function assignToSalesRep(WhatsAppConversation $conversation, User $manager, User $assignee): SalesLead
    {
        if (! $manager->isSalesManager()) {
            abort(403, 'توزيع طلبات واتساب لمديري المبيعات فقط.');
        }

        $team = $this->teams->managedTeamOrFail($manager);
        $memberIds = $this->teams->memberUserIds($team);

        if (! in_array((int) $assignee->id, $memberIds, true) || ! $assignee->isSalesEmployee()) {
            throw ValidationException::withMessages([
                'assigned_to' => 'يجب توزيع الطلب على عضو مبيعات من فريقك.',
            ]);
        }

        return DB::transaction(function () use ($conversation, $manager, $assignee) {
            /** @var WhatsAppConversation $locked */
            $locked = WhatsAppConversation::query()
                ->whereKey($conversation->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->assigned_to) {
                if ((int) $locked->assigned_to === (int) $assignee->id && $locked->sales_lead_id) {
                    return SalesLead::query()->findOrFail($locked->sales_lead_id);
                }

                throw ValidationException::withMessages([
                    'conversation' => 'تم توزيع هذا الطلب مسبقاً على موظف آخر.',
                ]);
            }

            if (! $this->isInQueue($locked)) {
                throw ValidationException::withMessages([
                    'conversation' => 'هذه المحادثة غير متاحة في قائمة الانتظار (يظهر فقط من ردّ على الواتساب).',
                ]);
            }

            $lead = $locked->sales_lead_id
                ? SalesLead::query()->lockForUpdate()->findOrFail($locked->sales_lead_id)
                : $this->createLeadFromConversation($locked, $assignee, $manager);

            if ((int) $lead->assigned_to !== (int) $assignee->id) {
                $lead->update(['assigned_to' => $assignee->id]);
            }

            $this->linkLead($locked, $lead);
            $this->crm->assign($locked, (int) $assignee->id, (int) $manager->id);

            if ($locked->status === WhatsAppConversation::STATUS_PENDING) {
                $locked->update(['status' => WhatsAppConversation::STATUS_OPEN]);
            }

            $this->crm->logEvent(
                $locked->fresh(),
                WhatsAppConversationEvent::TYPE_ASSIGNED,
                'توزيع طلب واتساب',
                $manager->name.' وزّع المحادثة على '.$assignee->name,
                [
                    'lead_id' => $lead->id,
                    'assigned_by_manager' => true,
                    'assignee_id' => $assignee->id,
                ],
                (int) $manager->id
            );

            SalesActivity::create([
                'sales_lead_id' => $lead->id,
                'user_id' => $manager->id,
                'type' => 'whatsapp',
                'title' => 'توزيع محادثة واتساب',
                'body' => 'وزّع المدير الطلب على '.$assignee->name,
                'meta' => [
                    'conversation_id' => $locked->id,
                    'assignee_id' => $assignee->id,
                ],
            ]);

            $this->notifications->notifyLeadAssigned($lead->fresh(['assignee', 'category']));

            return $lead->fresh();
        });
    }

    private function createLeadFromConversation(WhatsAppConversation $conversation, User $assignee, User $createdBy): SalesLead
    {
        $existing = $this->crm->findLeadByPhone($conversation->phone_number);
        if ($existing) {
            if (! $existing->assigned_to) {
                $existing->update(['assigned_to' => $assignee->id]);
            }

            return $existing->fresh();
        }

        return SalesLead::create(app(SalesLeadMovementPolicy::class)->withCreateDefaults([
            'name' => $conversation->displayName(),
            'phone' => '+'.ltrim($conversation->phone_number, '+'),
            'source' => 'whatsapp',
            'stage' => 'new_lead',
            'priority' => 'normal',
            'assigned_to' => $assignee->id,
            'created_by' => $createdBy->id,
            'category_id' => SalesLeadCategory::defaultGeneralId(),
            'last_contacted_at' => $conversation->last_message_at ?? now(),
            'notes' => 'أُنشئ تلقائياً من طابور واتساب (توزيع المدير).',
        ]));
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
