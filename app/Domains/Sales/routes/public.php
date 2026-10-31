<?php

use App\Domains\Sales\Http\Controllers\Public\SalesCourseBoardLandingController;
use App\Domains\Sales\Http\Controllers\Public\SalesDiplomaBoardLandingController;
use Illuminate\Support\Facades\Route;

/**
 * صفحات الكورسات والدبلومات العامة لروابط المبيعات.
 * يُحمَّل من routes/web/public.php في نفس موضعه القديم.
 */
Route::get('/course-info/{slug}', [SalesCourseBoardLandingController::class, 'show'])
    ->name('public.sales-course-board.show');

Route::get('/diploma-info/{slug}', [SalesDiplomaBoardLandingController::class, 'show'])
    ->name('public.sales-diploma-board.show');
