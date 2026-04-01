<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EmployeeDeliverableStorageService
{
    /**
     * رفع ملف تسليم موظف: يُفضّل Cloudflare R2 (قرص s3/r2)، مع الاحتياطي إلى public محلياً.
     *
     * @return array{path: string, disk: string}
     */
    public function storeUploadedFile(UploadedFile $file, string $deliveryType): array
    {
        $preferred = config('filesystems.employee_deliverables_disk', 'r2');
        if (! in_array($preferred, ['r2', 's3', 'public'], true)) {
            $preferred = 'public';
        }
        $folder = $deliveryType === 'image' ? 'employee-deliverables/images' : 'employee-deliverables/files';
        $disks = array_unique([$preferred, 'public']);

        $lastError = null;
        foreach ($disks as $disk) {
            if (! $this->diskIsLikelyConfigured($disk)) {
                continue;
            }
            try {
                $path = $file->store($folder, $disk);
                if ($path) {
                    return ['path' => $path, 'disk' => $disk];
                }
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
                Log::warning('employee_deliverable_store_failed', [
                    'disk' => $disk,
                    'message' => $lastError,
                ]);
            }
        }

        throw new \RuntimeException('تعذر رفع الملف على التخزين السحابي أو المحلي.');
    }

    public function deleteIfExists(?string $path, ?string $disk): void
    {
        if ($path === null || $path === '') {
            return;
        }
        $disk = $disk ?: 'public';
        try {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        } catch (\Throwable $e) {
            Log::warning('employee_deliverable_delete_failed', [
                'disk' => $disk,
                'path' => $path,
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function diskIsLikelyConfigured(string $disk): bool
    {
        if (in_array($disk, ['public', 'local'], true)) {
            return true;
        }
        $cfg = config("filesystems.disks.$disk");
        if (! is_array($cfg)) {
            return false;
        }

        return ! empty($cfg['key'])
            && ! empty($cfg['secret'])
            && ! empty($cfg['bucket']);
    }
}
