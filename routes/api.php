<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\MetaController;
use App\Http\Controllers\Api\V1\StudentHomeController;
use App\Http\Controllers\Api\V1\StudentLearnController;
use App\Http\Controllers\Api\V1\StudentNotificationsController;
use App\Http\Controllers\Api\V1\StudentPracticeController;
use App\Http\Controllers\Api\V1\StudentCommunityController;
use App\Http\Controllers\Api\V1\StudentProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mindlytics API (JSON) — للموبايل؛ عقد OpenAPI: docs/platform-api/contracts/openapi.yaml
|--------------------------------------------------------------------------
| المسارات هنا تُحمَّل تلقائياً تحت البادئة /api (انظر bootstrap/app.php).
*/

Route::prefix('v1')->group(function () {
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'service' => 'mindlytics-api',
            'version' => 'v1',
        ]);
    })->name('api.v1.health');

    Route::get('/meta/phone-countries', [MetaController::class, 'phoneCountries'])
        ->name('api.v1.meta.phone-countries');

    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:20,15')
        ->name('api.v1.auth.login');

    Route::post('/auth/register', [AuthController::class, 'register'])
        ->middleware('throttle:5,1')
        ->name('api.v1.auth.register');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/user', [AuthController::class, 'user'])->name('api.v1.auth.user');
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.v1.auth.logout');
    });

    Route::middleware(['auth:sanctum', 'api.student'])->prefix('student')->group(function () {
        Route::get('/home', [StudentHomeController::class, 'home'])
            ->middleware('throttle:60,1')
            ->name('api.v1.student.home');
    });

    Route::middleware(['auth:sanctum', 'api.student'])->prefix('student/learn')->group(function () {
        Route::get('/courses', [StudentLearnController::class, 'courses'])
            ->middleware('throttle:90,1')
            ->name('api.v1.student.learn.courses');
        Route::get('/courses/{course}', [StudentLearnController::class, 'courseOutline'])
            ->middleware('throttle:90,1')
            ->name('api.v1.student.learn.course-outline');
        Route::get('/assignments', [StudentLearnController::class, 'assignments'])
            ->middleware('throttle:90,1')
            ->name('api.v1.student.learn.assignments');
        Route::get('/exams', [StudentLearnController::class, 'exams'])
            ->middleware('throttle:90,1')
            ->name('api.v1.student.learn.exams');
    });

    Route::middleware(['auth:sanctum', 'api.student'])->prefix('student/practice')->group(function () {
        Route::get('/patterns', [StudentPracticeController::class, 'patterns'])
            ->middleware('throttle:90,1')
            ->name('api.v1.student.practice.patterns');
        Route::get('/patterns/{pattern}', [StudentPracticeController::class, 'pattern'])
            ->middleware('throttle:90,1')
            ->name('api.v1.student.practice.pattern');
    });

    Route::middleware(['auth:sanctum', 'api.student'])->prefix('student/community')->group(function () {
        Route::get('/courses', [StudentCommunityController::class, 'courses'])
            ->middleware('throttle:90,1')
            ->name('api.v1.student.community.courses');
        Route::get('/courses/{course}/feed', [StudentCommunityController::class, 'feed'])
            ->middleware('throttle:90,1')
            ->name('api.v1.student.community.feed');
        Route::post('/courses/{course}/posts', [StudentCommunityController::class, 'createPost'])
            ->middleware('throttle:30,1')
            ->name('api.v1.student.community.posts.create');
        Route::get('/posts/{post}', [StudentCommunityController::class, 'post'])
            ->middleware('throttle:90,1')
            ->name('api.v1.student.community.posts.show');
        Route::post('/posts/{post}/comments', [StudentCommunityController::class, 'createComment'])
            ->middleware('throttle:60,1')
            ->name('api.v1.student.community.comments.create');
        Route::post('/posts/{post}/react', [StudentCommunityController::class, 'reactToPost'])
            ->middleware('throttle:120,1')
            ->name('api.v1.student.community.posts.react');
        Route::delete('/posts/{post}/react', [StudentCommunityController::class, 'unreactToPost'])
            ->middleware('throttle:120,1')
            ->name('api.v1.student.community.posts.unreact');
    });

    Route::middleware(['auth:sanctum', 'api.student'])->prefix('student/notifications')->group(function () {
        Route::get('/', [StudentNotificationsController::class, 'index'])
            ->middleware('throttle:60,1')
            ->name('api.v1.student.notifications.index');
        Route::get('/unread-count', [StudentNotificationsController::class, 'unreadCount'])
            ->middleware('throttle:120,1')
            ->name('api.v1.student.notifications.unread-count');
        Route::post('/mark-all-read', [StudentNotificationsController::class, 'markAllRead'])
            ->middleware('throttle:20,1')
            ->name('api.v1.student.notifications.mark-all-read');
        Route::post('/{notification}/mark-read', [StudentNotificationsController::class, 'markRead'])
            ->middleware('throttle:60,1')
            ->name('api.v1.student.notifications.mark-read');
    });

    Route::middleware(['auth:sanctum', 'api.student'])->prefix('student/profile')->group(function () {
        Route::get('/', [StudentProfileController::class, 'show'])
            ->middleware('throttle:120,1')
            ->name('api.v1.student.profile.show');
        Route::put('/', [StudentProfileController::class, 'update'])
            ->middleware('throttle:30,1')
            ->name('api.v1.student.profile.update');
        Route::post('/photo', [StudentProfileController::class, 'uploadPhoto'])
            ->middleware('throttle:20,1')
            ->name('api.v1.student.profile.photo');
    });
});
