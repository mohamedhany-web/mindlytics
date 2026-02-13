<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogActivityMiddleware
{
    /**
     * مراقبة جميع الطلبات وتسجيل الأنشطة
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // تسجيل الأنشطة فقط للمستخدمين المسجلين
        if (Auth::check()) {
            $this->logActivity($request, $response);
        }

        return $response;
    }

    /**
     * تسجيل النشاط حسب نوع الطلب
     */
    private function logActivity(Request $request, Response $response)
    {
        $user = Auth::user();
        $method = $request->getMethod();
        $path = $request->getPathInfo();
        $route = $request->route();
        
        // تجاهل الطلبات التي لا تحتاج تسجيل
        if ($this->shouldIgnore($path, $method)) {
            return;
        }

        // تجاهل معظم طلبات GET إلا إذا كانت مهمة (مثل show, edit)
        if ($method === 'GET' && $route) {
            $routeName = $route->getName();
            if ($routeName && (
                strpos($routeName, 'show') === false && 
                strpos($routeName, 'edit') === false &&
                strpos($routeName, 'create') === false &&
                strpos($routeName, 'index') === false
            )) {
                return; // تجاهل طلبات GET العادية
            }
        }

        $action = $this->determineAction($method, $path, $route);
        $description = $this->getActionDescription($action, $path, $request);

        // تسجيل النشاط
        try {
            // إضافة session_id إذا كان العمود موجوداً ومتاحاً
            $insertData = [
                'user_id' => $user->id,
                'action' => $action,
                'description' => $description,
                'model_type' => $this->getModelType($path),
                'model_id' => $this->getModelId($route),
                'old_values' => null,
                'new_values' => $method === 'POST' || $method === 'PUT' || $method === 'PATCH' 
                    ? json_encode($this->sanitizeData($request->except(['_token', '_method', 'password', 'password_confirmation']))) 
                    : null,
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) ($request->userAgent() ?? ''), 0, 255),
                'url' => $request->fullUrl(),
                'method' => $method,
                'response_code' => $response->getStatusCode(),
                'duration' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // إضافة session_id إذا كان متاحاً
            if (session()->isStarted()) {
                try {
                    $insertData['session_id'] = session()->getId();
                } catch (\Throwable $e) {
                    // تجاهل
                }
            }
            
            \DB::table('activity_logs')->insert($insertData);
        } catch (\Throwable $e) {
            // تسجيل الخطأ في الـ log فقط بدون إيقاف العملية
            \Log::debug('Failed to log activity: ' . $e->getMessage());
        }
    }

    /**
     * تحديد ما إذا كان يجب تجاهل هذا الطلب
     */
    private function shouldIgnore(string $path, string $method): bool
    {
        $ignorePaths = [
            '/api/notifications',
            '/api/user/status',
            '/_debugbar',
            '/horizon',
            '/telescope',
        ];
        // تجاهل الموافقة/الرفض على الطلبات (يتم التسجيل من الـ Controller)
        if (preg_match('#^/admin/orders/\d+/(approve|reject)$#', $path) && in_array($method, ['POST', 'PUT'], true)) {
            return true;
        }

        $ignorePatterns = [
            '/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf)$/i',
            '/\/api\/.*\/status$/',
            '/\/livewire\//',
        ];

        // تجاهل المسارات المحددة
        foreach ($ignorePaths as $ignorePath) {
            if (strpos($path, $ignorePath) === 0) {
                return true;
            }
        }

        // تجاهل الأنماط المحددة
        foreach ($ignorePatterns as $pattern) {
            if (preg_match($pattern, $path)) {
                return true;
            }
        }

        // تجاهل طلبات GET للـ assets
        if ($method === 'GET' && (
            strpos($path, '/css/') !== false || 
            strpos($path, '/js/') !== false || 
            strpos($path, '/images/') !== false
        )) {
            return true;
        }

        return false;
    }

    /**
     * تحديد نوع النشاط
     */
    private function determineAction(string $method, string $path, $route): string
    {
        $routeName = $route ? $route->getName() : '';

        // أنشطة محددة حسب الـ route
        if ($routeName) {
            if (strpos($routeName, 'exams') !== false) {
                if (strpos($routeName, 'create') !== false || strpos($routeName, 'store') !== false) return 'exam_created';
                if (strpos($routeName, 'edit') !== false || strpos($routeName, 'update') !== false) return 'exam_updated';
                if (strpos($routeName, 'destroy') !== false) return 'exam_deleted';
                if (strpos($routeName, 'show') !== false) return 'exam_viewed';
                if (strpos($routeName, 'questions') !== false) return 'exam_questions_managed';
                if (strpos($routeName, 'statistics') !== false) return 'exam_statistics_viewed';
                if (strpos($routeName, 'preview') !== false) return 'exam_previewed';
                if (strpos($routeName, 'take') !== false) return 'exam_taken';
                if (strpos($routeName, 'result') !== false) return 'exam_result_viewed';
            }

            if (strpos($routeName, 'courses') !== false) {
                if (strpos($routeName, 'create') !== false || strpos($routeName, 'store') !== false) return 'course_created';
                if (strpos($routeName, 'edit') !== false || strpos($routeName, 'update') !== false) return 'course_updated';
                if (strpos($routeName, 'destroy') !== false) return 'course_deleted';
                if (strpos($routeName, 'show') !== false) return 'course_viewed';
                if (strpos($routeName, 'lessons') !== false) return 'lesson_activity';
            }

            if (strpos($routeName, 'users') !== false) {
                if (strpos($routeName, 'create') !== false || strpos($routeName, 'store') !== false) return 'user_created';
                if (strpos($routeName, 'edit') !== false || strpos($routeName, 'update') !== false) return 'user_updated';
                if (strpos($routeName, 'destroy') !== false) return 'user_deleted';
                if (strpos($routeName, 'show') !== false) return 'user_profile_viewed';
            }

            if (strpos($routeName, 'question') !== false) {
                if (strpos($routeName, 'create') !== false || strpos($routeName, 'store') !== false) return 'question_created';
                if (strpos($routeName, 'edit') !== false || strpos($routeName, 'update') !== false) return 'question_updated';
                if (strpos($routeName, 'destroy') !== false) return 'question_deleted';
            }
        }

        // أنشطة عامة حسب HTTP method
        switch($method) {
            case 'GET':
                return 'page_visited';
            case 'POST':
                return 'data_created';
            case 'PUT':
            case 'PATCH':
                return 'data_updated';
            case 'DELETE':
                return 'data_deleted';
            default:
                return 'unknown_action';
        }
    }

    /**
     * الحصول على وصف النشاط
     */
    private function getActionDescription(string $action, string $path, Request $request): string
    {
        $descriptions = [
            'exam_created' => 'إنشاء امتحان جديد',
            'exam_updated' => 'تحديث امتحان',
            'exam_deleted' => 'حذف امتحان',
            'exam_viewed' => 'عرض تفاصيل امتحان',
            'exam_questions_managed' => 'إدارة أسئلة امتحان',
            'exam_statistics_viewed' => 'عرض إحصائيات امتحان',
            'exam_previewed' => 'معاينة امتحان',
            'exam_taken' => 'بدء أداء امتحان',
            'exam_result_viewed' => 'عرض نتائج امتحان',
            'course_created' => 'إنشاء كورس جديد',
            'course_updated' => 'تحديث كورس',
            'course_deleted' => 'حذف كورس',
            'course_viewed' => 'عرض تفاصيل كورس',
            'lesson_activity' => 'نشاط في الدروس',
            'user_created' => 'إنشاء مستخدم جديد',
            'user_updated' => 'تحديث بيانات مستخدم',
            'user_deleted' => 'حذف مستخدم',
            'user_profile_viewed' => 'عرض ملف مستخدم',
            'question_created' => 'إنشاء سؤال جديد',
            'question_updated' => 'تحديث سؤال',
            'question_deleted' => 'حذف سؤال',
            'page_visited' => 'زيارة صفحة',
            'data_created' => 'إنشاء بيانات',
            'data_updated' => 'تحديث بيانات',
            'data_deleted' => 'حذف بيانات',
        ];

        $baseDescription = isset($descriptions[$action]) ? $descriptions[$action] : 'نشاط غير معروف';
        
        // إضافة تفاصيل أكثر
        if (strpos($path, '/admin/') !== false) {
            $baseDescription = '[لوحة الإدارة] ' . $baseDescription;
        } elseif (strpos($path, '/student/') !== false) {
            $baseDescription = '[لوحة الطالب] ' . $baseDescription;
        }

        return $baseDescription;
    }

    /**
     * الحصول على نوع النموذج من المسار
     */
    private function getModelType(string $path)
    {
        if (strpos($path, '/exams/') !== false) return 'App\\Models\\Exam';
        if (strpos($path, '/courses/') !== false) return 'App\\Models\\AdvancedCourse';
        if (strpos($path, '/users/') !== false) return 'App\\Models\\User';
        if (strpos($path, '/questions/') !== false) return 'App\\Models\\Question';
        if (strpos($path, '/academic-years/') !== false) return 'App\\Models\\AcademicYear';
        if (strpos($path, '/academic-subjects/') !== false) return 'App\\Models\\AcademicSubject';
        if (strpos($path, '/enrollments/') !== false) return 'App\\Models\\StudentCourseEnrollment';
        
        return null;
    }

    /**
     * الحصول على معرف النموذج من الـ route
     */
    private function getModelId($route): ?int
    {
        if (!$route) return null;

        $parameters = $route->parameters();
        
        // البحث عن معرف النموذج في parameters
        foreach ($parameters as $param) {
            if (is_object($param) && method_exists($param, 'getKey')) {
                return $param->getKey();
            } elseif (is_numeric($param)) {
                return (int) $param;
            }
        }

        return null;
    }

    /**
     * تنظيف البيانات الحساسة
     */
    private function sanitizeData(array $data): array
    {
        $sensitiveFields = ['password', 'password_confirmation', 'token', 'api_key', 'secret'];
        
        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '[محذوف للأمان]';
            }
        }

        return $data;
    }
}