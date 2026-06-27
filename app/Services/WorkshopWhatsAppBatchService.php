<?php

namespace App\Services;

use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use App\Models\WhatsAppBatch;
use App\Support\WhatsAppBridgeSettings;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class WorkshopWhatsAppBatchService
{
    public function __construct(
        private WhatsAppBatchService $batchService,
        private WhatsAppPacingService $pacing,
    ) {}

    public function latestForWorkshop(int $workshopId): ?WhatsAppBatch
    {
        return $this->batchService->latestForWorkshop($workshopId);
    }

    public function countDistinctPhones(Workshop $workshop, string $scope = 'all', ?string $phone = null): int
    {
        return $this->registrationsForWorkshop($workshop, $scope, $phone)
            ->pluck('phone')
            ->map(fn ($p) => $this->normalizePhone((string) $p))
            ->filter()
            ->unique()
            ->count();
    }

    /**
     * @return Collection<int, WorkshopRegistration>
     */
    public function registrationsForWorkshop(Workshop $workshop, string $scope = 'all', ?string $phone = null): Collection
    {
        $query = $workshop->registrations()
            ->whereNotNull('phone')
            ->where('phone', '!=', '');

        if (in_array($scope, ['online', 'offline'], true)) {
            $query->where('attendance_mode', $scope);
        }

        $registrations = $query->orderBy('name')->get();

        if ($scope === 'phone' && $phone !== null && trim($phone) !== '') {
            $target = $this->normalizePhone($phone);

            return $registrations
                ->filter(fn (WorkshopRegistration $reg) => $this->normalizePhone((string) $reg->phone) === $target)
                ->values();
        }

        return $registrations;
    }

    /**
     * @throws \RuntimeException
     */
    public function dispatch(
        Workshop $workshop,
        string $messageTemplate,
        int $createdBy,
        string $scope = 'all',
        ?string $phone = null
    ): WhatsAppBatch {
        if (! WhatsAppBridgeSettings::usesBridge()) {
            throw new \RuntimeException('إرسال الواتساب غير مفعّل — راجع إعدادات Bridge في قسم الواتساب.');
        }

        if (! WhatsAppBatchService::isReady()) {
            throw new \RuntimeException('جداول دفعات الواتساب غير موجودة. نفّذ php artisan migrate على السيرفر.');
        }

        $registrations = $this->registrationsForWorkshop($workshop, $scope, $phone);

        if ($registrations->isEmpty()) {
            throw new \RuntimeException('لا يوجد مسجلون لديهم أرقام واتساب ضمن المعايير المحددة.');
        }

        $items = $this->buildItems($workshop, $registrations, $messageTemplate);

        if ($items->isEmpty()) {
            throw new \RuntimeException('لا توجد أرقام صالحة للإرسال بعد التحقق.');
        }

        if ($limitError = $this->pacing->assertCanSend()) {
            throw new \RuntimeException($limitError);
        }

        $remainingToday = $this->pacing->remainingDailyQuota();
        if ($remainingToday !== null && $items->count() > $remainingToday) {
            throw new \RuntimeException(
                'عدد المستلمين (' . $items->count() . ') يتجاوز المتبقي اليوم (' . $remainingToday . ' رسالة). قلّل المجموعة أو زِد الحد في إعدادات الواتساب.'
            );
        }

        return $this->batchService->createAndDispatch(
            sourceType: 'workshop',
            sourceId: $workshop->id,
            title: 'ورشة — ' . Str::limit($workshop->title, 50) . ' — ' . now()->format('Y-m-d H:i'),
            messageTemplate: $messageTemplate,
            items: $items,
            createdBy: $createdBy,
            meta: [
                'workshop_id' => $workshop->id,
                'workshop_title' => $workshop->title,
                'scope' => $scope,
                'phone_filter' => $phone,
            ]
        );
    }

    /**
     * @param  Collection<int, WorkshopRegistration>  $registrations
     * @return Collection<int, array{recipient_name: string, phone: string, message: string, message_type: string, workshop_registration_id: int}>
     */
    private function buildItems(Workshop $workshop, Collection $registrations, string $messageTemplate): Collection
    {
        $seenPhones = [];

        return $registrations->shuffle()->map(function (WorkshopRegistration $reg) use ($workshop, $messageTemplate, &$seenPhones) {
            $normalized = $this->normalizePhone((string) $reg->phone);
            if ($normalized === '' || isset($seenPhones[$normalized])) {
                return null;
            }

            $seenPhones[$normalized] = true;

            return [
                'recipient_name' => $reg->name,
                'phone' => (string) $reg->phone,
                'message' => $this->renderMessage($messageTemplate, $workshop, $reg),
                'message_type' => 'text',
                'workshop_registration_id' => $reg->id,
            ];
        })->filter()->values();
    }

    private function renderMessage(string $template, Workshop $workshop, WorkshopRegistration $reg): string
    {
        $modeLabel = match ($reg->attendance_mode) {
            'online' => 'أونلاين',
            'offline' => 'حضوري',
            default => $reg->attendance_mode ?? '',
        };

        return str_replace(
            ['{{name}}', '{{phone}}', '{{workshop}}', '{{attendance}}', '{{location}}'],
            [
                $reg->name,
                $reg->phone ?? '',
                $workshop->title,
                $modeLabel,
                $workshop->location ?? '',
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

        if (str_starts_with($digits, '01') && strlen($digits) === 11) {
            $digits = '2' . $digits;
        } elseif (str_starts_with($digits, '0')) {
            $digits = '20' . substr($digits, 1);
        } elseif (! str_starts_with($digits, '20')) {
            $digits = '20' . $digits;
        }

        return $digits;
    }
}
