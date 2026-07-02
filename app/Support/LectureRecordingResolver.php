<?php

namespace App\Support;

use App\Helpers\VideoHelper;
use App\Models\VideoProvider;
use Illuminate\Support\Facades\Cache;

/**
 * توحيد روابط تسجيل المحاضرات/الدروس مع تخزين مؤقت لتقليل توقيع Bunny المتكرر.
 */
final class LectureRecordingResolver
{
    /** مدة الكاش — أقل قليلاً من صلاحية توكن Bunny */
    private const CACHE_MINUTES = 55;

    /** صلاحية توكن Bunny بالدقائق */
    private const TOKEN_MINUTES = 60;

    /**
     * @return array{recording_url: string, video_platform: ?string}
     */
    public static function resolve(?string $recordingUrl, ?string $videoPlatform): array
    {
        $url = trim((string) $recordingUrl);
        if ($url === '') {
            return ['recording_url' => '', 'video_platform' => null];
        }

        $platform = strtolower(trim((string) $videoPlatform));
        if ($platform === '' || $platform === 'unknown') {
            $platform = VideoHelper::getVideoSource($url);
        }

        $cacheKey = 'lecture_recording_v2:'.md5($url.'|'.$platform);

        return Cache::remember($cacheKey, now()->addMinutes(self::CACHE_MINUTES), function () use ($url, $platform) {
            $embed = VideoHelper::getEmbedUrl($url) ?? $url;
            $resolvedPlatform = $platform;

            if ($resolvedPlatform === 'bunny' || VideoHelper::getVideoSource($url) === 'bunny') {
                $resolvedPlatform = 'bunny';
                $key = self::bunnySecurityKey();
                if ($key !== '') {
                    $embed = BunnyStreamSigner::signEmbedUrl(
                        $embed,
                        $key,
                        now()->addMinutes(self::TOKEN_MINUTES)->timestamp
                    );
                }
            }

            if ($resolvedPlatform === 'other') {
                $resolvedPlatform = VideoHelper::getVideoSource($embed);
            }

            return [
                'recording_url' => $embed,
                'video_platform' => $resolvedPlatform,
            ];
        });
    }

    public static function bunnySecurityKey(): string
    {
        return (string) Cache::remember('bunny_active_token_key_v1', now()->addHour(), function () {
            $provider = VideoProvider::where('platform', 'bunny')
                ->where('is_active', true)
                ->orderByDesc('id')
                ->first();

            return $provider?->token_auth_key ? trim((string) $provider->token_auth_key) : '';
        });
    }
}
