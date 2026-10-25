<?php

use App\Domains\Accounting\Http\Controllers\Admin\PackageController;
use Illuminate\Support\Facades\Route;

/**
 * الأسعار والباقات.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::resource('packages', PackageController::class);
Route::post('/packages/{course}/update-price', [PackageController::class, 'updatePrice'])->name('packages.update-price');
Route::post('/packages/bulk-update', [PackageController::class, 'updateBulkPrices'])->name('packages.bulk-update');
