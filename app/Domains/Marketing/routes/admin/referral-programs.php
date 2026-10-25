<?php

use App\Domains\Marketing\Http\Controllers\Admin\ReferralProgramController;
use Illuminate\Support\Facades\Route;

/**
 * برامج الإحالات.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::resource('referral-programs', ReferralProgramController::class);
