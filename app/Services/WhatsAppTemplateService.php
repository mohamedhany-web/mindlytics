<?php

namespace App\Services;

use App\Models\WhatsAppMetaTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WhatsAppTemplateService
{
    public function __construct(
        private WhatsAppCloudService $cloud
    ) {}

    public function scalarToString(mixed $value, string $default = ''): string
    {
        if ($value === null) {
            return $default;
        }
        if (is_string($value)) {
            return trim($value);
        }
        if (is_int($value) || is_float($value) || is_bool($value)) {
            return trim((string) $value);
        }
        if (is_array($value)) {
            foreach ($value as $item) {
                $candidate = $this->scalarToString($item, '');
                if ($candidate !== '') {
                    return $candidate;
                }
            }

            return $default;
        }

        return $default;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createDraft(array $data, ?int $userId = null): WhatsAppMetaTemplate
    {
        $name = $this->normalizeTemplateName((string) ($data['name'] ?? ''));
        $data = $this->prepareMetaTemplateData($data);
        $bodyText = (string) $data['body_text'];
        $data['buttons'] = $this->normalizeButtonsForMeta(
            $data['buttons'] ?? [],
            $bodyText,
            (string) ($data['header_type'] ?? ''),
            (string) ($data['header_content'] ?? '')
        );

        return WhatsAppMetaTemplate::create([
            'name' => $name,
            'language' => (string) ($data['language'] ?? 'ar'),
            'category' => strtoupper((string) ($data['category'] ?? 'UTILITY')),
            'status' => WhatsAppMetaTemplate::STATUS_DRAFT,
            'body_text' => $bodyText,
            'header_type' => $data['header_type'] ?: null,
            'header_content' => $data['header_content'] ?? null,
            'footer_text' => $data['footer_text'] ?? null,
            'buttons' => $data['buttons'],
            'body_variable_count' => $this->countBodyVariables($bodyText),
            'components' => $this->buildMetaComponents($data),
            'created_by' => $userId,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateDraft(WhatsAppMetaTemplate $template, array $data): WhatsAppMetaTemplate
    {
        if (! $template->isEditable()) {
            throw new \RuntimeException('لا يمكن تعديل قالب بحالة: ' . $template->statusLabel());
        }

        $bodyText = $this->normalizeMetaBodyText((string) ($data['body_text'] ?? $template->body_text));
        $merged = array_merge($template->toArray(), $data, ['body_text' => $bodyText]);
        $merged = $this->prepareMetaTemplateData($merged);
        $bodyText = (string) $merged['body_text'];
        $merged['name'] = $this->normalizeTemplateName((string) ($data['name'] ?? $template->name));
        $merged['buttons'] = $this->normalizeButtonsForMeta(
            $merged['buttons'] ?? [],
            $bodyText,
            (string) ($merged['header_type'] ?? ''),
            (string) ($merged['header_content'] ?? '')
        );
        $merged['body_variable_count'] = $this->countBodyVariables($bodyText);

        $template->update([
            'name' => $merged['name'],
            'language' => (string) ($data['language'] ?? $template->language),
            'category' => strtoupper((string) ($data['category'] ?? $template->category)),
            'body_text' => $bodyText,
            'header_type' => ($data['header_type'] ?? $template->header_type) ?: null,
            'header_content' => $data['header_content'] ?? $template->header_content,
            'footer_text' => $data['footer_text'] ?? $template->footer_text,
            'buttons' => $merged['buttons'],
            'body_variable_count' => $merged['body_variable_count'],
            'components' => $this->buildMetaComponents($merged),
            'rejection_reason' => $template->status === WhatsAppMetaTemplate::STATUS_REJECTED
                ? null
                : $template->rejection_reason,
            'status' => WhatsAppMetaTemplate::STATUS_DRAFT,
        ]);

        return $template->fresh();
    }

    /**
     * @return array{success: bool, template?: WhatsAppMetaTemplate, error?: string}
     */
    public function submitToMeta(WhatsAppMetaTemplate $template): array
    {
        if (! in_array($template->status, [WhatsAppMetaTemplate::STATUS_DRAFT, WhatsAppMetaTemplate::STATUS_REJECTED], true)) {
            return ['success' => false, 'error' => 'يمكن إرسال المسودات والقوالب المرفوضة فقط إلى Meta.'];
        }

        $prepared = $this->prepareMetaTemplateData($template->toArray());
        $prepared['buttons'] = $this->normalizeButtonsForMeta(
            $prepared['buttons'] ?? [],
            (string) $prepared['body_text'],
            (string) ($prepared['header_type'] ?? ''),
            (string) ($prepared['header_content'] ?? '')
        );

        $components = $this->buildMetaComponents($prepared);
        $mediaHeader = in_array($template->header_type, ['image', 'video', 'document'], true);

        if ($mediaHeader && empty($template->header_content)) {
            return ['success' => false, 'error' => 'Header الوسائط يتطلب رابط مثال (Example URL) للمراجعة من Meta.'];
        }

        if ($components === []) {
            return ['success' => false, 'error' => 'محتوى القالب فارغ — أضف نص الرسالة (Body) على الأقل.'];
        }

        if ($validationError = $this->validateMetaComponents($components)) {
            return ['success' => false, 'error' => $validationError];
        }

        $template->update([
            'body_text' => $prepared['body_text'],
            'buttons' => $prepared['buttons'],
            'body_variable_count' => $this->countBodyVariables((string) $prepared['body_text']),
            'language' => $this->normalizeMetaLanguage($template->language),
            'components' => $components,
        ]);

        $result = $this->cloud->createMessageTemplate(
            $template->name,
            $this->normalizeMetaLanguage($template->language),
            $template->category,
            $components
        );

        if (! ($result['success'] ?? false)) {
            return ['success' => false, 'error' => $result['error'] ?? 'فشل إرسال القالب إلى Meta'];
        }

        $template->update([
            'status' => WhatsAppMetaTemplate::STATUS_PENDING,
            'meta_template_id' => $result['id'] ?? $template->meta_template_id,
            'submitted_at' => now(),
            'meta_synced_at' => now(),
            'rejection_reason' => null,
            'components' => $components,
        ]);

        return ['success' => true, 'template' => $template->fresh()];
    }

    /**
     * @return array{success: bool, synced: int, error?: string}
     */
    public function syncFromMeta(): array
    {
        $fetch = $this->cloud->fetchAllMessageTemplates();
        if (! ($fetch['success'] ?? false)) {
            return ['success' => false, 'synced' => 0, 'error' => $fetch['error'] ?? 'فشل المزامنة'];
        }

        $synced = 0;

        DB::transaction(function () use ($fetch, &$synced) {
            foreach ($fetch['templates'] as $row) {
                $name = (string) ($row['name'] ?? '');
                $language = (string) ($row['language'] ?? '');
                if ($name === '' || $language === '') {
                    continue;
                }

                $bodyText = $this->extractBodyFromComponents($row['components'] ?? []);
                $header = $this->extractHeaderFromComponents($row['components'] ?? []);
                $footer = $this->extractFooterFromComponents($row['components'] ?? []);
                $buttons = $this->extractButtonsFromComponents($row['components'] ?? []);

                WhatsAppMetaTemplate::updateOrCreate(
                    ['name' => $name, 'language' => $language],
                    [
                        'meta_template_id' => $row['id'] ?? null,
                        'category' => strtoupper((string) ($row['category'] ?? 'UTILITY')),
                        'status' => WhatsAppMetaTemplate::normalizeMetaStatus($row['status'] ?? 'PENDING'),
                        'body_text' => $bodyText ?: '—',
                        'header_type' => $header['type'] ?? null,
                        'header_content' => $header['content'] ?? null,
                        'footer_text' => $footer,
                        'buttons' => $buttons,
                        'body_variable_count' => $this->countBodyVariables($bodyText),
                        'components' => $row['components'] ?? null,
                        'rejection_reason' => $row['rejected_reason'] ?? $row['rejection_reason'] ?? null,
                        'quality_score' => is_array($row['quality_score'] ?? null)
                            ? json_encode($row['quality_score'], JSON_UNESCAPED_UNICODE)
                            : ($row['quality_score'] ?? null),
                        'meta_synced_at' => now(),
                    ]
                );

                $synced++;
            }
        });

        return ['success' => true, 'synced' => $synced];
    }

    /**
     * @return array{success: bool, error?: string}
     */
    public function deleteTemplate(WhatsAppMetaTemplate $template, bool $deleteFromMeta = true): array
    {
        if ($deleteFromMeta && $template->status !== WhatsAppMetaTemplate::STATUS_DRAFT) {
            $result = $this->cloud->deleteMessageTemplate($template->name, $template->language);
            if (! ($result['success'] ?? false)) {
                return ['success' => false, 'error' => $result['error'] ?? 'فشل حذف القالب من Meta'];
            }
        }

        $template->delete();

        return ['success' => true];
    }

    public function normalizeTemplateName(string $name): string
    {
        $name = Str::lower(trim($name));
        $name = preg_replace('/[^a-z0-9_]/', '_', $name) ?? $name;
        $name = preg_replace('/_+/', '_', $name) ?? $name;
        $name = trim($name, '_');

        if ($name === '') {
            throw new \InvalidArgumentException('اسم القالب مطلوب — أحرف إنجليزية صغيرة وأرقام و _ فقط.');
        }

        return $name;
    }

    public function countBodyVariables(string $body): int
    {
        preg_match_all('/\{\{(\d+)\}\}/', $body, $matches);

        if (empty($matches[1])) {
            return 0;
        }

        return max(array_map('intval', $matches[1]));
    }

    public function countHeaderVariables(?string $headerType, ?string $headerContent): int
    {
        if (strtolower((string) $headerType) !== 'text' || trim((string) $headerContent) === '') {
            return 0;
        }

        return $this->countBodyVariables((string) $headerContent);
    }

    /**
     * @return array{
     *     name: string,
     *     language: string,
     *     body_text: ?string,
     *     body_variable_count: int,
     *     header_type: ?string,
     *     header_content: ?string,
     *     header_variable_count: int
     * }|null
     */
    public function resolveApprovedTemplate(string $name, string $language): ?array
    {
        $name = trim($name);
        $language = trim($language);

        if ($name === '' || $language === '') {
            return null;
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('whatsapp_meta_templates')) {
            $tpl = WhatsAppMetaTemplate::query()
                ->where('name', $name)
                ->where('language', $language)
                ->where('status', WhatsAppMetaTemplate::STATUS_APPROVED)
                ->first();

            if ($tpl) {
                $bodyText = (string) ($tpl->body_text ?? '');
                $bodyCount = (int) $tpl->body_variable_count;
                if ($bodyCount < 1 && $bodyText !== '') {
                    $bodyCount = $this->countBodyVariables($bodyText);
                }

                return [
                    'name' => $tpl->name,
                    'language' => $tpl->language,
                    'body_text' => $bodyText !== '' ? $bodyText : null,
                    'body_variable_count' => $bodyCount,
                    'header_type' => $tpl->header_type,
                    'header_content' => $tpl->header_content,
                    'header_variable_count' => $this->countHeaderVariables($tpl->header_type, $tpl->header_content),
                    'buttons' => is_array($tpl->buttons) ? $tpl->buttons : [],
                ];
            }
        }

        $listed = $this->cloud->listApprovedTemplates();
        foreach ($listed['templates'] ?? [] as $row) {
            if (($row['name'] ?? '') === $name && ($row['language'] ?? '') === $language) {
                return [
                    'name' => $name,
                    'language' => $language,
                    'body_text' => $row['body_text'] ?? null,
                    'body_variable_count' => (int) ($row['body_variable_count'] ?? 0),
                    'header_type' => $row['header_type'] ?? null,
                    'header_content' => $row['header_content'] ?? null,
                    'header_variable_count' => (int) ($row['header_variable_count'] ?? 0),
                ];
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    public function validateDraftFromRequest(\Illuminate\Http\Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:512',
            'language' => 'required|string|max:20',
            'category' => 'required|in:AUTHENTICATION,UTILITY,MARKETING',
            'body_text' => 'required|string|max:1024',
            'header_type' => 'nullable|in:text,image,video,document',
            'header_content' => 'nullable|string|max:500',
            'footer_text' => 'nullable|string|max:60',
            'buttons' => 'nullable|array|max:10',
            'buttons.*.type' => 'nullable|in:QUICK_REPLY,URL,PHONE_NUMBER',
            'buttons.*.text' => 'nullable|string|max:25',
            'buttons.*.url' => 'nullable|string|max:500',
            'buttons.*.url_example' => 'nullable|string|max:200',
            'buttons.*.phone' => 'nullable|string|max:30',
            'submit_now' => 'nullable|boolean',
        ], [
            'name.required' => 'اسم القالب مطلوب',
            'body_text.required' => 'محتوى الرسالة مطلوب',
            'category.required' => 'فئة القالب مطلوبة',
        ]);
    }

    /**
     * @param  array<int|string, string|null>  $variables
     * @return array{components: array<int, array<string, mixed>>, error?: string}
     */
    public function buildSendComponents(string $name, string $language, array $variables): array
    {
        $definition = $this->resolveApprovedTemplate($name, $language);
        if ($definition === null) {
            return [
                'components' => [],
                'error' => 'القالب أو اللغة غير موجودين — اختر القالب من القائمة بنفس اللغة المعتمدة في Meta (مثل ar أو en_US).',
            ];
        }

        $components = [];

        if ($definition['header_variable_count'] > 0) {
            $headerValue = $this->scalarToString($variables['header_1'] ?? $variables['h1'] ?? '');
            if ($headerValue === '') {
                return ['components' => [], 'error' => 'متغير عنوان القالب (Header) مطلوب'];
            }

            $components[] = [
                'type' => 'header',
                'parameters' => [['type' => 'text', 'text' => $headerValue]],
            ];
        }

        $bodyCount = $definition['body_variable_count'];
        if ($bodyCount > 0) {
            $parameters = [];
            for ($i = 1; $i <= $bodyCount; $i++) {
                $value = $this->scalarToString($variables[$i] ?? $variables[(string) $i] ?? '');
                if ($value === '') {
                    return ['components' => [], 'error' => "متغير القالب {$i} مطلوب"];
                }
                $parameters[] = ['type' => 'text', 'text' => $value];
            }

            $components[] = ['type' => 'body', 'parameters' => $parameters];
        }

        $buttonError = $this->appendButtonSendComponents($components, $definition, $variables);
        if ($buttonError !== null) {
            return ['components' => [], 'error' => $buttonError];
        }

        return ['components' => $components];
    }

    /**
     * @param  array<int|string, string|null>  $variables
     */
    public function renderTemplatePreview(string $name, string $language, array $variables): ?string
    {
        $definition = $this->resolveApprovedTemplate($name, $language);
        if ($definition === null) {
            return null;
        }

        $parts = [];

        $headerType = strtolower((string) ($definition['header_type'] ?? ''));
        $headerContent = trim((string) ($definition['header_content'] ?? ''));
        if ($headerType === 'text' && $headerContent !== '') {
            $headerText = $headerContent;
            if (($definition['header_variable_count'] ?? 0) > 0) {
                $headerVal = $this->scalarToString($variables['header_1'] ?? $variables['h1'] ?? '');
                $headerText = preg_replace('/\{\{1\}\}/', $headerVal, $headerText) ?? $headerText;
            }
            $parts[] = $headerText;
        }

        $bodyText = trim((string) ($definition['body_text'] ?? ''));
        if ($bodyText !== '') {
            $bodyCount = (int) ($definition['body_variable_count'] ?? 0);
            for ($i = 1; $i <= $bodyCount; $i++) {
                $val = $this->scalarToString($variables[$i] ?? $variables[(string) $i] ?? '');
                $bodyText = str_replace('{{'.$i.'}}', $val, $bodyText);
            }
            $parts[] = $bodyText;
        }

        $rendered = trim(implode("\n\n", array_filter($parts, fn ($p) => trim((string) $p) !== '')));

        return $rendered !== '' ? $rendered : null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $components
     * @param  array<string, mixed>  $definition
     * @param  array<int|string, string|null>  $variables
     */
    private function appendButtonSendComponents(array &$components, array $definition, array $variables): ?string
    {
        $buttons = $definition['buttons'] ?? [];
        if (! is_array($buttons) || $buttons === []) {
            return null;
        }

        foreach ($buttons as $index => $btn) {
            if (! is_array($btn) || strtoupper((string) ($btn['type'] ?? '')) !== 'URL') {
                continue;
            }

            $url = $this->scalarToString($btn['url'] ?? '');
            if (! preg_match('/\{\{(\d+)\}\}/', $url, $matches)) {
                continue;
            }

            $varIndex = (int) $matches[1];
            $value = $this->scalarToString(
                $variables['button_'.$index]
                ?? $variables['button_url_'.$index]
                ?? $variables[$varIndex]
                ?? $variables[(string) $varIndex]
                ?? ''
            );

            if ($value === '') {
                return 'متغير زر الرابط «'.((string) ($btn['text'] ?? 'URL')).'» مطلوب عند الإرسال.';
            }

            $components[] = [
                'type' => 'button',
                'sub_type' => 'url',
                'index' => (string) $index,
                'parameters' => [['type' => 'text', 'text' => $value]],
            ];
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    public function templateMetaForList(WhatsAppMetaTemplate $template): array
    {
        $bodyText = (string) ($template->body_text ?? '');
        $bodyCount = (int) $template->body_variable_count;
        if ($bodyCount < 1 && $bodyText !== '') {
            $bodyCount = $this->countBodyVariables($bodyText);
        }

        return [
            'name' => $template->name,
            'language' => $template->language,
            'category' => $template->category,
            'label' => $template->displayLabel().' ('.$template->categoryLabel().')',
            'source' => 'database',
            'body_text' => $bodyText !== '' ? $bodyText : null,
            'body_variable_count' => $bodyCount,
            'header_type' => $template->header_type,
            'header_content' => $template->header_content,
            'header_variable_count' => $this->countHeaderVariables($template->header_type, $template->header_content),
            'buttons' => is_array($template->buttons) ? $template->buttons : [],
        ];
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    public function templateMetaFromMetaApiRow(array $row): array
    {
        $name = (string) ($row['name'] ?? '');
        $language = (string) ($row['language'] ?? '');
        $category = (string) ($row['category'] ?? '');
        $bodyText = $this->extractBodyFromComponents($row['components'] ?? []);
        $header = $this->extractHeaderFromComponents($row['components'] ?? []);
        $bodyCount = $this->countBodyVariables($bodyText);
        $headerCount = $this->countHeaderVariables($header['type'], $header['content']);

        return [
            'name' => $name,
            'language' => $language,
            'category' => $category,
            'label' => $name . ' · ' . $language . ($category !== '' ? ' (' . $category . ')' : ''),
            'source' => 'meta_api',
            'body_text' => $bodyText !== '' ? $bodyText : null,
            'body_variable_count' => $bodyCount,
            'header_type' => $header['type'],
            'header_content' => $header['content'],
            'header_variable_count' => $headerCount,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int, array<string, mixed>>
     */
    public function buildMetaComponents(array $data): array
    {
        $data = $this->prepareMetaTemplateData($data);
        $components = [];

        $headerType = strtolower((string) ($data['header_type'] ?? ''));
        $headerContent = trim((string) ($data['header_content'] ?? ''));

        if ($headerType === 'text' && $headerContent !== '') {
            $components[] = [
                'type' => 'HEADER',
                'format' => 'TEXT',
                'text' => $headerContent,
            ];
        } elseif (in_array($headerType, ['image', 'video', 'document'], true) && $headerContent !== '') {
            $format = strtoupper($headerType);
            $components[] = [
                'type' => 'HEADER',
                'format' => $format,
                'example' => [
                    'header_url' => [$headerContent],
                ],
            ];
        }

        $bodyText = $this->normalizeMetaBodyText((string) ($data['body_text'] ?? ''));
        if ($bodyText !== '') {
            $bodyComponent = [
                'type' => 'BODY',
                'text' => $bodyText,
            ];

            $varCount = $this->countBodyVariables($bodyText);
            if ($varCount > 0) {
                $examples = $this->sanitizeBodyExamples(
                    $bodyText,
                    $this->defaultBodyExamples($varCount, $data)
                );
                $bodyComponent['example'] = [
                    'body_text' => [$examples],
                ];
            }

            $components[] = $bodyComponent;
        }

        $footer = trim((string) ($data['footer_text'] ?? ''));
        if ($footer !== '') {
            $components[] = [
                'type' => 'FOOTER',
                'text' => $footer,
            ];
        }

        $buttons = $this->sanitizeButtons($data['buttons'] ?? []);
        if ($buttons !== []) {
            $metaButtons = [];
            foreach ($buttons as $btn) {
                $type = strtoupper((string) ($btn['type'] ?? ''));
                $text = $this->normalizeMetaButtonText(trim((string) ($btn['text'] ?? '')));
                if ($text === '') {
                    continue;
                }

                if ($type === 'QUICK_REPLY') {
                    $metaButtons[] = ['type' => 'QUICK_REPLY', 'text' => $text];
                } elseif ($type === 'URL') {
                    $url = $this->normalizeMetaButtonUrl(trim((string) ($btn['url'] ?? '')));
                    if ($url !== '') {
                        $metaBtn = ['type' => 'URL', 'text' => $text, 'url' => $url];
                        if (preg_match('/\{\{\d+\}\}/', $url)) {
                            $suffix = $this->resolveUrlButtonExampleSuffix(
                                $url,
                                trim((string) ($btn['url_example'] ?? ''))
                            );
                            if ($suffix === '') {
                                continue;
                            }
                            $metaBtn['example'] = [$suffix];
                        }
                        $metaButtons[] = $metaBtn;
                    }
                } elseif ($type === 'PHONE_NUMBER') {
                    $phone = trim((string) ($btn['phone'] ?? ''));
                    if ($phone !== '') {
                        $metaButtons[] = ['type' => 'PHONE_NUMBER', 'text' => $text, 'phone_number' => $phone];
                    }
                }
            }

            if ($metaButtons !== []) {
                $components[] = [
                    'type' => 'BUTTONS',
                    'buttons' => $metaButtons,
                ];
            }
        }

        return $components;
    }

    /**
     * @param  mixed  $buttons
     * @return array<int, array<string, string>>
     */
    public function sanitizeButtons(mixed $buttons): array
    {
        if (! is_array($buttons)) {
            return [];
        }

        $out = [];
        foreach ($buttons as $btn) {
            if (! is_array($btn)) {
                continue;
            }
            $type = strtoupper($this->scalarToString($btn['type'] ?? 'QUICK_REPLY'));
            $text = $this->scalarToString($btn['text'] ?? '');
            if ($text === '') {
                continue;
            }
            $row = ['type' => $type, 'text' => $text];
            if ($type === 'URL') {
                $row['url'] = $this->scalarToString($btn['url'] ?? '');
                $row['url_example'] = $this->scalarToString($btn['url_example'] ?? '');
            }
            if ($type === 'PHONE_NUMBER') {
                $row['phone'] = $this->scalarToString($btn['phone'] ?? '');
            }
            $out[] = $row;
        }

        return array_slice($out, 0, 10);
    }

    /**
     * @param  mixed  $buttons
     * @return array<int, array<string, string>>
     */
    public function normalizeButtonsForMeta(
        mixed $buttons,
        string $bodyText = '',
        string $headerType = '',
        ?string $headerContent = null
    ): array {
        $sanitized = $this->sanitizeButtons($buttons);
        $sanitized = array_values(array_filter(
            $sanitized,
            fn (array $btn) => ! $this->isWhatsappGroupUrlButton($btn)
        ));

        foreach ($sanitized as &$btn) {
            if (strtoupper((string) ($btn['type'] ?? '')) === 'URL') {
                $url = (string) ($btn['url'] ?? '');
                if (! preg_match('/\{\{\d+\}\}/', $url)) {
                    $btn['url'] = $this->normalizeMetaButtonUrl($url);
                }
            }
            $btn['text'] = $this->normalizeMetaButtonText((string) ($btn['text'] ?? ''));
        }
        unset($btn);

        return array_values(array_filter(
            $sanitized,
            fn (array $btn) => trim((string) ($btn['text'] ?? '')) !== ''
        ));
    }

    /**
     * Meta rejects chat.whatsapp.com in URL buttons (even dynamic {{N}}).
     * Move the group link into the body as a text variable instead.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function prepareMetaTemplateData(array $data): array
    {
        $headerType = strtolower((string) ($data['header_type'] ?? ''));
        $headerContent = trim((string) ($data['header_content'] ?? ''));
        $bodyText = $this->normalizeMetaBodyText((string) ($data['body_text'] ?? ''));
        $buttons = is_array($data['buttons'] ?? null) ? $this->sanitizeButtons($data['buttons']) : [];

        $keptButtons = [];
        $groupVarIndex = null;

        foreach ($buttons as $btn) {
            if (! $this->isWhatsappGroupUrlButton($btn)) {
                $keptButtons[] = $btn;

                continue;
            }

            $url = trim((string) ($btn['url'] ?? ''));
            if (preg_match('/\{\{(\d+)\}\}/', $url, $matches)) {
                $groupVarIndex = (int) $matches[1];
            } else {
                $textParts = [$bodyText];
                if ($headerType === 'text' && $headerContent !== '') {
                    $textParts[] = $headerContent;
                }
                $groupVarIndex = $this->maxVariableIndex(...$textParts) + 1;
            }
        }

        if ($groupVarIndex !== null) {
            $placeholder = '{{'.$groupVarIndex.'}}';
            if (! str_contains($bodyText, $placeholder)) {
                $linkLine = 'https://chat.whatsapp.com/'.$placeholder;
                if (preg_match('/\n?\n?للانضمام[^\n]*(?:الزر|الرابط)[^\n]*/u', $bodyText)) {
                    $bodyText = preg_replace(
                        '/\n?\n?للانضمام[^\n]*(?:الزر|الرابط)[^\n]*/u',
                        "\n\nللانضمام لجروب الورشة:\n".$linkLine,
                        $bodyText,
                        1
                    ) ?? $bodyText;
                } else {
                    $bodyText = rtrim($bodyText)."\n\nللانضمام لجروب الورشة:\n".$linkLine;
                }
            }

            $data['buttons'] = $keptButtons;
        }

        $data['body_text'] = $this->normalizeGroupInviteBodyFormat($bodyText);
        $data['body_variable_count'] = $this->countBodyVariables($bodyText);

        return $data;
    }

    /**
     * @param  array<string, mixed>  $btn
     */
    public function isWhatsappGroupUrlButton(array $btn): bool
    {
        if (strtoupper((string) ($btn['type'] ?? '')) !== 'URL') {
            return false;
        }

        return str_contains(strtolower(trim((string) ($btn['url'] ?? ''))), 'chat.whatsapp.com');
    }

    public function normalizeGroupInviteBodyFormat(string $body): string
    {
        if (preg_match('/chat\.whatsapp\.com\/\{\{\d+\}\}/i', $body)) {
            return $body;
        }

        return preg_replace(
            '/(للانضمام[^\n]*\n)(\{\{(\d+)\}\})/u',
            '$1https://chat.whatsapp.com/$2',
            $body,
            1
        ) ?? $body;
    }

    public function bodyVariableExpectsWhatsappInviteCode(string $body, int $varIndex): bool
    {
        return (bool) preg_match(
            '/chat\.whatsapp\.com\/\{\{'.$varIndex.'\}\}/i',
            $body
        );
    }

    /**
     * Meta rejects full URLs inside body variable examples — use invite code suffix only.
     *
     * @param  array<int, string>  $examples
     * @return array<int, string>
     */
    public function sanitizeBodyExamples(string $bodyText, array $examples): array
    {
        foreach ($examples as $index => $example) {
            $varNumber = $index + 1;

            if ($this->bodyVariableExpectsWhatsappInviteCode($bodyText, $varNumber)) {
                $examples[$index] = $this->inviteCodeFromExampleValue($example) ?? 'ExampleInviteCode';

                continue;
            }

            if (preg_match('#https?://#i', $example)) {
                $examples[$index] = 'نص_مثال_'.$varNumber;
            }
        }

        return $examples;
    }

    public function inviteCodeFromExampleValue(mixed $value): ?string
    {
        $value = $this->scalarToString($value);
        if ($value === '') {
            return null;
        }

        if (! str_contains($value, '://') && ! str_contains($value, '/')) {
            return $value;
        }

        return $this->extractWhatsappGroupInviteCode($value)
            ?? (preg_match('#chat\.whatsapp\.com/([A-Za-z0-9_-]+)#i', $value, $m) ? $m[1] : null);
    }

    public function normalizeMetaLanguage(string $language): string
    {
        $language = trim($language);

        return match ($language) {
            'ar' => 'ar_EG',
            default => $language,
        };
    }

    public function normalizeMetaBodyText(string $body): string
    {
        $body = str_replace(["\r\n", "\r"], "\n", $body);

        return trim($body);
    }

    public function normalizeMetaButtonUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '' || preg_match('/\{\{\d+\}\}/', $url)) {
            return $url;
        }

        $parts = parse_url($url);
        if (! is_array($parts) || empty($parts['host'])) {
            return $url;
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? 'https'));
        $host = strtolower((string) $parts['host']);
        $path = (string) ($parts['path'] ?? '');
        $path = $path !== '' ? rtrim($path, '/') : '';

        return $scheme.'://'.$host.$path;
    }

    /**
     * Meta uses one global variable index across body/header/buttons.
     * Static chat.whatsapp.com invite links in URL buttons are rejected — use {{N}} suffix.
     *
     * @param  array<int, array<string, string>>  $buttons
     * @return array<int, array<string, string>>
     */
    public function normalizeGroupInviteButtons(
        array $buttons,
        string $bodyText,
        string $headerType = '',
        string $headerContent = ''
    ): array {
        $textParts = [$bodyText];
        if (strtolower($headerType) === 'text' && trim($headerContent) !== '') {
            $textParts[] = $headerContent;
        }

        $textMaxVar = $this->maxVariableIndex(...$textParts);
        $nextVarIndex = $textMaxVar;

        foreach ($buttons as &$btn) {
            if (strtoupper((string) ($btn['type'] ?? '')) !== 'URL') {
                continue;
            }

            $url = trim((string) ($btn['url'] ?? ''));
            if ($url === '') {
                continue;
            }

            if (preg_match('#^https://chat\.whatsapp\.com/\{\{(\d+)\}\}$#i', $url, $matches)) {
                $currentVar = (int) $matches[1];
                if ($currentVar <= $textMaxVar) {
                    $nextVarIndex++;
                    $currentVar = $nextVarIndex;
                    $btn['url'] = 'https://chat.whatsapp.com/{{'.$currentVar.'}}';
                } else {
                    $nextVarIndex = max($nextVarIndex, $currentVar);
                }

                if (trim((string) ($btn['url_example'] ?? '')) !== '') {
                    $btn['url_example'] = $this->resolveUrlButtonExampleSuffix(
                        (string) $btn['url'],
                        (string) $btn['url_example']
                    );
                }

                continue;
            }

            $inviteCode = $this->extractWhatsappGroupInviteCode($url);
            if ($inviteCode === null) {
                continue;
            }

            $nextVarIndex++;
            $btn['url'] = 'https://chat.whatsapp.com/{{'.$nextVarIndex.'}}';

            if (trim((string) ($btn['url_example'] ?? '')) === '') {
                $btn['url_example'] = $inviteCode;
            } else {
                $btn['url_example'] = $this->resolveUrlButtonExampleSuffix(
                    (string) $btn['url'],
                    (string) $btn['url_example']
                );
            }
        }
        unset($btn);

        return $buttons;
    }

    public function extractWhatsappGroupInviteCode(string $url): ?string
    {
        $url = trim($url);
        if ($url === '') {
            return null;
        }

        if (preg_match('#^https://chat\.whatsapp\.com/([A-Za-z0-9_-]+)$#i', $url, $matches)) {
            return $matches[1];
        }

        if (preg_match('#^https://chat\.whatsapp\.com/\{\{(\d+)\}\}$#i', $url)) {
            $example = trim((string) (parse_url($url, PHP_URL_PATH) ?? ''));
            // For dynamic URLs, code comes from url_example — handled by caller.
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH);
        if (! is_string($path) || $path === '') {
            return null;
        }

        $segments = array_values(array_filter(explode('/', trim($path, '/'))));
        if ($segments === []) {
            return null;
        }

        $last = (string) end($segments);
        if ($last === '' || preg_match('/^\{\{\d+\}\}$/', $last)) {
            return null;
        }

        if (strtolower((string) parse_url($url, PHP_URL_HOST)) !== 'chat.whatsapp.com') {
            return null;
        }

        return $last;
    }

    /**
     * @param  string  ...$parts
     */
    public function maxVariableIndex(string ...$parts): int
    {
        $max = 0;

        foreach ($parts as $part) {
            preg_match_all('/\{\{(\d+)\}\}/', $part, $matches);
            foreach ($matches[1] ?? [] as $number) {
                $max = max($max, (int) $number);
            }
        }

        return $max;
    }

    public function normalizeMetaButtonText(string $text): string
    {
        $text = trim($text);
        if ($text === '') {
            return '';
        }

        if (mb_strlen($text) > 25) {
            return mb_substr($text, 0, 25);
        }

        return $text;
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<int, string>
     */
    public function defaultBodyExamples(int $count, array $context = []): array
    {
        $inviteExample = trim((string) ($context['group_invite_example'] ?? ''));
        if ($inviteExample === '') {
            $inviteExample = 'Ld0j8PUAprmCnDi65uUqTC';
        } else {
            $inviteExample = $this->inviteCodeFromExampleValue($inviteExample) ?? 'Ld0j8PUAprmCnDi65uUqTC';
        }

        $pool = ['أحمد', 'ورشة Mindlytics', $inviteExample, '201012345678', 'أونلاين', 'القاهرة'];

        $examples = [];
        for ($i = 1; $i <= $count; $i++) {
            $examples[] = $pool[$i - 1] ?? ('sample_'.$i);
        }

        return $examples;
    }

    /**
     * @param  array<int, array<string, mixed>>  $components
     */
    public function validateMetaComponents(array $components): ?string
    {
        $bodyText = '';
        $headerText = '';

        foreach ($components as $component) {
            $type = strtoupper((string) ($component['type'] ?? ''));
            if ($type === 'BODY') {
                $bodyText = (string) ($component['text'] ?? '');
            }
            if ($type === 'HEADER' && strtoupper((string) ($component['format'] ?? '')) === 'TEXT') {
                $headerText = (string) ($component['text'] ?? '');
            }
        }

        $textMaxVar = $this->maxVariableIndex($bodyText, $headerText);

        foreach ($components as $component) {
            if (strtoupper((string) ($component['type'] ?? '')) !== 'BODY') {
                continue;
            }

            $examples = $component['example']['body_text'][0] ?? [];
            if (! is_array($examples)) {
                continue;
            }

            foreach ($examples as $index => $example) {
                $varNumber = (int) $index + 1;
                $example = (string) $example;

                if ($this->bodyVariableExpectsWhatsappInviteCode($bodyText, $varNumber)
                    && preg_match('#https?://#i', $example)) {
                    return 'مثال {{'.$varNumber.'}} يجب أن يكون كود الدعوة فقط — الصيغة في النص: https://chat.whatsapp.com/{{'.$varNumber.'}}';
                }

                if (! $this->bodyVariableExpectsWhatsappInviteCode($bodyText, $varNumber)
                    && preg_match('#https?://#i', $example)) {
                    return 'مثال {{'.$varNumber.'}} لا يجب أن يحتوي رابطاً.';
                }
            }
        }

        foreach ($components as $component) {
            if (strtoupper((string) ($component['type'] ?? '')) !== 'BUTTONS') {
                continue;
            }

            foreach ($component['buttons'] ?? [] as $btn) {
                if (! is_array($btn) || strtoupper((string) ($btn['type'] ?? '')) !== 'URL') {
                    continue;
                }

                $url = (string) ($btn['url'] ?? '');
                if ($url === '') {
                    return 'رابط زر URL مطلوب.';
                }

                if (preg_match('#^https://chat\.whatsapp\.com/[A-Za-z0-9_-]+$#i', $url)
                    || preg_match('#^https://chat\.whatsapp\.com/\{\{\d+\}\}$#i', $url)) {
                    return 'Meta لا يقبل روابط chat.whatsapp.com في أزرار URL — ضع رابط الجروب في نص الرسالة كمتغير {{3}} بدلاً من زر.';
                }

                if (preg_match('/\{\{(\d+)\}\}/', $url, $matches)) {
                    $buttonVar = (int) $matches[1];
                    if ($buttonVar <= $textMaxVar) {
                        return 'متغير زر URL {{'.$buttonVar.'}} يتعارض مع متغيرات النص — استخدم {{'.($textMaxVar + 1).'}} أو أعلى لرابط الجروب.';
                    }

                    $examples = $btn['example'] ?? [];
                    if (! is_array($examples) || trim((string) ($examples[0] ?? '')) === '') {
                        return 'زر URL الديناميكي يحتاج مثالاً (كود الدعوة فقط، بدون الرابط الكامل).';
                    }

                    continue;
                }

                if (! filter_var($url, FILTER_VALIDATE_URL)) {
                    return 'رابط زر URL غير صالح — استخدم https:// فقط بدون مسافات.';
                }

                if (preg_match('/[\?#]/', $url)) {
                    return 'رابط زر URL يجب ألا يحتوي على ? أو # — للجروبات استخدم chat.whatsapp.com/{{N}}.';
                }
            }
        }

        return null;
    }

    /**
     * Meta expects only the dynamic URL suffix in button examples, not the full URL.
     */
    public function resolveUrlButtonExampleSuffix(string $urlTemplate, string $example): string
    {
        $example = trim($example);
        if ($example === '') {
            return '';
        }

        if (! preg_match('/\{\{\d+\}\}/', $urlTemplate)) {
            return $example;
        }

        if (! str_contains($example, '://') && ! str_contains($example, '/')) {
            return $example;
        }

        $staticPrefix = preg_replace('/\{\{\d+\}\}/', '', $urlTemplate) ?? '';
        if ($staticPrefix !== '' && str_starts_with($example, $staticPrefix)) {
            return trim(substr($example, strlen($staticPrefix)), '/');
        }

        $path = parse_url($example, PHP_URL_PATH);
        if (is_string($path) && $path !== '') {
            $segments = array_values(array_filter(explode('/', trim($path, '/'))));
            if ($segments !== []) {
                return (string) end($segments);
            }
        }

        return preg_replace('/\{\{\d+\}\}/', $example, $urlTemplate) === $urlTemplate
            ? $example
            : $example;
    }

    /**
     * @param  array<int, mixed>  $components
     */
    private function extractBodyFromComponents(array $components): string
    {
        foreach ($components as $c) {
            if (is_array($c) && strtoupper((string) ($c['type'] ?? '')) === 'BODY') {
                return (string) ($c['text'] ?? '');
            }
        }

        return '';
    }

    /**
     * @param  array<int, mixed>  $components
     * @return array{type: ?string, content: ?string}
     */
    private function extractHeaderFromComponents(array $components): array
    {
        foreach ($components as $c) {
            if (! is_array($c) || strtoupper((string) ($c['type'] ?? '')) !== 'HEADER') {
                continue;
            }
            $format = strtolower((string) ($c['format'] ?? 'text'));

            return [
                'type' => $format === 'text' ? 'text' : $format,
                'content' => $format === 'text'
                    ? (string) ($c['text'] ?? '')
                    : (string) (($c['example']['header_url'][0] ?? $c['example']['header_handle'][0] ?? '') ?: ''),
            ];
        }

        return ['type' => null, 'content' => null];
    }

    /**
     * @param  array<int, mixed>  $components
     */
    private function extractFooterFromComponents(array $components): ?string
    {
        foreach ($components as $c) {
            if (is_array($c) && strtoupper((string) ($c['type'] ?? '')) === 'FOOTER') {
                return (string) ($c['text'] ?? '');
            }
        }

        return null;
    }

    /**
     * @param  array<int, mixed>  $components
     * @return array<int, array<string, string>>
     */
    private function extractButtonsFromComponents(array $components): array
    {
        foreach ($components as $c) {
            if (! is_array($c) || strtoupper((string) ($c['type'] ?? '')) !== 'BUTTONS') {
                continue;
            }
            $buttons = [];
            foreach ($c['buttons'] ?? [] as $btn) {
                if (! is_array($btn)) {
                    continue;
                }
                $type = strtoupper((string) ($btn['type'] ?? ''));
                $row = [
                    'type' => $type,
                    'text' => (string) ($btn['text'] ?? ''),
                ];
                if ($type === 'URL') {
                    $row['url'] = $this->scalarToString($btn['url'] ?? '');
                    $example = $btn['example'] ?? null;
                    if ($row['url'] === '' && is_array($example)) {
                        $row['url_example'] = $this->scalarToString($example[0] ?? '');
                    }
                }
                if ($type === 'PHONE_NUMBER') {
                    $row['phone'] = $this->scalarToString($btn['phone_number'] ?? $btn['phone'] ?? '');
                }
                $buttons[] = $row;
            }

            return $buttons;
        }

        return [];
    }
}
