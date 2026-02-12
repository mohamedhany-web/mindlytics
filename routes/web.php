<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Storage files (صور وملفات) - يجب أن يكون أول Route لضمان عدم اعتراضه
| يعمل عند عدم وجود symlink public/storage على الاستضافة
|--------------------------------------------------------------------------
*/
Route::get('/storage/{path}', function ($path) {
    $path = rawurldecode($path);
    $path = str_replace('..', '', $path);
    $path = ltrim($path, '/');

    $basePath = storage_path('app/public');
    $filePath = $basePath . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    $filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);

    if (!@file_exists($filePath) || !@is_file($filePath)) {
        if (config('app.debug')) {
            \Log::warning('Storage file not found', ['requested_path' => $path]);
        }
        abort(404, 'File not found');
    }

    $realPath = @realpath($filePath) ?: $filePath;
    $allowedPath = @realpath($basePath) ?: $basePath;
    $normalizedRealPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $realPath);
    $normalizedAllowedPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $allowedPath);

    if ($allowedPath === '' || strpos($normalizedRealPath, $normalizedAllowedPath) !== 0) {
        abort(404, 'Access denied');
    }

    if (!@is_readable($realPath)) {
        abort(403, 'File not readable');
    }

    $mimeType = @mime_content_type($realPath);
    if (!$mimeType) {
        $extension = strtolower(pathinfo($realPath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif',
            'webp' => 'image/webp', 'svg' => 'image/svg+xml', 'pdf' => 'application/pdf',
            'doc' => 'application/msword', 'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];
        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
    }

    $headers = [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000, immutable',
    ];
    if ($mimeType === 'application/pdf') {
        $headers['Content-Disposition'] = 'inline; filename="' . basename($realPath) . '"';
    }

    return response()->file($realPath, $headers);
})->where('path', '.*')->name('storage.file')->middleware('web');

