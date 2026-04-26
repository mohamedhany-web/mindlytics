<?php

namespace App\Support;

use HTMLPurifier;
use HTMLPurifier_Config;

/**
 * Sanitize rich HTML for blog posts (stored XSS mitigation).
 */
final class BlogHtmlSanitizer
{
    private static ?HTMLPurifier $purifier = null;

    public static function purify(string $html): string
    {
        if (self::$purifier === null) {
            $cacheDir = storage_path('framework/cache/htmlpurifier');
            if (! is_dir($cacheDir)) {
                mkdir($cacheDir, 0755, true);
            }

            $config = HTMLPurifier_Config::createDefault();
            $config->set('Cache.SerializerPath', $cacheDir);
            $config->set('HTML.SafeIframe', true);
            $config->set(
                'URI.SafeIframeRegexp',
                '%^(https?:)?//(www\.youtube\.com/embed/|www\.youtube-nocookie\.com/embed/|player\.vimeo\.com/video/)%'
            );

            self::$purifier = new HTMLPurifier($config);
        }

        return self::$purifier->purify($html);
    }
}
