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

        $components = $template->components ?? $this->buildMetaComponents($template->toArray());
        $mediaHeader = in_array($template->header_type, ['image', 'video', 'document'], true);

        if ($mediaHeader && empty($template->header_content)) {
            return ['success' => false, 'error' => 'Header الوسائط يتطلب رابط مثال (Example URL) للمراجعة من Meta.'];
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
                        $metaButtons[] = ['type' => 'URL', 'text' => $text, 'url' => $url];
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
            }
            if ($type === 'PHONE_NUMBER') {
                $row['phone'] = trim((string) ($btn['phone'] ?? ''));
            }
            $out[] = $row;
        }

        return array_slice($out, 0, 10);
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
