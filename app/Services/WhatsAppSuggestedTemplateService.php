<?php

namespace App\Services;

use App\Models\WhatsAppMetaTemplate;
use App\Models\WhatsAppSuggestedTemplate;
use Illuminate\Support\Str;

class WhatsAppSuggestedTemplateService
{
    public function __construct(
        private WhatsAppTemplateService $templates
    ) {}

    /**
     * @param  array<int, string>  $variables
     */
    public function convertNamedVariablesToMeta(string $body, array $variables): string
    {
        $result = $body;
        $index = 1;

        foreach ($variables as $variable) {
            $variable = trim((string) $variable);
            if ($variable === '') {
                continue;
            }

            $result = str_replace('{{'.$variable.'}}', '{{'.$index.'}}', $result);
            $index++;
        }

        return $this->templates->normalizeMetaBodyText($result);
    }

    public function createOrUpdateMetaDraft(WhatsAppSuggestedTemplate $suggested, ?int $userId = null): WhatsAppMetaTemplate
    {
        $variables = is_array($suggested->variables) ? array_values($suggested->variables) : [];
        $metaBody = $this->convertNamedVariablesToMeta((string) $suggested->body, $variables);
        $language = $suggested->language === 'en' ? 'en' : 'ar';
        $baseName = $this->templates->normalizeTemplateName('mindlytics_'.$suggested->key);

        $data = [
            'name' => $baseName,
            'language' => $language,
            'category' => 'UTILITY',
            'body_text' => $metaBody,
            'header_type' => '',
            'header_content' => '',
            'footer_text' => '',
            'buttons' => [],
        ];

        $linked = $suggested->metaTemplate;
        if ($linked && $linked->isEditable()) {
            $updated = $this->templates->updateDraft($linked, $data);
            $suggested->update(['meta_template_id' => $updated->id]);

            return $updated;
        }

        $existing = WhatsAppMetaTemplate::query()
            ->where('name', $baseName)
            ->where('language', $language)
            ->first();

        if ($existing && $existing->isEditable()) {
            $updated = $this->templates->updateDraft($existing, $data);
            $suggested->update(['meta_template_id' => $updated->id]);

            return $updated;
        }

        if ($existing && ! $existing->isEditable()) {
            $data['name'] = $this->templates->normalizeTemplateName($baseName.'_'.Str::lower(Str::random(4)));
        }

        $draft = $this->templates->createDraft($data, $userId);
        $suggested->update(['meta_template_id' => $draft->id]);

        return $draft;
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function validateFromRequest(array $input): array
    {
        $variablesRaw = trim((string) ($input['variables_text'] ?? ''));
        $variables = $variablesRaw === ''
            ? []
            : array_values(array_filter(array_map(
                fn ($v) => preg_replace('/[^a-z0-9_]/', '', strtolower(trim($v))) ?? '',
                preg_split('/[\s,]+/', $variablesRaw) ?: []
            )));

        return [
            'title' => trim((string) ($input['title'] ?? '')),
            'category' => trim((string) ($input['category'] ?? 'intro')),
            'language' => in_array($input['language'] ?? 'ar', ['ar', 'en'], true) ? $input['language'] : 'ar',
            'body' => trim((string) ($input['body'] ?? '')),
            'help' => trim((string) ($input['help'] ?? '')) ?: null,
            'variables' => $variables,
            'is_active' => (bool) ($input['is_active'] ?? true),
            'sort_order' => max(0, (int) ($input['sort_order'] ?? 100)),
        ];
    }
}
