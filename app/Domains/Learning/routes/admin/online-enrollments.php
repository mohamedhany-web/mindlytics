<?php

use App\Domains\Learning\Http\Controllers\Admin\StudentEnrollmentController;
use App\Domains\Learning\Http\Controllers\Admin\VideoProviderController;
use Illuminate\Support\Facades\Route;

/**
 * التسجيلات الأونلاين.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::prefix('online-enrollments')->name('online-enrollments.')->group(function () {
    Route::get('/', [StudentEnrollmentController::class, 'index'])->name('index');
    Route::get('/export', [StudentEnrollmentController::class, 'export'])->name('export');
    Route::get('/export-pdf', [StudentEnrollmentController::class, 'exportPdf'])->name('export-pdf');
    Route::post('/resync-progress', [StudentEnrollmentController::class, 'resyncProgress'])->name('resync-progress');
    Route::get('/create', [StudentEnrollmentController::class, 'create'])->name('create');
    Route::post('/', [StudentEnrollmentController::class, 'store'])->name('store');
    Route::post('/quick-activate', [StudentEnrollmentController::class, 'quickActivate'])->name('quick-activate');
    Route::get('/{enrollment}', [StudentEnrollmentController::class, 'show'])->name('show');
    Route::post('/{enrollment}/activate', [StudentEnrollmentController::class, 'activate'])->name('activate');
    Route::post('/{enrollment}/deactivate', [StudentEnrollmentController::class, 'deactivate'])->name('deactivate');
    Route::post('/{enrollment}/update-progress', [StudentEnrollmentController::class, 'updateProgress'])->name('update-progress');
    Route::post('/{enrollment}/update-notes', [StudentEnrollmentController::class, 'updateNotes'])->name('update-notes');
    Route::delete('/{enrollment}', [StudentEnrollmentController::class, 'destroy'])->name('destroy');
    Route::get('/search/by-phone', [StudentEnrollmentController::class, 'searchStudentByPhone'])->name('search-by-phone');
});

// إدارة مصادر الفيديو (Bunny وغيرها)
Route::prefix('video-providers')->name('video-providers.')->group(function () {
    Route::get('/', [VideoProviderController::class, 'index'])->name('index');
    Route::post('/', [VideoProviderController::class, 'store'])->name('store');
    Route::put('/{videoProvider}', [VideoProviderController::class, 'update'])->name('update');
});
