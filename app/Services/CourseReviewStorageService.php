<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CourseReviewStorageService
{
    /**
     * رفع صورة ريفيو تسويقي: يُفضّل Cloudflare R2، مع احتياطي public محلياً.
     *
     * @return array{path: string, disk: string}
     */
    public function storeImage(UploadedFile $file): array
    {
        $preferred = (string) config('filesystems.course_reviews_disk', 'r2');
        if (! in_array($preferred, ['r2', 's3', 'public'], true)) {
            $preferred = 'r2';
        }

        $folder = 'course-reviews/'.now()->format('Y/m');
        $disks = array_values(array_unique([$preferred, 'public']));

        $lastError = null;
        foreach ($disks as $disk) {
            if (! $this->diskIsLikelyConfigured($disk)) {
                continue;
            }

            try {
                if ($disk === 'public') {
                    Storage::disk('public')->makeDirectory('course-reviews');
                }

                $path = $file->store($folder, $disk);
                if (is_string($path) && $path !== '') {
                    return ['path' => $path, 'disk' => $disk];
                }
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
                Log::warning('course_review_store_failed', [
                    'disk' => $disk,
                    'message' => $lastError,
                ]);
            }
        }

        throw new \RuntimeException(
            'تعذر رفع الصورة على Cloudflare R2 أو التخزين المحلي'.($lastError ? ': '.$lastError : '.')
        );
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
            Log::warning('course_review_delete_failed', [
                'disk' => $disk,
                'path' => $path,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function url(?string $path, ?string $disk): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        $path = ltrim(str_replace('\\', '/', $path), '/');
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $disk = $disk ?: 'public';
        $url = storage_inline_media_url($disk, $path, now()->addDays(7));
        if ($url !== '') {
            return $url;
        }

        if ($disk !== 'public') {
            $fallback = storage_inline_media_url('public', $path);
            if ($fallback !== '') {
                return $fallback;
            }
        }

        return asset('storage/'.$path);
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
