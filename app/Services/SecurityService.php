<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class SecurityService
{
    /**
     * تسجيل محاولة مشبوهة
     */
    public function logSuspiciousActivity(string $type, Request $request, ?string $details = null): void
    {
        $logData = [
            'type' => $type,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => auth()->id(),
            'details' => $details,
            'timestamp' => now()->toDateTimeString(),
        ];

        Log::warning('Suspicious Activity Detected', $logData);
    }

    /**
     * التحقق من SQL Injection في المدخلات
     */
    public function detectSQLInjection(string $input): bool
    {
        $sqlPatterns = [
            '/(\bUNION\b.*\bSELECT\b)/i',
            '/(\bSELECT\b.*\bFROM\b)/i',
            '/(\bINSERT\b.*\bINTO\b)/i',
            '/(\bDELETE\b.*\bFROM\b)/i',
            '/(\bUPDATE\b.*\bSET\b)/i',
            '/(\bDROP\b.*\bTABLE\b)/i',
            '/(\bEXEC\b|\bEXECUTE\b)/i',
            '/(\bSCRIPT\b)/i',
            '/(\b--\b|\b#\b)/',
            '/(\bOR\b.*=.*=)/i',
            '/(\bAND\b.*=.*=)/i',
            '/(\b1\b.*=.*\b1\b)/i',
            '/(\b1\b.*=.*\b0\b)/i',
        ];

        foreach ($sqlPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }

    /**
     * التحقق من XSS في المدخلات
     */
    public function detectXSS(string $input): bool
    {
        $xssPatterns = [
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i',
            '/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/i',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<img[^>]+src[^>]*=.*javascript:/i',
            '/<link[^>]+href[^>]*=.*javascript:/i',
            '/expression\s*\(/i',
            '/vbscript:/i',
        ];

        foreach ($xssPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }

    /**
     * تنظيف المدخلات من XSS و SQL Injection
     */
    public function sanitizeInput(string $input): string
    {
        // إزالة HTML tags
        $cleaned = strip_tags($input);
        
        // إزالة المسافات الزائدة
        $cleaned = trim($cleaned);
        
        // إزالة الأحرف الخاصة الخطيرة
        $cleaned = htmlspecialchars($cleaned, ENT_QUOTES, 'UTF-8');
        
        return $cleaned;
    }

    /**
     * التحقق من صحة الملف المرفوع
     */
    public function validateUploadedFile($file, array $allowedMimes = [], int $maxSize = 5242880): array
    {
        $errors = [];

        // التحقق من وجود الملف
        if (!$file || !$file->isValid()) {
            $errors[] = 'الملف غير صالح';
            return ['valid' => false, 'errors' => $errors];
        }

        // التحقق من الحجم
        if ($file->getSize() > $maxSize) {
            $errors[] = 'حجم الملف يتجاوز الحد المسموح (' . ($maxSize / 1024 / 1024) . ' MB)';
        }

        // التحقق من نوع الملف
        if (!empty($allowedMimes)) {
            $mimeType = $file->getMimeType();
            $extension = strtolower($file->getClientOriginalExtension());
            
            if (!in_array($mimeType, $allowedMimes) && !in_array($extension, $allowedMimes)) {
                $errors[] = 'نوع الملف غير مسموح';
            }
        }

        // التحقق من اسم الملف (السماح بحروف إنجليزية وأرقام ونقاط وشرطات ومسافات فقط - منع مسار أو أحرف خطيرة)
        $fileName = $file->getClientOriginalName();
        if (preg_match('/[\\\\\/<>:"|?*\x00-\x1f]/', $fileName) || preg_match('/\.\./', $fileName)) {
            $errors[] = 'اسم الملف يحتوي على أحرف غير مسموحة';
        }

        // التحقق من محتوى الملف (للملفات الخطيرة)
        $dangerousExtensions = ['php', 'phtml', 'php3', 'php4', 'php5', 'phps', 'phar', 'exe', 'bat', 'sh', 'js'];
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (in_array($extension, $dangerousExtensions)) {
            $errors[] = 'نوع الملف غير مسموح لأسباب أمنية';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Rate Limiting محسن
     */
    public function checkRateLimit(string $key, int $maxAttempts = 5, int $decayMinutes = 1): array
    {
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            return [
                'allowed' => false,
                'retry_after' => $seconds,
                'message' => "تم تجاوز عدد المحاولات المسموح. يرجى المحاولة بعد " . ceil($seconds / 60) . " دقيقة."
            ];
        }

        RateLimiter::hit($key, $decayMinutes * 60);
        
        return [
            'allowed' => true,
            'remaining' => $maxAttempts - RateLimiter::attempts($key)
        ];
    }

    /**
     * التحقق من IP المشبوه
     */
    public function isSuspiciousIP(string $ip): bool
    {
        // قائمة IPs محظورة (يمكن تحميلها من قاعدة البيانات)
        $blockedIPs = config('security.blocked_ips', []);
        
        if (in_array($ip, $blockedIPs)) {
            return true;
        }

        // التحقق من IPs الخاصة (localhost)
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            // IP خاص - قد يكون مشبوه في بيئة الإنتاج
            if (app()->environment('production')) {
                return true;
            }
        }

        return false;
    }

    /**
     * إنشاء اسم ملف آمن
     */
    public function generateSafeFileName(string $originalName): string
    {
        // استخراج الامتداد
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        
        // إنشاء اسم آمن
        $safeName = time() . '_' . uniqid() . '_' . bin2hex(random_bytes(8));
        
        // إضافة الامتداد إذا كان آمن
        $safeExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'mp4', 'webm'];
        if (in_array(strtolower($extension), $safeExtensions)) {
            $safeName .= '.' . strtolower($extension);
        }
        
        return $safeName;
    }
}
