<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\VideoHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VideoInfoController extends Controller
{
    /**
     * الحصول على معلومات الفيديو من الرابط
     */
    public function getInfo(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'platform' => 'required|in:youtube,vimeo,google_drive,direct'
        ]);

        $url = $request->input('url');
        $platform = $request->input('platform');

        $info = [
            'title' => null,
            'duration' => null,
            'thumbnail' => null,
            'description' => null,
        ];

        try {
            switch ($platform) {
                case 'youtube':
                    $info = $this->getYouTubeInfo($url);
                    break;
                case 'vimeo':
                    $info = $this->getVimeoInfo($url);
                    break;
                case 'google_drive':
                    $info = $this->getGoogleDriveInfo($url);
                    break;
                case 'direct':
                    $info = $this->getDirectVideoInfo($url);
                    break;
            }

            return response()->json([
                'success' => true,
                'data' => $info
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في قراءة معلومات الفيديو: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * الحصول على معلومات YouTube
     */
    private function getYouTubeInfo($url)
    {
        // استخراج video ID
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
        
        if (!isset($matches[1])) {
            throw new \Exception('رابط YouTube غير صحيح');
        }

        $videoId = $matches[1];

        // محاولة الحصول على المعلومات من oEmbed API (لا يحتاج API key)
        try {
            $response = Http::get("https://www.youtube.com/oembed", [
                'url' => "https://www.youtube.com/watch?v={$videoId}",
                'format' => 'json'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'title' => $data['title'] ?? null,
                    'duration' => null, // oEmbed لا يوفر المدة
                    'thumbnail' => $data['thumbnail_url'] ?? "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg",
                    'description' => null,
                ];
            }
        } catch (\Exception $e) {
            // في حالة فشل oEmbed، نرجع معلومات أساسية
        }

        // معلومات أساسية بدون API
        return [
            'title' => null,
            'duration' => null,
            'thumbnail' => "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg",
            'description' => null,
        ];
    }

    /**
     * الحصول على معلومات Vimeo
     */
    private function getVimeoInfo($url)
    {
        preg_match('/vimeo\.com\/(\d+)/', $url, $matches);
        
        if (!isset($matches[1])) {
            throw new \Exception('رابط Vimeo غير صحيح');
        }

        $videoId = $matches[1];

        // محاولة الحصول على المعلومات من oEmbed API
        try {
            $response = Http::get("https://vimeo.com/api/oembed.json", [
                'url' => "https://vimeo.com/{$videoId}"
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'title' => $data['title'] ?? null,
                    'duration' => isset($data['duration']) ? $this->formatDuration($data['duration']) : null,
                    'thumbnail' => $data['thumbnail_url'] ?? null,
                    'description' => $data['description'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            // في حالة فشل oEmbed
        }

        return [
            'title' => null,
            'duration' => null,
            'thumbnail' => null,
            'description' => null,
        ];
    }

    /**
     * الحصول على معلومات Google Drive
     */
    private function getGoogleDriveInfo($url)
    {
        preg_match('/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/', $url, $matches);
        
        if (!isset($matches[1])) {
            throw new \Exception('رابط Google Drive غير صحيح');
        }

        // Google Drive لا يوفر معلومات مباشرة بدون API
        return [
            'title' => null,
            'duration' => null,
            'thumbnail' => null,
            'description' => null,
        ];
    }

    /**
     * الحصول على معلومات الفيديو المباشر
     */
    private function getDirectVideoInfo($url)
    {
        // للفيديو المباشر، نحاول الحصول على معلومات من headers
        try {
            $headers = get_headers($url, 1);
            
            if ($headers && isset($headers['Content-Length'])) {
                $size = $headers['Content-Length'];
                $sizeFormatted = $this->formatBytes($size);
            }
        } catch (\Exception $e) {
            // لا يمكن الحصول على المعلومات
        }

        return [
            'title' => basename(parse_url($url, PHP_URL_PATH)),
            'duration' => null,
            'thumbnail' => null,
            'description' => null,
        ];
    }

    /**
     * تحويل الثواني إلى تنسيق الوقت
     */
    private function formatDuration($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
        }
        return sprintf('%d:%02d', $minutes, $secs);
    }

    /**
     * تحويل البايتات إلى تنسيق مقروء
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
