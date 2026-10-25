<?php

use Illuminate\Support\Facades\Route;

/**
 * Public marketing, catalog, checkout, community, and content pages.
 * Loaded by routes/web.php. Route order is part of matching behavior.
 */

// Careers (Public) — التوظيف
require app_path('Domains/Hr/routes/public.php');

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
        $courses = \App\Domains\Learning\Models\AdvancedCourse::where('is_active', true)
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
        $posts = \App\Domains\Cms\Models\BlogPost::where('is_published', true)
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
require app_path('Domains/Community/routes/public/challenges.php');
require app_path('Domains/Learning/routes/public/certificates.php');
Route::get('/help', [\App\Http\Controllers\Public\PageController::class, 'help'])->name('public.help');
Route::get('/refund', [\App\Http\Controllers\Public\PageController::class, 'refund'])->name('public.refund');
Route::get('/testimonials', [\App\Http\Controllers\Public\PageController::class, 'testimonials'])->name('public.testimonials');
Route::get('/events', [\App\Http\Controllers\Public\PageController::class, 'events'])->name('public.events');
Route::get('/partners', [\App\Http\Controllers\Public\PageController::class, 'partners'])->name('public.partners');
require app_path('Domains/Investment/routes/public.php');
Route::get('/groups', [\App\Http\Controllers\Public\PageController::class, 'groups'])->name('public.groups');
Route::get('/bookings', [\App\Http\Controllers\Public\PageController::class, 'bookings'])->middleware('auth')->name('public.bookings');

// المدونة
require app_path('Domains/Cms/routes/public/blog.php');

// Mindlytics Journey / Portfolio (معرض مشاريع + دليل مواهب + رحلة الطالب + Share Cards)
require app_path('Domains/Community/routes/public/community.php');

// التواصل
require app_path('Domains/Cms/routes/public/contact-media.php');

// صفحة الكورسات العامة
require app_path('Domains/Learning/routes/public/catalog.php');

require app_path('Domains/Sales/routes/public.php');

require app_path('Domains/Learning/routes/public/course-reviews.php');

// استبيان عملاء الأكاديمية (لمن اشترى كورساً) — يمنح كوبون خصم شخصي
require app_path('Domains/Marketing/routes/public/customer-survey.php');

// صفحة إتمام الطلب (Checkout) — متاحة للزائر مع تسجيل سريع داخل الصفحة
require app_path('Domains/Accounting/routes/public/course-checkout.php');

// صفحة المسارات التعليمية
require app_path('Domains/Learning/routes/public/learning-paths.php');

// صفحة إتمام الطلب للمسارات التعليمية (Checkout)
require app_path('Domains/Accounting/routes/public/learning-path-checkout.php');

// صفحة حجز ورش العمل العامة (workshops)
require app_path('Domains/Offline/routes/public.php');

// استقبال العودة من بوابة كاشير بعد الدفع (بدون auth لأن كاشير يوجّه المتصفح هنا)
require app_path('Domains/Accounting/routes/public/kashier-callback.php');

// الاشتراك في مسار تعليمي (للتوافق مع الكود القديم - سيتم توجيهه للـ checkout إذا كان مدفوع)
require app_path('Domains/Learning/routes/public/learning-path-enroll.php');

// صفحة تفاصيل الباقة (للتوافق مع الروابط القديمة)
Route::get('/package/{slug}', function ($slug) {
    $package = \App\Domains\Accounting\Models\Package::where('slug', $slug)
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
    $relatedPackages = \App\Domains\Accounting\Models\Package::where('is_active', true)
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
