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
Route::get('/webhooks/whatsapp', [\App\Http\Controllers\WhatsAppWebhookController::class, 'verify'])
    ->name('webhooks.whatsapp.verify')
    ->withoutMiddleware([
        \App\Http\Middleware\SetLocale::class,
        \App\Http\Middleware\InputSanitizationMiddleware::class,
        \App\Http\Middleware\FileUploadSecurityMiddleware::class,
        \App\Http\Middleware\EnsureTwoFactorEnabled::class,
        \App\Http\Middleware\CheckActiveStatus::class,
    ]);
Route::post('/webhooks/whatsapp', [\App\Http\Controllers\WhatsAppWebhookController::class, 'handle'])
    ->name('webhooks.whatsapp.handle')
    ->withoutMiddleware([
        \App\Http\Middleware\SetLocale::class,
        \App\Http\Middleware\InputSanitizationMiddleware::class,
        \App\Http\Middleware\FileUploadSecurityMiddleware::class,
        \App\Http\Middleware\EnsureTwoFactorEnabled::class,
        \App\Http\Middleware\CheckActiveStatus::class,
    ]);

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

// Careers (Public) — التوظيف
Route::get('/careers', [\App\Http\Controllers\CareersController::class, 'index'])->name('careers.index');
Route::get('/careers/{job}', [\App\Http\Controllers\CareersController::class, 'show'])->name('careers.show');
Route::get('/careers/{job}/apply', function (\App\Models\HrJobPosting $job) {
    abort_unless($job->is_published && $job->isOpen(), 404);

    return redirect()->route('careers.show', $job);
})->name('careers.apply.form');
Route::post('/careers/{job}/apply', [\App\Http\Controllers\CareersController::class, 'apply'])
    ->middleware('throttle:20,1')
    ->name('careers.apply');

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

// Favicon and Web App Manifest for stronger browser/search detection
Route::get('/favicon.ico', function () {
    return redirect(\App\Support\SiteBranding::faviconUrl());
})->name('favicon');

Route::get('/site.webmanifest', function () {
    $icon = \App\Support\SiteBranding::faviconUrl();
    $logo = \App\Support\SiteBranding::logoUrl();

    return response()->json([
        'name' => config('app.name', 'Mindlytics'),
        'short_name' => 'Mindlytics',
        'start_url' => url('/'),
        'display' => 'standalone',
        'background_color' => '#ffffff',
        'theme_color' => '#0ea5e9',
        'icons' => [
            ['src' => $icon, 'sizes' => '16x16', 'type' => 'image/png'],
            ['src' => $icon, 'sizes' => '32x32', 'type' => 'image/png'],
            ['src' => $logo, 'sizes' => '180x180', 'type' => 'image/png'],
            ['src' => $logo, 'sizes' => '512x512', 'type' => 'image/png'],
        ],
    ])->header('Content-Type', 'application/manifest+json');
})->name('site.webmanifest');

// الصفحة الرئيسية (Home) - الترجمة عبر SetLocale في مجموعة web
Route::get('/', [\App\Http\Controllers\Public\LandingController::class, 'index'])->name('home');

// الصفحات العامة
Route::get('/about', [\App\Http\Controllers\Public\PageController::class, 'about'])->name('public.about');
Route::get('/faq', [\App\Http\Controllers\Public\PageController::class, 'faq'])->name('public.faq');
Route::get('/terms', [\App\Http\Controllers\Public\PageController::class, 'terms'])->name('public.terms');
Route::get('/privacy', [\App\Http\Controllers\Public\PageController::class, 'privacy'])->name('public.privacy');
Route::get('/pricing', [\App\Http\Controllers\Public\PageController::class, 'pricing'])->name('public.pricing');
Route::get('/team', [\App\Http\Controllers\Public\PageController::class, 'team'])->name('public.team');
Route::get('/certificates', [\App\Http\Controllers\Public\PageController::class, 'certificates'])->name('public.certificates');
Route::get('/challenges', [\App\Http\Controllers\Public\ChallengesController::class, 'index'])->name('public.challenges');
Route::get('/certificates/verify', [\App\Http\Controllers\Public\CertificateVerificationController::class, 'verify'])->name('public.certificates.verify');
Route::get('/certificates/verify/{code}', [\App\Http\Controllers\Public\CertificateVerificationController::class, 'verify'])->name('public.certificates.verify.code');
Route::get('/help', [\App\Http\Controllers\Public\PageController::class, 'help'])->name('public.help');
Route::get('/refund', [\App\Http\Controllers\Public\PageController::class, 'refund'])->name('public.refund');
Route::get('/testimonials', [\App\Http\Controllers\Public\PageController::class, 'testimonials'])->name('public.testimonials');
Route::get('/events', [\App\Http\Controllers\Public\PageController::class, 'events'])->name('public.events');
Route::get('/partners', [\App\Http\Controllers\Public\PageController::class, 'partners'])->name('public.partners');
Route::prefix('investment')->name('investment.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Public\InvestmentController::class, 'index'])->name('index');
    Route::get('/{slug}', [\App\Http\Controllers\Public\InvestmentController::class, 'show'])->name('show');
    Route::post('/{slug}/apply', [\App\Http\Controllers\Public\InvestmentController::class, 'apply'])->name('apply');
});
Route::get('/groups', [\App\Http\Controllers\Public\PageController::class, 'groups'])->name('public.groups');
Route::get('/bookings', [\App\Http\Controllers\Public\PageController::class, 'bookings'])->middleware('auth')->name('public.bookings');

// المدونة
Route::get('/blog', [\App\Http\Controllers\Public\BlogController::class, 'index'])->name('public.blog.index');
Route::get('/blog/{slug}', [\App\Http\Controllers\Public\BlogController::class, 'show'])->name('public.blog.show');

// Mindlytics Portfolio (معرض أعمال الطلاب)
Route::get('/portfolio', [\App\Http\Controllers\Public\PortfolioController::class, 'index'])->name('public.portfolio.index');
Route::get('/portfolio/{id}', [\App\Http\Controllers\Public\PortfolioController::class, 'show'])->name('public.portfolio.show')->where('id', '[0-9]+');

// مجتمع البيانات والذكاء الاصطناعي (مسابقات، داتاسيت، مجتمع)
Route::get('/community', [\App\Http\Controllers\Public\CommunityController::class, 'index'])->name('public.community.index');

// مصادقة مجتمع البيانات (تسجيل دخول وإنشاء حساب منفصلان - نفس المستخدمين)
Route::prefix('community')->name('community.')->group(function () {
    // صفحة المساهمين عامة (بدون تسجيل دخول)
    Route::get('/contributors', [\App\Http\Controllers\Community\CommunityPageController::class, 'contributors'])->name('contributors.index');
    Route::get('/contributors/{user}', [\App\Http\Controllers\Community\CommunityPageController::class, 'contributorShow'])->name('contributors.show');
    // صفحة البيانات عامة (بدون تسجيل — استكشاف آلاف مجموعات البيانات)
    Route::get('/data', [\App\Http\Controllers\Community\CommunityPageController::class, 'publicDatasets'])->name('data.index');
    Route::get('/data/{dataset}', [\App\Http\Controllers\Community\CommunityPageController::class, 'publicDatasetShow'])->name('data.show');
    Route::get('/data/{dataset}/download-all', [\App\Http\Controllers\Community\CommunityPageController::class, 'datasetDownloadAll'])->name('data.download-all');
    Route::get('/data/{dataset}/download/{index}', [\App\Http\Controllers\Community\CommunityPageController::class, 'datasetDownloadFile'])->name('data.download-file')->whereNumber('index');
    Route::get('/data/{dataset}/download', [\App\Http\Controllers\Community\CommunityPageController::class, 'datasetDownload'])->name('data.download');
    Route::get('/data/{dataset}/preview', [\App\Http\Controllers\Community\CommunityPageController::class, 'datasetPreview'])->name('data.preview');
    Route::get('/data/{dataset}/preview-zip-entry', [\App\Http\Controllers\Community\CommunityPageController::class, 'datasetPreviewZipEntry'])->name('data.preview-zip-entry');
    Route::get('/data/{dataset}/zip-contents', [\App\Http\Controllers\Community\CommunityPageController::class, 'datasetZipContents'])->name('data.zip-contents');
    // صفحة النماذج (Model Zoo) عامة — بدون تسجيل (مثل البيانات)
    Route::get('/models', [\App\Http\Controllers\Community\CommunityPageController::class, 'publicModels'])->name('models.index');
    Route::get('/models/{model}', [\App\Http\Controllers\Community\CommunityPageController::class, 'publicModelShow'])->name('models.show');
    Route::get('/models/{model}/download', [\App\Http\Controllers\Community\CommunityPageController::class, 'publicModelDownload'])->name('models.download');
    Route::get('/models/{model}/download/{index}', [\App\Http\Controllers\Community\CommunityPageController::class, 'publicModelDownloadFile'])->name('models.download-file')->whereNumber('index');
    Route::get('/models/{model}/file/{index}/preview', [\App\Http\Controllers\Community\CommunityPageController::class, 'publicModelFilePreview'])->name('models.file-preview')->whereNumber('index');
    Route::middleware(['guest', 'guest-only'])->group(function () {
        Route::get('/login', [\App\Http\Controllers\Community\AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [\App\Http\Controllers\Community\AuthController::class, 'login'])->middleware('throttle:20,15')->name('login.post');
        Route::get('/register', [\App\Http\Controllers\Community\AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [\App\Http\Controllers\Community\AuthController::class, 'register'])->middleware('throttle:register')->name('register.post');
    });
    Route::middleware(['auth', 'prevent-concurrent'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Community\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/competitions', [\App\Http\Controllers\Community\CommunityPageController::class, 'competitions'])->name('competitions.index');
        Route::get('/datasets', [\App\Http\Controllers\Community\CommunityPageController::class, 'datasets'])->name('datasets.index');
        Route::get('/datasets/{dataset}', [\App\Http\Controllers\Community\CommunityPageController::class, 'datasetShow'])->name('datasets.show');
        Route::get('/datasets/{dataset}/download', [\App\Http\Controllers\Community\CommunityPageController::class, 'datasetDownload'])->name('datasets.download');
        Route::get('/discussions', [\App\Http\Controllers\Community\CommunityPageController::class, 'discussions'])->name('discussions.index');

        // لوحة تحكم المساهمين (للمستخدمين الذين is_community_contributor = true فقط)
        Route::middleware('community.contributor')->prefix('contributor')->name('contributor.')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Community\ContributorController::class, 'dashboard'])->name('dashboard');
            Route::get('/profile', [\App\Http\Controllers\Community\ContributorController::class, 'profileEdit'])->name('profile.edit');
            Route::post('/profile', [\App\Http\Controllers\Community\ContributorController::class, 'profileStore'])->name('profile.store');
            Route::get('/datasets', [\App\Http\Controllers\Community\ContributorController::class, 'datasets'])->name('datasets.index');
            Route::get('/datasets/create', [\App\Http\Controllers\Community\ContributorController::class, 'createDataset'])->name('datasets.create');
            Route::post('/datasets', [\App\Http\Controllers\Community\ContributorController::class, 'storeDataset'])->name('datasets.store');
            Route::get('/models', [\App\Http\Controllers\Community\ContributorController::class, 'models'])->name('models.index');
            Route::get('/models/create', [\App\Http\Controllers\Community\ContributorController::class, 'createModel'])->name('models.create');
            Route::post('/models', [\App\Http\Controllers\Community\ContributorController::class, 'storeModel'])->name('models.store');
        });
    });
});

// التواصل
Route::get('/contact', [\App\Http\Controllers\Public\ContactController::class, 'index'])->name('public.contact');
Route::post('/contact', [\App\Http\Controllers\Public\ContactController::class, 'store'])->name('public.contact.store');

// معرض الصور والفيديوهات
Route::get('/media', [\App\Http\Controllers\Public\MediaController::class, 'index'])->name('public.media.index');
Route::get('/media/{media}', [\App\Http\Controllers\Public\MediaController::class, 'show'])->name('public.media.show');

// صفحة الكورسات العامة
Route::get('/courses', function () {
    $coursesQuery = \App\Models\AdvancedCourse::where('is_active', true)
        ->visibleOnCurrentHost()
        ->publicCatalog();
    
    // جلب الكورسات مع العلاقات
    $coursesCollection = $coursesQuery
        ->with(['academicSubject', 'academicYear'])
        ->withCount('lectures')
        ->orderBy('is_featured', 'desc')
        ->orderBy('created_at', 'desc')
        ->get();
    
    // تحويل البيانات إلى array
    $courses = $coursesCollection->map(function ($course) {
        return [
            'id' => $course->id,
            'title' => $course->localized('title') ?: 'بدون عنوان',
            'description' => $course->localized('description') ?? '',
            'level' => $course->level ?? 'beginner',
            'price' => $course->effectivePrice(),
            'original_price' => $course->originalPrice(),
            'discount_amount' => $course->courseDiscountAmount(),
            'duration_hours' => (int)($course->duration_hours ?? 0),
            'is_featured' => (bool)($course->is_featured ?? false),
            'lectures_count' => (int)($course->lectures_count ?? 0),
            'thumbnail' => $course->thumbnail ?? null,
            'academic_subject' => $course->academicSubject ? [
                'name' => $course->academicSubject->name ?? 'غير محدد'
            ] : null,
        ];
    })->values()->toArray();
    
    // جلب الباقات النشطة (تظهر فقط إن وُجد بها كورس نشط يخص الفرع الحالي عند وجود فرع)
    $packages = \App\Models\Package::active()
        ->visibleOnCurrentHost()
        ->whereHas('courses', function ($q) {
            $q->where('advanced_courses.is_active', true)
                ->visibleOnCurrentHost()
                ->publicCatalog();
        })
        ->with(['courses' => function($query) {
            $query->where('is_active', true)->visibleOnCurrentHost()->publicCatalog();
        }])
        ->withCount([
            'courses' => function ($q) {
                $q->where('advanced_courses.is_active', true)
                    ->visibleOnCurrentHost()
                    ->publicCatalog();
            },
        ])
        ->orderBy('is_featured', 'desc')
        ->orderBy('is_popular', 'desc')
        ->orderBy('order')
        ->get();
    
    return view('courses', compact('courses', 'packages'));
})->name('public.courses');

