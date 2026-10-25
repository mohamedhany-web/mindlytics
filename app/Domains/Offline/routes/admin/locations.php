<?php

use App\Domains\Offline\Http\Controllers\Admin\OfflineLocationController;
use Illuminate\Support\Facades\Route;

/**
 * الأماكن (المقرات) ومديروها.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::resource('offline-locations', OfflineLocationController::class);
Route::post('offline-locations/{offlineLocation}/place-managers', [OfflineLocationController::class, 'storePlaceManager'])
    ->name('offline-locations.place-managers.store');
