<?php

namespace App\Services;

use App\Models\WhatsAppMetaTemplate;
use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class WorkshopWhatsAppTemplateService
{
    public function __construct(
        private WhatsAppTemplateService $templates,
        private WorkshopWhatsAppBatchService $batchService,
    ) {}

    public function defaultWelcomeBody(): string
    {
        return "مرحباً {{1}} 👋\n\n"
            ."شكراً لتسجيلك في ورشة «{{2}}».\n\n"
            ."سعداء بانضمامك — نراك في موعد الورشة.\n\n"
            ."فريق Mindlytics";
    }

    public function templateNameFor(Workshop $workshop): string
    {
        $slug = Str::slug(Str::limit($workshop->title, 24, ''));

        return 'workshop_welcome_'.($slug !== '' ? $slug : 'ws').'_'.$workshop->id;
    }

    public function normalizeBodyForMeta(string $body): string
    {
        return str_replace(
            ['{{name}}', '{{workshop_name}}', '{{workshop}}'],
            ['{{1}}', '{{2}}', '{{2}}'],
            trim($body)
        );
    }

    public function linkedTemplate(Workshop $workshop): ?WhatsAppMetaTemplate
    {
        $workshop->loadMissing('welcomeMetaTemplate');

        return $workshop->welcomeMetaTemplate;
    }

    /**
     * @return array{success: bool, template?: WhatsAppMetaTemplate, error?: string, message?: string}
     */
    public function createAndSubmitWelcomeTemplate(Workshop $workshop, ?string $bodyText = null, ?int $userId = null): array
    {
        $bodyText = $this->normalizeBodyForMeta($bodyText ?: $this->defaultWelcomeBody());
        $name = $this->templateNameFor($workshop);

        $existing = $workshop->welcomeMetaTemplate;
        if ($existing && $existing->isSendable()) {
            return [
                'success' => true,
                'template' => $existing,
                'message' => 'القالب معتمد بالفعل — يمكنك الإرسال مباشرة.',
            ];
        }

        if ($existing && $existing->status === WhatsAppMetaTemplate::STATUS_PENDING) {
            return [
                'success' => true,
                'template' => $existing,
                'message' => 'القالب قيد مراجعة Meta — انتظر الموافقة ثم أرسل للمسجلين.',
            ];
        }

        if ($existing && $existing->isEditable()) {
            $existing = $this->templates->updateDraft($existing, [
                'name' => $name,
                'language' => 'ar',
                'category' => 'UTILITY',
                'body_text' => $bodyText,
                'header_type' => null,
                'footer_text' => 'Mindlytics',
            ]);
        } else {
            $existing = WhatsAppMetaTemplate::query()
                ->where('name', $name)
                ->where('language', 'ar')
                ->first();

            if ($existing && $existing->isEditable()) {
                $existing = $this->templates->updateDraft($existing, [
                    'body_text' => $bodyText,
                    'footer_text' => 'Mindlytics',
                ]);
            } else {
                $existing = $this->templates->createDraft([
                    'name' => $name,
                    'language' => 'ar',
                    'category' => 'UTILITY',
                    'body_text' => $bodyText,
                    'header_type' => null,
                    'footer_text' => 'Mindlytics',
                ], $userId);
            }
        }

        $workshop->update(['welcome_meta_template_id' => $existing->id]);

        $submit = $this->templates->submitToMeta($existing->fresh());
        if (! ($submit['success'] ?? false)) {
            return [
                'success' => false,
                'template' => $existing->fresh(),
                'error' => $submit['error'] ?? 'فشل إرسال القالب إلى Meta',
            ];
        }

        return [
            'success' => true,
            'template' => $submit['template'] ?? $existing->fresh(),
            'message' => 'تم إنشاء القالب وإرساله لـ Meta للمراجعة. بعد الموافقة يمكنك إرساله لجميع المسجلين.',
        ];
    }

    /**
     * @return array{success: bool, template?: WhatsAppMetaTemplate, error?: string}
     */
    public function syncWelcomeTemplate(Workshop $workshop): array
    {
        $template = $this->linkedTemplate($workshop);
        if (! $template) {
            return ['success' => false, 'error' => 'لا يوجد قالب مرتبط بهذه الورشة.'];
        }

        $sync = $this->templates->syncFromMeta();
        if (! ($sync['success'] ?? false)) {
            return ['success' => false, 'error' => $sync['error'] ?? 'فشل المزامنة مع Meta'];
        }

        return ['success' => true, 'template' => $template->fresh()];
    }

    /**
     * @throws \RuntimeException
     */
    public function dispatchWelcomeTemplate(
        Workshop $workshop,
        int $createdBy,
        string $scope = 'all',
        ?string $phone = null
    ): \App\Models\WhatsAppBatch {
        $template = $this->linkedTemplate($workshop);
        if (! $template) {
            throw new \RuntimeException('أنشئ قالب الترحيب أولاً من هذه الصفحة.');
        }

        if (! $template->isSendable()) {
            throw new \RuntimeException(
                'القالب غير معتمد بعد (الحالة: '.$template->statusLabel().'). انتظر موافقة Meta أو اضغط «مزامنة الحالة».'
            );
        }

        $registrations = $this->batchService->registrationsForWorkshop($workshop, $scope, $phone);
        if ($registrations->isEmpty()) {
            throw new \RuntimeException('لا يوجد مسجلون لديهم أرقام واتساب ضمن المعايير المحددة.');
        }

        $items = $this->buildTemplateItems($workshop, $registrations, $template);

        return $this->batchService->dispatchPreparedItems(
            $workshop,
            $items,
            $createdBy,
            $scope,
            $phone,
            [
                'send_mode' => 'template',
                'template_name' => $template->name,
                'template_language' => $template->language,
                'template_label' => 'قالب ترحيب الورشة',
                'workshop_id' => $workshop->id,
                'workshop_title' => $workshop->title,
            ],
            'ورشة — قالب ترحيب — '.Str::limit($workshop->title, 40).' — '.now()->format('Y-m-d H:i'),
            $template->body_text
        );
    }

    /**
     * @param  Collection<int, WorkshopRegistration>  $registrations
     * @return Collection<int, array<string, mixed>>
     */
    private function buildTemplateItems(Workshop $workshop, Collection $registrations, WhatsAppMetaTemplate $template): Collection
    {
        $seenPhones = [];

        return $registrations->shuffle()->map(function (WorkshopRegistration $reg) use ($workshop, $template, &$seenPhones) {
            $normalized = preg_replace('/[^0-9]/', '', (string) $reg->phone) ?? '';
            if ($normalized === '' || isset($seenPhones[$normalized])) {
                return null;
            }
            $seenPhones[$normalized] = true;

            $variables = [
                1 => $reg->name,
                2 => $workshop->title,
            ];

            $built = $this->templates->buildSendComponents($template->name, $template->language, $variables);
            if ($built['error'] ?? null) {
                throw new \RuntimeException((string) $built['error']);
            }

            $preview = $this->renderPreview((string) $template->body_text, $reg, $workshop);

            return [
                'recipient_name' => $reg->name,
                'phone' => (string) $reg->phone,
                'message' => json_encode([
                    'template_name' => $template->name,
                    'language' => $template->language,
                    'components' => $built['components'],
                    'preview' => $preview,
                ], JSON_UNESCAPED_UNICODE),
                'message_type' => 'template',
                'workshop_registration_id' => $reg->id,
            ];
        })->filter()->values();
    }

    private function renderPreview(string $body, WorkshopRegistration $reg, Workshop $workshop): string
    {
        return str_replace(
            ['{{1}}', '{{2}}', '{{name}}', '{{workshop_name}}', '{{workshop}}'],
            [$reg->name, $workshop->title, $reg->name, $workshop->title, $workshop->title],
            $body
        );
    }
}
