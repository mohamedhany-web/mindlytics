<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('offline_course_resources_disk')) {
    /**
     * قرص تخزين ملفات موارد الكورس الأوفلاين/الأونلاين (رفع المدرب).
     *
     * @return string 'r2' أو 'public'
     */
    function offline_course_resources_disk(): string
    {
        $envDisk = env('FILESYSTEM_DISK_OFFLINE_RESOURCES');
        if ($envDisk !== null && $envDisk !== '' && in_array($envDisk, ['r2', 'public'], true)) {
            return $envDisk;
        }

        return config('filesystems.offline_course_resources_disk', 'r2');
    }
}

if (!function_exists('stored_upload_file_url')) {
    /**
     * رابط تحميل/عرض ملف مرفوع (موارد، مرفقات نشاط، تسليمات) — public محلي أو R2/S3.
     *
     * @param  array{path?: string, name?: string, disk?: string, url?: string}|null  $file
     */
    function stored_upload_file_url(?array $file): string
    {
        if (empty($file) || empty($file['path'])) {
            return '#';
        }
        if (! empty($file['url'])) {
            return $file['url'];
        }
        $path = $file['path'];
        $disk = $file['disk'] ?? 'public';
        if ($disk === 'public') {
            return asset('storage/'.ltrim($path, '/'));
        }
        try {
            $driver = Storage::disk($disk);

            // R2/S3 عادةً يكون Private، فـ url() يعطي رابط مباشر بدون توقيع → Authorization error.
            // الأفضل استخدام رابط مؤقت موقّع إن كان متاحاً.
            if (method_exists($driver, 'temporaryUrl')) {
                $name = $file['name'] ?? basename((string) $path);
                $disposition = 'attachment; filename="' . str_replace('"', '', (string) $name) . '"';

                return $driver->temporaryUrl(
                    $path,
                    now()->addMinutes(15),
                    ['ResponseContentDisposition' => $disposition]
                );
            }

            return $driver->url($path);
        } catch (\Throwable) {
            return asset('storage/'.ltrim($path, '/'));
        }
    }
}

if (!function_exists('offline_course_resource_file_url')) {
    /**
     * @param  array{path?: string, name?: string, disk?: string, url?: string}|null  $file
     */
    function offline_course_resource_file_url(?array $file): string
    {
        return stored_upload_file_url($file);
    }
}

if (!function_exists('offline_activity_submission_file_url')) {
    /**
     * رابط تحميل ملف ضمن تسليم نشاط أوفلاين (نفس منطق الموارد).
     *
     * @param  array{path?: string, name?: string, disk?: string, url?: string}|null  $file
     */
    function offline_activity_submission_file_url(?array $file): string
    {
        return stored_upload_file_url($file);
    }
}

if (!function_exists('offline_activity_submissions_disk')) {
    /**
     * قرص تخزين تسليمات الطلاب لأنشطة الكورس الأوفلاين.
     *
     * @return string 'r2' أو 'public'
     */
    function offline_activity_submissions_disk(): string
    {
        $envDisk = env('FILESYSTEM_DISK_OFFLINE_ACTIVITY_SUBMISSIONS');
        if ($envDisk !== null && $envDisk !== '' && in_array($envDisk, ['r2', 'public'], true)) {
            return $envDisk;
        }

        return config('filesystems.offline_activity_submissions_disk', 'r2');
    }
}

if (!function_exists('student_mobile_disk')) {
    /**
     * قرص رفع ملفات تطبيق الطالب (بروفايل، صور منشورات المجتمع) — يُفضّل R2.
     *
     * @return string اسم القرص في config/filesystems.php (مثل r2 أو public)
     */
    function student_mobile_disk(): string
    {
        $envDisk = env('FILESYSTEM_DISK_STUDENT_MOBILE');
        if ($envDisk !== null && $envDisk !== '') {
            return $envDisk;
        }

        return config('filesystems.student_mobile_disk', 'r2');
    }
}

if (!function_exists('storage_inline_media_url')) {
    /**
     * رابط عرض وسائط إنلاين (صورة، فيديو): مسار public/local عبر url()، أو رابط موقّع مؤقتًا لـ R2/S3 الخاص.
     */
    function storage_inline_media_url(string $disk, string $path, ?\DateTimeInterface $expires = null): string
    {
        $path = ltrim(str_replace('\\', '/', $path), '/');
        if ($path === '') {
            return '';
        }

        try {
            $driver = Storage::disk($disk);

            if (in_array($disk, ['public', 'local'], true)) {
                return $driver->url($path);
            }

            $expires = $expires ?? now()->addDays(7);

            if (method_exists($driver, 'temporaryUrl')) {
                return $driver->temporaryUrl($path, $expires);
            }

            return $driver->url($path);
        } catch (\Throwable) {
            return '';
        }
    }
}

if (!function_exists('community_disk')) {
    /**
     * قرص تخزين ملفات المجتمع (تقديمات المساهمين).
     * يُفضّل القراءة من .env إن وُجدت لتجنب مشكلة كاش الإعدادات.
     *
     * @return string 'r2' أو 'local'
     */
    function community_disk(): string
    {
        $envDisk = env('FILESYSTEM_DISK_COMMUNITY');
        if ($envDisk !== null && $envDisk !== '' && in_array($envDisk, ['r2', 'local'], true)) {
            return $envDisk;
        }
        return config('filesystems.community_disk', 'local');
    }
}