// صفحة المدربين (الملفات التعريفية المعتمدة)
Route::get('/instructors', [\App\Http\Controllers\Public\InstructorController::class, 'index'])->name('public.instructors.index');
Route::get('/instructors/{instructor}', [\App\Http\Controllers\Public\InstructorController::class, 'show'])->name('public.instructors.show');

// روابط الفيديو المحمية (موقّعة — لا تُعرَض الروابط الحقيقية في الصفحة)
Route::get('/v/watch', [\App\Http\Controllers\ProtectedVideoController::class, 'watch'])
    ->name('video.protected.watch')
    ->middleware('signed');
Route::get('/course/{courseId}/preview-watch-url/{lessonId}', [\App\Http\Controllers\ProtectedVideoController::class, 'getPreviewWatchUrl'])
    ->name('video.preview.watch-url');

// صفحة تفاصيل الكورس العامة
Route::get('/course/{id}', function ($id) {
    $course = \App\Models\AdvancedCourse::where('id', $id)
        ->where('is_active', true)
        ->publicCatalog()
        ->visibleOnCurrentHost()
        ->with(['academicSubject', 'academicYear', 'instructor'])
        ->withCount('lessons')
        ->firstOrFail();

    // التقسيمات (عناوين فقط للمعاينة قبل الشراء)
    $sections = \App\Models\CourseSection::where('advanced_course_id', $course->id)
        ->where('is_active', true)
        ->whereNull('parent_id')
        ->orderBy('order')
        ->get(['id', 'title', 'order']);

    // أول 3 فيديوهات للمعاينة (رابط التضمين يُمرَّر مشفّراً base64 لعدم ظهور الرابط الحقيقي في المصدر)
    $previewVideoLessons = \App\Models\CourseLesson::where('advanced_course_id', $course->id)
        ->where('is_active', true)
        ->where('type', 'video')
        ->orderBy('order')
        ->limit(3)
        ->get(['id', 'title', 'duration_minutes', 'video_url', 'order']);

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
        ->visibleOnCurrentHost()
        ->publicCatalog()
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

    $approvedReviews = \App\Models\CourseReview::query()
        ->where('course_id', $course->id)
        ->where('is_approved', true)
        ->with('user')
        ->latest()
        ->limit(20)
        ->get();

    $reviewsAvg = (float) (\App\Models\CourseReview::query()
        ->where('course_id', $course->id)
        ->where('is_approved', true)
        ->avg('rating') ?? 0);
    $reviewsCount = (int) \App\Models\CourseReview::query()
        ->where('course_id', $course->id)
        ->where('is_approved', true)
        ->count();

    $mindMapSteps = $course->mind_map_steps;
    $courseMindMapVisible = $course->mind_map_published
        && is_array($mindMapSteps)
        && count($mindMapSteps) >= 2;

    return view('course-show', compact(
        'course',
        'relatedCourses',
        'isEnrolled',
        'sections',
        'previewVideoLessons',
        'approvedReviews',
        'reviewsAvg',
        'reviewsCount',
        'courseMindMapVisible'
    ));
})->name('public.course.show');

Route::get('/course/{course}/mind-map', [\App\Http\Controllers\Public\CourseMindMapController::class, 'show'])
    ->name('public.course.mind-map');

Route::post('/course/{courseId}/reviews', [\App\Http\Controllers\Public\PublicReviewController::class, 'storeCourse'])
    ->middleware('auth')
    ->name('public.course.reviews.store');

// صفحة إتمام الطلب (Checkout)
Route::get('/course/{courseId}/checkout', [\App\Http\Controllers\Public\CheckoutController::class, 'show'])
    ->middleware('auth')
    ->name('public.course.checkout');

Route::post('/course/{courseId}/checkout/complete', [\App\Http\Controllers\Public\CheckoutController::class, 'complete'])
    ->middleware('auth')
    ->name('public.course.checkout.complete');

// التوجيه لبوابة الدفع كاشير (كورس)
Route::post('/course/{courseId}/checkout/kashier', [\App\Http\Controllers\Public\CheckoutController::class, 'redirectToKashier'])
    ->middleware('auth')
    ->name('public.course.checkout.kashier');

// فواتيرك (iframe): تجهيز الإضافة + بروكسي السكربت
Route::get('/js/checkout-pay-widget.v1.js', \App\Http\Controllers\Public\FawaterkPluginController::class)
    ->name('public.fawaterk.plugin');
Route::post('/course/{courseId}/checkout/fawaterak/prepare', [\App\Http\Controllers\Public\CheckoutController::class, 'fawaterakPrepare'])
    ->middleware('auth')
    ->name('public.course.checkout.fawaterak.prepare');
Route::get('/checkout/fawaterak/{status}', [\App\Http\Controllers\Public\CheckoutController::class, 'fawaterakReturn'])
    ->middleware('auth')
    ->where('status', 'success|fail|pending')
    ->name('public.checkout.fawaterak.return');

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

Route::post('/learning-path/{slug}/reviews', [\App\Http\Controllers\Public\PublicReviewController::class, 'storeLearningPath'])
    ->middleware('auth')
    ->where('slug', '[a-z0-9-]+')
    ->name('public.learning-path.reviews.store');

// صفحة إتمام الطلب للمسارات التعليمية (Checkout)
Route::get('/learning-path/{slug}/checkout', [\App\Http\Controllers\Public\CheckoutController::class, 'showLearningPath'])
    ->middleware('auth')
    ->name('public.learning-path.checkout');

Route::post('/learning-path/{slug}/checkout/complete', [\App\Http\Controllers\Public\CheckoutController::class, 'completeLearningPath'])
    ->middleware('auth')
    ->name('public.learning-path.checkout.complete');

// التوجيه لبوابة الدفع كاشير (مسار تعليمي)
Route::post('/learning-path/{slug}/checkout/kashier', [\App\Http\Controllers\Public\CheckoutController::class, 'redirectToKashierLearningPath'])
    ->middleware('auth')
    ->name('public.learning-path.checkout.kashier');

// صفحة حجز ورش العمل العامة (workshops)
Route::get('/workshops/{slug}', [\App\Http\Controllers\Public\WorkshopPublicController::class, 'show'])
    ->name('public.workshops.show');
Route::post('/workshops/{slug}/register', [\App\Http\Controllers\Public\WorkshopPublicController::class, 'register'])
    ->name('public.workshops.register');
Route::get('/workshops/{slug}/confirm', [\App\Http\Controllers\Public\WorkshopPublicController::class, 'showConfirm'])
    ->name('public.workshops.confirm.show');
Route::post('/workshops/{slug}/confirm', [\App\Http\Controllers\Public\WorkshopPublicController::class, 'confirmAttendance'])
    ->name('public.workshops.confirm.store');

// حجز مجموعة كورس أوفلاين (رابط عام — تصميم مشابه لصفحة الدفع)
Route::get('/offline-groups/{slug}', [\App\Http\Controllers\Public\OfflineGroupPublicBookingController::class, 'show'])
    ->where('slug', '[a-z0-9-]+')
    ->name('public.offline-groups.show');
Route::post('/offline-groups/{slug}/book', [\App\Http\Controllers\Public\OfflineGroupPublicBookingController::class, 'store'])
    ->middleware('auth')
    ->where('slug', '[a-z0-9-]+')
    ->name('public.offline-groups.book');

// حجز مجموعة كورس أونلاين (موازي للأوفلاين وبفصل كامل في المقاعد والحجوزات)
Route::get('/online-groups/{slug}', [\App\Http\Controllers\Public\OfflineGroupPublicBookingController::class, 'showOnline'])
    ->where('slug', '[a-z0-9-]+')
    ->name('public.online-groups.show');
Route::post('/online-groups/{slug}/book', [\App\Http\Controllers\Public\OfflineGroupPublicBookingController::class, 'storeOnline'])
    ->middleware('auth')
    ->where('slug', '[a-z0-9-]+')
    ->name('public.online-groups.book');

// استقبال العودة من بوابة كاشير بعد الدفع (بدون auth لأن كاشير يوجّه المتصفح هنا)
Route::get('/checkout/kashier/callback', [\App\Http\Controllers\Public\CheckoutController::class, 'kashierCallback'])
    ->name('public.checkout.kashier.callback');

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
        ->visibleOnCurrentHost()
        ->whereHas('courses', function ($q) {
            $q->where('advanced_courses.is_active', true)->visibleOnCurrentHost();
        })
        ->with(['courses' => function($query) {
            $query->where('is_active', true)
                  ->visibleOnCurrentHost()
                  ->with(['academicSubject', 'academicYear'])
                  ->withCount('lessons');
        }])
        ->firstOrFail();
    
    // باقات ذات صلة
    $relatedPackages = \App\Models\Package::where('is_active', true)
        ->visibleOnCurrentHost()
        ->where('id', '!=', $package->id)
        ->whereHas('courses', function ($q) {
            $q->where('advanced_courses.is_active', true)->visibleOnCurrentHost();
        })
        ->withCount([
            'courses' => function ($q) {
                $q->where('advanced_courses.is_active', true)->visibleOnCurrentHost();
            },
        ])
        ->limit(3)
        ->get();
    
    return view('package-show', compact('package', 'relatedPackages'));
})->name('public.package.show');

