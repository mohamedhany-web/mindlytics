<?php

use App\Domains\Offline\Http\Controllers\Public\OfflineGroupPublicBookingController;
use App\Domains\Offline\Http\Controllers\Public\WorkshopPublicController;
use Illuminate\Support\Facades\Route;

/**
 * صفحات الورش العامة وحجز المجموعات الأوفلاين/الأونلاين.
 * يُحمَّل من routes/web/public.php في نفس موضعه القديم.
 */
Route::get('/workshops/{slug}', [WorkshopPublicController::class, 'show'])
    ->name('public.workshops.show');
Route::post('/workshops/{slug}/register', [WorkshopPublicController::class, 'register'])
    ->name('public.workshops.register');
Route::get('/workshops/{slug}/confirm', [WorkshopPublicController::class, 'showConfirm'])
    ->name('public.workshops.confirm.show');
Route::post('/workshops/{slug}/confirm', [WorkshopPublicController::class, 'confirmAttendance'])
    ->name('public.workshops.confirm.store');

// حجز مجموعة كورس أوفلاين (رابط عام — تصميم مشابه لصفحة الدفع)
Route::get('/offline-groups/{slug}', [OfflineGroupPublicBookingController::class, 'show'])
    ->where('slug', '[a-z0-9-]+')
    ->name('public.offline-groups.show');
Route::post('/offline-groups/{slug}/book', [OfflineGroupPublicBookingController::class, 'store'])
    ->middleware('auth')
    ->where('slug', '[a-z0-9-]+')
    ->name('public.offline-groups.book');

// حجز مجموعة كورس أونلاين (موازي للأوفلاين وبفصل كامل في المقاعد والحجوزات)
Route::get('/online-groups/{slug}', [OfflineGroupPublicBookingController::class, 'showOnline'])
    ->where('slug', '[a-z0-9-]+')
    ->name('public.online-groups.show');
Route::post('/online-groups/{slug}/book', [OfflineGroupPublicBookingController::class, 'storeOnline'])
    ->middleware('auth')
    ->where('slug', '[a-z0-9-]+')
    ->name('public.online-groups.book');
