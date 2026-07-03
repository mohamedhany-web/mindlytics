<?php

namespace App\Services;

use App\Models\SalesLead;
use App\Models\SalesLeadGroup;
use App\Models\WhatsAppGroup;
use App\Models\WhatsAppGroupParticipant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WhatsAppGroupService
{
    public function __construct(
        private WhatsAppGroupBridgeService $bridge,
        private WhatsAppService $whatsapp,
    ) {}

    /**
     * @return array{success: bool, error?: string, group?: WhatsAppGroup}
     */
    public function createAndProvision(
        string $subject,
        array $participantRows,
        int $userId,
        ?int $salesLeadGroupId = null,
        ?string $description = null,
        bool $announceOnly = false,
        bool $restrictInfo = false,
    ): array {
        $conn = $this->bridge->connectionStatus();
        if (! ($conn['connected'] ?? false)) {
            return ['success' => false, 'error' => $conn['error'] ?? 'جلسة الواتساب غير متصلة على الجسر.'];
        }

        $phones = collect($participantRows)->pluck('phone')->filter()->unique()->values()->all();
        if (count($phones) < 1) {
            return ['success' => false, 'error' => 'أضف رقماً واحداً على الأقل.'];
        }

        $group = WhatsAppGroup::create([
            'sales_lead_group_id' => $salesLeadGroupId,
            'created_by' => $userId,
            'assigned_to' => $userId,
            'subject' => $subject,
            'description' => $description,
            'announce_only' => $announceOnly,
            'restrict_info' => $restrictInfo,
            'status' => WhatsAppGroup::STATUS_CREATING,
        ]);

        foreach ($participantRows as $row) {
            $phone = $this->whatsapp->formatPhoneNumber((string) ($row['phone'] ?? ''));
            if ($phone === '') {
                continue;
            }
            WhatsAppGroupParticipant::create([
                'whatsapp_group_id' => $group->id,
                'sales_lead_id' => $row['sales_lead_id'] ?? null,
                'phone' => $phone,
                'display_name' => $row['display_name'] ?? null,
                'status' => WhatsAppGroupParticipant::STATUS_PENDING,
            ]);
        }

        $result = $this->bridge->createGroup($subject, $phones, $description, $announceOnly, $restrictInfo);

        if (! ($result['success'] ?? false)) {
            $group->update([
                'status' => WhatsAppGroup::STATUS_FAILED,
                'bridge_error' => $result['error'] ?? 'فشل الإنشاء',
            ]);

            return ['success' => false, 'error' => $result['error'] ?? 'فشل إنشاء المجموعة', 'group' => $group];
        }

        $waGroup = is_array($result['group'] ?? null) ? $result['group'] : [];
        $group->update([
            'wa_group_jid' => (string) ($waGroup['jid'] ?? ''),
            'invite_link' => $result['invite_link'] ?? null,
            'status' => WhatsAppGroup::STATUS_ACTIVE,
            'bridge_error' => null,
            'last_synced_at' => now(),
        ]);

        $group->participants()->update([
            'status' => WhatsAppGroupParticipant::STATUS_ADDED,
            'error_message' => null,
        ]);

        return ['success' => true, 'group' => $group->fresh(['participants', 'salesLeadGroup'])];
    }

    /**
     * @param  array<int, array{phone: string, sales_lead_id?: int|null, display_name?: string|null}>  $participantRows
     * @return array{success: bool, error?: string, added?: int}
     */
    public function addParticipants(WhatsAppGroup $group, array $participantRows): array
    {
        if (! $group->wa_group_jid) {
            return ['success' => false, 'error' => 'المجموعة غير منشأة على واتساب بعد.'];
        }

        $phones = [];
        foreach ($participantRows as $row) {
            $phone = $this->whatsapp->formatPhoneNumber((string) ($row['phone'] ?? ''));
            if ($phone === '') {
                continue;
            }
            $phones[] = $phone;
            WhatsAppGroupParticipant::updateOrCreate(
                ['whatsapp_group_id' => $group->id, 'phone' => $phone],
                [
                    'sales_lead_id' => $row['sales_lead_id'] ?? null,
                    'display_name' => $row['display_name'] ?? null,
                    'status' => WhatsAppGroupParticipant::STATUS_PENDING,
                ]
            );
        }

        if ($phones === []) {
            return ['success' => false, 'error' => 'لا توجد أرقام صالحة.'];
        }

        $result = $this->bridge->addParticipants($group->wa_group_jid, $phones);
        if (! ($result['success'] ?? false)) {
            $group->participants()->whereIn('phone', $phones)->update([
                'status' => WhatsAppGroupParticipant::STATUS_FAILED,
                'error_message' => $result['error'] ?? null,
            ]);

            return ['success' => false, 'error' => $result['error'] ?? 'فشل الإضافة'];
        }

        $group->participants()->whereIn('phone', $phones)->update([
            'status' => WhatsAppGroupParticipant::STATUS_ADDED,
            'error_message' => null,
        ]);
        $group->update(['last_synced_at' => now()]);

        return ['success' => true, 'added' => count($phones)];
    }

    public function updateSettings(WhatsAppGroup $group, array $data): array
    {
        if (! $group->wa_group_jid) {
            return ['success' => false, 'error' => 'المجموعة غير منشأة على واتساب.'];
        }

        $payload = [];
        if (array_key_exists('subject', $data) && trim((string) $data['subject']) !== '') {
            $payload['subject'] = trim((string) $data['subject']);
            $group->subject = $payload['subject'];
        }
        if (array_key_exists('description', $data)) {
            $payload['description'] = (string) ($data['description'] ?? '');
            $group->description = $payload['description'];
        }
        if (array_key_exists('announce_only', $data)) {
            $payload['announce_only'] = (bool) $data['announce_only'];
            $group->announce_only = $payload['announce_only'];
        }
        if (array_key_exists('restrict_info', $data)) {
            $payload['restrict'] = (bool) $data['restrict_info'];
            $group->restrict_info = $payload['restrict_info'];
        }

        $result = $this->bridge->updateGroup($group->wa_group_jid, $payload);
        if (! ($result['success'] ?? false)) {
            return ['success' => false, 'error' => $result['error'] ?? 'فشل التحديث'];
        }

        $group->last_synced_at = now();
        $group->save();

        return ['success' => true];
    }

    public function refreshInviteLink(WhatsAppGroup $group): array
    {
        if (! $group->wa_group_jid) {
            return ['success' => false, 'error' => 'لا يوجد معرّف مجموعة.'];
        }

        $result = $this->bridge->inviteLink($group->wa_group_jid);
        if (! ($result['success'] ?? false)) {
            return $result;
        }

        $group->update([
            'invite_link' => $result['invite_link'] ?? null,
            'last_synced_at' => now(),
        ]);

        return $result;
    }

    public function syncFromBridge(WhatsAppGroup $group): array
    {
        if (! $group->wa_group_jid) {
            return ['success' => false, 'error' => 'لا يوجد معرّف مجموعة.'];
        }

        $result = $this->bridge->getGroup($group->wa_group_jid);
        if (! ($result['success'] ?? false)) {
            return $result;
        }

        $wa = is_array($result['group'] ?? null) ? $result['group'] : [];
        $group->update([
            'subject' => (string) ($wa['subject'] ?? $group->subject),
            'description' => (string) ($wa['description'] ?? $group->description),
            'announce_only' => (bool) ($wa['announce_only'] ?? $group->announce_only),
            'restrict_info' => (bool) ($wa['restrict'] ?? $group->restrict_info),
            'invite_link' => $result['invite_link'] ?? $group->invite_link,
            'last_synced_at' => now(),
        ]);

        return ['success' => true, 'group' => $group->fresh()];
    }

    public function removeParticipant(WhatsAppGroup $group, WhatsAppGroupParticipant $participant): array
    {
        if (! $group->wa_group_jid) {
            return ['success' => false, 'error' => 'المجموعة غير نشطة على واتساب.'];
        }

        $result = $this->bridge->removeParticipants($group->wa_group_jid, [$participant->phone]);
        if (! ($result['success'] ?? false)) {
            return $result;
        }

        $participant->update(['status' => WhatsAppGroupParticipant::STATUS_REMOVED]);
        $group->update(['last_synced_at' => now()]);

        return ['success' => true];
    }

    public function leave(WhatsAppGroup $group): array
    {
        if (! $group->wa_group_jid) {
            $group->update(['status' => WhatsAppGroup::STATUS_LEFT]);

            return ['success' => true];
        }

        $result = $this->bridge->leaveGroup($group->wa_group_jid);
        if (! ($result['success'] ?? false)) {
            return $result;
        }

        $group->update(['status' => WhatsAppGroup::STATUS_LEFT, 'last_synced_at' => now()]);

        return ['success' => true];
    }

    /**
     * @return Collection<int, array{phone: string, sales_lead_id: int, display_name: string}>
     */
    public function participantsFromSalesLeadGroup(SalesLeadGroup $crmGroup, ?int $assigneeId = null): Collection
    {
        $query = $crmGroup->leads()->whereNotNull('phone')->where('phone', '!=', '');
        if ($assigneeId) {
            $query->where('assigned_to', $assigneeId);
        }

        return $query->get(['id', 'name', 'phone'])->map(fn (SalesLead $lead) => [
            'phone' => $this->whatsapp->formatPhoneNumber($lead->phone),
            'sales_lead_id' => $lead->id,
            'display_name' => $lead->name,
        ])->filter(fn ($r) => $r['phone'] !== '');
    }

    public function bridgeStatus(): array
    {
        return $this->bridge->connectionStatus();
    }
}
