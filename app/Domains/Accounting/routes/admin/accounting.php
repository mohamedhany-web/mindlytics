<?php

use App\Domains\Accounting\Http\Controllers\Admin\AccountingGatewayOperationsController;
use App\Domains\Accounting\Http\Controllers\Admin\AccountingHubController;
use App\Domains\Accounting\Http\Controllers\Admin\AccountingInsightsController;
use App\Domains\Accounting\Http\Controllers\Admin\AccountingInstallmentsController;
use App\Domains\Accounting\Http\Controllers\Admin\AccountingReceivablesController;
use App\Domains\Accounting\Http\Controllers\Admin\AccountingReportsController;
use App\Domains\Accounting\Http\Controllers\Admin\ExpenseController;
use App\Domains\Accounting\Http\Controllers\Admin\InstructorAccountController;
use App\Domains\Accounting\Http\Controllers\Admin\InvoiceController;
use App\Domains\Accounting\Http\Controllers\Admin\PaymentController;
use App\Domains\Accounting\Http\Controllers\Admin\SubscriptionController;
use App\Domains\Accounting\Http\Controllers\Admin\TransactionController;
use App\Domains\Accounting\Http\Controllers\Admin\WalletController;
use Illuminate\Support\Facades\Route;

/**
 * المحاسبة: الفواتير، المدفوعات، المعاملات، المحافظ، المصروفات، الاشتراكات، مركز المحاسبة وتقاريره.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::resource('invoices', InvoiceController::class)
    ->middleware('throttle:60,1')
    ->except(['update', 'destroy']);
Route::match(['put', 'patch', 'post'], '/invoices/{invoice}', [InvoiceController::class, 'update'])->middleware('throttle:20,5')->name('invoices.update');
Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->middleware('throttle:10,1')->name('invoices.destroy');
        
Route::resource('payments', PaymentController::class)
    ->middleware('throttle:60,1')
    ->except(['update', 'destroy']);
Route::match(['put', 'patch', 'post'], '/payments/{payment}', [PaymentController::class, 'update'])->middleware('throttle:20,5')->name('payments.update');
Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->middleware('throttle:10,1')->name('payments.destroy');
        
Route::resource('transactions', TransactionController::class)
    ->middleware('throttle:60,1')
    ->except(['update', 'destroy']);
Route::match(['put', 'patch', 'post'], '/transactions/{transaction}', [TransactionController::class, 'update'])->middleware('throttle:20,5')->name('transactions.update');
Route::post('/transactions/{transaction}/refund', [TransactionController::class, 'refund'])->middleware('throttle:10,5')->name('transactions.refund');
Route::post('/transactions/{transaction}/sync-wallet-withdrawal', [TransactionController::class, 'syncWalletWithdrawal'])->middleware('throttle:10,5')->name('transactions.sync-wallet-withdrawal');
Route::get('/transactions/{transaction}/sync-wallet-withdrawal', fn (\App\Domains\Accounting\Models\Transaction $transaction) => redirect()
    ->route('admin.transactions.show', $transaction)
    ->with('error', 'استخدم زر «تنفيذ السحب الآن» من صفحة المعاملة — لا تفتح رابط السحب مباشرة.'));
Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->middleware('throttle:10,1')->name('transactions.destroy');
        
Route::resource('wallets', WalletController::class)
    ->middleware('throttle:60,1')
    ->except(['update', 'destroy']);
Route::post('/wallets/transfer', [WalletController::class, 'transfer'])->middleware('throttle:20,5')->name('wallets.transfer');
Route::match(['put', 'patch', 'post'], '/wallets/{wallet}', [WalletController::class, 'update'])->middleware('throttle:20,5')->name('wallets.update');
Route::delete('/wallets/{wallet}', [WalletController::class, 'destroy'])->middleware('throttle:10,1')->name('wallets.destroy');
        
Route::resource('expenses', ExpenseController::class)->except(['destroy']);
Route::post('/expenses/{expense}/approve', [ExpenseController::class, 'approve'])->middleware('throttle:10,1')->name('expenses.approve');
Route::post('/expenses/{expense}/reject', [ExpenseController::class, 'reject'])->middleware('throttle:10,1')->name('expenses.reject');
Route::post('/expenses/{expense}', [ExpenseController::class, 'update'])->middleware('throttle:20,5')->name('expenses.update');
Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->middleware('throttle:10,1')->name('expenses.destroy');
        
Route::resource('subscriptions', SubscriptionController::class)
    ->middleware('throttle:60,1');
Route::post('/subscriptions/{subscription}', [SubscriptionController::class, 'update'])->middleware('throttle:20,5')->name('subscriptions.update');
Route::delete('/subscriptions/{subscription}', [SubscriptionController::class, 'destroy'])->middleware('throttle:10,1')->name('subscriptions.destroy');
Route::get('/accounting/instructor-accounts', [InstructorAccountController::class, 'index'])->name('accounting.instructor-accounts.index');
Route::get('/accounting/instructor-accounts/{instructor}', [InstructorAccountController::class, 'show'])->name('accounting.instructor-accounts.show');

Route::get('/accounting/hub', [AccountingHubController::class, 'hub'])->name('accounting.hub');
Route::get('/accounting/chart-of-accounts', [AccountingHubController::class, 'chart'])->name('accounting.chart');
Route::get('/accounting/insights', [AccountingInsightsController::class, 'index'])->name('accounting.insights');
Route::get('/accounting/insights/metrics', [AccountingInsightsController::class, 'metrics'])->name('accounting.insights.metrics');
Route::get('/accounting/receivables', [AccountingReceivablesController::class, 'index'])->name('accounting.receivables');
Route::post('/accounting/receivables/debts', [AccountingReceivablesController::class, 'storeDebt'])->name('accounting.receivables.debts.store');
Route::post('/accounting/receivables/debts/{accountingDebt}/repayment', [AccountingReceivablesController::class, 'addRepayment'])->name('accounting.receivables.debts.repayment');
Route::delete('/accounting/receivables/debts/{accountingDebt}', [AccountingReceivablesController::class, 'cancelDebt'])->name('accounting.receivables.debts.cancel');
Route::get('/accounting/installments', [AccountingInstallmentsController::class, 'index'])->name('accounting.installments');
Route::get('/accounting/gateway-operations', [AccountingGatewayOperationsController::class, 'index'])->name('accounting.gateway-operations');

Route::get('/accounting/reports', [AccountingReportsController::class, 'index'])->name('accounting.reports');
Route::get('/accounting/financial-analysis', [AccountingReportsController::class, 'financialAnalysis'])->name('accounting.financial-analysis');
Route::get('/accounting/financial-analysis/export', [AccountingReportsController::class, 'exportFinancialAnalysis'])->name('accounting.financial-analysis.export');
Route::get('/accounting/reports/export', [AccountingReportsController::class, 'export'])->name('accounting.reports.export');
Route::get('/accounting/reports/invoices', [AccountingReportsController::class, 'invoices'])->name('accounting.reports.invoices');
Route::get('/accounting/reports/payments', [AccountingReportsController::class, 'payments'])->name('accounting.reports.payments');
Route::get('/accounting/reports/transactions', [AccountingReportsController::class, 'transactions'])->name('accounting.reports.transactions');
Route::get('/accounting/reports/expenses', [AccountingReportsController::class, 'expenses'])->name('accounting.reports.expenses');
Route::get('/accounting/reports/wallets', [AccountingReportsController::class, 'wallets'])->name('accounting.reports.wallets');
Route::get('/accounting/reports/orders', [AccountingReportsController::class, 'orders'])->name('accounting.reports.orders');
