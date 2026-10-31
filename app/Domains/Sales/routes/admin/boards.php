<?php

use App\Domains\Sales\Http\Controllers\Admin\SalesCourseBoardController;
use App\Domains\Sales\Http\Controllers\Admin\SalesDiplomaBoardController;
use Illuminate\Support\Facades\Route;

/**
 * لوحات محتوى المبيعات: الكورسات والدبلومات.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::resource('sales-course-board', SalesCourseBoardController::class)->except(['show']);
Route::resource('sales-diploma-board', SalesDiplomaBoardController::class)->except(['show']);
