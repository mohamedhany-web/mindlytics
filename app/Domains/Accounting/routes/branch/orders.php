<?php

use App\Domains\Accounting\Http\Controllers\Branch\BranchOrdersController;
use Illuminate\Support\Facades\Route;

/**
 * طلبات الفرع.
 * يُحمَّل من داخل مجموعة branch-office في routes/web/portal.php (role:branch_manager + branch.office) وفي نفس موضعه القديم.
 */
Route::get('/orders', [BranchOrdersController::class, 'index'])->name('orders');
