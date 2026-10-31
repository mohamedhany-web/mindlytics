<?php

use App\Domains\Sales\Http\Controllers\Crm\SalesCrmController;
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
