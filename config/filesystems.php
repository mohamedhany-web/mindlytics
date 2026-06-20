<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => false, // Disabled so custom /storage route is used (fixes 404 on shared hosting)
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

        /*
         * Cloudflare R2 — متوافق مع واجهة S3.
         * يُستخدم لرفع ملفات مجتمع الذكاء الاصطناعي (تقديمات المساهمين).
         */
        'r2' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'auto'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', true),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | قرص ملفات المجتمع (مساهمون + أدمن)
    |--------------------------------------------------------------------------
    | استخدم 'r2' لرفع الملفات على Cloudflare R2، أو 'local' للتطوير المحلي.
    | بعد تغيير .env نفّذ: php artisan config:clear
    */
    'community_disk' => env('FILESYSTEM_DISK_COMMUNITY', 'local'),

    /*
    |--------------------------------------------------------------------------
    | قرص ملفات التوظيف (CV + مرفقات)
    |--------------------------------------------------------------------------
    | افتراضياً Cloudflare R2 (Private) مع AWS_* أعلاه. للتطوير المحلي يمكن ضبطه إلى public.
    */
    'hr_recruitment_disk' => env('FILESYSTEM_DISK_HR_RECRUITMENT', 'r2'),

    /*
    |--------------------------------------------------------------------------
    | قرص ملفات الواجبات
    |--------------------------------------------------------------------------
    | افتراضيًا نرفع تسليمات الواجبات على Cloudflare R2.
    | يمكن تغييره عبر .env عند الحاجة.
    */
    'assignments_disk' => env('FILESYSTEM_DISK_ASSIGNMENTS', 'r2'),

    /*
    |--------------------------------------------------------------------------
    | قرص تسليمات الموظفين (ملفات/صور)
    |--------------------------------------------------------------------------
    | افتراضياً Cloudflare R2 (نفس إعدادات r2 في .env). عند التعذر يُستخدم public محلياً.
    */
    'employee_deliverables_disk' => env('FILESYSTEM_DISK_EMPLOYEE_DELIVERABLES', 'r2'),

    /*
    |--------------------------------------------------------------------------
    | قرص موارد الكورس الأوفلاين / الأونلاين (ملفات المدرب)
    |--------------------------------------------------------------------------
    | يُفضّل r2 (Cloudflare) مع تعبئة AWS_* أعلاه. للتطوير المحلي: public
    */
    'offline_course_resources_disk' => env('FILESYSTEM_DISK_OFFLINE_RESOURCES', 'r2'),

    /*
    |--------------------------------------------------------------------------
    | قرص تسليمات أنشطة الكورس الأوفلاين (ملفات الطلاب)
    |--------------------------------------------------------------------------
    | يُفضّل r2 مع تعبئة AWS_* أعلاه. للتطوير المحلي: public
    */
    'offline_activity_submissions_disk' => env('FILESYSTEM_DISK_OFFLINE_ACTIVITY_SUBMISSIONS', 'r2'),

    /*
    |--------------------------------------------------------------------------
    | قرص رفع طلاب التطبيق (صورة البروفايل، صور منشورات المجتمع)
    |--------------------------------------------------------------------------
    | Cloudflare R2 (S3-compatible). للتطوير المحلي: public
    */
    'student_mobile_disk' => env('FILESYSTEM_DISK_STUDENT_MOBILE', 'r2'),

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