// Sitemap Route
Route::get('/sitemap.xml', function() {
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
    
    // Home Page
    $sitemap .= '
    <url>
        <loc>' . url('/') . '</loc>
        <lastmod>' . date('Y-m-d') . '</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>';
    
    // Public Pages
    $publicPages = [
        ['url' => '/courses', 'priority' => '0.9', 'changefreq' => 'daily'],
        ['url' => '/about', 'priority' => '0.8', 'changefreq' => 'monthly'],
        ['url' => '/blog', 'priority' => '0.9', 'changefreq' => 'daily'],
        ['url' => '/contact', 'priority' => '0.7', 'changefreq' => 'monthly'],
        ['url' => '/pricing', 'priority' => '0.8', 'changefreq' => 'weekly'],
        ['url' => '/faq', 'priority' => '0.7', 'changefreq' => 'monthly'],
        ['url' => '/terms', 'priority' => '0.5', 'changefreq' => 'yearly'],
        ['url' => '/privacy', 'priority' => '0.5', 'changefreq' => 'yearly'],
    ];
    
    foreach ($publicPages as $page) {
        $sitemap .= '
    <url>
        <loc>' . url($page['url']) . '</loc>
        <lastmod>' . date('Y-m-d') . '</lastmod>
        <changefreq>' . $page['changefreq'] . '</changefreq>
        <priority>' . $page['priority'] . '</priority>
    </url>';
    }
    
    // Active Courses
    try {
        $courses = \App\Models\AdvancedCourse::where('is_active', true)
            ->select('id', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get();
        
        foreach ($courses as $course) {
            $sitemap .= '
    <url>
        <loc>' . url('/course/' . $course->id) . '</loc>
        <lastmod>' . $course->updated_at->format('Y-m-d') . '</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>';
        }
    } catch (\Exception $e) {
        // Skip if courses table doesn't exist
    }
    
    // Blog Posts
    try {
        $posts = \App\Models\BlogPost::where('is_published', true)
            ->select('slug', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get();
        
        foreach ($posts as $post) {
            $sitemap .= '
    <url>
        <loc>' . url('/blog/' . $post->slug) . '</loc>
        <lastmod>' . $post->updated_at->format('Y-m-d') . '</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>';
        }
    } catch (\Exception $e) {
        // Skip if blog table doesn't exist
    }
    
    $sitemap .= '
</urlset>';
    
    return response($sitemap, 200)
        ->header('Content-Type', 'application/xml');
})->name('sitemap');

// الصفحة الرئيسية (Home) - welcome.blade.php
Route::get('/', function () {
    return view('welcome');
})->name('home');

// الصفحات العامة
Route::get('/about', [\App\Http\Controllers\Public\PageController::class, 'about'])->name('public.about');
Route::get('/faq', [\App\Http\Controllers\Public\PageController::class, 'faq'])->name('public.faq');
Route::get('/terms', [\App\Http\Controllers\Public\PageController::class, 'terms'])->name('public.terms');
Route::get('/privacy', [\App\Http\Controllers\Public\PageController::class, 'privacy'])->name('public.privacy');
Route::get('/pricing', [\App\Http\Controllers\Public\PageController::class, 'pricing'])->name('public.pricing');
Route::get('/team', [\App\Http\Controllers\Public\PageController::class, 'team'])->name('public.team');
Route::get('/certificates', [\App\Http\Controllers\Public\PageController::class, 'certificates'])->name('public.certificates');
Route::get('/certificates/verify', [\App\Http\Controllers\Public\CertificateVerificationController::class, 'verify'])->name('public.certificates.verify');
Route::get('/certificates/verify/{code}', [\App\Http\Controllers\Public\CertificateVerificationController::class, 'verify'])->name('public.certificates.verify.code');
Route::get('/help', [\App\Http\Controllers\Public\PageController::class, 'help'])->name('public.help');
Route::get('/refund', [\App\Http\Controllers\Public\PageController::class, 'refund'])->name('public.refund');
Route::get('/testimonials', [\App\Http\Controllers\Public\PageController::class, 'testimonials'])->name('public.testimonials');
Route::get('/events', [\App\Http\Controllers\Public\PageController::class, 'events'])->name('public.events');
Route::get('/partners', [\App\Http\Controllers\Public\PageController::class, 'partners'])->name('public.partners');

// المدونة
Route::get('/blog', [\App\Http\Controllers\Public\BlogController::class, 'index'])->name('public.blog.index');
Route::get('/blog/{slug}', [\App\Http\Controllers\Public\BlogController::class, 'show'])->name('public.blog.show');

// Mindlytics Portfolio (معرض أعمال الطلاب)
Route::get('/portfolio', [\App\Http\Controllers\Public\PortfolioController::class, 'index'])->name('public.portfolio.index');
Route::get('/portfolio/{id}', [\App\Http\Controllers\Public\PortfolioController::class, 'show'])->name('public.portfolio.show')->where('id', '[0-9]+');

// التواصل
Route::get('/contact', [\App\Http\Controllers\Public\ContactController::class, 'index'])->name('public.contact');
Route::post('/contact', [\App\Http\Controllers\Public\ContactController::class, 'store'])->name('public.contact.store');

// معرض الصور والفيديوهات
Route::get('/media', [\App\Http\Controllers\Public\MediaController::class, 'index'])->name('public.media.index');
Route::get('/media/{media}', [\App\Http\Controllers\Public\MediaController::class, 'show'])->name('public.media.show');

// صفحة الكورسات العامة
Route::get('/courses', function () {
    $coursesQuery = \App\Models\AdvancedCourse::where('is_active', true);
    
    // جلب الكورسات مع العلاقات
    $coursesCollection = $coursesQuery
        ->with(['academicSubject', 'academicYear'])
        ->withCount('lessons')
        ->orderBy('is_featured', 'desc')
        ->orderBy('created_at', 'desc')
        ->get();
    
    // تحويل البيانات إلى array
    $courses = $coursesCollection->map(function ($course) {
        return [
            'id' => $course->id,
            'title' => $course->title ?? 'بدون عنوان',
            'description' => $course->description ?? '',
            'level' => $course->level ?? 'beginner',
            'price' => (float)($course->price ?? 0),
            'duration_hours' => (int)($course->duration_hours ?? 0),
            'is_featured' => (bool)($course->is_featured ?? false),
            'lessons_count' => (int)($course->lessons_count ?? 0),
            'thumbnail' => $course->thumbnail ?? null,
            'academic_subject' => $course->academicSubject ? [
                'name' => $course->academicSubject->name ?? 'غير محدد'
            ] : null,
        ];
    })->values()->toArray();
    
    // جلب الباقات النشطة
    $packages = \App\Models\Package::active()
        ->with(['courses' => function($query) {
            $query->where('is_active', true);
        }])
        ->withCount('courses')
        ->orderBy('is_featured', 'desc')
        ->orderBy('is_popular', 'desc')
        ->orderBy('order')
        ->get();
    
    return view('courses', compact('courses', 'packages'));
})->name('public.courses');

// صفحة تفاصيل الكورس العامة
Route::get('/course/{id}', function ($id) {
    $course = \App\Models\AdvancedCourse::where('id', $id)
        ->where('is_active', true)
        ->with(['academicSubject', 'academicYear'])
        ->withCount('lessons')
        ->firstOrFail();
    
    // التحقق من التسجيل في الكورس
    $isEnrolled = false;
    if(auth()->check()) {
        $isEnrolled = \App\Models\StudentCourseEnrollment::where('user_id', auth()->id())
            ->where('advanced_course_id', $course->id)
            ->where('status', 'active')
            ->exists();
    }
    
    // كورسات ذات صلة
    $relatedCourses = \App\Models\AdvancedCourse::where('is_active', true)
        ->where('id', '!=', $course->id)
        ->where(function($query) use ($course) {
            $query->where('level', $course->level)
                  ->orWhere('academic_subject_id', $course->academic_subject_id)
                  ->orWhere('is_featured', true);
        })
        ->with(['academicSubject'])
        ->withCount('lessons')
        ->limit(3)
        ->get();
    
    return view('course-show', compact('course', 'relatedCourses', 'isEnrolled'));
})->name('public.course.show');

// صفحة إتمام الطلب (Checkout)
Route::get('/course/{courseId}/checkout', [\App\Http\Controllers\Public\CheckoutController::class, 'show'])
    ->middleware('auth')
    ->name('public.course.checkout');

Route::post('/course/{courseId}/checkout/complete', [\App\Http\Controllers\Public\CheckoutController::class, 'complete'])
    ->middleware('auth')
    ->name('public.course.checkout.complete');

// تسجيل مجاني للكورسات المجانية
Route::post('/course/{courseId}/enroll-free', [\App\Http\Controllers\Public\CheckoutController::class, 'enrollFree'])
    ->middleware('auth')
    ->name('public.course.enroll.free');

// صفحة المسارات التعليمية
Route::get('/learning-paths', [\App\Http\Controllers\Public\LearningPathController::class, 'index'])->name('public.learning-paths.index');

// صفحة تفاصيل مسار تعليمي (يجب أن يكون قبل الـ routes المحمية)
// ملاحظة: يجب أن يكون هذا الـ route قبل أي route محمي يستخدم نفس الـ pattern
Route::get('/learning-path/{slug}', [\App\Http\Controllers\Public\LearningPathController::class, 'show'])
    ->where('slug', '[a-z0-9-]+')
    ->name('public.learning-path.show');

// صفحة إتمام الطلب للمسارات التعليمية (Checkout)
Route::get('/learning-path/{slug}/checkout', [\App\Http\Controllers\Public\CheckoutController::class, 'showLearningPath'])
    ->middleware('auth')
    ->name('public.learning-path.checkout');

Route::post('/learning-path/{slug}/checkout/complete', [\App\Http\Controllers\Public\CheckoutController::class, 'completeLearningPath'])
    ->middleware('auth')
    ->name('public.learning-path.checkout.complete');

// تسجيل مجاني للمسارات المجانية
Route::post('/learning-path/{slug}/enroll-free', [\App\Http\Controllers\Public\CheckoutController::class, 'enrollFreeLearningPath'])
    ->middleware('auth')
    ->name('public.learning-path.enroll.free');

// الاشتراك في مسار تعليمي (للتوافق مع الكود القديم - سيتم توجيهه للـ checkout إذا كان مدفوع)
Route::post('/learning-path/{slug}/enroll', [\App\Http\Controllers\Public\LearningPathController::class, 'enroll'])
    ->middleware('auth')
    ->name('public.learning-path.enroll');

// صفحة تفاصيل الباقة (للتوافق مع الروابط القديمة)
Route::get('/package/{slug}', function ($slug) {
    $package = \App\Models\Package::where('slug', $slug)
        ->where('is_active', true)
        ->with(['courses' => function($query) {
            $query->where('is_active', true)
                  ->with(['academicSubject', 'academicYear'])
                  ->withCount('lessons');
        }])
        ->firstOrFail();
    
    // باقات ذات صلة
    $relatedPackages = \App\Models\Package::where('is_active', true)
        ->where('id', '!=', $package->id)
        ->withCount('courses')
        ->limit(3)
        ->get();
    
    return view('package-show', compact('package', 'relatedPackages'));
})->name('public.package.show');

// مسارات المصادقة - محمية بحيث لا يمكن الوصول إليها إذا كان المستخدم مسجل دخول
Route::middleware(['guest', 'guest-only'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,15'); // 5 محاولات كل 15 دقيقة - حماية من Brute Force
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    // Rate limiting للتسجيل: 5 محاولات في الدقيقة من نفس IP
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
    // تسجيل الدخول بـ Google (إنشاء أو ربط حساب طالب)
    Route::get('/auth/google', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'callback'])->name('auth.google.callback');
});

// المصادقة الثنائية (2FA) - بعد إدخال البريد وكلمة المرور للمدربين/الإدمن/الموظفين
// throttle: 30 طلب كل دقيقتين لتقليل ظهور "طلبات كثيرة جداً" أثناء الاستخدام العادي
Route::middleware(['web', 'throttle:30,2'])->group(function () {
    Route::get('/2fa/challenge', [\App\Http\Controllers\Auth\TwoFactorController::class, 'showChallenge'])->name('two-factor.challenge');
    Route::post('/2fa/verify', [\App\Http\Controllers\Auth\TwoFactorController::class, 'verifyChallenge'])->name('two-factor.verify');
});

// تسجيل الخروج - يجب أن يكون المستخدم مسجل دخول
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// إعداد المصادقة الثنائية (للإدمن / المدربين / الموظفين)
Route::middleware(['auth'])->prefix('2fa')->name('two-factor.')->group(function () {
    Route::get('/setup', [\App\Http\Controllers\Auth\TwoFactorController::class, 'showSetup'])->name('setup');
    Route::post('/enable', [\App\Http\Controllers\Auth\TwoFactorController::class, 'enable'])->name('enable');
    Route::post('/disable', [\App\Http\Controllers\Auth\TwoFactorController::class, 'disable'])->name('disable');
});

// مسارات لوحة التحكم - محمية بالتأكد من تسجيل الدخول ومنع الجلسات المتزامنة
Route::middleware(['auth', 'prevent-concurrent'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // مسارات الطلاب
    Route::get('/academic-years', [\App\Http\Controllers\Student\AcademicYearController::class, 'index'])->name('academic-years');
    Route::get('/academic-years/{academicYear}/subjects', [\App\Http\Controllers\Student\AcademicYearController::class, 'subjects'])->name('academic-years.subjects');
    Route::get('/subjects/{academicSubject}/courses', [\App\Http\Controllers\Student\SubjectController::class, 'courses'])->name('subjects.courses');
    Route::get('/courses/{advancedCourse}', [\App\Http\Controllers\Student\CourseController::class, 'show'])->name('courses.show');
    
        // كورساتي المفعلة - محمية للطلاب فقط
        Route::middleware(['role:student'])->group(function () {
            Route::get('/my-courses', [\App\Http\Controllers\Student\MyCourseController::class, 'index'])->name('my-courses.index');
            Route::get('/my-courses/{course}', [\App\Http\Controllers\Student\MyCourseController::class, 'show'])
                ->middleware(['ownership:course,course'])
                ->name('my-courses.show');
            
            // الكورسات الأوفلاين للطلاب (واجهات منفصلة عن الأونلاين)
            Route::prefix('offline-courses')->name('student.offline-courses.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Student\OfflineCourseController::class, 'index'])->name('index');
                Route::get('/{offlineCourse}/resources', [\App\Http\Controllers\Student\OfflineCourseController::class, 'resources'])->name('resources');
                Route::get('/{offlineCourse}/lectures', [\App\Http\Controllers\Student\OfflineCourseController::class, 'lectures'])->name('lectures');
                Route::get('/{offlineCourse}/activities/{activity}', [\App\Http\Controllers\Student\OfflineCourseController::class, 'activityShow'])->name('activities.show');
                Route::post('/{offlineCourse}/activities/{activity}/submit', [\App\Http\Controllers\Student\OfflineCourseController::class, 'activitySubmit'])->name('activities.submit');
                Route::get('/{offlineCourse}', [\App\Http\Controllers\Student\OfflineCourseController::class, 'show'])->name('show');
            });
            
            // المسار التعليمي للطالب
            Route::get('/student/learning-path/{slug}', [\App\Http\Controllers\Student\LearningPathController::class, 'show'])->name('student.learning-path.show');
        Route::get('/my-courses/{course}/learn', [\App\Http\Controllers\Student\MyCourseController::class, 'learn'])
            ->middleware(['ownership:course,course'])
            ->name('my-courses.learn');
        Route::get('/my-courses/{course}/lessons/{lesson}/watch', [\App\Http\Controllers\Student\MyCourseController::class, 'watchLesson'])
            ->middleware([\App\Http\Middleware\VideoProtectionMiddleware::class, 'ownership:course,course'])
            ->name('my-courses.lesson.watch');
        Route::post('/my-courses/{course}/lessons/{lesson}/progress', [\App\Http\Controllers\Student\MyCourseController::class, 'updateLessonProgress'])
            ->middleware(['ownership:course,course'])
            ->name('my-courses.lesson.progress');
        
        // أنماط التعلم التفاعلية
        Route::prefix('my-courses/{course}/learning-patterns')->name('my-courses.learning-patterns.')->group(function () {
            Route::get('/{pattern}', [\App\Http\Controllers\Student\LearningPatternController::class, 'show'])
                ->middleware(['ownership:course,course'])
                ->name('show');
            Route::post('/{pattern}/start', [\App\Http\Controllers\Student\LearningPatternController::class, 'startAttempt'])
                ->middleware(['ownership:course,course'])
                ->name('start');
            Route::post('/{pattern}/attempts/{attempt}/save', [\App\Http\Controllers\Student\LearningPatternController::class, 'saveProgress'])
                ->middleware(['ownership:course,course'])
                ->name('save-progress');
            Route::post('/{pattern}/attempts/{attempt}/complete', [\App\Http\Controllers\Student\LearningPatternController::class, 'completeAttempt'])
                ->middleware(['ownership:course,course'])
                ->name('complete');
        });
    });
    
    // الإحالات
    Route::get('/referrals', [\App\Http\Controllers\Student\ReferralController::class, 'index'])->name('referrals.index');
    Route::post('/referrals/copy-link', [\App\Http\Controllers\Student\ReferralController::class, 'copyLink'])->name('referrals.copy-link');
    
    // API للتحقق من الكوبون
    Route::post('/api/validate-coupon', [\App\Http\Controllers\Student\CouponController::class, 'validateCoupon'])->name('api.validate-coupon');
    
    // API لمعلومات الفيديو
    Route::post('/api/video/info', [\App\Http\Controllers\Api\VideoInfoController::class, 'getInfo'])->name('api.video.info');
    
    // API للدروس - محمية بالتأكد من التسجيل
    Route::get('/api/lessons/{lesson}', function(\App\Models\CourseLesson $lesson) {
        $user = auth()->user();
        
        // التحقق من أن المستخدم طالب
        if (!$user->isStudent()) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }
        
        // التحقق من أن المستخدم مسجل في الكورس
        if (!$user->isEnrolledIn($lesson->advanced_course_id)) {
            return response()->json(['error' => 'غير مصرح - غير مسجل في الكورس'], 403);
        }
        
        $progress = $lesson->progress()->where('user_id', $user->id)->first();
        
        return response()->json([
            'id' => $lesson->id,
            'title' => $lesson->title,
            'description' => $lesson->description,
            'content' => $lesson->content,
            'type' => $lesson->type,
            'video_url' => $lesson->video_url ? trim($lesson->video_url) : null,
            'duration_minutes' => $lesson->duration_minutes,
            'attachments' => $lesson->attachments ? json_decode($lesson->attachments, true) : null,
            'progress' => $progress ? ['is_completed' => (bool) $progress->is_completed] : null
        ]);
    });

    // API للطلاب المسجلين في الكورس - محمية بـ role middleware
    Route::get('/api/courses/{course}/students', function($course) {
        $instructor = auth()->user();
        
        // التحقق من أن المستخدم مدرب
        if (!$instructor->isInstructor()) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }
        
        // التحقق من أن الكورس يخص المدرب
        $advancedCourse = \App\Models\AdvancedCourse::where('id', $course)
            ->where('instructor_id', $instructor->id)
            ->firstOrFail();
        
        // جلب الطلاب المسجلين في الكورس
        $enrollments = \App\Models\StudentCourseEnrollment::where('advanced_course_id', $course)
            ->where('status', 'active')
            ->with('user')
            ->get();
        
        $students = $enrollments->map(function($enrollment) {
            $user = $enrollment->user;
            return [
                'id' => $user->id,
                'name' => $user->name ?? $user->full_name ?? ($user->first_name . ' ' . $user->last_name),
                'full_name' => $user->full_name ?? ($user->first_name . ' ' . $user->last_name),
                'first_name' => $user->first_name ?? '',
                'last_name' => $user->last_name ?? '',
                'email' => $user->email,
            ];
        });
        
        return response()->json([
            'students' => $students,
            'count' => $students->count()
        ]);
    })->middleware(['auth', 'role:instructor|teacher']);

    // نظام الطلبات - محمي للطلاب فقط
    Route::middleware(['role:student'])->group(function () {
        Route::post('/courses/{advancedCourse}/order', [\App\Http\Controllers\Student\OrderController::class, 'store'])->name('courses.order');
        Route::get('/orders', [\App\Http\Controllers\Student\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [\App\Http\Controllers\Student\OrderController::class, 'show'])
            ->middleware(['ownership:order,order'])
            ->name('orders.show');
    });
    
    // امتحانات الطلاب - محمية للطلاب فقط
    Route::prefix('exams')->name('student.exams.')->middleware(['role:student'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\ExamController::class, 'index'])->name('index');
        Route::get('/{exam}', [\App\Http\Controllers\Student\ExamController::class, 'show'])->name('show');
        Route::post('/{exam}/start', [\App\Http\Controllers\Student\ExamController::class, 'start'])->name('start');
        Route::get('/{exam}/attempts/{attempt}/take', [\App\Http\Controllers\Student\ExamController::class, 'take'])
            ->middleware(\App\Http\Middleware\VideoProtectionMiddleware::class)
            ->name('take');
        Route::post('/{exam}/attempts/{attempt}/save-answer', [\App\Http\Controllers\Student\ExamController::class, 'saveAnswer'])->name('save-answer');
        Route::post('/{exam}/attempts/{attempt}/submit', [\App\Http\Controllers\Student\ExamController::class, 'submit'])->name('submit');
        Route::get('/{exam}/attempts/{attempt}/result', [\App\Http\Controllers\Student\ExamController::class, 'result'])->name('result');
        Route::post('/{exam}/attempts/{attempt}/tab-switch', [\App\Http\Controllers\Student\ExamController::class, 'logTabSwitch'])->name('tab-switch');
    });

    // صفحات الطلاب - محمية للطلاب فقط
    Route::middleware(['role:student'])->group(function () {
        Route::get('/profile', [\App\Http\Controllers\Student\ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [\App\Http\Controllers\Student\ProfileController::class, 'update'])->name('profile.update');
        Route::get('/settings', [\App\Http\Controllers\Student\SettingsController::class, 'index'])->name('settings');
        Route::get('/notifications', [\App\Http\Controllers\Student\NotificationController::class, 'index'])->name('notifications');
        Route::get('/notifications/{notification}/go', [\App\Http\Controllers\Student\NotificationController::class, 'go'])
            ->name('notifications.go');
        Route::get('/notifications/{notification}', [\App\Http\Controllers\Student\NotificationController::class, 'show'])
            ->middleware(['ownership:user,user'])
            ->name('notifications.show');
        Route::post('/notifications/{notification}/mark-read', [\App\Http\Controllers\Student\NotificationController::class, 'markAsRead'])
            ->middleware(['ownership:user,user'])
            ->name('notifications.mark-read');
        Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Student\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::delete('/notifications/{notification}', [\App\Http\Controllers\Student\NotificationController::class, 'destroy'])
            ->middleware(['ownership:user,user'])
            ->name('notifications.destroy');
        Route::post('/notifications/cleanup', [\App\Http\Controllers\Student\NotificationController::class, 'cleanup'])->name('notifications.cleanup');
        Route::get('/api/notifications/unread-count', [\App\Http\Controllers\Student\NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
        Route::get('/api/notifications/recent', [\App\Http\Controllers\Student\NotificationController::class, 'getRecent'])->name('notifications.recent');
        Route::get('/calendar', [\App\Http\Controllers\Student\CalendarController::class, 'index'])->name('calendar');
        Route::get('/api/calendar/events', [\App\Http\Controllers\Student\CalendarController::class, 'getEvents'])->name('calendar.events');
        // البورتفوليو - مشاريع الطالب (مسار /my-portfolio لتفادي التعارض مع البورتفوليو العام /portfolio)
        Route::prefix('my-portfolio')->name('student.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Student\PortfolioProjectController::class, 'index'])->name('portfolio.index');
            Route::get('/create', [\App\Http\Controllers\Student\PortfolioProjectController::class, 'create'])->name('portfolio.create');
            Route::post('/', [\App\Http\Controllers\Student\PortfolioProjectController::class, 'store'])->name('portfolio.store');
        });
    });

    // لوحة الموظفين
    Route::prefix('employee')->name('employee.')->middleware(['auth'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Employee\EmployeeController::class, 'dashboard'])->name('dashboard');
        Route::get('/tasks', [\App\Http\Controllers\Employee\EmployeeTaskController::class, 'index'])->name('tasks.index');
        Route::get('/tasks/{task}', [\App\Http\Controllers\Employee\EmployeeTaskController::class, 'show'])->name('tasks.show');
        Route::put('/tasks/{task}/status', [\App\Http\Controllers\Employee\EmployeeTaskController::class, 'updateStatus'])->name('tasks.update-status');
        Route::post('/tasks/{task}/deliverables', [\App\Http\Controllers\Employee\EmployeeTaskController::class, 'submitDeliverable'])->name('tasks.submit-deliverable');
        
        // طلبات الإجازة
        Route::resource('leaves', \App\Http\Controllers\Employee\EmployeeLeaveController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
        
        // المحاسبة والراتب
        Route::get('/accounting', [\App\Http\Controllers\Employee\AccountingController::class, 'index'])->name('accounting.index');
        
        // اتفاقيات الموظف
        Route::get('/agreements', [\App\Http\Controllers\Employee\AgreementController::class, 'index'])->name('agreements.index');
        Route::get('/agreements/{agreement}', [\App\Http\Controllers\Employee\AgreementController::class, 'show'])->name('agreements.show');
        
        // الملف الشخصي
        Route::get('/profile', [\App\Http\Controllers\Employee\EmployeeProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [\App\Http\Controllers\Employee\EmployeeProfileController::class, 'update'])->name('profile.update');
        
        // الإشعارات
        Route::get('/notifications', [\App\Http\Controllers\Employee\EmployeeNotificationController::class, 'index'])->name('notifications');
        Route::get('/notifications/{notification}/go', [\App\Http\Controllers\Employee\EmployeeNotificationController::class, 'go'])->name('notifications.go');
        Route::get('/notifications/{notification}', [\App\Http\Controllers\Employee\EmployeeNotificationController::class, 'show'])->name('notifications.show');
        Route::post('/notifications/{notification}/mark-read', [\App\Http\Controllers\Employee\EmployeeNotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Employee\EmployeeNotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        
        // التقويم
        Route::get('/calendar', [\App\Http\Controllers\Employee\EmployeeCalendarController::class, 'index'])->name('calendar');
        Route::get('/api/calendar/events', [\App\Http\Controllers\Employee\EmployeeCalendarController::class, 'getEvents'])->name('calendar.events');
        
        // التقارير والإحصائيات
        Route::get('/reports', [\App\Http\Controllers\Employee\EmployeeReportController::class, 'index'])->name('reports');
        
        // الإعدادات
        Route::get('/settings', [\App\Http\Controllers\Employee\EmployeeSettingsController::class, 'index'])->name('settings');
        Route::put('/settings', [\App\Http\Controllers\Employee\EmployeeSettingsController::class, 'update'])->name('settings.update');
        
        // API للإشعارات
        Route::get('/api/notifications/unread', [\App\Http\Controllers\Employee\EmployeeNotificationController::class, 'getUnread'])->name('notifications.unread');
        Route::post('/api/notifications/{notification}/mark-read', [\App\Http\Controllers\Employee\EmployeeNotificationController::class, 'markAsRead'])->name('notifications.api.mark-read');
    });

    // مسارات الإدارة - محمية بالـ role middleware للإداريين فقط
    Route::prefix('admin')->name('admin.')->middleware(['role:admin|super_admin'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');

        // بروفايل الأدمن
        Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');

        // إدارة المستخدمين
        Route::get('/users', [\App\Http\Controllers\Admin\AdminController::class, 'users'])->name('users.index');
        Route::get('/users/create', [\App\Http\Controllers\Admin\AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [\App\Http\Controllers\Admin\AdminController::class, 'storeUser'])
            ->middleware('throttle:20,1')
            ->name('users.store');
        Route::get('/users/{id}/edit', [\App\Http\Controllers\Admin\AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'deleteUser'])->name('users.delete');
        
        // إدارة السنوات الدراسية
        Route::resource('academic-years', \App\Http\Controllers\Admin\AcademicYearController::class);
        Route::post('/academic-years/{academicYear}/toggle-status', [\App\Http\Controllers\Admin\AcademicYearController::class, 'toggleStatus'])->name('academic-years.toggle-status');
        Route::post('/academic-years/reorder', [\App\Http\Controllers\Admin\AcademicYearController::class, 'reorder'])->name('academic-years.reorder');
        Route::post('/academic-years/{academicYear}/add-course', [\App\Http\Controllers\Admin\AcademicYearController::class, 'addCourse'])->name('academic-years.add-course');
        Route::delete('/academic-years/{academicYear}/remove-course/{course}', [\App\Http\Controllers\Admin\AcademicYearController::class, 'removeCourse'])->name('academic-years.remove-course');
        Route::post('/academic-years/{academicYear}/add-instructor', [\App\Http\Controllers\Admin\AcademicYearController::class, 'addInstructor'])->name('academic-years.add-instructor');
        Route::delete('/academic-years/{academicYear}/remove-instructor/{instructor}', [\App\Http\Controllers\Admin\AcademicYearController::class, 'removeInstructor'])->name('academic-years.remove-instructor');

        // إدارة الكورسات والمدربين في المسارات التعليمية
        Route::prefix('learning-paths')->name('learning-paths.')->group(function () {
            // إدارة الكورسات
            Route::prefix('courses')->name('courses.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\LearningPathManagementController::class, 'coursesIndex'])->name('index');
                Route::get('/{academicYear}/manage', [\App\Http\Controllers\Admin\LearningPathManagementController::class, 'coursesManage'])->name('manage');
                Route::post('/{academicYear}', [\App\Http\Controllers\Admin\LearningPathManagementController::class, 'coursesStore'])->name('store');
                Route::delete('/{academicYear}/{course}', [\App\Http\Controllers\Admin\LearningPathManagementController::class, 'coursesDestroy'])->name('destroy');
                Route::post('/{academicYear}/update-order', [\App\Http\Controllers\Admin\LearningPathManagementController::class, 'coursesUpdateOrder'])->name('update-order');
            });
            
            // إدارة المدربين
            Route::prefix('instructors')->name('instructors.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\LearningPathManagementController::class, 'instructorsIndex'])->name('index');
                Route::get('/{academicYear}/manage', [\App\Http\Controllers\Admin\LearningPathManagementController::class, 'instructorsManage'])->name('manage');
                Route::post('/{academicYear}', [\App\Http\Controllers\Admin\LearningPathManagementController::class, 'instructorsStore'])->name('store');
                Route::delete('/{academicYear}/{instructor}', [\App\Http\Controllers\Admin\LearningPathManagementController::class, 'instructorsDestroy'])->name('destroy');
                Route::put('/{academicYear}/{instructor}/update-courses', [\App\Http\Controllers\Admin\LearningPathManagementController::class, 'instructorsUpdateCourses'])->name('update-courses');
            });
        });

        // إدارة المواد الدراسية
        Route::resource('academic-subjects', \App\Http\Controllers\Admin\AcademicSubjectController::class);
        Route::post('/academic-subjects/{academicSubject}/toggle-status', [\App\Http\Controllers\Admin\AcademicSubjectController::class, 'toggleStatus'])->name('academic-subjects.toggle-status');
        Route::post('/academic-subjects/reorder', [\App\Http\Controllers\Admin\AcademicSubjectController::class, 'reorder'])->name('academic-subjects.reorder');

        // إدارة الكورسات المتطورة
        Route::resource('advanced-courses', \App\Http\Controllers\Admin\AdvancedCourseController::class);
        Route::post('/advanced-courses/{advancedCourse}/activate-student', [\App\Http\Controllers\Admin\AdvancedCourseController::class, 'activateStudent'])->name('advanced-courses.activate-student');
        Route::get('/advanced-courses/{advancedCourse}/students', [\App\Http\Controllers\Admin\AdvancedCourseController::class, 'students'])->name('advanced-courses.students');
        Route::post('/advanced-courses/{advancedCourse}/toggle-status', [\App\Http\Controllers\Admin\AdvancedCourseController::class, 'toggleStatus'])->name('advanced-courses.toggle-status');
        Route::post('/advanced-courses/{advancedCourse}/toggle-featured', [\App\Http\Controllers\Admin\AdvancedCourseController::class, 'toggleFeatured'])->name('advanced-courses.toggle-featured');
        Route::get('/advanced-courses/{advancedCourse}/orders', [\App\Http\Controllers\Admin\AdvancedCourseController::class, 'orders'])->name('advanced-courses.orders');
        Route::get('/advanced-courses/{advancedCourse}/statistics', [\App\Http\Controllers\Admin\AdvancedCourseController::class, 'statistics'])->name('advanced-courses.statistics');
        Route::post('/advanced-courses/{advancedCourse}/duplicate', [\App\Http\Controllers\Admin\AdvancedCourseController::class, 'duplicate'])->name('advanced-courses.duplicate');
        Route::get('/get-subjects-by-year', [\App\Http\Controllers\Admin\AdvancedCourseController::class, 'getSubjectsByYear'])->name('advanced-courses.get-subjects-by-year');
        Route::get('/courses/{course}/lessons-list', function(\App\Models\AdvancedCourse $course) {
            return response()->json($course->lessons()->active()->select('id', 'title')->get());
        });

        // إدارة دروس الكورسات
        Route::prefix('courses/{course}/lessons')->name('courses.lessons.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\CourseLessonController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\CourseLessonController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\CourseLessonController::class, 'store'])->name('store');
            Route::get('/{lesson}', [\App\Http\Controllers\Admin\CourseLessonController::class, 'show'])->name('show');
            Route::get('/{lesson}/edit', [\App\Http\Controllers\Admin\CourseLessonController::class, 'edit'])->name('edit');
            Route::put('/{lesson}', [\App\Http\Controllers\Admin\CourseLessonController::class, 'update'])->name('update');
            Route::delete('/{lesson}', [\App\Http\Controllers\Admin\CourseLessonController::class, 'destroy'])->name('destroy');
            Route::post('/{lesson}/toggle-status', [\App\Http\Controllers\Admin\CourseLessonController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/reorder', [\App\Http\Controllers\Admin\CourseLessonController::class, 'reorder'])->name('reorder');
        });

        // إدارة بنك الأسئلة
        Route::prefix('question-bank')->name('question-bank.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\QuestionBankController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\QuestionBankController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\QuestionBankController::class, 'store'])->name('store');
            Route::get('/{question}', [\App\Http\Controllers\Admin\QuestionBankController::class, 'show'])->name('show');
            Route::get('/{question}/edit', [\App\Http\Controllers\Admin\QuestionBankController::class, 'edit'])->name('edit');
            Route::put('/{question}', [\App\Http\Controllers\Admin\QuestionBankController::class, 'update'])->name('update');
            Route::delete('/{question}', [\App\Http\Controllers\Admin\QuestionBankController::class, 'destroy'])->name('destroy');
            Route::post('/{question}/duplicate', [\App\Http\Controllers\Admin\QuestionBankController::class, 'duplicate'])->name('duplicate');
            Route::post('/export', [\App\Http\Controllers\Admin\QuestionBankController::class, 'export'])->name('export');
            Route::post('/import', [\App\Http\Controllers\Admin\QuestionBankController::class, 'import'])->name('import');
        });

        // إدارة تصنيفات الأسئلة
        Route::prefix('question-categories')->name('question-categories.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'store'])->name('store');
            Route::get('/{questionCategory}', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'show'])->name('show');
            Route::get('/{questionCategory}/edit', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'edit'])->name('edit');
            Route::put('/{questionCategory}', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'update'])->name('update');
            Route::delete('/{questionCategory}', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'destroy'])->name('destroy');
            Route::post('/reorder', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'reorder'])->name('reorder');
            Route::get('/subjects-by-year/{year}', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'getSubjectsByYear'])->name('subjects-by-year');
        });

        // إدارة الامتحانات
        Route::prefix('exams')->name('exams.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ExamController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\ExamController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\ExamController::class, 'store'])->name('store');
            Route::get('/{exam}', [\App\Http\Controllers\Admin\ExamController::class, 'show'])->name('show');
            Route::get('/{exam}/edit', [\App\Http\Controllers\Admin\ExamController::class, 'edit'])->name('edit');
            Route::put('/{exam}', [\App\Http\Controllers\Admin\ExamController::class, 'update'])->name('update');
            Route::delete('/{exam}', [\App\Http\Controllers\Admin\ExamController::class, 'destroy'])->name('destroy');
            Route::get('/{exam}/questions', [\App\Http\Controllers\Admin\ExamController::class, 'manageQuestions'])->name('questions.manage');
            Route::post('/{exam}/questions', [\App\Http\Controllers\Admin\ExamController::class, 'addQuestion'])->name('questions.add');
            Route::delete('/{exam}/questions/{examQuestion}', [\App\Http\Controllers\Admin\ExamController::class, 'removeQuestion'])->name('questions.remove');
            Route::post('/{exam}/questions/reorder', [\App\Http\Controllers\Admin\ExamController::class, 'reorderQuestions'])->name('questions.reorder');
            Route::post('/{exam}/toggle-publish', [\App\Http\Controllers\Admin\ExamController::class, 'togglePublish'])->name('toggle-publish');
            Route::post('/{exam}/toggle-status', [\App\Http\Controllers\Admin\ExamController::class, 'toggleStatus'])->name('toggle-status');
            Route::get('/{exam}/statistics', [\App\Http\Controllers\Admin\ExamController::class, 'statistics'])->name('statistics');
            Route::get('/{exam}/preview', [\App\Http\Controllers\Admin\ExamController::class, 'preview'])->name('preview');
            Route::post('/{exam}/duplicate', [\App\Http\Controllers\Admin\ExamController::class, 'duplicate'])->name('duplicate');
        });

        // إدارة المواد الدراسية القديمة
        Route::resource('subjects', \App\Http\Controllers\Admin\SubjectController::class);

        // إدارة الكورسات القديمة
        Route::resource('courses', \App\Http\Controllers\Admin\CourseController::class);

        // سجل النشاطات
        Route::get('/activity-log', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-log');
        Route::get('/activity-log/{activityLog}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('activity-log.show');
        Route::post('/activity-log/clear', [\App\Http\Controllers\Admin\ActivityLogController::class, 'destroy'])->name('activity-log.destroy');

        // الإحصائيات
        Route::get('/statistics', [\App\Http\Controllers\Admin\StatisticsController::class, 'index'])->name('statistics.index');
        Route::get('/statistics/users', [\App\Http\Controllers\Admin\StatisticsController::class, 'users'])->name('statistics.users');
        Route::get('/statistics/courses', [\App\Http\Controllers\Admin\StatisticsController::class, 'courses'])->name('statistics.courses');

        // إدارة الطلبات
        Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/approve', [\App\Http\Controllers\Admin\OrderController::class, 'approve'])
            ->middleware('throttle:10,1')
            ->name('orders.approve');
        Route::post('/orders/{order}/reject', [\App\Http\Controllers\Admin\OrderController::class, 'reject'])
            ->middleware('throttle:10,1')
            ->name('orders.reject');

        // إدارة الصلاحيات والأدوار
        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
        Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class);
        Route::post('/roles/{role}/permissions', [\App\Http\Controllers\Admin\RoleController::class, 'updatePermissions'])->name('roles.update-permissions');
        
        // إدارة صلاحيات المستخدمين
        Route::get('/user-permissions', [\App\Http\Controllers\Admin\UserPermissionController::class, 'index'])->name('user-permissions.index');
        Route::get('/user-permissions/{user}', [\App\Http\Controllers\Admin\UserPermissionController::class, 'show'])->name('user-permissions.show');
        Route::put('/user-permissions/{user}', [\App\Http\Controllers\Admin\UserPermissionController::class, 'update'])->name('user-permissions.update');
        Route::post('/user-permissions/{user}/attach', [\App\Http\Controllers\Admin\UserPermissionController::class, 'attachPermission'])->name('user-permissions.attach');
        Route::post('/user-permissions/{user}/detach', [\App\Http\Controllers\Admin\UserPermissionController::class, 'detachPermission'])->name('user-permissions.detach');

        // إدارة المحافظ الذكية
        Route::resource('wallets', \App\Http\Controllers\Admin\WalletController::class);
        Route::get('/wallets/{wallet}/transactions', [\App\Http\Controllers\Admin\WalletController::class, 'transactions'])->name('wallets.transactions');
        Route::get('/wallets/{wallet}/reports', [\App\Http\Controllers\Admin\WalletController::class, 'reports'])->name('wallets.reports');
        Route::post('/wallets/{wallet}/generate-report', [\App\Http\Controllers\Admin\WalletController::class, 'generateReport'])->name('wallets.generate-report');

        // إدارة المحاضرات والجروبات
        Route::resource('lectures', \App\Http\Controllers\Admin\LectureController::class);
        Route::post('/lectures/{lecture}/sync-teams-attendance', [\App\Http\Controllers\Admin\LectureController::class, 'syncTeamsAttendance'])->name('lectures.sync-teams-attendance');
        Route::resource('groups', \App\Http\Controllers\Admin\GroupController::class);
        Route::post('/groups/{group}/members', [\App\Http\Controllers\Admin\GroupController::class, 'addMember'])->name('groups.add-member');
        Route::delete('/groups/{group}/members/{member}', [\App\Http\Controllers\Admin\GroupController::class, 'removeMember'])->name('groups.remove-member');

        // إدارة الواجبات والمشاريع
        Route::resource('assignments', \App\Http\Controllers\Admin\AssignmentController::class);
        Route::get('/assignments/{assignment}/submissions', [\App\Http\Controllers\Admin\AssignmentController::class, 'submissions'])->name('assignments.submissions');
        Route::post('/assignments/{assignment}/grade/{submission}', [\App\Http\Controllers\Admin\AssignmentController::class, 'grade'])->name('assignments.grade');

        // إدارة المهام
        Route::resource('tasks', \App\Http\Controllers\Admin\TaskController::class);
        Route::post('/tasks/{task}/complete', [\App\Http\Controllers\Admin\TaskController::class, 'complete'])->name('tasks.complete');
        Route::post('/tasks/{task}/comments', [\App\Http\Controllers\Admin\TaskController::class, 'addComment'])->name('tasks.add-comment');
        Route::post('/tasks/{task}/deliverables/{deliverable}/review', [\App\Http\Controllers\Admin\TaskController::class, 'reviewDeliverable'])->name('tasks.review-deliverable');

        // إدارة الصفحات الخارجية
        Route::resource('blog', \App\Http\Controllers\Admin\BlogController::class);
        // البورتفوليو - الرقابة والجودة (الأدمن يرى الكل ويمكنه إخفاء مشروع)
        Route::get('portfolio', [\App\Http\Controllers\Admin\PortfolioController::class, 'index'])->name('portfolio.index');
        Route::get('portfolio/{project}', [\App\Http\Controllers\Admin\PortfolioController::class, 'show'])->name('portfolio.show');
        Route::post('portfolio/{project}/toggle-visibility', [\App\Http\Controllers\Admin\PortfolioController::class, 'toggleVisibility'])->name('portfolio.toggle-visibility');
        Route::resource('contact-messages', \App\Http\Controllers\Admin\ContactMessageController::class);
        Route::post('/contact-messages/{contactMessage}/mark-as-read', [\App\Http\Controllers\Admin\ContactMessageController::class, 'markAsRead'])->name('contact-messages.mark-as-read');
        Route::post('/contact-messages/{contactMessage}/mark-as-unread', [\App\Http\Controllers\Admin\ContactMessageController::class, 'markAsUnread'])->name('contact-messages.mark-as-unread');
        
        // إدارة الأسعار والباقات
        Route::resource('packages', \App\Http\Controllers\Admin\PackageController::class);
        Route::post('/packages/{course}/update-price', [\App\Http\Controllers\Admin\PackageController::class, 'updatePrice'])->name('packages.update-price');
        Route::post('/packages/bulk-update', [\App\Http\Controllers\Admin\PackageController::class, 'updateBulkPrices'])->name('packages.bulk-update');

        // إدارة الإشعارات
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\NotificationController::class, 'store'])
                ->middleware('throttle:20,5')
                ->name('store');
            Route::get('/{notification}', [\App\Http\Controllers\Admin\NotificationController::class, 'show'])->name('show');
            Route::delete('/{notification}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])
                ->middleware('throttle:30,1')
                ->name('destroy');
            Route::post('/quick-send', [\App\Http\Controllers\Admin\NotificationController::class, 'quickSend'])
                ->middleware('throttle:30,5')
                ->name('quick-send');
            Route::get('/target-count', [\App\Http\Controllers\Admin\NotificationController::class, 'getTargetCount'])
                ->middleware('throttle:60,1')
                ->name('target-count');
            Route::post('/mark-all-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])
                ->middleware('throttle:10,1')
                ->name('mark-all-read');
            Route::post('/cleanup', [\App\Http\Controllers\Admin\NotificationController::class, 'cleanup'])
                ->middleware('throttle:5,10')
                ->name('cleanup');
            Route::get('/statistics', [\App\Http\Controllers\Admin\NotificationController::class, 'statistics'])->name('statistics');
        });

        // إشعارات الموظفين
        Route::prefix('employee-notifications')->name('employee-notifications.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\EmployeeNotificationController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\EmployeeNotificationController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\EmployeeNotificationController::class, 'store'])
                ->middleware('throttle:10,1')
                ->name('store');
            Route::get('/{notification}', [\App\Http\Controllers\Admin\EmployeeNotificationController::class, 'show'])->name('show');
        });

        // إدارة تسجيل الطلاب في الكورسات الأونلاين
        Route::prefix('online-enrollments')->name('online-enrollments.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'store'])->name('store');
            Route::get('/{enrollment}', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'show'])->name('show');
            Route::post('/{enrollment}/activate', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'activate'])->name('activate');
            Route::post('/{enrollment}/deactivate', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'deactivate'])->name('deactivate');
            Route::post('/{enrollment}/update-progress', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'updateProgress'])->name('update-progress');
            Route::post('/{enrollment}/update-notes', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'updateNotes'])->name('update-notes');
            Route::delete('/{enrollment}', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'destroy'])->name('destroy');
            Route::get('/search/by-phone', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'searchStudentByPhone'])->name('search-by-phone');
        });

        // إدارة تسجيل الطلاب في الكورسات الأوفلاين
        Route::prefix('offline-enrollments')->name('offline-enrollments.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\OfflineEnrollmentsController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\OfflineEnrollmentsController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\OfflineEnrollmentsController::class, 'store'])->name('store');
            Route::get('/{offlineEnrollment}', [\App\Http\Controllers\Admin\OfflineEnrollmentsController::class, 'show'])->name('show');
            Route::put('/{offlineEnrollment}/status', [\App\Http\Controllers\Admin\OfflineEnrollmentsController::class, 'updateStatus'])->name('update-status');
            Route::delete('/{offlineEnrollment}', [\App\Http\Controllers\Admin\OfflineEnrollmentsController::class, 'destroy'])->name('destroy');
        });

        // إدارة تسجيل الطلاب في المسارات التعليمية
        Route::prefix('learning-path-enrollments')->name('learning-path-enrollments.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\LearningPathEnrollmentController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\LearningPathEnrollmentController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\LearningPathEnrollmentController::class, 'store'])->name('store');
            Route::post('/{enrollment}/toggle-status', [\App\Http\Controllers\Admin\LearningPathEnrollmentController::class, 'toggleStatus'])->name('toggle-status');
            Route::delete('/{enrollment}', [\App\Http\Controllers\Admin\LearningPathEnrollmentController::class, 'destroy'])->name('destroy');
        });

        // إدارة الأماكن للأوفلاين
        Route::resource('offline-locations', \App\Http\Controllers\Admin\OfflineLocationController::class);

        // إدارة الكورسات الأوفلاين
        Route::resource('offline-courses', \App\Http\Controllers\Admin\OfflineCourseController::class);
        
        // إدارة المجموعات للكورسات الأوفلاين
        Route::prefix('offline-courses/{offlineCourse}/groups')->name('offline-courses.groups.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\OfflineGroupController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\OfflineGroupController::class, 'store'])->name('store');
            Route::put('/{group}', [\App\Http\Controllers\Admin\OfflineGroupController::class, 'update'])->name('update');
            Route::delete('/{group}', [\App\Http\Controllers\Admin\OfflineGroupController::class, 'destroy'])->name('destroy');
        });

        // إدارة التسجيلات في الكورسات الأوفلاين
        Route::prefix('offline-courses/{offlineCourse}/enrollments')->name('offline-courses.enrollments.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\OfflineEnrollmentController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\OfflineEnrollmentController::class, 'store'])->name('store');
            Route::put('/{enrollment}/status', [\App\Http\Controllers\Admin\OfflineEnrollmentController::class, 'updateStatus'])->name('update-status');
            Route::delete('/{enrollment}', [\App\Http\Controllers\Admin\OfflineEnrollmentController::class, 'destroy'])->name('destroy');
        });

        // إدارة الأنشطة الأوفلاين
        Route::prefix('offline-courses/{offlineCourse}/activities')->name('offline-courses.activities.')->group(function () {
            // Route::get('/', [\App\Http\Controllers\Admin\OfflineActivityController::class, 'index'])->name('index');
            // Route::get('/create', [\App\Http\Controllers\Admin\OfflineActivityController::class, 'create'])->name('create');
            // Route::post('/', [\App\Http\Controllers\Admin\OfflineActivityController::class, 'store'])->name('store');
            // Route::get('/{activity}', [\App\Http\Controllers\Admin\OfflineActivityController::class, 'show'])->name('show');
            // Route::get('/{activity}/edit', [\App\Http\Controllers\Admin\OfflineActivityController::class, 'edit'])->name('edit');
            // Route::put('/{activity}', [\App\Http\Controllers\Admin\OfflineActivityController::class, 'update'])->name('update');
            // Route::delete('/{activity}', [\App\Http\Controllers\Admin\OfflineActivityController::class, 'destroy'])->name('destroy');
        });

        // إدارة اتفاقيات المدربين للأوفلاين
        Route::prefix('offline-agreements')->name('offline-agreements.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\OfflineAgreementController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\OfflineAgreementController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\OfflineAgreementController::class, 'store'])->name('store');
            Route::get('/{agreement}', [\App\Http\Controllers\Admin\OfflineAgreementController::class, 'show'])->name('show');
            Route::get('/{agreement}/edit', [\App\Http\Controllers\Admin\OfflineAgreementController::class, 'edit'])->name('edit');
            Route::put('/{agreement}', [\App\Http\Controllers\Admin\OfflineAgreementController::class, 'update'])->name('update');
            Route::delete('/{agreement}', [\App\Http\Controllers\Admin\OfflineAgreementController::class, 'destroy'])->name('destroy');
        });

        // إدارة الموظفين
        Route::resource('employees', \App\Http\Controllers\Admin\EmployeeController::class);
        Route::resource('employee-jobs', \App\Http\Controllers\Admin\EmployeeJobController::class);
        Route::resource('employee-tasks', \App\Http\Controllers\Admin\EmployeeTaskController::class);
        
        // إدارة الإجازات
        Route::get('/leaves', [\App\Http\Controllers\Admin\AdminLeaveController::class, 'index'])->name('leaves.index');
        Route::get('/leaves/{leave}', [\App\Http\Controllers\Admin\AdminLeaveController::class, 'show'])->name('leaves.show');
        Route::post('/leaves/{leave}/approve', [\App\Http\Controllers\Admin\AdminLeaveController::class, 'approve'])->name('leaves.approve');
        Route::post('/leaves/{leave}/reject', [\App\Http\Controllers\Admin\AdminLeaveController::class, 'reject'])->name('leaves.reject');

        // طلبات المدربين للإدارة
        Route::prefix('instructor-requests')->name('instructor-requests.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\InstructorRequestController::class, 'index'])->name('index');
            Route::get('/{instructorRequest}', [\App\Http\Controllers\Admin\InstructorRequestController::class, 'show'])->name('show');
            Route::post('/{instructorRequest}/respond', [\App\Http\Controllers\Admin\InstructorRequestController::class, 'respond'])->name('respond');
        });

        // الرقابة والجودة
        Route::prefix('quality-control')->name('quality-control.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\QualityControlController::class, 'index'])->name('index');
            Route::get('/students', [\App\Http\Controllers\Admin\QualityControlController::class, 'students'])->name('students');
            Route::get('/instructors', [\App\Http\Controllers\Admin\QualityControlController::class, 'instructors'])->name('instructors');
            Route::get('/employees', [\App\Http\Controllers\Admin\QualityControlController::class, 'employees'])->name('employees');
            Route::get('/operations', [\App\Http\Controllers\Admin\QualityControlController::class, 'operations'])->name('operations');
        });

        // إدارة الرسائل والتقارير
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\MessagesController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\MessagesController::class, 'create'])->name('create');
            Route::post('/send-single', [\App\Http\Controllers\Admin\MessagesController::class, 'sendSingle'])->name('send-single');
            Route::post('/send-bulk', [\App\Http\Controllers\Admin\MessagesController::class, 'sendBulk'])->name('send-bulk');
            Route::get('/{message}', [\App\Http\Controllers\Admin\MessagesController::class, 'show'])->name('show');
            Route::post('/{message}/resend', [\App\Http\Controllers\Admin\MessagesController::class, 'resend'])->name('resend');
            Route::delete('/{message}', [\App\Http\Controllers\Admin\MessagesController::class, 'destroy'])->name('destroy');
            
            // التقارير الشهرية
            Route::get('/monthly-reports', [\App\Http\Controllers\Admin\MessagesController::class, 'monthlyReports'])->name('monthly-reports');
            Route::post('/generate-monthly-reports', [\App\Http\Controllers\Admin\MessagesController::class, 'generateMonthlyReports'])->name('generate-monthly-reports');
            
            // قوالب الرسائل
            Route::get('/templates', [\App\Http\Controllers\Admin\MessagesController::class, 'templates'])->name('templates');
            Route::post('/templates', [\App\Http\Controllers\Admin\MessagesController::class, 'storeTemplate'])->name('templates.store');
            Route::delete('/templates/{template}', [\App\Http\Controllers\Admin\MessagesController::class, 'destroyTemplate'])->name('templates.destroy');
            
            // إعدادات WhatsApp API
            Route::get('/settings', [\App\Http\Controllers\Admin\WhatsAppSettingsController::class, 'settings'])->name('settings');
            Route::post('/save-api-settings', [\App\Http\Controllers\Admin\WhatsAppSettingsController::class, 'saveApiSettings'])->name('save-api-settings');
            Route::post('/test-api', [\App\Http\Controllers\Admin\WhatsAppSettingsController::class, 'testApi'])->name('test-api');
        });

        // إدارة المحاسبة
        Route::resource('invoices', \App\Http\Controllers\Admin\InvoiceController::class)
            ->middleware('throttle:60,1')
            ->except(['update', 'destroy']);
        Route::post('/invoices/{invoice}', [\App\Http\Controllers\Admin\InvoiceController::class, 'update'])->middleware('throttle:20,5')->name('invoices.update');
        Route::delete('/invoices/{invoice}', [\App\Http\Controllers\Admin\InvoiceController::class, 'destroy'])->middleware('throttle:10,1')->name('invoices.destroy');
        
        Route::resource('payments', \App\Http\Controllers\Admin\PaymentController::class)
            ->middleware('throttle:60,1');
        Route::post('/payments/{payment}', [\App\Http\Controllers\Admin\PaymentController::class, 'update'])->middleware('throttle:20,5')->name('payments.update');
        Route::delete('/payments/{payment}', [\App\Http\Controllers\Admin\PaymentController::class, 'destroy'])->middleware('throttle:10,1')->name('payments.destroy');
        
        Route::resource('transactions', \App\Http\Controllers\Admin\TransactionController::class)
            ->middleware('throttle:60,1');
        Route::post('/transactions/{transaction}', [\App\Http\Controllers\Admin\TransactionController::class, 'update'])->middleware('throttle:20,5')->name('transactions.update');
        Route::delete('/transactions/{transaction}', [\App\Http\Controllers\Admin\TransactionController::class, 'destroy'])->middleware('throttle:10,1')->name('transactions.destroy');
        
        Route::resource('wallets', \App\Http\Controllers\Admin\WalletController::class)
            ->middleware('throttle:60,1');
        Route::post('/wallets/{wallet}', [\App\Http\Controllers\Admin\WalletController::class, 'update'])->middleware('throttle:20,5')->name('wallets.update');
        Route::delete('/wallets/{wallet}', [\App\Http\Controllers\Admin\WalletController::class, 'destroy'])->middleware('throttle:10,1')->name('wallets.destroy');
        
        Route::resource('expenses', \App\Http\Controllers\Admin\ExpenseController::class)->except(['destroy']);
        Route::post('/expenses/{expense}/approve', [\App\Http\Controllers\Admin\ExpenseController::class, 'approve'])->middleware('throttle:10,1')->name('expenses.approve');
        Route::post('/expenses/{expense}/reject', [\App\Http\Controllers\Admin\ExpenseController::class, 'reject'])->middleware('throttle:10,1')->name('expenses.reject');
        Route::post('/expenses/{expense}', [\App\Http\Controllers\Admin\ExpenseController::class, 'update'])->middleware('throttle:20,5')->name('expenses.update');
        Route::delete('/expenses/{expense}', [\App\Http\Controllers\Admin\ExpenseController::class, 'destroy'])->middleware('throttle:10,1')->name('expenses.destroy');
        
        Route::resource('subscriptions', \App\Http\Controllers\Admin\SubscriptionController::class)
            ->middleware('throttle:60,1');
        Route::post('/subscriptions/{subscription}', [\App\Http\Controllers\Admin\SubscriptionController::class, 'update'])->middleware('throttle:20,5')->name('subscriptions.update');
        Route::delete('/subscriptions/{subscription}', [\App\Http\Controllers\Admin\SubscriptionController::class, 'destroy'])->middleware('throttle:10,1')->name('subscriptions.destroy');
        Route::get('/accounting/reports', [\App\Http\Controllers\Admin\AccountingReportsController::class, 'index'])->name('accounting.reports');
        Route::get('/accounting/reports/export', [\App\Http\Controllers\Admin\AccountingReportsController::class, 'export'])->name('accounting.reports.export');

        // الماليات الخاصة بالمدربين (قائمة المدربين ثم المطلوب دفعه لكل مدرب)
        Route::prefix('salaries')->name('salaries.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SalaryController::class, 'index'])->name('index');
            Route::get('/instructor/{instructor}', [\App\Http\Controllers\Admin\SalaryController::class, 'instructor'])->name('instructor');
            Route::post('/instructor/{instructor}/pay-now/{agreement}', [\App\Http\Controllers\Admin\SalaryController::class, 'payNowFromAgreement'])->name('pay-now-from-agreement');
            Route::get('/pay/{payment}', [\App\Http\Controllers\Admin\SalaryController::class, 'pay'])->name('pay');
            Route::post('/pay/{payment}', [\App\Http\Controllers\Admin\SalaryController::class, 'markPaid'])->name('mark-paid');
        });

        // التقارير الشاملة
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('index');
            Route::get('/users', [\App\Http\Controllers\Admin\ReportsController::class, 'users'])->name('users');
            Route::get('/courses', [\App\Http\Controllers\Admin\ReportsController::class, 'courses'])->name('courses');
            Route::get('/financial', [\App\Http\Controllers\Admin\ReportsController::class, 'financial'])->name('financial');
            Route::get('/academic', [\App\Http\Controllers\Admin\ReportsController::class, 'academic'])->name('academic');
            Route::get('/activities', [\App\Http\Controllers\Admin\ReportsController::class, 'activities'])->name('activities');
            Route::get('/comprehensive', [\App\Http\Controllers\Admin\ReportsController::class, 'comprehensive'])->name('comprehensive');
            
            // تصدير التقارير
            Route::get('/export/users', [\App\Http\Controllers\Admin\ReportsController::class, 'exportUsers'])
                ->middleware('throttle:10,5')
                ->name('export.users');
            Route::get('/export/courses', [\App\Http\Controllers\Admin\ReportsController::class, 'exportCourses'])
                ->middleware('throttle:10,5')
                ->name('export.courses');
            Route::get('/export/financial', [\App\Http\Controllers\Admin\ReportsController::class, 'exportFinancial'])
                ->middleware('throttle:10,5')
                ->name('export.financial');
            Route::get('/export/comprehensive', [\App\Http\Controllers\Admin\ReportsController::class, 'exportComprehensive'])
                ->middleware('throttle:5,10')
                ->name('export.comprehensive');
        });
        Route::prefix('installments')->name('installments.')->group(function () {
            Route::resource('plans', \App\Http\Controllers\Admin\InstallmentPlanController::class);
            Route::resource('agreements', \App\Http\Controllers\Admin\InstallmentAgreementController::class);
            Route::post('/agreements/payments/{payment}/mark', [\App\Http\Controllers\Admin\InstallmentAgreementController::class, 'markPayment'])
                ->name('agreements.mark-payment');
        });

        // نظام الاتفاقيات للمدربين
        Route::prefix('agreements')->name('agreements.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\InstructorAgreementController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\InstructorAgreementController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\InstructorAgreementController::class, 'store'])
                ->middleware('throttle:20,5')
                ->name('store');
            Route::get('/{agreement}', [\App\Http\Controllers\Admin\InstructorAgreementController::class, 'show'])->name('show');
            Route::get('/{agreement}/edit', [\App\Http\Controllers\Admin\InstructorAgreementController::class, 'edit'])->name('edit');
            Route::put('/{agreement}', [\App\Http\Controllers\Admin\InstructorAgreementController::class, 'update'])
                ->middleware('throttle:20,5')
                ->name('update');
            Route::delete('/{agreement}', [\App\Http\Controllers\Admin\InstructorAgreementController::class, 'destroy'])
                ->middleware('throttle:10,1')
                ->name('destroy');
        });

        // نظام اتفاقيات الموظفين
        Route::prefix('employee-agreements')->name('employee-agreements.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\EmployeeAgreementController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\EmployeeAgreementController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\EmployeeAgreementController::class, 'store'])
                ->middleware('throttle:20,5')
                ->name('store');
            Route::get('/{employeeAgreement}', [\App\Http\Controllers\Admin\EmployeeAgreementController::class, 'show'])->name('show');
            Route::get('/{employeeAgreement}/edit', [\App\Http\Controllers\Admin\EmployeeAgreementController::class, 'edit'])->name('edit');
            Route::put('/{employeeAgreement}', [\App\Http\Controllers\Admin\EmployeeAgreementController::class, 'update'])
                ->middleware('throttle:20,5')
                ->name('update');
            Route::delete('/{employeeAgreement}', [\App\Http\Controllers\Admin\EmployeeAgreementController::class, 'destroy'])
                ->middleware('throttle:10,1')
                ->name('destroy');
        });
        
        // إدارة طلبات السحب للمدربين
        Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\WithdrawalRequestController::class, 'index'])->name('index');
            Route::get('/{withdrawal}', [\App\Http\Controllers\Admin\WithdrawalRequestController::class, 'show'])->name('show');
            Route::post('/{withdrawal}/approve', [\App\Http\Controllers\Admin\WithdrawalRequestController::class, 'approve'])
                ->middleware('throttle:10,1')
                ->name('approve');
            Route::post('/{withdrawal}/reject', [\App\Http\Controllers\Admin\WithdrawalRequestController::class, 'reject'])
                ->middleware('throttle:10,1')
                ->name('reject');
            Route::post('/{withdrawal}/complete', [\App\Http\Controllers\Admin\WithdrawalRequestController::class, 'complete'])
                ->middleware('throttle:10,1')
                ->name('complete');
        });

        // إدارة التسويق
        Route::resource('coupons', \App\Http\Controllers\Admin\CouponController::class);
        // إدارة برامج الإحالات
        Route::resource('referral-programs', \App\Http\Controllers\Admin\ReferralProgramController::class);
        
        // إدارة الإحالات
        Route::prefix('referrals')->name('referrals.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ReferralController::class, 'index'])->name('index');
            Route::get('/{referral}', [\App\Http\Controllers\Admin\ReferralController::class, 'show'])->name('show');
        });
        Route::prefix('loyalty')->name('loyalty.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\LoyaltyController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\LoyaltyController::class, 'store'])->name('store');
            Route::get('/{loyaltyProgram}', [\App\Http\Controllers\Admin\LoyaltyController::class, 'show'])->name('show');
            Route::put('/{loyaltyProgram}', [\App\Http\Controllers\Admin\LoyaltyController::class, 'update'])->name('update');
        });

        // إدارة الشهادات والإنجازات
        Route::resource('certificates', \App\Http\Controllers\Admin\CertificateController::class);
        Route::resource('achievements', \App\Http\Controllers\Admin\AchievementController::class);
        Route::resource('badges', \App\Http\Controllers\Admin\BadgeController::class);
        Route::resource('reviews', \App\Http\Controllers\Admin\ReviewController::class);

        // إدارة المحاضرات
        Route::resource('lectures', \App\Http\Controllers\Admin\LectureController::class);
        Route::get('/lectures/course/{course}', [\App\Http\Controllers\Admin\LectureController::class, 'index'])->name('lectures.by-course');

        // إدارة الحضور
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('index');
            Route::get('/lecture/{lecture}', [\App\Http\Controllers\Admin\AttendanceController::class, 'showLectureAttendance'])->name('lecture');
            Route::post('/lecture/{lecture}/upload-teams', [\App\Http\Controllers\Admin\AttendanceController::class, 'uploadTeamsFile'])->name('upload-teams');
        });

        // إدارة المجموعات
        Route::resource('groups', \App\Http\Controllers\Admin\GroupController::class);

        // إدارة الأداء
        Route::prefix('performance')->name('performance.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\PerformanceController::class, 'index'])->name('index');
            Route::post('/clear-cache', [\App\Http\Controllers\Admin\PerformanceController::class, 'clearCache'])
                ->middleware('throttle:10,1')
                ->name('clear-cache');
            Route::post('/optimize-cache', [\App\Http\Controllers\Admin\PerformanceController::class, 'optimizeCache'])
                ->middleware('throttle:5,5')
                ->name('optimize-cache');
            Route::post('/clear-temp-files', [\App\Http\Controllers\Admin\PerformanceController::class, 'clearTempFiles'])
                ->middleware('throttle:5,5')
                ->name('clear-temp-files');
            Route::post('/optimize-database', [\App\Http\Controllers\Admin\PerformanceController::class, 'optimizeDatabase'])
                ->middleware('throttle:3,10')
                ->name('optimize-database');
        });

    });

    // المهام (للجميع)
    Route::resource('tasks', \App\Http\Controllers\TaskController::class);

    // مسارات الطلاب - محمية للطلاب فقط
    Route::prefix('student')->name('student.')->middleware(['role:student'])->group(function () {
        Route::resource('invoices', \App\Http\Controllers\Student\InvoiceController::class)->only(['index', 'show']);
        Route::resource('wallet', \App\Http\Controllers\Student\WalletController::class)->only(['index', 'show']);
        Route::resource('certificates', \App\Http\Controllers\Student\CertificateController::class)->only(['index', 'show']);
        Route::resource('achievements', \App\Http\Controllers\Student\AchievementController::class)->only(['index', 'show']);
        Route::resource('assignments', \App\Http\Controllers\Student\AssignmentController::class)->only(['index', 'show']);
        Route::post('/assignments/{assignment}/submit', [\App\Http\Controllers\Student\AssignmentController::class, 'submit'])
            ->middleware(['ownership:assignment,assignment'])
            ->name('assignments.submit');
        Route::resource('tasks', \App\Http\Controllers\Student\TaskController::class);
        // مجموعاتي: عرض المجموعات والمحادثة والتسليمات
        Route::get('groups', [\App\Http\Controllers\Student\GroupController::class, 'index'])->name('groups.index');
        Route::get('groups/{group}/messages', [\App\Http\Controllers\Student\GroupController::class, 'getMessages'])->name('groups.messages.index');
        Route::get('groups/{group}/assignments', [\App\Http\Controllers\Student\GroupController::class, 'assignments'])->name('groups.assignments.index');
        Route::get('groups/{group}', [\App\Http\Controllers\Student\GroupController::class, 'show'])->name('groups.show');
        Route::post('groups/{group}/messages', [\App\Http\Controllers\Student\GroupController::class, 'storeMessage'])->name('groups.messages.store');
        Route::post('groups/{group}/assignments/{assignment}/submit', [\App\Http\Controllers\Student\GroupController::class, 'submitAssignment'])->name('groups.assignments.submit');
    });

    // مسارات المدرسين
    Route::prefix('instructor')->name('instructor.')->middleware(['auth', 'role:instructor|teacher'])->group(function () {
        // بروفايل المدرب
        Route::get('/profile', [\App\Http\Controllers\Instructor\ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [\App\Http\Controllers\Instructor\ProfileController::class, 'update'])->name('profile.update');

        Route::resource('courses', \App\Http\Controllers\Instructor\CourseController::class)->only(['index', 'show']);
        Route::resource('offline-courses', \App\Http\Controllers\Instructor\OfflineCourseController::class)->only(['index', 'show'])->parameters(['offline_course' => 'offlineCourse']);
        // موارد ومحاضرات وأنشطة الكورسات الأوفلاين (واجهات منفصلة عن الأونلاين)
        Route::prefix('offline-courses/{offlineCourse}')->name('offline-courses.')->group(function () {
            Route::resource('resources', \App\Http\Controllers\Instructor\OfflineResourceController::class)->except(['show'])->parameters(['resource' => 'resource']);
            Route::resource('lectures', \App\Http\Controllers\Instructor\OfflineLectureController::class)->parameters(['lecture' => 'lecture']);
            Route::resource('activities', \App\Http\Controllers\Instructor\OfflineActivityController::class)->parameters(['activity' => 'activity']);
            Route::post('activities/{activity}/submissions/{submission}/grade', [\App\Http\Controllers\Instructor\OfflineActivityController::class, 'gradeSubmission'])->name('activities.submissions.grade');
        });
        Route::get('courses/{course}/curriculum', [\App\Http\Controllers\Instructor\CurriculumController::class, 'index'])->name('courses.curriculum');
        Route::post('courses/{course}/curriculum/exams', [\App\Http\Controllers\Instructor\CurriculumController::class, 'storeExamFromCurriculum'])->name('courses.curriculum.exams.store');
        Route::post('courses/{course}/curriculum/assignments', [\App\Http\Controllers\Instructor\CurriculumController::class, 'storeAssignmentFromCurriculum'])->name('courses.curriculum.assignments.store');
        Route::post('courses/{course}/sections', [\App\Http\Controllers\Instructor\CurriculumController::class, 'storeSection'])->name('courses.sections.store');
        Route::put('sections/{section}', [\App\Http\Controllers\Instructor\CurriculumController::class, 'updateSection'])->name('sections.update');
        Route::delete('sections/{section}', [\App\Http\Controllers\Instructor\CurriculumController::class, 'destroySection'])->name('sections.destroy');
        Route::post('sections/{section}/items', [\App\Http\Controllers\Instructor\CurriculumController::class, 'addItem'])->name('sections.items.store');
        Route::delete('curriculum-items/{item}', [\App\Http\Controllers\Instructor\CurriculumController::class, 'removeItem'])->name('curriculum-items.destroy');
        Route::post('courses/{course}/sections/order', [\App\Http\Controllers\Instructor\CurriculumController::class, 'updateSectionsOrder'])->name('courses.sections.order');
        Route::post('sections/{section}/items/order', [\App\Http\Controllers\Instructor\CurriculumController::class, 'updateItemsOrder'])->name('sections.items.order');
        
        // تم إلغاء نظام الدروس — الاعتماد على المحاضرات فقط (إعادة توجيه الروابط القديمة)
        Route::prefix('courses/{course}/lessons')->name('courses.lessons.')->group(function () {
            Route::get('/', fn($course) => redirect()->route('instructor.courses.curriculum', $course))->name('index');
            Route::get('/create', fn($course) => redirect()->route('instructor.lectures.index'))->name('create');
            Route::post('/', fn($course) => redirect()->route('instructor.courses.curriculum', $course)->with('info', 'تم إلغاء نظام الدروس؛ استخدم المحاضرات.'))->name('store');
            Route::get('/{lesson}', fn($course) => redirect()->route('instructor.lectures.index'))->name('show');
            Route::get('/{lesson}/edit', fn($course) => redirect()->route('instructor.lectures.index'))->name('edit');
            Route::put('/{lesson}', fn($course) => redirect()->route('instructor.courses.curriculum', $course))->name('update');
            Route::delete('/{lesson}', fn($course) => redirect()->route('instructor.courses.curriculum', $course))->name('destroy');
            Route::post('/{lesson}/toggle-status', fn($course) => redirect()->route('instructor.courses.curriculum', $course))->name('toggle-status');
            Route::post('/reorder', fn($course) => redirect()->route('instructor.courses.curriculum', $course))->name('reorder');
        });

        Route::get('/api/courses/{course}/lessons-list', fn($course) => response()->json([]));
        
        // أنماط التعلم التفاعلية
        Route::get('courses/{course}/learning-patterns', [\App\Http\Controllers\Instructor\LearningPatternController::class, 'index'])->name('learning-patterns.index');
        Route::get('courses/{course}/learning-patterns/create', [\App\Http\Controllers\Instructor\LearningPatternController::class, 'create'])->name('learning-patterns.create');
        Route::post('courses/{course}/learning-patterns', [\App\Http\Controllers\Instructor\LearningPatternController::class, 'store'])->name('learning-patterns.store');
        Route::get('courses/{course}/learning-patterns/{pattern}', [\App\Http\Controllers\Instructor\LearningPatternController::class, 'show'])->name('learning-patterns.show');
        Route::get('courses/{course}/learning-patterns/{pattern}/edit', [\App\Http\Controllers\Instructor\LearningPatternController::class, 'edit'])->name('learning-patterns.edit');
        Route::put('courses/{course}/learning-patterns/{pattern}', [\App\Http\Controllers\Instructor\LearningPatternController::class, 'update'])->name('learning-patterns.update');
        Route::delete('courses/{course}/learning-patterns/{pattern}', [\App\Http\Controllers\Instructor\LearningPatternController::class, 'destroy'])->name('learning-patterns.destroy');
        Route::delete('courses/{course}/learning-patterns/{pattern}/attempts/{attempt}', [\App\Http\Controllers\Instructor\LearningPatternController::class, 'destroyAttempt'])->name('learning-patterns.attempts.destroy');
        
        // API لدروس الكورس للمدرب
        Route::resource('lectures', \App\Http\Controllers\Instructor\LectureController::class);
        Route::post('/lectures/{lecture}/sync-teams-attendance', [\App\Http\Controllers\Instructor\LectureController::class, 'syncTeamsAttendance'])->name('lectures.sync-teams-attendance');
        Route::post('/lectures/{lecture}/update-attendance', [\App\Http\Controllers\Instructor\LectureController::class, 'updateAttendance'])->name('lectures.update-attendance');
        
        // المسار التعليمي للمدرب
        Route::get('/learning-path', [\App\Http\Controllers\Instructor\LearningPathController::class, 'index'])->name('learning-path.index');
        Route::get('/learning-path/{slug}', [\App\Http\Controllers\Instructor\LearningPathController::class, 'show'])->name('learning-path.show');
        Route::post('/lectures/{lecture}/update-status', [\App\Http\Controllers\Instructor\LectureController::class, 'updateStatus'])->name('lectures.update-status');
        Route::resource('groups', \App\Http\Controllers\Instructor\GroupController::class);
        Route::post('/groups/{group}/add-member', [\App\Http\Controllers\Instructor\GroupController::class, 'addMember'])->name('groups.add-member');
        Route::delete('/groups/{group}/remove-member', [\App\Http\Controllers\Instructor\GroupController::class, 'removeMember'])->name('groups.remove-member');
        Route::resource('assignments', \App\Http\Controllers\Instructor\AssignmentController::class);
        Route::get('/assignments/{assignment}/submissions', [\App\Http\Controllers\Instructor\AssignmentController::class, 'submissions'])->name('assignments.submissions');
        Route::post('/assignments/{assignment}/grade/{submission}', [\App\Http\Controllers\Instructor\AssignmentController::class, 'grade'])->name('assignments.grade');
        Route::resource('exams', \App\Http\Controllers\Instructor\ExamController::class);
        Route::get('exams/{exam}/questions', [\App\Http\Controllers\Instructor\ExamQuestionController::class, 'manage'])->name('exams.questions.manage');
        Route::post('exams/{exam}/questions/from-bank', [\App\Http\Controllers\Instructor\ExamQuestionController::class, 'addFromBank'])->name('exams.questions.add-from-bank');
        Route::post('exams/{exam}/questions/new', [\App\Http\Controllers\Instructor\ExamQuestionController::class, 'createNew'])->name('exams.questions.create-new');
        Route::delete('exams/{exam}/questions/{question}', [\App\Http\Controllers\Instructor\ExamQuestionController::class, 'remove'])->name('exams.questions.remove');
        Route::post('exams/{exam}/questions/reorder', [\App\Http\Controllers\Instructor\ExamQuestionController::class, 'reorder'])->name('exams.questions.reorder');
        
        // بنك الأسئلة
        Route::resource('question-banks', \App\Http\Controllers\Instructor\QuestionBankController::class);
        Route::post('question-banks/{questionBank}/questions', [\App\Http\Controllers\Instructor\QuestionController::class, 'store'])->name('question-banks.questions.store');
        Route::get('question-banks/{questionBank}/questions/create', [\App\Http\Controllers\Instructor\QuestionController::class, 'create'])->name('question-banks.questions.create');
        Route::resource('questions', \App\Http\Controllers\Instructor\QuestionController::class)->except(['create', 'store']);
        Route::get('/attendance', [\App\Http\Controllers\Instructor\AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/lecture/{lecture}', [\App\Http\Controllers\Instructor\AttendanceController::class, 'showLecture'])->name('attendance.lecture');
        Route::resource('tasks', \App\Http\Controllers\Instructor\TaskController::class);
        Route::get('/tasks/lectures', [\App\Http\Controllers\Instructor\TaskController::class, 'getLectures'])->name('tasks.lectures');
        Route::post('/tasks/{task}/deliverables', [\App\Http\Controllers\Instructor\TaskController::class, 'submitDeliverable'])->name('tasks.submit-deliverable');
        Route::put('/tasks/{task}/progress', [\App\Http\Controllers\Instructor\TaskController::class, 'updateProgress'])->name('tasks.update-progress');

        // تقديم طلبات للإدارة
        Route::prefix('management-requests')->name('management-requests.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Instructor\ManagementRequestController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Instructor\ManagementRequestController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Instructor\ManagementRequestController::class, 'store'])->name('store');
            Route::get('/{managementRequest}', [\App\Http\Controllers\Instructor\ManagementRequestController::class, 'show'])->name('show');
        });
        
        // نظام الاتفاقيات للمدرب
        Route::prefix('agreements')->name('agreements.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Instructor\AgreementController::class, 'index'])->name('index');
            Route::get('/{agreement}', [\App\Http\Controllers\Instructor\AgreementController::class, 'show'])->name('show');
        });

        // حساب التحويل (بيانات استلام المبالغ)
        Route::get('/transfer-account', [\App\Http\Controllers\Instructor\TransferAccountController::class, 'index'])->name('transfer-account.index');
        Route::post('/transfer-account', [\App\Http\Controllers\Instructor\TransferAccountController::class, 'store'])->name('transfer-account.store');
        
        // طلبات السحب للمدرب
        Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Instructor\WithdrawalRequestController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Instructor\WithdrawalRequestController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Instructor\WithdrawalRequestController::class, 'store'])->name('store');
            Route::get('/{withdrawal}', [\App\Http\Controllers\Instructor\WithdrawalRequestController::class, 'show'])->name('show');
            Route::post('/{withdrawal}/cancel', [\App\Http\Controllers\Instructor\WithdrawalRequestController::class, 'cancel'])->name('cancel');
        });

        // مراجعة مشاريع البورتفوليو (المدرب يراجع ثم ينشر)
        Route::prefix('portfolio')->name('portfolio.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Instructor\PortfolioReviewController::class, 'index'])->name('index');
            Route::get('/{project}', [\App\Http\Controllers\Instructor\PortfolioReviewController::class, 'show'])->name('show');
            Route::post('/{project}/approve', [\App\Http\Controllers\Instructor\PortfolioReviewController::class, 'approve'])->name('approve');
            Route::post('/{project}/reject', [\App\Http\Controllers\Instructor\PortfolioReviewController::class, 'reject'])->name('reject');
            Route::post('/{project}/publish', [\App\Http\Controllers\Instructor\PortfolioReviewController::class, 'publish'])->name('publish');
        });
    });
});
