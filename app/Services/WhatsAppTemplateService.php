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

    /**
     * @param  array<string, mixed>  $data
     */
    public function createDraft(array $data, ?int $userId = null): WhatsAppMetaTemplate
    {
        $name = $this->normalizeTemplateName((string) ($data['name'] ?? ''));
        $bodyText = trim((string) ($data['body_text'] ?? ''));

        return WhatsAppMetaTemplate::create([
            'name' => $name,
            'language' => (string) ($data['language'] ?? 'ar'),
            'category' => strtoupper((string) ($data['category'] ?? 'UTILITY')),
            'status' => WhatsAppMetaTemplate::STATUS_DRAFT,
            'body_text' => $bodyText,
            'header_type' => $data['header_type'] ?: null,
            'header_content' => $data['header_content'] ?? null,
            'footer_text' => $data['footer_text'] ?? null,
            'buttons' => $this->sanitizeButtons($data['buttons'] ?? []),
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

        $bodyText = trim((string) ($data['body_text'] ?? $template->body_text));
        $merged = array_merge($template->toArray(), $data, [
            'name' => $this->normalizeTemplateName((string) ($data['name'] ?? $template->name)),
            'body_text' => $bodyText,
            'buttons' => $this->sanitizeButtons($data['buttons'] ?? $template->buttons ?? []),
            'body_variable_count' => $this->countBodyVariables($bodyText),
        ]);

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

        $components = $this->buildMetaComponents($template->toArray());
        $mediaHeader = in_array($template->header_type, ['image', 'video', 'document'], true);

        if ($mediaHeader && empty($template->header_content)) {
            return ['success' => false, 'error' => 'Header الوسائط يتطلب رابط مثال (Example URL) للمراجعة من Meta.'];
        }

        if ($components === []) {
            return ['success' => false, 'error' => 'محتوى القالب فارغ — أضف نص الرسالة (Body) على الأقل.'];
        }

        $result = $this->cloud->createMessageTemplate(
            $template->name,
            $template->language,
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
            $headerValue = trim((string) ($variables['header_1'] ?? $variables['h1'] ?? ''));
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
                $value = trim((string) ($variables[$i] ?? $variables[(string) $i] ?? ''));
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

            $url = (string) ($btn['url'] ?? '');
            if (! preg_match('/\{\{(\d+)\}\}/', $url, $matches)) {
                continue;
            }

            $varIndex = (int) $matches[1];
            $value = trim((string) (
                $variables['button_'.$index]
                ?? $variables['button_url_'.$index]
                ?? $variables[$varIndex]
                ?? $variables[(string) $varIndex]
                ?? ''
            ));

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

        $bodyText = trim((string) ($data['body_text'] ?? ''));
        if ($bodyText !== '') {
            $bodyComponent = [
                'type' => 'BODY',
                'text' => $bodyText,
            ];

            $varCount = $this->countBodyVariables($bodyText);
            if ($varCount > 0) {
                $bodyComponent['example'] = [
                    'body_text' => [array_map(fn ($i) => 'مثال' . $i, range(1, $varCount))],
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
                $text = trim((string) ($btn['text'] ?? ''));
                if ($text === '') {
                    continue;
                }

                if ($type === 'QUICK_REPLY') {
                    $metaButtons[] = ['type' => 'QUICK_REPLY', 'text' => $text];
                } elseif ($type === 'URL') {
                    $url = trim((string) ($btn['url'] ?? ''));
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
            $type = strtoupper((string) ($btn['type'] ?? 'QUICK_REPLY'));
            $text = trim((string) ($btn['text'] ?? ''));
            if ($text === '') {
                continue;
            }
            $row = ['type' => $type, 'text' => $text];
            if ($type === 'URL') {
                $row['url'] = trim((string) ($btn['url'] ?? ''));
                $row['url_example'] = trim((string) ($btn['url_example'] ?? ''));
            }
            if ($type === 'PHONE_NUMBER') {
                $row['phone'] = trim((string) ($btn['phone'] ?? ''));
            }
            $out[] = $row;
        }

        return array_slice($out, 0, 10);
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
                    $row['url'] = (string) ($btn['url'] ?? '');
                }
                if ($type === 'PHONE_NUMBER') {
                    $row['phone'] = (string) ($btn['phone_number'] ?? $btn['phone'] ?? '');
                }
                $buttons[] = $row;
            }

            return $buttons;
        }

        return [];
    }
}
