<?php

namespace App\Domains\Community\Services;

use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

/**
 * قراءة ملفات مجموعات البيانات (Excel / CSV) لعرض معاينة في الصفحة.
 * يدعم القرص المحلي و Cloudflare R2 (عن طريق ملف مؤقت).
 */
class DatasetFileReaderService
{
    /** أقصى عدد صفوف للمعاينة في الواجهة */
    public const PREVIEW_MAX_ROWS = 500;

    /**
     * قراءة معاينة من أي قرص (local أو r2).
     *
     * @return array{headers: array<int, string>, rows: array<int, array<int, mixed>>}
     */
    public function readPreviewFromStorage(string $disk, string $path): array
    {
        if (!Storage::disk($disk)->exists($path)) {
            return ['headers' => [], 'rows' => []];
        }

        if ($disk === 'local') {
            $fullPath = Storage::disk($disk)->path($path);
            return $this->readPreview($fullPath);
        }

        $content = Storage::disk($disk)->get($path);
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION)) ?: 'csv';
        $tmp = tempnam(sys_get_temp_dir(), 'dataset_') . '.' . $ext;
        file_put_contents($tmp, $content);
        try {
            return $this->readPreview($tmp);
        } finally {
            @unlink($tmp);
        }
    }

    /**
     * قراءة الملف من مسار محلي وإرجاع مصفوفة صفوف (أول صف = عناوين إن وُجدت).
     *
     * @return array{headers: array<int, string>, rows: array<int, array<int, mixed>>}
     */
    public function readPreview(string $fullPath): array
    {
        if (!file_exists($fullPath) || !is_readable($fullPath)) {
            return ['headers' => [], 'rows' => []];
        }

        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        try {
            if ($extension === 'csv') {
                return $this->readCsv($fullPath);
            }
            if (in_array($extension, ['xlsx', 'xls'], true)) {
                return $this->readSpreadsheet($fullPath);
            }
            if ($extension === 'json') {
                return $this->readJsonPreview($fullPath);
            }
            if ($extension === 'txt') {
                return $this->readTxtPreview($fullPath);
            }
        } catch (\Throwable $e) {
            report($e);
            return ['headers' => [], 'rows' => []];
        }

        return ['headers' => [], 'rows' => []];
    }

    /**
     * معاينة JSON: إذا كان مصفوفة نعرض أول عناصر كصفوف؛ إذا كان كائناً نعرض المفاتيح كصف.
     */
    private function readJsonPreview(string $path): array
    {
        $raw = file_get_contents($path);
        if ($raw === false) {
            return ['headers' => [], 'rows' => []];
        }
        $data = json_decode($raw, true);
        if ($data === null) {
            return ['headers' => [], 'rows' => []];
        }
        if (array_is_list($data)) {
            $rows = array_slice($data, 0, self::PREVIEW_MAX_ROWS);
            $headers = [];
            foreach ($rows as $row) {
                if (is_array($row)) {
                    $headers = array_unique(array_merge($headers, array_keys($row)));
                }
            }
            $headers = array_values($headers);
            if (empty($headers) && !empty($rows)) {
                $first = $rows[0];
                $headers = is_array($first) ? array_map(fn ($i) => 'العمود ' . ($i + 1), range(0, count($first) - 1)) : ['القيمة'];
            }
            $out = [];
            foreach ($rows as $row) {
                if (!is_array($row)) {
                    $out[] = [$row];
                    continue;
                }
                $r = [];
                foreach ($headers as $h) {
                    $r[] = $row[$h] ?? '';
                }
                $out[] = $r;
            }
            return $this->normalizeRows([$headers] + $out);
        }
        $headers = array_keys($data);
        $row = array_map(fn ($v) => is_scalar($v) ? $v : json_encode($v), array_values($data));
        return ['headers' => $headers, 'rows' => [$row]];
    }

    /**
     * معاينة TXT: أسطر كصفوف، أعمدة بمحدد تبويب أو فاصلة.
     */
    private function readTxtPreview(string $path): array
    {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return ['headers' => [], 'rows' => []];
        }
        $lines = array_slice($lines, 0, self::PREVIEW_MAX_ROWS + 1);
        $rows = [];
        foreach ($lines as $line) {
            if (strpos($line, "\t") !== false) {
                $rows[] = array_map('trim', explode("\t", $line));
            } else {
                $rows[] = array_map('trim', str_getcsv($line));
            }
        }
        return $this->normalizeRows($rows);
    }

    /**
     * @return array{headers: array<int, string>, rows: array<int, array<int, mixed>>}
     */
    private function readCsv(string $path): array
    {
        $reader = new Csv();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return $this->normalizeRows($rows);
    }

    /**
     * @return array{headers: array<int, string>, rows: array<int, array<int, mixed>>}
     */
    private function readSpreadsheet(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return $this->normalizeRows($rows);
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array{headers: array<int, string>, rows: array<int, array<int, mixed>>}
     */
    private function normalizeRows(array $rows): array
    {
        $rows = array_values($rows);
        $rows = array_slice($rows, 0, self::PREVIEW_MAX_ROWS + 1);

        $headers = [];
        $dataRows = [];

        foreach ($rows as $index => $row) {
            $row = is_array($row) ? array_values($row) : [];
            $row = array_map(function ($cell) {
                if (is_null($cell) || $cell === '') {
                    return '';
                }
                return trim((string) $cell);
            }, $row);

            if ($index === 0) {
                $colCount = count($row);
                $headers = $row;
                foreach ($headers as $i => $h) {
                    if ($h === '') {
                        $headers[$i] = 'العمود ' . ((int) $i + 1);
                    }
                }
                if (empty(array_filter($headers))) {
                    $headers = array_map(fn ($i) => 'العمود ' . ($i + 1), range(0, $colCount - 1));
                }
                continue;
            }

            $dataRows[] = $row;
        }

        if (empty($headers) && !empty($dataRows)) {
            $first = $dataRows[0];
            $headers = array_map(fn ($i) => 'العمود ' . ($i + 1), range(0, count($first) - 1));
        }

        return ['headers' => $headers, 'rows' => $dataRows];
    }

    /**
     * إرجاع قائمة ملفات داخل أرشيف ZIP من التخزين.
     *
     * @return array<int, array{name: string, size: int}>
     */
    public function listZipEntriesFromStorage(string $disk, string $path): array
    {
        if (!Storage::disk($disk)->exists($path) || strtolower(pathinfo($path, PATHINFO_EXTENSION)) !== 'zip') {
            return [];
        }
        $content = Storage::disk($disk)->get($path);
        $tmp = tempnam(sys_get_temp_dir(), 'zip_') . '.zip';
        file_put_contents($tmp, $content);
        $entries = [];
        try {
            $zip = new \ZipArchive;
            if ($zip->open($tmp, \ZipArchive::RDONLY) !== true) {
                return [];
            }
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                if ($stat && !str_ends_with($stat['name'], '/')) {
                    $entries[] = ['name' => $stat['name'], 'size' => (int) $stat['size']];
                }
            }
            $zip->close();
        } finally {
            @unlink($tmp);
        }
        return $entries;
    }

    /**
     * قراءة معاينة لملف داخل أرشيف ZIP (مثل CSV أو Excel داخل الـ ZIP).
     *
     * @return array{headers: array<int, string>, rows: array<int, array<int, mixed>>}
     */
    public function readPreviewFromZipEntry(string $disk, string $zipPath, string $entryName): array
    {
        if (!Storage::disk($disk)->exists($zipPath) || strtolower(pathinfo($zipPath, PATHINFO_EXTENSION)) !== 'zip') {
            return ['headers' => [], 'rows' => []];
        }
        $content = Storage::disk($disk)->get($zipPath);
        $tmpZip = tempnam(sys_get_temp_dir(), 'zip_') . '.zip';
        file_put_contents($tmpZip, $content);
        try {
            $zip = new \ZipArchive();
            if ($zip->open($tmpZip, \ZipArchive::RDONLY) !== true) {
                return ['headers' => [], 'rows' => []];
            }
            $entryContent = $zip->getFromName($entryName);
            $zip->close();
            if ($entryContent === false) {
                return ['headers' => [], 'rows' => []];
            }
            $ext = strtolower(pathinfo($entryName, PATHINFO_EXTENSION)) ?: 'csv';
            $tmpFile = tempnam(sys_get_temp_dir(), 'zipentry_') . '.' . $ext;
            file_put_contents($tmpFile, $entryContent);
            try {
                return $this->readPreview($tmpFile);
            } finally {
                @unlink($tmpFile);
            }
        } finally {
            @unlink($tmpZip);
        }
    }
}
