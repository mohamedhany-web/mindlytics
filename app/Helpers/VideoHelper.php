<?php

namespace App\Helpers;

class VideoHelper
{
    /**
     * تحويل رابط الفيديو إلى رابط قابل للتضمين
     */
    public static function getEmbedUrl($url)
    {
        if (empty($url)) {
            return null;
        }

        // YouTube
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches)) {
            $videoId = $matches[1];
            return "https://www.youtube-nocookie.com/embed/{$videoId}?autoplay=0&controls=0&disablekb=1&enablejsapi=1&fs=0&iv_load_policy=3&modestbranding=1&playsinline=1&rel=0&showinfo=0&origin=" . request()->getSchemeAndHttpHost();
        }

        // Vimeo
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            $videoId = $matches[1];
            return "https://player.vimeo.com/video/{$videoId}?title=0&byline=0&portrait=0&controls=0&pip=0&dnt=1&transparent=0";
        }

        // Google Drive
        if (preg_match('/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $fileId = $matches[1];
            return "https://drive.google.com/file/d/{$fileId}/preview";
        }

        // إذا كان رابط مباشر للفيديو
        if (preg_match('/\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i', $url)) {
            return $url;
        }

        // إرجاع الرابط كما هو إذا لم يتطابق مع أي نمط
        return $url;
    }

    /**
     * تحديد نوع مصدر الفيديو
     */
    public static function getVideoSource($url)
    {
        if (empty($url)) {
            return 'unknown';
        }

        if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
            return 'youtube';
        }

        if (strpos($url, 'vimeo.com') !== false) {
            return 'vimeo';
        }

        if (strpos($url, 'drive.google.com') !== false) {
            return 'google_drive';
        }

        if (preg_match('/\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i', $url)) {
            return 'direct';
        }

        return 'other';
    }

    /**
     * الحصول على صورة مصغرة للفيديو
     */
    public static function getThumbnail($url)
    {
        if (empty($url)) {
            return null;
        }

        // YouTube thumbnail
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches)) {
            $videoId = $matches[1];
            return "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";
        }

        // Vimeo thumbnail (يحتاج API call، لكن يمكن استخدام صورة افتراضية)
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            // يمكن تطوير هذا لاحقاً للحصول على الصورة المصغرة من Vimeo API
            return null;
        }

        return null;
    }

    /**
     * التحقق من صحة رابط الفيديو
     */
    public static function isValidVideoUrl($url)
    {
        if (empty($url)) {
            return false;
        }

        // التحقق من الأنماط المدعومة
        $patterns = [
            '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', // YouTube
            '/vimeo\.com\/(\d+)/', // Vimeo
            '/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/', // Google Drive
            '/\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i' // Direct video files
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url)) {
                return true;
            }
        }

        return false;
    }

    /**
     * إنشاء كود HTML لتضمين الفيديو
     */
    public static function generateEmbedHtml($url, $width = '100%', $height = '400px')
    {
        $embedUrl = self::getEmbedUrl($url);
        $source = self::getVideoSource($url);

        if (!$embedUrl) {
            return '<div class="bg-red-100 text-red-700 p-4 rounded-lg">رابط الفيديو غير صحيح أو غير مدعوم</div>';
        }

        switch ($source) {
            case 'youtube':
            case 'vimeo':
            case 'google_drive':
                return "<iframe src='{$embedUrl}' width='{$width}' height='{$height}' frameborder='0' allow='encrypted-media' class='w-full h-full' style='border: none;'></iframe>";
            
            case 'direct':
                return "<video width='{$width}' height='{$height}' class='w-full h-full' controlsList='nodownload noplaybackrate nofullscreen noremoteplayback' disablePictureInPicture disableRemotePlayback><source src='{$embedUrl}' type='video/mp4'>متصفحك لا يدعم تشغيل الفيديو.</video>";
            
            default:
                return "<div class='bg-yellow-100 text-yellow-700 p-4 rounded-lg h-full flex items-center justify-center'>نوع الفيديو غير مدعوم حالياً</div>";
        }
    }
}
