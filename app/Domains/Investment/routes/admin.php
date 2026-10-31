<?php

use App\Domains\Investment\Http\Controllers\Admin\DashboardController;
use App\Domains\Investment\Http\Controllers\Admin\InquiryController;
use App\Domains\Investment\Http\Controllers\Admin\PlanController;
use App\Domains\Investment\Http\Controllers\Admin\PolicyController;
use Illuminate\Support\Facades\Route;

/**
 * لوحة إدارة الاستثمار.
 * يُحمَّل بـ require من داخل مجموعة admin في ملفات routes الويب (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::prefix('investment')->name('investment.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('plans', PlanController::class);
    Route::get('inquiries', [InquiryController::class, 'index'])->name('inquiries.index');
    Route::get('inquiries/{inquiry}', [InquiryController::class, 'show'])->name('inquiries.show');
    Route::put('inquiries/{inquiry}', [InquiryController::class, 'update'])->name('inquiries.update');
    Route::delete('inquiries/{inquiry}', [InquiryController::class, 'destroy'])->name('inquiries.destroy');
    Route::get('policies', [PolicyController::class, 'edit'])->name('policies.edit');
    Route::put('policies', [PolicyController::class, 'update'])->name('policies.update');
});
