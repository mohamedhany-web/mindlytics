<?php

use App\Domains\Community\Http\Controllers\Student\PortfolioProjectController;
use Illuminate\Support\Facades\Route;

/**
 * بورتفوليو الطالب.
 * يُحمَّل من routes/web/student.php في نفس موضعه القديم.
 */
Route::prefix('my-portfolio')->name('student.')->group(function () {
    Route::get('/', [PortfolioProjectController::class, 'index'])->name('portfolio.index');
    Route::get('/journey', [PortfolioProjectController::class, 'editJourney'])->name('portfolio.journey');
    Route::put('/journey', [PortfolioProjectController::class, 'updateJourney'])->name('portfolio.journey.update');
    Route::get('/create', [PortfolioProjectController::class, 'create'])->name('portfolio.create');
    Route::post('/', [PortfolioProjectController::class, 'store'])->name('portfolio.store');
    Route::get('/{project}/edit', [PortfolioProjectController::class, 'edit'])->name('portfolio.edit');
    Route::put('/{project}', [PortfolioProjectController::class, 'update'])->name('portfolio.update');
});
