<?php

namespace App\Support;

use App\Helpers\VideoHelper;
use App\Models\VideoProvider;
use Illuminate\Support\Facades\Cache;

/**
 * توحيد روابط تسجيل المحاضرات مع تخزين مؤقت لتقليل توقيع Bunny المتكرر.
 */
final class LectureRecordingResolver
{
    private const CACHE_MINUTES = 18;

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

        $cacheKey = 'lecture_recording_v1:'.md5($url.'|'.$platform);

        return Cache::remember($cacheKey, now()->addMinutes(self::CACHE_MINUTES), function () use ($url, $platform) {
            $embed = VideoHelper::getEmbedUrl($url) ?? $url;
            $resolvedPlatform = $platform;

            if ($resolvedPlatform === 'bunny' || VideoHelper::getVideoSource($url) === 'bunny') {
                $resolvedPlatform = 'bunny';
                $provider = VideoProvider::where('platform', 'bunny')
                    ->where('is_active', true)
                    ->orderByDesc('id')
                    ->first();
                $key = $provider?->token_auth_key ? trim((string) $provider->token_auth_key) : '';
                if ($key !== '') {
                    $embed = BunnyStreamSigner::signEmbedUrl(
                        $embed,
                        $key,
                        now()->addMinutes(20)->timestamp
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
}
