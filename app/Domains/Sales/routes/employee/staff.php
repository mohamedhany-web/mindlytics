<?php

use Illuminate\Support\Facades\Route;

/**
 * صفحات مشتركة لموظفي ومديري المبيعات — تُحوَّل لـ CRM فقط.
 */
Route::middleware('sales.staff')->prefix('sales')->name('sales.')->group(function () {
    Route::get('course-board', fn () => redirect()->route('employee.crm.quotes.index'))->name('course-board.index');
    Route::get('policy', function () {
        $user = auth()->user();
        $toAdmin = $user && method_exists($user, 'hasSalesManagerPortalAccess') && $user->hasSalesManagerPortalAccess();

        return redirect()->route($toAdmin ? 'employee.crm.admin.index' : 'employee.crm.home');
    })->name('policy.index');
});
