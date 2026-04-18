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
            return Storage::disk($disk)->url($path);
        } catch (\Throwable $e) {
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
