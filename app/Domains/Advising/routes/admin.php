<?php

use App\Domains\Advising\Http\Controllers\Admin\AdvisingAdminController;
use Illuminate\Support\Facades\Route;

/**
 * إدارة الإرشاد المهني (docs/ADVISING.md).
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware).
 */
Route::prefix('advising')->name('advising.')->group(function () {
    Route::get('/', [AdvisingAdminController::class, 'bookings'])->name('bookings');
    Route::put('bookings/{booking}', [AdvisingAdminController::class, 'updateBooking'])->whereNumber('booking')->name('bookings.update');
    Route::get('availability', [AdvisingAdminController::class, 'availability'])->name('availability');
    Route::post('availability', [AdvisingAdminController::class, 'storeRule'])->name('availability.store');
    Route::put('availability/{rule}', [AdvisingAdminController::class, 'updateRule'])->whereNumber('rule')->name('availability.update');
    Route::delete('availability/{rule}', [AdvisingAdminController::class, 'destroyRule'])->whereNumber('rule')->name('availability.destroy');
    Route::post('blocked', [AdvisingAdminController::class, 'storeBlocked'])->name('blocked.store');
    Route::delete('blocked/{blocked}', [AdvisingAdminController::class, 'destroyBlocked'])->whereNumber('blocked')->name('blocked.destroy');
    Route::put('settings', [AdvisingAdminController::class, 'updateSettings'])->name('settings.update');
});
