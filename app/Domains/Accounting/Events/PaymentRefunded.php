<?php

namespace App\Domains\Accounting\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * استرداد (كلي أو جزئي) لدفعة. يُطلق بعد نجاح الـ transaction فقط،
 * والمجالات الأخرى تتصرّف عليه (المبيعات: استرجاع العمولة — docs/SALES_INTEGRITY.md).
 */
class PaymentRefunded implements ShouldDispatchAfterCommit
{
    use Dispatchable;

    public function __construct(
        public readonly int $paymentId,
        public readonly float $amount,
        public readonly ?int $refundTransactionId = null,
    ) {}
}
