<?php

use App\Domains\Sales\Http\Controllers\Crm\SalesCrmActivityController;
use App\Domains\Sales\Http\Controllers\Crm\SalesCrmCalendarController;
use App\Domains\Sales\Http\Controllers\Crm\SalesCrmController;
use App\Domains\Sales\Http\Controllers\Crm\SalesCrmInboxController;
use App\Domains\Sales\Http\Controllers\Crm\SalesCrmReportController;
use App\Domains\Sales\Http\Controllers\Crm\SalesCrmStudentController;
use Illuminate\Support\Facades\Route;

/**
 * شاشات CRM الموحّدة (docs/SALES_CRM.md).
 * تُحمَّل مرتين بنفس الـ controller: من routes/web/employee.php (prefix: employee/crm، name: employee.crm.، middleware: sales.staff)
 * ومن routes/web/admin.php (prefix: admin/crm، name: admin.crm.). CrmScope يحدد البيانات حسب الدور.
 */
Route::get('/', [SalesCrmController::class, 'home'])->name('home');
Route::get('leads', [SalesCrmController::class, 'leads'])->name('leads.index');
Route::post('leads/bulk', [SalesCrmController::class, 'bulk'])->name('leads.bulk');
Route::get('leads/{lead}', [SalesCrmController::class, 'lead'])->whereNumber('lead')->name('leads.show');
Route::get('leads/{lead}/convert', [SalesCrmController::class, 'convertForm'])->whereNumber('lead')->name('leads.convert');
Route::post('leads/{lead}/convert', [SalesCrmController::class, 'convert'])->whereNumber('lead')->name('leads.convert.store');
Route::get('deals', [SalesCrmController::class, 'deals'])->name('deals.index');
Route::get('deals/{lead}', [SalesCrmController::class, 'deal'])->whereNumber('lead')->name('deals.show');

Route::get('students', [SalesCrmStudentController::class, 'index'])->name('students.index');
Route::get('students/{student}', [SalesCrmStudentController::class, 'show'])->whereNumber('student')->name('students.show');

Route::get('activities', [SalesCrmActivityController::class, 'index'])->name('activities.index');
Route::get('activities/create', [SalesCrmActivityController::class, 'create'])->name('activities.create');
Route::post('activities', [SalesCrmActivityController::class, 'store'])->name('activities.store');
Route::get('activities/{activity}', [SalesCrmActivityController::class, 'show'])->whereNumber('activity')->name('activities.show');
Route::get('activities/{activity}/edit', [SalesCrmActivityController::class, 'edit'])->whereNumber('activity')->name('activities.edit');
Route::put('activities/{activity}', [SalesCrmActivityController::class, 'update'])->whereNumber('activity')->name('activities.update');
Route::post('activities/{activity}/complete', [SalesCrmActivityController::class, 'complete'])->whereNumber('activity')->name('activities.complete');
Route::post('activities/{activity}/cancel', [SalesCrmActivityController::class, 'cancel'])->whereNumber('activity')->name('activities.cancel');

Route::get('calendar', [SalesCrmCalendarController::class, 'index'])->name('calendar');

Route::get('inbox', [SalesCrmInboxController::class, 'index'])->name('inbox');

Route::get('reports', [SalesCrmReportController::class, 'index'])->name('reports.index');
Route::get('reports/{report}', [SalesCrmReportController::class, 'show'])->where('report', '[a-z_]+')->name('reports.show');
