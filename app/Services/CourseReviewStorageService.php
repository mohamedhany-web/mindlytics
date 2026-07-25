<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
                $path = $this->putOnDisk($file, $folder, $disk);
                if (is_string($path) && $path !== '') {
                    return ['path' => $path, 'disk' => $disk];
                }
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
                Log::warning('course_review_store_failed', [
                    'disk' => $disk,
                    'message' => $lastError,
                    'exception' => $e::class,
                ]);
            }
        }

        throw new \RuntimeException(
            'تعذر رفع الصورة على Cloudflare R2 أو التخزين المحلي'.($lastError ? ': '.$lastError : '.')
        );
    }

    private function putOnDisk(UploadedFile $file, string $folder, string $disk): ?string
    {
        if ($disk === 'public') {
            Storage::disk('public')->makeDirectory($folder);
        }

        $ext = strtolower((string) ($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg'));
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            $ext = 'jpg';
        }
        $filename = Str::uuid()->toString().'.'.$ext;
        $fullPath = trim($folder, '/').'/'.$filename;

        // R2/S3: تجنّب ACL العام (غالباً يسبب فشل الرفع على Cloudflare)
        if (in_array($disk, ['r2', 's3'], true)) {
            $stream = fopen($file->getRealPath(), 'r');
            if ($stream === false) {
                throw new \RuntimeException('تعذر قراءة ملف الصورة المؤقت.');
            }

            try {
                $ok = Storage::disk($disk)->put($fullPath, $stream, [
                    'visibility' => 'private',
                    'ContentType' => $file->getMimeType() ?: 'image/jpeg',
                ]);
            } finally {
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }

            return $ok ? $fullPath : null;
        }

        $path = $file->storeAs($folder, $filename, $disk);

        return is_string($path) && $path !== '' ? $path : null;
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

        try {
            $url = storage_inline_media_url($disk, $path, now()->addDays(7));
            if ($url !== '') {
                return $url;
            }
        } catch (\Throwable $e) {
            Log::warning('course_review_url_failed', [
                'disk' => $disk,
                'path' => $path,
                'message' => $e->getMessage(),
            ]);
        }

        if ($disk !== 'public') {
            try {
                $fallback = storage_inline_media_url('public', $path);
                if ($fallback !== '') {
                    return $fallback;
                }
            } catch (\Throwable) {
                //
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

        // بدون الحزمة S3، تخطّى R2 حتى لا يحدث Fatal
        if (! class_exists(\League\Flysystem\AwsS3V3\AwsS3V3Adapter::class)
            && ! class_exists(\Aws\S3\S3Client::class)) {
            return false;
        }

        return ! empty($cfg['key'])
            && ! empty($cfg['secret'])
            && ! empty($cfg['bucket']);
    }
}
