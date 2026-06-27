<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class SiteBranding
{
    /** @var list<string> */
    private const LOGO_EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp', 'gif'];

    /** @var list<string> */
    private const FAVICON_EXTENSIONS = ['ico', 'png', 'svg', 'webp'];

    public static function logoUrl(): string
    {
        $disk = Storage::disk('public');
        foreach (self::LOGO_EXTENSIONS as $ext) {
            $path = "site/logo.{$ext}";
            if ($disk->exists($path)) {
                $url = asset('storage/'.$path);
                try {
                    $v = $disk->lastModified($path);
                    if ($v > 0) {
                        $url .= '?v='.$v;
                    }
                } catch (\Throwable) {
                }

                return $url;
            }
        }

        // Fallback: ملف ثابت داخل public حتى لا يتعطل الشعار في أي صفحة
        return asset('logo-fallback.svg');
    }

    public static function faviconUrl(): string
    {
        $disk = Storage::disk('public');
        foreach (self::FAVICON_EXTENSIONS as $ext) {
            $path = "site/favicon.{$ext}";
            if ($disk->exists($path)) {
                $url = asset('storage/'.$path);
                try {
                    $v = $disk->lastModified($path);
                    if ($v > 0) {
                        $url .= '?v='.$v;
                    }
                } catch (\Throwable) {
                }

                return $url;
            }
        }

        return asset('favicon.ico');
    }

    /**
     * @return list<string>
     */
    public static function logoExtensions(): array
    {
        return self::LOGO_EXTENSIONS;
    }

    /**
     * @return list<string>
     */
    public static function faviconExtensions(): array
    {
        return self::FAVICON_EXTENSIONS;
    }

    public static function logoLocalPath(): ?string
    {
        $disk = Storage::disk('public');
        foreach (self::LOGO_EXTENSIONS as $ext) {
            $path = "site/logo.{$ext}";
            if ($disk->exists($path)) {
                return $disk->path($path);
            }
        }

        foreach (['logo-removebg-preview.png', 'logo-fallback.svg'] as $file) {
            $full = public_path($file);
            if (is_readable($full)) {
                return $full;
            }
        }

        return null;
    }

    public static function logoDataUri(): ?string
    {
        $path = self::logoLocalPath();
        if (! $path) {
            return null;
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            default => 'application/octet-stream',
        };

        return 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($path));
    }
}
