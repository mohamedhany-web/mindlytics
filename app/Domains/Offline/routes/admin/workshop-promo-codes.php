<?php

use App\Domains\Offline\Http\Controllers\Admin\WorkshopPromoCodeController;
use Illuminate\Support\Facades\Route;

/**
 * أكواد خصم الورش.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::get('workshop-promo-codes/preview-discount', [WorkshopPromoCodeController::class, 'previewDiscount'])
    ->name('workshop-promo-codes.preview-discount');
Route::post('workshop-promo-activations/{activation}/sales-task', [WorkshopPromoCodeController::class, 'storeActivationSalesTask'])
    ->name('workshop-promo-activations.sales-task');
Route::get('workshop-promo-codes/{workshop_promo_code}/export-activations', [WorkshopPromoCodeController::class, 'exportActivations'])
    ->name('workshop-promo-codes.export-activations');
Route::resource('workshop-promo-codes', WorkshopPromoCodeController::class);
