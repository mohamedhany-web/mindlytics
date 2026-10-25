<?php

use App\Domains\Accounting\Http\Controllers\Branch\BranchInvoicesController;
use App\Domains\Accounting\Http\Controllers\Branch\BranchPaymentsController;
use Illuminate\Support\Facades\Route;

/**
 * فواتير ومدفوعات الفرع.
 * يُحمَّل من داخل مجموعة branch-office في routes/web/portal.php (role:branch_manager + branch.office) وفي نفس موضعه القديم.
 */
Route::get('/invoices', [BranchInvoicesController::class, 'index'])->name('invoices');
Route::get('/payments', [BranchPaymentsController::class, 'index'])->name('payments');
