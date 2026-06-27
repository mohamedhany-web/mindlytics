<?php

namespace App\Services;

use App\Models\SalesLead;
use App\Models\SalesLeadGroup;
use App\Models\WhatsAppBatch;
use App\Support\WhatsAppBridgeSettings;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SalesLeadWhatsAppBatchService
{
    public function __construct(
        private WhatsAppBatchService $batchService,
        private WhatsAppPacingService $pacing,
    ) {}

    public function latestForGroup(int $groupId): ?WhatsAppBatch
    {
        if (! WhatsAppBatchService::isReady()) {
            return null;
        }

        return WhatsAppBatch::query()
            ->where('source_type', 'sales_group')
            ->where('source_id', $groupId)
            ->latest()
            ->first();
    }

    /**
     * @return Collection<int, SalesLead>
     */
    public function leadsForGroup(SalesLeadGroup $group, ?int $assigneeId = null): Collection
    {
        $query = SalesLead::query()
            ->where('sales_lead_group_id', $group->id)
            ->whereNotNull('phone')
            ->where('phone', '!=', '');

        if ($assigneeId !== null) {
            $query->where('assigned_to', $assigneeId);
        }

        return $query->orderBy('name')->get();
    }

    /**
     * @throws \RuntimeException
     */
    public function dispatch(
        SalesLeadGroup $group,
        string $messageTemplate,
        int $createdBy,
        ?int $assigneeId = null
    ): WhatsAppBatch {
        if (! WhatsAppBridgeSettings::usesBridge()) {
            throw new \RuntimeException('إرسال الواتساب غير مفعّل — راجع إعدادات Bridge في قسم الواتساب.');
        }

        if (! WhatsAppBatchService::isReady()) {
            throw new \RuntimeException('جداول دفعات الواتساب غير موجودة. نفّذ php artisan migrate على السيرفر.');
        }

        $leads = $this->leadsForGroup($group, $assigneeId);

        if ($leads->isEmpty()) {
            throw new \RuntimeException('لا يوجد عملاء في هذه المجموعة لديهم أرقام هواتف.');
        }

        $items = $this->buildItems($leads, $messageTemplate);

        if ($items->isEmpty()) {
            throw new \RuntimeException('لا توجد أرقام صالحة للإرسال بعد التحقق.');
        }

        if ($limitError = $this->pacing->assertCanSend()) {
            throw new \RuntimeException($limitError);
        }

        app(WhatsAppBridgeService::class)->assertReadyForBulkSend();

        $remainingToday = $this->pacing->remainingDailyQuota();
        if ($remainingToday !== null && $items->count() > $remainingToday) {
            throw new \RuntimeException(
                'عدد المستلمين (' . $items->count() . ') يتجاوز المتبقي اليوم (' . $remainingToday . ' رسالة). قلّل المجموعة أو زِد الحد في إعدادات الواتساب.'
            );
        }

        return $this->batchService->createAndDispatch(
            sourceType: 'sales_group',
            sourceId: $group->id,
            title: 'مبيعات — ' . Str::limit($group->name, 60) . ' — ' . now()->format('Y-m-d H:i'),
            messageTemplate: $messageTemplate,
            items: $items,
            createdBy: $createdBy,
            meta: [
                'sales_lead_group_id' => $group->id,
                'sales_lead_group_name' => $group->name,
                'assignee_filter' => $assigneeId,
            ]
        );
    }

    /**
     * @param  Collection<int, SalesLead>  $leads
     * @return Collection<int, array{recipient_name: string, phone: string, message: string, message_type: string, sales_lead_id: int}>
     */
    private function buildItems(Collection $leads, string $messageTemplate): Collection
    {
        $seenPhones = [];

        return $leads->shuffle()->map(function (SalesLead $lead) use ($messageTemplate, &$seenPhones) {
            $normalized = $this->normalizePhone((string) $lead->phone);
            if ($normalized === '' || isset($seenPhones[$normalized])) {
                return null;
            }

            $seenPhones[$normalized] = true;

            return [
                'recipient_name' => $lead->name,
                'phone' => (string) $lead->phone,
                'message' => $this->renderMessage($messageTemplate, $lead),
                'message_type' => 'text',
                'sales_lead_id' => $lead->id,
            ];
        })->filter()->values();
    }

    private function renderMessage(string $template, SalesLead $lead): string
    {
        return str_replace(
            ['{{name}}', '{{company}}', '{{phone}}', '{{stage}}'],
            [
                $lead->name,
                $lead->company ?? '',
                $lead->phone ?? '',
                SalesLead::stageLabel($lead->stage),
            ],
            $template
        );
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone) ?? '';

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '0')) {
            $digits = '20' . substr($digits, 1);
        } elseif (! str_starts_with($digits, '20')) {
            $digits = '20' . $digits;
        }

        return $digits;
    }
}
