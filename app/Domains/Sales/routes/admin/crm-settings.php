<?php

use App\Domains\Sales\Http\Controllers\Crm\SalesCrmSettingsController;
use Illuminate\Support\Facades\Route;

/**
 * إعدادات CRM للإدارة (docs/SALES_CRM.md): صلاحيات الأدوار واستثناءات الموظفين.
 * تُحمَّل من مجموعة admin/crm في routes/web/admin.php بس (مش من مسارات الموظفين).
 */
Route::get('settings', [SalesCrmSettingsController::class, 'index'])->name('settings.index');
Route::put('settings/roles', [SalesCrmSettingsController::class, 'updateRoles'])->name('settings.roles.update');
Route::put('settings/users/{user}', [SalesCrmSettingsController::class, 'updateUser'])->whereNumber('user')->name('settings.users.update');
