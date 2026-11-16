<?php

namespace App\Domains\Messaging\Services;

use App\Domains\Marketing\Models\AdvertisingCampaign;
use App\Domains\Messaging\Models\MetaSocialConversation;
use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Models\SalesLeadCategory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MetaCampaignLeadSyncService
{
    public function __construct(
        private MetaAdsGraphService $metaAds,
        private MetaSocialCrmService $crm,
    ) {}

    /**
     * @return array{
     *   success: bool,
     *   message: string,
     *   created?: int,
     *   updated?: int,
     *   skipped?: int,
     *   conversations_linked?: int,
     *   errors?: list<string>,
     *   advertising_campaign_id?: int|null
     * }
     */
    public function syncByMetaCampaignId(
        string $metaCampaignId,
        ?AdvertisingCampaign $advertisingCampaign = null,
        ?string $metaCampaignName = null,
        ?int $actorUserId = null,
    ): array {
        $metaCampaignId = trim($metaCampaignId);
        if ($metaCampaignId === '') {
            return ['success' => false, 'message' => 'معرّف كامبين ميتا مطلوب.'];
        }

        if ($advertisingCampaign === null) {
            $advertisingCampaign = AdvertisingCampaign::query()
                ->where('meta_campaign_id', $metaCampaignId)
                ->first();
        }

        if ($advertisingCampaign && blank($advertisingCampaign->meta_campaign_id)) {
            $advertisingCampaign->meta_campaign_id = $metaCampaignId;
            if ($metaCampaignName) {
                $advertisingCampaign->meta_campaign_name = $metaCampaignName;
            }
            $advertisingCampaign->save();
        }

        $adsResult = $this->metaAds->listAdsForCampaign($metaCampaignId);
        if (! ($adsResult['success'] ?? false)) {
            $error = (string) ($adsResult['error'] ?? 'تعذر جلب إعلانات الحملة');
            if ($this->looksLikePermissionError($error)) {
                $error .= ' — أعد ربط Meta Social ووافق على صلاحيات ads_read و leads_retrieval.';
            }

            return ['success' => false, 'message' => $error];
        }

        /** @var list<array<string, mixed>> $ads */
        $ads = $adsResult['data'] ?? [];
        $adIds = [];
        $formIds = [];
        foreach ($ads as $ad) {
            if (! is_array($ad)) {
                continue;
            }
            $adId = (string) ($ad['id'] ?? '');
            if ($adId !== '') {
                $adIds[] = $adId;
            }
            $formId = $this->metaAds->extractLeadGenFormIdFromAd($ad);
            if ($formId) {
                $formIds[$formId] = true;
            }
        }
        $adIds = array_values(array_unique($adIds));
        $formIds = array_keys($formIds);
        $formTokens = [];

        // اكتشاف فورم الصفحات / الحساب (أهم مسار لـ Instant Forms)
        $pageForms = $this->metaAds->listLeadgenForms($this->metaAds->leadgenPageIds());
        if ($pageForms['success'] ?? false) {
            foreach (($pageForms['data'] ?? []) as $form) {
                if (! is_array($form) || blank($form['id'] ?? null)) {
                    continue;
                }
                $fid = (string) $form['id'];
                $formIds[] = $fid;
                if (! empty($form['token'])) {
                    $formTokens[$fid] = (string) $form['token'];
                }
            }
        } elseif (! empty($pageForms['error'])) {
            Log::info('Meta campaign lead sync: page forms discovery', [
                'campaign_id' => $metaCampaignId,
                'error' => $pageForms['error'],
            ]);
        }
        $formIds = array_values(array_unique($formIds));

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];
        $seenLeadgenIds = [];
        $adLeadAttempts = 0;
        $adLeadHits = 0;
        $formLeadHits = 0;
        $importBatch = 'meta_camp_'.$metaCampaignId.'_'.now()->format('Ymd_His');
        $assigneeId = $this->defaultAssigneeId($advertisingCampaign, $actorUserId);
        $adIdLookup = array_fill_keys($adIds, true);

        $ingestRows = function (array $rows) use (
            &$created,
            &$updated,
            &$skipped,
            &$seenLeadgenIds,
            $metaCampaignId,
            $advertisingCampaign,
            $importBatch,
            $assigneeId,
            $actorUserId,
        ): int {
            $accepted = 0;
            foreach ($rows as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $leadgenId = (string) ($row['id'] ?? '');
                if ($leadgenId === '' || isset($seenLeadgenIds[$leadgenId])) {
                    continue;
                }
                $seenLeadgenIds[$leadgenId] = true;
                $accepted++;

                $result = $this->upsertLeadgenLead(
                    $row,
                    $metaCampaignId,
                    $advertisingCampaign,
                    $importBatch,
                    $assigneeId,
                    $actorUserId,
                );

                match ($result) {
                    'created' => $created++,
                    'updated' => $updated++,
                    default => $skipped++,
                };
            }

            return $accepted;
        };

        foreach ($adIds as $adId) {
            $adLeadAttempts++;
            $leadsResult = $this->metaAds->listLeadsForAd($adId);
            if (! ($leadsResult['success'] ?? false)) {
                $err = (string) ($leadsResult['error'] ?? 'فشل جلب ليدات الإعلان '.$adId);
                if ($this->looksLikePermissionError($err)) {
                    $errors[] = $err.' — تأكد من صلاحية leads_retrieval.';
                } else {
                    Log::info('Meta campaign lead sync: ad leads skipped', ['ad_id' => $adId, 'error' => $err]);
                }
                continue;
            }

            $adLeadHits += $ingestRows($leadsResult['data'] ?? []);
        }

        foreach ($formIds as $formId) {
            $token = $formTokens[$formId] ?? null;
            $leadsResult = $this->metaAds->listLeadsForForm($formId, 100, $token);
            if (! ($leadsResult['success'] ?? false)) {
                $err = (string) ($leadsResult['error'] ?? '');
                if ($this->looksLikePermissionError($err)) {
                    $errors[] = 'فورم '.$formId.': '.$err;
                }
                continue;
            }

            $filtered = [];
            foreach (($leadsResult['data'] ?? []) as $row) {
                if (! is_array($row)) {
                    continue;
                }
                if (! $this->leadBelongsToCampaign($row, $metaCampaignId, $adIdLookup)) {
                    continue;
                }
                $filtered[] = $row;
            }
            $formLeadHits += $ingestRows($filtered);
        }

        // Fallback: leads على مستوى الحساب مفلترة بالحملة
        if ($seenLeadgenIds === []) {
            $accountLeads = $this->metaAds->listLeadsForCampaign($metaCampaignId);
            if ($accountLeads['success'] ?? false) {
                $ingestRows($accountLeads['data'] ?? []);
            } elseif (! empty($accountLeads['error'])) {
                Log::info('Meta campaign lead sync: account leads fallback skipped', [
                    'campaign_id' => $metaCampaignId,
                    'error' => $accountLeads['error'],
                ]);
                if ($this->looksLikePermissionError((string) $accountLeads['error'])) {
                    $errors[] = (string) $accountLeads['error'];
                }
            }
        }

        $conversationsLinked = $this->syncAdConversations(
            $metaCampaignId,
            $adIds,
            $advertisingCampaign,
            $assigneeId,
            $actorUserId,
            $importBatch,
        );

        if ($advertisingCampaign) {
            $advertisingCampaign->forceFill([
                'meta_campaign_id' => $metaCampaignId,
                'meta_campaign_name' => $metaCampaignName ?: $advertisingCampaign->meta_campaign_name,
                'meta_leads_synced_at' => now(),
            ])->save();
        }

        $parts = [
            "جديدة: {$created}",
            "محدَّثة: {$updated}",
            "متخطاة: {$skipped}",
            "محادثات مربوطة: {$conversationsLinked}",
        ];
        if ($errors !== []) {
            $parts[] = 'تحذيرات: '.implode(' | ', array_slice($errors, 0, 2));
        }

        $message = 'تم سحب ليدز الكامبين — '.implode(' · ', $parts);
        if ($created === 0 && $updated === 0 && $skipped === 0 && $conversationsLinked === 0) {
            $message .= sprintf(
                ' | تشخيص: إعلانات=%d · فورم=%d · ليدات من إعلان=%d · ليدات من فورم=%d. ',
                count($adIds),
                count($formIds),
                $adLeadHits,
                $formLeadHits,
            );
            if (count($adIds) === 0) {
                $message .= 'لا توجد إعلانات تحت الحملة.';
            } elseif (count($formIds) === 0 && $adLeadHits === 0) {
                $message .= 'غالباً الحملة مش Lead Ads Instant Form، أو مفيش صلاحية leads_retrieval / Page token. أعد الربط وفعّل الصفحات في السوشيال.';
            } else {
                $message .= 'اتوجدت مصادر لكن مفيش ليدات مطابقة لهذه الحملة في ميتا.';
            }
        }

        return [
            'success' => true,
            'message' => $message,
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'conversations_linked' => $conversationsLinked,
            'errors' => $errors,
            'advertising_campaign_id' => $advertisingCampaign?->id,
            'diagnostics' => [
                'ads' => count($adIds),
                'forms' => count($formIds),
                'ad_lead_hits' => $adLeadHits,
                'form_lead_hits' => $formLeadHits,
                'ad_lead_attempts' => $adLeadAttempts,
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  array<string, true>  $adIdLookup
     */
    private function leadBelongsToCampaign(array $row, string $metaCampaignId, array $adIdLookup): bool
    {
        $campaignFromLead = (string) ($row['campaign_id'] ?? '');
        if ($campaignFromLead !== '') {
            return $campaignFromLead === $metaCampaignId;
        }

        $adFromLead = (string) ($row['ad_id'] ?? '');
        if ($adFromLead !== '' && isset($adIdLookup[$adFromLead])) {
            return true;
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return 'created'|'updated'|'skipped'
     */
    public function upsertLeadgenLead(
        array $row,
        string $metaCampaignId,
        ?AdvertisingCampaign $advertisingCampaign,
        string $importBatch,
        ?int $assigneeId,
        ?int $actorUserId,
    ): string {
        if (! Schema::hasTable('sales_leads') || ! Schema::hasColumn('sales_leads', 'meta_leadgen_id')) {
            return 'skipped';
        }

        $leadgenId = (string) ($row['id'] ?? '');
        if ($leadgenId === '') {
            return 'skipped';
        }

        $fields = $this->parseFieldData(is_array($row['field_data'] ?? null) ? $row['field_data'] : []);
        $name = $fields['name'] !== '' ? $fields['name'] : 'Lead Meta '.$leadgenId;
        $phone = $fields['phone'] !== '' ? $fields['phone'] : 'meta_leadgen_'.$leadgenId;
        $email = $fields['email'] !== '' ? $fields['email'] : null;
        $adId = (string) ($row['ad_id'] ?? '');

        $existing = SalesLead::query()->where('meta_leadgen_id', $leadgenId)->first();
        if (! $existing && $fields['phone'] !== '') {
            $existing = $this->crm->findLeadByPhone($fields['phone']);
        }

        $payload = [
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'source' => 'meta_ads',
            'meta_leadgen_id' => $leadgenId,
            'meta_campaign_id' => $metaCampaignId,
            'meta_ad_id' => $adId !== '' ? $adId : null,
            'advertising_campaign_id' => $advertisingCampaign?->id,
            'import_batch' => $importBatch,
        ];

        if ($existing) {
            $existing->fill(array_filter([
                'meta_leadgen_id' => $leadgenId,
                'meta_campaign_id' => $metaCampaignId,
                'meta_ad_id' => $adId !== '' ? $adId : $existing->meta_ad_id,
                'advertising_campaign_id' => $advertisingCampaign?->id ?? $existing->advertising_campaign_id,
                'email' => $email ?: $existing->email,
                'import_batch' => $importBatch,
            ], static fn ($v) => $v !== null && $v !== ''))->save();

            return 'updated';
        }

        SalesLead::query()->create(array_merge($payload, [
            'stage' => 'new_lead',
            'priority' => 'normal',
            'assigned_to' => $assigneeId,
            'created_by' => $actorUserId,
            'category_id' => SalesLeadCategory::defaultGeneralId(),
            'notes' => 'مستورد من Meta Lead Ads'
                ."\nCampaign: ".$metaCampaignId
                .($adId !== '' ? "\nAd: ".$adId : '')
                .(isset($row['form_id']) ? "\nForm: ".$row['form_id'] : ''),
        ]));

        return 'created';
    }

    /**
     * @param  list<array{name?: string, values?: list<string>}|mixed>  $fieldData
     * @return array{name: string, phone: string, email: string}
     */
    public function parseFieldData(array $fieldData): array
    {
        $map = [];
        foreach ($fieldData as $field) {
            if (! is_array($field)) {
                continue;
            }
            $key = strtolower(trim((string) ($field['name'] ?? '')));
            $values = $field['values'] ?? [];
            $value = is_array($values) ? trim((string) ($values[0] ?? '')) : trim((string) $values);
            if ($key !== '' && $value !== '') {
                $map[$key] = $value;
            }
        }

        $composed = trim(($map['first_name'] ?? '').' '.($map['last_name'] ?? ''));
        $name = trim((string) (
            $map['full_name']
            ?? $map['full name']
            ?? ($composed !== '' ? $composed : null)
            ?? $map['name']
            ?? ''
        ));

        $phone = $map['phone_number']
            ?? $map['phone']
            ?? $map['mobile_number']
            ?? $map['mobile']
            ?? $map['رقم الهاتف']
            ?? $map['موبايل']
            ?? '';

        $email = $map['email']
            ?? $map['email_address']
            ?? $map['البريد الإلكتروني']
            ?? '';

        return [
            'name' => $name,
            'phone' => preg_replace('/\s+/', '', (string) $phone) ?: '',
            'email' => trim((string) $email),
        ];
    }

    /**
     * @param  list<string>  $adIds
     */
    private function syncAdConversations(
        string $metaCampaignId,
        array $adIds,
        ?AdvertisingCampaign $advertisingCampaign,
        ?int $assigneeId,
        ?int $actorUserId,
        string $importBatch,
    ): int {
        if (! Schema::hasTable('meta_social_conversations')
            || ! Schema::hasColumn('meta_social_conversations', 'meta_ad_id')) {
            return 0;
        }

        $query = MetaSocialConversation::query()->where(function ($q) use ($metaCampaignId, $adIds) {
            $q->where('meta_campaign_id', $metaCampaignId);
            if ($adIds !== []) {
                $q->orWhereIn('meta_ad_id', $adIds);
            }
        });

        $linked = 0;
        foreach ($query->cursor() as $conversation) {
            /** @var MetaSocialConversation $conversation */
            if (! $conversation->meta_campaign_id) {
                $conversation->meta_campaign_id = $metaCampaignId;
                $conversation->save();
            }

            $lead = null;
            if ($conversation->sales_lead_id) {
                $lead = SalesLead::query()->find($conversation->sales_lead_id);
            }

            if (! $lead) {
                try {
                    if ($assigneeId && $this->crm->isEligibleAgent($assigneeId)) {
                        $lead = $this->crm->createLeadFromConversation(
                            $conversation,
                            $assigneeId,
                            $actorUserId,
                        );
                    } else {
                        $lead = $this->createConversationLeadWithoutAssignee(
                            $conversation,
                            $metaCampaignId,
                            $advertisingCampaign,
                            $importBatch,
                            $actorUserId,
                        );
                    }
                } catch (\Throwable $e) {
                    Log::warning('Meta campaign conversation sync failed', [
                        'conversation_id' => $conversation->id,
                        'error' => $e->getMessage(),
                    ]);
                    continue;
                }
            }

            if (! $lead) {
                continue;
            }

            $updates = array_filter([
                'meta_campaign_id' => $metaCampaignId,
                'meta_ad_id' => $conversation->meta_ad_id ?: $lead->meta_ad_id,
                'advertising_campaign_id' => $advertisingCampaign?->id ?? $lead->advertising_campaign_id,
                'source' => $lead->source === 'social' ? 'meta_ads' : $lead->source,
                'import_batch' => $importBatch,
            ], static fn ($v) => $v !== null && $v !== '');

            $lead->fill($updates)->save();
            $linked++;
        }

        return $linked;
    }

    private function createConversationLeadWithoutAssignee(
        MetaSocialConversation $conversation,
        string $metaCampaignId,
        ?AdvertisingCampaign $advertisingCampaign,
        string $importBatch,
        ?int $actorUserId,
    ): ?SalesLead {
        $phone = trim((string) ($conversation->phone ?: ''));
        if ($phone === '') {
            $phone = 'meta_'.$conversation->platform.'_'.$conversation->participant_id;
        }

        $byPhone = $this->crm->findLeadByPhone($phone);
        if ($byPhone) {
            $this->crm->linkLead($conversation, $byPhone);

            return $byPhone->fresh();
        }

        $lead = SalesLead::query()->create([
            'name' => $conversation->displayName(),
            'phone' => $phone,
            'email' => $conversation->email,
            'source' => 'meta_ads',
            'stage' => 'new_lead',
            'priority' => 'normal',
            'assigned_to' => null,
            'created_by' => $actorUserId,
            'category_id' => SalesLeadCategory::defaultGeneralId(),
            'meta_campaign_id' => $metaCampaignId,
            'meta_ad_id' => $conversation->meta_ad_id,
            'advertising_campaign_id' => $advertisingCampaign?->id,
            'import_batch' => $importBatch,
            'notes' => 'مستورد من محادثة إعلان Meta ('.$conversation->platformLabel().')'
                ."\nCampaign: ".$metaCampaignId,
            'last_contacted_at' => $conversation->last_message_at ?? now(),
        ]);

        $this->crm->linkLead($conversation, $lead);

        return $lead->fresh();
    }

    private function defaultAssigneeId(?AdvertisingCampaign $campaign, ?int $actorUserId): ?int
    {
        if ($campaign) {
            $repId = $campaign->salesEmployees()->orderBy('users.id')->value('users.id');
            if ($repId && $this->crm->isEligibleAgent((int) $repId)) {
                return (int) $repId;
            }
        }

        if ($actorUserId && $this->crm->isEligibleAgent($actorUserId)) {
            return $actorUserId;
        }

        return null;
    }

    private function looksLikePermissionError(string $error): bool
    {
        $lower = strtolower($error);

        return str_contains($lower, 'permission')
            || str_contains($lower, '(#200)')
            || str_contains($lower, 'leads_retrieval')
            || str_contains($lower, 'oauth')
            || str_contains($lower, 'access token');
    }
}
