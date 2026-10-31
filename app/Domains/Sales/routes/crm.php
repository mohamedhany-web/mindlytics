<?php

use App\Domains\Sales\Http\Controllers\Crm\CrmAccessController;
use App\Domains\Sales\Http\Controllers\Crm\CrmModuleController;
use App\Domains\Sales\Http\Controllers\Crm\CrmSocialReportController;
use App\Domains\Sales\Http\Controllers\Crm\SalesCrmController;
use Illuminate\Support\Facades\Route;

/**
 * شاشات CRM الموحّدة (SuiteCRM-style).
 * تُحمَّل من employee.php و admin.php بنفس الـ controller + CrmScope.
 */
Route::get('/', [SalesCrmController::class, 'home'])->name('home');

// Leads / Opportunities
Route::get('leads', [SalesCrmController::class, 'leads'])->name('leads.index');
Route::get('leads/create', [SalesCrmController::class, 'leadCreate'])->name('leads.create');
Route::post('leads/bulk', [SalesCrmController::class, 'bulk'])->name('leads.bulk');
Route::get('leads/{lead}/edit', [SalesCrmController::class, 'leadEdit'])->whereNumber('lead')->name('leads.edit');
Route::get('leads/{lead}', [SalesCrmController::class, 'lead'])->whereNumber('lead')->name('leads.show');
Route::get('leads/{lead}/convert', [SalesCrmController::class, 'convertForm'])->whereNumber('lead')->name('leads.convert');
Route::post('leads/{lead}/convert', [SalesCrmController::class, 'convert'])->whereNumber('lead')->name('leads.convert.store');

Route::get('deals', [SalesCrmController::class, 'deals'])->name('deals.index');
Route::get('deals/{lead}', [SalesCrmController::class, 'deal'])->whereNumber('lead')->name('deals.show');
Route::get('opportunities', [SalesCrmController::class, 'deals'])->name('opportunities.index');
Route::get('opportunities/{lead}', [SalesCrmController::class, 'deal'])->whereNumber('lead')->name('opportunities.show');

// Accounts
Route::get('accounts', [CrmModuleController::class, 'accountsIndex'])->name('accounts.index');
Route::get('accounts/create', [CrmModuleController::class, 'accountsCreate'])->name('accounts.create');
Route::post('accounts', [CrmModuleController::class, 'accountsStore'])->name('accounts.store');
Route::get('accounts/{account}', [CrmModuleController::class, 'accountsShow'])->name('accounts.show');
Route::get('accounts/{account}/edit', [CrmModuleController::class, 'accountsEdit'])->name('accounts.edit');
Route::put('accounts/{account}', [CrmModuleController::class, 'accountsUpdate'])->name('accounts.update');

// Contacts
Route::get('contacts', [CrmModuleController::class, 'contactsIndex'])->name('contacts.index');
Route::get('contacts/create', [CrmModuleController::class, 'contactsCreate'])->name('contacts.create');
Route::post('contacts', [CrmModuleController::class, 'contactsStore'])->name('contacts.store');
Route::get('contacts/{contact}', [CrmModuleController::class, 'contactsShow'])->name('contacts.show');
Route::get('contacts/{contact}/edit', [CrmModuleController::class, 'contactsEdit'])->name('contacts.edit');
Route::put('contacts/{contact}', [CrmModuleController::class, 'contactsUpdate'])->name('contacts.update');

