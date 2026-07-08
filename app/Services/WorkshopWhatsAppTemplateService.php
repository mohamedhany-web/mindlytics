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

    /**
     * @return array<string, string>
     */
    public function workshopVariableLabels(): array
    {
        return [
            '1' => 'اسم المسجّل',
            '2' => 'عنوان الورشة',
            '3' => 'رابط جروب واتساب',
            '4' => 'رقم الهاتف',
            '5' => 'نوع الحضور (أونلاين/حضوري)',
            '6' => 'مكان الورشة',
        ];
    }

    public function defaultWelcomeBody(): string
    {
        return "مرحباً {{1}}\n\n"
            ."شكراً لتسجيلك في ورشة {{2}}.\n\n"
            ."للانضمام لجروب الورشة استخدم الزر أدناه.\n\n"
            ."فريق Mindlytics";
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function defaultWelcomeButtons(Workshop $workshop): array
    {
        $groupLink = trim((string) $workshop->whatsapp_group_link);
        if ($groupLink === '') {
            return [];
        }

        $groupLink = app(WhatsAppTemplateService::class)->normalizeMetaButtonUrl($groupLink);
        if ($groupLink === '') {
            return [];
        }

        $inviteCode = $this->groupLinkDynamicPart($groupLink);

        return [[
            'type' => 'URL',
            'text' => 'انضم للجروب',
            'url' => 'https://chat.whatsapp.com/{{3}}',
            'url_example' => $inviteCode,
            'phone' => '',
        ]];
    }

    /**
     * @return array<string, mixed>
     */
    public function formPreset(Workshop $workshop): array
    {
        $groupLink = trim((string) $workshop->whatsapp_group_link);
        $normalizedLink = $groupLink !== ''
            ? app(WhatsAppTemplateService::class)->normalizeMetaButtonUrl($groupLink)
            : '';

        return [
            'name' => $this->templateNameFor($workshop),
            'body_text' => $this->defaultWelcomeBody(),
            'footer_text' => 'Mindlytics',
            'category' => 'UTILITY',
            'language' => 'ar',
            'buttons' => $this->defaultWelcomeButtons($workshop),
            'has_group_link' => $normalizedLink !== '',
            'group_link' => $normalizedLink !== '' ? $normalizedLink : null,
            'workshop_title' => $workshop->title,
            'variable_labels' => $this->workshopVariableLabels(),
        ];
    }

    public function linkTemplateToWorkshop(Workshop $workshop, WhatsAppMetaTemplate $template): void
    {
        $workshop->update(['welcome_meta_template_id' => $template->id]);
    }

    public function templateNameFor(Workshop $workshop): string
    {
        $slug = Str::slug(Str::limit($workshop->title, 24, ''));

        return 'workshop_welcome_'.($slug !== '' ? $slug : 'ws').'_'.$workshop->id;
    }

    public function normalizeBodyForMeta(string $body): string
    {
        return str_replace(
            ['{{name}}', '{{workshop_name}}', '{{workshop}}', '{{group_link}}', '{{whatsapp_group}}'],
            ['{{1}}', '{{2}}', '{{2}}', '{{3}}', '{{3}}'],
            trim($body)
        );
    }

    public function linkedTemplate(Workshop $workshop): ?WhatsAppMetaTemplate
    {
        $workshop->loadMissing('welcomeMetaTemplate');

        return $workshop->welcomeMetaTemplate;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{success: bool, template?: WhatsAppMetaTemplate, error?: string, message?: string}
     */
    public function createAndSubmitWelcomeTemplate(Workshop $workshop, array $payload, ?int $userId = null): array
    {
        $submitNow = (bool) ($payload['submit_now'] ?? true);

        $payload['name'] = $this->templateNameFor($workshop);
        $payload['body_text'] = $this->normalizeBodyForMeta((string) ($payload['body_text'] ?? $this->defaultWelcomeBody()));
        $payload['language'] = (string) ($payload['language'] ?? 'ar');
        $payload['category'] = strtoupper((string) ($payload['category'] ?? 'UTILITY'));
        $payload['buttons'] = $this->enrichButtonExamples($payload['buttons'] ?? [], $workshop);

        $name = $payload['name'];
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
            $existing = $this->templates->updateDraft($existing, $payload);
        } else {
            $orphan = WhatsAppMetaTemplate::query()
                ->where('name', $name)
                ->where('language', $payload['language'])
                ->first();

            if ($orphan && $orphan->isEditable()) {
                $existing = $this->templates->updateDraft($orphan, $payload);
            } else {
                $existing = $this->templates->createDraft($payload, $userId);
            }
        }

        $workshop->update(['welcome_meta_template_id' => $existing->id]);

        if (! $submitNow) {
            return [
                'success' => true,
                'template' => $existing->fresh(),
                'message' => 'تم حفظ القالب كمسودة — يمكنك إرساله لـ Meta لاحقاً.',
            ];
        }

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
            throw new \RuntimeException('أنشئ قالب الترحيب أولاً أو اختر قالباً معتمداً من القائمة.');
        }

        return $this->dispatchTemplate($workshop, $template, $createdBy, $scope, $phone);
    }

    /**
     * @throws \RuntimeException
     */
    public function dispatchTemplate(
        Workshop $workshop,
        WhatsAppMetaTemplate $template,
        int $createdBy,
        string $scope = 'all',
        ?string $phone = null
    ): \App\Models\WhatsAppBatch {
        if (! $template->isSendable()) {
            throw new \RuntimeException(
                'القالب غير معتمد بعد (الحالة: '.$template->statusLabel().'). انتظر موافقة Meta أو اضغط «مزامنة».'
            );
        }

        $registrations = $this->batchService->registrationsForWorkshop($workshop, $scope, $phone);
        if ($registrations->isEmpty()) {
            throw new \RuntimeException('لا يوجد مسجلون لديهم أرقام واتساب ضمن المعايير المحددة.');
        }

        $items = $this->buildTemplateItems($workshop, $registrations, $template, $createdBy);

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
                'template_label' => $template->displayLabel(),
                'workshop_id' => $workshop->id,
                'workshop_title' => $workshop->title,
            ],
            'ورشة — قالب — '.Str::limit($workshop->title, 40).' — '.now()->format('Y-m-d H:i'),
            $template->body_text
        );
    }

    public function resolveSendableTemplate(string $name, string $language): ?WhatsAppMetaTemplate
    {
        return WhatsAppMetaTemplate::query()
            ->where('name', trim($name))
            ->where('language', trim($language))
            ->where('status', WhatsAppMetaTemplate::STATUS_APPROVED)
            ->first();
    }

    /**
     * @throws \RuntimeException
     */
    public function validateTemplateForRegistration(
        Workshop $workshop,
        WhatsAppMetaTemplate $template,
        WorkshopRegistration $reg
    ): void {
        $variables = $this->variablesForRegistration($reg, $workshop, $template);
        $built = $this->templates->buildSendComponents($template->name, $template->language, $variables);

        if ($built['error'] ?? null) {
            throw new \RuntimeException((string) $built['error']);
        }
    }

    /**
     * @param  Collection<int, WorkshopRegistration>  $registrations
     * @return Collection<int, array<string, mixed>>
     */
    private function buildTemplateItems(
        Workshop $workshop,
        Collection $registrations,
        WhatsAppMetaTemplate $template,
        int $senderId
    ): Collection {
        $seenPhones = [];
        $whatsapp = app(WhatsAppService::class);

        return $registrations->shuffle()->map(function (WorkshopRegistration $reg) use ($workshop, $template, &$seenPhones, $whatsapp, $senderId) {
            $formatted = $whatsapp->formatPhoneNumber((string) $reg->phone);
            if ($formatted === '' || isset($seenPhones[$formatted])) {
                return null;
            }
            $seenPhones[$formatted] = true;

            $variables = $this->variablesForRegistration($reg, $workshop, $template);

            $built = $this->templates->buildSendComponents($template->name, $template->language, $variables);
            if ($built['error'] ?? null) {
                throw new \RuntimeException((string) $built['error']);
            }

            $preview = $this->renderPreview((string) $template->body_text, $reg, $workshop);

            return [
                'recipient_name' => $reg->name,
                'phone' => $formatted,
                'message' => json_encode([
                    'template_name' => $template->name,
                    'language' => $template->language,
                    'components' => $built['components'],
                    'preview' => $preview,
                ], JSON_UNESCAPED_UNICODE),
                'message_type' => 'template',
                'workshop_registration_id' => $reg->id,
                'user_id' => $senderId,
            ];
        })->filter()->values();
    }

    private function renderPreview(string $body, WorkshopRegistration $reg, Workshop $workshop): string
    {
        $pool = $this->variablePool($reg, $workshop);
        $out = $body;

        foreach ($pool as $index => $value) {
            $n = $index + 1;
            $out = str_replace('{{'.$n.'}}', $value, $out);
        }

        return str_replace(
            ['{{name}}', '{{workshop_name}}', '{{workshop}}', '{{group_link}}'],
            [$reg->name, $workshop->title, $workshop->title, (string) ($workshop->whatsapp_group_link ?? '')],
            $out
        );
    }

    /**
     * @return array<int, string>
     */
    private function variablePool(WorkshopRegistration $reg, Workshop $workshop): array
    {
        $attendance = $reg->attendance_mode === 'offline' ? 'حضوري' : ($reg->attendance_mode === 'online' ? 'أونلاين' : '—');
        $groupLink = trim((string) $workshop->whatsapp_group_link);

        return [
            $reg->name,
            $workshop->title,
            $groupLink,
            (string) $reg->phone,
            $attendance,
            (string) ($workshop->location ?? ''),
        ];
    }

    /**
     * @return array<int|string, string>
     */
    private function variablesForRegistration(
        WorkshopRegistration $reg,
        Workshop $workshop,
        WhatsAppMetaTemplate $template
    ): array {
        $pool = $this->variablePool($reg, $workshop);

        $bodyCount = (int) $template->body_variable_count;
        if ($bodyCount < 1 && $template->body_text) {
            $bodyCount = $this->templates->countBodyVariables((string) $template->body_text);
        }

        $variables = [];
        for ($i = 1; $i <= $bodyCount; $i++) {
            $variables[$i] = $pool[$i - 1] ?? $reg->name;
        }

        if ($template->header_type === 'text' && str_contains((string) $template->header_content, '{{1}}')) {
            $variables['header_1'] = $workshop->title;
        }

        $buttons = is_array($template->buttons) ? $template->buttons : [];
        $groupDynamic = $this->groupLinkDynamicPart((string) $workshop->whatsapp_group_link);

        foreach ($buttons as $index => $btn) {
            if (! is_array($btn) || strtoupper((string) ($btn['type'] ?? '')) !== 'URL') {
                continue;
            }

            $url = (string) ($btn['url'] ?? '');
            if (! preg_match('/\{\{(\d+)\}\}/', $url, $matches)) {
                continue;
            }

            $varIndex = (int) $matches[1];
            if (str_contains(strtolower($url), 'chat.whatsapp.com')) {
                $value = $groupDynamic !== '' ? $groupDynamic : $this->groupLinkDynamicPart((string) ($pool[$varIndex - 1] ?? ''));
            } else {
                $value = $varIndex === 1 && $groupDynamic !== ''
                    ? $groupDynamic
                    : ($pool[$varIndex - 1] ?? $groupDynamic);
            }

            if ($value === '') {
                continue;
            }

            $variables['button_'.$index] = $value;
            $variables['button_url_'.$index] = $value;
            if (! isset($variables[$varIndex])) {
                $variables[$varIndex] = $value;
            }
        }

        return $variables;
    }

    /**
     * @param  mixed  $buttons
     * @return array<int, array<string, string>>
     */
    public function enrichButtonExamplesForWorkshop(Workshop $workshop, mixed $buttons): array
    {
        return $this->enrichButtonExamples($buttons, $workshop);
    }

    /**
     * @param  mixed  $buttons
     * @return array<int, array<string, string>>
     */
    private function enrichButtonExamples(mixed $buttons, Workshop $workshop): array
    {
        $sanitized = $this->templates->sanitizeButtons($buttons);
        $groupLink = trim((string) $workshop->whatsapp_group_link);
        $dynamicPart = $this->groupLinkDynamicPart($groupLink);

        foreach ($sanitized as &$btn) {
            if (strtoupper((string) ($btn['type'] ?? '')) !== 'URL') {
                continue;
            }

            $url = (string) ($btn['url'] ?? '');
            if (! preg_match('/\{\{\d+\}\}/', $url)) {
                continue;
            }

            if (trim((string) ($btn['url_example'] ?? '')) === '') {
                $btn['url_example'] = $dynamicPart !== '' ? $dynamicPart : 'sample_invite';
            } else {
                $btn['url_example'] = app(WhatsAppTemplateService::class)
                    ->resolveUrlButtonExampleSuffix($url, (string) $btn['url_example']);
            }
        }
        unset($btn);

        return $sanitized;
    }

    private function groupLinkDynamicPart(string $groupLink): string
    {
        $groupLink = trim($groupLink);
        if ($groupLink === '') {
            return '';
        }

        $path = parse_url($groupLink, PHP_URL_PATH);
        if (is_string($path) && $path !== '') {
            $segments = array_values(array_filter(explode('/', trim($path, '/'))));
            if ($segments !== []) {
                return (string) end($segments);
            }
        }

        return $groupLink;
    }
}
