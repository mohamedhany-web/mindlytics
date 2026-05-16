<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\MetaController;
use App\Http\Controllers\Api\V1\StudentHomeController;
use App\Http\Controllers\Api\V1\StudentLearnController;
use App\Http\Controllers\Api\V1\StudentNotificationsController;
use App\Http\Controllers\Api\V1\StudentPracticeController;
use App\Http\Controllers\Api\V1\StudentCommunityController;
use App\Http\Controllers\Api\V1\StudentProfileController;
use App\Http\Controllers\Api\V1\StudentPeersController;
use App\Http\Controllers\Api\V1\StudentGroupsController;
use App\Http\Controllers\Api\V1\StudentGroupChatController;
use App\Http\Controllers\Api\V1\StudentPeerChatController;
use App\Http\Controllers\Api\V1\StudentChallengesController;
use App\Http\Controllers\Api\V1\InstructorCoursesController;
use App\Http\Controllers\Api\V1\InstructorCommunityController;
use App\Http\Controllers\Api\V1\InstructorAnnouncementsController;
use App\Http\Controllers\Api\V1\InstructorMessagesController;
use App\Http\Controllers\Api\V1\InstructorAssignmentsController;
use App\Http\Controllers\Api\V1\SupportTicketsController;
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

    Route::middleware('auth:sanctum')->prefix('support')->group(function () {
        Route::get('/tickets', [SupportTicketsController::class, 'index'])
            ->middleware('throttle:90,1')
            ->name('api.v1.support.tickets.index');
        Route::post('/tickets', [SupportTicketsController::class, 'store'])
            ->middleware('throttle:30,1')
            ->name('api.v1.support.tickets.store');
        Route::get('/tickets/{ticket}', [SupportTicketsController::class, 'show'])
            ->middleware('throttle:120,1')
            ->name('api.v1.support.tickets.show');
        Route::post('/tickets/{ticket}/reply', [SupportTicketsController::class, 'reply'])
            ->middleware('throttle:120,1')
            ->name('api.v1.support.tickets.reply');
    });

    Route::middleware(['auth:sanctum', 'api.student'])->prefix('student')->group(function () {
        Route::get('/home', [StudentHomeController::class, 'home'])
            ->middleware('throttle:60,1')
            ->name('api.v1.student.home');
        Route::get('/challenges', [StudentChallengesController::class, 'index'])
            ->middleware('throttle:60,1')
            ->name('api.v1.student.challenges.index');
        Route::get('/groups', [StudentGroupsController::class, 'index'])
            ->middleware('throttle:60,1')
            ->name('api.v1.student.groups.index');
        Route::prefix('group-chat')->group(function () {
            Route::get('/groups/{group}/messages', [StudentGroupChatController::class, 'messages'])
                ->middleware('throttle:120,1')
                ->name('api.v1.student.group-chat.messages');
            Route::post('/groups/{group}/messages', [StudentGroupChatController::class, 'send'])
                ->middleware('throttle:60,1')
                ->name('api.v1.student.group-chat.send');
        });
        Route::prefix('dm')->group(function () {
            Route::get('/threads', [StudentPeerChatController::class, 'threads'])
                ->middleware('throttle:60,1')
                ->name('api.v1.student.dm.threads');
            Route::get('/with/{peer}/messages', [StudentPeerChatController::class, 'messages'])
                ->middleware('throttle:120,1')
                ->name('api.v1.student.dm.messages');
            Route::post('/with/{peer}/messages', [StudentPeerChatController::class, 'send'])
                ->middleware('throttle:60,1')
                ->name('api.v1.student.dm.send');
        });
    });

    Route::middleware(['auth:sanctum', 'api.instructor'])->prefix('instructor')->group(function () {
        Route::get('/courses', [InstructorCoursesController::class, 'index'])
            ->middleware('throttle:60,1')
            ->name('api.v1.instructor.courses.index');
        Route::get('/courses/{course}/students', [InstructorCoursesController::class, 'students'])
            ->middleware('throttle:60,1')
            ->name('api.v1.instructor.courses.students');

        Route::get('/courses/{course}/announcements', [InstructorAnnouncementsController::class, 'index'])
            ->middleware('throttle:60,1')
            ->name('api.v1.instructor.courses.announcements.index');
        Route::post('/courses/{course}/announcements', [InstructorAnnouncementsController::class, 'store'])
            ->middleware('throttle:30,1')
            ->name('api.v1.instructor.courses.announcements.store');

        Route::prefix('community')->group(function () {
            Route::get('/courses', [InstructorCommunityController::class, 'courses'])
                ->middleware('throttle:60,1')
                ->name('api.v1.instructor.community.courses');
            Route::get('/courses/{course}/feed', [InstructorCommunityController::class, 'feed'])
                ->middleware('throttle:90,1')
                ->name('api.v1.instructor.community.feed');
            Route::post('/courses/{course}/posts', [InstructorCommunityController::class, 'createPost'])
                ->middleware('throttle:30,1')
                ->name('api.v1.instructor.community.posts.create');
            Route::get('/posts/{post}', [InstructorCommunityController::class, 'post'])
                ->middleware('throttle:90,1')
                ->name('api.v1.instructor.community.posts.show');
            Route::post('/posts/{post}/comments', [InstructorCommunityController::class, 'createComment'])
                ->middleware('throttle:60,1')
                ->name('api.v1.instructor.community.comments.create');
            Route::post('/posts/{post}/react', [InstructorCommunityController::class, 'reactToPost'])
                ->middleware('throttle:120,1')
                ->name('api.v1.instructor.community.posts.react');
            Route::delete('/posts/{post}/react', [InstructorCommunityController::class, 'unreactToPost'])
                ->middleware('throttle:120,1')
                ->name('api.v1.instructor.community.posts.unreact');
        });

        Route::prefix('messages')->group(function () {
            Route::get('/threads', [InstructorMessagesController::class, 'threads'])
                ->middleware('throttle:90,1')
                ->name('api.v1.instructor.messages.threads');
            Route::post('/threads', [InstructorMessagesController::class, 'startThread'])
                ->middleware('throttle:60,1')
                ->name('api.v1.instructor.messages.threads.start');
            Route::get('/threads/{thread}/messages', [InstructorMessagesController::class, 'messages'])
                ->middleware('throttle:120,1')
                ->name('api.v1.instructor.messages.messages');
            Route::post('/threads/{thread}/messages', [InstructorMessagesController::class, 'send'])
                ->middleware('throttle:120,1')
                ->name('api.v1.instructor.messages.send');
        });

        Route::prefix('assignments')->group(function () {
            Route::get('/', [InstructorAssignmentsController::class, 'assignments'])
                ->middleware('throttle:90,1')
                ->name('api.v1.instructor.assignments.index');
            Route::get('/{assignment}/submissions', [InstructorAssignmentsController::class, 'submissions'])
                ->middleware('throttle:120,1')
                ->name('api.v1.instructor.assignments.submissions');
        });
        Route::put('/submissions/{submission}', [InstructorAssignmentsController::class, 'grade'])
            ->middleware('throttle:120,1')
            ->name('api.v1.instructor.assignments.submissions.grade');
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

    Route::middleware(['auth:sanctum', 'api.student'])->prefix('student/peers')->group(function () {
        Route::get('/recommended', [StudentPeersController::class, 'recommended'])
            ->middleware('throttle:90,1')
            ->name('api.v1.student.peers.recommended');
        Route::get('/incoming', [StudentPeersController::class, 'incoming'])
            ->middleware('throttle:60,1')
            ->name('api.v1.student.peers.incoming');
        Route::get('/{user}/social-state', [StudentPeersController::class, 'socialState'])
            ->middleware('throttle:120,1')
            ->name('api.v1.student.peers.social-state');
        Route::post('/{user}/connect', [StudentPeersController::class, 'connect'])
            ->middleware('throttle:30,1')
            ->name('api.v1.student.peers.connect');
        Route::post('/{user}/cancel-outgoing', [StudentPeersController::class, 'cancelOutgoing'])
            ->middleware('throttle:30,1')
            ->name('api.v1.student.peers.cancel-outgoing');
        Route::post('/{user}/accept', [StudentPeersController::class, 'accept'])
            ->middleware('throttle:30,1')
            ->name('api.v1.student.peers.accept');
        Route::post('/{user}/decline', [StudentPeersController::class, 'decline'])
            ->middleware('throttle:30,1')
            ->name('api.v1.student.peers.decline');
        Route::get('/{user}', [StudentPeersController::class, 'show'])
            ->middleware('throttle:120,1')
            ->name('api.v1.student.peers.show');
    });
});
