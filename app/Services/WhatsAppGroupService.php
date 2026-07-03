<?php

namespace App\Services;

use App\Models\SalesLead;
use App\Models\SalesLeadGroup;
use App\Models\WhatsAppGroup;
use App\Models\WhatsAppGroupParticipant;
use Illuminate\Support\Collection;

class WhatsAppGroupService
{
    public function __construct(
        private WhatsAppCloudGroupService $cloudGroups,
        private WhatsAppCloudService $cloud,
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
        string $joinApprovalMode = 'auto_approve',
        ?string $inviteTemplateName = null,
        string $inviteTemplateLanguage = 'en',
    ): array {
        $conn = $this->cloudGroups->connectionStatus();
        if (! ($conn['connected'] ?? false)) {
            return ['success' => false, 'error' => $conn['error'] ?? 'Meta Cloud غير جاهز.'];
        }

        $group = WhatsAppGroup::create([
            'sales_lead_group_id' => $salesLeadGroupId,
            'created_by' => $userId,
            'assigned_to' => $userId,
            'subject' => $subject,
            'description' => $description,
            'announce_only' => $announceOnly,
            'restrict_info' => $restrictInfo,
            'join_approval_mode' => $joinApprovalMode,
            'invite_template_name' => $inviteTemplateName,
            'invite_template_language' => $inviteTemplateLanguage,
            'api_provider' => 'meta_cloud',
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

        $result = $this->cloudGroups->createGroup($subject, $description, $joinApprovalMode);

        if (! ($result['success'] ?? false)) {
            $group->update([
                'status' => WhatsAppGroup::STATUS_FAILED,
                'bridge_error' => $result['error'] ?? 'فشل الإنشاء',
            ]);

            return ['success' => false, 'error' => $result['error'] ?? 'فشل إنشاء المجموعة', 'group' => $group];
        }

        $group->update([
            'wa_group_jid' => (string) ($result['group_id'] ?? ''),
            'invite_link' => $result['invite_link'] ?? null,
            'status' => WhatsAppGroup::STATUS_ACTIVE,
            'bridge_error' => null,
            'last_synced_at' => now(),
        ]);

        $group = $group->fresh(['participants', 'salesLeadGroup']);

        if ($inviteTemplateName && $group->participants->isNotEmpty()) {
            $inviteResult = $this->sendInvites($group, $inviteTemplateName, $inviteTemplateLanguage);
            if (! ($inviteResult['success'] ?? false) && ($inviteResult['sent'] ?? 0) === 0) {
                return [
                    'success' => true,
                    'group' => $group->fresh(['participants', 'salesLeadGroup']),
                    'warning' => $inviteResult['error'] ?? 'تم إنشاء المجموعة لكن فشل إرسال الدعوات.',
                ];
            }
        }

        return ['success' => true, 'group' => $group->fresh(['participants', 'salesLeadGroup'])];
    }

    /**
     * @param  array<int, array{phone: string, sales_lead_id?: int|null, display_name?: string|null}>  $participantRows
     * @return array{success: bool, error?: string, added?: int, sent?: int}
     */
    public function addParticipants(
        WhatsAppGroup $group,
        array $participantRows,
        ?string $templateName = null,
        ?string $templateLanguage = null,
    ): array {
        if (! $group->wa_group_jid) {
            return ['success' => false, 'error' => 'المجموعة غير منشأة على Meta بعد.'];
        }

        $templateName = $templateName ?: $group->invite_template_name;
        $templateLanguage = $templateLanguage ?: $group->invite_template_language ?: 'en';

        if (! $templateName) {
            return ['success' => false, 'error' => 'اختر قالب Group Invite معتمداً لإرسال الدعوات.'];
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

        $group->update([
            'invite_template_name' => $templateName,
            'invite_template_language' => $templateLanguage,
        ]);

        return $this->sendInvites($group->fresh('participants'), $templateName, $templateLanguage, $phones);
    }

    /**
     * @param  array<int, string>|null  $onlyPhones
     * @return array{success: bool, error?: string, sent?: int, failed?: int}
     */
    public function sendInvites(
        WhatsAppGroup $group,
        string $templateName,
        string $templateLanguage = 'en',
        ?array $onlyPhones = null,
    ): array {
        if (! $group->wa_group_jid) {
            return ['success' => false, 'error' => 'لا يوجد معرّف مجموعة على Meta.'];
        }

        $participants = $group->participants;
        if ($onlyPhones !== null) {
            $participants = $participants->whereIn('phone', $onlyPhones);
        }

        $sent = 0;
        $failed = 0;
        $lastError = null;

        foreach ($participants as $participant) {
            if (in_array($participant->status, [WhatsAppGroupParticipant::STATUS_JOINED, WhatsAppGroupParticipant::STATUS_REMOVED], true)) {
                continue;
            }

            $result = $this->cloudGroups->sendGroupInviteTemplate(
                $participant->phone,
                $group->wa_group_jid,
                $templateName,
                $templateLanguage,
            );

            if ($result['success'] ?? false) {
                $sent++;
                $participant->update([
                    'status' => WhatsAppGroupParticipant::STATUS_INVITED,
                    'invited_at' => now(),
                    'error_message' => null,
                ]);
            } else {
                $failed++;
                $lastError = $result['error'] ?? 'فشل الإرسال';
                $participant->update([
                    'status' => WhatsAppGroupParticipant::STATUS_FAILED,
                    'error_message' => $lastError,
                ]);
            }
        }

        $group->update(['last_synced_at' => now()]);

        if ($sent === 0) {
            return ['success' => false, 'error' => $lastError ?? 'فشل إرسال الدعوات', 'sent' => 0, 'failed' => $failed];
        }

        return ['success' => true, 'sent' => $sent, 'failed' => $failed];
    }

    public function updateSettings(WhatsAppGroup $group, array $data): array
    {
        if (! $group->wa_group_jid) {
            return ['success' => false, 'error' => 'المجموعة غير منشأة على Meta.'];
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

        if ($payload === []) {
            return ['success' => true];
        }

        $result = $this->cloudGroups->updateGroup($group->wa_group_jid, $payload);
        if (! ($result['success'] ?? false)) {
            return ['success' => false, 'error' => $result['error'] ?? 'فشل التحديث'];
        }

        if (array_key_exists('join_approval_mode', $data)) {
            $group->join_approval_mode = (string) $data['join_approval_mode'];
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

        $result = $this->cloudGroups->resetInviteLink($group->wa_group_jid);
        if (! ($result['success'] ?? false)) {
            return $result;
        }

        $group->update([
            'invite_link' => $result['invite_link'] ?? null,
            'last_synced_at' => now(),
        ]);

        return $result;
    }

    public function syncFromCloud(WhatsAppGroup $group): array
    {
        if (! $group->wa_group_jid) {
            return ['success' => false, 'error' => 'لا يوجد معرّف مجموعة.'];
        }

        $result = $this->cloudGroups->getGroup($group->wa_group_jid);
        if (! ($result['success'] ?? false)) {
            return $result;
        }

        $wa = is_array($result['group'] ?? null) ? $result['group'] : [];
        $group->update([
            'subject' => (string) ($wa['subject'] ?? $group->subject),
            'description' => (string) ($wa['description'] ?? $group->description),
            'join_approval_mode' => (string) ($wa['join_approval_mode'] ?? $group->join_approval_mode),
            'invite_link' => $result['invite_link'] ?? $group->invite_link,
            'last_synced_at' => now(),
        ]);

        $participantWaIds = collect($wa['participants'] ?? [])
            ->map(fn ($p) => is_array($p) ? (string) ($p['wa_id'] ?? '') : '')
            ->filter()
            ->values();

        foreach ($participantWaIds as $waId) {
            $phone = $this->whatsapp->formatPhoneNumber($waId);
            $participant = $group->participants()
                ->where(function ($q) use ($phone, $waId) {
                    $q->where('phone', $phone)->orWhere('phone', $waId);
                })
                ->first();

            if ($participant) {
                $participant->update([
                    'status' => WhatsAppGroupParticipant::STATUS_JOINED,
                    'joined_at' => $participant->joined_at ?? now(),
                ]);
            }
        }

        return ['success' => true, 'group' => $group->fresh()];
    }

    public function removeParticipant(WhatsAppGroup $group, WhatsAppGroupParticipant $participant): array
    {
        if (! $group->wa_group_jid) {
            return ['success' => false, 'error' => 'المجموعة غير نشطة على Meta.'];
        }

        $result = $this->cloudGroups->removeParticipants($group->wa_group_jid, [$participant->phone]);
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

        $result = $this->cloudGroups->deleteGroup($group->wa_group_jid);
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

    /**
     * @return array{success: bool, connected?: bool, error?: ?string, label?: string, notes?: array<int, string>}
     */
    public function cloudStatus(): array
    {
        return $this->cloudGroups->connectionStatus();
    }

    /**
     * @return array{success: bool, templates: array<int, array<string, mixed>>, error?: string}
     */
    public function inviteTemplates(): array
    {
        $result = $this->cloud->listApprovedTemplates();
        if (! ($result['success'] ?? false)) {
            return $result;
        }

        $templates = collect($result['templates'] ?? [])
            ->filter(function ($t) {
                $category = strtoupper((string) ($t['category'] ?? ''));
                $name = strtolower((string) ($t['name'] ?? ''));

                return $category === 'UTILITY' || str_contains($name, 'group') || str_contains($name, 'invite');
            })
            ->values()
            ->all();

        if ($templates === []) {
            $templates = $result['templates'] ?? [];
        }

        return ['success' => true, 'templates' => $templates];
    }

    /** @deprecated use cloudStatus() */
    public function bridgeStatus(): array
    {
        return $this->cloudStatus();
    }

    /** @deprecated use syncFromCloud() */
    public function syncFromBridge(WhatsAppGroup $group): array
    {
        return $this->syncFromCloud($group);
    }
}
