<?php

namespace App\Support;

final class BunnyStreamSigner
{
    /**
     * Bunny Stream "Embed View Token Authentication".
     *
     * Token = sha256_hex(securityKey + videoId + expiresUnixSeconds)
     * URL   = https://iframe.mediadelivery.net/embed/{libraryId}/{videoId}?token=...&expires=...
     */
    public static function signEmbedUrl(string $embedUrl, string $securityKey, int $expiresUnixSeconds): string
    {
        $base = preg_replace('/\?.*$/', '', trim($embedUrl));
        if ($base === '') {
            return $embedUrl;
        }

        // Normalize scheme
        if (!preg_match('#^https?://#i', $base)) {
            $base = 'https://' . ltrim($base, '/');
        }

        if (!preg_match('#/embed/\d+/([a-zA-Z0-9_-]+)$#', $base, $m)) {
            // Not the expected embed URL format; return as-is.
            return $embedUrl;
        }

        $videoId = $m[1];
        $token = hash('sha256', $securityKey . $videoId . $expiresUnixSeconds);

        return $base . '?token=' . $token . '&expires=' . $expiresUnixSeconds;
    }
}

