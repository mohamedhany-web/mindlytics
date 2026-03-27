<?php

namespace App\Support;

/**
 * تطبيع روابط Bunny وحساب الدقائق من نصوص المدد.
 */
class MontageVideoHelper
{
    public static function normalizeVideoUrl(string $url): string
    {
        $url = trim($url);
        $parts = parse_url($url);
        if (! isset($parts['scheme'], $parts['host'])) {
            return $url;
        }
        $scheme = strtolower($parts['scheme']);
        $host = strtolower($parts['host']);
        $path = $parts['path'] ?? '';
        $path = $path !== '' ? rtrim($path, '/') : '';
        $query = isset($parts['query']) ? '?' . $parts['query'] : '';

        return $scheme . '://' . $host . $path . $query;
    }

    public static function linkUrlHash(?string $url): ?string
    {
        if ($url === null || trim($url) === '') {
            return null;
        }

        return hash('sha256', self::normalizeVideoUrl($url));
    }

    /**
     * تحويل مدة نصية إلى دقائق: 45 أو 10:30 أو 1:05:00
     */
    public static function parseDurationToMinutes(?string $value): ?int
    {
        if ($value === null) {
            return null;
        }
        $v = trim((string) $value);
        if ($v === '') {
            return null;
        }

        if (preg_match('/^\d+$/', $v)) {
            return min((int) $v, 999999);
        }

        if (preg_match('/^(\d{1,3}):(\d{2})$/', $v, $m)) {
            return min((int) $m[1] * 60 + (int) $m[2], 999999);
        }

        if (preg_match('/^(\d{1,2}):(\d{2}):(\d{2})$/', $v, $m)) {
            return min((int) $m[1] * 3600 + (int) $m[2] * 60 + (int) $m[3], 999999);
        }

        if (preg_match('/^(\d+)\s*(?:دقيقة|دقائق|min|minutes?)\s*$/iu', $v, $m)) {
            return min((int) $m[1], 999999);
        }

        return null;
    }

    public static function minutesToDisplay(?int $minutes): ?string
    {
        if ($minutes === null) {
            return null;
        }
        if ($minutes < 60) {
            return (string) $minutes;
        }
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;

        return sprintf('%d:%02d', $h, $m);
    }

    public static function syncTextFromMinutes(?int $beforeMin, ?int $afterMin, ?string $beforeText, ?string $afterText): array
    {
        return [
            'duration_before' => $beforeMin !== null ? self::minutesToDisplay($beforeMin) : $beforeText,
            'duration_after' => $afterMin !== null ? self::minutesToDisplay($afterMin) : $afterText,
        ];
    }
}
