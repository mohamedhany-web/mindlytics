<?php

namespace App\Services;

use App\Models\WhatsAppGroup;
use App\Models\WhatsAppGroupParticipant;
use Illuminate\Support\Facades\Log;

class WhatsAppGroupWebhookService
{
    public function __construct(
        private WhatsAppService $whatsapp,
    ) {}

    /**
     * @param  array<string, mixed>  $value
     */
    public function handleChangeValue(array $value): void
    {
        if (isset($value['group_lifecycle_update'])) {
            $this->handleLifecycle($value['group_lifecycle_update'], $value);
        }

        if (isset($value['group_participants_update'])) {
            $this->handleParticipants($value['group_participants_update'], $value);
        }

        if (isset($value['group_settings_update'])) {
            $this->handleSettings($value['group_settings_update'], $value);
        }
    }

    /**
     * @param  array<string, mixed>  $event
     * @param  array<string, mixed>  $value
     */
    private function handleLifecycle(array $event, array $value): void
    {
        $groupId = (string) ($event['group_id'] ?? $value['group_id'] ?? '');
        if ($groupId === '') {
            return;
        }

        $group = WhatsAppGroup::query()->where('wa_group_jid', $groupId)->first();
        if (! $group) {
            return;
        }

        $type = (string) ($event['type'] ?? $event['event'] ?? '');
        $inviteLink = (string) ($event['invite_link'] ?? '');

        $updates = ['last_synced_at' => now()];

        if ($inviteLink !== '') {
            $updates['invite_link'] = $inviteLink;
        }

        if (str_contains(strtolower($type), 'fail')) {
            $updates['status'] = WhatsAppGroup::STATUS_FAILED;
            $updates['bridge_error'] = (string) ($event['error'] ?? $event['errors'][0]['message'] ?? 'فشل إنشاء المجموعة');
        } elseif (str_contains(strtolower($type), 'delete') || str_contains(strtolower($type), 'removed')) {
            $updates['status'] = WhatsAppGroup::STATUS_LEFT;
        } elseif ($inviteLink !== '' || str_contains(strtolower($type), 'create')) {
            $updates['status'] = WhatsAppGroup::STATUS_ACTIVE;
            $updates['bridge_error'] = null;
        }

        $group->update($updates);

        Log::info('WhatsApp group lifecycle webhook', ['group_id' => $groupId, 'type' => $type]);
    }

    /**
     * @param  array<string, mixed>  $event
     * @param  array<string, mixed>  $value
     */
    private function handleParticipants(array $event, array $value): void
    {
        $groupId = (string) ($event['group_id'] ?? $value['group_id'] ?? '');
        if ($groupId === '') {
            return;
        }

        $group = WhatsAppGroup::query()->where('wa_group_jid', $groupId)->first();
        if (! $group) {
            return;
        }

        $participants = $event['participants'] ?? $event['participant'] ?? [];
        if (! is_array($participants)) {
            $participants = [$participants];
        }

        foreach ($participants as $row) {
            if (! is_array($row)) {
                continue;
            }

            $waId = (string) ($row['wa_id'] ?? $row['user'] ?? $row['phone'] ?? '');
            if ($waId === '') {
                continue;
            }

            $action = strtolower((string) ($row['action'] ?? $event['type'] ?? 'join'));
            $phone = $this->whatsapp->formatPhoneNumber($waId);

            $participant = $group->participants()
                ->where(function ($q) use ($phone, $waId) {
                    $q->where('phone', $phone)->orWhere('phone', $waId);
                })
                ->first();

            if (! $participant) {
                $participant = WhatsAppGroupParticipant::create([
                    'whatsapp_group_id' => $group->id,
                    'phone' => $phone !== '' ? $phone : $waId,
                    'status' => WhatsAppGroupParticipant::STATUS_PENDING,
                ]);
            }

            if (str_contains($action, 'remove') || str_contains($action, 'leave')) {
                $participant->update(['status' => WhatsAppGroupParticipant::STATUS_REMOVED]);
            } else {
                $participant->update([
                    'status' => WhatsAppGroupParticipant::STATUS_JOINED,
                    'joined_at' => now(),
                    'error_message' => null,
                ]);
            }
        }

        $group->update(['last_synced_at' => now()]);
    }

    /**
     * @param  array<string, mixed>  $event
     * @param  array<string, mixed>  $value
     */
    private function handleSettings(array $event, array $value): void
    {
        $groupId = (string) ($event['group_id'] ?? $value['group_id'] ?? '');
        if ($groupId === '') {
            return;
        }

        $group = WhatsAppGroup::query()->where('wa_group_jid', $groupId)->first();
        if (! $group) {
            return;
        }

        $updates = ['last_synced_at' => now()];

        if (isset($event['subject'])) {
            $updates['subject'] = (string) $event['subject'];
        }
        if (isset($event['description'])) {
            $updates['description'] = (string) $event['description'];
        }
        if (isset($event['join_approval_mode'])) {
            $updates['join_approval_mode'] = (string) $event['join_approval_mode'];
        }

        $group->update($updates);
    }
}
