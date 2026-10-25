<?php

use App\Domains\Cms\Http\Controllers\Admin\BlogController;
use Illuminate\Support\Facades\Route;

/**
 * المدونة.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::resource('blog', BlogController::class);
