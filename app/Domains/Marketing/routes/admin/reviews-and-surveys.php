<?php

use App\Domains\Marketing\Http\Controllers\Admin\InstructorPersonalBrandingController;
use App\Domains\Marketing\Http\Controllers\Admin\MarketingCourseReviewController;
use App\Domains\Marketing\Http\Controllers\Admin\MarketingCustomerSurveyController;
use Illuminate\Support\Facades\Route;

/**
 * مراجعات الكورسات التسويقية، استبيان العملاء، إجراءات البراندينغ الشخصي.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::prefix('marketing-course-reviews')->name('marketing-course-reviews.')->group(function () {
    Route::get('/', [MarketingCourseReviewController::class, 'index'])->name('index');
    Route::get('/create', [MarketingCourseReviewController::class, 'create'])->name('create');
    Route::post('/', [MarketingCourseReviewController::class, 'store'])->name('store');
    Route::post('/{marketing_course_review}/toggle', [MarketingCourseReviewController::class, 'toggleApprove'])->name('toggle');
    Route::delete('/{marketing_course_review}', [MarketingCourseReviewController::class, 'destroy'])->name('destroy');
});

// استبيان عملاء الأكاديمية (ردود العملاء + الخصم الممنوح)
Route::prefix('marketing-customer-surveys')->name('marketing-customer-surveys.')->group(function () {
    Route::get('/', [MarketingCustomerSurveyController::class, 'index'])->name('index');
    Route::get('/export', [MarketingCustomerSurveyController::class, 'export'])->name('export');
    Route::get('/{marketing_customer_survey}', [MarketingCustomerSurveyController::class, 'show'])->name('show');
    Route::delete('/{marketing_customer_survey}', [MarketingCustomerSurveyController::class, 'destroy'])->name('destroy');
});

Route::get('/personal-branding/{personal_branding}', [InstructorPersonalBrandingController::class, 'show'])->name('personal-branding.show');
Route::post('/personal-branding/{personal_branding}/approve', [InstructorPersonalBrandingController::class, 'approve'])->name('personal-branding.approve');
Route::post('/personal-branding/{personal_branding}/reject', [InstructorPersonalBrandingController::class, 'reject'])->name('personal-branding.reject');
Route::post('/personal-branding/{personal_branding}/send-back', [InstructorPersonalBrandingController::class, 'sendBackForReview'])->name('personal-branding.send-back');
