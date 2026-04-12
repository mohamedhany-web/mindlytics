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
                return asset('storage/'.$path);
            }
        }

        return asset('logo-removebg-preview.png');
    }

    public static function faviconUrl(): string
    {
        $disk = Storage::disk('public');
        foreach (self::FAVICON_EXTENSIONS as $ext) {
            $path = "site/favicon.{$ext}";
            if ($disk->exists($path)) {
                return asset('storage/'.$path);
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
}