// مسارات المصادقة - محمية بحيث لا يمكن الوصول إليها إذا كان المستخدم مسجل دخول
Route::middleware(['guest', 'guest-only'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register');
    // نسيت كلمة المرور: طلب رابط إعادة التعيين + صفحة تعيين كلمة مرور جديدة
    Route::get('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware('throttle:password-reset')->name('password.email');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->middleware('throttle:password-reset')->name('password.update');
});

// المنح الدراسية — تسجيل عام برابط خاص
Route::prefix('scholarships')->name('scholarships.')->group(function () {
    Route::get('{slug}', [\App\Http\Controllers\Public\ScholarshipRegistrationController::class, 'landing'])->name('show');
    Route::get('{slug}/register', [\App\Http\Controllers\Public\ScholarshipRegistrationController::class, 'show'])->name('register');
    Route::post('{slug}/register', [\App\Http\Controllers\Public\ScholarshipRegistrationController::class, 'register'])
        ->middleware('throttle:register')
        ->name('register.post');
});

// المصادقة الثنائية (2FA) - بعد إدخال البريد وكلمة المرور للمدربين/الإدمن/الموظفين
Route::middleware(['web', 'throttle:60,5'])->group(function () {
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

    // لوحة مدير الفرع (بيانات الفرع المرتبط بحسابه فقط)
    Route::prefix('branch-office')->name('branch.office.')->middleware(['role:branch_manager', 'branch.office'])->group(function () {
        Route::get('/', function () {
            return redirect()->route('branch.office.dashboard');
        });
        Route::get('/dashboard', [\App\Http\Controllers\Branch\BranchDashboardController::class, 'index'])->name('dashboard');
        Route::get('/users', [\App\Http\Controllers\Branch\BranchUsersController::class, 'index'])->name('users');
        Route::get('/orders', [\App\Http\Controllers\Branch\BranchOrdersController::class, 'index'])->name('orders');
        Route::get('/courses-online', [\App\Http\Controllers\Branch\BranchOnlineCoursesController::class, 'index'])->name('courses-online');
        Route::get('/courses-offline', [\App\Http\Controllers\Branch\BranchOfflineCoursesController::class, 'index'])->name('courses-offline');
        Route::get('/learning-paths', [\App\Http\Controllers\Branch\BranchLearningPathsController::class, 'index'])->name('learning-paths');
        Route::get('/invoices', [\App\Http\Controllers\Branch\BranchInvoicesController::class, 'index'])->name('invoices');
        Route::get('/payments', [\App\Http\Controllers\Branch\BranchPaymentsController::class, 'index'])->name('payments');
    });

    // لوحة مدير المكان الإداري (تسجيل الساعات والمخالصة الشهرية)
    Route::prefix('place-office')->name('place.office.')->middleware(['role:place_manager', 'place.office'])->group(function () {
        Route::get('/', fn () => redirect()->route('place.office.dashboard'));
        Route::get('/dashboard', [\App\Http\Controllers\Place\PlaceDashboardController::class, 'index'])->name('dashboard');
        Route::get('/usage-logs', [\App\Http\Controllers\Place\PlaceUsageLogController::class, 'index'])->name('usage-logs.index');
        Route::get('/usage-logs/create', [\App\Http\Controllers\Place\PlaceUsageLogController::class, 'create'])->name('usage-logs.create');
        Route::post('/usage-logs', [\App\Http\Controllers\Place\PlaceUsageLogController::class, 'store'])->name('usage-logs.store');
        Route::get('/settlements', [\App\Http\Controllers\Place\PlaceSettlementController::class, 'index'])->name('settlements.index');
        Route::get('/settlements/{settlement}', [\App\Http\Controllers\Place\PlaceSettlementController::class, 'show'])->name('settlements.show');
        Route::post('/settlements/{settlement}/submit', [\App\Http\Controllers\Place\PlaceSettlementController::class, 'submit'])->name('settlements.submit');
        Route::get('/invoices', [\App\Http\Controllers\Place\PlaceInvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [\App\Http\Controllers\Place\PlaceInvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/profile', [\App\Http\Controllers\Place\PlaceProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [\App\Http\Controllers\Place\PlaceProfileController::class, 'update'])->name('profile.update');
    });

    // API لإشعارات الناف بار (تعمل للطالب/المدرب/الموظف)
    Route::prefix('api/nav-notifications')->name('nav-notifications.')->group(function () {
        Route::get('/unread-count', [\App\Http\Controllers\NavbarNotificationController::class, 'unreadCount'])->name('unread-count');
        Route::get('/recent', [\App\Http\Controllers\NavbarNotificationController::class, 'recent'])->name('recent');
        Route::post('/{notification}/mark-read', [\App\Http\Controllers\NavbarNotificationController::class, 'markRead'])->name('mark-read');
        Route::post('/mark-all-read', [\App\Http\Controllers\NavbarNotificationController::class, 'markAllRead'])->name('mark-all-read');
    });
    
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
            
            // الكورسات الأوفلاين للطلاب (مسار مستقل عن الأونلاين)
            Route::prefix('offline-courses')->name('student.offline-courses.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Student\OfflineCourseController::class, 'index'])->name('index');
                Route::get('/booking/catalog', function () {
                    return redirect()
                        ->route('student.offline-courses.index')
                        ->with('info', 'تم إيقاف صفحة كتالوج الحجز العام. للتسجيل تواصل مع الإدارة أو استخدم رابط الحجز المباشر إن وُجد.');
                });
                Route::get('/{offlineCourse}/booking/create', [\App\Http\Controllers\Student\OfflineCourseBookingController::class, 'create'])->name('booking.create');
                Route::post('/{offlineCourse}/booking', [\App\Http\Controllers\Student\OfflineCourseBookingController::class, 'store'])->name('booking.store');
                Route::get('/{offlineCourse}/curriculum', [\App\Http\Controllers\Student\OfflineCourseController::class, 'curriculum'])->name('curriculum');
                Route::get('/{offlineCourse}/schedule', [\App\Http\Controllers\Student\OfflineCourseController::class, 'schedule'])->name('schedule');
                Route::get('/{offlineCourse}/resources', [\App\Http\Controllers\Student\OfflineCourseController::class, 'resources'])->name('resources');
                Route::get('/{offlineCourse}/lectures', [\App\Http\Controllers\Student\OfflineCourseController::class, 'lectures'])->name('lectures');
                Route::get('/{offlineCourse}/lectures/{lecture}/watch', [\App\Http\Controllers\Student\OfflineCourseController::class, 'watchLectureRecording'])->name('lectures.watch');
                Route::get('/{offlineCourse}/activities/{activity}', [\App\Http\Controllers\Student\OfflineCourseController::class, 'activityShow'])->name('activities.show');
                Route::post('/{offlineCourse}/activities/{activity}/submit', [\App\Http\Controllers\Student\OfflineCourseController::class, 'activitySubmit'])->name('activities.submit');
                Route::get('/{offlineCourse}', [\App\Http\Controllers\Student\OfflineCourseController::class, 'show'])->name('show');
            });

            // كورسات الأونلاين (مجموعات أونلاين) — مسار وقائمة منفصلان؛ الظهور بعد تفعيل «بوابة الطالب» من الإدارة
            Route::prefix('online-courses')->name('student.online-courses.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Student\OfflineCourseController::class, 'index'])->name('index');
                Route::get('/{offlineCourse}/curriculum', [\App\Http\Controllers\Student\OfflineCourseController::class, 'curriculum'])->name('curriculum');
                Route::get('/{offlineCourse}/schedule', [\App\Http\Controllers\Student\OfflineCourseController::class, 'schedule'])->name('schedule');
                Route::get('/{offlineCourse}/resources', [\App\Http\Controllers\Student\OfflineCourseController::class, 'resources'])->name('resources');
                Route::get('/{offlineCourse}/lectures', [\App\Http\Controllers\Student\OfflineCourseController::class, 'lectures'])->name('lectures');
                Route::get('/{offlineCourse}/lectures/{lecture}/watch', [\App\Http\Controllers\Student\OfflineCourseController::class, 'watchLectureRecording'])->name('lectures.watch');
                Route::get('/{offlineCourse}/activities/{activity}', [\App\Http\Controllers\Student\OfflineCourseController::class, 'activityShow'])->name('activities.show');
                Route::post('/{offlineCourse}/activities/{activity}/submit', [\App\Http\Controllers\Student\OfflineCourseController::class, 'activitySubmit'])->name('activities.submit');
                Route::get('/{offlineCourse}', [\App\Http\Controllers\Student\OfflineCourseController::class, 'show'])->name('show');
            });

            // المسار التعليمي للطالب
            Route::get('/student/learning-path/{slug}', [\App\Http\Controllers\Student\LearningPathController::class, 'show'])->name('student.learning-path.show');
        Route::get('/my-courses/{course}/learn', [\App\Http\Controllers\Student\MyCourseController::class, 'learn'])
            ->middleware(['ownership:course,course'])
            ->name('my-courses.learn');
        Route::get('/my-courses/{course}/lectures/{lecture}', [\App\Http\Controllers\Student\MyCourseController::class, 'getLectureData'])
            ->middleware(['ownership:course,course'])
            ->name('my-courses.lectures.show');
        Route::get('/my-courses/{course}/lectures/{lecture}/materials/{material}/download', [\App\Http\Controllers\Student\MyCourseController::class, 'downloadLectureMaterial'])
            ->middleware(['ownership:course,course'])
            ->name('my-courses.lectures.material.download');
        Route::post('/my-courses/{course}/lectures/{lecture}/video-questions/{videoQuestion}/answer', [\App\Http\Controllers\Student\MyCourseController::class, 'submitLectureVideoQuestionAnswer'])
            ->middleware(['ownership:course,course'])
            ->name('my-courses.lectures.video-question.answer');
        Route::post('/my-courses/{course}/lectures/{lecture}/progress', [\App\Http\Controllers\Student\MyCourseController::class, 'updateLectureProgress'])
            ->middleware(['ownership:course,course'])
            ->name('my-courses.lectures.progress');
        Route::get('/my-courses/{course}/lessons/{lesson}/watch', [\App\Http\Controllers\Student\MyCourseController::class, 'watchLesson'])
            ->middleware([\App\Http\Middleware\VideoProtectionMiddleware::class, 'ownership:course,course'])
            ->name('my-courses.lesson.watch');
        Route::post('/my-courses/{course}/lessons/{lesson}/progress', [\App\Http\Controllers\Student\MyCourseController::class, 'updateLessonProgress'])
            ->middleware(['ownership:course,course'])
            ->name('my-courses.lesson.progress');
        Route::get('/my-courses/{course}/curriculum/locks', [\App\Http\Controllers\Student\MyCourseController::class, 'curriculumLocks'])
            ->middleware(['ownership:course,course'])
            ->name('my-courses.curriculum.locks');
        
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
    Route::post('/promo-code/activate', [\App\Http\Controllers\Student\WorkshopPromoController::class, 'activate'])
        ->middleware('auth')
        ->name('promo-code.activate');
    
    // API للتحقق من الكوبون
    Route::post('/api/validate-coupon', [\App\Http\Controllers\Student\CouponController::class, 'validateCoupon'])->name('api.validate-coupon');
    
    // API لمعلومات الفيديو
    Route::post('/api/video/info', [\App\Http\Controllers\Api\VideoInfoController::class, 'getInfo'])->name('api.video.info');
    
    // API للدروس - محمية بالتأكد من التسجيل
    Route::get('/api/lessons/{lesson}', function(\App\Models\CourseLesson $lesson) {
        $user = auth()->user();
        
        if (!$user->isStudent()) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }
        
        if (!$user->isEnrolledIn($lesson->advanced_course_id)) {
            return response()->json(['error' => 'غير مصرح - غير مسجل في الكورس'], 403);
        }
        
        $progress = $lesson->progress()->where('user_id', $user->id)->first();

        $videoUrl = $lesson->video_url ? trim($lesson->video_url) : null;
        $videoPlatform = null;
        if ($videoUrl) {
            $resolved = \App\Support\LectureRecordingResolver::resolve(
                $videoUrl,
                \App\Helpers\VideoHelper::getVideoSource($videoUrl)
            );
            $videoUrl = $resolved['recording_url'] ?: $videoUrl;
            $videoPlatform = $resolved['video_platform'];
        }

        return response()->json([
            'id' => $lesson->id,
            'title' => $lesson->title,
            'description' => $lesson->description,
            'content' => $lesson->content,
            'type' => $lesson->type,
            'video_url' => $videoUrl,
            'video_platform' => $videoPlatform,
            'duration_minutes' => $lesson->duration_minutes,
            'attachments' => $lesson->attachments ? json_decode($lesson->attachments, true) : null,
            'progress' => $progress ? [
                'is_completed' => (bool) $progress->is_completed,
                'progress_percent' => (int) ($progress->progress_percent ?? 0),
                'watch_time' => (int) ($progress->watch_time ?? 0),
            ] : null
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
        Route::match(['get', 'post'], '/{exam}/start', [\App\Http\Controllers\Student\ExamController::class, 'start'])->name('start');
        Route::get('/{exam}/attempts/{attempt}/take', [\App\Http\Controllers\Student\ExamController::class, 'take'])
            ->middleware(\App\Http\Middleware\VideoProtectionMiddleware::class)
            ->name('take');
        Route::post('/{exam}/attempts/{attempt}/save-answer', [\App\Http\Controllers\Student\ExamController::class, 'saveAnswer'])->name('save-answer');
        Route::match(['get', 'post'], '/{exam}/attempts/{attempt}/submit', [\App\Http\Controllers\Student\ExamController::class, 'submit'])->name('submit');
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

        Route::middleware('sales.employee')->prefix('sales')->name('sales.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Employee\SalesDashboardController::class, 'index'])->name('dashboard');
            Route::get('kpi', [\App\Http\Controllers\Employee\SalesKpiController::class, 'index'])->name('kpi.index');
            Route::get('commissions', [\App\Http\Controllers\Employee\SalesCommissionController::class, 'index'])->name('commissions.index');
            Route::get('reports', [\App\Http\Controllers\Employee\SalesReportController::class, 'index'])->name('reports.index');
            Route::get('daily-reports', [\App\Http\Controllers\Employee\SalesDailyReportController::class, 'index'])->name('daily-reports.index');
            Route::get('daily-reports/edit', [\App\Http\Controllers\Employee\SalesDailyReportController::class, 'edit'])->name('daily-reports.edit');
            Route::post('daily-reports', [\App\Http\Controllers\Employee\SalesDailyReportController::class, 'store'])->name('daily-reports.store');
            Route::post('daily-reports/sync-auto', [\App\Http\Controllers\Employee\SalesDailyReportController::class, 'syncAuto'])->name('daily-reports.sync-auto');
            Route::resource('groups', \App\Http\Controllers\Employee\SalesLeadGroupController::class)->except(['edit']);
            Route::post('groups/{group}/whatsapp-bulk', [\App\Http\Controllers\Employee\SalesGroupWhatsAppController::class, 'store'])->name('groups.whatsapp.store');
            Route::get('groups/{group}/whatsapp-batches/{batch}', [\App\Http\Controllers\Employee\SalesGroupWhatsAppController::class, 'showBatch'])->name('groups.whatsapp-batches.show');
            Route::get('groups/{group}/whatsapp-batches/{batch}/status', [\App\Http\Controllers\Employee\SalesGroupWhatsAppController::class, 'statusJson'])->name('groups.whatsapp-batches.status');
            Route::prefix('whatsapp-groups')->name('whatsapp-groups.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Employee\SalesWhatsAppGroupController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Employee\SalesWhatsAppGroupController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Employee\SalesWhatsAppGroupController::class, 'store'])->name('store');
                Route::get('/{whatsappGroup}', [\App\Http\Controllers\Employee\SalesWhatsAppGroupController::class, 'show'])->name('show');
                Route::put('/{whatsappGroup}', [\App\Http\Controllers\Employee\SalesWhatsAppGroupController::class, 'update'])->name('update');
                Route::post('/{whatsappGroup}/participants', [\App\Http\Controllers\Employee\SalesWhatsAppGroupController::class, 'addParticipants'])->name('participants.store');
                Route::delete('/{whatsappGroup}/participants/{participant}', [\App\Http\Controllers\Employee\SalesWhatsAppGroupController::class, 'removeParticipant'])->name('participants.destroy');
                Route::post('/{whatsappGroup}/refresh-invite', [\App\Http\Controllers\Employee\SalesWhatsAppGroupController::class, 'refreshInvite'])->name('refresh-invite');
                Route::post('/{whatsappGroup}/sync', [\App\Http\Controllers\Employee\SalesWhatsAppGroupController::class, 'sync'])->name('sync');
                Route::post('/{whatsappGroup}/leave', [\App\Http\Controllers\Employee\SalesWhatsAppGroupController::class, 'leave'])->name('leave');
                Route::post('/{whatsappGroup}/import-crm', [\App\Http\Controllers\Employee\SalesWhatsAppGroupController::class, 'importFromCrm'])->name('import-crm');
            });
            Route::post('leads/{lead}/activities', [\App\Http\Controllers\Employee\SalesLeadController::class, 'storeActivity'])->name('leads.activities.store');
            Route::post('leads/{lead}/quick-activity', [\App\Http\Controllers\Employee\SalesLeadController::class, 'quickActivity'])->name('leads.quick-activity');
            Route::post('leads/{lead}/csat', [\App\Http\Controllers\Employee\SalesLeadController::class, 'storeCsat'])->name('leads.csat.store');
            Route::resource('leads', \App\Http\Controllers\Employee\SalesLeadController::class);

            Route::prefix('whatsapp/inbox')->name('whatsapp.inbox.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Employee\SalesWhatsAppInboxController::class, 'index'])->name('index');
                Route::get('/templates', [\App\Http\Controllers\Employee\SalesWhatsAppInboxController::class, 'templates'])->name('templates');
                Route::get('/poll', [\App\Http\Controllers\Employee\SalesWhatsAppInboxController::class, 'poll'])->name('poll');
                Route::get('/{conversation}', [\App\Http\Controllers\Employee\SalesWhatsAppInboxController::class, 'showConversation'])->name('conversation');
                Route::post('/start', [\App\Http\Controllers\Employee\SalesWhatsAppInboxController::class, 'start'])->name('start');
                Route::post('/{conversation}/reply', [\App\Http\Controllers\Employee\SalesWhatsAppInboxController::class, 'reply'])->name('reply');
                Route::post('/{conversation}/template', [\App\Http\Controllers\Employee\SalesWhatsAppInboxController::class, 'sendTemplate'])->name('template');
                Route::post('/{conversation}/read', [\App\Http\Controllers\Employee\SalesWhatsAppInboxController::class, 'markRead'])->name('read');
                Route::post('/{conversation}/status', [\App\Http\Controllers\Employee\SalesWhatsAppInboxController::class, 'updateStatus'])->name('status');
                Route::post('/{conversation}/lead-stage', [\App\Http\Controllers\Employee\SalesWhatsAppInboxController::class, 'updateLeadStage'])->name('lead-stage');
                Route::post('/{conversation}/notes', [\App\Http\Controllers\Employee\SalesWhatsAppInboxController::class, 'storeNote'])->name('notes');
                Route::post('/{conversation}/tags/{tag}', [\App\Http\Controllers\Employee\SalesWhatsAppInboxController::class, 'syncTag'])->name('tag');
            });
        });
        

        Route::middleware('moderator.employee')->prefix('design-cycles')->name('design-cycles.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Employee\DesignTaskCycleController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Employee\DesignTaskCycleController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Employee\DesignTaskCycleController::class, 'store'])->name('store');
            Route::post('/{design_task_cycle}/planner-items', [\App\Http\Controllers\Employee\DesignTaskCycleController::class, 'storePlannerItem'])->name('planner-items.store');
            Route::patch('/{design_task_cycle}/planner-items/{planner_item}', [\App\Http\Controllers\Employee\DesignTaskCycleController::class, 'updatePlannerItem'])->name('planner-items.update');
            Route::delete('/{design_task_cycle}/planner-items/{planner_item}', [\App\Http\Controllers\Employee\DesignTaskCycleController::class, 'destroyPlannerItem'])->name('planner-items.destroy');
            Route::get('/{design_task_cycle}', [\App\Http\Controllers\Employee\DesignTaskCycleController::class, 'show'])->name('show');
            Route::post('/{design_task_cycle}/moderator-delivery', [\App\Http\Controllers\Employee\DesignTaskCycleController::class, 'storeModeratorDelivery'])->name('moderator-delivery.store');
            Route::post('/{design_task_cycle}/cancel', [\App\Http\Controllers\Employee\DesignTaskCycleController::class, 'cancel'])->name('cancel');
        });

        Route::middleware('moderator.employee')->prefix('marketing-plans')->name('marketing-plans.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Employee\ModeratorMarketingPlanController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Employee\ModeratorMarketingPlanController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Employee\ModeratorMarketingPlanController::class, 'store'])->name('store');
            Route::get('/{marketing_plan}', [\App\Http\Controllers\Employee\ModeratorMarketingPlanController::class, 'show'])->name('show');
            Route::get('/{marketing_plan}/edit', [\App\Http\Controllers\Employee\ModeratorMarketingPlanController::class, 'edit'])->name('edit');
            Route::put('/{marketing_plan}', [\App\Http\Controllers\Employee\ModeratorMarketingPlanController::class, 'update'])->name('update');
            Route::delete('/{marketing_plan}', [\App\Http\Controllers\Employee\ModeratorMarketingPlanController::class, 'destroy'])->name('destroy');
            Route::post('/{marketing_plan}/platforms', [\App\Http\Controllers\Employee\ModeratorMarketingPlanController::class, 'storePlatform'])->name('platforms.store');
            Route::put('/{marketing_plan}/platforms/{platform}', [\App\Http\Controllers\Employee\ModeratorMarketingPlanController::class, 'updatePlatform'])->name('platforms.update');
            Route::delete('/{marketing_plan}/platforms/{platform}', [\App\Http\Controllers\Employee\ModeratorMarketingPlanController::class, 'destroyPlatform'])->name('platforms.destroy');
            Route::post('/{marketing_plan}/events', [\App\Http\Controllers\Employee\ModeratorMarketingPlanController::class, 'storeEvent'])->name('events.store');
            Route::put('/{marketing_plan}/events/{event}', [\App\Http\Controllers\Employee\ModeratorMarketingPlanController::class, 'updateEvent'])->name('events.update');
            Route::delete('/{marketing_plan}/events/{event}', [\App\Http\Controllers\Employee\ModeratorMarketingPlanController::class, 'destroyEvent'])->name('events.destroy');
        });

        Route::get('/tasks', [\App\Http\Controllers\Employee\EmployeeTaskController::class, 'index'])->name('tasks.index');
        Route::get('/tasks/{task}', [\App\Http\Controllers\Employee\EmployeeTaskController::class, 'show'])->name('tasks.show');
        Route::put('/tasks/{task}/status', [\App\Http\Controllers\Employee\EmployeeTaskController::class, 'updateStatus'])->name('tasks.update-status');
        Route::get('/tasks/{task}/deliverables/montage-excel-template', [\App\Http\Controllers\Employee\EmployeeTaskController::class, 'downloadMontageExcelTemplate'])->name('tasks.deliverables.montage-excel-template');
        Route::get('/tasks/{task}/deliverables/montage-export', [\App\Http\Controllers\Employee\EmployeeTaskController::class, 'exportMontageDeliverables'])->name('tasks.deliverables.montage-export');
        Route::post('/tasks/{task}/deliverables', [\App\Http\Controllers\Employee\EmployeeTaskController::class, 'submitDeliverable'])->name('tasks.submit-deliverable');
        Route::match(['put', 'patch'], '/tasks/{task}/deliverables/{deliverable}', [\App\Http\Controllers\Employee\EmployeeTaskController::class, 'updateDeliverable'])->name('tasks.deliverables.update');
        Route::delete('/tasks/{task}/deliverables/{deliverable}', [\App\Http\Controllers\Employee\EmployeeTaskController::class, 'destroyDeliverable'])->name('tasks.deliverables.destroy');
        
        // طلبات الإجازة
        Route::resource('leaves', \App\Http\Controllers\Employee\EmployeeLeaveController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
        
        // المحاسبة والراتب
        Route::get('/accounting', [\App\Http\Controllers\Employee\AccountingController::class, 'index'])->name('accounting.index');
        Route::post('/accounting/bank-account', [\App\Http\Controllers\Employee\AccountingController::class, 'updateBankAccount'])->name('accounting.update-bank');

        Route::get('daily-reports', [\App\Http\Controllers\Employee\EmployeeDailyReportController::class, 'index'])->name('daily-reports.index');
        Route::get('daily-reports/edit', [\App\Http\Controllers\Employee\EmployeeDailyReportController::class, 'edit'])->name('daily-reports.edit');
        Route::post('daily-reports', [\App\Http\Controllers\Employee\EmployeeDailyReportController::class, 'store'])->name('daily-reports.store');
        
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
        
        // مهام التسويق اليومية (تأكيد التنفيذ)
        Route::get('/marketing-today', [\App\Http\Controllers\Employee\MarketingTodayController::class, 'index'])->name('marketing-today.index');
        Route::post('/marketing-today/{event}/confirm', [\App\Http\Controllers\Employee\MarketingTodayController::class, 'confirm'])->name('marketing-today.confirm');

        // التقويم
        Route::get('/calendar', [\App\Http\Controllers\Employee\EmployeeCalendarController::class, 'index'])->name('calendar');
        Route::get('/api/calendar/events', [\App\Http\Controllers\Employee\EmployeeCalendarController::class, 'getEvents'])->name('calendar.events');
        
        // التقارير والإحصائيات
        Route::get('/reports', [\App\Http\Controllers\Employee\EmployeeReportController::class, 'index'])->name('reports');
        Route::get('/documentation', [\App\Http\Controllers\Employee\EmployeeController::class, 'documentation'])->name('documentation');
        
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

        Route::get('branches/rollout-plan', [\App\Http\Controllers\Admin\BranchController::class, 'rolloutPlan'])->name('branches.rollout-plan');
        Route::get('branch-managers/create', [\App\Http\Controllers\Admin\BranchController::class, 'createBranchManager'])->name('branch-managers.create');
        Route::post('branch-managers', [\App\Http\Controllers\Admin\BranchController::class, 'storeBranchManagerGlobal'])
            ->middleware('throttle:10,1')
            ->name('branch-managers.store');
        Route::post('branches/{branch}/branch-managers', [\App\Http\Controllers\Admin\BranchController::class, 'storeBranchManager'])
            ->middleware('throttle:10,1')
            ->name('branches.branch-managers.store');
        Route::resource('branches', \App\Http\Controllers\Admin\BranchController::class);

        Route::prefix('support-tickets')->name('support-tickets.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SupportTicketsController::class, 'index'])->name('index');
            Route::get('/{ticket}', [\App\Http\Controllers\Admin\SupportTicketsController::class, 'show'])->name('show');
            Route::post('/{ticket}/reply', [\App\Http\Controllers\Admin\SupportTicketsController::class, 'reply'])->name('reply');
            Route::post('/{ticket}/close', [\App\Http\Controllers\Admin\SupportTicketsController::class, 'close'])->name('close');
        });

        Route::prefix('mobile-app')->name('mobile-app.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\MobileAppSettingsController::class, 'edit'])->name('edit');
            Route::put('/', [\App\Http\Controllers\Admin\MobileAppSettingsController::class, 'update'])->name('update');
            Route::get('/notifications', [\App\Http\Controllers\Admin\MobileAppNotificationsController::class, 'index'])->name('notifications');
            Route::post('/notifications', [\App\Http\Controllers\Admin\MobileAppNotificationsController::class, 'store'])
                ->middleware('throttle:30,1')
                ->name('notifications.store');
            Route::get('/maintenance', [\App\Http\Controllers\Admin\MobileAppPagesController::class, 'maintenance'])->name('maintenance');
            Route::get('/links', [\App\Http\Controllers\Admin\MobileAppPagesController::class, 'links'])->name('links');
            Route::get('/appearance', [\App\Http\Controllers\Admin\MobileAppPagesController::class, 'appearance'])->name('appearance');

            Route::prefix('course-community')->name('course-community.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\AdminCourseCommunityController::class, 'index'])->name('index');
                Route::get('/posts/create', [\App\Http\Controllers\Admin\AdminCourseCommunityController::class, 'create'])->name('posts.create');
                Route::post('/posts', [\App\Http\Controllers\Admin\AdminCourseCommunityController::class, 'store'])
                    ->middleware('throttle:30,1')
                    ->name('posts.store');
                Route::get('/posts/{post}', [\App\Http\Controllers\Admin\AdminCourseCommunityController::class, 'show'])->name('posts.show');
                Route::delete('/posts/{post}', [\App\Http\Controllers\Admin\AdminCourseCommunityController::class, 'destroyPost'])->name('posts.destroy');
                Route::post('/posts/{post}/pin', [\App\Http\Controllers\Admin\AdminCourseCommunityController::class, 'togglePin'])->name('posts.pin');
                Route::delete('/posts/{post}/comments/{comment}', [\App\Http\Controllers\Admin\AdminCourseCommunityController::class, 'destroyComment'])
                    ->name('posts.comments.destroy');
            });
        });

        Route::prefix('sales')->name('sales.')->group(function () {
            Route::get('reports', [\App\Http\Controllers\Admin\SalesReportController::class, 'index'])->name('reports.index');
            Route::get('reports/employee', [\App\Http\Controllers\Admin\SalesReportController::class, 'employee'])->name('reports.employee');
            Route::get('reports/export', [\App\Http\Controllers\Admin\SalesReportController::class, 'export'])->name('reports.export');
            Route::get('reports/pdf', [\App\Http\Controllers\Admin\SalesReportController::class, 'pdfExport'])->name('reports.pdf');
            Route::get('reports/daily-export', [\App\Http\Controllers\Admin\SalesReportController::class, 'dailyExport'])->name('reports.daily-export');
            Route::get('daily-reports', [\App\Http\Controllers\Admin\SalesDailyReportController::class, 'index'])->name('daily-reports.index');
            Route::get('daily-reports/export', [\App\Http\Controllers\Admin\SalesDailyReportController::class, 'export'])->name('daily-reports.export');
            Route::get('daily-reports/settings', [\App\Http\Controllers\Admin\SalesDailyReportController::class, 'settings'])->name('daily-reports.settings');
            Route::put('daily-reports/settings', [\App\Http\Controllers\Admin\SalesDailyReportController::class, 'updateSettings'])->name('daily-reports.settings.update');
            Route::get('daily-reports/{id}', [\App\Http\Controllers\Admin\SalesDailyReportController::class, 'show'])->name('daily-reports.show')->where('id', '[0-9]+');
            Route::get('audit-log', [\App\Http\Controllers\Admin\SalesAuditController::class, 'index'])->name('audit-log.index');
            Route::get('transfer', [\App\Http\Controllers\Admin\SalesTransferController::class, 'index'])->name('transfer.index');
            Route::post('transfer', [\App\Http\Controllers\Admin\SalesTransferController::class, 'store'])->name('transfer.store');
            Route::get('kpi', [\App\Http\Controllers\Admin\SalesKpiController::class, 'index'])->name('kpi.index');
            Route::get('kpi/targets', [\App\Http\Controllers\Admin\SalesKpiController::class, 'targets'])->name('kpi.targets');
            Route::put('kpi/targets', [\App\Http\Controllers\Admin\SalesKpiController::class, 'updateTargets'])->name('kpi.targets.update');
            Route::get('insights', [\App\Http\Controllers\Admin\SalesInsightsController::class, 'index'])->name('insights.index');
            Route::get('commissions', [\App\Http\Controllers\Admin\SalesCommissionController::class, 'index'])->name('commissions.index');
            Route::resource('groups', \App\Http\Controllers\Admin\SalesLeadGroupController::class)->except(['edit']);
            Route::post('groups/{group}/whatsapp-bulk', [\App\Http\Controllers\Admin\SalesGroupWhatsAppController::class, 'store'])->name('groups.whatsapp.store');
            Route::get('groups/{group}/whatsapp-batches/{batch}', [\App\Http\Controllers\Admin\SalesGroupWhatsAppController::class, 'showBatch'])->name('groups.whatsapp-batches.show');
            Route::get('groups/{group}/whatsapp-batches/{batch}/status', [\App\Http\Controllers\Admin\SalesGroupWhatsAppController::class, 'statusJson'])->name('groups.whatsapp-batches.status');
            Route::prefix('whatsapp-groups')->name('whatsapp-groups.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\SalesWhatsAppGroupController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Admin\SalesWhatsAppGroupController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Admin\SalesWhatsAppGroupController::class, 'store'])->name('store');
                Route::get('/{whatsappGroup}', [\App\Http\Controllers\Admin\SalesWhatsAppGroupController::class, 'show'])->name('show');
                Route::put('/{whatsappGroup}', [\App\Http\Controllers\Admin\SalesWhatsAppGroupController::class, 'update'])->name('update');
                Route::post('/{whatsappGroup}/participants', [\App\Http\Controllers\Admin\SalesWhatsAppGroupController::class, 'addParticipants'])->name('participants.store');
                Route::delete('/{whatsappGroup}/participants/{participant}', [\App\Http\Controllers\Admin\SalesWhatsAppGroupController::class, 'removeParticipant'])->name('participants.destroy');
                Route::post('/{whatsappGroup}/refresh-invite', [\App\Http\Controllers\Admin\SalesWhatsAppGroupController::class, 'refreshInvite'])->name('refresh-invite');
                Route::post('/{whatsappGroup}/sync', [\App\Http\Controllers\Admin\SalesWhatsAppGroupController::class, 'sync'])->name('sync');
                Route::post('/{whatsappGroup}/leave', [\App\Http\Controllers\Admin\SalesWhatsAppGroupController::class, 'leave'])->name('leave');
                Route::post('/{whatsappGroup}/import-crm', [\App\Http\Controllers\Admin\SalesWhatsAppGroupController::class, 'importFromCrm'])->name('import-crm');
            });
            Route::get('leads/export', [\App\Http\Controllers\Admin\SalesLeadController::class, 'export'])->name('leads.export');
            Route::get('leads/import', [\App\Http\Controllers\Admin\SalesLeadController::class, 'importForm'])->name('leads.import');
            Route::get('leads/import/template', [\App\Http\Controllers\Admin\SalesLeadController::class, 'importTemplate'])->name('leads.import.template');
            Route::post('leads/import', [\App\Http\Controllers\Admin\SalesLeadController::class, 'importStore'])->name('leads.import.store');
            Route::post('leads/{lead}/activities', [\App\Http\Controllers\Admin\SalesLeadController::class, 'storeActivity'])->name('leads.activities.store');
            Route::post('leads/{lead}/confirm-win', [\App\Http\Controllers\Admin\SalesLeadController::class, 'confirmWin'])->name('leads.confirm-win');
            Route::resource('leads', \App\Http\Controllers\Admin\SalesLeadController::class);

            Route::get('categories', [\App\Http\Controllers\Admin\SalesLeadCategoryController::class, 'index'])->name('categories.index');
            Route::post('categories', [\App\Http\Controllers\Admin\SalesLeadCategoryController::class, 'store'])->name('categories.store');
            Route::put('categories/{category}', [\App\Http\Controllers\Admin\SalesLeadCategoryController::class, 'update'])->name('categories.update');
            Route::delete('categories/{category}', [\App\Http\Controllers\Admin\SalesLeadCategoryController::class, 'destroy'])->name('categories.destroy');
        });

        // HR (Recruitment / ATS)
        Route::prefix('hr')->name('hr.')->group(function () {
            Route::resource('jobs', \App\Http\Controllers\Admin\Hr\JobPostingController::class);
            Route::get('applications', [\App\Http\Controllers\Admin\Hr\ApplicationController::class, 'index'])->name('applications.index');
            Route::get('applications/{application}', [\App\Http\Controllers\Admin\Hr\ApplicationController::class, 'show'])->name('applications.show');
            Route::put('applications/{application}/status', [\App\Http\Controllers\Admin\Hr\ApplicationController::class, 'updateStatus'])->name('applications.status');
            Route::post('applications/{application}/rescore', [\App\Http\Controllers\Admin\Hr\ApplicationController::class, 'rescore'])->name('applications.rescore');
            Route::delete('applications/{application}', [\App\Http\Controllers\Admin\Hr\ApplicationController::class, 'destroy'])->name('applications.destroy');
            Route::resource('rubrics', \App\Http\Controllers\Admin\Hr\RubricController::class)->except(['show']);
        });

        // قسم المنح الدراسية
        Route::prefix('scholarships')->name('scholarships.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\Scholarship\DashboardController::class, 'index'])->name('dashboard');
            Route::resource('programs', \App\Http\Controllers\Admin\Scholarship\ProgramController::class);
            Route::get('courses', [\App\Http\Controllers\Admin\Scholarship\CourseController::class, 'index'])->name('courses.index');
            Route::get('courses/{course}', [\App\Http\Controllers\Admin\Scholarship\CourseController::class, 'show'])->name('courses.show');
            Route::get('instructors', [\App\Http\Controllers\Admin\Scholarship\InstructorController::class, 'index'])->name('instructors.index');
            Route::get('instructors/{instructor}', [\App\Http\Controllers\Admin\Scholarship\InstructorController::class, 'show'])->name('instructors.show');
            Route::get('students', [\App\Http\Controllers\Admin\Scholarship\RegistrationController::class, 'index'])->name('students.index');
            Route::post('registrations/{registration}/activate', [\App\Http\Controllers\Admin\Scholarship\RegistrationController::class, 'activate'])->name('registrations.activate');
            Route::post('registrations/{registration}/deactivate', [\App\Http\Controllers\Admin\Scholarship\RegistrationController::class, 'deactivate'])->name('registrations.deactivate');
            Route::post('registrations/{registration}/reject', [\App\Http\Controllers\Admin\Scholarship\RegistrationController::class, 'reject'])->name('registrations.reject');
        });

        // قسم الاستثمار
        Route::prefix('investment')->name('investment.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\Investment\DashboardController::class, 'index'])->name('dashboard');
            Route::resource('plans', \App\Http\Controllers\Admin\Investment\PlanController::class);
            Route::get('inquiries', [\App\Http\Controllers\Admin\Investment\InquiryController::class, 'index'])->name('inquiries.index');
            Route::get('inquiries/{inquiry}', [\App\Http\Controllers\Admin\Investment\InquiryController::class, 'show'])->name('inquiries.show');
            Route::put('inquiries/{inquiry}', [\App\Http\Controllers\Admin\Investment\InquiryController::class, 'update'])->name('inquiries.update');
            Route::delete('inquiries/{inquiry}', [\App\Http\Controllers\Admin\Investment\InquiryController::class, 'destroy'])->name('inquiries.destroy');
            Route::get('policies', [\App\Http\Controllers\Admin\Investment\PolicyController::class, 'edit'])->name('policies.edit');
            Route::put('policies', [\App\Http\Controllers\Admin\Investment\PolicyController::class, 'update'])->name('policies.update');
        });

        // بروفايل الأدمن
        Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');

        // إدارة المستخدمين
        Route::get('/users', [\App\Http\Controllers\Admin\AdminController::class, 'users'])->name('users.index');
        Route::get('/users/create', [\App\Http\Controllers\Admin\AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [\App\Http\Controllers\Admin\AdminController::class, 'storeUser'])
            ->middleware('throttle:20,1')
            ->name('users.store');
        Route::get('/users/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'showUser'])->name('users.show')->where('id', '[0-9]+');
        Route::get('/users/{id}/edit', [\App\Http\Controllers\Admin\AdminController::class, 'editUser'])->name('users.edit')->where('id', '[0-9]+');
        Route::put('/users/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'updateUser'])->name('users.update')->where('id', '[0-9]+');
        Route::delete('/users/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'deleteUser'])->name('users.delete')->where('id', '[0-9]+');
        
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

        // الاجتماعات / الورش
        Route::get('workshops/{workshop}/confirmations', [\App\Http\Controllers\Admin\WorkshopController::class, 'confirmations'])
            ->name('workshops.confirmations');
        Route::post('workshops/{workshop}/deactivate', [\App\Http\Controllers\Admin\WorkshopController::class, 'deactivate'])
            ->name('workshops.deactivate');
        Route::post('workshops/{workshop}/activate', [\App\Http\Controllers\Admin\WorkshopController::class, 'activate'])
            ->name('workshops.activate');
        Route::resource('workshops', \App\Http\Controllers\Admin\WorkshopController::class);
        Route::get('workshops/{workshop}/export', [\App\Http\Controllers\Admin\WorkshopController::class, 'exportRegistrations'])
            ->name('workshops.export');
        Route::post('workshops/{workshop}/send-acceptance', [\App\Http\Controllers\Admin\WorkshopController::class, 'sendAcceptanceEmails'])
            ->name('workshops.send-acceptance');
        Route::post('workshops/{workshop}/send-whatsapp', [\App\Http\Controllers\Admin\WorkshopController::class, 'sendWhatsappMessages'])
            ->name('workshops.send-whatsapp');
        Route::post('workshops/{workshop}/registrations/{registration}/whatsapp-contacted', [\App\Http\Controllers\Admin\WorkshopController::class, 'markWhatsappContacted'])
            ->name('workshops.whatsapp-contacted');
        Route::post('workshops/{workshop}/whatsapp-bulk', [\App\Http\Controllers\Admin\WorkshopWhatsAppController::class, 'store'])
            ->name('workshops.whatsapp-bulk');
        Route::post('workshops/{workshop}/checkin', [\App\Http\Controllers\Admin\WorkshopController::class, 'checkin'])
            ->name('workshops.checkin');
        Route::post('workshops/{workshop}/convert-to-leads', [\App\Http\Controllers\Admin\WorkshopController::class, 'convertRegistrationsToLeads'])
            ->name('workshops.convert-to-leads');
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

        // إدارة الامتحانات (مسار الكورس قبل المسارات الأخرى لتفادي التعارض)
        Route::prefix('exams')->name('exams.')->group(function () {
            Route::get('/course/{course}', [\App\Http\Controllers\Admin\ExamController::class, 'indexByCourse'])->name('by-course');
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

        // إدارة التمارين العملية (Practice / Learning Patterns)
        Route::prefix('practice')->name('practice.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\PracticeController::class, 'index'])->name('index');
            Route::get('/{pattern}', [\App\Http\Controllers\Admin\PracticeController::class, 'show'])->name('show');
        });

        // إدارة المواد الدراسية القديمة
        Route::resource('subjects', \App\Http\Controllers\Admin\SubjectController::class);

        // إدارة الكورسات القديمة
        Route::resource('courses', \App\Http\Controllers\Admin\CourseController::class);

        // إعدادات النظام (الهوية البصرية: اللوجو والأيقونة)
        Route::get('/system-settings', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'index'])->name('system-settings.index');
        Route::put('/system-settings', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'update'])->name('system-settings.update');

        // سجل النشاطات
        Route::get('/activity-log', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-log');
        Route::get('/activity-log/{activityLog}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('activity-log.show');
        Route::post('/activity-log/clear', [\App\Http\Controllers\Admin\ActivityLogController::class, 'destroy'])->name('activity-log.destroy');

        // سجل أخطاء المنصة
        Route::get('/platform-errors', [\App\Http\Controllers\Admin\PlatformErrorLogController::class, 'index'])->name('platform-errors.index');
        Route::get('/platform-errors/{platformError}', [\App\Http\Controllers\Admin\PlatformErrorLogController::class, 'show'])->name('platform-errors.show');
        Route::patch('/platform-errors/{platformError}/status', [\App\Http\Controllers\Admin\PlatformErrorLogController::class, 'updateStatus'])->name('platform-errors.update-status');
        Route::post('/platform-errors/bulk', [\App\Http\Controllers\Admin\PlatformErrorLogController::class, 'bulkUpdate'])->name('platform-errors.bulk');

        // سجلات التحقق الثنائي (2FA)
        Route::get('/two-factor-logs', [\App\Http\Controllers\Admin\TwoFactorLogController::class, 'index'])->name('two-factor-logs.index');

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

        // إدارة الواجبات والمشاريع (مسار الكورس قبل المسارات الأخرى لتفادي التعارض)
        Route::get('/assignments/course/{course}', [\App\Http\Controllers\Admin\AssignmentController::class, 'indexByCourse'])->name('assignments.by-course');
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

        // مجتمع البيانات والذكاء الاصطناعي — للإدارة العليا فقط (صلاحية super_admin أو admin)
        Route::prefix('community')->name('community.')->middleware('role:super_admin')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\CommunityController::class, 'dashboard'])->name('dashboard');
            Route::resource('competitions', \App\Http\Controllers\Admin\CommunityCompetitionController::class)->except(['show']);
            Route::resource('datasets', \App\Http\Controllers\Admin\CommunityDatasetController::class)->except(['show']);
            Route::get('/submissions', [\App\Http\Controllers\Admin\CommunityController::class, 'submissions'])->name('submissions.index');
            Route::get('/submissions/dataset/{dataset}', [\App\Http\Controllers\Admin\CommunityController::class, 'showSubmission'])->name('submissions.dataset.show');
            Route::get('/submissions/dataset/{dataset}/download', [\App\Http\Controllers\Admin\CommunityController::class, 'downloadSubmission'])->name('submissions.dataset.download');
            Route::get('/submissions/dataset/{dataset}/preview', [\App\Http\Controllers\Admin\CommunityController::class, 'submissionPreview'])->name('submissions.dataset.preview');
            Route::get('/submissions/dataset/{dataset}/preview-zip-entry', [\App\Http\Controllers\Admin\CommunityController::class, 'submissionPreviewZipEntry'])->name('submissions.dataset.preview-zip-entry');
            Route::get('/submissions/dataset/{dataset}/download/{index}', [\App\Http\Controllers\Admin\CommunityController::class, 'submissionDownloadFile'])->name('submissions.dataset.download-file')->whereNumber('index');
            Route::get('/submissions/dataset/{dataset}/download-all', [\App\Http\Controllers\Admin\CommunityController::class, 'submissionDownloadAll'])->name('submissions.dataset.download-all');
            Route::get('/submissions/dataset/{dataset}/zip-contents', [\App\Http\Controllers\Admin\CommunityController::class, 'submissionZipContents'])->name('submissions.dataset.zip-contents');
            Route::post('/submissions/dataset/{dataset}/approve', [\App\Http\Controllers\Admin\CommunityController::class, 'approveDataset'])->name('submissions.dataset.approve');
            Route::post('/submissions/dataset/{dataset}/reject', [\App\Http\Controllers\Admin\CommunityController::class, 'rejectDataset'])->name('submissions.dataset.reject');
            Route::get('/submissions/models', [\App\Http\Controllers\Admin\CommunityController::class, 'modelSubmissions'])->name('submissions.models.index');
            Route::get('/submissions/model/{community_model}', [\App\Http\Controllers\Admin\CommunityController::class, 'showModelSubmission'])->name('submissions.model.show');
            Route::post('/submissions/model/{community_model}/approve', [\App\Http\Controllers\Admin\CommunityController::class, 'approveModel'])->name('submissions.model.approve');
            Route::post('/submissions/model/{community_model}/reject', [\App\Http\Controllers\Admin\CommunityController::class, 'rejectModel'])->name('submissions.model.reject');
            Route::get('/contributors', [\App\Http\Controllers\Admin\CommunityController::class, 'contributors'])->name('contributors.index');
            Route::post('/contributors', [\App\Http\Controllers\Admin\CommunityController::class, 'addContributor'])->name('contributors.store');
            Route::post('/contributors/new', [\App\Http\Controllers\Admin\CommunityController::class, 'storeNewContributor'])->name('contributors.new.store');
            Route::post('/contributors/profiles/{profile}/approve', [\App\Http\Controllers\Admin\CommunityController::class, 'approveContributorProfile'])->name('contributors.profiles.approve');
            Route::post('/contributors/profiles/{profile}/reject', [\App\Http\Controllers\Admin\CommunityController::class, 'rejectContributorProfile'])->name('contributors.profiles.reject');
            Route::delete('/contributors/{user}', [\App\Http\Controllers\Admin\CommunityController::class, 'removeContributor'])->name('contributors.destroy');
            Route::get('/notifications', [\App\Http\Controllers\Admin\CommunityController::class, 'notificationsForm'])->name('notifications.index');
            Route::post('/notifications/send', [\App\Http\Controllers\Admin\CommunityController::class, 'sendNotifications'])->name('notifications.send');
            Route::get('/discussions', [\App\Http\Controllers\Admin\CommunityController::class, 'discussions'])->name('discussions.index');
            Route::get('/settings', [\App\Http\Controllers\Admin\CommunityController::class, 'settings'])->name('settings.index');
        });

        // الإدارة العليا (من نحن وغيرها)
        Route::get('about', [\App\Http\Controllers\Admin\AboutPageController::class, 'index'])->name('about.index');
        Route::get('about/view', [\App\Http\Controllers\Admin\AboutPageController::class, 'viewPublic'])->name('about.view-public');

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
            Route::post('/quick-activate', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'quickActivate'])->name('quick-activate');
            Route::get('/{enrollment}', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'show'])->name('show');
            Route::post('/{enrollment}/activate', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'activate'])->name('activate');
            Route::post('/{enrollment}/deactivate', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'deactivate'])->name('deactivate');
            Route::post('/{enrollment}/update-progress', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'updateProgress'])->name('update-progress');
            Route::post('/{enrollment}/update-notes', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'updateNotes'])->name('update-notes');
            Route::delete('/{enrollment}', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'destroy'])->name('destroy');
            Route::get('/search/by-phone', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'searchStudentByPhone'])->name('search-by-phone');
        });

        // إدارة مصادر الفيديو (Bunny وغيرها)
        Route::prefix('video-providers')->name('video-providers.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\VideoProviderController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\VideoProviderController::class, 'store'])->name('store');
            Route::put('/{videoProvider}', [\App\Http\Controllers\Admin\VideoProviderController::class, 'update'])->name('update');
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

        Route::prefix('offline-course-bookings')->name('offline-course-bookings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\OfflineCourseBookingController::class, 'index'])->name('index');
            Route::get('/{offlineCourseBooking}', [\App\Http\Controllers\Admin\OfflineCourseBookingController::class, 'show'])->name('show');
            Route::post('/{offlineCourseBooking}/approve', [\App\Http\Controllers\Admin\OfflineCourseBookingController::class, 'approve'])->name('approve');
            Route::post('/{offlineCourseBooking}/reject', [\App\Http\Controllers\Admin\OfflineCourseBookingController::class, 'reject'])->name('reject');
        });
        Route::prefix('online-course-bookings')->name('online-course-bookings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\OnlineCourseBookingController::class, 'index'])->name('index');
            Route::get('/{offlineCourseBooking}', [\App\Http\Controllers\Admin\OnlineCourseBookingController::class, 'show'])->name('show');
            Route::post('/{offlineCourseBooking}/approve', [\App\Http\Controllers\Admin\OnlineCourseBookingController::class, 'approve'])->name('approve');
            Route::post('/{offlineCourseBooking}/reject', [\App\Http\Controllers\Admin\OnlineCourseBookingController::class, 'reject'])->name('reject');
        });

        // إدارة الأونلاين — كورسات بمجموعات مفعّل لها الأونلاين + كورس أونلاين فقط + تسجيل بالإيميل
        Route::prefix('online-management')->name('online-management.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\OnlineManagementController::class, 'index'])->name('index');
            Route::get('/courses/create', [\App\Http\Controllers\Admin\OnlineManagementController::class, 'createCourse'])->name('courses.create');
            Route::post('/courses', [\App\Http\Controllers\Admin\OnlineManagementController::class, 'storeCourse'])->name('courses.store');
            Route::get('/enroll', [\App\Http\Controllers\Admin\OnlineManagementController::class, 'enrollForm'])->name('enroll');
            Route::post('/enroll', [\App\Http\Controllers\Admin\OnlineManagementController::class, 'enrollStore'])->name('enroll.store');
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
        Route::post('offline-locations/{offlineLocation}/place-managers', [\App\Http\Controllers\Admin\OfflineLocationController::class, 'storePlaceManager'])
            ->name('offline-locations.place-managers.store');

        Route::get('place-usage-logs', [\App\Http\Controllers\Admin\PlaceUsageLogController::class, 'index'])->name('place-usage-logs.index');
        Route::post('place-usage-logs/{placeUsageLog}/approve', [\App\Http\Controllers\Admin\PlaceUsageLogController::class, 'approve'])->name('place-usage-logs.approve');
        Route::post('place-usage-logs/{placeUsageLog}/reject', [\App\Http\Controllers\Admin\PlaceUsageLogController::class, 'reject'])->name('place-usage-logs.reject');
        Route::post('place-daily-expenses/{placeDailyExpense}/approve', [\App\Http\Controllers\Admin\PlaceDailyExpenseController::class, 'approve'])->name('place-daily-expenses.approve');
        Route::post('place-daily-expenses/{placeDailyExpense}/reject', [\App\Http\Controllers\Admin\PlaceDailyExpenseController::class, 'reject'])->name('place-daily-expenses.reject');

        Route::get('place-settlements', [\App\Http\Controllers\Admin\PlaceSettlementController::class, 'index'])->name('place-settlements.index');
        Route::get('place-settlements/{placeSettlement}', [\App\Http\Controllers\Admin\PlaceSettlementController::class, 'show'])->name('place-settlements.show');
        Route::post('place-settlements/{placeSettlement}/approve', [\App\Http\Controllers\Admin\PlaceSettlementController::class, 'approve'])->name('place-settlements.approve');
        Route::post('place-settlements/{placeSettlement}/close', [\App\Http\Controllers\Admin\PlaceSettlementController::class, 'close'])->name('place-settlements.close');

        // إدارة الكورسات الأوفلاين
        Route::resource('offline-courses', \App\Http\Controllers\Admin\OfflineCourseController::class);
        
        // إدارة المجموعات للكورسات الأوفلاين
        Route::prefix('offline-courses/{offlineCourse}/groups')->name('offline-courses.groups.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\OfflineGroupController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\OfflineGroupController::class, 'store'])->name('store');
            Route::put('/{group}', [\App\Http\Controllers\Admin\OfflineGroupController::class, 'update'])->name('update');
            Route::delete('/{group}', [\App\Http\Controllers\Admin\OfflineGroupController::class, 'destroy'])->name('destroy');

            // جلسات المجموعة
            Route::post('/{group}/sessions', [\App\Http\Controllers\Admin\OfflineGroupController::class, 'storeSession'])->name('sessions.store');
            Route::put('/{group}/sessions/{session}', [\App\Http\Controllers\Admin\OfflineGroupController::class, 'updateSession'])->name('sessions.update');
            Route::delete('/{group}/sessions/{session}', [\App\Http\Controllers\Admin\OfflineGroupController::class, 'destroySession'])->name('sessions.destroy');
            Route::post('/{group}/sessions/bulk', [\App\Http\Controllers\Admin\OfflineGroupController::class, 'bulkCreateSessions'])->name('sessions.bulk');
        });

        // إدارة التسجيلات في الكورسات الأوفلاين
        Route::prefix('offline-courses/{offlineCourse}/enrollments')->name('offline-courses.enrollments.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\OfflineEnrollmentController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\OfflineEnrollmentController::class, 'store'])->name('store');
            Route::put('/{enrollment}/status', [\App\Http\Controllers\Admin\OfflineEnrollmentController::class, 'updateStatus'])->name('update-status');
            Route::post('/{enrollment}/payment', [\App\Http\Controllers\Admin\OfflineEnrollmentController::class, 'addPayment'])->name('add-payment');
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
        Route::match(['put', 'patch'], 'employee-tasks/{employee_task}/deliverables/{deliverable}', [\App\Http\Controllers\Admin\EmployeeTaskController::class, 'updateDeliverable'])->name('employee-tasks.deliverables.update');
        Route::delete('employee-tasks/{employee_task}/deliverables/{deliverable}', [\App\Http\Controllers\Admin\EmployeeTaskController::class, 'destroyDeliverable'])->name('employee-tasks.deliverables.destroy');
        Route::get('employee-tasks/{employee_task}/deliverables/montage-excel-template', [\App\Http\Controllers\Admin\EmployeeTaskController::class, 'downloadMontageExcelTemplate'])->name('employee-tasks.deliverables.montage-excel-template');
        Route::post('employee-tasks/{employee_task}/deliverables/import-excel', [\App\Http\Controllers\Admin\EmployeeTaskController::class, 'importMontageExcel'])->name('employee-tasks.deliverables.import-excel');
        Route::resource('employee-tasks', \App\Http\Controllers\Admin\EmployeeTaskController::class);

        Route::get('design-task-cycles/performance-report', [\App\Http\Controllers\Admin\DesignTaskCycleController::class, 'performanceReport'])->name('design-task-cycles.performance-report');
        Route::get('design-task-cycles/performance-report/excel', [\App\Http\Controllers\Admin\DesignTaskCycleController::class, 'performanceReportExcel'])->name('design-task-cycles.performance-report.excel');
        Route::prefix('design-task-cycles')->name('design-task-cycles.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\DesignTaskCycleController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\DesignTaskCycleController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\DesignTaskCycleController::class, 'store'])->name('store');
            Route::get('{design_task_cycle}/edit', [\App\Http\Controllers\Admin\DesignTaskCycleController::class, 'edit'])->name('edit');
            Route::put('{design_task_cycle}', [\App\Http\Controllers\Admin\DesignTaskCycleController::class, 'update'])->name('update');
            Route::delete('{design_task_cycle}', [\App\Http\Controllers\Admin\DesignTaskCycleController::class, 'destroy'])->name('destroy');
            Route::post('{design_task_cycle}/notes', [\App\Http\Controllers\Admin\DesignTaskCycleController::class, 'updateNotes'])->name('notes.update');
            Route::post('{design_task_cycle}/cancel', [\App\Http\Controllers\Admin\DesignTaskCycleController::class, 'cancel'])->name('cancel');
            Route::get('{design_task_cycle}', [\App\Http\Controllers\Admin\DesignTaskCycleController::class, 'show'])->name('show');
        });

        Route::get('moderator-marketing-plans/settings', [\App\Http\Controllers\Admin\ModeratorMarketingPlanController::class, 'settings'])->name('moderator-marketing-plans.settings');
        Route::put('moderator-marketing-plans/settings', [\App\Http\Controllers\Admin\ModeratorMarketingPlanController::class, 'updateSettings'])->name('moderator-marketing-plans.settings.update');
        Route::prefix('moderator-marketing-plans')->name('moderator-marketing-plans.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ModeratorMarketingPlanController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\ModeratorMarketingPlanController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\ModeratorMarketingPlanController::class, 'store'])->name('store');
            Route::get('{plan}/edit', [\App\Http\Controllers\Admin\ModeratorMarketingPlanController::class, 'edit'])->name('edit');
            Route::put('{plan}', [\App\Http\Controllers\Admin\ModeratorMarketingPlanController::class, 'update'])->name('update');
            Route::delete('{plan}', [\App\Http\Controllers\Admin\ModeratorMarketingPlanController::class, 'destroy'])->name('destroy');
            Route::post('{plan}/platforms', [\App\Http\Controllers\Admin\ModeratorMarketingPlanController::class, 'storePlatform'])->name('platforms.store');
            Route::put('{plan}/platforms/{platform}', [\App\Http\Controllers\Admin\ModeratorMarketingPlanController::class, 'updatePlatform'])->name('platforms.update');
            Route::delete('{plan}/platforms/{platform}', [\App\Http\Controllers\Admin\ModeratorMarketingPlanController::class, 'destroyPlatform'])->name('platforms.destroy');
            Route::post('{plan}/events', [\App\Http\Controllers\Admin\ModeratorMarketingPlanController::class, 'storeEvent'])->name('events.store');
            Route::put('{plan}/events/{event}', [\App\Http\Controllers\Admin\ModeratorMarketingPlanController::class, 'updateEvent'])->name('events.update');
            Route::delete('{plan}/events/{event}', [\App\Http\Controllers\Admin\ModeratorMarketingPlanController::class, 'destroyEvent'])->name('events.destroy');
            Route::post('{plan}/events/{event}/confirm', [\App\Http\Controllers\Admin\ModeratorMarketingPlanController::class, 'confirmEvent'])->name('events.confirm');
            Route::get('{plan}', [\App\Http\Controllers\Admin\ModeratorMarketingPlanController::class, 'show'])->name('show');
        });

        Route::get('employee-deductions/daily-report-penalty-settings', [\App\Http\Controllers\Admin\EmployeeDeductionController::class, 'dailyReportPenaltySettings'])->name('employee-deductions.daily-report-penalty-settings');
        Route::put('employee-deductions/daily-report-penalty-settings', [\App\Http\Controllers\Admin\EmployeeDeductionController::class, 'updateDailyReportPenaltySettings'])->name('employee-deductions.daily-report-penalty-settings.update');
        Route::post('employee-deductions/bulk-delete-by-date', [\App\Http\Controllers\Admin\EmployeeDeductionController::class, 'bulkDestroyByDateRange'])->name('employee-deductions.bulk-delete-by-date');
        Route::resource('employee-deductions', \App\Http\Controllers\Admin\EmployeeDeductionController::class);
        Route::resource('employee-additions', \App\Http\Controllers\Admin\EmployeeAdditionController::class);

        Route::prefix('employee-daily-reports')->name('employee-daily-reports.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\EmployeeDailyReportAdminController::class, 'index'])->name('index');
            Route::get('/settings', [\App\Http\Controllers\Admin\EmployeeDailyReportAdminController::class, 'settings'])->name('settings');
            Route::put('/settings', [\App\Http\Controllers\Admin\EmployeeDailyReportAdminController::class, 'updateSettings'])->name('settings.update');
            Route::get('/{id}', [\App\Http\Controllers\Admin\EmployeeDailyReportAdminController::class, 'show'])->name('show')->where('id', '[0-9]+');
        });
        
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
            Route::get('/instructors/{instructor}', [\App\Http\Controllers\Admin\QualityControlController::class, 'instructorShow'])->name('instructors.show');
            Route::get('/instructors/{instructor}/export', [\App\Http\Controllers\Admin\QualityControlController::class, 'instructorExport'])->name('instructors.export');
            Route::get('/employees', [\App\Http\Controllers\Admin\QualityControlController::class, 'employees'])->name('employees');
            Route::get('/operations', [\App\Http\Controllers\Admin\QualityControlController::class, 'operations'])->name('operations');
        });

        // قسم الواتساب — Meta Cloud API (رسمي)
        Route::prefix('whatsapp')->name('whatsapp.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\WhatsAppController::class, 'index'])->name('index');
            Route::get('/send', [\App\Http\Controllers\Admin\WhatsAppController::class, 'sendForm'])->name('send');
            Route::post('/send', [\App\Http\Controllers\Admin\WhatsAppController::class, 'sendMessage'])->name('send.post');
            Route::get('/messages', [\App\Http\Controllers\Admin\WhatsAppController::class, 'messages'])->name('messages');
            Route::post('/messages/{message}/resend', [\App\Http\Controllers\Admin\WhatsAppController::class, 'resendMessage'])->name('messages.resend');
            Route::get('/templates', [\App\Http\Controllers\Admin\WhatsAppTemplateController::class, 'index'])->name('templates.index');
            Route::get('/templates/create', [\App\Http\Controllers\Admin\WhatsAppTemplateController::class, 'create'])->name('templates.create');
            Route::post('/templates', [\App\Http\Controllers\Admin\WhatsAppTemplateController::class, 'store'])->name('templates.store');
            Route::post('/templates/sync', [\App\Http\Controllers\Admin\WhatsAppTemplateController::class, 'sync'])->name('templates.sync');
            Route::get('/templates/{template}', [\App\Http\Controllers\Admin\WhatsAppTemplateController::class, 'show'])->name('templates.show');
            Route::get('/templates/{template}/edit', [\App\Http\Controllers\Admin\WhatsAppTemplateController::class, 'edit'])->name('templates.edit');
            Route::put('/templates/{template}', [\App\Http\Controllers\Admin\WhatsAppTemplateController::class, 'update'])->name('templates.update');
            Route::post('/templates/{template}/submit', [\App\Http\Controllers\Admin\WhatsAppTemplateController::class, 'submit'])->name('templates.submit');
            Route::delete('/templates/{template}', [\App\Http\Controllers\Admin\WhatsAppTemplateController::class, 'destroy'])->name('templates.destroy');
            Route::get('/inbox', [\App\Http\Controllers\Admin\WhatsAppInboxController::class, 'index'])->name('inbox');
            Route::get('/reports', [\App\Http\Controllers\Admin\WhatsAppReportController::class, 'index'])->name('reports');
            Route::get('/inbox/templates', [\App\Http\Controllers\Admin\WhatsAppInboxController::class, 'templates'])->name('inbox.templates');
            Route::get('/inbox/poll', [\App\Http\Controllers\Admin\WhatsAppInboxController::class, 'poll'])->name('inbox.poll');
            Route::get('/inbox/{conversation}', [\App\Http\Controllers\Admin\WhatsAppInboxController::class, 'showConversation'])->name('inbox.conversation');
            Route::post('/inbox/start', [\App\Http\Controllers\Admin\WhatsAppInboxController::class, 'start'])->name('inbox.start');
            Route::post('/inbox/{conversation}/reply', [\App\Http\Controllers\Admin\WhatsAppInboxController::class, 'reply'])->name('inbox.reply');
            Route::post('/inbox/{conversation}/template', [\App\Http\Controllers\Admin\WhatsAppInboxController::class, 'sendTemplate'])->name('inbox.template');
            Route::post('/inbox/{conversation}/read', [\App\Http\Controllers\Admin\WhatsAppInboxController::class, 'markRead'])->name('inbox.read');
            Route::post('/inbox/{conversation}/status', [\App\Http\Controllers\Admin\WhatsAppInboxController::class, 'updateStatus'])->name('inbox.status');
            Route::post('/inbox/{conversation}/lead-stage', [\App\Http\Controllers\Admin\WhatsAppInboxController::class, 'updateLeadStage'])->name('inbox.lead-stage');
            Route::post('/inbox/{conversation}/transfer', [\App\Http\Controllers\Admin\WhatsAppInboxController::class, 'transfer'])->name('inbox.transfer');
            Route::post('/inbox/{conversation}/assign', [\App\Http\Controllers\Admin\WhatsAppInboxController::class, 'assign'])->name('inbox.assign');
            Route::post('/inbox/{conversation}/notes', [\App\Http\Controllers\Admin\WhatsAppInboxController::class, 'storeNote'])->name('inbox.notes');
            Route::post('/inbox/{conversation}/tags/{tag}', [\App\Http\Controllers\Admin\WhatsAppInboxController::class, 'syncTag'])->name('inbox.tag');
            Route::get('/settings', [\App\Http\Controllers\Admin\WhatsAppController::class, 'settings'])->name('settings');
            Route::post('/settings', [\App\Http\Controllers\Admin\WhatsAppCloudController::class, 'saveSettings'])->name('settings.update');
            Route::post('/test-connection', [\App\Http\Controllers\Admin\WhatsAppCloudController::class, 'testConnection'])->name('test-connection');
            Route::post('/disconnect', [\App\Http\Controllers\Admin\WhatsAppCloudController::class, 'disconnect'])->name('disconnect');
            Route::get('/status', [\App\Http\Controllers\Admin\WhatsAppCloudController::class, 'statusJson'])->name('status');
            Route::post('/webhook/resubscribe', [\App\Http\Controllers\Admin\WhatsAppCloudController::class, 'resubscribeWebhook'])->name('webhook.resubscribe');
            Route::post('/webhook/refresh', [\App\Http\Controllers\Admin\WhatsAppCloudController::class, 'refreshWebhook'])->name('webhook.refresh');
            Route::get('/batches', [\App\Http\Controllers\Admin\WhatsAppBatchController::class, 'index'])->name('batches.index');
            Route::get('/batches/{batch}', [\App\Http\Controllers\Admin\WhatsAppBatchController::class, 'show'])->name('batches.show');
            Route::get('/batches/{batch}/status', [\App\Http\Controllers\Admin\WhatsAppBatchController::class, 'statusJson'])->name('batches.status');
            Route::post('/batches/{batch}/process', [\App\Http\Controllers\Admin\WhatsAppBatchController::class, 'process'])->name('batches.process');
            Route::post('/batches/{batch}/retry', [\App\Http\Controllers\Admin\WhatsAppBatchController::class, 'retry'])->name('batches.retry');
            Route::post('/batches/{batch}/items/{item}/retry', [\App\Http\Controllers\Admin\WhatsAppBatchController::class, 'retryItem'])->name('batches.items.retry');
            Route::post('/batches/{batch}/cancel', [\App\Http\Controllers\Admin\WhatsAppBatchController::class, 'cancel'])->name('batches.cancel');
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
        Route::match(['put', 'patch', 'post'], '/invoices/{invoice}', [\App\Http\Controllers\Admin\InvoiceController::class, 'update'])->middleware('throttle:20,5')->name('invoices.update');
        Route::delete('/invoices/{invoice}', [\App\Http\Controllers\Admin\InvoiceController::class, 'destroy'])->middleware('throttle:10,1')->name('invoices.destroy');
        
        Route::resource('payments', \App\Http\Controllers\Admin\PaymentController::class)
            ->middleware('throttle:60,1')
            ->except(['update', 'destroy']);
        Route::match(['put', 'patch', 'post'], '/payments/{payment}', [\App\Http\Controllers\Admin\PaymentController::class, 'update'])->middleware('throttle:20,5')->name('payments.update');
        Route::delete('/payments/{payment}', [\App\Http\Controllers\Admin\PaymentController::class, 'destroy'])->middleware('throttle:10,1')->name('payments.destroy');
        
        Route::resource('transactions', \App\Http\Controllers\Admin\TransactionController::class)
            ->middleware('throttle:60,1')
            ->except(['update', 'destroy']);
        Route::match(['put', 'patch', 'post'], '/transactions/{transaction}', [\App\Http\Controllers\Admin\TransactionController::class, 'update'])->middleware('throttle:20,5')->name('transactions.update');
        Route::post('/transactions/{transaction}/refund', [\App\Http\Controllers\Admin\TransactionController::class, 'refund'])->middleware('throttle:10,5')->name('transactions.refund');
        Route::delete('/transactions/{transaction}', [\App\Http\Controllers\Admin\TransactionController::class, 'destroy'])->middleware('throttle:10,1')->name('transactions.destroy');
        
        Route::resource('wallets', \App\Http\Controllers\Admin\WalletController::class)
            ->middleware('throttle:60,1')
            ->except(['update', 'destroy']);
        Route::post('/wallets/transfer', [\App\Http\Controllers\Admin\WalletController::class, 'transfer'])->middleware('throttle:20,5')->name('wallets.transfer');
        Route::match(['put', 'patch', 'post'], '/wallets/{wallet}', [\App\Http\Controllers\Admin\WalletController::class, 'update'])->middleware('throttle:20,5')->name('wallets.update');
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
        Route::get('/accounting/instructor-accounts', [\App\Http\Controllers\Admin\InstructorAccountController::class, 'index'])->name('accounting.instructor-accounts.index');
        Route::get('/accounting/instructor-accounts/{instructor}', [\App\Http\Controllers\Admin\InstructorAccountController::class, 'show'])->name('accounting.instructor-accounts.show');

        Route::get('/accounting/hub', [\App\Http\Controllers\Admin\AccountingHubController::class, 'hub'])->name('accounting.hub');
        Route::get('/accounting/chart-of-accounts', [\App\Http\Controllers\Admin\AccountingHubController::class, 'chart'])->name('accounting.chart');
        Route::get('/accounting/insights', [\App\Http\Controllers\Admin\AccountingInsightsController::class, 'index'])->name('accounting.insights');
        Route::get('/accounting/insights/metrics', [\App\Http\Controllers\Admin\AccountingInsightsController::class, 'metrics'])->name('accounting.insights.metrics');
        Route::get('/accounting/receivables', [\App\Http\Controllers\Admin\AccountingReceivablesController::class, 'index'])->name('accounting.receivables');
        Route::post('/accounting/receivables/debts', [\App\Http\Controllers\Admin\AccountingReceivablesController::class, 'storeDebt'])->name('accounting.receivables.debts.store');
        Route::post('/accounting/receivables/debts/{accountingDebt}/repayment', [\App\Http\Controllers\Admin\AccountingReceivablesController::class, 'addRepayment'])->name('accounting.receivables.debts.repayment');
        Route::delete('/accounting/receivables/debts/{accountingDebt}', [\App\Http\Controllers\Admin\AccountingReceivablesController::class, 'cancelDebt'])->name('accounting.receivables.debts.cancel');
        Route::get('/accounting/installments', [\App\Http\Controllers\Admin\AccountingInstallmentsController::class, 'index'])->name('accounting.installments');
        Route::get('/accounting/gateway-operations', [\App\Http\Controllers\Admin\AccountingGatewayOperationsController::class, 'index'])->name('accounting.gateway-operations');

        Route::get('/accounting/reports', [\App\Http\Controllers\Admin\AccountingReportsController::class, 'index'])->name('accounting.reports');
        Route::get('/accounting/reports/export', [\App\Http\Controllers\Admin\AccountingReportsController::class, 'export'])->name('accounting.reports.export');
        Route::get('/accounting/reports/invoices', [\App\Http\Controllers\Admin\AccountingReportsController::class, 'invoices'])->name('accounting.reports.invoices');
        Route::get('/accounting/reports/payments', [\App\Http\Controllers\Admin\AccountingReportsController::class, 'payments'])->name('accounting.reports.payments');
        Route::get('/accounting/reports/transactions', [\App\Http\Controllers\Admin\AccountingReportsController::class, 'transactions'])->name('accounting.reports.transactions');
        Route::get('/accounting/reports/expenses', [\App\Http\Controllers\Admin\AccountingReportsController::class, 'expenses'])->name('accounting.reports.expenses');
        Route::get('/accounting/reports/wallets', [\App\Http\Controllers\Admin\AccountingReportsController::class, 'wallets'])->name('accounting.reports.wallets');
        Route::get('/accounting/reports/orders', [\App\Http\Controllers\Admin\AccountingReportsController::class, 'orders'])->name('accounting.reports.orders');

        // رواتب الموظفين — مسير شهري ودفع من المحفظة
        Route::prefix('employee-salaries')->name('employee-salaries.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\EmployeeSalaryPayrollController::class, 'index'])->name('index');
            Route::post('/generate', [\App\Http\Controllers\Admin\EmployeeSalaryPayrollController::class, 'generate'])->name('generate');
            Route::get('/export', [\App\Http\Controllers\Admin\EmployeeSalaryPayrollController::class, 'export'])->name('export');
            Route::post('/pay-batch', [\App\Http\Controllers\Admin\EmployeeSalaryPayrollController::class, 'payBatch'])->name('pay-batch');
            Route::get('/pay/{payment}', [\App\Http\Controllers\Admin\EmployeeSalaryPayrollController::class, 'pay'])->name('pay');
            Route::post('/pay/{payment}', [\App\Http\Controllers\Admin\EmployeeSalaryPayrollController::class, 'markPaid'])->name('mark-paid');
        });

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
            Route::get('agreements/manual-booking', [\App\Http\Controllers\Admin\InstallmentAgreementController::class, 'createManualBooking'])
                ->name('agreements.manual-booking');
            Route::post('agreements/manual-booking', [\App\Http\Controllers\Admin\InstallmentAgreementController::class, 'storeManualBooking'])
                ->name('agreements.manual-booking.store');
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
            Route::put('/payments/{payment}', [\App\Http\Controllers\Admin\InstructorAgreementController::class, 'updatePayment'])
                ->name('payments.update');
            Route::delete('/payments/{payment}', [\App\Http\Controllers\Admin\InstructorAgreementController::class, 'destroyPayment'])
                ->name('payments.destroy');
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
            Route::post('payments/{payment}/mark-paid', [\App\Http\Controllers\Admin\EmployeeAgreementController::class, 'markPaymentPaid'])->name('payments.mark-paid');
            Route::post('{employeeAgreement}/payments', [\App\Http\Controllers\Admin\EmployeeAgreementController::class, 'storePayment'])->name('payments.store');
            Route::post('{employeeAgreement}/stop', [\App\Http\Controllers\Admin\EmployeeAgreementController::class, 'stop'])
                ->middleware('throttle:20,5')
                ->name('stop');
            Route::post('{employeeAgreement}/reactivate', [\App\Http\Controllers\Admin\EmployeeAgreementController::class, 'reactivate'])
                ->middleware('throttle:20,5')
                ->name('reactivate');
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
        Route::get('/personal-branding', [\App\Http\Controllers\Admin\InstructorPersonalBrandingController::class, 'index'])->name('personal-branding.index');
        Route::resource('popup-ads', \App\Http\Controllers\Admin\PopupAdController::class)->except(['show']);
        Route::get('/personal-branding/{personal_branding}', [\App\Http\Controllers\Admin\InstructorPersonalBrandingController::class, 'show'])->name('personal-branding.show');
        Route::post('/personal-branding/{personal_branding}/approve', [\App\Http\Controllers\Admin\InstructorPersonalBrandingController::class, 'approve'])->name('personal-branding.approve');
        Route::post('/personal-branding/{personal_branding}/reject', [\App\Http\Controllers\Admin\InstructorPersonalBrandingController::class, 'reject'])->name('personal-branding.reject');
        Route::post('/personal-branding/{personal_branding}/send-back', [\App\Http\Controllers\Admin\InstructorPersonalBrandingController::class, 'sendBackForReview'])->name('personal-branding.send-back');
        Route::resource('coupons', \App\Http\Controllers\Admin\CouponController::class);
        // إدارة برامج الإحالات
        Route::resource('referral-programs', \App\Http\Controllers\Admin\ReferralProgramController::class);
        Route::get('workshop-promo-codes/preview-discount', [\App\Http\Controllers\Admin\WorkshopPromoCodeController::class, 'previewDiscount'])
            ->name('workshop-promo-codes.preview-discount');
        Route::post('workshop-promo-activations/{activation}/sales-task', [\App\Http\Controllers\Admin\WorkshopPromoCodeController::class, 'storeActivationSalesTask'])
            ->name('workshop-promo-activations.sales-task');
        Route::get('workshop-promo-codes/{workshop_promo_code}/export-activations', [\App\Http\Controllers\Admin\WorkshopPromoCodeController::class, 'exportActivations'])
            ->name('workshop-promo-codes.export-activations');
        Route::resource('workshop-promo-codes', \App\Http\Controllers\Admin\WorkshopPromoCodeController::class);
        
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
        Route::resource('learning-path-reviews', \App\Http\Controllers\Admin\LearningPathReviewController::class)
            ->only(['index', 'show', 'update', 'destroy'])
            ->parameters(['learning-path-reviews' => 'learningPathReview']);

        // إدارة المحاضرات (مسار الكورس قبل الـ resource لتفادي التعارض)
        Route::get('/lectures/course/{course}', [\App\Http\Controllers\Admin\LectureController::class, 'indexByCourse'])->name('lectures.by-course');
        Route::resource('lectures', \App\Http\Controllers\Admin\LectureController::class);

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
        Route::post('invoices/{invoice}/payment-proof', [\App\Http\Controllers\Student\InvoiceController::class, 'storePaymentProof'])
            ->name('invoices.payment-proof');
        Route::resource('wallet', \App\Http\Controllers\Student\WalletController::class)->only(['index', 'show']);
        Route::post('wallet/transfer', [\App\Http\Controllers\Student\WalletController::class, 'transfer'])->name('wallet.transfer');
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

        // التسويق الشخصي (البراندينغ) — ملف تعريفي للمدرب للمراجعة والنشر
        Route::get('/personal-branding', [\App\Http\Controllers\Instructor\PersonalBrandingController::class, 'edit'])->name('personal-branding.edit');
        Route::put('/personal-branding', [\App\Http\Controllers\Instructor\PersonalBrandingController::class, 'update'])->name('personal-branding.update');
        Route::post('/personal-branding/submit', [\App\Http\Controllers\Instructor\PersonalBrandingController::class, 'submit'])->name('personal-branding.submit');

        Route::resource('courses', \App\Http\Controllers\Instructor\CourseController::class)->only(['index', 'show']);
        Route::get('scholarships', [\App\Http\Controllers\Instructor\ScholarshipController::class, 'index'])->name('scholarships.index');
        Route::get('scholarships/students', [\App\Http\Controllers\Instructor\ScholarshipController::class, 'students'])->name('scholarships.students.index');
        Route::post('scholarships/registrations/{registration}/activate', [\App\Http\Controllers\Instructor\Scholarship\RegistrationController::class, 'activate'])->name('scholarships.registrations.activate');
        Route::post('scholarships/registrations/{registration}/deactivate', [\App\Http\Controllers\Instructor\Scholarship\RegistrationController::class, 'deactivate'])->name('scholarships.registrations.deactivate');
        Route::post('scholarships/registrations/{registration}/reject', [\App\Http\Controllers\Instructor\Scholarship\RegistrationController::class, 'reject'])->name('scholarships.registrations.reject');
        Route::get('scholarships/{program}', [\App\Http\Controllers\Instructor\ScholarshipController::class, 'show'])->name('scholarships.show');
        Route::get('courses/{course}/mind-map', [\App\Http\Controllers\Instructor\CourseMindMapController::class, 'edit'])->name('courses.mind-map.edit');
        Route::put('courses/{course}/mind-map', [\App\Http\Controllers\Instructor\CourseMindMapController::class, 'update'])->name('courses.mind-map.update');
        Route::get('online-group-courses', [\App\Http\Controllers\Instructor\OfflineCourseController::class, 'onlineIndex'])->name('online-group-courses.index');
        Route::get('online-group-courses/{offlineCourse}', [\App\Http\Controllers\Instructor\OfflineCourseController::class, 'onlineShow'])->name('online-group-courses.show');
        Route::resource('offline-courses', \App\Http\Controllers\Instructor\OfflineCourseController::class)->only(['index', 'show'])->parameters(['offline_course' => 'offlineCourse']);

        // تقويم المدرب (جلسات الأوفلاين)
        Route::get('/calendar', [\App\Http\Controllers\Instructor\OfflineCourseController::class, 'calendar'])->name('calendar');
        Route::get('/api/calendar/events', [\App\Http\Controllers\Instructor\OfflineCourseController::class, 'calendarEvents'])->name('calendar.events');
        // موارد ومحاضرات وأنشطة الكورسات الأوفلاين (واجهات منفصلة عن الأونلاين)
        Route::prefix('offline-courses/{offlineCourse}')->name('offline-courses.')->group(function () {
            Route::resource('resources', \App\Http\Controllers\Instructor\OfflineResourceController::class)->except(['show'])->parameters(['resource' => 'resource']);
            Route::get('lectures/sessions/{session}', [\App\Http\Controllers\Instructor\OfflineLectureController::class, 'showGroupSession'])->name('lectures.sessions.show');
            Route::resource('lectures', \App\Http\Controllers\Instructor\OfflineLectureController::class)->parameters(['lecture' => 'lecture']);
            Route::resource('activities', \App\Http\Controllers\Instructor\OfflineActivityController::class)->parameters(['activity' => 'activity']);
            Route::post('activities/{activity}/submissions/{submission}/grade', [\App\Http\Controllers\Instructor\OfflineActivityController::class, 'gradeSubmission'])->name('activities.submissions.grade');

            // حضور وغياب الكورس (موحّد للأوفلاين/الأونلاين حسب نفس المجموعة)
            Route::get('attendance', [\App\Http\Controllers\Instructor\OfflineCourseController::class, 'attendanceIndex'])->name('attendance.index');
            Route::get('attendance/sessions/{session}', [\App\Http\Controllers\Instructor\OfflineCourseController::class, 'attendanceSession'])->name('attendance.sessions.show');
            Route::post('attendance/mark', [\App\Http\Controllers\Instructor\OfflineCourseController::class, 'markAttendance'])->name('attendance.mark');

            // تقارير الطلاب
            Route::get('student-reports', [\App\Http\Controllers\Instructor\OfflineCourseController::class, 'studentReportsIndex'])->name('student-reports.index');
            Route::get('student-reports/{student}', [\App\Http\Controllers\Instructor\OfflineCourseController::class, 'studentReportsShow'])->name('student-reports.show');

            Route::prefix('curriculum')->name('curriculum.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Instructor\OfflineCurriculumController::class, 'index'])->name('index');
                Route::post('/sections', [\App\Http\Controllers\Instructor\OfflineCurriculumController::class, 'storeSection'])->name('sections.store');
                Route::put('/sections/{section}', [\App\Http\Controllers\Instructor\OfflineCurriculumController::class, 'updateSection'])->name('sections.update');
                Route::delete('/sections/{section}', [\App\Http\Controllers\Instructor\OfflineCurriculumController::class, 'destroySection'])->name('sections.destroy');
                Route::post('/sections/{section}/move', [\App\Http\Controllers\Instructor\OfflineCurriculumController::class, 'moveSection'])->name('sections.move');
                Route::post('/attach-item', [\App\Http\Controllers\Instructor\OfflineCurriculumController::class, 'attachItem'])->name('attach-item');
                Route::post('/sections/{section}/notes', [\App\Http\Controllers\Instructor\OfflineCurriculumController::class, 'storeNote'])->name('sections.notes.store');
                Route::post('/items/{item}/move', [\App\Http\Controllers\Instructor\OfflineCurriculumController::class, 'moveItem'])->name('items.move');
                Route::delete('/items/{item}', [\App\Http\Controllers\Instructor\OfflineCurriculumController::class, 'destroyItem'])->name('items.destroy');
            });
        });
        Route::get('courses/{course}/curriculum', [\App\Http\Controllers\Instructor\CurriculumController::class, 'index'])->name('courses.curriculum');
        Route::post('courses/{course}/curriculum/exams', [\App\Http\Controllers\Instructor\CurriculumController::class, 'storeExamFromCurriculum'])->name('courses.curriculum.exams.store');
        Route::post('courses/{course}/curriculum/assignments', [\App\Http\Controllers\Instructor\CurriculumController::class, 'storeAssignmentFromCurriculum'])->name('courses.curriculum.assignments.store');
        Route::post('courses/{course}/sections', [\App\Http\Controllers\Instructor\CurriculumController::class, 'storeSection'])->name('courses.sections.store');
        Route::put('sections/{section}', [\App\Http\Controllers\Instructor\CurriculumController::class, 'updateSection'])->name('sections.update');
        Route::delete('sections/{section}', [\App\Http\Controllers\Instructor\CurriculumController::class, 'destroySection'])->name('sections.destroy');
        Route::post('sections/{section}/items', [\App\Http\Controllers\Instructor\CurriculumController::class, 'addItem'])->name('sections.items.store');
        Route::delete('curriculum-items/{item}', [\App\Http\Controllers\Instructor\CurriculumController::class, 'removeItem'])->name('curriculum-items.destroy');
        Route::post('curriculum-items/{item}/move', [\App\Http\Controllers\Instructor\CurriculumController::class, 'moveItem'])->name('curriculum-items.move');
        Route::post('courses/{course}/sections/order', [\App\Http\Controllers\Instructor\CurriculumController::class, 'updateSectionsOrder'])->name('courses.sections.order');
        Route::post('sections/{section}/items/order', [\App\Http\Controllers\Instructor\CurriculumController::class, 'updateItemsOrder'])->name('sections.items.order');
        Route::get('lectures/{lecture}/video-questions', [\App\Http\Controllers\Instructor\LectureVideoQuestionController::class, 'index'])->name('lectures.video-questions.index');
        Route::post('lectures/{lecture}/video-questions', [\App\Http\Controllers\Instructor\LectureVideoQuestionController::class, 'store'])->name('lectures.video-questions.store');
        Route::delete('lectures/{lecture}/video-questions/{videoQuestion}', [\App\Http\Controllers\Instructor\LectureVideoQuestionController::class, 'destroy'])->name('lectures.video-questions.destroy');
        
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
            Route::get('/{agreement}/export-activations', [\App\Http\Controllers\Instructor\AgreementController::class, 'exportActivations'])->name('export-activations');
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
