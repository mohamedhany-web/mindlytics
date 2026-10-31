<?php

namespace App\Domains\Accounting;

use App\Domains\Accounting\Console\Commands\ProcessInstallmentPayments;
use App\Domains\Accounting\Console\Commands\SyncInstructorAgreementPaymentsCommand;
use App\Domains\Accounting\Console\Commands\TestInvoiceStoreCommand;
use App\Domains\Accounting\Console\Commands\TestOrderApprove;
use Illuminate\Support\ServiceProvider;

/**
 * مجال المحاسبة والمدفوعات (docs/MODULARIZATION_PLAN.md).
 *
 * الـ routes لا تُحمَّل من هنا: ملفات routes/ داخل المجال تُستدعى بـ require في نفس مواضعها القديمة.
 * ⚠️ روابط بوابات الدفع (checkout/kashier/callback و checkout/fawaterak/{status} و js/checkout-pay-widget.v1.js)
 * مسجّلة عند Kashier/Fawaterak – لا تتغير.
 */
class AccountingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // view('accounting::admin.orders.index') و view('accounting::student.wallet.index') و view('accounting::public.checkout')
        $this->loadViewsFrom(__DIR__.'/resources/views', 'accounting');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ProcessInstallmentPayments::class,
                SyncInstructorAgreementPaymentsCommand::class,
                TestInvoiceStoreCommand::class,
                TestOrderApprove::class,
            ]);
        }
    }
}