// Activities
Route::get('activities', [CrmModuleController::class, 'activitiesIndex'])->name('activities.index');
Route::get('activities/create', [CrmModuleController::class, 'activitiesCreate'])->name('activities.create');
Route::post('activities', [CrmModuleController::class, 'activitiesStore'])->name('activities.store');
Route::get('activities/{activity}', [CrmModuleController::class, 'activitiesShow'])->whereNumber('activity')->name('activities.show');
Route::get('calls', fn (\Illuminate\Http\Request $r) => app(CrmModuleController::class)->activitiesIndex($r, 'call'))->name('calls.index');
Route::get('meetings', fn (\Illuminate\Http\Request $r) => app(CrmModuleController::class)->activitiesIndex($r, 'meeting'))->name('meetings.index');
Route::get('tasks', fn (\Illuminate\Http\Request $r) => app(CrmModuleController::class)->activitiesIndex($r, 'follow_up'))->name('tasks.index');
Route::get('notes', fn (\Illuminate\Http\Request $r) => app(CrmModuleController::class)->activitiesIndex($r, 'note'))->name('notes.index');
Route::get('emails', fn (\Illuminate\Http\Request $r) => app(CrmModuleController::class)->activitiesIndex($r, 'email'))->name('emails.index');

// Calendar / Campaigns / Cases / Documents
Route::get('calendar', [CrmModuleController::class, 'calendar'])->name('calendar.index');

Route::get('campaigns', [CrmModuleController::class, 'campaignsIndex'])->name('campaigns.index');
Route::get('campaigns/create', [CrmModuleController::class, 'campaignsCreate'])->name('campaigns.create');
Route::post('campaigns', [CrmModuleController::class, 'campaignsStore'])->name('campaigns.store');
Route::get('campaigns/{campaign}', [CrmModuleController::class, 'campaignsShow'])->name('campaigns.show');
Route::get('campaigns/{campaign}/edit', [CrmModuleController::class, 'campaignsEdit'])->name('campaigns.edit');
Route::put('campaigns/{campaign}', [CrmModuleController::class, 'campaignsUpdate'])->name('campaigns.update');

Route::get('cases', [CrmModuleController::class, 'casesIndex'])->name('cases.index');
Route::get('cases/create', [CrmModuleController::class, 'casesCreate'])->name('cases.create');
Route::post('cases', [CrmModuleController::class, 'casesStore'])->name('cases.store');
Route::get('cases/{case}', [CrmModuleController::class, 'casesShow'])->name('cases.show');
Route::get('cases/{case}/edit', [CrmModuleController::class, 'casesEdit'])->name('cases.edit');
Route::put('cases/{case}', [CrmModuleController::class, 'casesUpdate'])->name('cases.update');

Route::get('documents', [CrmModuleController::class, 'documentsIndex'])->name('documents.index');
Route::get('documents/create', [CrmModuleController::class, 'documentsCreate'])->name('documents.create');
Route::post('documents', [CrmModuleController::class, 'documentsStore'])->name('documents.store');
Route::get('documents/{document}', [CrmModuleController::class, 'documentsShow'])->name('documents.show');

// Quotes / Reports / Admin
Route::get('quotes', [CrmModuleController::class, 'quotesIndex'])->name('quotes.index');
Route::get('quotes/{order}', [CrmModuleController::class, 'quotesShow'])->whereNumber('order')->name('quotes.show');

Route::get('reports', [CrmModuleController::class, 'reportsIndex'])->name('reports.index');
Route::get('reports/kpi', [CrmModuleController::class, 'reportsKpi'])->name('reports.kpi');
Route::get('reports/daily', [CrmModuleController::class, 'reportsDaily'])->name('reports.daily');
Route::get('reports/campaigns', [CrmModuleController::class, 'reportsCampaigns'])->name('reports.campaigns');
Route::get('reports/social', [CrmSocialReportController::class, 'index'])->name('reports.social');

Route::get('inbox', [CrmModuleController::class, 'inboxIndex'])->name('inbox.index');

Route::get('win-approvals', [CrmModuleController::class, 'winApprovalsIndex'])->name('win-approvals.index');

Route::get('admin', [CrmModuleController::class, 'adminIndex'])->name('admin.index');

Route::get('access', [CrmAccessController::class, 'index'])->name('access.index');
Route::get('access/{user}', [CrmAccessController::class, 'edit'])->whereNumber('user')->name('access.edit');
Route::put('access/{user}', [CrmAccessController::class, 'update'])->whereNumber('user')->name('access.update');
