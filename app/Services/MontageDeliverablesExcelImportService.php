<?php

namespace App\Services;

use App\Models\EmployeeTask;
use App\Models\EmployeeTaskDeliverable;
use App\Support\MontageVideoHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MontageDeliverablesExcelImportService
{
    /**
     * @return array{imported:int, skipped_duplicates:array<int, string>, row_errors:array<int, string>, message:string}
     */
    public function import(UploadedFile $file, EmployeeTask $task): array
    {
        if (! $task->isVideoEditing()) {
            return [
                'imported' => 0,
                'skipped_duplicates' => [],
                'row_errors' => [],
                'message' => 'هذه المهمة ليست من نوع مونتاج فيديو.',
            ];
        }

        $path = $file->getRealPath();
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        if (count($rows) < 2) {
            return [
                'imported' => 0,
                'skipped_duplicates' => [],
                'row_errors' => [],
                'message' => 'الملف فارغ أو لا يحتوي صف بيانات بعد العناوين.',
            ];
        }

        $headerRow = array_shift($rows);
        $colMap = $this->mapHeaders($headerRow);

        if (! isset($colMap['link'])) {
            return [
                'imported' => 0,
                'skipped_duplicates' => [],
                'row_errors' => [],
                'message' => 'لم يُعثر على عمود رابط الفيديو. استخدم أحد العناوين: رابط_الفيديو، رابط، video_link، link',
            ];
        }

        $imported = 0;
        $skippedDuplicates = [];
        $rowErrors = [];
        $seenHashes = [];

        $existingHashes = EmployeeTaskDeliverable::query()
            ->where('task_id', $task->id)
            ->whereNotNull('link_url_hash')
            ->pluck('link_url_hash')
            ->flip()
            ->all();

        DB::beginTransaction();
        try {
            $rowNum = 1;
            foreach ($rows as $row) {
                $rowNum++;
                if ($this->rowIsEmpty($row)) {
                    continue;
                }

                $link = $this->cell($row, $colMap['link'] ?? null);
                if ($link === null || trim((string) $link) === '') {
                    $rowErrors[$rowNum] = 'رابط الفيديو فارغ';

                    continue;
                }

                $link = trim((string) $link);
                if (! $this->isAllowedBunnyHost($link)) {
                    $rowErrors[$rowNum] = 'الرابط يجب أن يكون من Bunny فقط';

                    continue;
                }

                $hash = MontageVideoHelper::linkUrlHash($link);
                if ($hash === null) {
                    $rowErrors[$rowNum] = 'رابط غير صالح';

                    continue;
                }

                if (isset($seenHashes[$hash]) || isset($existingHashes[$hash])) {
                    $skippedDuplicates[] = 'صف ' . $rowNum . ': ' . mb_substr($link, 0, 80) . '…';

                    continue;
                }

                $title = $this->optionalCell($row, $colMap['title'] ?? null);
                $receivedFrom = $this->optionalCell($row, $colMap['received_from'] ?? null) ?? '';
                $description = $this->optionalCell($row, $colMap['description'] ?? null);

                $minBefore = $this->readMinutes($row, $colMap, 'minutes_before', 'duration_before_text');
                $minAfter = $this->readMinutes($row, $colMap, 'minutes_after', 'duration_after_text');

                $textBefore = $minBefore !== null ? MontageVideoHelper::minutesToDisplay($minBefore) : $this->optionalCell($row, $colMap['duration_before_text'] ?? null);
                $textAfter = $minAfter !== null ? MontageVideoHelper::minutesToDisplay($minAfter) : $this->optionalCell($row, $colMap['duration_after_text'] ?? null);

                EmployeeTaskDeliverable::create([
                    'task_id' => $task->id,
                    'title' => $title ?: ('استيراد ' . now()->format('Y-m-d H:i') . ' — صف ' . $rowNum),
                    'description' => $description,
                    'delivery_type' => 'link',
                    'link_url' => $link,
                    'received_from' => $receivedFrom !== '' ? $receivedFrom : '—',
                    'duration_before' => $textBefore,
                    'duration_after' => $textAfter,
                    'duration_before_minutes' => $minBefore,
                    'duration_after_minutes' => $minAfter,
                    'status' => 'submitted',
                    'submitted_at' => now(),
                ]);

                $seenHashes[$hash] = true;
                $existingHashes[$hash] = true;
                $imported++;
            }

            if ($imported > 0 && $task->status !== 'completed') {
                $task->update(['status' => 'in_progress']);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        $message = 'تم استيراد ' . $imported . ' تسليم بنجاح.';
        if (count($skippedDuplicates) > 0) {
            $message .= ' تم تخطي ' . count($skippedDuplicates) . ' رابطاً مكرراً.';
        }
        if (count($rowErrors) > 0) {
            $message .= ' صفوف بها أخطاء: ' . count($rowErrors) . '.';
        }

        return [
            'imported' => $imported,
            'skipped_duplicates' => $skippedDuplicates,
            'row_errors' => $rowErrors,
            'message' => $message,
        ];
    }

    private function mapHeaders(array $headerRow): array
    {
        $map = [];
        foreach ($headerRow as $colIndex => $label) {
            if ($label === null || trim((string) $label) === '') {
                continue;
            }
            $key = $this->normalizeHeaderKey((string) $label);
            $canonical = $this->canonicalColumn($key);
            if ($canonical !== null) {
                $map[$canonical] = (int) $colIndex;
            }
        }

        return $map;
    }

    private function normalizeHeaderKey(string $label): string
    {
        $label = mb_strtolower(trim($label), 'UTF-8');
        $label = str_replace([' ', "\t", '-', '،'], '_', $label);
        $label = preg_replace('/_+/', '_', $label) ?? $label;

        return $label;
    }

    private function canonicalColumn(string $key): ?string
    {
        $aliases = [
            'link' => ['رابط_الفيديو', 'رابط', 'video_link', 'link_url', 'link', 'url', 'bunny', 'bunny_link'],
            'title' => ['عنوان', 'title', 'name'],
            'received_from' => ['ممن_استلم', 'ممن_استلمته', 'received_from', 'source', 'المصدر'],
            'minutes_before' => ['دقائق_قبل', 'قبل_بالدقائق', 'minutes_before', 'min_before', 'before_minutes'],
            'minutes_after' => ['دقائق_بعد', 'بعد_بالدقائق', 'minutes_after', 'min_after', 'after_minutes'],
            'duration_before_text' => ['مدة_قبل', 'قبل', 'duration_before', 'before'],
            'duration_after_text' => ['مدة_بعد', 'بعد', 'duration_after', 'after'],
            'description' => ['ملاحظات', 'وصف', 'description', 'notes'],
        ];

        foreach ($aliases as $canonical => $list) {
            foreach ($list as $alias) {
                if ($key === $this->normalizeHeaderKey($alias)) {
                    return $canonical;
                }
            }
        }

        return null;
    }

    private function cell(array $row, int|string|null $colIndex): ?string
    {
        if ($colIndex === null) {
            return null;
        }
        $v = $row[$colIndex] ?? null;

        return $v === null ? null : (is_scalar($v) ? trim((string) $v) : null);
    }

    private function optionalCell(array $row, int|string|null $colIndex): ?string
    {
        $v = $this->cell($row, $colIndex);

        return ($v !== null && trim($v) !== '') ? trim($v) : null;
    }

    private function readMinutes(array $row, array $colMap, string $minKey, string $textFallbackKey): ?int
    {
        $direct = $this->optionalCell($row, $colMap[$minKey] ?? null);
        if ($direct !== null) {
            if (! str_contains($direct, ':') && is_numeric(str_replace(',', '.', $direct))) {
                return min(max(0, (int) round((float) str_replace(',', '.', $direct))), 999999);
            }
            $parsed = MontageVideoHelper::parseDurationToMinutes($direct);
            if ($parsed !== null) {
                return $parsed;
            }
        }

        $text = $this->optionalCell($row, $colMap[$textFallbackKey] ?? null);
        if ($text !== null) {
            return MontageVideoHelper::parseDurationToMinutes($text);
        }

        return null;
    }

    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $v) {
            if ($v !== null && trim((string) $v) !== '') {
                return false;
            }
        }

        return true;
    }

    private function isAllowedBunnyHost(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);
        $hostLower = $host ? strtolower($host) : '';

        return $host && (
            str_contains($hostLower, 'bunny')
            || str_contains($hostLower, 'b-cdn')
            || str_contains($hostLower, 'mediadelivery')
        );
    }
}
