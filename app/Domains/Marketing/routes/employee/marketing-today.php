<?php

use App\Domains\Marketing\Http\Controllers\Employee\MarketingTodayController;
use Illuminate\Support\Facades\Route;

/**
 * مهام التسويق اليومية.
 * يُحمَّل من داخل مجموعة employee في routes/web/employee.php (auth + employee.work) وفي نفس موضعه القديم.
 */
Route::get('/marketing-today', [MarketingTodayController::class, 'index'])->name('marketing-today.index');
Route::post('/marketing-today/{event}/confirm', [MarketingTodayController::class, 'confirm'])->name('marketing-today.confirm');
