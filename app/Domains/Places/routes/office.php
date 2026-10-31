<?php

use App\Domains\Places\Http\Controllers\Office\PlaceDashboardController;
use App\Domains\Places\Http\Controllers\Office\PlaceInvoiceController;
use App\Domains\Places\Http\Controllers\Office\PlaceProfileController;
use App\Domains\Places\Http\Controllers\Office\PlaceSettlementController;
use App\Domains\Places\Http\Controllers\Office\PlaceUsageLogController;
use Illuminate\Support\Facades\Route;

/**
 * بوابة مدير المكان (تسجيل الساعات والمخالصة الشهرية والفواتير).
 * يُحمَّل من routes/web/portal.php في نفس موضعه القديم.
 */
Route::prefix('place-office')->name('place.office.')->middleware(['role:place_manager', 'place.office'])->group(function () {
    Route::get('/', fn () => redirect()->route('place.office.dashboard'));
    Route::get('/dashboard', [PlaceDashboardController::class, 'index'])->name('dashboard');
    Route::get('/usage-logs', [PlaceUsageLogController::class, 'index'])->name('usage-logs.index');
    Route::get('/usage-logs/create', [PlaceUsageLogController::class, 'create'])->name('usage-logs.create');
    Route::post('/usage-logs', [PlaceUsageLogController::class, 'store'])->name('usage-logs.store');
    Route::get('/settlements', [PlaceSettlementController::class, 'index'])->name('settlements.index');
    Route::get('/settlements/{settlement}', [PlaceSettlementController::class, 'show'])->name('settlements.show');
    Route::post('/settlements/{settlement}/submit', [PlaceSettlementController::class, 'submit'])->name('settlements.submit');
    Route::get('/invoices', [PlaceInvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [PlaceInvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/profile', [PlaceProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [PlaceProfileController::class, 'update'])->name('profile.update');
});
