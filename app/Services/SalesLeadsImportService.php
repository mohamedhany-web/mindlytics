<?php

namespace App\Services;

use App\Models\SalesInterestType;
use App\Models\SalesLead;
use App\Models\SalesLeadCategory;
use App\Models\SalesLeadGroup;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SalesLeadsImportService
{
    /**
     * @param  list<int>  $assigneeIds  فارغ = عملاء بدون إسناد موظف
     * @return array{created: int, skipped: int, errors: list<string>, batch_id: string, per_rep: array<int, int>}
     */
    public function import(
        UploadedFile $file,
        array $assigneeIds,
        int $categoryId,
        int $createdBy,
        string $source = 'other',
        string $defaultPriority = 'normal',
        ?int $groupId = null,
    ): array {
        $group = null;
        if ($groupId !== null) {
            $group = SalesLeadGroup::query()->find($groupId);
            if (! $group) {
                throw new \InvalidArgumentException('المجموعة المحددة غير موجودة.');
            }
        }

        $assigneeIds = array_values(array_unique(array_filter(array_map('intval', $assigneeIds))));

        $reps = collect();
        if ($assigneeIds !== []) {
            $reps = User::query()->whereIn('id', $assigneeIds)->get();
            foreach ($reps as $rep) {
                if (! $rep->isSalesEmployee()) {
                    throw new \InvalidArgumentException('الموظف «'.$rep->name.'» ليس موظف مبيعات.');
                }
            }
            if ($reps->count() !== count($assigneeIds)) {
                throw new \InvalidArgumentException('بعض الموظفين المحددين غير موجودين.');
            }
        }

        $category = SalesLeadCategory::query()->whereKey($categoryId)->where('is_active', true)->firstOrFail();
        $batchId = 'IMP-'.now()->format('Ymd-His').'-'.strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        $path = $file->getRealPath();
        $spreadsheet = IOFactory::load($path);
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $headerRow = array_shift($rows);
        $map = $this->mapHeaders($headerRow ?? []);

        if (! isset($map['name'])) {
            throw new \InvalidArgumentException('الملف يجب أن يحتوي على عمود «الاسم» أو name.');
        }

        $created = 0;
        $skipped = 0;
        $errors = [];
        $perRep = array_fill_keys($assigneeIds, 0);
        $repIndex = 0;
        $repCount = count($assigneeIds);
        $roundRobin = $repCount > 0;

        foreach ($rows as $lineNum => $row) {
            $line = (int) $lineNum + 2;
            $name = trim((string) ($row[$map['name']] ?? ''));
            if ($name === '') {
                continue;
            }

            $assignedTo = null;
            if ($roundRobin) {
                $assignedTo = $assigneeIds[$repIndex % $repCount];
                $repIndex++;
            }

            $phone = isset($map['phone']) ? trim((string) ($row[$map['phone']] ?? '')) : null;
            $email = isset($map['email']) ? trim((string) ($row[$map['email']] ?? '')) : null;

            if ($phone && $this->isDuplicatePhone($phone, $assignedTo)) {
                $skipped++;
                $errors[] = "سطر {$line}: تخطي — هاتف مكرر ({$phone})";

                continue;
            }

            try {
                $interestTypeId = $this->resolveInterestTypeId(
                    isset($map['interest_type']) ? trim((string) ($row[$map['interest_type']] ?? '')) : null
                );

                SalesLead::create(app(SalesLeadMovementPolicy::class)->withCreateDefaults([
                    'assigned_to' => $assignedTo,
                    'created_by' => $createdBy,
                    'category_id' => $category->id,
                    'sales_lead_group_id' => $group?->id,
                    'import_batch' => $batchId,
                    'name' => $name,
                    'phone' => $phone ?: null,
                    'email' => $email ?: null,
                    'company' => isset($map['company']) ? trim((string) ($row[$map['company']] ?? '')) ?: null : null,
                    'interest_type_id' => $interestTypeId,
                    'interest' => isset($map['interest']) ? trim((string) ($row[$map['interest']] ?? '')) ?: null : null,
                    'expected_value' => $this->parseNumber($row[$map['expected_value'] ?? ''] ?? null),
                    'notes' => isset($map['notes']) ? trim((string) ($row[$map['notes']] ?? '')) ?: null : null,
                    'source' => $source,
                    'stage' => 'new_lead',
                    'priority' => $this->parsePriority($row[$map['priority'] ?? ''] ?? null, $defaultPriority),
                    'next_follow_up_at' => now()->addDay()->setTime(10, 0),
                    'follow_up_channel' => 'call',
                ]));
                $created++;
                if ($assignedTo !== null) {
                    $perRep[$assignedTo] = ($perRep[$assignedTo] ?? 0) + 1;
                }
            } catch (\Throwable $e) {
                $skipped++;
                $errors[] = "سطر {$line}: ".$e->getMessage();
            }
        }

        if ($created > 0) {
            $notificationService = app(SalesNotificationService::class);
            foreach ($reps as $rep) {
                $count = (int) ($perRep[$rep->id] ?? 0);
                if ($count > 0) {
                    $notificationService->notifyBulkImport($rep, $count, $batchId, $category);
                }
            }

            $assigneeLabel = $reps->isNotEmpty()
                ? ' — موظفون: '.implode(', ', $reps->pluck('name')->all())
                : ' — بدون إسناد موظف';

            SalesAuditService::log(
                'sales_leads_bulk_import',
                null,
                null,
                [
                    'batch' => $batchId,
                    'count' => $created,
                    'category_id' => $category->id,
                    'group_id' => $group?->id,
                    'assignees' => $assigneeIds,
                    'per_rep' => $perRep,
                ],
                "استيراد {$created} عميل — تصنيف: {$category->name}"
                .($group ? " — مجموعة: {$group->name}" : '')
                .$assigneeLabel
            );
        }

        return [
            'created' => $created,
            'skipped' => $skipped,
            'errors' => $errors,
            'batch_id' => $batchId,
            'per_rep' => $perRep,
        ];
    }

    private function isDuplicatePhone(string $phone, ?int $assignedTo): bool
    {
        $query = SalesLead::query()->where('phone', $phone);

        if ($assignedTo !== null) {
            $query->where('assigned_to', $assignedTo);
        } else {
            $query->whereNull('assigned_to');
        }

        return $query->exists();
    }

    /**
     * @param  array<string, mixed>  $headerRow
     * @return array<string, string>  key => column letter
     */
    private function mapHeaders(array $headerRow): array
    {
        $aliases = [
            'name' => ['الاسم', 'name', 'اسم', 'العميل'],
            'phone' => ['الهاتف', 'phone', 'تليفون', 'موبايل'],
            'email' => ['البريد', 'email', 'ايميل'],
            'company' => ['الشركة', 'company'],
            'interest_type' => ['نوع الاهتمام', 'interest_type', 'اهتمام', 'الاهتمام', 'interest type', 'slug الاهتمام'],
            'interest' => ['تفاصيل الاهتمام', 'interest_details', 'منتج', 'الاهتمام التفصيلي'],
            'expected_value' => ['القيمة', 'expected_value', 'value', 'قيمة متوقعة'],
            'notes' => ['ملاحظات', 'notes'],
            'priority' => ['الأولوية', 'priority'],
        ];

        $map = [];
        foreach ($headerRow as $col => $label) {
            $label = mb_strtolower(trim((string) $label));
            foreach ($aliases as $key => $options) {
                foreach ($options as $opt) {
                    if ($label === mb_strtolower($opt)) {
                        $map[$key] = $col;
                    }
                }
            }
        }

        return $map;
    }

    private function parseNumber(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return round((float) preg_replace('/[^\d.]/', '', (string) $value), 2);
    }

    private function parsePriority(mixed $value, string $default): string
    {
        $v = mb_strtolower(trim((string) $value));
        $map = [
            'low' => 'low', 'منخفض' => 'low',
            'normal' => 'normal', 'عادي' => 'normal',
            'high' => 'high', 'مرتفع' => 'high',
            'urgent' => 'urgent', 'عاجل' => 'urgent',
        ];

        return $map[$v] ?? $default;
    }

    private function resolveInterestTypeId(?string $raw): ?int
    {
        $raw = trim((string) $raw);
        if ($raw === '') {
            return null;
        }

        $slug = mb_strtolower($raw);
        $bySlug = SalesInterestType::query()->where('slug', $slug)->value('id');
        if ($bySlug) {
            return (int) $bySlug;
        }

        $byName = SalesInterestType::query()
            ->where('name_ar', $raw)
            ->orWhere('name_en', $raw)
            ->value('id');

        return $byName ? (int) $byName : null;
    }
}
