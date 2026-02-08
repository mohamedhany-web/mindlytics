<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\SecurityService;
use Symfony\Component\HttpFoundation\Response;

class FileUploadSecurityMiddleware
{
    protected $securityService;

    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * Handle an incoming request.
     * التحقق من أمان الملفات المرفوعة
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasFile('*')) {
            $files = $request->allFiles();
            
            foreach ($files as $key => $file) {
                // معالجة ملف واحد أو مصفوفة
                $fileArray = is_array($file) ? $file : [$file];
                
                foreach ($fileArray as $uploadedFile) {
                    if ($uploadedFile && $uploadedFile->isValid()) {
                        // التحقق من نوع الملف المسموح
                        $allowedMimes = $this->getAllowedMimes($key);
                        $maxSize = $this->getMaxSize($key);
                        
                        $validation = $this->securityService->validateUploadedFile(
                            $uploadedFile,
                            $allowedMimes,
                            $maxSize
                        );

                        if (!$validation['valid']) {
                            $this->securityService->logSuspiciousActivity(
                                'Invalid File Upload',
                                $request,
                                'File: ' . $uploadedFile->getClientOriginalName() . ' - Errors: ' . implode(', ', $validation['errors'])
                            );
                            
                            return back()
                                ->withErrors([$key => $validation['errors']])
                                ->withInput();
                        }
                    }
                }
            }
        }

        return $next($request);
    }

    /**
     * الحصول على أنواع الملفات المسموحة حسب نوع الحقل
     */
    private function getAllowedMimes(string $fieldName): array
    {
        $mimeMap = [
            // ملفات الموارد والرفع العام (تشمل Excel و Word و PDF والصور) + امتدادات للتحقق عند اختلاف MIME
            'file' => [
                'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
                'application/pdf',
                'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/csv', 'text/plain',
                'application/zip', 'application/vnd.rar',
                'xlsx', 'xls', 'csv', 'doc', 'docx', 'pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'zip', 'rar',
            ],
            'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            'video' => ['video/mp4', 'video/webm', 'video/ogg'],
            'document' => [
                'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/csv',
            ],
            'avatar' => ['image/jpeg', 'image/png', 'image/gif'],
            'thumbnail' => ['image/jpeg', 'image/png', 'image/gif'],
        ];

        foreach ($mimeMap as $key => $mimes) {
            if (stripos($fieldName, $key) !== false) {
                return $mimes;
            }
        }

        // أنواع افتراضية آمنة (تشمل Excel)
        return [
            'image/jpeg', 'image/png', 'image/gif', 'application/pdf',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/csv',
        ];
    }

    /**
     * الحصول على الحد الأقصى لحجم الملف
     */
    private function getMaxSize(string $fieldName): int
    {
        $sizeMap = [
            'video' => 524288000,   // 500 MB
            'image' => 10485760,   // 10 MB
            'document' => 52428800, // 50 MB
            'file' => 52428800,    // 50 MB (ملفات الموارد)
            'avatar' => 2097152,   // 2 MB
        ];

        foreach ($sizeMap as $key => $size) {
            if (stripos($fieldName, $key) !== false) {
                return $size;
            }
        }

        return 10485760; // 10 MB افتراضي
    }
}
