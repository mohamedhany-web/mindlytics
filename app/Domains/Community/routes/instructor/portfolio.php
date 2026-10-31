<?php

use App\Domains\Community\Http\Controllers\Instructor\PortfolioReviewController;
use Illuminate\Support\Facades\Route;

/**
 * مراجعة بورتفوليو الطلاب من المدرب.
 * يُحمَّل من داخل مجموعة instructor في routes/web/instructor.php وفي نفس موضعه القديم.
 */
Route::prefix('portfolio')->name('portfolio.')->group(function () {
    Route::get('/', [PortfolioReviewController::class, 'index'])->name('index');
    Route::get('/{project}', [PortfolioReviewController::class, 'show'])->name('show');
    Route::post('/{project}/approve', [PortfolioReviewController::class, 'approve'])->name('approve');
    Route::post('/{project}/reject', [PortfolioReviewController::class, 'reject'])->name('reject');
    Route::post('/{project}/request-changes', [PortfolioReviewController::class, 'requestChanges'])->name('request-changes');
    Route::post('/{project}/publish', [PortfolioReviewController::class, 'publish'])->name('publish');
    Route::post('/{project}/toggle-featured', [PortfolioReviewController::class, 'toggleFeatured'])->name('toggle-featured');
});
