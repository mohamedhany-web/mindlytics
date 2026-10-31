<?php

use App\Domains\Community\Http\Controllers\Portal\AuthController;
use App\Domains\Community\Http\Controllers\Portal\CommunityPageController;
use App\Domains\Community\Http\Controllers\Portal\ContributorController;
use App\Domains\Community\Http\Controllers\Portal\DashboardController;
use App\Domains\Community\Http\Controllers\Public\CommunityController;
use App\Domains\Community\Http\Controllers\Public\JourneyController;
use App\Domains\Community\Http\Controllers\Public\JourneyShareCardController;
use App\Domains\Community\Http\Controllers\Public\PortfolioController;
use Illuminate\Support\Facades\Route;

/**
 * البورتفوليو والرحلة العامة وبوابة المجتمع العامة.
 * يُحمَّل من routes/web/public.php في نفس موضعه القديم.
 */
Route::get('/portfolio', [PortfolioController::class, 'index'])->name('public.portfolio.index');
Route::get('/portfolio/talent', [PortfolioController::class, 'talent'])->name('public.portfolio.talent');
Route::get('/portfolio/{id}/share-card', [JourneyShareCardController::class, 'project'])->name('public.portfolio.share-card')->where('id', '[0-9]+');
Route::get('/portfolio/{id}', [PortfolioController::class, 'show'])->name('public.portfolio.show')->where('id', '[0-9]+');
Route::get('/j/{slug}/share-card', [JourneyShareCardController::class, 'profile'])->name('public.journey.share-card')->where('slug', '[A-Za-z0-9\-]+');
Route::get('/j/{slug}', [JourneyController::class, 'show'])->name('public.journey.show')->where('slug', '[A-Za-z0-9\-]+');
Route::post('/journey/share-track', [JourneyShareCardController::class, 'trackShare'])->name('public.journey.share-track');

// مجتمع البيانات والذكاء الاصطناعي (مسابقات، داتاسيت، مجتمع)
Route::get('/community', [CommunityController::class, 'index'])->name('public.community.index');

// مصادقة مجتمع البيانات (تسجيل دخول وإنشاء حساب منفصلان - نفس المستخدمين)
Route::prefix('community')->name('community.')->group(function () {
    // صفحة المساهمين عامة (بدون تسجيل دخول)
    Route::get('/contributors', [CommunityPageController::class, 'contributors'])->name('contributors.index');
    Route::get('/contributors/{user}', [CommunityPageController::class, 'contributorShow'])->name('contributors.show');
    // صفحة البيانات عامة (بدون تسجيل — استكشاف آلاف مجموعات البيانات)
    Route::get('/data', [CommunityPageController::class, 'publicDatasets'])->name('data.index');
    Route::get('/data/{dataset}', [CommunityPageController::class, 'publicDatasetShow'])->name('data.show');
    Route::get('/data/{dataset}/download-all', [CommunityPageController::class, 'datasetDownloadAll'])->name('data.download-all');
    Route::get('/data/{dataset}/download/{index}', [CommunityPageController::class, 'datasetDownloadFile'])->name('data.download-file')->whereNumber('index');
    Route::get('/data/{dataset}/download', [CommunityPageController::class, 'datasetDownload'])->name('data.download');
    Route::get('/data/{dataset}/preview', [CommunityPageController::class, 'datasetPreview'])->name('data.preview');
    Route::get('/data/{dataset}/preview-zip-entry', [CommunityPageController::class, 'datasetPreviewZipEntry'])->name('data.preview-zip-entry');
    Route::get('/data/{dataset}/zip-contents', [CommunityPageController::class, 'datasetZipContents'])->name('data.zip-contents');
    // صفحة النماذج (Model Zoo) عامة — بدون تسجيل (مثل البيانات)
    Route::get('/models', [CommunityPageController::class, 'publicModels'])->name('models.index');
    Route::get('/models/{model}', [CommunityPageController::class, 'publicModelShow'])->name('models.show');
    Route::get('/models/{model}/download', [CommunityPageController::class, 'publicModelDownload'])->name('models.download');
    Route::get('/models/{model}/download/{index}', [CommunityPageController::class, 'publicModelDownloadFile'])->name('models.download-file')->whereNumber('index');
    Route::get('/models/{model}/file/{index}/preview', [CommunityPageController::class, 'publicModelFilePreview'])->name('models.file-preview')->whereNumber('index');
    Route::middleware(['guest', 'guest-only'])->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:20,15')->name('login.post');
        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register')->name('register.post');
    });
    Route::middleware(['auth', 'prevent-concurrent'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/competitions', [CommunityPageController::class, 'competitions'])->name('competitions.index');
        Route::get('/datasets', [CommunityPageController::class, 'datasets'])->name('datasets.index');
        Route::get('/datasets/{dataset}', [CommunityPageController::class, 'datasetShow'])->name('datasets.show');
        Route::get('/datasets/{dataset}/download', [CommunityPageController::class, 'datasetDownload'])->name('datasets.download');
        Route::get('/discussions', [CommunityPageController::class, 'discussions'])->name('discussions.index');

        // لوحة تحكم المساهمين (للمستخدمين الذين is_community_contributor = true فقط)
        Route::middleware('community.contributor')->prefix('contributor')->name('contributor.')->group(function () {
            Route::get('/dashboard', [ContributorController::class, 'dashboard'])->name('dashboard');
            Route::get('/profile', [ContributorController::class, 'profileEdit'])->name('profile.edit');
            Route::post('/profile', [ContributorController::class, 'profileStore'])->name('profile.store');
            Route::get('/datasets', [ContributorController::class, 'datasets'])->name('datasets.index');
            Route::get('/datasets/create', [ContributorController::class, 'createDataset'])->name('datasets.create');
            Route::post('/datasets', [ContributorController::class, 'storeDataset'])->name('datasets.store');
            Route::get('/models', [ContributorController::class, 'models'])->name('models.index');
            Route::get('/models/create', [ContributorController::class, 'createModel'])->name('models.create');
            Route::post('/models', [ContributorController::class, 'storeModel'])->name('models.store');
        });
    });
});
