<?php

namespace App\Support;

/**
 * تحديد طريقة عرض فيديو المقدمة (كورس / مسار تعليمي):
 * YouTube، Vimeo، Bunny (رابط مباشر .mp4… أو رابط embed/play من mediadelivery).
 */
final class IntroVideoResolver
{
    /**
     * @return array{type: string, embed: ?string, direct: ?string, mime: string}
     */
    public static function resolve(?string $rawUrl): array
    {
        $url = trim((string) $rawUrl);
        $empty = ['type' => 'unknown', 'embed' => null, 'direct' => null, 'mime' => ''];

        if ($url === '') {
            return $empty;
        }

        // YouTube
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $url, $m)) {
            return [
                'type' => 'youtube',
                'embed' => 'https://www.youtube.com/embed/'.$m[1].'?rel=0&modestbranding=1&showinfo=0',
                'direct' => null,
                'mime' => '',
            ];
        }

        // Vimeo
        if (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $url, $m)) {
            return [
                'type' => 'vimeo',
                'embed' => 'https://player.vimeo.com/video/'.$m[1],
                'direct' => null,
                'mime' => '',
            ];
        }

        $isBunnyHost = (bool) preg_match('#(?:iframe\.)?mediadelivery\.net|bunnycdn\.com|\.b-cdn\.net#i', $url);

        // رابط ملف مباشر (Bunny CDN أو أي استضافة) — mp4, webm, mov…
        if (preg_match('/\.(mp4|webm|ogg|m4v|mov|avi)(\?.*)?$/i', $url)) {
            return [
                'type' => 'html5',
                'embed' => null,
                'direct' => $url,
                'mime' => 'video/mp4',
            ];
        }

        // HLS (شائع مع Bunny) — يعمل جيداً في Safari؛ في كروم قد تحتاج مشغّلاً متقدماً
        if (preg_match('/\.m3u8(\?.*)?$/i', $url)) {
            return [
                'type' => 'html5',
                'embed' => null,
                'direct' => $url,
                'mime' => 'application/vnd.apple.mpegURL',
            ];
        }

        // Bunny: صفحة تشغيل / تضمين (ليس ملفاً مباشراً)
        if ($isBunnyHost) {
            $embed = self::normalizeBunnyFrameUrl($url);
            if ($embed !== null) {
                return [
                    'type' => 'bunny_embed',
                    'embed' => $embed,
                    'direct' => null,
                    'mime' => '',
                ];
            }
        }

        return $empty;
    }

    private static function normalizeBunnyFrameUrl(string $url): ?string
    {
        $trimmed = trim($url);
        if ($trimmed === '') {
            return null;
        }

        // رابط embed قياسي: ...mediadelivery.net/embed/{libraryId}/{videoId}
        if (preg_match('/mediadelivery\.net\/embed\/(\d+)\/([a-zA-Z0-9_-]+)/i', $trimmed, $m)) {
            $base = preg_replace('/\?.*$/', '', $trimmed);
            if (! preg_match('#^https?://#i', $base)) {
                $base = 'https://'.ltrim($base, '/');
            }

            return $base;
        }

        // روابط play على iframe.mediadelivery.net أو مسارات play/
        if (preg_match('#iframe\.mediadelivery\.net#i', $trimmed)
            || preg_match('#mediadelivery\.net/(?:play|embed)/#i', $trimmed)) {
            $base = preg_replace('/\?.*$/', '', $trimmed);
            if (! preg_match('#^https?://#i', $base)) {
                $base = 'https://'.ltrim($base, '/');
            }

            return $base;
        }

        // أي رابط https على مضيف Bunny نعرضه كإطار (توجيهات Bunny «رابط المشغّل»)
        if (preg_match('#^https?://[^\s]+$#i', $trimmed) && preg_match('#(?:iframe\.)?mediadelivery\.net|bunnycdn\.com|\.b-cdn\.net#i', $trimmed)) {
            return $trimmed;
        }

        return null;
    }
}
