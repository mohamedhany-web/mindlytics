<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Providers\AppServiceProvider;

/**
 * نسخ صورة خلفية صفحات تسجيل الدخول/إنشاء الحساب إلى التخزين (storage/app/public/auth-pages)
 * بنفس أسلوب حفظ صور المسارات التعليمية لتعمل على السيرفر عبر route /storage/
 */
class CopyAuthPagesImage extends Command
{
    protected $signature = 'auth:copy-login-image
                            {--source= : مسار الملف في public (مثال: images/brainstorm-meeting.jpg)}';

    protected $description = 'نسخ صورة خلفية صفحة تسجيل الدخول من public إلى storage (نفس أسلوب المسارات التعليمية)';

    public function handle(): int
    {
        $source = $this->option('source') ?? 'images/brainstorm-meeting.jpg';
        $publicPath = public_path($source);

        if (!File::isFile($publicPath)) {
            $this->warn("الملف غير موجود: {$publicPath}");
            $this->info('ضع صورة الخلفية في public/images/brainstorm-meeting.jpg ثم شغّل الأمر مرة أخرى.');
            return self::FAILURE;
        }

        $storagePath = AppServiceProvider::AUTH_BACKGROUND_STORAGE_PATH;
        $disk = Storage::disk('public');

        $dir = dirname($storagePath);
        if (!$disk->exists($dir)) {
            $disk->makeDirectory($dir);
        }

        $content = File::get($publicPath);
        $disk->put($storagePath, $content);

        $fullPath = $disk->path($storagePath);
        $this->info("تم النسخ بنجاح:");
        $this->line("  من: {$publicPath}");
        $this->line("  إلى: {$fullPath}");
        $this->line("  الرابط: " . asset('storage/' . $storagePath));
        $this->newLine();
        $this->info('صفحات تسجيل الدخول وإنشاء الحساب ستستخدم الصورة من التخزين (تعمل على السيرفر مثل صور المسارات).');
        $size = File::size($publicPath);
        if ($size > 500 * 1024) {
            $this->warn('للتحميل الأسرع: يُفضّل أن تكون الصورة أقل من 500 كيلوبايت (الحالي: ' . round($size / 1024) . ' ك.ب). يمكنك ضغطها بأداة مثل TinyPNG أو تصغير أبعادها إلى 1920px عرض كحد أقصى.');
        }

        return self::SUCCESS;
    }
}
