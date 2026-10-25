<?php

use App\Domains\Marketing\Http\Controllers\Employee\BusinessDeveloperMarketingController;
use Illuminate\Support\Facades\Route;

/**
 * لوحة تسويق Business Developer.
 * يُحمَّل من داخل مجموعة employee في routes/web/employee.php (auth + employee.work) وفي نفس موضعه القديم.
 */
Route::get('/business-developer/marketing', [BusinessDeveloperMarketingController::class, 'index'])->name('business-developer.marketing');
