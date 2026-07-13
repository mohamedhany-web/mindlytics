<?php

namespace App\Services;

use App\Models\WhatsAppBatch;
use App\Models\WhatsAppMetaTemplate;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelWhatsAppCampaignService
{
    public function __construct(
        private WhatsAppTemplateService $templates,
        private WhatsAppBatchService $batches,
        private WhatsAppService $whatsapp,
        private WhatsAppCloudService $cloud,
    ) {}

    /**
     * @return array<string, string>
     */
    public function variableLabels(): array
    {
        return [
            '1' => 'اسم المستلم',
            '2' => 'اسم الجروب',
            '3' => 'كود دعوة واتساب (بعد chat.whatsapp.com/)',
        ];
    }

    public function defaultBody(): string
    {
        return "مرحباً {{1}}\n\n"
            ."ندعوك للانضمام إلى جروب واتساب: {{2}}\n\n"
            ."للانضمام افتح الرابط:\n"
            ."https://chat.whatsapp.com/{{3}}\n\n"
            ."فريق Mindlytics";
    }

    public function defaultTemplateName(string $groupName): string
    {
        $slug = Str::slug(Str::limit($groupName, 28, ''));

        return 'group_invite_'.($slug !== '' ? $slug : 'campaign').'_'.now()->format('ymdHi');
    }

    /**
     * قراءة Excel وترتيب/تطبيع الأرقام مع استبعاد المكرر والفاسد.
     *
     * @return array{
     *   recipients: Collection<int, array{name: string, phone: string, raw_phone: string}>,
     *   skipped: list<array{row: int, reason: string, value?: string}>,
     *   total_rows: int
     * }
     */
    public function parseRecipientsFromExcel(UploadedFile $file): array
    {
        $spreadsheet = IOFactory::load($file->getRealPath());
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        if ($rows === []) {
            throw new \InvalidArgumentException('ملف Excel فارغ.');
        }

        $headerRow = array_shift($rows);
        $map = $this->mapHeaders($headerRow ?? []);

        if (! isset($map['phone'])) {
            throw new \InvalidArgumentException('الملف يجب أن يحتوي على عمود الهاتف (phone / هاتف / رقم / mobile).');
        }

        $seen = [];
        $recipients = collect();
        $skipped = [];
        $totalRows = 0;

        foreach ($rows as $lineNum => $row) {
            $line = (int) $lineNum + 2;
            $rawPhone = trim((string) ($row[$map['phone']] ?? ''));
            $name = isset($map['name']) ? trim((string) ($row[$map['name']] ?? '')) : '';

            if ($rawPhone === '' && $name === '') {
                continue;
            }

            $totalRows++;

            if ($rawPhone === '') {
                $skipped[] = ['row' => $line, 'reason' => 'رقم الهاتف فارغ'];

                continue;
            }

            $formatted = $this->whatsapp->formatPhoneNumber($rawPhone);
            if ($formatted === '' || strlen($formatted) < 10) {
                $skipped[] = ['row' => $line, 'reason' => 'رقم غير صالح', 'value' => $rawPhone];

                continue;
            }

            if (isset($seen[$formatted])) {
                $skipped[] = ['row' => $line, 'reason' => 'رقم مكرر بعد التطبيع', 'value' => $rawPhone];

                continue;
            }

            $seen[$formatted] = true;
            if ($name === '') {
                $name = 'عميل';
            }

            $recipients->push([
                'name' => $name,
                'phone' => $formatted,
                'raw_phone' => $rawPhone,
            ]);
        }

        return [
            'recipients' => $recipients->values(),
            'skipped' => $skipped,
            'total_rows' => $totalRows,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{success: bool, template?: WhatsAppMetaTemplate, error?: string, message?: string}
     */
    public function createAndSubmitInviteTemplate(array $payload, ?int $userId = null): array
    {
        $this->cloud->assertReadyForBulkSend();

        $groupName = trim((string) ($payload['group_name'] ?? ''));
        $groupLink = trim((string) ($payload['group_link'] ?? ''));
        $body = trim((string) ($payload['body_text'] ?? $this->defaultBody()));
        $templateName = trim((string) ($payload['template_name'] ?? ''));
        $submitNow = (bool) ($payload['submit_now'] ?? true);

        if ($groupName === '') {
            return ['success' => false, 'error' => 'اسم الجروب مطلوب.'];
        }
        if ($groupLink === '') {
            return ['success' => false, 'error' => 'لينك جروب واتساب مطلوب.'];
        }

        $inviteCode = $this->templates->inviteCodeFromExampleValue($groupLink)
            ?? $this->templates->inviteCodeFromExampleValue(
                $this->templates->normalizeMetaButtonUrl($groupLink)
            );

        if (! $inviteCode) {
            return ['success' => false, 'error' => 'لينك الجروب غير صالح — استخدم رابط بصيغة https://chat.whatsapp.com/XXXX'];
        }

        if ($templateName === '') {
            $templateName = $this->defaultTemplateName($groupName);
        }

        $draftPayload = [
            'name' => $templateName,
            'display_name' => $this->templates->normalizeDisplayName(
                $payload['display_name'] ?? $groupName
            ),
            'body_text' => $body !== '' ? $body : $this->defaultBody(),
            'footer_text' => (string) ($payload['footer_text'] ?? 'Mindlytics'),
            'category' => strtoupper((string) ($payload['category'] ?? 'UTILITY')),
            'language' => (string) ($payload['language'] ?? 'ar_EG'),
            'buttons' => [],
            'group_invite_example' => $inviteCode,
            'example_values' => [
                '1' => 'أحمد',
                '2' => $groupName,
                '3' => $inviteCode,
            ],
        ];

        $existing = WhatsAppMetaTemplate::query()
            ->where('name', $this->templates->normalizeTemplateName($templateName))
            ->where('language', $draftPayload['language'])
            ->first();

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
                'message' => 'القالب قيد مراجعة Meta — انتظر الموافقة ثم أرسل.',
            ];
        }

        try {
            if ($existing && $existing->isEditable()) {
                $template = $this->templates->updateDraft($existing, $draftPayload);
            } else {
                $template = $this->templates->createDraft($draftPayload, $userId);
            }
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }

        if (! $submitNow) {
            return [
                'success' => true,
                'template' => $template->fresh(),
                'message' => 'تم حفظ القالب كمسودة.',
            ];
        }

        $submit = $this->templates->submitToMeta($template->fresh());
        if (! ($submit['success'] ?? false)) {
            return [
                'success' => false,
                'template' => $template->fresh(),
                'error' => $submit['error'] ?? 'فشل إرسال القالب إلى Meta',
            ];
        }

        return [
            'success' => true,
            'template' => $submit['template'] ?? $template->fresh(),
            'message' => 'تم إنشاء القالب وإرساله لـ Meta للمراجعة. بعد الاعتماد يمكنك الإرسال للمستلمين.',
        ];
    }

    /**
     * @throws \RuntimeException
     */
    public function dispatchInviteCampaign(
        UploadedFile $file,
        WhatsAppMetaTemplate $template,
        string $groupName,
        string $groupLink,
        int $createdBy,
        ?string $fileName = null
    ): WhatsAppBatch {
        $this->cloud->assertReadyForBulkSend();

        if (! $template->isSendable()) {
            throw new \RuntimeException(
                'القالب غير معتمد بعد (الحالة: '.$template->statusLabel().'). انتظر موافقة Meta أو اضغط مزامنة القوالب.'
            );
        }

        $parsed = $this->parseRecipientsFromExcel($file);
        $recipients = $parsed['recipients'];
        if ($recipients->isEmpty()) {
            throw new \RuntimeException('لا توجد أرقام صالحة في الملف بعد الترتيب والتطبيع.');
        }

        $inviteCode = $this->templates->inviteCodeFromExampleValue($groupLink)
            ?? $this->templates->inviteCodeFromExampleValue(
                $this->templates->normalizeMetaButtonUrl($groupLink)
            );

        if (! $inviteCode) {
            throw new \RuntimeException('لينك الجروب غير صالح.');
        }

        $inviteIndex = $this->groupInviteVariableIndex($template) ?? 3;
        $items = $this->buildTemplateItems($recipients, $template, $groupName, $inviteCode, $inviteIndex, $createdBy);

        if ($items->isEmpty()) {
            throw new \RuntimeException('تعذر بناء رسائل الإرسال من الملف.');
        }

        return $this->batches->createAndDispatch(
            'excel_campaign',
            null,
            'دعوة جروب — '.Str::limit($groupName, 40).' — '.now()->format('Y-m-d H:i'),
            $template->body_text,
            $items,
            $createdBy,
            [
                'send_mode' => 'template',
                'template_name' => $template->name,
                'template_language' => $template->language,
                'template_label' => $template->displayLabel(),
                'group_name' => $groupName,
                'group_link' => $this->templates->normalizeMetaButtonUrl($groupLink),
                'group_invite_code' => $inviteCode,
                'file_name' => $fileName ?: $file->getClientOriginalName(),
                'parsed_valid' => $recipients->count(),
                'parsed_skipped' => count($parsed['skipped']),
            ]
        );
    }

    public function groupInviteVariableIndex(WhatsAppMetaTemplate $template): ?int
    {
        $body = (string) ($template->body_text ?? '');
        $max = (int) $template->body_variable_count;
        if ($max < 1 && $body !== '') {
            $max = $this->templates->countBodyVariables($body);
        }

        for ($i = 1; $i <= $max; $i++) {
            if ($this->templates->bodyVariableExpectsWhatsappInviteCode($body, $i)) {
                return $i;
            }
        }

        return null;
    }

    /**
     * @param  Collection<int, array{name: string, phone: string}>  $recipients
     * @return Collection<int, array<string, mixed>>
     */
    private function buildTemplateItems(
        Collection $recipients,
        WhatsAppMetaTemplate $template,
        string $groupName,
        string $inviteCode,
        int $inviteIndex,
        int $senderId
    ): Collection {
        return $recipients->map(function (array $row) use ($template, $groupName, $inviteCode, $inviteIndex, $senderId) {
            $variables = $this->variablesForRecipient($template, $row['name'], $groupName, $inviteCode, $inviteIndex);
            $built = $this->templates->buildSendComponents($template->name, $template->language, $variables);
            if ($built['error'] ?? null) {
                throw new \RuntimeException((string) $built['error']);
            }

            $preview = (string) $template->body_text;
            foreach ($variables as $key => $value) {
                $preview = str_replace('{{'.$key.'}}', $this->templates->scalarToString($value), $preview);
            }

            return [
                'recipient_name' => $this->templates->scalarToString($row['name']),
                'phone' => $row['phone'],
                'message' => json_encode([
                    'template_name' => $template->name,
                    'language' => $template->language,
                    'components' => $built['components'],
                    'preview' => $preview,
                    'contact_name' => $row['name'],
                ], JSON_UNESCAPED_UNICODE),
                'message_type' => 'template',
                'user_id' => $senderId,
            ];
        })->values();
    }

    /**
     * @return array<int|string, string>
     */
    private function variablesForRecipient(
        WhatsAppMetaTemplate $template,
        string $name,
        string $groupName,
        string $inviteCode,
        int $inviteIndex
    ): array {
        $bodyCount = (int) $template->body_variable_count;
        if ($bodyCount < 1 && $template->body_text) {
            $bodyCount = $this->templates->countBodyVariables((string) $template->body_text);
        }

        $pool = [
            1 => $this->templates->scalarToString($name),
            2 => $this->templates->scalarToString($groupName),
            3 => $this->templates->scalarToString($inviteCode),
        ];
        $pool[$inviteIndex] = $this->templates->scalarToString($inviteCode);

        $variables = [];
        for ($i = 1; $i <= max(1, $bodyCount); $i++) {
            $variables[$i] = $pool[$i] ?? $pool[1];
        }

        return $variables;
    }

    /**
     * @param  array<string|int, mixed>  $headerRow
     * @return array{name?: string, phone?: string}
     */
    private function mapHeaders(array $headerRow): array
    {
        $map = [];
        foreach ($headerRow as $col => $label) {
            $key = mb_strtolower(trim((string) $label));
            $key = str_replace(['_', '-', ' '], '', $key);

            if (in_array($key, ['name', 'الاسم', 'اسم', 'fullname', 'fullnameالطالب', 'recipient', 'fullnameالمستلم'], true)
                || str_contains($key, 'اسم')
                || $key === 'name') {
                $map['name'] = (string) $col;
            }

            if (in_array($key, ['phone', 'mobile', 'tel', 'whatsapp', 'الهاتف', 'هاتف', 'رقم', 'رقمالهاتف', 'جوال', 'موبايل'], true)
                || str_contains($key, 'هاتف')
                || str_contains($key, 'phone')
                || str_contains($key, 'mobile')
                || str_contains($key, 'رقم')) {
                $map['phone'] = (string) $col;
            }
        }

        return $map;
    }
}
