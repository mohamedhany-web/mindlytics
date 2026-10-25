<?php

use App\Domains\Hr\Http\Controllers\Admin\Recruitment\ApplicationController;
use App\Domains\Hr\Http\Controllers\Admin\Recruitment\JobPostingController;
use App\Domains\Hr\Http\Controllers\Admin\Recruitment\RubricController;
use Illuminate\Support\Facades\Route;

/**
 * التوظيف (ATS): الوظائف والطلبات ومعايير التقييم.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::prefix('hr')->name('hr.')->group(function () {
    Route::resource('jobs', JobPostingController::class);
    Route::get('applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
    Route::put('applications/{application}/status', [ApplicationController::class, 'updateStatus'])->name('applications.status');
    Route::post('applications/{application}/rescore', [ApplicationController::class, 'rescore'])->name('applications.rescore');
    Route::delete('applications/{application}', [ApplicationController::class, 'destroy'])->name('applications.destroy');
    Route::resource('rubrics', RubricController::class)->except(['show']);
});
